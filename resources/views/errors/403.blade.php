@extends('errors.layout')
@section('code', '403')
@section('title', 'Access denied')
@section('message', 'You don’t have permission to open this page. If you think this is a mistake, please contact the hotel administrator.')
@section('extra')<a class="btn ghost" href="javascript:history.back()">Go back</a>@endsection
