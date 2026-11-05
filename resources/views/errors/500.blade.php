@extends('errors.layout')
@section('code', '500')
@section('title', 'Something went wrong')
@section('message', 'We hit an unexpected problem on our side. Please try again in a moment. If it keeps happening, contact us and we’ll sort it out.')
@section('extra')<a class="btn ghost" href="javascript:location.reload()">Try again</a>@endsection
