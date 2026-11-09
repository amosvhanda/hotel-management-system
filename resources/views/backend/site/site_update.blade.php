@extends('admin.admin_dashboard')
@section('admin')

<div class="page-content">
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Update Site Setting</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Update Site Setting</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="container">
        <div class="main-body">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">

                        <form action="{{ route('site.update') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{ $site->id }}">

                            <div class="card-body">

                                <!-- Site Name -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-site_name">Site Name</label></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input id="f-site_name" type="text" name="site_name" class="form-control" value="{{ old('site_name', $site->site_name) }}" />
                                    </div>
                                </div>

                                <!-- Tagline -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-tagline">Tagline</label></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input id="f-tagline" type="text" name="tagline" class="form-control" value="{{ old('tagline', $site->tagline) }}" />
                                    </div>
                                </div>

                                <!-- Phone -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-phone">Phone</label></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input id="f-phone" type="text" name="phone" class="form-control" value="{{ old('phone', $site->phone) }}" />
                                    </div>
                                </div>

                                <!-- Address -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-address">Address</label></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input id="f-address" type="text" name="address" class="form-control" value="{{ old('address', $site->address) }}" />
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-email">Email</label></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input id="f-email" type="email" name="email" class="form-control" value="{{ old('email', $site->email) }}" />
                                    </div>
                                </div>

                                <!-- Support Email -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-support_email">Support Email</label></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input id="f-support_email" type="email" name="support_email" class="form-control" value="{{ old('support_email', $site->support_email) }}" />
                                    </div>
                                </div>

                                <!-- Facebook -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-facebook">Facebook</label></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input id="f-facebook" type="text" name="facebook" class="form-control" value="{{ old('facebook', $site->facebook) }}" />
                                    </div>
                                </div>

                                <!-- Twitter -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-twitter">Twitter</label></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input id="f-twitter" type="text" name="twitter" class="form-control" value="{{ old('twitter', $site->twitter) }}" />
                                    </div>
                                </div>

                                <!-- Instagram -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-instagram">Instagram</label></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input id="f-instagram" type="text" name="instagram" class="form-control" value="{{ old('instagram', $site->instagram) }}" />
                                    </div>
                                </div>

                                <!-- LinkedIn -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-linkedin">LinkedIn</label></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input id="f-linkedin" type="text" name="linkedin" class="form-control" value="{{ old('linkedin', $site->linkedin) }}" />
                                    </div>
                                </div>

                                <!-- YouTube -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-youtube">YouTube</label></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input id="f-youtube" type="text" name="youtube" class="form-control" value="{{ old('youtube', $site->youtube) }}" />
                                    </div>
                                </div>

                                <!-- Copyright -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-copyright">Copyright</label></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input id="f-copyright" type="text" name="copyright" class="form-control" value="{{ old('copyright', $site->copyright) }}" />
                                    </div>
                                </div>

                                <!-- Logo Upload -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-logo">Logo</label></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input id="f-logo" type="file" name="logo" class="form-control" accept="image/*" />
                                        <img src="{{ $site->logo_url }}" alt="Logo" style="max-width:220px; max-height:100px; margin-top:10px;">
                                    </div>
                                </div>

                                <!-- Favicon Upload -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-favicon">Favicon</label></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input id="f-favicon" type="file" name="favicon" class="form-control" accept="image/*" />
                                        <img src="{{ $site->favicon_url }}" alt="Favicon" style="width:50px; height:50px; margin-top:10px;">
                                    </div>
                                </div>

                                <!-- Site Description -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-site_description">Site Description</label></div>
                                    <div class="col-sm-9 text-secondary">
                                        <textarea id="f-site_description" name="site_description" rows="4" class="form-control">{{ old('site_description', $site->site_description) }}</textarea>
                                    </div>
                                </div>

                                <!-- SEO Meta Title -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-meta_title">Meta Title</label></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input id="f-meta_title" type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $site->meta_title) }}" />
                                    </div>
                                </div>

                                <!-- SEO Meta Description -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-meta_description">Meta Description</label></div>
                                    <div class="col-sm-9 text-secondary">
                                        <textarea id="f-meta_description" name="meta_description" rows="3" class="form-control">{{ old('meta_description', $site->meta_description) }}</textarea>
                                    </div>
                                </div>

                                <!-- SEO Meta Keywords -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-meta_keywords">Meta Keywords</label></div>
                                    <div class="col-sm-9 text-secondary">
                                        <textarea id="f-meta_keywords" name="meta_keywords" rows="2" class="form-control">{{ old('meta_keywords', $site->meta_keywords) }}</textarea>
                                    </div>
                                </div>

                                <!-- Google Search Console -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-gsv">Google Search Console code</label></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input id="f-gsv" type="text" name="google_site_verification" class="form-control" value="{{ old('google_site_verification', $site->google_site_verification) }}" placeholder="e.g. a1B2c3D4e5…">
                                        <small>From Search Console → Add property → HTML tag: paste only the <code>content="…"</code> value. Your sitemap is <code>{{ url('/sitemap.xml') }}</code>.</small>
                                    </div>
                                </div>

                                <!-- Google Analytics ID -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-google_analytics">Google Analytics ID</label></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input id="f-google_analytics" type="text" name="google_analytics" class="form-control" value="{{ old('google_analytics', $site->google_analytics) }}" />
                                    </div>
                                </div>

                                <!-- Timezone -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-timezone">Timezone</label></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input id="f-timezone" type="text" name="timezone" class="form-control" value="{{ old('timezone', $site->timezone) }}" />
                                    </div>
                                </div>

                                <!-- Language -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-language">Language</label></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input id="f-language" type="text" name="language" class="form-control" value="{{ old('language', $site->language) }}" />
                                    </div>
                                </div>

                                <!-- Maintenance Mode -->
                                <div class="row mb-3 align-items-center">
                                    <div class="col-sm-3"><h6 class="mb-0">Maintenance Mode</h6></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="checkbox" name="maintenance_mode" value="1" {{ old('maintenance_mode', $site->maintenance_mode) ? 'checked' : '' }} />
                                    </div>
                                </div>

                                <!-- Social Sharing Enabled -->
                                <div class="row mb-3 align-items-center">
                                    <div class="col-sm-3"><h6 class="mb-0">Social Sharing Enabled</h6></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="checkbox" name="social_sharing_enabled" value="1" {{ old('social_sharing_enabled', $site->social_sharing_enabled) ? 'checked' : '' }} />
                                    </div>
                                </div>

                                <!-- Custom CSS -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-custom_css">Custom CSS</label></div>
                                    <div class="col-sm-9 text-secondary">
                                        <textarea id="f-custom_css" name="custom_css" rows="4" class="form-control">{{ old('custom_css', $site->custom_css) }}</textarea>
                                    </div>
                                </div>

                                <!-- Custom JS -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-custom_js">Custom JS</label></div>
                                    <div class="col-sm-9 text-secondary">
                                        <textarea id="f-custom_js" name="custom_js" rows="4" class="form-control">{{ old('custom_js', $site->custom_js) }}</textarea>
                                    </div>
                                </div>

                                <!-- Default Currency -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-default_currency">Default Currency</label></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input id="f-default_currency" type="text" name="default_currency" class="form-control" value="{{ old('default_currency', $site->default_currency) }}" />
                                    </div>
                                </div>

                                <!-- Default Locale -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-default_locale">Default Locale</label></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input id="f-default_locale" type="text" name="default_locale" class="form-control" value="{{ old('default_locale', $site->default_locale) }}" />
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="submit" class="btn btn-primary px-4" value="Save changes" />
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
