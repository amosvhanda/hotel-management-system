<?php

namespace Tests\Feature;

use Database\Seeders\SettingsSeeder;
use Database\Seeders\ZimbabweDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesHotelData;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase, CreatesHotelData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SettingsSeeder::class);
        $this->makeRoom(2);
        $this->seed(ZimbabweDemoSeeder::class);
    }

    public function test_homepage_shows_hotel_rooms_services_and_working_links(): void
    {
        $html = $this->get('/')->assertOk()
            ->assertSee('Mopane Heights Hotel')
            ->assertSee('A quiet room in the middle of Harare.')
            ->assertSee('Standard Single Room')
            ->assertSee('Restaurant &amp; Bar', false)
            ->assertSee('How do I book a room?')
            ->assertSee('US$')
            ->assertDontSee('Sea-facing')
            ->assertDontSee('Evenings here are the whole point')
            ->assertSee(route('room.details', 1))
            ->getContent();

        $this->assertStringNotContainsString('room-details.html', $html);
        $this->assertStringNotContainsString('team.html', $html);
        $this->assertDoesNotMatchRegularExpression('/href="#"/', $html);
        $this->assertDoesNotMatchRegularExpression('/lorem ipsum/i', $html);
    }

    public function test_main_pages_load(): void
    {
        foreach (['/about', '/rooms', '/room/details/1', '/service/details/1', '/blog', '/gallery', '/contact', '/login', '/register', '/forgot-password'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_unknown_page_shows_friendly_404(): void
    {
        $this->get('/no-such-page')->assertNotFound()->assertSee('Page not found');
    }

    public function test_contact_form_validates_and_saves(): void
    {
        $this->get('/contact')->assertOk()
            ->assertSee('Write, call, or come by.')
            ->assertSee('mailto:', false)
            ->assertDontSee('best agency')
            ->assertDontSee('Let\'s Start to Give Us a Message');

        $this->from('/contact')->post('/store/contact', [])->assertSessionHasErrors(['name', 'email', 'reason', 'subject', 'message']);

        $this->post('/store/contact', [
            'name' => 'Farai', 'email' => 'farai@example.com', 'reason' => 'event', 'subject' => 'Conference', 'message' => 'Do you have a room for 20 people?',
        ])->assertSessionHas('alert-type', 'success');
        $this->assertDatabaseHas('contacts', [
            'email' => 'farai@example.com',
            'subject' => 'An event or meeting — Conference',
        ]);
    }

    public function test_demo_seeder_can_run_twice_without_duplicates(): void
    {
        $this->seed(ZimbabweDemoSeeder::class);
        $this->assertSame(6, \App\Models\Service::count());
    }
}
