@extends('layouts.general')

@section('title', __('issuerBadgeClassInfo.bci_page-title'))


@section('content')
    <h2 class="section-title"><span>{{ __('issuerBadgeClassInfo.bci_page-title') }}</span></h2>

    <div class="row">
        <div class="col-4">
            <p>
                <!--<strong>{{ __('issuerBadgeClassInfo.bci_badge-image') }}</strong><br />-->
                <img src="data:image/png;base64,{{ base64_encode($badgeImage) }}" style="width: 150px;" alt="badge image">
            </p>
            <p><a href=" {{ env('APP_URL') }}/badgeclasses/{{ $thisBadgeClass->badgeclassID }} " target="_blank"
                    class="btn btn-action">{{ __('issuerBadgeClassInfo.bci_bc-json') }}</a></p>
            </p>

            {{-- -- Copy badgeclassID  -- --}}
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <button class="btn btn-primary" type="button" id="embedButton"
                        onclick="copyTxt('embed-code')">BadgeclassID&nbsp;&nbsp;<i class="bi-code-slash"></i></button>
                </div>
                <textarea style="resize: none;" id="embed-code" class="form-control" placeholder="" aria-label=""
                    aria-describedby="basic-addon1" rows="1">{{ $badgeclassID }}</textarea>
            </div>
        </div>
        <div class="col-8">

            <h3>{{ __('issuerBadgeClassInfo.bci_details') }}</h3>

            {{-- Multilanguage  --}}

            @php

                $guiLang = session()->get('locale');

                $languages = json_decode(env('LANGUAGES'), true);

                if (!is_null($multilangPack = json_decode($thisBadgeClass->get_multilangpack, true))) {
                    $originalLang = $thisBadgeClass->get_originallang;

                    echo '<p>' . 'Original language: ' . $languages[$originalLang] . '</p>';

                    if ($guiLang != $originalLang) {
                        echo '<h4>' .
                            __('infoEarnerBadgePage.info_earnerbadge_name') .
                            ' [' .
                            __('issuerNewBadgeClass.nbc_name', [], $originalLang) .
                            ']</h4>';
                        echo '<p>' . $printEarnerBadge['badgeClass']['name'] . '</p>';
                        echo '<h4>' .
                            __('infoEarnerBadgePage.info_earnerbadge_descrpt') .
                            ' [' .
                            __('issuerNewBadgeClass.nbc_desc', [], $originalLang) .
                            ']</h4>';
                        echo '<p>' . $printEarnerBadge['badgeClass']['description'] . '</p>';
                        echo '<h4>' .
                            __('infoEarnerBadgePage.info_earnerbadge_criteria') .
                            ' [' .
                            __('issuerNewBadgeClass.nbc_criteria', [], $originalLang) .
                            ']</h4>';
                    } else {
                        echo '<h4>' . __('infoEarnerBadgePage.info_earnerbadge_name') . '</h4>';
                        echo '<p>' . $printEarnerBadge['badgeClass']['name'] . '</p>';
                        echo '<h4>' . __('infoEarnerBadgePage.info_earnerbadge_descrpt') . '</h4>';
                        echo '<p>' . $printEarnerBadge['badgeClass']['description'] . '</p>';
                        echo '<h4>' . __('infoEarnerBadgePage.info_earnerbadge_criteria') . '</h4>';
                    }
                }

            @endphp




            <h4>{{ __('issuerBadgeClassInfo.bci_badge-name') }}</h4>
            <p>{{ $badgeName }}</p>
            <h4>{{ __('issuerBadgeClassInfo.bci_badge-descrpt') }}</h4>
            <p>{{ $badgeDescription }}</p>
            <h4>{{ __('issuerBadgeClassInfo.bci_badge-crit') }}</h4>
            <p>{{ $thisBadgeClass->criteria }}</p>
            <hr />

            <h3>{{ __('issuerBadgeClassInfo.bci_stack') }}</h3>
            @if (count($inStack))
                <p>{{ __('issuerBadgeClassInfo.bci_stack-yes') }}:<br />
                    {{ App\Models\Stackmember::where('stackmemberID', '=', $inStack[0]['stackmemberID'])->first()->name }}<br />
                    <i>{{ App\Models\Stackmember::where('stackmemberID', '=', $inStack[0]['stackmemberID'])->first()->description }}</i>
                </p>
            @else
                <p>{{ __('issuerBadgeClassInfo.bci_stack-no') }}</p>
            @endif
            <hr />

            <?php
            $badgeType = json_decode($thisBadgeClass->type, true);
            // ddd($assertionType);
            ?>


            <!-- A ZD miatt új kiegészítő űrlap készült, 1.2 változatszámmal 20240423 -->

            @php $badgetypeVersion = $badgeType['header']['version']; @endphp
            <p>@php echo($badgetypeVersion); @endphp</p>




            @if ($badgetypeVersion == '1.2')
                <input class="badgetypeversion" name="badgetypeversion" hidden value="{{ $badgetypeVersion }}">
                <p><button type="button" class="hubutton btn btn-primary" value="Hu">Magyar</button>
                    <button type="button" class="enbutton btn btn-primary" value="En">English</button>
                </p>
                @foreach ($badgeType['form'] as $item)
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

                        @foreach ($item['fields'] as $field)
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

                                    echo App\Http\Controllers\ManageIssuerController::getContentWithLangsTextarea(
                                        $field['load'],
                                        $field['id'],
                                        $disabled,
                                    );
                                @endphp

                            </p>
                        @endforeach

                    </div>
                @endforeach
            @else
                @foreach ($badgeType['form'] as $item)
                    <div class="row typefields" id="{{ $item['id'] }}"
                        @if ($item['selected'] == 'false') style="display:none" @endif>
                        <h3>{{ __('issuerBadgeClassInfo.bci_badge-type') }}</h3>


                        <h4>{{ $item['name'] }}</h4>
                        <p><i>{{ $item['description'] }}</i></p>


                        <!--<ul>-->
                        {{--    @foreach ($item['fields'] as $field)
                        <!--<li>--><p><strong>{{ $field['name'] }}</strong>
                            (<i>{{ $field['type'] }}</i>)
                            <br />{{ $field['description'] }}<br />
                            <?php $tmp = substr($field['id'], 15, 2); ?>
                            <textarea style="resize: none;" name="{{ $field['id'] }}" class="form-control" id="{{ $field['id'] }}" rows="1"
                                disabled>{{ $field['content'] }}</textarea>
                        <!--</li>--></p>
                    @endforeach --}}
                        <!--</ul>-->
                    </div>
                @endforeach

            @endif






        </div>
    </div>

    <h2 class="section-title"><span>


            @if (str_contains(url()->previous(), 'issuerBadgeclasses'))
                <p class="text-center"><a href="{{ env('APP_URL') }}/issuerBadgeclasses/{{ session()->get('issuerID') }}"
                        class="btn btn-action">{{ __('issuerBadgeClassInfo.bci_back') }}</a>
            @endif

            @if (str_contains(url()->previous(), '/company'))
                <p class="text-center"><a href="{{ env('APP_URL') }}/company/{{ session()->get('issuerID') }}"
                        class="btn btn-action">{{ __('issuerBadgeClassInfo.bci_back') }}</a>
            @endif

            @if (str_contains(url()->previous(), 'assistant'))
            <p class="text-center"><a href="{{ env('APP_URL') }}/assistantcompany/{{ session()->get('issuerID') }}"
                    class="btn btn-action">{{ __('issuerBadgeClassInfo.bci_back') }}</a>
        @endif

        </span></h2>


    <script>
        function copyTxt(param) {
            var text = document.getElementById(param).select();
            document.execCommand('copy');
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
        crossorigin="anonymous"></script>


    <script type="application/javascript">
                    $(function() {
            
                        $('.langcontent[lang="en"]').prop("hidden", false);
    
                        $('.hubutton').click(function(){
                         
                            $('.langcontent[lang="en"]').prop("hidden", true);
                            $('.langcontent[lang="hu"]').prop("hidden", false);
                        });
    
                        $('.enbutton').click(function(){
                      
                            $('.langcontent[lang="hu"]').prop("hidden", true);
                            $('.langcontent[lang="en"]').prop("hidden", false);
                        });
    
                    });
                </script>

@endsection
