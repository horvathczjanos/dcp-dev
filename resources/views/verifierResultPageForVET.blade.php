@extends('layouts.general')

@section('title', __('verifierSearchForVET.resultpage_title'))


@section('content')

    <h2 class="section-title"><span>{{ __('verifierSearchForVET.resultpage_title') }} </span></h2>

    @foreach ($foundEarnerBadges as $key => $topic)
        <h3>{{ $key }}</h3>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Badge</th>
                    <th scope="col">Short Info</th>
                    <th scope="col">...</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($topic as $earnerbadge)
                    <tr>
                        <td scope="col">{{ $loop->index + 1 }}</td>
                        <td scope="col"><a
                                href="{{ env('APP_URL') }}/earnerbadges/{{ $earnerbadge['earnerbadgeID'] }}/badge"
                                target="_blank" rel="noopener noreferrer"><img
                                    src="{{ env('APP_URL') }}/earnerbadges/{{ $earnerbadge['earnerbadgeID'] }}/badge"
                                    width="100px" alt="badge image"></a></td>
                        <td style="width:350px">
                            <strong>{{ App\Models\User::where('id', '=', $earnerbadge['earnerID'])->first()->name }}</strong><br />{{ $earnerbadge->badge_name }}<br>{{ $earnerbadge['description'] }}<br>{{ $earnerbadge['created_at'] }}
                        </td>
                        <td> <a class="btn btn-primary" href="earnerbadges/{{ $earnerbadge['earnerbadgeID'] }}/info"
                                role="button" target="_blank">Info</a>

                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach


    <h2 class="section-title"><span>
            <p class="text-center"><a href="{{ env('APP_URL') }}/verifierMain"
                    class="btn btn-action">{{ __('general.back-to-verifier') }}</a></p>
        </span></h2>


@endsection
