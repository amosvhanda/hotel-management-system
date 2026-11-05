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
                                    <div class="col-sm-3"><h6 class="mb-0">Small line</h6></div>
                                    <div class="col-sm-9">
                                        <input type="text" name="hero_kicker" class="form-control" value="{{ $value('hero_kicker') }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">Headline</h6></div>
                                    <div class="col-sm-9">
                                        <input type="text" name="hero_title" class="form-control" value="{{ $value('hero_title') }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">Supporting line</h6></div>
                                    <div class="col-sm-9">
                                        <textarea name="hero_text" rows="3" class="form-control">{{ $value('hero_text') }}</textarea>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">Photograph</h6></div>
                                    <div class="col-sm-9">
                                        <input type="file" name="hero_image" class="form-control" accept="image/*">
                                        <img src="{{ $site->heroImageUrl() }}" alt="" class="mt-2" style="width:220px;height:120px;object-fit:cover;border-radius:8px">
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-sm-3"><h6 class="mb-0">Booking note</h6></div>
                                    <div class="col-sm-9">
                                        <input type="text" name="booking_note" class="form-control" value="{{ $value('booking_note') }}">
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
                                        <div class="col-sm-3"><h6 class="mb-0">{{ $label }}</h6></div>
                                        <div class="col-sm-9">
                                            <input type="text" name="{{ $key }}_kicker" class="form-control mb-2" value="{{ $value($key . '_kicker') }}" placeholder="Small line">
                                            <input type="text" name="{{ $key }}_title" class="form-control" value="{{ $value($key . '_title') }}" placeholder="Heading">
                                        </div>
                                    </div>
                                @endforeach

                                <h5 class="mb-3">Questions</h5>
                                <p class="text-muted">The questions themselves are edited under FAQ. These lines sit above them.</p>
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">Small line</h6></div>
                                    <div class="col-sm-9">
                                        <input type="text" name="faq_kicker" class="form-control" value="{{ $value('faq_kicker') }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">Heading</h6></div>
                                    <div class="col-sm-9">
                                        <input type="text" name="faq_title" class="form-control" value="{{ $value('faq_title') }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">Intro</h6></div>
                                    <div class="col-sm-9">
                                        <textarea name="faq_intro" rows="2" class="form-control">{{ $value('faq_intro') }}</textarea>
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-sm-3"><h6 class="mb-0">Side photograph</h6></div>
                                    <div class="col-sm-9">
                                        <input type="file" name="faq_image" class="form-control" accept="image/*">
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
                                        <div class="col-sm-3"><h6 class="mb-0">{{ $label }}</h6></div>
                                        <div class="col-sm-9"><input type="text" name="{{ $key }}" class="form-control" value="{{ $value($key) }}"></div>
                                    </div>
                                @endforeach
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">About paragraph</h6></div>
                                    <div class="col-sm-9">
                                        <textarea name="about_text" rows="3" class="form-control">{{ $value('about_text') }}</textarea>
                                        <small class="text-muted">Leave this as the sample and the About page uses the description from Site Setting instead.</small>
                                    </div>
                                </div>
                                @foreach (['about_point_1' => 'About point 1', 'about_point_2' => 'About point 2', 'about_point_3' => 'About point 3'] as $key => $label)
                                    <div class="row mb-3">
                                        <div class="col-sm-3"><h6 class="mb-0">{{ $label }}</h6></div>
                                        <div class="col-sm-9"><input type="text" name="{{ $key }}" class="form-control" value="{{ $value($key) }}"></div>
                                    </div>
                                @endforeach
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">Why stay</h6></div>
                                    <div class="col-sm-9">
                                        <input type="text" name="about_why_kicker" class="form-control mb-2" value="{{ $value('about_why_kicker') }}" placeholder="Small line">
                                        <input type="text" name="about_why_title" class="form-control" value="{{ $value('about_why_title') }}" placeholder="Heading">
                                    </div>
                                </div>
                                @foreach ([1, 2, 3] as $n)
                                    <div class="row mb-3">
                                        <div class="col-sm-3"><h6 class="mb-0">Reason {{ $n }}</h6></div>
                                        <div class="col-sm-9">
                                            <input type="text" name="about_feature_{{ $n }}_title" class="form-control mb-2" value="{{ $value('about_feature_'.$n.'_title') }}">
                                            <textarea name="about_feature_{{ $n }}_text" rows="2" class="form-control">{{ $value('about_feature_'.$n.'_text') }}</textarea>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="row mb-3">
                                    <div class="col-sm-3"><h6 class="mb-0">About closing line</h6></div>
                                    <div class="col-sm-9">
                                        <input type="text" name="about_cta_title" class="form-control mb-2" value="{{ $value('about_cta_title') }}">
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
                                        <div class="col-sm-3"><h6 class="mb-0">{{ $label }}</h6></div>
                                        <div class="col-sm-9"><input type="text" name="{{ $key }}" class="form-control" value="{{ $value($key) }}"></div>
                                    </div>
                                @endforeach
                                <div class="row mb-4">
                                    <div class="col-sm-3"><h6 class="mb-0">Contact paragraph</h6></div>
                                    <div class="col-sm-9"><textarea name="contact_text" rows="2" class="form-control">{{ $value('contact_text') }}</textarea></div>
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
