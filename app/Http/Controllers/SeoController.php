<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Room;
use App\Models\Service;

/** sitemap.xml and robots.txt for Google. */
class SeoController extends Controller
{
    public function sitemap()
    {
        $urls = collect([
            ['loc' => route('home'), 'priority' => '1.0', 'freq' => 'weekly'],
            ['loc' => route('froom.all'), 'priority' => '0.9', 'freq' => 'weekly'],
            ['loc' => route('about'), 'priority' => '0.6', 'freq' => 'monthly'],
            ['loc' => route('show.gallery'), 'priority' => '0.5', 'freq' => 'monthly'],
            ['loc' => route('contact.us'), 'priority' => '0.5', 'freq' => 'yearly'],
            ['loc' => route('blog.list'), 'priority' => '0.6', 'freq' => 'weekly'],
        ]);

        foreach (Room::where('status', 1)->get(['id', 'updated_at']) as $room) {
            $urls->push(['loc' => route('room.details', $room->id), 'lastmod' => $room->updated_at, 'priority' => '0.9', 'freq' => 'weekly']);
        }
        foreach (Service::get(['id', 'updated_at']) as $service) {
            $urls->push(['loc' => route('service.details', $service->id), 'lastmod' => $service->updated_at, 'priority' => '0.5', 'freq' => 'monthly']);
        }
        foreach (BlogPost::latest()->get(['post_slug', 'updated_at']) as $post) {
            $urls->push(['loc' => route('blog.details', $post->post_slug), 'lastmod' => $post->updated_at, 'priority' => '0.6', 'freq' => 'monthly']);
        }

        return response()->view('seo.sitemap', ['urls' => $urls], 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots()
    {
        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /dashboard',
            'Disallow: /checkout',
            'Disallow: /user/',
            'Disallow: /profile',
            'Allow: /',
            '',
            'Sitemap: ' . url('/sitemap.xml'),
        ];

        return response(implode("\n", $lines) . "\n", 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
