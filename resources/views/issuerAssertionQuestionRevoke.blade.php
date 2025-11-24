@extends('layouts.general')

@section('title', __('issuerAssertionInfopage.aip_page-title'))


@section('content')
    <h2 class="section-title"><span>{{ __('issuerAssertionInfopage.aip_page-title') }}</span></h2>
    <div class="row">
        <div class="col-4">
            <p>
                <!--<strong>{{ __('issuerAssertionInfopage.aip_badge-image') }} </strong><br />-->
                <img src="data:image/png;base64,{{ base64_encode($thisAssertion->badge_image) }}" style="width: 150px;"
                    alt="badge image">
            </p>



        </div>



        <div class="col-8">
            <form method="POST" action="{{ env('APP_URL') }}/finalRevoke"
                enctype="multipart/form-data">
                @csrf
                <h3>{{ __('issuerAssertionInfopage.aip_details') }}</h3>
                <h4>{{ __('issuerAssertionInfopage.aip_badge-name') }}</h4>
                <p>{{ $thisAssertion->badge_name }}</p>
                <h4>{{ __('issuerAssertionInfopage.aip_badge-descrpt') }}</h4>
                <p>{{ $thisAssertion->badge_description }}</p>
                <hr />
                <h3>{{ __('issuerAssertionRevoke.revokeReason_details') }}</h3>

                <textarea style="resize: none;" name="revokedReason" class="form-control" id="revokedReason" rows="5"></textarea>


                <hr />



                {{-- Revoke button --}}
                <div class="bd-callout bd-callout-info">
                    <p>{{ __('assertionBadgeRevoke.final_revoke_info') }}</p>
                </div>

                <button type="submit" class="btn btn-primary" value="Save Changes, Click Here">
                    {{ __('assertionBadgeRevoke.final_revoke_button') }} &nbsp;&nbsp;<i class="bi-save"></i>
                </button>
              
            </form>
        </div>

    </div>

    <h2 class="section-title"><span>
            {{-- <p class="text-center"><a href="./../../earnerMain" class="btn btn-action">Back to Earner main page</a></p> --}}


            <p class="text-center"><a href="{{ env('APP_URL') }}/backtocompany"
                    class="btn btn-action">{{ __('issuerAssertionInfopage.aip_back') }}</a>



        </span></h2>
@endsection
