@extends('layouts.general')

@section('title', __('issuerAssertionInfopage.aip_page-title'))


@section('content')
    <h2 class="section-title"><span>{{ __('issuerAssertionInfopage.aip_page-title') }}</span></h2>
    <div class="row">
        <div class="col-4">
            <p>
                <!--<strong>{{ __('issuerAssertionInfopage.aip_badge-image') }} </strong><br />-->
                <img src="data:image/png;base64,{{ base64_encode($badgeImage) }}" style="width: 150px;" alt="badge image">
            </p>

            <p><a href=" {{ env('APP_URL') }}/assertions/{{ $assertionID }} " target="_blank"
                    class="btn btn-action">{{ __('issuerAssertionInfopage.aip_badge_btn-assrtn') }}</a></p>
            <p><a href=" {{ env('APP_URL') }}/badgeclasses/{{ $badgeclassID }} " target="_blank"
                    class="btn btn-action">{{ __('issuerAssertionInfopage.aip_badge_btn-class') }}</a></p>
            <p><a href=" {{ env('APP_URL') }}/criteria/{{ $badgeclassID }} " target="_blank"
                    class="btn btn-action">{{ __('issuerAssertionInfopage.aip_badge_btn-criteria') }}</a></p>

            @if ($thisAssertion->revoked == 'on')
                <p>Revoked</p>
                <p>Revoked at: {{ $thisAssertion->revokedAt }}</p>
                <p>Reason of revocation: {{ $thisAssertion->revokedReason }}</p>
            @else
                <p>Not revoked</p>
                <p><a href=" {{ env('APP_URL') }}/assertions/{{ $assertionID }}/revoke "
                        class="btn btn-primary">{{ __('issuerAssertionInfopage.aip_badge_btn-revoke') }}</a></p>
            @endif

            @if ($thisAssertion->is_badgeclass_in_metabadgerule)
                <p>This is a metabadge assertion!</p>
                @if ($metabadgeRule = $thisAssertion->get_metabadgerule)
                    @php
                        $metabadgeRule = json_decode($metabadgeRule, true);

                        echo '<div><strong>Name:</strong> ' . $metabadgeRule['name'] . '</div>';
                        echo '<div><strong>Rule type:</strong> ' .
                            App\Http\Controllers\ManageMetabadgeRuleController::changeMetabadgeRuleText(
                                $metabadgeRule['rule']
                            )
                            .
                            '</div>';

                        $countBasebadges = $metabadgeRule['badgeMustHaveRule']['badgeCount'];
                        $baseBadges = $metabadgeRule['badgeMustHaveRule']['subjectBadge'];

                        foreach ($countBasebadges as $key => $item) {
                            echo '<div><strong>Base badgeclass:</strong> ' .
                                '(' .
                                $item .
                                ') ' .
                                '<a href="/issuerbadges/' .
                                $baseBadges[$key] .
                                '/info"
                                                                                target="_blank" rel="noopener noreferrer"><img
                                                                                src="' .
                                env('APP_URL') .
                                '/badgeclasses/' .
                                $baseBadges[$key] .
                                '/image"
                                                                                width="25px" alt="badge image"></a>' .
                                ' </div>';
                        }

                        echo '<hr />';
                        echo '<div><strong>You earned these badges to get this metabadge assertion:</strong></div>';

                        $assertionType = json_decode($thisAssertion->assertiontype, true);
                        $baseAssertions = json_decode($assertionType['form'][0]['fields'][19]['content'], true);

                        foreach ($baseAssertions as $key => $badgeclass) {
                            echo '<div>';
                            foreach ($badgeclass as $key2 => $assertion) {
                                echo '<div><strong>Base badgeclass:</strong> ' .
                                    '<a href="/assertions/' .
                                    $assertion .
                                    '/info"
                                                                                target="_blank" rel="noopener noreferrer"><img
                                     src="' .
                                    env('APP_URL') .
                                    '/assertions/' .
                                    $assertion .
                                    '/badge"
                                     width="25px" alt="assertion image"></a>' .   ' </div>';
                            }
                            echo '</div>';
                        }

                    @endphp
                @endif
            @endif

        </div>
        <div class="col-8">

            <h3>{{ __('issuerAssertionInfopage.aip_details') }}</h3>
            <h4>{{ __('issuerAssertionInfopage.aip_badge-name') }}</h4>
            <p>{{ $badgeName }}</p>
            <h4>{{ __('issuerAssertionInfopage.aip_badge-descrpt') }}</h4>
            <p>{{ $badgeDescription }}</p>
            <h4>{{ __('issuerAssertionInfopage.aip_badge-crit') }}</h4>
            <p>{{ $thisBadgeClass->criteria }}</p>
            <hr />

            <h3>{{ __('issuerAssertionInfopage.aip_rcpt') }}</h3>
            <h4>{{ __('issuerAssertionInfopage.aip_rcpt-type') }}</h4>
            <p>{{ $recipientType }} </p>
            <h4>{{ __('issuerAssertionInfopage.aip_rcpt-id') }}</h4>
            <p>{{ $awardedTo }}</p>
            <h4>{{ __('issuerAssertionInfopage.aip_rcpt-date') }}</h4>
            <p>{{ $issuedOn }}</p>

            @if ($thisAssertion->neverexpires == 'off')
                    <h4>{{ __('issuerAssertionInfopage.aip_exp-date') }}</h4>

                    <?php

                $nowTime = time();

                $assertionExpiresTime = strtotime($thisAssertion->expiresAt);

                //ddd($assertionExpiresTime-$nowTime);

                                                    ?>

                    <p @if ($assertionExpiresTime - $nowTime) style="color:red;font-weight:bold" @endif>
                        {{ $thisAssertion->expiresAt }}
                    </p>
            @else
                <h4>{{ __('issuerAssertionInfopage.aip_exp-date') }}</h4>
                <p>Never expires</p>
            @endif


            <hr />

            {{-- Régi Stackmember megoldástól elbúcsúzunk :-)

            <h3>{{ __('issuerAssertionInfopage.aip_stack') }}</h3>
            @if (count($inStack))
            <p>{{ __('issuerAssertionInfopage.aip_stack-yes') }}:<br />
                {{ App\Models\Stackmember::where('stackmemberID', '=', $inStack[0]['stackmemberID'])->first()->name }}<br />
                <i>{{ App\Models\Stackmember::where('stackmemberID', '=',
                    $inStack[0]['stackmemberID'])->first()->description }}</i>
                @else
            <p>{{ __('issuerAssertionInfopage.aip_stack-no') }}</p>
            @endif

            <hr />
            --}}

            <?php
                $assertionType = json_decode($thisAssertion->assertiontype, true);
                $badgetypeVersion = $assertionType['header']['version'];
            ?>

            <!-- A ZD miatt új kiegészítő űrlap készült, 1.2 változatszámmal 20240423 -->


            <p>@php echo ($badgetypeVersion); @endphp</p>



            @if ($badgetypeVersion == '1.2')

                <input class="badgetypeversion" name="badgetypeversion" hidden value="{{ $badgetypeVersion }}">
                <p><button type="button" class="hubutton btn btn-primary" value="Hu">Magyar</button>
                    <button type="button" class="enbutton btn btn-primary" value="En">English</button>
                </p>




                @foreach ($assertionType['form'] as $key1 => $item)
                    <div class="row typefields" id="{{ $item['id'] }}">


                        @php

                            $disabled = '';
                            if ($item['id'] == 'GCB_01000') {
                                $disabled = ' disabled';
                            }

                            echo ' <h3>' .
                                App\Http\Controllers\ManageIssuerController::getContentWithLangs($item['name']) .
                                '</h3>';

                            echo '<i>' .
                                App\Http\Controllers\ManageIssuerController::getContentWithLangs($item['description']) .
                                '</i>';
                        @endphp

                        @foreach ($item['fields'] as $key2 => $field)
                            <p>
                                @php

                                    echo '<strong>' .
                                        App\Http\Controllers\ManageIssuerController::getContentWithLangs(
                                            $field['name'],
                                        ) .
                                        '</strong>';
                                    echo '(<i>' .
                                        App\Http\Controllers\ManageIssuerController::getContentWithLangs(
                                            $field['type'],
                                        ) .
                                        '</i>)';
                                    echo '<br />' .
                                        App\Http\Controllers\ManageIssuerController::getContentWithLangs(
                                            $field['description'],
                                        ) .
                                        '<br />';

                                    switch ($field['type']) {
                                        case 'text':
                                            echo App\Http\Controllers\ManageIssuerController::getContentWithLangsTextarea(
                                                $field['load'],
                                                $field['id'],
                                                $disabled,
                                            );
                                            break;
                                        case 'url':
                                            echo App\Http\Controllers\ManageIssuerController::getContentWithLangsTextarea(
                                                $field['load'],
                                                $field['id'],
                                                $disabled,
                                            );
                                            break;
                                        case 'table':
                                            echo App\Http\Controllers\ManageIssuerController::getContentWithLangsTable(
                                                $field['load'],
                                                $field['id'],
                                                $disabled,
                                            );
                                            break;
                                        default:
                                            # code...
                                            break;
                                    }


                                @endphp

                            </p>
                        @endforeach

                    </div>
                @endforeach
            @else
                @foreach ($assertionType['form'] as $item)
                    <div class="row typefields" id="{{ $item['id'] }}" @if ($item['selected'] == 'false') style="display:none" @endif>
                        <h3>{{ __('issuerAssertionInfopage.aip_badge-type') }}</h3>
                        <h4>{{ $item['name'] }}</h4>
                        <p><i>{{ $item['description'] }}</i></p>
                        <!--<ul>-->
                        @foreach ($item['fields'] as $field)
                            <!--<li>-->
                            <p><strong>{{ $field['name'] }}</strong>
                                (<i>{{ $field['type'] }}</i>)
                                <br />{{ $field['description'] }}<br />
                                <?php            $tmp = substr($field['id'], 15, 2); ?>
                                <textarea style="resize: none;" name="{{ $field['id'] }}" class="form-control" id="{{ $field['id'] }}"
                                    rows="1" disabled>{{ $field['content'] }}</textarea>
                                <!--</li>-->
                            </p>
                        @endforeach
                        <!--</ul>-->
                    </div>
                @endforeach
            @endif




        </div>
    </div>

    <h2 class="section-title"><span>
            {{-- <p class="text-center"><a href="./../../earnerMain" class="btn btn-action">Back to Earner main page</a></p>
            --}}


            <p class="text-center"><a href=" {{ url()->previous() }}"
                    class="btn btn-action">{{ __('issuerAssertionInfopage.aip_back') }}</a>



        </span></h2>


    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
        crossorigin="anonymous"></script>


    <script type="application/javascript">
        $(function () {

            $('.langcontent[lang="en"]').prop("hidden", false);

            $('.hubutton').click(function () {

                $('.langcontent[lang="en"]').prop("hidden", true);
                $('.langcontent[lang="hu"]').prop("hidden", false);
            });

            $('.enbutton').click(function () {

                $('.langcontent[lang="hu"]').prop("hidden", true);
                $('.langcontent[lang="en"]').prop("hidden", false);
            });

        });
    </script>
@endsection