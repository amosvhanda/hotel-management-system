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
                                    <div class="col-sm-3"><h6 class="mb-0">Site Name</h6></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="site_name" class="form-control" value="{{ old('site_name', $site->site_name) }}" />
                                    </div>
                                </div>

                                <!-- Tagline -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">Tagline</h6></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="tagline" class="form-control" value="{{ old('tagline', $site->tagline) }}" />
                                    </div>
                                </div>

                                <!-- Phone -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">Phone</h6></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $site->phone) }}" />
                                    </div>
                                </div>

                                <!-- Address -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">Address</h6></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="address" class="form-control" value="{{ old('address', $site->address) }}" />
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">Email</h6></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="email" name="email" class="form-control" value="{{ old('email', $site->email) }}" />
                                    </div>
                                </div>

                                <!-- Support Email -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">Support Email</h6></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="email" name="support_email" class="form-control" value="{{ old('support_email', $site->support_email) }}" />
                                    </div>
                                </div>

                                <!-- Facebook -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">Facebook</h6></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="facebook" class="form-control" value="{{ old('facebook', $site->facebook) }}" />
                                    </div>
                                </div>

                                <!-- Twitter -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">Twitter</h6></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="twitter" class="form-control" value="{{ old('twitter', $site->twitter) }}" />
                                    </div>
                                </div>

                                <!-- Instagram -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">Instagram</h6></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="instagram" class="form-control" value="{{ old('instagram', $site->instagram) }}" />
                                    </div>
                                </div>

                                <!-- LinkedIn -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">LinkedIn</h6></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="linkedin" class="form-control" value="{{ old('linkedin', $site->linkedin) }}" />
                                    </div>
                                </div>

                                <!-- YouTube -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">YouTube</h6></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="youtube" class="form-control" value="{{ old('youtube', $site->youtube) }}" />
                                    </div>
                                </div>

                                <!-- Copyright -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">Copyright</h6></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="copyright" class="form-control" value="{{ old('copyright', $site->copyright) }}" />
                                    </div>
                                </div>

                                <!-- Logo Upload -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">Logo</h6></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="file" name="logo" class="form-control" />
                                        @if($site->logo)
                                            <img src="{{ asset($site->logo) }}" alt="Logo" style="width:100px; height:100px; margin-top:10px;">
                                        @endif
                                    </div>
                                </div>

                                <!-- Favicon Upload -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">Favicon</h6></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="file" name="favicon" class="form-control" />
                                        @if($site->favicon)
                                            <img src="{{ asset($site->favicon) }}" alt="Favicon" style="width:50px; height:50px; margin-top:10px;">
                                        @endif
                                    </div>
                                </div>

                                <!-- Site Description -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">Site Description</h6></div>
                                    <div class="col-sm-9 text-secondary">
                                        <textarea name="site_description" rows="4" class="form-control">{{ old('site_description', $site->site_description) }}</textarea>
                                    </div>
                                </div>

                                <!-- SEO Meta Title -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">Meta Title</h6></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $site->meta_title) }}" />
                                    </div>
                                </div>

                                <!-- SEO Meta Description -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">Meta Description</h6></div>
                                    <div class="col-sm-9 text-secondary">
                                        <textarea name="meta_description" rows="3" class="form-control">{{ old('meta_description', $site->meta_description) }}</textarea>
                                    </div>
                                </div>

                                <!-- SEO Meta Keywords -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">Meta Keywords</h6></div>
                                    <div class="col-sm-9 text-secondary">
                                        <textarea name="meta_keywords" rows="2" class="form-control">{{ old('meta_keywords', $site->meta_keywords) }}</textarea>
                                    </div>
                                </div>

                                <!-- Google Analytics ID -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">Google Analytics ID</h6></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="google_analytics" class="form-control" value="{{ old('google_analytics', $site->google_analytics) }}" />
                                    </div>
                                </div>

                                <!-- Timezone -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">Timezone</h6></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="timezone" class="form-control" value="{{ old('timezone', $site->timezone) }}" />
                                    </div>
                                </div>

                                <!-- Language -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">Language</h6></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="language" class="form-control" value="{{ old('language', $site->language) }}" />
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
                                    <div class="col-sm-3"><h6 class="mb-0">Custom CSS</h6></div>
                                    <div class="col-sm-9 text-secondary">
                                        <textarea name="custom_css" rows="4" class="form-control">{{ old('custom_css', $site->custom_css) }}</textarea>
                                    </div>
                                </div>

                                <!-- Custom JS -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">Custom JS</h6></div>
                                    <div class="col-sm-9 text-secondary">
                                        <textarea name="custom_js" rows="4" class="form-control">{{ old('custom_js', $site->custom_js) }}</textarea>
                                    </div>
                                </div>

                                <!-- Default Currency -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">Default Currency</h6></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="default_currency" class="form-control" value="{{ old('default_currency', $site->default_currency) }}" />
                                    </div>
                                </div>

                                <!-- Default Locale -->
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">Default Locale</h6></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="default_locale" class="form-control" value="{{ old('default_locale', $site->default_locale) }}" />
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="submit" class="btn btn-primary px-4" value="Save Changes" />
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
