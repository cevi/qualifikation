<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Profilbilder (Helper::updateAvatar) landen im öffentlichen storage-Ordner.
 */
class AvatarUploadTest extends TestCase
{
    private string $storage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->storage = sys_get_temp_dir().'/quali-test-storage-'.uniqid();
        File::makeDirectory($this->storage, 0775, true);
        $this->app->useStoragePath($this->storage);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->storage);

        parent::tearDown();
    }

    public function test_cropped_avatar_is_stored_as_jpeg_with_generated_name()
    {
        $s = CampScenario::create();
        $cropped = ImageManager::gd()->create(8, 8)->toPng()->toDataUri();

        $this->actingAs($s->kursleiter)->put(route('admin.users.update', $s->participant->id), [
            'username' => $s->participant->username,
            'email' => $s->participant->email,
            'avatar' => UploadedFile::fake()->image('Mein Portrait.png'),
            'cropped_photo_id' => $cropped,
        ])->assertRedirect('/admin/users');

        $avatar = $s->campUser($s->participant)->avatar;
        $this->assertMatchesRegularExpression('#^'.$s->camp->id.'_[a-z0-9-]+/profiles/[0-9a-f-]{36}\.jpg$#', $avatar);
        $this->assertSame('image/jpeg', mime_content_type(storage_path('app/public/'.$avatar)));
    }
}
