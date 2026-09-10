<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\Plugin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class PhotoEditTest extends TestCase
{
    use RefreshDatabase;

    private function makeTestImage(): string
    {
        $path = sys_get_temp_dir().'/photoedit-test-'.Str::random(8).'.jpg';
        $image = imagecreatetruecolor(800, 600);
        imagefill($image, 0, 0, imagecolorallocate($image, 120, 80, 60));
        imagejpeg($image, $path);
        imagedestroy($image);

        return $path;
    }

    private function makeMedia(string $sourcePath): Media
    {
        Storage::fake('media');
        Storage::fake('public');
        [$width, $height] = getimagesize($sourcePath);
        $uuid = (string) Str::uuid();
        $diskPath = "originals/{$uuid}.jpg";
        Storage::disk('media')->put($diskPath, file_get_contents($sourcePath));

        return Media::create([
            'uuid' => $uuid,
            'slug' => 'photoedit-test-'.Str::random(6),
            'mime_type' => 'image/jpeg',
            'disk_path' => $diskPath,
            'filesize' => filesize($sourcePath),
            'width' => $width,
            'height' => $height,
            'checksum' => hash_file('sha256', $sourcePath),
            'status' => 'published',
        ]);
    }

    public function test_edit_route_is_blocked_when_plugin_disabled(): void
    {
        Plugin::updateOrCreate(['slug' => 'photoedit'], ['label' => 'PhotoEdit', 'enabled' => false, 'installed_at' => now()]);
        $staff = User::factory()->create(['is_customer' => false]);
        $source = $this->makeTestImage();
        $media = $this->makeMedia($source);

        $this->actingAs($staff)->get(route('admin.media.edit-image', $media))->assertNotFound();

        @unlink($source);
    }

    public function test_apply_edit_updates_dimensions_and_creates_backup(): void
    {
        Plugin::updateOrCreate(['slug' => 'photoedit'], ['label' => 'PhotoEdit', 'enabled' => true, 'installed_at' => now()]);
        $staff = User::factory()->create(['is_customer' => false]);
        $source = $this->makeTestImage();
        $media = $this->makeMedia($source);

        $this->assertNull($media->original_backup_path);

        $this->actingAs($staff)->post(route('admin.media.edit-image.apply', $media), [
            'crop_x' => 0,
            'crop_y' => 0,
            'crop_width' => 400,
            'crop_height' => 300,
            'rotate' => 0,
        ])->assertRedirect(route('admin.media.edit-image', $media));

        $media->refresh();

        $this->assertNotNull($media->original_backup_path);
        $this->assertSame(400, $media->width);
        $this->assertSame(300, $media->height);
        $this->assertTrue(Storage::disk('media')->exists($media->original_backup_path));

        @unlink($source);
    }

    public function test_revert_restores_original_dimensions(): void
    {
        Plugin::updateOrCreate(['slug' => 'photoedit'], ['label' => 'PhotoEdit', 'enabled' => true, 'installed_at' => now()]);
        $staff = User::factory()->create(['is_customer' => false]);
        $source = $this->makeTestImage();
        $media = $this->makeMedia($source);

        $this->actingAs($staff)->post(route('admin.media.edit-image.apply', $media), [
            'crop_width' => 400,
            'crop_height' => 300,
        ]);

        $media->refresh();
        $this->assertSame(400, $media->width);

        $this->actingAs($staff)->post(route('admin.media.edit-image.revert', $media))
            ->assertRedirect(route('admin.media.edit-image', $media));

        $media->refresh();

        $this->assertNull($media->original_backup_path);
        $this->assertSame(800, $media->width);
        $this->assertSame(600, $media->height);

        @unlink($source);
    }
}
