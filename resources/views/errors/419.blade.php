@extends('errors.layout')
@section('code', '419')
@section('title', 'Page expired')
@section('message', 'For your security this page timed out. Please go back, refresh the page and try again.')
@section('extra')<a class="btn ghost" href="javascript:history.back()">Go back</a>@endsection
