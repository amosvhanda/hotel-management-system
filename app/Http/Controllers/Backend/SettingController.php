<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SmtpSetting;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Schema;
use Intervention\Image\Facades\Image;

class SettingController extends Controller
{
    public function SmtpSetting()
    {
        $smtp = SmtpSetting::orderBy('id')->first() ?? SmtpSetting::create(['mailer' => 'smtp']);
        return view('backend.setting.smpt_update', compact('smtp'));
    }

    public function SmtpUpdate(Request $request)
    {
        SmtpSetting::findOrFail($request->id)->update($request->only([
            'mailer', 'host', 'port', 'username', 'password', 'encryption', 'from_address'
        ]));

        return back()->with([
            'message' => 'SMTP settings updated successfully',
            'alert-type' => 'success'
        ]);
    }

    public function Theme()
    {
        $site = SiteSetting::orderBy('id')->first() ?? SiteSetting::create(['site_name' => config('app.name')]);
        $ready = Schema::hasColumn('site_settings', 'theme');

        return view('backend.setting.theme', [
            'site' => $site,
            'colors' => $site->themeColors(),
            'defaults' => SiteSetting::THEME_DEFAULTS,
            'ready' => $ready,
        ]);
    }

    public function ThemeUpdate(Request $request)
    {
        if (! Schema::hasColumn('site_settings', 'theme')) {
            return back()->with(['message' => 'Run "php artisan migrate" first, then save again.', 'alert-type' => 'error']);
        }

        $rules = [];
        foreach (array_keys(SiteSetting::THEME_DEFAULTS) as $key) {
            $rules[$key] = ['required', 'regex:/^#[0-9a-fA-F]{6}$/'];
        }
        $data = $request->validate($rules, ['*.regex' => 'Use a colour like #1f3a2b.']);

        $site = SiteSetting::orderBy('id')->firstOrFail();
        $site->update(['theme' => array_map('strtolower', $data)]);

        return back()->with(['message' => 'Brand colours saved. They now apply to the website, sign-in pages and admin.', 'alert-type' => 'success']);
    }

    public function ThemeReset()
    {
        if (Schema::hasColumn('site_settings', 'theme')) {
            SiteSetting::orderBy('id')->first()?->update(['theme' => null]);
        }

        return redirect()->route('theme.setting')->with(['message' => 'Brand colours reset to the original palette.', 'alert-type' => 'success']);
    }

    public function SiteSetting()
    {
        $site = SiteSetting::orderBy('id')->first() ?? SiteSetting::create(['site_name' => config('app.name')]);
        return view('backend.site.site_update', compact('site'));
    }

    public function SiteUpdate(Request $request)
    {
        $site = SiteSetting::findOrFail($request->id);

        $request->validate([
            'site_name' => 'nullable|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'support_email' => 'nullable|email',
            'site_description' => 'nullable|string|max:5000',
            'default_currency' => 'nullable|string|max:10',
            'google_analytics' => 'nullable|string|max:50',
            'logo' => 'nullable|image|max:4096',
            'favicon' => 'nullable|image|max:1024',
        ]);

        $data = $request->only([
            'site_name',
            'tagline',
            'phone',
            'email',
            'support_email',
            'address',
            'facebook',
            'twitter',
            'instagram',
            'linkedin',
            'youtube',
            'site_description',
            'meta_title',
            'meta_description',
            'meta_keywords',
            'google_analytics',
            'copyright',
            'timezone',
            'language',
            'custom_css',
            'custom_js',
            'default_currency',
            'default_locale',
        ]);

        // Checkboxes are absent from the request when they are off.
        $data['maintenance_mode'] = $request->boolean('maintenance_mode');
        $data['social_sharing_enabled'] = $request->boolean('social_sharing_enabled');

        if ($request->file('logo')) {
            $data['logo'] = $this->storeSiteImage($request->file('logo'), 'logo', null, 160);
        }

        if ($request->file('favicon')) {
            $favicon = $request->file('favicon');
            $favicon_name = 'favicon_' . hexdec(uniqid()) . '.' . $favicon->getClientOriginalExtension();
            @mkdir(public_path('upload/site'), 0755, true);
            Image::make($favicon)->resize(64, 64)->save(public_path('upload/site/' . $favicon_name));
            $data['favicon'] = 'upload/site/' . $favicon_name;
        }

        $site->update($data);

        return back()->with([
            'message' => 'Site settings updated successfully',
            'alert-type' => 'success'
        ]);
    }

    public function Homepage()
    {
        $site = SiteSetting::orderBy('id')->first() ?? SiteSetting::create(['site_name' => config('app.name')]);

        return view('backend.site.homepage', compact('site'));
    }

    public function HomepageUpdate(Request $request)
    {
        $site = SiteSetting::findOrFail($request->id);

        $request->validate(array_merge(
            collect(SiteSetting::HOME_TEXT_KEYS)->mapWithKeys(fn ($key) => [
                $key => in_array($key, SiteSetting::HOME_LONG_KEYS, true)
                    ? 'nullable|string|max:1000'
                    : 'nullable|string|max:255',
            ])->all(),
            [
                'hero_image' => 'nullable|image|max:5120',
                'faq_image' => 'nullable|image|max:5120',
            ]
        ));

        $home = $site->homepage ?? [];
        foreach (SiteSetting::HOME_TEXT_KEYS as $key) {
            $home[$key] = $request->input($key);
        }

        if ($request->file('hero_image')) {
            $home['hero_image'] = $this->storeSiteImage($request->file('hero_image'), 'hero', 1920, null);
        }
        if ($request->file('faq_image')) {
            $home['faq_image'] = $this->storeSiteImage($request->file('faq_image'), 'faq', 1400, null);
        }

        $site->update(['homepage' => $home]);

        return back()->with([
            'message' => 'Homepage updated successfully',
            'alert-type' => 'success',
        ]);
    }

    /** Keep uploads sharp. CSS decides how tall the logo looks in the header. */
    private function storeSiteImage($file, string $prefix, ?int $maxWidth, ?int $maxHeight): string
    {
        $name = $prefix . '_' . hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();
        @mkdir(public_path('upload/site'), 0755, true);
        Image::make($file)->resize($maxWidth, $maxHeight, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->save(public_path('upload/site/' . $name), 88);

        return 'upload/site/' . $name;
    }
}
