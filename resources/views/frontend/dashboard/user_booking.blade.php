@extends('frontend.main_master')
@section('title', 'My Bookings')
@section('main')

@include('frontend.body.page_head', ['title' => 'My bookings', 'class' => 'hms-page-head--booking'])

<div class="pt-100 pb-70">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                @include('frontend.dashboard.user_menu')
            </div>
            <div class="col-lg-9">
                <div class="hms-card">
                    <h3>All bookings</h3>
                    @include('frontend.dashboard._booking_table', ['bookings' => $allData])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
