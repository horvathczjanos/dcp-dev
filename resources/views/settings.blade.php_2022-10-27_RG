@extends('layouts.general')

@section('title', __('langSettingsPage.settings_title'))

@section('content')

    <h2 class="section-title"><span>{{ __('langSettingsPage.settings_title') }}</span></h2>

    <div class="container">
        <div class="bd-callout bd-callout-info">
            <p>{{ __('langSettingsPage.settings_choosen') }} <strong> {{ $locale }} </strong></p>
        </div>

    <form method="POST" action="settings">
        @csrf

        <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="language" id="en" value="en" autocomplete="off"
                        {{ $locale == 'en' ? 'checked' : '' }}>
                    <label class="form-check-label" for="language">
                        English
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="language" id="el" value="el" autocomplete="off"
                        {{ $locale == 'el' ? 'checked' : '' }}>
                    <label class="form-check-label" for="language">
                        Greek
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="language" id="hu" value="hu" autocomplete="off"
                        {{ $locale == 'hu' ? 'checked' : '' }}>
                    <label class="form-check-label" for="language">
                        Magyar
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="language" id="lv" value="lv" autocomplete="off"
                        {{ $locale == 'lv' ? 'checked' : '' }}>
                    <label class="form-check-label" for="language">
                        Latvia
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="language" id="lt" value="lt" autocomplete="off"
                        {{ $locale == 'lt' ? 'checked' : '' }}>
                    <label class="form-check-label" for="language">
                        Lietuva
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="language" id="es" value="es" autocomplete="off"
                        {{ $locale == 'es' ? 'checked' : '' }}>
                    <label class="form-check-label" for="language">
                        Español
                    </label>
                </div>
        </div>
	    <p class="text-center">
                <button type="submit" class="btn btn-primary" value="Change">
		    {{ __('langSettingsPage.settings_btn') }}
		</button>
	    </p>

    </form>
    </div>
    <h2 class="section-title"><span>
    <a href="{{ url()->previous() }}" class="btn btn-action">{{ __('general.back')}}</a>
    </span></h2>


@endsection
