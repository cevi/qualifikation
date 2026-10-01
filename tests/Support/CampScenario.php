<?php

namespace Tests\Support;

use App\Models\Answer;
use App\Models\Camp;
use App\Models\CampUser;
use App\Models\Chapter;
use App\Models\Feedback;
use App\Models\Group;
use App\Models\Help;
use App\Models\Post;
use App\Models\StandardText;
use App\Models\Survey;
use App\Models\SurveyChapter;
use App\Models\SurveyQuestion;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * Ein laufender Kurs mit allen Rollen und den Daten, die die Seiten typischerweise anzeigen.
 * Setzt die Stammdaten aus dem TestingSeeder voraus.
 */
final class CampScenario
{
    public Camp $camp;

    public Group $group;

    public User $admin;

    public User $kursleiter;

    public User $gruppenleiter;

    /** Teilnehmer mit $gruppenleiter als zuständigem Leiter. */
    public User $participant;

    /** Teilnehmer ohne zugewiesenen Leiter. */
    public User $unassignedParticipant;

    public Survey $survey;

    public Survey $unassignedSurvey;

    public Post $post;

    public Feedback $feedback;

    public Help $help;

    public StandardText $standardText;

    public static function create(): self
    {
        $s = new self();

        $s->admin = User::findOrFail(1);
        $s->group = Group::factory()->create();

        $s->kursleiter = User::factory()->kursleiter()->create(['group_id' => $s->group->id]);
        $s->camp = Camp::factory()->create([
            'user_id' => $s->kursleiter->id,
            'group_id' => $s->group->id,
        ]);
        $s->kursleiter->update(['camp_id' => $s->camp->id]);
        CampUser::create([
            'camp_id' => $s->camp->id,
            'user_id' => $s->kursleiter->id,
            'role_id' => config('status.role_Kursleiter'),
        ]);

        $s->gruppenleiter = User::factory()->gruppenleiter()->inCamp($s->camp)->create(['group_id' => $s->group->id]);
        $s->participant = User::factory()->inCamp($s->camp, $s->gruppenleiter)->create(['group_id' => $s->group->id]);
        $s->unassignedParticipant = User::factory()->inCamp($s->camp)->create(['group_id' => $s->group->id]);

        $s->survey = self::createSurvey($s->campUser($s->participant));
        $s->unassignedSurvey = self::createSurvey($s->campUser($s->unassignedParticipant));

        $s->post = Post::factory()
            ->for($s->campUser($s->participant), 'campUser')
            ->for($s->gruppenleiter, 'leader')
            ->create(['camp_id' => $s->camp->id]);

        $s->feedback = Feedback::create(['user_id' => $s->participant->id, 'feedback' => 'Tolles Tool']);
        $s->help = Help::create(['title' => 'Profil', 'content' => 'Hilfe zum Profil']);
        $s->standardText = StandardText::create([
            'title' => 'Standardtext',
            'content' => 'Inhalt',
            'camp_id' => $s->camp->id,
        ]);

        return $s;
    }

    public function campUser(User $user): CampUser
    {
        return CampUser::where('camp_id', $this->camp->id)->where('user_id', $user->id)->firstOrFail();
    }

    /**
     * @return array<string, User|null>
     */
    public function users(): array
    {
        return [
            'gast' => null,
            'teilnehmer' => $this->participant,
            'gruppenleiter' => $this->gruppenleiter,
            'kursleiter' => $this->kursleiter,
            'admin' => $this->admin,
        ];
    }

    /**
     * Gleiche Struktur wie AdminSurveysController@create: alle Kapitel mit ihren Fragen, Antwort "0".
     */
    private static function createSurvey(CampUser $campUser): Survey
    {
        $survey = Survey::create([
            'camp_user_id' => $campUser->id,
            'slug' => (string) Str::uuid(),
            'survey_status_id' => config('status.survey_neu'),
        ]);
        $answer = Answer::where('name', '0')->firstOrFail();

        foreach (Chapter::with('questions')->get() as $chapter) {
            $surveyChapter = SurveyChapter::create(['survey_id' => $survey->id, 'chapter_id' => $chapter->id]);
            foreach ($chapter->questions as $question) {
                SurveyQuestion::create([
                    'survey_chapter_id' => $surveyChapter->id,
                    'question_id' => $question->id,
                    'answer_first_id' => $answer->id,
                    'answer_second_id' => $answer->id,
                    'answer_leader_id' => $answer->id,
                ]);
            }
        }

        return $survey;
    }
}
