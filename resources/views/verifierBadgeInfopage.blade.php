@extends('layouts.general')

@section('title', __('infoVerifierBadgePage.info_verifierbadge_title'))


@section('content')
    <h2 class="section-title"><span>{{ __('infoVerifierBadgePage.info_earnerbadge_title') }}</span></h2>

    <div class="row">
        <div class="col-4">
            <p>
                <!--<strong>{{ __('infoVerifierBadgePage.info_earnerbadge_image') }}</strong><br />-->
                <a href="{{ $earnerBadgeURL }}"><img src="data:image/png;base64,{{ base64_encode($image) }}"
                        style="width: 150px;" alt="badge image"></a>
            </p>

            @if (!session()->get('loginPage') && auth()->check())
                <p><a href="{{ $earnerBadgeURL }}" download="MyBadge.png"
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
                        aria-describedby="basic-addon1" rows="1">&lt;a target=&quot;_blank&quot; href=&quot;{{ $earnerBadgeEmbed }}&quot;&gt;&lt;img src=&quot;{{ $earnerBadgeURL }}&quot; width=&quot;100&quot; /&gt;&lt;/a&gt;</textarea>
                </div>

                </p>
            @endif

            @if ($earnerBadgeEvidenceURL)
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
                    {{ $lastcheckingtime }}
                @else
                    NONE
                @endif
                </strong>
            </p>


            

            {{-- @if ($thisBadge->is_dcp_badge)
                <hr />
            @endif --}}
                
        </div>

        <div class="col-8">
            <h3>{{ __('infoEarnerBadgePage.info_earnerbadge_rcpt') }}</h3>
            {{-- --    <h4>{{ __('infoEarnerBadgePage.info_earnerbadge_rcpt_username') }}</h4>
            <p>{{ $printEarnerBadge['user']['name'] }}</p>
            <h4>{{ __('infoEarnerBadgePage.info_earnerbadge_rcpt_email') }}</h4>
                <p><i>{{ $printEarnerBadge['user']['email'] }}</i></p>
            <hr /> -- --}}
            <p><strong>{{ __('infoEarnerBadgePage.info_earnerbadge_rcpt_type') }}</strong> {{ $recipientType }}<br />
                <strong>{{ __('infoEarnerBadgePage.info_earnerbadge_rcpt_id') }}</strong> {{ $recipientIdentity }}<br />

                @if ($recipientValid)
                    <span
                        style="color:green"><strong>{{ __('infoEarnerBadgePage.info_earnerbadge_rcpt_valid') }}</strong></span>
                @else
                    <span
                        style="color:red"><strong>{{ __('infoEarnerBadgePage.info_earnerbadge_rcpt_invalid') }}</strong></span>
                @endif
            </p>
            <hr />

            <h3>{{ __('infoEarnerBadgePage.info_earnerbadge_details') }}</h3>
            <h4>{{ __('infoEarnerBadgePage.info_earnerbadge_name') }}</h4>
            <p>{{ $printEarnerBadge['badgeClass']['name'] }}</p>
            <h4>{{ __('infoEarnerBadgePage.info_earnerbadge_descrpt') }}</h4>
            <p>{{ $printEarnerBadge['badgeClass']['description'] }}</p>
            @if ($thisBadge->is_dcp_badge)
                <h4>{{ __('infoEarnerBadgePage.info_earnerbadge_criteria') }}</h4>

                @php
                    $thisbadgeclassID = $thisBadge->get_dcp_badgeclass_id;
                    //ddd(App\Models\Badgeclass::where('badgeclassID', '=', $thisbadgeclassID)->value('criteria'));
                @endphp
                <p>{{ App\Models\Badgeclass::where('badgeclassID', '=', $thisbadgeclassID)->value('criteria') }}</p>
            @endif


            <h4>{{ __('infoEarnerBadgePage.info_earnerbadge_date_issue') }}</h4>
            <p>{{ $issuedOn }}</p>
            <hr />
            <h4>{{ __('infoEarnerBadgePage.info_earnerbadge_issuer_name') }}</h4>
            <p>{{ $printEarnerBadge['issuer']['name'] }} <br /> {{ $printEarnerBadge['issuer']['email'] }}</p>
            <h4>{{ __('infoEarnerBadgePage.info_earnerbadge_issuer_url') }}</h4>
            <p><a href="{{ $printEarnerBadge['issuer']['url'] }}" target="_blank">
                    @if ($printEarnerBadge['issuer']['image'])
                        <img src="{{ $printEarnerBadge['issuer']['image'] }}" style="width: 150px;" alt="issuer's image">
                    @else
                        {{ $printEarnerBadge['issuer']['url'] }}
                    @endif
                </a>

            </p>

            <?php
            if (array_key_exists('tags', $printEarnerBadge['badgeClass'])) {
                $badgeclasstags = $printEarnerBadge['badgeClass']['tags'];
            } else {
                $badgeclasstags = false;
            }
            
            ?>

            @if ($badgeclasstags)
                <h4>{{ __('infoEarnerBadgePage.info_earnerbadge_tags') }}</h4>
                <p>
                    @foreach ($badgeclasstags as $tag)
                        <span class="badge rounded-pill bg-warning">{{ $tag }}</span>
                    @endforeach
                </p>
            @endif


            @if ($thisBadge->is_dcp_badge)
                <hr />
                <div>
                    <?php
                    $assertionType = json_decode($thisBadge->get_assertion_type, true);
                    
                    ?>
                    @if ($assertionType)
                        @foreach ($assertionType['form'] as $item)
                            <div class="row typefields" id="{{ $item['id'] }}"
                                @if ($item['selected'] == 'false') style="display:none" @endif>
                                <h3>{{ $item['name'] }}</h3>
                                <i>{{ $item['description'] }}</i>
                                <ul>
                                    @foreach ($item['fields'] as $field)
                                        <li><strong>{{ $field['name'] }}</strong><br />{{ $field['description'] }}<br />
                                            @if ($field['type'] == 'text')
                                                <textarea style="resize: none;" name="{{ $field['id'] }}" class="form-control" id="{{ $field['id'] }}"
                                                    rows="1" disabled>{{ $field['content'] }}</textarea>
                                            @elseif($field['type'] == 'url')
                                                <a href="{{ $field['content'] }}">{{ $field['content'] }}</a>
                                            @elseif($field['type'] == 'email')
                                                <a href="mailto:{{ $field['content'] }}">{{ $field['content'] }}</a>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>

                            </div>
                            <br />
                        @endforeach
                    @endif
                </div>
            @endif

            {{-- -- DCP specific data  -- --}}
            @if (!session()->get('loginPage') && auth()->check())
                <h4>{{ __('infoEarnerBadgePage.info_earnerbadge_note') }}</h4>
                <p> {{ $memo }}</p>

                {{-- -- explode php function returns an array with one element of empty string if the explorable string is empty  -- --}}
                {{-- @if ($earnerBadgeTags[0] != null) --}}

                @if (!empty($earnerBadgeTags))
                    <h4>{{ __('infoEarnerBadgePage.info_earnerbadge_tags') }}</h4>
                    <p>
                        @foreach ($earnerBadgeTags as $tag)
                            <span class="badge rounded-pill bg-primary">{{ $tag }}</span>
                        @endforeach
                    </p>
                @endif
            @endif

        </div>
 
    </div>


    {{-- <p>{{ url()->previous() }}</p> --}}


    <h2 class="section-title"><span>
            {{-- <p class="text-center"><a href="./../../earnerMain" class="btn btn-action">{{ __('general.back-to-earner')}}</a></p> --}}

            @if (!session()->get('loginPage') && auth()->check())

                @if (str_contains(url()->previous(), 'earnerbadges'))
                    <p class="text-center"><a href=" {{ url()->previous() }}"
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


    <script>
        function copyTxt(param) {
            var text = document.getElementById(param).select();
            document.execCommand('copy');

        }
    </script>
@endsection
