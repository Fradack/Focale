<?php

namespace Tests\Feature;

use App\Models\Album;
use App\Models\Media;
use App\Models\Plugin;
use App\Models\User;
use App\Support\Plugins;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PluginSystemTest extends TestCase
{
    use RefreshDatabase;

    private function makeMedia(): Media
    {
        return Media::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'slug' => 'test-media',
            'mime_type' => 'image/jpeg',
            'disk_path' => 'media/test/original.jpg',
            'filesize' => 1000,
            'checksum' => str_repeat('a', 64),
            'status' => 'published',
        ]);
    }

    private function makeAlbum(): Album
    {
        return Album::create([
            'title' => 'Test',
            'slug' => 'test-album',
            'status' => 'published',
        ]);
    }

    public function test_plugins_helper_reports_disabled_for_missing_row(): void
    {
        $this->assertFalse(Plugins::enabled('does-not-exist'));
        $this->assertFalse(Plugins::installed('does-not-exist'));
    }

    public function test_likes_routes_are_blocked_when_plugin_disabled(): void
    {
        Plugin::updateOrCreate(['slug' => 'likes'], ['label' => "J'aime", 'enabled' => false, 'installed_at' => now()]);
        $media = $this->makeMedia();
        $album = $this->makeAlbum();

        $this->post(route('public.image.like', $media))->assertNotFound();
        $this->post(route('public.album.like', $album))->assertNotFound();
    }

    public function test_likes_routes_work_when_plugin_enabled(): void
    {
        Plugin::updateOrCreate(['slug' => 'likes'], ['label' => "J'aime", 'enabled' => true, 'installed_at' => now()]);
        $media = $this->makeMedia();

        $this->post(route('public.image.like', $media))
            ->assertOk()
            ->assertJson(['liked' => true, 'count' => 1]);
    }

    public function test_admin_can_toggle_plugin_enabled_state(): void
    {
        $staff = User::factory()->create(['is_customer' => false]);
        Plugin::updateOrCreate(['slug' => 'boutique'], ['label' => 'Boutique', 'enabled' => true, 'installed_at' => now()]);

        $this->actingAs($staff)->post(route('admin.plugins.disable', 'boutique'))->assertRedirect();
        $this->assertFalse(Plugins::enabled('boutique'));

        $this->actingAs($staff)->post(route('admin.plugins.enable', 'boutique'))->assertRedirect();
        $this->assertTrue(Plugins::enabled('boutique'));
    }

    public function test_admin_plugins_registry_page_renders(): void
    {
        $staff = User::factory()->create(['is_customer' => false]);
        Plugin::updateOrCreate(['slug' => 'boutique'], ['label' => 'Boutique', 'enabled' => true, 'installed_at' => now()]);

        $this->actingAs($staff)->get(route('admin.plugins.index'))
            ->assertOk()
            ->assertSee('Boutique');
    }
}
