@extends('layouts.general')

@section('title', __('issuerBadgeMultilang.bml_page-title'))


@section('content')

    <h2 class="section-title"><span>{{ __('issuerBadgeMultilang.bml_page-title') }} </span></h2>

    <div class="row">
        <div class="col-12 text-center">

            <img src="data:image/png;base64,{{ base64_encode($badgeImage) }}" style="width: 150px;" alt="badge image">
            <p><strong>{{ $thisBadgeClass->name }}</strong><br />

            </p>
        </div>
    </div>

   


    <div class="container">
        <div class="alert alert-success mt-5" role="alert">
            <p class="text-center"><stong>{{ __('issuerBadgeMultilang.bml_chooser') }}</strong></p>
        </div>
    </div>

    <div class="bd-callout bd-callout-info">
        <p>{{ __('issuerBadgeMultilang.bml_btn-orig-lbl') }}</p>
    </div>
    <div class="mb-3">
	<p class="text-center">
        <a class="btn btn-primary" href="/issuerbadges/{{ $thisBadgeClass->badgeclassID }}/setoriginallang"
            role="button">{{ __('issuerBadgeMultilang.bml_btn-orig') }}&nbsp;&nbsp;<i
                class="bi-file-earmark-check-fill"></i></a>
	</p>
    </div>

    @if (!is_null($thisBadgeClass->originalLang))
        <div class="bd-callout bd-callout-info">
            <p>{{ __('issuerBadgeMultilang.bml_btn-lang-lbl') }}</p>
        </div>
        <div class="mb-3">
	    <p class="text-center">
            <a class="btn btn-primary text-center" href="/issuerbadges/{{ $thisBadgeClass->badgeclassID }}/feedmultilangcontents"
                role="button">{{ __('issuerBadgeMultilang.bml_btn-lang') }}&nbsp;&nbsp;<i
                    class="bi-file-earmark-check-fill"></i></a>
	    </p>
        </div>
    @endif

    <h2 class="section-title"><span>
            <p class="text-center"><a href="{{ env('APP_URL') }}/backtocompany" class="btn btn-action">{{ __('issuerBadgeMultilang.bml_btn-back') }}</a></p>

        </span></h2>

@endsection
