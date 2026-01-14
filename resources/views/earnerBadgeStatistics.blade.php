@extends('layouts.general')

@section('title', __('adminStatistics.statistics_title'))


@section('content')
    <h2 class="section-title"><span>{{ __('adminStatistics.statistics_title') }}</span></h2>
    <div class="container">
        <div class="alert alert-success mt-5" role="alert">
            <!--  <p class="text-center"><strong>{{ __('adminStatistics.statistics_num-o-users') }}:
                        </strong>{{ $numberofuploaderearnerusers }}<br />
                        <strong>{{ __('adminStatistics.statistics_num-o-badges') }}: </strong>{{ $numberofbadges }}
                    </p>
                    <hr /> -->

            <strong>All Issuers: </strong> {{ $statistics['allissuerNum'] }}<br />
            <strong>All Badge Classes: </strong> {{ $statistics['allbadgeclassNum'] }}<br />
            <strong>All Earners who upload at least one badge: </strong> {{ $statistics['alluploaderearnerNum'] }}<br />
            <strong>All uploaded earner Badges : </strong> {{ $statistics['alluploadedearnerbadgeNum'] }}<br />
            <strong>All Issued Assertions: </strong> {{ $statistics['allissuedassertionNum'] }}<br />
            <strong>All expiring Assertions: </strong> {{ $statistics['allexpiringassertionNum'] }}<br />
            <strong>All revoked Assertions: </strong> {{ $statistics['allrevokedassertionNum'] }}<br />
            <strong>Number of Earners: </strong> {{ $statistics['earnerNum'] }}<br />
            <strong>Maximum uploaded badge number by earner: </strong>
            {{ $statistics['maxuploadedbyearnerbadgeNum'] }}<br />
            <strong>Number of NOT DCP badges at earner section: </strong> {{ $statistics['allnotDCPbadgeNum'] }}<br />

        </div>
    </div>

    @foreach ($users as $user)
        <div class="row">
            <div class="col-4">
                <p> {{ $user['name'] }} </p>
            </div>
            <div class="col-8">
                <p>
                    <a href=" {{ $user['earnerBadgeInfoURL'] }} "><img src=" {{ $user['earnerBadgeURL'] }} "
                            width="75px" /></a><br />
                    {{ $user['badgename'] }}<br />
                    <strong>{{ __('adminStatistics.statistics_upload-at') }}:</strong> {{ $user['created_at'] }}<br />
                    <strong>{{ __('adminStatistics.statistics_badge-id') }}:</strong> {{ $user['earnerbadgeID'] }}
                </p>
            </div>
        </div>
        <hr />
    @endforeach

    <h2 class="section-title"><span>
            <p class="text-center"><a href="adminMain"
                    class="btn btn-action">{{ __('adminStatistics.statistics_back') }}</a></p>
        </span></h2>

@endsection
