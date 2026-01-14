@extends('layouts.general')

@section('title', __('searchResultsEarnerBadgePage.searchresults_earnerbadge_title'))


@section('content')
    <h2 class="section-title"><span>{{ __('searchResultsEarnerBadgePage.searchresults_earnerbadge_title') }}</span></h2>
    <div class="container">
        <p class="alert alert-success mt-5" role="alert"><strong>{{ __('searchResultsEarnerBadgePage.searchresults_earnerbadge_results') }}: </strong> {{ count($resultBadges) }}</p>

    @if (count($resultBadges))

        @foreach ($resultBadges as $badge)
            <div class="row">
                <div class="col-4">
                    <a href="{{ env('APP_URL') }}/earnerbadges/{{ $badge['earnerbadgeID'] }}/info"><img
                            src="{{ env('APP_URL') }}/earnerbadges/{{ $badge['earnerbadgeID'] }}/badge"
                            width="75px" alt="badge image"></a>
                </div>
                <div class="col-8">
                    
                    <p>{{ $badge->badge_class['name'] }}<br />
                        Uploaded at: {{ $badge['created_at'] }}<br />

                        @php
                            $tags = json_decode($badge['earnerBadgeTags']);
                            $OBtags = $badge->badge_tags;
                            
                        @endphp
                        @foreach ($tags as $tag)
                            <span class="badge rounded-pill bg-primary">{{ $tag }}</span>
                        @endforeach

                        <br />

                        @foreach ($OBtags as $OBtag)
                            <span class="badge rounded-pill bg-warning">{{ $OBtag }}</span>
                        @endforeach

                    </p>
                </div>
            </div>
            <hr />
        @endforeach


    @endif

    {{-- @foreach ($users as $user)
        <div class="row">
            <div class="col-4">
                <p> {{ $user['name'] }} </p>
            </div>
            <div class="col-8">
                <p> {{ $user['earnerbadgeID'] }} <br />
                    <strong>Uploaded at:</strong> {{ $user['created_at'] }}
                    <br />

                    <a href=" {{ $user['earnerBadgeInfoURL'] }} "><img src=" {{ $user['earnerBadgeURL'] }} "
                            width="75px" /></a>

                    <br />
                    {{ $user['badgename'] }}.
                </p>
            </div>
        </div>
        <hr />
    @endforeach --}}

    </div>

    <h2 class="section-title"><span>
            {{-- <p class="text-center"><a href="./../../earnerMain" class="btn btn-action">Back to Earner main page</a></p> --}}
            <p class="text-center">

                <a href=" {{ url()->previous() }}" class="btn btn-action">{{ __('general.back-to-earner') }}</a>
            </p>


        </span></h2>

@endsection
