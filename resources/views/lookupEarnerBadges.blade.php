@extends('layouts.general')

@section('title', __('lookupEarnerBadges.lookup_EB_title'))

@section('content')
    <h2 class="section-title"><span>{{ __('lookupEarnerBadges.lookup_EB_title') }}</span></h2>

    <div class="container">
        <div class="alert alert-success mt-5" role="alert">
            <p class="text-center"><strong>{{ __('lookupEarnerBadges.lookup_EB_nob') }}: </strong>{{ $numberoflookedupearnerbadges }}</p>

        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">{{ __('lookupEarnerBadges.lookup_EB_sum-th-badge') }}</th>
                <th scope="col">{{ __('lookupEarnerBadges.lookup_EB_sum-th-short-info') }}</th>
                <th scope="col">{{ __('lookupEarnerBadges.lookup_EB_sum-th-functions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($earnerbadges as $item)
                <tr>
                    <td scope="col">{{ $loop->index + 1 }}</td>
                    <td scope="col"><a
                            href="{{ env('APP_URL') }}/earnerbadges/{{ $item['earnerbadgeID'] }}/badge"
                            target="_blank" rel="noopener noreferrer"><img
                                src="{{ env('APP_URL') }}/earnerbadges/{{ $item['earnerbadgeID'] }}/badge"
                                width="100px" alt="badge image"></a></td>
                    <td scop="col" style="width:50%;">
                        <p><strong>{{ App\Models\User::where('id', '=', $item['earnerID'])->first()->name }}</strong><br />
			<!--{{ $item['description'] }}<br /> -->
			{{ $item['badge_name'] }}<br />
			{{ __('lookupEarnerBadges.lookup_EB_uploaded_at') }}: {{ $item['created_at'] }}</p>
                    </td>
                    <td scop="col"> <a class="btn btn-primary" href="earnerbadges/{{ $item['earnerbadgeID'] }}/info" role="button"
                            target="_blank">{{ __('lookupEarnerBadges.lookup_EB_btn-info') }}&nbsp;&nbsp;<i class="bi-info-circle"></i></a>
                        <a class="btn btn-primary" href="earnerbadges/{{ $item['earnerbadgeID'] }}/check"
                            role="button">{{ __('lookupEarnerBadges.lookup_EB_btn-check') }}&nbsp;&nbsp;<i class="bi-check-circle"></i></a>
                      {{--  <a class="btn btn-primary" href="earnerbadges/{{ $item['earnerbadgeID'] }}/endorse"
                            role="button">Endorse</a> --}}
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>

    <h2 class="section-title"><span>
            <p class="text-center"><a href="./../verifierMain" class="btn btn-action">{{ __('lookupEarnerBadges.lookup_EB_back-2-manver') }}</a></p>
        </span></h2>
@endsection
