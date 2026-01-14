@extends('layouts.general')

@section('title', __('adminAllBadgeIssuer.issuer_all_badge_title'))


@section('content')

    <h2 class="section-title"><span>{{ __('adminAllBadgeIssuer.issuer_all_badge_title') }}</span></h2>

    <h3>{{__('adminAllBadgeIssuer.issuer_all_badge_issuer-name') }}{{ $issuer->name }}</h3>
        <p>{{ __('adminAllBadgeIssuer.issuer_all_badge_issuer-url') }}: <a href="{{ $issuer->url }}" target="_blank">{{ $issuer->url }}</a><br />
	{{ __('adminAllBadgeIssuer.issuer_all_badge_issuer-email') }}: {{ $issuer->email }}<br />
	{{ __('adminAllBadgeIssuer.issuer_all_badge_issuer-id') }}: <span style="font-size: 0.75em;">[{{ $issuer->issuerID }}]</span>
    </p>
    <div class="container">
        <div class="alert alert-success mt-5" role="alert">
            <p class="text-center"><b>{{ __('adminAllBadgeIssuer.issuer_all_badge_num-o-badges') }}:</b> {{ $numberofassertions }}</p>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
		<th scope="col">#</th>
		<th scope="col">{{ __('adminAllBadgeIssuer.issuer_all_badge_sum_th_badge') }}</th>
		<th scope="col">{{ __('adminAllBadgeIssuer.issuer_all_badge_sum_th_short-info') }}</th>
		<th scope="col">{{ __('adminAllBadgeIssuer.issuer_all_badge_sum_th_functions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($assertionlist as $item)
                <tr>
                    <td scope="col">{{ $loop->index + 1 }}</td>

                    <?php
                    $thisAssertion = App\Models\Assertion::where('assertionID', '=', $item['assertionID'])->first();
                    ?>
                    <td scope="col">
			<a href="{{ env('APP_URL') }}/assertions/{{ $item['assertionID'] }}/badge" target="_blank" rel="noopener noreferrer">
			    <img src="{{ env('APP_URL') }}/assertions/{{ $item['assertionID'] }}/badge" width="100px" alt="badge image" />
			</a>
                    </td>
		    <td scop="col">
			<p>{{ $thisAssertion->badge_name }}</p>
			<p>{{ __('adminAllBadgeIssuer.issuer_all_badge_issued_at') }}: {{ $item['issuedOn'] }}<br />
			{{ __('adminAllBadgeIssuer.issuer_all_badge_awarded-to') }}: 
                        @if ($item['originalIdentity'])
                            {{ $item['originalIdentity'] }}
                        @else
                            NA
                        @endif
			</p>
		    </td>
                    <td>
                        <a class="btn btn-primary" href="/assertions/{{ $item['assertionID'] }}/info" role="button">{{ __('adminAllBadgeIssuer.issuer_all_badge_btn_info') }}&nbsp;&nbsp;<i class="bi-info-circle"></i></a>
		    </td>
                </tr>
            @endforeach

        </tbody>
    </table>

    <h2 class="section-title"><span>
            <p class="text-center"><a href="{{ env('APP_URL') }}/manageIssuers" class="btn btn-action">{{ __('adminAllBadgeIssuer.issuer_all_badge_back-2-manis') }}</a></p>
        </span></h2>

@endsection
