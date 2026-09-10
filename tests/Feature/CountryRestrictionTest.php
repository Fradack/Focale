<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CountryRestrictionTest extends TestCase
{
    use RefreshDatabase;

    private function fakeGeo(?string $countryCode, int $status = 200): void
    {
        Http::fake([
            'ipwho.is/*' => $countryCode === null
                ? Http::response(['success' => false, 'message' => 'invalid query'], $status)
                : Http::response(['success' => true, 'country_code' => $countryCode], $status),
        ]);
    }

    public function test_disabled_mode_always_allows_without_calling_geolocation_api(): void
    {
        Setting::set('country_restriction_mode', 'disabled');
        Http::fake(); // any call would be recorded/fail the assertion below

        $this->get('/')->assertOk();

        Http::assertNothingSent();
    }

    public function test_blocklist_mode_blocks_a_faked_blocked_country(): void
    {
        Setting::set('country_restriction_mode', 'blocklist');
        Setting::set('country_restriction_countries', json_encode(['FR', 'DE']));
        $this->fakeGeo('FR');

        $this->get('/')->assertStatus(403);
    }

    public function test_blocklist_mode_allows_countries_not_in_the_list(): void
    {
        Setting::set('country_restriction_mode', 'blocklist');
        Setting::set('country_restriction_countries', json_encode(['FR', 'DE']));
        $this->fakeGeo('US');

        $this->get('/')->assertOk();
    }

    public function test_allowlist_mode_allows_a_faked_allowed_country(): void
    {
        Setting::set('country_restriction_mode', 'allowlist');
        Setting::set('country_restriction_countries', json_encode(['FR', 'DE']));
        $this->fakeGeo('FR');

        $this->get('/')->assertOk();
    }

    public function test_allowlist_mode_blocks_countries_not_in_the_list(): void
    {
        Setting::set('country_restriction_mode', 'allowlist');
        Setting::set('country_restriction_countries', json_encode(['FR', 'DE']));
        $this->fakeGeo('US');

        $this->get('/')->assertStatus(403);
    }

    public function test_failed_geolocation_lookup_always_allows_fail_open_blocklist(): void
    {
        Setting::set('country_restriction_mode', 'blocklist');
        Setting::set('country_restriction_countries', json_encode(['FR']));
        $this->fakeGeo(null, 500);

        $this->get('/')->assertOk();
    }

    public function test_failed_geolocation_lookup_always_allows_fail_open_allowlist(): void
    {
        Setting::set('country_restriction_mode', 'allowlist');
        Setting::set('country_restriction_countries', json_encode(['FR']));
        $this->fakeGeo(null, 500);

        $this->get('/')->assertOk();
    }

    public function test_authenticated_staff_user_bypasses_restriction_regardless_of_mode(): void
    {
        Setting::set('country_restriction_mode', 'blocklist');
        Setting::set('country_restriction_countries', json_encode(['FR']));
        $this->fakeGeo('FR');

        $staff = User::factory()->create(['is_customer' => false]);

        $this->actingAs($staff)->get('/')->assertOk();
    }

    public function test_authenticated_customer_user_bypasses_restriction_regardless_of_mode(): void
    {
        Setting::set('country_restriction_mode', 'allowlist');
        Setting::set('country_restriction_countries', json_encode(['FR']));
        $this->fakeGeo('US');

        $customer = User::factory()->create(['is_customer' => true]);

        $this->actingAs($customer)->get('/')->assertOk();
    }

    public function test_admin_panel_is_never_subject_to_country_restriction(): void
    {
        Setting::set('country_restriction_mode', 'blocklist');
        Setting::set('country_restriction_countries', json_encode(['FR']));
        $this->fakeGeo('FR');

        $staff = User::factory()->create(['is_customer' => false]);

        // Logged in, the admin route must never be gated by this middleware
        // at all — it isn't attached to the admin route group, so the owner
        // can never be locked out of their own back office by a misconfigured
        // country restriction.
        $this->actingAs($staff)->get(route('admin.dashboard'))->assertOk();
    }

    public function test_unauthenticated_admin_panel_visit_redirects_to_login_not_country_blocked(): void
    {
        Setting::set('country_restriction_mode', 'blocklist');
        Setting::set('country_restriction_countries', json_encode(['FR']));

        // Unauthenticated, the admin route redirects to the login screen
        // (guarded by 'auth', not this middleware) rather than ever
        // returning the 403 country-blocked response, and never even
        // attempts a geolocation lookup — country-restriction isn't part
        // of the admin route group at all.
        Http::fake();
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        Http::assertNothingSent();
    }

    public function test_admin_settings_page_renders_country_restriction_section(): void
    {
        $staff = User::factory()->create(['is_customer' => false]);

        $this->actingAs($staff)->get(route('admin.settings.edit'))
            ->assertOk()
            ->assertSee('Restriction géographique')
            ->assertSee('Bloquer certains pays')
            ->assertSee('Autoriser uniquement certains pays')
            ->assertSee('France');
    }

    public function test_admin_can_save_and_reload_a_blocklist_selection(): void
    {
        $staff = User::factory()->create(['is_customer' => false]);

        $this->actingAs($staff)->put(route('admin.settings.update'), [
            'country_restriction_mode' => 'blocklist',
            'country_restriction_countries' => ['FR', 'DE'],
        ])->assertRedirect();

        $this->assertSame('blocklist', Setting::get('country_restriction_mode'));
        $this->assertSame(['FR', 'DE'], json_decode(Setting::get('country_restriction_countries'), true));

        // Reloading the settings page reflects the persisted selection.
        $response = $this->actingAs($staff)->get(route('admin.settings.edit'))->assertOk();
        $response->assertSee('name="country_restriction_mode" value="blocklist"', false);
    }

    public function test_admin_settings_update_rejects_invalid_country_code(): void
    {
        $staff = User::factory()->create(['is_customer' => false]);

        $this->actingAs($staff)->put(route('admin.settings.update'), [
            'country_restriction_mode' => 'blocklist',
            'country_restriction_countries' => ['ZZ'],
        ])->assertSessionHasErrors('country_restriction_countries.0');
    }

    public function test_bot_restriction_disabled_by_default_allows_bot_user_agent(): void
    {
        $this->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'])
            ->get('/')
            ->assertOk();
    }

    public function test_bot_restriction_blocks_a_known_crawler_user_agent(): void
    {
        Setting::set('bot_restriction_enabled', '1');

        $this->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'])
            ->get('/')
            ->assertStatus(403);
    }

    public function test_bot_restriction_blocks_a_generic_script_user_agent(): void
    {
        Setting::set('bot_restriction_enabled', '1');

        $this->withHeaders(['User-Agent' => 'python-requests/2.31.0'])
            ->get('/')
            ->assertStatus(403);
    }

    public function test_bot_restriction_blocks_a_missing_user_agent(): void
    {
        Setting::set('bot_restriction_enabled', '1');

        $this->withHeaders(['User-Agent' => ''])
            ->get('/')
            ->assertStatus(403);
    }

    public function test_bot_restriction_allows_a_real_browser_user_agent(): void
    {
        Setting::set('bot_restriction_enabled', '1');

        $this->withHeaders(['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'])
            ->get('/')
            ->assertOk();
    }

    public function test_bot_restriction_does_not_trigger_geolocation_lookup(): void
    {
        Setting::set('bot_restriction_enabled', '1');
        Http::fake();

        $this->withHeaders(['User-Agent' => 'curl/8.4.0'])->get('/')->assertStatus(403);

        Http::assertNothingSent();
    }

    public function test_bot_restriction_bypassed_by_authenticated_staff(): void
    {
        Setting::set('bot_restriction_enabled', '1');
        $staff = User::factory()->create(['is_customer' => false]);

        $this->actingAs($staff)
            ->withHeaders(['User-Agent' => 'curl/8.4.0'])
            ->get('/')
            ->assertOk();
    }

    public function test_bot_restriction_does_not_affect_admin_panel(): void
    {
        Setting::set('bot_restriction_enabled', '1');
        $staff = User::factory()->create(['is_customer' => false]);

        $this->actingAs($staff)
            ->withHeaders(['User-Agent' => 'curl/8.4.0'])
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_admin_can_test_geo_detection(): void
    {
        $staff = User::factory()->create(['is_customer' => false]);
        $this->fakeGeo('FR');

        $this->actingAs($staff)->get(route('admin.settings.test-geo'))
            ->assertOk()
            ->assertJson(['ok' => true, 'country' => 'FR']);
    }

    public function test_admin_test_geo_detection_reports_failure(): void
    {
        $staff = User::factory()->create(['is_customer' => false]);
        $this->fakeGeo(null, 500);

        $this->actingAs($staff)->get(route('admin.settings.test-geo'))
            ->assertOk()
            ->assertJson(['ok' => false, 'country' => null]);
    }
}
