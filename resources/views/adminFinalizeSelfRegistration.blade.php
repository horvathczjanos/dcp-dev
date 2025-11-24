@extends('layouts.general')

@section('title', __('adminFinalizeSelfRegistration.page_title'))


@section('content')

    <h2 class="section-title"><span>{{ __('adminFinalizeSelfRegistration.page_title') }} </span></h2>

    {{-- Self registering data --}}
    <div class="bd-callout bd-callout-info">
        <p>{{ __('adminFinalizeSelfRegistration.page_description') }}</p>
    </div>

    <p>
        {{ __('adminFinalizeSelfRegistration.name_label') }}: <strong>{{ $newUserData['name'] }}</strong><br />
        {{ __('adminFinalizeSelfRegistration.email_label') }}: <strong>{{ $newUserData['email'] }}</strong>
    </p>

    <div class="bd-callout bd-callout-info">
        <p>{{ __('adminFinalizeSelfRegistration.registration_information') }}</p>
    </div>

    <h2 class="section-title"><span>
            <p class="text-center"><a href="{{ env('APP_URL') }}/home" class="btn btn-action">{{ __('adminFinalizeSelfRegistration.registration_btn_back') }}</a></p>
        </span></h2>


@endsection
