<?php

namespace App\Services;

use App\Enums\Role;
use App\Enums\SurveyStatus;
use App\Models\Camp;
use App\Models\CampUser;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Offene Arbeiten in einem Kurs, für die Aufgabenliste auf der Übersicht. Jede Prüfung ist eine eigene Methode,
 * damit die Monats-Mail "Kurs abschliessen" (camps:finish-reminder) dieselbe Regel verwendet.
 *
 * Aufgabe: array{title: string, subtitle: string, urgent: bool, link: string}
 */
class CampChecks
{
    /** So lange nach dem Schlussdatum darf ein Kurs offen bleiben. */
    public const FINISH_AFTER_MONTHS = 3;

    /**
     * Qualis, die die Person auf der Übersicht sieht: Kursleitung und Stabsleitung alle des Kurses, Leitende die
     * ihrer Teilnehmenden, Teilnehmende die eigene.
     */
    public function surveysFor(User $user): Collection
    {
        $camp = $user->camp;
        if (! $camp) {
            return collect();
        }
        $campUsers = CampUser::where('camp_id', $camp->id)
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id);
                if ($user->isCampleader() || $user->role_id === Role::Stabsleiter) {
                    $query->orWhere('role_id', Role::Teilnehmer);
                } else {
                    $query->orWhere('leader_id', $user->id);
                }
            })
            ->pluck('id');

        return Survey::with(['campuser.user.group', 'campuser.leader', 'survey_status'])
            ->whereIn('camp_user_id', $campUsers)
            ->get()
            ->sortBy('campuser.user.username', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
    }

    /** Aufgaben zu den Qualis, die die Person sieht; für Kursleitende zusätzlich die des Kurses. Dringende zuerst. */
    public function dashboardTasks(User $user, Collection $surveys): Collection
    {
        $tasks = $user->isTeilnehmer()
            ? $this->participantTasks($surveys)
            : $this->leaderTasks($surveys, $user->camp);
        if ($user->camp && $user->isCampleader()) {
            $tasks = $tasks->merge($this->campTasks($user->camp));
        }

        return $tasks->sortByDesc('urgent')->values();
    }

    /** Teilnehmende: eigene Selbsteinschätzung, solange sie ausgefüllt werden darf. */
    public function participantTasks(Collection $surveys): Collection
    {
        return $surveys->filter(fn (Survey $survey) => $survey->TNIsAllowed())
            ->map(fn (Survey $survey) => [
                'title' => $survey->SurveyName().' ausfüllen',
                'subtitle' => 'Noch nicht abgeschlossen',
                'urgent' => false,
                'link' => route('survey.survey', $survey->slug),
            ])->values();
    }

    /** Leitende: Selbsteinschätzung abgeschlossen (wartet auf die Leitenden) oder trotz Freigabe nicht begonnen. */
    public function leaderTasks(Collection $surveys, ?Camp $camp): Collection
    {
        $tasks = collect();
        foreach ($surveys as $survey) {
            $name = $survey->campuser->user->username;
            if ($survey->status() === SurveyStatus::TnAbgeschlossen) {
                $tasks->push([
                    'title' => "$name hat die Selbsteinschätzung abgeschlossen",
                    'subtitle' => 'Vergleich besprechen und Quali abschliessen',
                    'urgent' => true,
                    'link' => route('survey.compare', $survey->slug),
                ]);
            } elseif ($survey->status() === SurveyStatus::Neu && $camp && $this->firstRoundReleased($camp)) {
                $tasks->push([
                    'title' => "$name hat noch nicht begonnen",
                    'subtitle' => '1. Selbsteinschätzung ist offen',
                    'urgent' => false,
                    'link' => route('home.profile', $survey->campuser->user->slug),
                ]);
            }
        }

        return $tasks;
    }

    /** Kursleitung: Zuteilung, fehlende Qualis, 2. Runde freigeben, Kurs abschliessen. */
    public function campTasks(Camp $camp): Collection
    {
        $tasks = collect();
        if ($this->mustBeFinished($camp)) {
            $tasks->push([
                'title' => 'Kurs abschliessen',
                'subtitle' => 'Das Schlussdatum ('.Carbon::parse($camp->end_date)->format('d.m.Y').') liegt über '.self::FINISH_AFTER_MONTHS.' Monate zurück',
                'urgent' => true,
                'link' => route('admin.camps.edit', $camp),
            ]);
        }
        if ($count = $this->participantsWithoutLeader($camp)->count()) {
            $tasks->push([
                'title' => $count === 1 ? '1 Teilnehmende Person ohne Leitende' : "$count Teilnehmende ohne Leitende",
                'subtitle' => 'Unter Personen zuteilen',
                'urgent' => false,
                'link' => route('admin.users.index'),
            ]);
        }
        if ($count = $this->participantsWithoutSurvey($camp)->count()) {
            $tasks->push([
                'title' => $count === 1 ? '1 Teilnehmende Person ohne Qualifikation' : "$count Teilnehmende ohne Qualifikation",
                'subtitle' => 'Unter Qualifikationen erstellen',
                'urgent' => false,
                'link' => route('surveys.index'),
            ]);
        }
        if ($this->secondRoundCanBeReleased($camp)) {
            $tasks->push([
                'title' => '2. Selbsteinschätzung freigeben',
                'subtitle' => 'Alle Teilnehmenden haben die 1. abgeschlossen',
                'urgent' => false,
                'link' => route('surveys.index'),
            ]);
        }

        return $tasks;
    }

    /** Ohne Status-Steuerung sofort, sonst sobald der Kurs-Status die 1. Runde erreicht hat. */
    public function firstRoundReleased(Camp $camp): bool
    {
        return ! $camp->status_control || (bool) $camp->survey_status_id?->isAtLeast(SurveyStatus::ErsteRundeOffen);
    }

    /** Offen, mehr als FINISH_AFTER_MONTHS nach dem Schlussdatum; Demo- und Global-Kurs nie. */
    public function mustBeFinished(Camp $camp): bool
    {
        // Alte Kurse haben teils "0000-00-00" als Schlussdatum, das gilt wie keines.
        if ($camp->finish || $camp->demo || $camp->global_camp || ! $camp->end_date || str_starts_with($camp->end_date, '0000')) {
            return false;
        }

        return Carbon::parse($camp->end_date)->addMonths(self::FINISH_AFTER_MONTHS)->isPast();
    }

    /** Alle Kurse, die abgeschlossen werden müssten (Monats-Mail an die Kursleitung, camps:finish-reminder). */
    public function campsToFinish(): Collection
    {
        return Camp::with(['user', 'camp_type'])
            ->where('finish', false)->where('demo', false)->where('global_camp', false)
            ->whereNotNull('end_date')
            ->where('end_date', '<=', now()->subMonths(self::FINISH_AFTER_MONTHS)->toDateString())
            ->get()
            ->filter(fn (Camp $camp) => $this->mustBeFinished($camp))
            ->values();
    }

    public function participantsWithoutLeader(Camp $camp): Collection
    {
        return CampUser::where('camp_id', $camp->id)->where('role_id', Role::Teilnehmer)->whereNull('leader_id')->get();
    }

    public function participantsWithoutSurvey(Camp $camp): Collection
    {
        return CampUser::where('camp_id', $camp->id)->where('role_id', Role::Teilnehmer)->doesntHave('surveys')->get();
    }

    /** Noch nicht freigegeben, es gibt Qualis, und alle haben die 1. Selbsteinschätzung abgeschlossen. */
    public function secondRoundCanBeReleased(Camp $camp): bool
    {
        if ($camp->secondsurveyopen) {
            return false;
        }
        $surveys = $camp->surveys()->get();

        return $surveys->isNotEmpty() && $surveys->every(fn (Survey $survey) => $survey->status()->isClosedByParticipant());
    }
}
