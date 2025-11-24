@extends('layouts.general')

@section('title', __('earnerBadgeDelete.delete_earnerbadge_title'))


@section('content')
    <h2 class="section-title"><span>{{ __('earnerBadgeDelete.delete_earnerbadge_title') }}</span></h2>

    <div class="row">
        <div class="col-4">
            <p>

                <img src="data:image/png;base64,{{ base64_encode(Storage::get($thisEarnerbadge->earnerbadgepath)) }}"
                    style="width: 150px;" alt="badge image">
            </p>

        </div>
        <div class="col-8">
            <p> {{ $backData['checkOB']['badgeClass']['name'] }} </p>
            <p> {{ $backData['checkOB']['badgeClass']['description'] }} </p>

            {{-- Delete button --}}
            <div class="bd-callout bd-callout-info">
                <p>{{ __('earnerBadgeDelete.delete_earnerbadge_info') }}</p>
            </div>
            <p class="text-center ">
                <a href="{{ env('APP_URL') }}/earnerbadges/{{ $thisEarnerbadge->earnerbadgeID }}/finalDelete"
                    class="btn btn-primary">{{ __('earnerBadgeDelete.delete_earnerbadge_button') }}</a>
            </p>
        </div>

    </div>


    <h2 class="section-title"><span>

            <p class="text-center"><a href="{{ env('APP_URL') }}/earnerMain"
                    class="btn btn-action">{{ __('general.back-to-earner') }}</a></p>
        </span></h2>



@endsection
