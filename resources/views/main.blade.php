@extends('layouts.general')

@section('title', __('hello.title'))


@section('content')



    <p class="text-center fs-1"> <strong>DIScoPLAYER (DcP) </strong></p>


    <div class="container">
        <div class="alert alert-success mt-5" role="alert">
            <p class="text-center"><b>{{ __('hello.welcome') }}</b></p>
        </div>
    </div>

    <form method="GET" action="settings">
        <p class="text-center "> <input type="submit" class="btn btn-primary " value="{{ __('hello.settings_title') }}" />
        </p>

    </form>

@endsection
