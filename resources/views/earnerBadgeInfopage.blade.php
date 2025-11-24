@extends('layouts.general')

@section('title', __('infoEarnerBadgePage.info_earnerbadge_title'))


@section('content')
    <h2 class="section-title"><span>{{ __('infoEarnerBadgePage.info_earnerbadge_title') }}</span></h2>

    <div class="row">
        <div class="col-4">
            <div class="bg-image position-relative mb-3 dcp-badge-image-div">
                <!--<strong>{{ __('infoEarnerBadgePage.info_earnerbadge_image') }}</strong><br />-->
                <a href="{{ $earnerBadgeURL }}"><img
                        src="data:image/{{ $filetypeOfBadge }};base64,{{ base64_encode($image) }}" class="dcp-badge-image"
                        alt="badge image"></a>

                @if ($thisBadge->is_dcp_badge_revoked)
                    <div class="position-absolute" style="rotate: 45deg; top: 50px; left: 10px;">
                        <p
                            class="fs-3 px-2 py-1 mb-0 fw-semibold text-light bg-danger bg-opacity-80 border-start border-2 border-danger">
                            {{ __('infoEarnerBadgePage.info_earnerbadge_flag-revoked') }}
                        </p>
                    </div>
                @endif

                @if (!empty($thisBadge->badge_expires_at))

                            <?php
                    $nowTime = time();
                    $earnerbadgeExpiresTime = strtotime($thisBadge->badge_expires_at);
                                    ?>
                            @if (($earnerbadgeExpiresTime - $nowTime) < 0)
                                <div class="position-absolute" style="rotate: -45deg; bottom: 45px; left: 15px;">
                                    <p
                                        class="fs-3 px-2 py-1 mb-0 fw-semibold text-dark bg-warning bg-opacity-80 border-start border-2 border-warning">
                                        {{ __('infoEarnerBadgePage.info_earnerbadge_flag-expired') }}
                                    </p>
                                </div>
                            @endif
                @endif


            </div>

            @if (!session()->get('loginPage') && auth()->check())
                <p><a href="{{ $earnerBadgeURL2 }}" download="MyBadge.png"
                        class="btn btn-primary">{{ __('infoEarnerBadgePage.info_earnerbadge_btn_dwnload') }}&nbsp;&nbsp;<i
                            class="bi-download"></i></a></p>
                <p>

                    {{-- -- Copy URL of the badge itself -- --}}
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <button class="btn btn-primary" type="button" id="copylinkButton"
                            onclick="copyTxt('badge-url')">{{ __('infoEarnerBadgePage.info_earnerbadge_btn_copyurl') }}&nbsp;&nbsp;<i
                                class="bi-files"></i></button>
                    </div>
                    <input type="text" id="badge-url" class="form-control" placeholder="" aria-label=""
                        aria-describedby="basic-addon1" value="{{ $earnerBadgeURL }}">
                </div>

                {{-- -- Copy Embed code -- --}}
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <button class="btn btn-primary" type="button" id="embedButton"
                            onclick="copyTxt('embed-code')">{{ __('infoEarnerBadgePage.info_earnerbadge_btn_embed') }}&nbsp;&nbsp;<i
                                class="bi-code-slash"></i></button>
                    </div>
                    <textarea style="resize: none;" id="embed-code" class="form-control" placeholder="" aria-label=""
                        aria-describedby="basic-addon1"
                        rows="1">&lt;a target=&quot;_blank&quot; href=&quot;{{ $earnerBadgeURL }}&quot;&gt;&lt;img src=&quot;{{ $earnerBadgeEmbed}}&quot; width=&quot;100&quot; /&gt;&lt;/a&gt;</textarea>
                </div>

                </p>
            @endif

            {{-- @if ($earnerBadgeEvidenceURL)
            <p><a href=" {{ $earnerBadgeEvidenceURL }} " target="_blank" class="btn btn-primary">{{
                    __('infoEarnerBadgePage.info_earnerbadge_btn_evidence') }}&nbsp;&nbsp;<i
                        class="bi-box-arrow-up-right"></i></a></p>
            @endif --}}

            @if (!is_null($thisBadge->dcp_badge_evidence_url))
                <p><a href=" {{ $thisBadge->dcp_badge_evidence_url }} " target="_blank"
                        class="btn btn-primary">{{ __('infoEarnerBadgePage.info_earnerbadge_btn_evidence') }}&nbsp;&nbsp;<i
                            class="bi-box-arrow-up-right"></i></a></p>
            @elseif ($earnerBadgeEvidenceURL)
                <p><a href=" {{ $earnerBadgeEvidenceURL }} " target="_blank"
                        class="btn btn-primary">{{ __('infoEarnerBadgePage.info_earnerbadge_btn_evidence') }}&nbsp;&nbsp;<i
                            class="bi-box-arrow-up-right"></i></a></p>
            @endif


            <p><a href=" {{ $assertionURL }} " target="_blank"
                    class="btn btn-action">{{ __('infoEarnerBadgePage.info_earnerbadge_btn_assrtn') }}</a></p>

            @if (!session()->get('loginPage') && auth()->check())
                <p><a href=" {{ $badgeclassURL }} " target="_blank"
                        class="btn btn-action">{{ __('infoEarnerBadgePage.info_earnerbadge_btn_class') }}</a></p>
            @endif

            @if ($earnerBadgeCriteriaURL)
                <p><a href=" {{ $earnerBadgeCriteriaURL }} " target="_blank"
                        class="btn btn-action">{{ __('infoEarnerBadgePage.info_earnerbadge_btn_criteria') }}</a></p>
            @endif

            <p>{{ __('infoEarnerBadgePage.info_earnerbadge_last_chktime') }}
                @if ($checkedOB)
                    <strong style="color:green;">
                @else
                        <strong style="color:red;">
                    @endif
                        @if ($lastcheckingtime)
                            {{ date('Y.m.d H:i:s', strtotime($lastcheckingtime)) }}
                        @else
                            NONE
                        @endif
                    </strong>
            </p>

            <!-- Itt volt a revoked -->

            <!-- Metabadge section -->

            @if ($thisBadge->is_dcp_metabadge)
                <p>This is a metabadge assertion!</p>
                @if ($metabadgeRule = $thisBadge->get_metabadgerule)
                    @php
                        $metabadgeRule = json_decode($metabadgeRule, true);

                        echo '<div><strong>Name:</strong> ' . $metabadgeRule['name'] . '</div>';
                        echo '<div><strong>Rule type:</strong> ' . $metabadgeRule['rule'] . '</div>';

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




                        $assertionType = json_decode($thisBadge->get_assertion_type, true);
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
                                     width="25px" alt="assertion image"></a>' .
                                    ' </div>';
                            }
                            echo '</div>';
                        } 

                     @endphp
                @endif
            @endif



            @if ($thisBadge->is_stackmember)
                <hr />

                {{-- ddd($thisBadge->get_dcp_badge_stackreport) --}}

                <strong>Reporting stacking info</strong><br />
                <strong>{{ $thisBadge->get_dcp_badge_stackreport['name'] }}</strong>
                (<i>{{ $thisBadge->badge_name }}</i>),
                <i>{{ $thisBadge->get_dcp_badge_stackreport['description'] }}</i>. <br /><br />

                @foreach ($thisBadge->get_dcp_badge_stackreport['stackroles'] as $stackrole)
                    @if ($stackrole[0]['relationship'] == 'partOf')
                        This badge is part of <strong>{{ $stackrole[0]['includePartName'] }}</strong>.<br /><br />
                        All parts of <strong>{{ $stackrole[0]['includePartName'] }}</strong> are:<br />
                        <ul>
                            @foreach ($stackrole[0]['parts'] as $part)
                                <br />
                                <li>
                                    <strong>{{ $part[1] }}</strong> (<i>{{ $part[4] }}</i>),
                                    @if ($part[2])
                                        <br />
                                        <img src="{{ $part[5] }}" style="width: 50px;"><br />

                                        <strong>You got it</strong>,
                                        @if ($part[3])
                                            recipient is <strong>VALID</strong>
                                        @else
                                            recipient is <strong>NOT VALID</strong>
                                        @endif
                                    @else
                                        <br />
                                        <img src="{{ $part[5] }}" style="width: 50px;"><br />

                                        <i>You have not got it</i>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                        @if ($stackrole[0]['metaBadge'][0])
                            @if ($stackrole[0]['metaBadge'][1])
                                You got metabadge
                                <strong>{{ $stackrole[0]['includePartName'] }}</strong>
                                (<i>{{ $stackrole[0]['metaBadge'][3] }}</i>),
                                <br />
                                <img src="{{ $stackrole[0]['metaBadge'][4] }}" style="width: 50px;"><br />
                                @if ($stackrole[0]['metaBadge'][2])
                                    recipient is <strong>VALID</strong>
                                @else
                                    recipient is <strong>NOT VALID</strong>
                                @endif
                            @else
                                You have got all part badges for metabadge
                                <strong>{{ $stackrole[0]['includePartName'] }}</strong>
                                (<i>{{ $stackrole[0]['metaBadge'][3] }}</i>),
                                <br />
                                <img src="{{ $stackrole[0]['metaBadge'][4] }}" style="width: 50px;"><br />, claim it from
                                your ISSUER!
                            @endif
                        @else
                            You have not got all part badges for metabadge
                            <strong>{{ $stackrole[0]['includePartName'] }}</strong>
                            (<i>{{ $stackrole[0]['metaBadge'][3] }}</i>),
                            <br />
                            <img src="{{ $stackrole[0]['metaBadge'][4] }}" style="width: 50px;"><br />,
                            collect them!
                        @endif
                    @endif
                    @if ($stackrole[0]['relationship'] == 'includes')
                        This metabadge <strong>{{ $thisBadge->get_dcp_badge_stackreport['name'] }}</strong> contains this
                        badge:<br />
                        <img src="{{ $stackrole[4] }}" style="width: 50px;"><br />
                        <p><strong>{{ $stackrole[0]['includedPartName'] }}</strong> (<i>{{ $stackrole[3] }}</i>),
                            @if ($stackrole[1])
                                badge is <strong>PRESENT</strong> and
                                @if ($stackrole[2])
                                    recipient is <strong>VALID</strong>.
                                @else
                                    recipient is <strong>NOT VALID</strong>.
                                @endif
                            @else
                                badge is <strong>NOT PRESENT</strong>.
                            @endif
                        </p>
                    @endif
                @endforeach
                {{-- $thisBadge->get_dcp_badge_stackreport['stackmessage'] --}}
                {{-- ddd($thisBadge->get_dcp_badge_stackreport) --}}

                <hr />
            @endif

            {{-- @if ($thisBadge->is_dcp_badge)
            <hr />
            @endif --}}

            @if (str_contains(url()->previous(), 'earnerMain'))
                @php

                    $earnerbadgeID = $thisBadge->earnerbadgeID;

                    $isEndorsementFromVerifier = (bool) ($numberOfEndorsements = App\Models\VerifierEndorsement::where(
                        'status',
                        '=',
                        '1',
                    )
                        ->where('earnerbadgeID', '=', $earnerbadgeID)
                        ->count());

                    if ($isEndorsementFromVerifier) {
                        $thisEndorsements = App\Models\VerifierEndorsement::where('status', '=', '1')
                            ->where('earnerbadgeID', '=', $earnerbadgeID)
                            ->orderBy('created_at')
                            ->get();
                    }

                    //ddd($isEndorsementFromVerifier);

                @endphp

                @if ($isEndorsementFromVerifier)
                    <p>Number of your endorsemenets: {{ $numberOfEndorsements }}</p>
                    @foreach ($thisEndorsements as $item)
                        <strong>{{ App\Models\User::where('id', '=', $item->verifierID)->first()->name }}</strong>
                        <p>Rating: {{ $item->rating }}</p>
                        <div class="mb-3">
                            <label for="verifier_endorsement_message" class="form-label">Text of endorsement</label>
                            <textarea name="verifier_endorsement_message" class="form-control" id="verifier_endorsement" rows="3"
                                disabled>{{ $item->verifierMessage }}</textarea>
                        </div>
                        <hr />
                    @endforeach
                @endif

            @endif

            @if (str_contains(url()->previous(), 'lookupEarnerBadges'))
                @php

                    $verifierID = session()->get('userID');
                    $earnerbadgeID = $thisBadge->earnerbadgeID;

                    $isEndorsementFromVerifier = (bool) App\Models\VerifierEndorsement::where('status', '=', '1')
                        ->where('verifierID', '=', $verifierID)
                        ->where('earnerbadgeID', '=', $earnerbadgeID)
                        ->count();

                    if ($isEndorsementFromVerifier) {
                        $thisEndorsement = App\Models\VerifierEndorsement::where('status', '=', '1')
                            ->where('verifierID', '=', $verifierID)
                            ->where('earnerbadgeID', '=', $earnerbadgeID)
                            ->first();
                    }

                    $numberOfEndorsements = App\Models\VerifierEndorsement::where('status', '=', '1')
                        ->where('earnerbadgeID', '=', $earnerbadgeID)
                        ->count();

                    if ((bool) $numberOfEndorsements) {
                        $allEndorsements = App\Models\VerifierEndorsement::where('status', '=', '1')

                            ->where('earnerbadgeID', '=', $earnerbadgeID)
                            ->orderBy('created_at')
                            ->get();

                        $summOfRatings = 0;
                        foreach ($allEndorsements as $key => $endorsement) {
                            $summOfRatings += $endorsement->rating;
                        }

                        $averageOfRatings = round($summOfRatings / $numberOfEndorsements, 2);
                    }

                @endphp

                <hr />
                <h3>Verifier endorsement</h3>

                @if ((bool) $numberOfEndorsements)
                    <p>Number of endorsements: {{ $numberOfEndorsements }}</p>
                    <p>Average of ratings: {{ $averageOfRatings }}</p>
                    <hr />


                    @foreach ($allEndorsements as $endorsement)
                        @if ($endorsement->verifierID != $verifierID)
                            <div class="input-group mb-3">
                                <p>The rating is: {{ $endorsement->rating }} </p>
                            </div>
                            <div class="mb-3">
                                <textarea name="verifier_endorsement_message" class="form-control" id="verifier_endorsement" rows="3"
                                    disabled>{{ $endorsement->verifierMessage }}</textarea>
                            </div>
                        @endif
                    @endforeach

                    <hr />
                @endif


                @if ($isEndorsementFromVerifier)
                    <form method="POST" action="{{ env('APP_URL') }}/deleteVerifierEndorsement">
                        @csrf


                        <div class="input-group mb-3">
                            <p>Your rating is: {{ $thisEndorsement->rating }} </p>
                            <p><i>(0 = no rating, 1 = worst ... 5 = best)</i></p>
                        </div>

                        <div class="mb-3">
                            <label for="verifier_endorsement_message" class="form-label">Text of endorsement</label>
                            <textarea name="verifier_endorsement_message" class="form-control" id="verifier_endorsement" rows="3"
                                disabled>{{ $thisEndorsement->verifierMessage }}</textarea>
                        </div>

                        @php
                            session()->put('actEarnerbadgeID', $thisBadge->earnerbadgeID);
                        @endphp

                        <p class="text-center">
                            <button type="submit" class="btn btn-primary" value="Delete"> Delete </button>
                        </p>
                    </form>
                @else
                    <form method="POST" action="{{ env('APP_URL') }}/recordVerifierEndorsement">
                        @csrf
                        <hr />
                        <h3>Verifier endorsement</h3>
                        <div class="input-group mb-3">
                            <label for="verifierRating" class="form-label" id="verifierRatingLabel">Rating is not set:
                                0</label>
                            <input type="range" class="form-range" min="0" max="5" val="0" id="verifierRating" name="verifierRating"
                                list="values">
                            <p><i>(0 = no rating, 1 = worst ... 5 = best)</i></p>
                        </div>

                        <div class="mb-3">
                            <label for="verifier_endorsement_message" class="form-label">Text of endorsement</label>
                            <textarea name="verifier_endorsement_message" class="form-control" id="verifier_endorsement"
                                rows="3"></textarea>
                        </div>

                        @php
                            session()->put('actEarnerbadgeID', $thisBadge->earnerbadgeID);
                        @endphp

                        <p class="text-center">
                            <button type="submit" class="btn btn-primary" value="Send"> Send </button>
                        </p>
                    </form>
                @endif
            @endif


        </div>

        {{--*******************
        Badge info begin
        *******************--}}

        <div class="col-8">

            <div class="dcp-info-header">
                <h2 role="heading" class="dcp-info-heading-1">{{ $printEarnerBadge['badgeClass']['name'] }}</h2>

                <!--<h3>{{-- __('infoEarnerBadgePage.info_earnerbadge_rcpt') --}}</h3>-->

                {{-- -- <h4>{{ __('infoEarnerBadgePage.info_earnerbadge_rcpt_username') }}</h4>
                <p>{{ $printEarnerBadge['user']['name'] }}</p>
                <h4>{{ __('infoEarnerBadgePage.info_earnerbadge_rcpt_email') }}</h4>
                <p><i>{{ $printEarnerBadge['user']['email'] }}</i></p>
                <hr /> -- --}}

                <p class="dcp-info-text">{{ $printEarnerBadge['badgeClass']['description'] }}</p>
                <p class="dcp-info-text">
                    <!--{{ __('infoEarnerBadgePage.info_earnerbadge_rcpt_type') }} <strong>{{ $recipientType }}</strong><br />-->
                    {{ __('infoEarnerBadgePage.info_earnerbadge_rcpt_id') }} <strong>{{ $recipientIdentity }}</strong>
                </p>
                <p class="dcp-info-text">
                    @if ($recipientValid)
                        <span
                            style="color:green"><strong>{{ __('infoEarnerBadgePage.info_earnerbadge_rcpt_valid') }}</strong></span>
                    @else
                        <span
                            style="color:red"><strong>{{ __('infoEarnerBadgePage.info_earnerbadge_rcpt_invalid') }}</strong></span>
                    @endif
                </p>

                {{--*******
                Datumok eleje
                *******--}}


                <p class="dcp-info-text-small"><strong>{{ __('infoEarnerBadgePage.info_earnerbadge_date_issue') }}:</strong>
                    {{-- $issuedOn --}}{{ date('Y.m.d H:i', strtotime($issuedOn)) }}</p>
                <p class="dcp-info-text-small">
                    <strong>{{ __('infoEarnerBadgePage.info_earnerbadge_date_expire') }}:</strong>
                    @if (!empty($thisBadge->badge_expires_at))
                        <span @if ($earnerbadgeExpiresTime - $nowTime) class="text-warning fw-semibold" @endif>
                            {{ date('Y.m.d H:i', strtotime($thisBadge->badge_expires_at)) }}</span>
                    @else
                        Never expires
                    @endif

                    @if ($thisBadge->is_dcp_badge_revoked)
                        <h2 class="dcp-info-block-heading-2"><i
                                class="text-danger bi bi-exclamation-circle-fill"></i>&nbsp;{{ __('infoEarnerBadgePage.info_earnerbadge_h-revoked') }}
                        </h2>
                        <p class="dcp-info-text-small"><strong>{{ __('infoEarnerBadgePage.info_earnerbadge_revoked-at') }}</strong>
                            <span
                                class="text-danger fw-semibold">{{ date('Y.m.d H:i', strtotime($thisBadge->dcp_badge_revokedat)) }}</span>
                            @if ($thisBadge->dcp_badge_revoked_reason)
                                <br />
                                {{ __('infoEarnerBadgePage.info_earnerbadge_revoked-reason') }}{{ $thisBadge->dcp_badge_revoked_reason }}
                            @endif
                        </p>
                    @endif
                </strong></p>
            </div>
            {{--
            Datumok vege
            --}}

            <div class="dcp-info-section">

                <h3 class="dcp-info-heading-2">{{ __('infoEarnerBadgePage.info_earnerbadge_details') }}</h3>

                @php
                    // Is there any OB Tags?
                    if (array_key_exists('tags', $printEarnerBadge['badgeClass'])) {
                        $badgeclasstags = $printEarnerBadge['badgeClass']['tags'];
                    } else {
                        $badgeclasstags = false;
                    }

                @endphp

                {{-- ******************************************************
                Bootstrap TAB for multilanguage capable badge
                ******************************************************* --}}

                @php

                    if ($thisBadge->is_dcp_badge) {
                        // the badge is issued by DCP ? begin

                        $guiLang = session()->get('locale');

                        $languages = json_decode(env('LANGUAGES'), true);

                        // Is there multilanguage ? begin

                        if (!is_null($multilangPack = json_decode($thisBadge->get_multilangpack, true))) {
                            $originalLang = $thisBadge->get_originallang;
                            $multiLangTXT = '';
                            $multiLangTAB = '';
                            $thisbadgeclassID = $thisBadge->get_dcp_badgeclass_id;

                            //ddd($multilangPack);

                            $multiHeader = [];

                            $additionalLang = '';

                            foreach ($multilangPack['form'] as $key => $item) {
                                if ($item['selected'] == 'true' && $item['available'] == 'true') {
                                    $multiHeader[] = $item;
                                    $additionalLang .= $item['name'];
                                }
                            }

                            $multiLangTAB =
                                '<ul class="nav nav-tabs" id="MLTab" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="' .
                                $originalLang .
                                '-tab" data-bs-toggle="tab" data-bs-target="#' .
                                $originalLang .
                                '-tab-pane" type="button" role="tab" aria-controls="' .
                                $originalLang .
                                '-tab-pane" aria-selected="true">Original language: ' .
                                $languages[$originalLang] .
                                '</button>
                                                </li>
                                                    ';


                            //$multiLangTAB .= '<p>' . 'Original language: ' . $languages[$originalLang] . '</p>';

                            //ddd($multiHeader);

                            $multiLangTXT .=
                                '<div class="tab-content" id="myTabContent">
                                            <div class="tab-pane fade show active" id="' .
                                $originalLang .
                                '-tab-pane" role="tabpanel" aria-labelledby="' .
                                $originalLang .
                                '-tab" tabindex="0">';
                            if ($guiLang != $originalLang) {
                                // If the locale not the original language of the badge, we write the lables in original language too in square brackets
                                $multiLangTXT .=
                                    '<div class="dcp-info-block"><div class="dcp-info-block-header"><h2 class="dcp-info-block-heading-2">' .
                                    __('infoEarnerBadgePage.info_earnerbadge_name') .
                                    '</h2><p class="dcp-info-block-desc">' .
                                    __('infoEarnerBadgePage.info_earnerbadge_name', [], $originalLang) .
                                    '</p></div>
                                                <div class="dcp-info-block-body"><p class="dcp-info-text">' .
                                    $printEarnerBadge['badgeClass']['name'] .
                                    '</p></div></div>
                                                <div class="dcp-info-block"><div class="dcp-info-block-header"><h2 class="dcp-info-block-heading-2">' .
                                    __('infoEarnerBadgePage.info_earnerbadge_descrpt') .
                                    '</h2><p class="dcp-info-block-desc">' .
                                    __('infoEarnerBadgePage.info_earnerbadge_descrpt', [], $originalLang) .
                                    '</p></div>
                                        <div class="dcp-info-block-body"><p class="dcp-info-text">' .
                                    $printEarnerBadge['badgeClass']['description'] .
                                    '</p></div></div>
                                                <div class="dcp-info-block"><div class="dcp-info-block-header"><h2 class="dcp-info-block-heading-2">' .
                                    __('infoEarnerBadgePage.info_earnerbadge_criteria') .
                                    '</h2><p class="dcp-info-block-desc">' .
                                    __('infoEarnerBadgePage.info_earnerbadge_criteria', [], $originalLang) .
                                    '</p></div>
                                    <div class="dcp-info-block-body"><p class="dcp-info-text">' .
                                    App\Models\Badgeclass::where('badgeclassID', '=', $thisbadgeclassID)->value(
                                        'criteria',
                                    ) .
                                    '</p></div></div>';

                                if ($badgeclasstags) {
                                    $multiLangTXT .=
                                        '<div class="dcp-info-block"><div class="dcp-info-block-header"><h2 class="dcp-info-block-heading-2">' .
                                        __('infoEarnerBadgePage.info_earnerbadge_tags') .
                                        '</h2><p class="dcp-info-block-desc">' .
                                        __('infoEarnerBadgePage.info_earnerbadge_tags', [], $originalLang) .
                                        '</p></div>
                                        <div class="dcp-info-block-body"><p class="dcp-info-text">';
                                }
                            } else {
                                $multiLangTXT .=
                                    '<div class="dcp-info-block"><div class="dcp-info-block-header"><h2 class="dcp-info-block-heading-2">' .
                                    __('infoEarnerBadgePage.info_earnerbadge_name') .
                                    '</h2></div>
                                    <div class="dcp-info-block-body"><p class="dcp-info-text">' .
                                    $printEarnerBadge['badgeClass']['name'] .
                                    '</p></div></div>
                                                <div class="dcp-info-block"><div class="dcp-info-block-header"><h2 class="dcp-info-block-heading-2">' .
                                    __('infoEarnerBadgePage.info_earnerbadge_descrpt') .
                                    '</h2></div>
                                    <div class="dcp-info-block-body"><p class="dcp-info-text">' .
                                    $printEarnerBadge['badgeClass']['description'] .
                                    '</p></div></div>
                                                <div class="dcp-info-block"><div class="dcp-info-block-header"><h2 class="dcp-info-block-heading-2">' .
                                    __('infoEarnerBadgePage.info_earnerbadge_criteria') .
                                    '</h2></div>
                                    <div class="dcp-info-block-body"><p class="dcp-info-text">' .
                                    App\Models\Badgeclass::where('badgeclassID', '=', $thisbadgeclassID)->value(
                                        'criteria',
                                    ) .
                                    '</p></div></div>';
                                if ($badgeclasstags) {
                                    $multiLangTXT .=
                                        '<div class="dcp-info-block"><div class="dcp-info-block-header"><h2 class="dcp-info-block-heading-2">' .
                                        __('infoEarnerBadgePage.info_earnerbadge_tags') .
                                        '</h2></div>
                                        <div class="dcp-info-block-body"><p class="dcp-info-text">';
                                }
                            }
                            foreach ($badgeclasstags as $tag) {
                                $multiLangTXT .= '<span class="badge rounded-pill bg-warning">' . $tag . '</span>&nbsp;';
                            }
                            $multiLangTXT .= '</p></div></div></div>';

                            if (!empty($multiHeader)) {
                                //ddd($multiHeader);
                                foreach ($languages as $key => $lang) {
                                    $multiLangTXT .=
                                        '<div class="tab-pane fade" id="' .
                                        $lang .
                                        '-tab-pane" role="tabpanel" aria-labelledby="' .
                                        $lang .
                                        '-tab" tabindex="0">';
                                    foreach ($multiHeader as $pack) {
                                        if ($pack['id'] == $key) {
                                            $multiLangTAB .=
                                                '<li class="nav-item" role="presentation">  <button class="nav-link" id="' .
                                                $lang .
                                                '-tab" data-bs-toggle="tab" data-bs-target="#' .
                                                $lang .
                                                '-tab-pane" type="button" role="tab" aria-controls="' .
                                                $lang .
                                                '-tab-pane" aria-selected="false">Additional language: ' .
                                                $lang .
                                                '</button>  </li>';
                                            foreach ($pack['fields'] as $field) {
                                                if (strpos($field['id'], '_name') !== false) {
                                                    $multiLangTXT .=
                                                        '<div class="dcp-info-block"><div class="dcp-info-block-header"><h2 class="dcp-info-block-heading-2">' .
                                                        __('infoEarnerBadgePage.info_earnerbadge_name', [], $key) .
                                                        '</h2></div><div class="dcp-info-block-body"><p class="dcp-info-text">' .
                                                        $field['content'] .
                                                        '</p></div></div>';
                                                } elseif (strpos($field['id'], '_description') !== false) {
                                                    $multiLangTXT .=
                                                        '<div class="dcp-info-block"><div class="dcp-info-block-header"><h2 class="dcp-info-block-heading-2">' .
                                                        __('infoEarnerBadgePage.info_earnerbadge_descrpt', [], $key) .
                                                        '</h2></div><div class="dcp-info-block-body"><p class="dcp-info-text">' .
                                                        $field['content'] .
                                                        '</p></div></div>';
                                                } elseif (strpos($field['id'], '_criteria') !== false) {
                                                    $multiLangTXT .=
                                                        '<div class="dcp-info-block"><div class="dcp-info-block-header"><h2 class="dcp-info-block-heading-2">' .
                                                        __('infoEarnerBadgePage.info_earnerbadge_criteria', [], $key) .
                                                        '</h2></div><div class="dcp-info-block-body"><p class="dcp-info-text">' .
                                                        $field['content'] .
                                                        '</p></div></div>';
                                                } elseif (strpos($field['id'], '_tags') !== false) {
                                                    $langTags = explode(';', $field['content']);
                                                    if (!empty($langTags)) {
                                                        $multiLangTXT .=
                                                            '<div class="dcp-info-block"><div class="dcp-info-block-header"><h2 class="dcp-info-block-heading-2">' .
                                                            __('infoEarnerBadgePage.info_earnerbadge_tags', [], $key) .
                                                            '</h2></div><div class="dcp-info-block-body"><p class="dcp-info-text">';
                                                        foreach ($langTags as $key => $ltag) {
                                                            $multiLangTXT .=
                                                                '<span class="badge rounded-pill bg-warning">' .
                                                                $ltag .
                                                                '</span> ';
                                                        }
                                                        $multiLangTXT .= '</p></div></div>';
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    $multiLangTXT .= "</div>\r\n";
                                }
                                $multiLangTAB .= "</ul>\r\n";
                            }
                            $multiLangTXT .= "</div>\r\n";
                            echo $multiLangTAB;
                            echo $multiLangTXT;
                        }
                        // Multilinguual DCP badge? end
                        else {
                            $multiLangTXT = '';
                            $multiLangTXT .= '<div class="dcp-info-block"><div class="dcp-info-block-header"><h2 class="dcp-info-block-heading-2">' . __('infoEarnerBadgePage.info_earnerbadge_name') . '</h2></div>';
                            $multiLangTXT .= '<div class="dcp-info-block-body"><p class="dcp-info-text">' . $printEarnerBadge['badgeClass']['name'] . '</p></div></div>';
                            $multiLangTXT .= '<div class="dcp-info-block"><div class="dcp-info-block-header"><h2 class="dcp-info-block-heading-2">' . __('infoEarnerBadgePage.info_earnerbadge_descrpt') . '</h2></div>';
                            $multiLangTXT .= '<div class="dcp-info-block-body"><p class="dcp-info-text">' . $printEarnerBadge['badgeClass']['description'] . '</p></div></div>';
                            $multiLangTXT .= '<div class="dcp-info-block"><div class="dcp-info-block-header"><h2 class="dcp-info-block-heading-2">' . __('infoEarnerBadgePage.info_earnerbadge_criteria') . '</h2></div>';
                            $multiLangTXT .= '<div class="dcp-info-block-body"><p class="dcp-info-text">' . $thisBadge->get_badgeclass_criteria . '</p></div></div>';

                            if ($badgeclasstags) {
                                $multiLangTXT .= '<div class="dcp-info-block"><div class="dcp-info-block-header"><h2 class="dcp-info-block-heading-2">' . __('infoEarnerBadgePage.info_earnerbadge_tags') . '</h2></div>
                    <div class="dcp-info-block-body"><p class="dcp-info-text">';
                                foreach ($badgeclasstags as $bctag) {
                                    $multiLangTXT .= '<span class="badge rounded-pill bg-warning">' . $bctag . '</span> ';
                                }
                                $multiLangTXT .= '</p></div></div>';
                            }
                            echo $multiLangTXT;
                        }
                    }
                    // End of multilanguage badge display
                    else {
                        // Badge from outside
                        $multiLangTXT = '';
                        $multiLangTXT .= '<div class="dcp-info-block"><div class="dcp-info-block-header"><h2 class="dcp-info-block-heading-2">' . __('infoEarnerBadgePage.info_earnerbadge_name') . '</h2></div>';
                        $multiLangTXT .= '<div class="dcp-info-block-body"><p class="dcp-info-text">' . $printEarnerBadge['badgeClass']['name'] . '</p></div></div>';
                        $multiLangTXT .= '<div class="dcp-info-block"><div class="dcp-info-block-header"><h2 class="dcp-info-block-heading-2">' . __('infoEarnerBadgePage.info_earnerbadge_descrpt') . '</h2></div>';
                        $multiLangTXT .= '<div class="dcp-info-block-body"><p class="dcp-info-text">' . $printEarnerBadge['badgeClass']['description'] . '</p></div></div>';

                        if ($badgeclasstags) {
                            $multiLangTXT .= '<div class="dcp-info-block"><div class="dcp-info-block-header"><h2 class="dcp-info-block-heading-2">' . __('infoEarnerBadgePage.info_earnerbadge_tags') . '</h2></div><div class="dcp-info-block-body"><p class="dcp-info-text">';
                            foreach ($badgeclasstags as $bctag) {
                                $multiLangTXT .= '<span class="badge rounded-pill bg-warning">' . $bctag . '</span> ';
                            }
                            $multiLangTXT .= '</p></div></div>';
                        }
                        echo $multiLangTXT;
                    } // there is NO criteria field
                @endphp

            </div>

            {{-- ISSUER --}}

            <div class="dcp-info-section">

                <div class="dcp-info-block">
                    <div class="dcp-info-block-header">
                        <h2 class="dcp-info-block-heading-2">{{ __('infoEarnerBadgePage.info_earnerbadge_issuer_name') }}
                        </h2>
                    </div>
                    <div class="dcp-info-block-body">
                        <p class="dcp-info-text">{{ $printEarnerBadge['issuer']['name'] }} <br /> <a
                                href="mailto:{{ $printEarnerBadge['issuer']['email'] }}">{{ $printEarnerBadge['issuer']['email'] }}</a>
                        </p>
                    </div>
                </div>
                <div class="dcp-info-block">
                    <div class="dcp-info-block-header">
                        <h2 class="dcp-info-block-heading-2">{{ __('infoEarnerBadgePage.info_earnerbadge_issuer_url') }}
                        </h2>
                    </div>
                    <div class="dcp-info-block-body">
                        <p class="dcp-info-text"><a href="{{ $printEarnerBadge['issuer']['url'] }}" target="_blank">
                                @if ($printEarnerBadge['issuer']['image'])
                                    <img src="{{ $printEarnerBadge['issuer']['image'] }}" style="width: 150px;"
                                        alt="issuer's image">
                                @else
                                    {{ $printEarnerBadge['issuer']['url'] }}
                                @endif
                            </a>
                        </p>
                    </div>
                </div>
            </div>

            @if ($thisBadge->is_dcp_badge)
                    <?php
                $assertionType = json_decode($thisBadge->get_assertion_type, true);
                $thisbadgeclassID = $thisBadge->get_dcp_badgeclass_id;
                                ?>
                    @if ($assertionType)

                        @php
                            // ddd($thisbadgeclassID);
                        @endphp
                        @if ($thisbadgeclassID == env('GREENCERT_ID'))
                            @foreach ($assertionType['form'] as $item)
                                <div class="row typefields" id="{{ $item['id'] }}">
                                    <div class="dcp-info-section">
                                        <h3 class="dcp-info-heading-2">
                                            @php echo App\Http\Controllers\ManageIssuerController::getContentWithLangs($item['name']); @endphp
                                        </h3>
                                        <p class="dcp-ino-text-small">
                                            @php echo App\Http\Controllers\ManageIssuerController::getContentWithLangs($item['description']); @endphp
                                        </p>
                                        @foreach ($item['fields'] as $field)
                                            <div class="dcp-info-block">
                                                <div class="dcp-info-block-header">
                                                    <h2 class="dcp-info-block-heading-2">
                                                        @php echo App\Http\Controllers\ManageIssuerController::getContentWithLangs($field['name']); @endphp
                                                    </h2>
                                                    <p class="dcp-info-block-desc">
                                                        @php echo App\Http\Controllers\ManageIssuerController::getContentWithLangs($field['description']); @endphp
                                                        (<i>@php echo App\Http\Controllers\ManageIssuerController::getContentWithLangs($field['type']); @endphp</i>)
                                                    </p>
                                                </div>
                                                <div class="dcp-info-block-body">
                                                    <p class="dcp-info-text">
                                                        @php

                                                            switch ($field['type']) {
                                                                case 'text':
                                                                    echo App\Http\Controllers\ManageIssuerController::getContentWithLangsTextarea(
                                                                        $field['load'],
                                                                        $field['id'],
                                                                        ' disabled',
                                                                    );
                                                                    break;
                                                                case 'url':
                                                                    echo App\Http\Controllers\ManageIssuerController::getContentWithLangsTextarea(
                                                                        $field['load'],
                                                                        $field['id'],
                                                                        ' disabled',
                                                                    );
                                                                    break;
                                                                case 'table':
                                                                    echo App\Http\Controllers\ManageIssuerController::getContentWithLangsTable(
                                                                        $field['load'],
                                                                        $field['id'],
                                                                        ' disabled',
                                                                    );
                                                                    break;
                                                                default:
                                                                    # code...
                                                                    break;
                                                            }


                                                            /*    echo App\Http\Controllers\ManageIssuerController::getContentWithLangsTextarea(
                                                                    $field['load'],
                                                                    $field['id'],
                                                                    ' disabled',
                                                                );*/
                                                        @endphp
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="dcp-info-section">
                                @foreach ($assertionType['form'] as $item)
                                    <div class="row typefields" id="{{ $item['id'] }}" @if ($item['selected'] == 'false') style="display:none"
                                    @endif>
                                        <h3 class="dcp-info-heading-2">{{ $item['name'] }}</h3>
                                        <p class="dcp-info-text-small">{{ $item['description'] }}</p>
                                        <!--<ul>-->
                                        @foreach ($item['fields'] as $field)
                                            <!--<li @if (is_null($field['content']) || strlen($field['content']) == 0) hidden @endif>-->
                                            <div class="dcp-info-block" @if (is_null($field['content']) || strlen($field['content']) == 0)
                                            style="display:none" @endif>
                                                <div class="dcp-info-block-header">
                                                    <h2 class="dcp-info-block-heading-2">{{ $field['name'] }}</h2>
                                                    <p class="dcp-info-block-desc">{{ $field['description'] }}</p>
                                                </div>
                                                <div class="dcp-info-block-body">
                                                    @if ($field['type'] == 'text')
                                                        <!--<textarea style="resize: none;" name="{{ $field['id'] }}" class="form-control" id="{{ $field['id'] }}"
                                                                                        rows="1" disabled>{{ $field['content'] }}</textarea>-->
                                                        <p class="dcp-info-text" name="{{ $field['id'] }}" id="{{ $field['id'] }}">
                                                            {{ $field['content'] }}</p>
                                                    @elseif($field['type'] == 'url')
                                                        <p class="dcp-info-text"><a href="{{ $field['content'] }}">{{ $field['content'] }}</a></p>
                                                    @elseif($field['type'] == 'email')
                                                        <p class="dcp-info-text"><a href="mailto:{{ $field['content'] }}">{{ $field['content'] }}</a>
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                            <!--</li>-->
                                        @endforeach
                                        <!--</ul>-->
                                    </div>
                                    <!--<br />-->
                                @endforeach
                            </div>
                        @endif

                    @endif
            @endif

            {{-- -- DCP specific data -- --}}
            @if (!session()->get('loginPage') && auth()->check())
                <div class="dcp-info-section">
                    <div class="dcp-info-block">
                        <div class="dcp-info-block-header">
                            <h2 class="dcp-info-block-heading-2">{{ __('infoEarnerBadgePage.info_earnerbadge_note') }}</h2>
                        </div>
                        <div class="dcp-info-block-body">
                            <p class="dcp-info-text">{{ $memo }}</p>
                        </div>
                    </div>

                    {{-- -- explode php function returns an array with one element of empty string if the explorable string is
                    empty -- --}}
                    {{-- @if ($earnerBadgeTags[0] != null) --}}

                    @if (!empty($earnerBadgeTags))
                        <div class="dcp-info-block">
                            <div class="dcp-info-block-header">
                                <h2 class="dcp-info-block-heading-2">{{ __('infoEarnerBadgePage.info_earnerbadge_tags') }}</h2>
                            </div>
                            <div class="dcp-info-block-body">
                                <p class="dcp-info-text">
                                    @foreach ($earnerBadgeTags as $tag)
                                        <span class="badge rounded-pill bg-primary">{{ $tag }}</span>&nbsp;
                                    @endforeach
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

        </div>

    </div>


    {{-- <p>{{ url()->previous() }}</p> --}}


    <h2 class="section-title"><span>
            {{-- <p class="text-center"><a href="./../../earnerMain" class="btn btn-action">{{
                    __('general.back-to-earner')}}</a></p> --}}



            @if (!session()->get('loginPage') && auth()->check())

                @if (str_contains(url()->previous(), 'earnerbadges'))
                    <p class="text-center"><a href=" {{ url()->previous() }}" class="btn btn-action">{{ __('general.back') }}</a>
                @endif

                    @if (str_contains(url()->previous(), 'lookupEarnerBadges'))
                        <p class="text-center"><a href="{{ env('APP_URL') }}/verifierMain"
                                class="btn btn-action">{{ __('general.back') }}</a>
                    @endif

                    @if (url()->previous() === env('APP_URL'))
                        *
                    @endif

                    @if (str_contains(url()->previous(), 'earnerMain'))
                        <p class="text-center"><a href=" {{ url()->previous() }}"
                                class="btn btn-action">{{ __('general.back-to-earner') }}</a>
                    @elseif (str_contains(url()->previous(), 'earnerSearch'))
                        <p class="text-center"><a href="javascript:history.back();"
                                class="btn btn-action">{{ __('general.back-to-searchresults') }}</a>
                    @elseif (str_contains(url()->previous(), 'earnerStatistics'))
                        <p class="text-center"><a href="javascript:history.back();"
                                class="btn btn-action">{{ __('general.back-to-statistics') }}</a>
                    @endif

            @endif

        </span></h2>


    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
        crossorigin="anonymous"></script>

    <script type="application/javascript">
        function copyTxt(param) {
            var text = document.getElementById(param).select();
            document.execCommand('copy');
        }
        $(function () {

            $('.form-range#verifierRating').val(0);

            $('.form-range#verifierRating').change(function () {

                $('label#verifierRatingLabel').html("Actual rating is: " + $(this).val());

            });

            originalLang = $('.nav-item, .active').children('button').attr('id').slice(0, 2).toLowerCase();

            if (originalLang == 'hu') {

                $('.langcontent[lang="hu"]').prop("hidden", false);
            } else {

                $('.langcontent[lang="en"]').prop("hidden", false);
            }


            $('.nav-item').on('click', function () {
                id = $(this).children('button').attr('id');
                lang = id.slice(0, 2).toLowerCase();
                if (lang == 'hu') {
                    $('.langcontent[lang="en"]').prop("hidden", true);
                    $('.langcontent[lang="hu"]').prop("hidden", false);
                } else {
                    $('.langcontent[lang="hu"]').prop("hidden", true);
                    $('.langcontent[lang="en"]').prop("hidden", false);
                }




            });

        });

    </script>

    <script></script>
@endsection