@extends('errors.layout')
@section('code', '404')
@section('title', 'Page not found')
@section('message', 'Sorry, we couldn’t find that page. It may have moved, or the link may be wrong.')
@section('extra')<a class="btn ghost" href="{{ url('/rooms') }}">View rooms</a>@endsection
