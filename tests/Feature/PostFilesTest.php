<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Rückmeldungen mit Dateianhang: speichern, herunterladen, entfernen.
 */
class PostFilesTest extends TestCase
{
    private string $storage;

    private CampScenario $s;

    protected function setUp(): void
    {
        parent::setUp();

        // Helper::storePost schreibt direkt nach storage_path(), deshalb ein eigenes Verzeichnis pro Test.
        $this->storage = sys_get_temp_dir().'/quali-test-storage-'.uniqid();
        File::makeDirectory($this->storage, 0775, true);
        $this->app->useStoragePath($this->storage);
        $this->s = CampScenario::create();
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->storage);

        parent::tearDown();
    }

    public function test_leader_attaches_file_to_feedback()
    {
        $post = $this->createPost(['camp_user_id' => $this->s->campUser($this->s->participant)->id]);

        $this->assertNotNull($post->uuid);
        $this->assertSame('Protokoll.pdf', $post->filename());
        $this->assertFileExists(storage_path($post->file));
    }

    public function test_leaders_of_same_camp_download_file()
    {
        $post = $this->createPost(['camp_user_id' => $this->s->campUser($this->s->participant)->id]);

        $this->actingAs($this->s->kursleiter)
            ->get(route('downloadFile', $post->uuid))
            ->assertOk()
            ->assertDownload();
    }

    public function test_participants_and_other_camps_cannot_download_file()
    {
        $post = $this->createPost(['camp_user_id' => $this->s->campUser($this->s->participant)->id]);
        $other = CampScenario::create();

        $this->actingAs($this->s->participant)->get(route('downloadFile', $post->uuid))->assertRedirect();
        $this->actingAs($other->kursleiter)->get(route('downloadFile', $post->uuid))->assertRedirect();
    }

    public function test_file_of_general_feedback_can_be_downloaded()
    {
        // Allgemeine Rückmeldung ohne zugeordnete Person (Seite "Rückmeldungen").
        $post = $this->createPost([]);
        $this->assertNull($post->camp_user_id);

        $this->actingAs($this->s->gruppenleiter)
            ->get(route('downloadFile', $post->uuid))
            ->assertOk();
    }

    public function test_feedback_cannot_be_attached_to_participant_of_other_camp()
    {
        $other = CampScenario::create();

        $this->actingAs($this->s->gruppenleiter)->post(route('posts.store'), [
            'comment' => 'Falscher Kurs',
            'camp_user_id' => $other->campUser($other->participant)->id,
        ]);

        $this->assertDatabaseMissing('posts', ['comment' => 'Falscher Kurs', 'camp_user_id' => $other->campUser($other->participant)->id]);
    }

    public function test_editing_text_keeps_the_attached_file()
    {
        $post = $this->createPost(['camp_user_id' => $this->s->campUser($this->s->participant)->id]);

        $this->actingAs($this->s->gruppenleiter)->post(route('profile.post.store', $this->s->participant), [
            'post_id' => $post->id,
            'comment' => 'Angepasst',
        ]);

        $post->refresh();
        $this->assertSame('Angepasst', $post->comment);
        $this->assertFileExists(storage_path($post->file));
    }

    public function test_deleting_feedback_removes_file()
    {
        $post = $this->createPost(['camp_user_id' => $this->s->campUser($this->s->participant)->id]);
        $path = storage_path($post->file);

        $this->actingAs($this->s->gruppenleiter)->delete(route('posts.destroy', $post))->assertRedirect();

        $this->assertModelMissing($post);
        $this->assertFileDoesNotExist($path);
    }

    public function test_feedback_can_be_deleted_when_file_is_already_gone()
    {
        $post = $this->createPost(['camp_user_id' => $this->s->campUser($this->s->participant)->id]);
        File::delete(storage_path($post->file));

        $this->actingAs($this->s->gruppenleiter)->delete(route('posts.destroy', $post))->assertRedirect();

        $this->assertModelMissing($post);
    }

    public function test_demo_users_cannot_upload_files()
    {
        $demo = User::factory()->gruppenleiter()->demo()->inCamp($this->s->camp)->create();

        $this->actingAs($demo)->post(route('posts.store'), [
            'comment' => 'Demo',
            'file' => UploadedFile::fake()->create('Protokoll.pdf', 10),
        ]);

        $this->assertNull(Post::where('comment', 'Demo')->firstOrFail()->file);
    }

    private function createPost(array $data): Post
    {
        $this->actingAs($this->s->gruppenleiter)->post(route('posts.store'), $data + [
            'comment' => 'Mit Anhang',
            'file' => UploadedFile::fake()->create('Protokoll.pdf', 10),
        ])->assertRedirect('/posts');

        return Post::where('leader_id', $this->s->gruppenleiter->id)->latest('id')->firstOrFail();
    }
}
