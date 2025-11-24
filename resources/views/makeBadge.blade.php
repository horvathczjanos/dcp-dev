@extends('layouts.general')

@section('title', __('hello.title_make_badge'))

@section('content')



    <p class="text-center fs-1"> <strong>DIScoPLAYER (DcP) </strong></p>


    <div class="container">
        <div class="alert alert-success mt-5" role="alert">
            <p class="text-center"><b>{{ __('hello.title_make_badge') }}</b></p>
        </div>
    </div>

    <form method="POST" action="makeBadge">
        @csrf

        <p class="text-center "> <input type="submit" class="btn btn-primary"
                value="{{ __('hello.make_badge_button') }}" />
        </p>

    </form>

@endsection
