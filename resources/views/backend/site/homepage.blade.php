@extends('admin.admin_dashboard')
@section('admin')

@php($home = $site->homepage ?? [])
@php($value = fn ($key) => old($key, $home[$key] ?? \App\Models\SiteSetting::HOME_DEFAULTS[$key] ?? ''))

<div class="page-content">
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Homepage</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Homepage</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container">
        <div class="main-body">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <form action="{{ route('homepage.update') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{ $site->id }}">
                            <div class="card-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        @foreach ($errors->all() as $error)
                                            <div>{{ $error }}</div>
                                        @endforeach
                                    </div>
                                @endif

                                <h5 class="mb-3">Opening photograph</h5>
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-hero_kicker">Small line</label></div>
                                    <div class="col-sm-9">
                                        <input id="f-hero_kicker" type="text" name="hero_kicker" class="form-control" value="{{ $value('hero_kicker') }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-hero_title">Headline</label></div>
                                    <div class="col-sm-9">
                                        <input id="f-hero_title" type="text" name="hero_title" class="form-control" value="{{ $value('hero_title') }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-hero_text">Supporting line</label></div>
                                    <div class="col-sm-9">
                                        <textarea id="f-hero_text" name="hero_text" rows="3" class="form-control">{{ $value('hero_text') }}</textarea>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-hero_image">Photograph</label></div>
                                    <div class="col-sm-9">
                                        <input id="f-hero_image" type="file" name="hero_image" class="form-control" accept="image/*">
                                        <img src="{{ $site->heroImageUrl() }}" alt="" class="mt-2" style="width:220px;height:120px;object-fit:cover;border-radius:8px">
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-booking_note">Booking note</label></div>
                                    <div class="col-sm-9">
                                        <input id="f-booking_note" type="text" name="booking_note" class="form-control" value="{{ $value('booking_note') }}">
                                        <small class="text-muted">Shown under the date form. Mention how guests can pay.</small>
                                    </div>
                                </div>

                                <h5 class="mb-3">Section headings</h5>
                                @foreach ([
                                    'rooms' => 'Rooms',
                                    'moments' => 'Photo strip',
                                    'services' => 'Services',
                                    'team' => 'Team',
                                    'guests' => 'Guest quotes',
                                    'journal' => 'Journal',
                                ] as $key => $label)
                                    <div class="row mb-3">
                                        <div class="col-sm-3"><label class="form-label mb-0" for="f-key-_kicker">{{ $label }}</label></div>
                                        <div class="col-sm-9">
                                            <input id="f-key-_kicker" type="text" name="{{ $key }}_kicker" class="form-control mb-2" value="{{ $value($key . '_kicker') }}" placeholder="Small line">
                                            <input type="text" name="{{ $key }}_title" class="form-control" value="{{ $value($key . '_title') }}" placeholder="Heading">
                                        </div>
                                    </div>
                                @endforeach

                                <h5 class="mb-3">Questions</h5>
                                <p class="text-muted">The questions themselves are edited under FAQ. These lines sit above them.</p>
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-faq_kicker">Small line</label></div>
                                    <div class="col-sm-9">
                                        <input id="f-faq_kicker" type="text" name="faq_kicker" class="form-control" value="{{ $value('faq_kicker') }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-faq_title">Heading</label></div>
                                    <div class="col-sm-9">
                                        <input id="f-faq_title" type="text" name="faq_title" class="form-control" value="{{ $value('faq_title') }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-faq_intro">Intro</label></div>
                                    <div class="col-sm-9">
                                        <textarea id="f-faq_intro" name="faq_intro" rows="2" class="form-control">{{ $value('faq_intro') }}</textarea>
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-faq_image">Side photograph</label></div>
                                    <div class="col-sm-9">
                                        <input id="f-faq_image" type="file" name="faq_image" class="form-control" accept="image/*">
                                        <img src="{{ $site->faqImageUrl() }}" alt="" class="mt-2" style="width:220px;height:120px;object-fit:cover;border-radius:8px">
                                    </div>
                                </div>

                                <h5 class="mb-3">Other pages</h5>
                                <p class="text-muted">About, contact, the rooms list, the gallery, and the footer note. Rooms, services, staff, photos, and articles are edited in their own menus.</p>
                                @foreach ([
                                    'about_kicker' => 'About, small line',
                                    'about_title' => 'About heading',
                                ] as $key => $label)
                                    <div class="row mb-3">
                                        <div class="col-sm-3"><label class="form-label mb-0" for="f-key">{{ $label }}</label></div>
                                        <div class="col-sm-9"><input id="f-key" type="text" name="{{ $key }}" class="form-control" value="{{ $value($key) }}"></div>
                                    </div>
                                @endforeach
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-about_text">About paragraph</label></div>
                                    <div class="col-sm-9">
                                        <textarea id="f-about_text" name="about_text" rows="3" class="form-control">{{ $value('about_text') }}</textarea>
                                        <small class="text-muted">Leave this as the sample and the About page uses the description from Site Setting instead.</small>
                                    </div>
                                </div>
                                @foreach (['about_point_1' => 'About point 1', 'about_point_2' => 'About point 2', 'about_point_3' => 'About point 3'] as $key => $label)
                                    <div class="row mb-3">
                                        <div class="col-sm-3"><label class="form-label mb-0" for="f-key-2">{{ $label }}</label></div>
                                        <div class="col-sm-9"><input id="f-key-2" type="text" name="{{ $key }}" class="form-control" value="{{ $value($key) }}"></div>
                                    </div>
                                @endforeach
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-about_why_kicker">Why stay</label></div>
                                    <div class="col-sm-9">
                                        <input id="f-about_why_kicker" type="text" name="about_why_kicker" class="form-control mb-2" value="{{ $value('about_why_kicker') }}" placeholder="Small line">
                                        <input type="text" name="about_why_title" class="form-control" value="{{ $value('about_why_title') }}" placeholder="Heading">
                                    </div>
                                </div>
                                @foreach ([1, 2, 3] as $n)
                                    <div class="row mb-3">
                                        <div class="col-sm-3"><label class="form-label mb-0" for="f-about_feature_-n-_title">Reason {{ $n }}</label></div>
                                        <div class="col-sm-9">
                                            <input id="f-about_feature_-n-_title" type="text" name="about_feature_{{ $n }}_title" class="form-control mb-2" value="{{ $value('about_feature_'.$n.'_title') }}">
                                            <textarea name="about_feature_{{ $n }}_text" rows="2" class="form-control">{{ $value('about_feature_'.$n.'_text') }}</textarea>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="row mb-3">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-about_cta_title">About closing line</label></div>
                                    <div class="col-sm-9">
                                        <input id="f-about_cta_title" type="text" name="about_cta_title" class="form-control mb-2" value="{{ $value('about_cta_title') }}">
                                        <input type="text" name="about_cta_text" class="form-control" value="{{ $value('about_cta_text') }}">
                                    </div>
                                </div>
                                @foreach ([
                                    'contact_kicker' => 'Contact, small line',
                                    'contact_title' => 'Contact heading',
                                    'contact_hours' => 'Desk hours',
                                    'rooms_page_kicker' => 'Rooms page, small line',
                                    'rooms_page_title' => 'Rooms page heading',
                                    'gallery_kicker' => 'Gallery, small line',
                                    'gallery_title' => 'Gallery heading',
                                    'footer_plan' => 'Footer note',
                                ] as $key => $label)
                                    <div class="row mb-3">
                                        <div class="col-sm-3"><label class="form-label mb-0" for="f-key-3">{{ $label }}</label></div>
                                        <div class="col-sm-9"><input id="f-key-3" type="text" name="{{ $key }}" class="form-control" value="{{ $value($key) }}"></div>
                                    </div>
                                @endforeach
                                <div class="row mb-4">
                                    <div class="col-sm-3"><label class="form-label mb-0" for="f-contact_text">Contact paragraph</label></div>
                                    <div class="col-sm-9"><textarea id="f-contact_text" name="contact_text" rows="2" class="form-control">{{ $value('contact_text') }}</textarea></div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-9">
                                        <button type="submit" class="btn btn-primary px-4">Save homepage</button>
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
