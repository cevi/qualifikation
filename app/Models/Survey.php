<?php

namespace App\Models;

use App\Enums\SurveyStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Survey extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'survey_status_id', 'slug', 'camp_user_id', 'comment',
    ];

    protected $casts = [
        'demo' => 'boolean',
        'survey_status_id' => SurveyStatus::class,
    ];

    /**
     * Status der Quali. Alte Qualis ohne Status gelten als neu.
     */
    public function status(): SurveyStatus
    {
        return $this->survey_status_id ?? SurveyStatus::Neu;
    }

    public function campUser()
    {
        return $this->belongsTo('App\Models\CampUser');
    }

    public function MySurvey()
    {
        $aktUser = Auth::user();
        $camp_user = CampUser::where('user_id', $aktUser['id'])->where('camp_id', $aktUser->camp['id'])->first();

        return $this->camp_user_id === $camp_user['id'] || $this->campUser->isLedBy($aktUser);
    }

    public function SurveyName()
    {
        $aktUser = Auth::user();
        $name = 'Qualifikation';
        if ($aktUser->isTeilnehmer() && ! $this->status()->isClosedByParticipant()) {
            $name = $this->status()->isFirstRound() ? '1. Selbsteinschätzung' : '2. Selbsteinschätzung';
        }

        return $name;
    }

    public function chapters()
    {
        return $this->HasMany('App\Models\SurveyChapter');
    }

    public function questions()
    {
        return $this->hasManyThrough('App\Models\SurveyQuestion', 'App\Models\SurveyChapter');
    }

    public function survey_status()
    {
        return $this->belongsTo('App\Models\SurveyStatus');
    }

    public function TNIsAllowed()
    {
        $aktUser = Auth::user();
        $status = $this->status();
        // Leitende füllen ihre Einschätzung aus, bis die Quali fertig ist.
        $result = $aktUser->isLeader() && ! $aktUser->isCampleader() &&
            $this->SurveyIsAllowed() &&
            ! $status->isFinished();
        // Teilnehmende nur in einer freigegebenen Runde, die sie noch nicht abgeschlossen haben.
        if (! $result) {
            $camp = $aktUser->camp;
            // Mit Status-Steuerung gibt der Kurs-Status vor, bis wohin ausgefüllt werden darf.
            if ($camp['status_control']) {
                $max_status = $camp->survey_status_id;
            } else {
                $max_status = SurveyStatus::TnAbgeschlossen;
            }

            if ($max_status && $status->isBefore($max_status) &&
                ($aktUser->isTeilnehmer() && $this->campUser->user_id == $aktUser['id'])) {
                $result = $status->isFirstRound();
                if (! $result) {
                    $result = $camp['secondsurveyopen'];
                }
            }
        }

        return $result;
    }

    public function SurveyIsAllowed()
    {
        $aktUser = Auth::user();
        $result = $aktUser->isAdmin();
        if (! $result) {
            $camp = $aktUser->camp;
            $camp_survey = $this->campUser->camp;
            if ($camp && $camp->is($camp_survey)) {
                $result =
                    ($aktUser->isCampleader()) ||
                    ($aktUser->isLeader() && $this->campUser->isLedBy($aktUser)) ||
                    ($aktUser->isTeilnehmer() && ($this->campUser->user_id == $aktUser['id']));
            }
        }

        return $result;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
