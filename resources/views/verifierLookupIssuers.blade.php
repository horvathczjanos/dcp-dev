@extends('layouts.general')

@section('title', __('verifierLookupIssuers.verifier_lookupissuers_title'))


@section('content')
    <h2 class="section-title"><span>{{ __('verifierLookupIssuers.verifier_lookupissuers_title') }}</span></h2>

    <div class="container">
        <div class="alert alert-success mt-5" role="alert">
            <p class="text-center"><strong>{{ __('verifierLookupIssuers.verifier_lookupissuers_num-o-issuers') }}:</strong>
                {{ App\Models\Issuer::count() }}</p>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">{{ __('verifierLookupIssuers.verifier_lookupissuers_sum-th-name') }}</th>
                <th scope="col">{{ __('verifierLookupIssuers.verifier_lookupissuers_sum-th-email') }}</th>
                <th scope="col">{{ __('verifierLookupIssuers.verifier_lookupissuers_sum-th-logo') }}</th>
                <th scope="col">{{ __('verifierLookupIssuers.verifier_lookupissuers_sum-th-num-o-badges') }}</th>
                <th scope="col">{{ __('verifierLookupIssuers.verifier_lookupissuers_sum-th-functions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach (App\Models\Issuer::get() as $issuer)
                <tr>
                    <td scope="col">{{ $loop->index + 1 }}</td>
                    <td scope="col"><a href="{{ $issuer->url }}" target="_blank">{{ $issuer->name }}</a></td>
                    <td scope="col">{{ $issuer->email }}
                    </td>
                    <td scope="col">
                    <?php $imageData64 = base64_encode(Storage::get('public/images/' . $issuer->logofile));
                    $ext = pathinfo(storage_path() . '/public/images/' . $issuer->logofile, PATHINFO_EXTENSION);
                    
                    ?>
			<img src="data:image/{{ $ext }};base64,{{ $imageData64 }}" style="width: 50px;"></td>
		    <td scope="col">
                        @php
                            
                            if (isset($countOfBadgeclasses[$issuer->issuerID])) {
                                echo $countOfBadgeclasses[$issuer->issuerID];
                            
                                if (isset($countOfAssertions[$issuer->issuerID])) {
                                    echo ' / ' . $countOfAssertions[$issuer->issuerID];
                                } else {
                                    echo ' / 0';
                                }
                            } else {
                                echo '0 / 0';
                            }
                            
                        @endphp
		    </td>
                    <td scope="col">
                        <a class="btn btn-primary" href="issuerBadgeclasses/{{ $issuer['issuerID'] }}"
                            role="button" style="white-space: nowrap;">{{ __('verifierLookupIssuers.verifier_lookupissuers_btn-lookup') }}&nbsp;&nbsp;<i
                                class="bi-award-fill"></i></a>
                    </td>
                </tr>
            @endforeach


        </tbody>
    </table>

    <h2 class="section-title"><span>
            <p class="text-center"><a href="verifierMain"
                    class="btn btn-action">{{ __('verifierLookupIssuers.verifier_lookupissuers_back-2-manver') }}</a></p>
        </span></h2>
@endsection
