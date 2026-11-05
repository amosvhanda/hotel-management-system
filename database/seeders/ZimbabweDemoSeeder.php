<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BookArea;
use App\Models\Booking;
use App\Models\Facility;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Team;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Zimbabwe mock-up content for demos and screenshots.
 *
 *   php artisan db:seed --class=ZimbabweDemoSeeder
 *
 * Everything here is FICTIONAL sample data (hotel, staff, guests, reviews).
 * Replace it with your real details from the admin panel before going live.
 * Safe to run more than once. Login emails and passwords are never changed.
 */
class ZimbabweDemoSeeder extends Seeder
{
    public function run(): void
    {
        // All or nothing: if any step fails, no data is changed.
        DB::transaction(function () {
            $this->hotel();
            $this->rooms();
            $this->services();
            $this->team();
            $this->testimonials();
            $this->blog();
            $this->comments();
            $this->bookings();
            $this->faqs();
            $this->homeGallery();
        });

        \App\Models\SiteSetting::flushCurrent();
    }

    private function hotel(): void
    {
        $site = SiteSetting::orderBy('id')->first() ?? new SiteSetting();
        $site->fill([
            'site_name'        => 'Mopane Heights Hotel',
            'tagline'          => 'Your Peaceful Stay in the Heart of Harare',
            'site_description' => 'Mopane Heights Hotel is a modern city hotel in Harare with comfortable rooms, a restaurant serving local and international dishes, free Wi-Fi and easy online booking with EcoCash, OneMoney or pay at the hotel.',
            'address'          => '45 Samora Machel Avenue, Harare, Zimbabwe',
            'phone'            => '+263 77 000 0000',
            'email'            => 'reservations@example.com',
            'meta_title'       => 'Mopane Heights Hotel | Book a Hotel Room in Harare',
            'meta_description' => 'Book comfortable rooms in central Harare. Free Wi-Fi, restaurant, airport transfers. Pay with EcoCash, OneMoney or at the hotel.',
            'meta_keywords'    => 'Harare hotel, Zimbabwe hotel, hotel booking Harare, EcoCash hotel booking',
            'timezone'         => 'Africa/Harare',
            'default_currency' => 'USD',
            'copyright'        => null,
        ]);
        // Use the demo logo only if no own logo is set (or it is still the old template one).
        if (blank($site->logo) || ! is_file(public_path($site->logo)) || $site->logo === 'upload/site/1770528607651701.png') {
            $site->logo = 'upload/site/mopane-heights-logo.png';
        }
        if (blank($site->favicon) || ! is_file(public_path($site->favicon))) {
            $site->favicon = 'upload/site/mopane-heights-favicon.png';
        }

        $home = SiteSetting::HOME_DEFAULTS;
        $home['hero_image'] = 'upload/site/mopane-hero.jpg';
        $home['faq_image'] = 'upload/site/mopane-faq.jpg';
        $this->copyAsset('frontend/assets/img/hotel/facade.jpg', $home['hero_image']);
        $this->copyAsset('frontend/assets/img/hotel/lounge.jpg', $home['faq_image']);
        $site->homepage = $home;
        $site->save();

        if ($area = BookArea::orderBy('id')->first()) {
            $area->update([
                'short_title' => 'Your stay',
                'main_title'  => 'Comfortable rooms in Harare, ready when you are',
                'short_desc'  => 'Check live availability, choose the room that suits you and confirm online. Pay at the hotel, or with EcoCash or OneMoney.',
                'link_url'    => null,
            ]);
        }
    }

    private function rooms(): void
    {
        $profiles = [
            ['name' => 'Standard Single Room', 'price' => 65, 'discount' => 0, 'capacity' => 1, 'adult' => 1, 'child' => 0, 'size' => 180, 'view' => 'City View', 'bed' => 'Queen Bed',
             'short' => 'A bright, quiet room for business travellers and solo guests, with a work desk, free Wi-Fi and a rain shower.',
             'long'  => 'Our Standard Single Rooms are ideal for a short business trip or a city break in Harare. Each room has a comfortable queen bed, a work desk, smart TV, free high-speed Wi-Fi, tea and coffee station and an en-suite bathroom with a rain shower. Breakfast is available in our restaurant every morning.',
             'features' => ['Free Wi-Fi', 'Air conditioning', 'Smart TV', 'Work desk', 'Tea & coffee station', 'Hair dryer', 'Safety box', 'Electronic door lock', 'Smoke alarms', 'Wake-up service', 'Slippers', 'Rain shower']],
            ['name' => 'Deluxe Double Room', 'price' => 95, 'discount' => 10, 'capacity' => 2, 'adult' => 2, 'child' => 1, 'size' => 240, 'view' => 'Garden View', 'bed' => 'King Bed',
             'short' => 'Spacious room with a king bed and garden views — perfect for couples visiting Harare.',
             'long'  => 'Relax in our Deluxe Double Rooms overlooking the hotel gardens. Enjoy a king-size bed, sitting area, smart TV, air conditioning, minibar, free Wi-Fi and a modern bathroom. A great choice for couples and weekend getaways.',
             'features' => ['Free Wi-Fi', 'Air conditioning', 'Smart TV', 'Work desk', 'Tea & coffee station', 'Hair dryer', 'Safety box', 'Electronic door lock', 'Smoke alarms', 'Wake-up service', 'Slippers', 'Rain shower', 'Minibar', 'Sitting area', 'Laundry & dry cleaning']],
            ['name' => 'Twin Room', 'price' => 110, 'discount' => 0, 'capacity' => 2, 'adult' => 2, 'child' => 1, 'size' => 260, 'view' => 'Pool View', 'bed' => 'Twin Beds',
             'short' => 'Two comfortable single beds and a view of the pool — great for colleagues, friends or family.',
             'long'  => 'Our Twin Rooms have two single beds, plenty of storage, a work desk, smart TV, air conditioning and free Wi-Fi. Ideal for colleagues attending a conference in Harare, friends travelling together or a parent with a child.',
             'features' => ['Free Wi-Fi', 'Air conditioning', 'Smart TV', 'Work desk', 'Tea & coffee station', 'Hair dryer', 'Safety box', 'Electronic door lock', 'Smoke alarms', 'Wake-up service', 'Slippers', 'Rain shower', 'Minibar', 'Laundry & dry cleaning', 'Pool access']],
            ['name' => 'Executive Suite', 'price' => 180, 'discount' => 10, 'capacity' => 3, 'adult' => 2, 'child' => 2, 'size' => 420, 'view' => 'City View', 'bed' => 'King Bed',
             'short' => 'Our most spacious suite with a separate lounge, king bed and panoramic views over the city.',
             'long'  => 'The Executive Suite offers a separate lounge and bedroom, king-size bed, dining area, Nespresso machine, minibar, bathtub and walk-in shower. Includes complimentary breakfast and airport transfer from Robert Gabriel Mugabe International Airport.',
             'features' => ['Free Wi-Fi', 'Air conditioning', 'Smart TV', 'Work desk', 'Tea & coffee station', 'Hair dryer', 'Safety box', 'Electronic door lock', 'Smoke alarms', 'Wake-up service', 'Slippers', 'Walk-in shower', 'Bathtub', 'Minibar', 'Nespresso machine', 'Separate lounge', 'Dining area', 'Complimentary breakfast', 'Airport transfer', '24-hour room service', 'Laundry & dry cleaning', 'Pool access']],
        ];

        foreach (Room::orderBy('id')->get()->values() as $i => $room) {
            $p = $profiles[$i % count($profiles)];
            RoomType::whereKey($room->roomtype_id)->update(['name' => $p['name']]);
            $room->update([
                'price'         => $p['price'],
                'discount'      => $p['discount'],
                'room_capacity' => $p['capacity'],
                'total_adult'   => $p['adult'],
                'total_child'   => $p['child'],
                'size'          => $p['size'],
                'view'          => $p['view'],
                'bed_style'     => $p['bed'],
                'short_desc'    => $p['short'],
                'description'   => $p['long'],
            ]);

            // Replace the room's features so every filter chip on /rooms has real data behind it.
            Facility::where('rooms_id', $room->id)->delete();
            foreach ($p['features'] as $feature) {
                Facility::create(['rooms_id' => $room->id, 'facility_name' => $feature]);
            }
        }
    }

    private function services(): void
    {
        $services = [
            ['Restaurant & Bar', 'Start the day with a full Zimbabwean or continental breakfast, and enjoy sadza and relish, grilled meats and international favourites at lunch and dinner. Our bar serves local and imported drinks until late.', 'bx bx-restaurant'],
            ['Airport Transfers', 'We collect you from Robert Gabriel Mugabe International Airport and bring you straight to the hotel. Book at least 24 hours before you arrive and share your flight number with the front desk.', 'bx bx-car'],
            ['Conference & Events', 'Air-conditioned meeting rooms for 10 to 120 people with projector, sound system, fast Wi-Fi and catering — ideal for workshops, board meetings, weddings and celebrations.', 'bx bx-chalkboard'],
            ['Free Wi-Fi', 'Fast, free Wi-Fi in every room and across the hotel, so you can work, stream and stay in touch with family.', 'bx bx-wifi'],
            ['Laundry & Dry Cleaning', 'Same-day laundry, ironing and dry cleaning. Hand your items to housekeeping before 10am and get them back the same evening.', 'bx bx-closet'],
            ['Swimming Pool', 'Cool off in our outdoor swimming pool surrounded by gardens, with sun loungers and poolside drinks.', 'bx bx-swim'],
        ];
        foreach ($services as [$title, $desc, $icon]) {
            Service::updateOrCreate(['title' => $title], ['description' => $desc, 'icon_class' => $icon]);
        }

        // Drop leftover template services so the homepage stays the Harare set.
        Service::whereNotIn('title', array_column($services, 0))->delete();
    }

    private function faqs(): void
    {
        $rows = [
            ['How do I book a room?', 'Choose your dates on the homepage, open a room, and complete checkout. You will need an account so we can keep your booking and invoice together.', 1],
            ['How can I pay?', 'Pay when you arrive, or settle now with EcoCash or NetOne OneMoney. You approve the payment on your phone, and the booking is marked paid as soon as it clears.', 2],
            ['What if the dates I want are taken?', 'The search only shows rooms that are free for your stay. If a payment is cancelled, those dates open again.', 3],
            ['Where do I find my confirmation?', 'Sign in and open My Bookings. You can review the stay and download a PDF invoice from the same account.', 4],
        ];
        foreach ($rows as [$question, $answer, $order]) {
            Faq::updateOrCreate(['question' => $question], ['answer' => $answer, 'sort_order' => $order]);
        }
    }

    private function homeGallery(): void
    {
        $shots = [
            ['pool.jpg', 'The pool, open from morning until the light goes'],
            ['dining.jpg', 'Dinner in the restaurant, when you are ready'],
            ['lobby.jpg', 'The lobby on Samora Machel Avenue'],
        ];

        Gallery::query()->update(['show_on_home' => false]);
        foreach ($shots as [$file, $caption]) {
            $dest = 'upload/gallery/mopane-' . $file;
            $this->copyAsset('frontend/assets/img/hotel/' . $file, $dest);
            if (is_file(public_path($dest))) {
                Gallery::updateOrCreate(
                    ['photo_name' => $dest],
                    ['caption' => $caption, 'show_on_home' => true]
                );
            }
        }
    }

    private function copyAsset(string $from, string $to): void
    {
        $dest = public_path($to);
        if (is_file($dest) || ! is_file(public_path($from))) {
            return;
        }
        @mkdir(dirname($dest), 0755, true);
        copy(public_path($from), $dest);
    }

    private function team(): void
    {
        $people = [
            ['Tendai Moyo', 'General Manager'],
            ['Rutendo Chikwanha', 'Front Office Manager'],
            ['Tatenda Ncube', 'Head Chef'],
        ];
        foreach (Team::orderBy('id')->get()->values() as $i => $member) {
            [$name, $role] = $people[$i % count($people)];
            $member->update(['name' => $name, 'postion' => $role, 'facebook' => '']);
        }
    }

    private function testimonials(): void
    {
        $reviews = [
            ['Farai Mutasa', 'Bulawayo', 'Booking with EcoCash took less than five minutes and the room was spotless. Friendly staff and a great breakfast — we will definitely be back.'],
            ['Nyasha Chirwa', 'Harare', 'We held our company workshop here. The conference room, catering and Wi-Fi were excellent and the team looked after every detail.'],
            ['Thabo Dlamini', 'Johannesburg', 'The airport pickup was waiting when I landed and check-in was quick. A comfortable, quiet stay in the middle of Harare.'],
        ];
        $existing = Testimonial::orderBy('id')->get()->values();
        foreach ($reviews as $i => [$name, $city, $message]) {
            $row = $existing[$i] ?? new Testimonial(['image' => $existing[0]->image ?? null]);
            $row->fill(['name' => $name, 'city' => $city, 'message' => $message])->save();
        }
    }

    private function blog(): void
    {
        $cats = ['Harare City Guide', 'Travel in Zimbabwe', 'Food & Dining', 'Travel Tips', 'Hotel News'];
        foreach (BlogCategory::orderBy('id')->get()->values() as $i => $cat) {
            $name = $cats[$i % count($cats)];
            $cat->update(['category_name' => $name, 'category_slug' => Str::slug($name)]);
        }
        $catIds = BlogCategory::orderBy('id')->pluck('id')->values();

        $posts = [
            ['Top Things to Do in Harare This Weekend', 0,
             'From Mbare Musika to the National Heroes Acre and the Harare Gardens, here are our favourite ways to spend a weekend in the capital.',
             "Harare has plenty to offer a weekend visitor. Start with a walk through the Harare Gardens, visit the National Gallery of Zimbabwe for contemporary Zimbabwean art, then head to a local market for crafts and fresh produce.\n\nIn the evening, enjoy dinner in Borrowdale or Avondale, or relax at our own restaurant. Ask the front desk for directions, taxi bookings or a personalised itinerary."],
            ['Planning a Trip to Victoria Falls from Harare', 1,
             'Flying, driving or taking the bus — how to plan a trip to Victoria Falls with a stopover in Harare.',
             "Many guests combine a few nights in Harare with a trip to Victoria Falls. Domestic flights take about an hour, while the drive is a full day with stops in Kadoma, Kwekwe and Bulawayo or via Hwange.\n\nTip: visit between February and May to see the falls at their fullest, or September to December for clearer views. Our front desk can arrange an airport transfer for your connecting flight."],
            ['How to Pay for Your Booking with EcoCash or OneMoney', 3,
             'Paying online for your room is quick and secure. Here is exactly how mobile money payments work on our website.',
             "When you reach checkout, choose EcoCash (Econet numbers starting 077 or 078) or OneMoney (NetOne numbers starting 071) and enter your mobile number.\n\nYou will get a prompt on your phone — enter your PIN to approve. The booking page updates automatically once the payment is confirmed and your booking shows as Paid under My Bookings. Prefer to pay later? Choose Pay at the hotel instead."],
            ['A Taste of Zimbabwe: Dishes to Try at Our Restaurant', 2,
             'Sadza, nyama, muriwo and more — our chef shares the local favourites every visitor should try.',
             "Our kitchen celebrates Zimbabwean food. Try sadza with beef stew or road runner chicken, muriwo une dovi (greens in peanut butter), and mazhanje when it's in season.\n\nWe also serve international dishes and vegetarian options every day. Breakfast runs from 6:30am, and the bar is open until late."],
        ];

        foreach (BlogPost::orderBy('id')->get()->values() as $i => $post) {
            if (! isset($posts[$i])) {
                break;
            }
            [$title, $cat, $short, $long] = $posts[$i];
            $post->update([
                'post_titile' => $title,
                'post_slug'   => Str::slug($title),
                'blogcat_id'  => $catIds[$cat] ?? $post->blogcat_id,
                'short_descp' => $short,
                'long_descp'  => '<p>' . str_replace("\n\n", '</p><p>', e($long)) . '</p>',
            ]);
        }
    }

    private function comments(): void
    {
        $messages = [
            'Very helpful, thank you! We followed this guide on our last visit and loved it.',
            'Great tips. The EcoCash payment was quick and easy when I booked.',
            'Thanks for sharing — looking forward to trying the restaurant on our next stay in Harare.',
        ];
        foreach (\App\Models\Comment::orderBy('id')->get()->values() as $i => $comment) {
            $comment->update(['message' => $messages[$i % count($messages)]]);
        }
    }

    private function bookings(): void
    {
        // Demo guest names on demo bookings (accounts and their logins are not changed).
        $guests = [
            ['Farai Mutasa', 'Harare', 'Borrowdale, Harare'],
            ['Nyasha Chirwa', 'Harare', 'Avondale, Harare'],
            ['Tinashe Banda', 'Bulawayo', 'Suburbs, Bulawayo'],
            ['Chipo Mlambo', 'Manicaland', 'Mutare'],
            ['Kudakwashe Sibanda', 'Midlands', 'Gweru'],
            ['Rumbidzai Nyathi', 'Masvingo', 'Masvingo'],
        ];
        foreach (Booking::orderBy('id')->get()->values() as $i => $b) {
            [$name, $province, $address] = $guests[$i % count($guests)];
            $b->update([
                'name'     => $name,
                'email'    => Str::slug($name, '.') . '@example.com',
                'phone'    => '077' . str_pad((string) (1000000 + $i), 7, '0', STR_PAD_LEFT),
                'country'  => 'Zimbabwe',
                'state'    => $province,
                'address'  => $address,
                'zip_code' => null,
            ]);
        }

        // Friendlier display names for the two sample guest accounts.
        User::where('email', 'user@gmail.com')->where('name', 'User')->update(['name' => 'Tafadzwa Moyo']);
        User::where('email', 'khan@gmail.com')->where('name', 'Khan')->update(['name' => 'Kudzai Dube']);
    }
}
