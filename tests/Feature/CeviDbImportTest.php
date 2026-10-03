<?php

namespace Tests\Feature;

use App\Models\CampUser;
use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Import der Teilnehmenden aus der Cevi-DB (POST admin/users/import), siehe Commit ebe4d02.
 */
class CeviDbImportTest extends TestCase
{
    private const API = 'https://db.cevi.test';

    private const EVENT_ID = 5000;

    private CampScenario $s;

    /** @var array<int, array> Teilnahmen (data) im JSON:API-Format */
    private array $data = [];

    /** @var array<int, array> Personen und Rollen (included) */
    private array $included = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->s = CampScenario::create();
        $this->s->camp->update(['foreign_id' => self::EVENT_ID]);
        $this->s->group->update(['api_token' => Crypt::encryptString('geheimer-token'), 'url' => self::API]);
        $this->s->kursleiter->update(['foreign_id' => 900]);
        $this->addParticipation(900, 'Event::Role::Leader', ['first_name' => 'Kurs', 'email' => $this->s->kursleiter->email]);
    }

    public function test_imports_participants_and_leaders_into_camp()
    {
        $this->addParticipation(101, 'Event::Course::Role::Participant', ['first_name' => 'Anna', 'nickname' => 'Ameise', 'email' => 'anna@example.com']);
        $this->addParticipation(102, 'Event::Role::AssistantLeader', ['first_name' => 'Ben', 'email' => 'ben@example.com']);

        $this->import()->assertOk();

        $anna = User::where('email', 'anna@example.com')->firstOrFail();
        $ben = User::where('email', 'ben@example.com')->firstOrFail();
        $this->assertSame(['Ameise', 101, config('status.role_Teilnehmer')], [$anna->username, $anna->foreign_id, $anna->role_id]);
        $this->assertSame(['Ben', 102, config('status.role_Gruppenleiter')], [$ben->username, $ben->foreign_id, $ben->role_id]);
        $this->assertTrue(CampUser::where('camp_id', $this->s->camp->id)->where('user_id', $anna->id)->exists());
    }

    public function test_people_without_email_are_skipped_and_reported_by_name()
    {
        $this->addParticipation(101, 'Event::Course::Role::Participant', ['first_name' => 'Anna', 'email' => 'anna@example.com']);
        $this->addParticipation(103, 'Event::Course::Role::Participant', ['first_name' => 'Lena', 'last_name' => 'Ohnemail', 'email' => null]);

        $this->import()->assertOk()->assertExactJson(['ok' => true, 'skipped' => ['Lena Ohnemail']]);

        $this->assertDatabaseHas('users', ['email' => 'anna@example.com']);
        $this->assertDatabaseMissing('users', ['foreign_id' => 103]);
    }

    public function test_second_run_creates_no_duplicates()
    {
        $this->addParticipation(101, 'Event::Course::Role::Participant', ['first_name' => 'Anna', 'email' => 'anna@example.com']);

        $this->import();
        $count = User::count();
        $this->import();

        $this->assertSame($count, User::count());
    }

    public function test_existing_account_is_matched_by_email_ignoring_case()
    {
        $existing = User::factory()->create(['email' => 'Max@Example.com']);
        $this->addParticipation(104, 'Event::Course::Role::Participant', ['first_name' => 'Max', 'email' => 'max@example.com']);

        $this->import();

        $this->assertSame(0, User::where('email', 'max@example.com')->where('id', '!=', $existing->id)->count());
        $this->assertSame(104, $existing->fresh()->foreign_id);
        $this->assertSame($this->s->camp->id, $existing->fresh()->camp_id);
    }

    public function test_underscore_in_email_does_not_match_another_person()
    {
        // "_" ist bei SQL-LIKE ein Platzhalter für ein beliebiges Zeichen.
        $other = User::factory()->create(['email' => 'maxamuster@example.com']);
        $this->addParticipation(105, 'Event::Course::Role::Participant', ['first_name' => 'Max', 'email' => 'max_muster@example.com']);

        $this->import();

        $this->assertDatabaseHas('users', ['email' => 'max_muster@example.com', 'foreign_id' => 105]);
        $this->assertNull($other->fresh()->foreign_id);
        $this->assertNotSame($this->s->camp->id, $other->fresh()->camp_id);
    }

    public function test_only_course_leaders_may_import()
    {
        $this->data = [];
        $this->included = [];
        $this->addParticipation(900, 'Event::Course::Role::Participant', ['first_name' => 'Kurs', 'email' => $this->s->kursleiter->email]);
        $this->addParticipation(101, 'Event::Course::Role::Participant', ['first_name' => 'Anna', 'email' => 'anna@example.com']);

        $this->import()->assertNotFound()->assertJsonFragment(['ok' => false]);

        $this->assertDatabaseMissing('users', ['email' => 'anna@example.com']);
    }

    public function test_missing_person_data_is_reported_as_api_key_problem()
    {
        $this->included = [];

        $this->import()->assertNotFound()->assertJsonPath('error', fn ($error) => str_contains($error, 'API-Key'));
    }

    public function test_follows_pagination_and_sorts_by_id()
    {
        $this->addParticipation(101, 'Event::Course::Role::Participant', ['first_name' => 'Anna', 'email' => 'anna@example.com']);
        $page1 = ['data' => array_slice($this->data, 0, 1), 'included' => array_slice($this->included, 0, 2), 'links' => ['next' => '/api/event_participations?page=2']];
        $page2 = ['data' => array_slice($this->data, 1), 'included' => array_slice($this->included, 2), 'links' => []];
        Http::fake([self::API.'/api/event_participations?page=2' => Http::response($page2), self::API.'/*' => Http::response($page1)]);

        $this->actingAs($this->s->kursleiter)->post(route('users.import'))->assertOk();

        $this->assertDatabaseHas('users', ['email' => 'anna@example.com']);
        Http::assertSent(function (Request $request) {
            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

            return $request->hasHeader('X-TOKEN', 'geheimer-token')
                && ($query['sort'] ?? null) === 'id'
                && ($query['filter']['event_id']['eq'] ?? null) == self::EVENT_ID;
        });
        Http::assertSent(fn (Request $request) => $request->url() === self::API.'/api/event_participations?page=2');
    }

    public function test_failure_leaves_no_half_imported_people()
    {
        $this->addParticipation(101, 'Event::Course::Role::Participant', ['first_name' => 'Anna', 'email' => 'anna@example.com']);
        // Weder Nickname noch Vorname: users.username ist NOT NULL.
        $this->addParticipation(106, 'Event::Course::Role::Participant', ['email' => 'ohne-namen@example.com']);
        $count = User::count();

        $this->import();

        $this->assertSame($count, User::count());
        $this->assertDatabaseMissing('users', ['email' => 'anna@example.com']);
    }

    public function test_cevi_db_export_can_be_uploaded_as_json()
    {
        $this->addParticipation(101, 'Event::Course::Role::Participant', ['first_name' => 'Anna', 'email' => 'anna@example.com']);
        $export = UploadedFile::fake()->createWithContent('export.json', json_encode(['data' => $this->data, 'included' => $this->included]));

        $this->actingAs($this->s->kursleiter)
            ->post('/admin/users/uploadFile', ['csv_file' => $export])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', ['email' => 'anna@example.com', 'foreign_id' => 101]);
    }

    public function test_csv_files_are_no_longer_imported()
    {
        $csv = UploadedFile::fake()->createWithContent('liste.csv', "ceviname,e_mail,passwort,rollen,leiter\nanna,anna@example.com,geheim,T,\n");

        $this->actingAs($this->s->kursleiter)
            ->post('/admin/users/uploadFile', ['csv_file' => $csv])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseMissing('users', ['email' => 'anna@example.com']);
    }

    public function test_camp_without_cevi_db_id_is_rejected_without_request()
    {
        $this->s->camp->update(['foreign_id' => null]);
        Http::fake();

        $this->actingAs($this->s->kursleiter)->post(route('users.import'))->assertNotFound();

        Http::assertNothingSent();
    }

    private function import()
    {
        Http::fake([self::API.'/*' => Http::response(['data' => $this->data, 'included' => $this->included, 'links' => []])]);

        return $this->actingAs($this->s->kursleiter)->post(route('users.import'));
    }

    private function addParticipation(int $personId, string $roleType, array $attributes): void
    {
        $this->data[] = [
            'id' => 'p'.$personId,
            'type' => 'event_participations',
            'relationships' => [
                'participant' => ['data' => ['id' => (string) $personId, 'type' => 'people']],
                'roles' => ['data' => [['id' => 'r'.$personId, 'type' => 'event_roles']]],
            ],
        ];
        $this->included[] = ['id' => (string) $personId, 'type' => 'people', 'attributes' => $attributes];
        $this->included[] = ['id' => 'r'.$personId, 'type' => 'event_roles', 'attributes' => ['type' => $roleType]];
    }
}
