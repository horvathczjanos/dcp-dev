@extends('layouts.general')

@section('title', __('issuerBadgeMultilangPrimary.bmlp_page-title'))


@section('content')

    <h2 class="section-title"><span>{{ __('issuerBadgeMultilangPrimary.bmlp_page-title') }}</span></h2>

    <div class="row">
        <div class="col-12 text-center">

            <img src="data:image/png;base64,{{ base64_encode($badgeImage) }}" style="width: 150px;" alt="badge image">
            <p><strong>{{ $thisBadgeClass->name }}</strong><br />

            </p>
        </div>
    </div>

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bd-callout bd-callout-info">
        <p>{{ __('issuerBadgeMultilangPrimary.bmlp_btn-lbl') }}</p>
    </div>

    <form method="POST" action="{{ env('APP_URL') }}/setIssuerBadgeOriginalLang">
        @csrf

        <div class="mb-3">

            @foreach ($languages as $key => $lang)
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="language" id="{{ $key }}}"
                        value="{{ $key }}" autocomplete="off"
                        {{ $thisBadgeClass->originalLang == $key ? 'checked' : '' }}>
                    <label class="form-check-label" for="language">
                        {{ $lang }}
                    </label>
                </div>
            @endforeach
        </div>


        <p class="text-center">
            <button type="submit" class="btn btn-primary" value="Change">
                {{ __('issuerBadgeMultilangPrimary.bmlp_btn') }}
            </button>
        </p>

    </form>

    <h2 class="section-title"><span>
            <p class="text-center"><a href="{{ env('APP_URL') }}/backtocompany" class="btn btn-action">{{ __('issuerBadgeMultilangPrimary.bmlp_btn-back') }}</a></p>

        </span></h2>

@endsection
