@extends('layouts.general')

@section('title', __('verifierShowIssuerBadgeclasses.issuer_all_badge_title'))


@section('content')

    <h2 class="section-title"><span>{{ __('verifierShowIssuerBadgeclasses.issuer_all_badge_title') }}</span></h2>

    <h3>{{ __('verifierShowIssuerBadgeclasses.issuer_all_badge_issuer-name') }}{{ $issuer->name }}</h3>
    <p>{{ __('verifierShowIssuerBadgeclasses.issuer_all_badge_issuer-url') }}: <a href="{{ $issuer->url }}"
            target="_blank">{{ $issuer->url }}</a><br />
        {{ __('verifierShowIssuerBadgeclasses.issuer_all_badge_issuer-email') }}: {{ $issuer->email }}<br />
        {{ __('verifierShowIssuerBadgeclasses.issuer_all_badge_issuer-id') }}: <span
            style="font-size: 0.75em;">[{{ $issuer->issuerID }}]</span>
    </p>
    <div class="container">
        <div class="alert alert-success mt-5" role="alert">
            <p class="text-center"><b>{{ __('verifierShowIssuerBadgeclasses.issuer_all_badge_num-o-badges') }}:</b>
                {{ $issuerBadgeclasses->count() }}</p>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">{{ __('verifierShowIssuerBadgeclasses.issuer_all_badge_sum_th_badge') }}</th>
                <th scope="col">{{ __('verifierShowIssuerBadgeclasses.issuer_all_badge_sum_th_short-info') }}</th>
                <th scope="col">{{ __('verifierShowIssuerBadgeclasses.issuer_all_badge_sum_th_functions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($issuerBadgeclasses as $item)
                <tr>
                    <td scope="col">{{ $loop->index + 1 }}</td>


                    <td scope="col">

                        <img src="data:image/png;base64,{{ base64_encode($item->badgeclass_image_data) }}" width="100px"
                            alt="{{ $item->name }}">
                    </td>
                    <td scop="col">
                        <p>{{ $item->name }}</p>
                        <p>{{ __('verifierShowIssuerBadgeclasses.issuer_all_badge_created_at') }}:
                            {{ $item['created_at'] }}<br />
			    {{ __('verifierShowIssuerBadgeclasses.issuer_all_badge_num-o-asserts') }} 
                            @if (isset($countOfAssertions[$item->badgeclassID]))
                                {{ $countOfAssertions[$item->badgeclassID] }}
                            @else
                            0
                            @endif


                        </p>
                    </td>
                    <td>
                        <a class="btn btn-primary" href="/issuerbadges/{{ $item['badgeclassID'] }}/info"
                            role="button">{{ __('verifierShowIssuerBadgeclasses.issuer_all_badge_btn_info') }}&nbsp;&nbsp;<i
                                class="bi-info-circle"></i></a>


                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>

    <h2 class="section-title"><span>
            <p class="text-center"><a href="{{ env('APP_URL') }}/lookupIssuers"
                    class="btn btn-action">{{ __('verifierShowIssuerBadgeclasses.issuer_all_badge_back-2-issuerlookup') }}</a></p>
        </span></h2>

@endsection
