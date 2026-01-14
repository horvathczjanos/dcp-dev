@extends('layouts.general')

@section('title', __('issuerBadgeMultilangAlter.bmla_page-title'))


@section('content')

    <h2 class="section-title"><span>{{ __('issuerBadgeMultilangAlter.bmla_page-title') }}</span></h2>

    <div class="row">
        <div class="col-12 text-center">

            <img src="data:image/png;base64,{{ base64_encode($badgeImage) }}" style="width: 150px;" alt="badge image">
            <p><strong>{{ $thisBadgeClass->name }}</strong><br />

            </p>
        </div>
    </div>


    <div class="container">
        <div class="alert alert-success mt-5" role="alert">
            <p class="text-center">{{ __('issuerBadgeMultilangAlter.bmla_descr') }}</p>
        </div>
    </div>

    <form method="POST" action="{{ env('APP_URL') }}/recordmultilangfeed">
        @csrf


        @foreach ($languages as $key => $lang)
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="{{ $key }}" name="language[]"
                    value="{{ $key }}" autocomplete="off"
                    {{ in_array($key, $selectedLanguages) ? ' checked' : '' }}
                    {{ $thisBadgeClass->originalLang == $key ? 'checked disabled' : '' }}>
                <label class="form-check-label" for="{{ $key }}">
                    {{ $lang }}
                </label>
            </div>
        @endforeach

        <hr />

        @foreach ($languages as $key => $lang)
            <div class="mb-3 {{ $key }} langheader"
                {{ !in_array($key, $selectedLanguages) ? ' style=display:none' : '' }}>
                <h3>{{ $lang }}</h3>

                @php
                    foreach ($multilangPack['form'] as $pack) {
                        if ($pack['id'] == $key) {
                            foreach ($pack['fields'] as $field) {
                                if (strpos($field['id'], '_name') !== false) {
                                    //echo '<p>' . __('issuerNewBadgeClass.nbc_name', [], $key) . '</p>';
                                    echo '<div class="bd-callout bd-callout-info">';
                                    echo '<p>' . __('issuerNewBadgeClass.nbc_name-lbl', [], $key) . '</p>';
                                    echo '</div>';
                                    echo '<div class="mb-3">';
                                    echo '<label for="badgeclass_name" class="form-label" >' . __('issuerNewBadgeClass.nbc_name', [], $key) . '</label>';
                                    echo '<input type="text" name="badgeclass_name_' . $key . '" class="form-control" id="badgeclass_name" value="' . $field['content'] . '">';
                                    echo '</div>';
                                } elseif (strpos($field['id'], '_description') !== false) {
                                    //echo '<p>' . __('issuerNewBadgeClass.nbc_desc', [], $key) . '</p>';
                                    echo '<div class="bd-callout bd-callout-info">';
                                    echo '<p>' . __('issuerNewBadgeClass.nbc_desc-lbl', [], $key) . '</p>';
                                    echo '</div>';
                                    echo '<div class="mb-3">';
                                    echo '<label for="badgeclass_description" class="form-label" >' . __('issuerNewBadgeClass.nbc_desc', [], $key) . '</label>';
                                    echo '<textarea name="badgeclass_description_' . $key . '" class="form-control" id="badgeclass_description" rows="3" >' . $field['content'] . '</textarea>';
                                    echo '</div>';
                                } elseif (strpos($field['id'], '_criteria') !== false) {
                                    //echo '<p>' . __('issuerNewBadgeClass.nbc_criteria', [], $key) . '</p>';
                                    echo '<div class="bd-callout bd-callout-info">';
                                    echo '<p>' . __('issuerNewBadgeClass.nbc_criteria-lbl', [], $key) . '</p>';
                                    echo '</div>';
                                    echo '<div class="mb-3">';
                                    echo '<label for="badgeclass_criteria" class="form-label" >' . __('issuerNewBadgeClass.nbc_criteria', [], $key) . '</label>';
                                    echo '<textarea name="badgeclass_criteria_' . $key . '" class="form-control" id="badgeclass_criteria" rows="3" >' . $field['content'] . '</textarea>';
                                    echo '</div>';
                                } elseif (strpos($field['id'], '_tags') !== false) {
                                    //echo '<p>' . __('issuerNewBadgeClass.nbc_tags', [], $key) . '</p>';
                                    echo '<div class="bd-callout bd-callout-info">';
                                    echo '<p>' . __('issuerNewBadgeClass.nbc_tags-lbl', [], $key) . '</p>';
                                    echo '</div>';
                                    echo '<div class="mb-3">';
                                    echo '<label for="badgeclass_tags" class="form-label" >' . __('issuerNewBadgeClass.nbc_tags', [], $key) . '</label>';
                                    echo '<input type="text" name="badgeclass_tags_' . $key . '" class="form-control" id="badgeclass_tags" value="' . $field['content'] . '">';
                                    echo '</div>';
                                } elseif (strpos($field['id'], '_imageauthorname') !== false) {
                                    //echo '<p>' . __('issuerNewBadgeClass.nbc_badge-img-auth-name', [], $key) . '</p>';
                                    echo '<hr />';
                                    echo '<div class="mb-3">';
                                    echo '<label for="badgeclass_image_author_name" class="form-label">' . __('issuerNewBadgeClass.nbc_badge-img-auth-name', [], $key) . '</label>';
                                    echo '<input type="text" name="badgeclass_image_author_name_' . $key . '" class="form-control" id="badgeclass_image_author_name" value="' . $field['content'] . '">';
                                    echo '</div>';
                                } elseif (strpos($field['id'], '_imagecaption') !== false) {
                                    //echo '<p>' . __('issuerNewBadgeClass.nbc_badge-img-capt', [], $key) . '</p>';
                                    echo '<div class="mb-3">';
                                    echo '<label for="badgeclass_image_caption" class="form-label">' . __('issuerNewBadgeClass.nbc_badge-img-capt', [], $key) . '</label>';
                                    echo '<input type="text" name="badgeclass_image_caption_' . $key . '" class="form-control" id="badgeclass_image_caption" value="' . $field['content'] . '">';
                                    echo '</div>';
                                }
                            }
                    
                            break;
                        }
                    }
                    
                @endphp

                <hr />
            </div>
        @endforeach

        <p class="text-center">
            <button type="submit" class="btn btn-primary" value="Change">
                {{ __('issuerBadgeMultilangAlter.bmla_btn') }}
            </button>
        </p>

    </form>

    <h2 class="section-title"><span>
            <p class="text-center"><a href="{{ env('APP_URL') }}/backtocompany" class="btn btn-action">{{ __('issuerBadgeMultilangAlter.bmla_btn-back') }}</a></p>

        </span></h2>

    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
        crossorigin="anonymous"></script>

    <script type="application/javascript">
    
    $(function() {
        $('.form-check-input').change(function() {
            
            if ($(this).is(":checked")){
            
                $('div.langheader.'+$(this).attr('id')+'').show();

               
            }else{
                $('div.langheader.'+$(this).attr('id')+'').hide();
            }
            
        });

        let originalLang = {{Illuminate\Support\Js::from($thisBadgeClass->originalLang)}};

        $('div.langheader.' + originalLang + '').show();
      
        $('div.langheader.' + originalLang + '').children('div.mb-3').children('input.form-control#badgeclass_name').val({{Illuminate\Support\Js::from($thisBadgeClass->name)}}).prop( "disabled", true );
        $('div.langheader.' + originalLang + '').children('div.mb-3').children('textarea.form-control#badgeclass_description').val({{Illuminate\Support\Js::from($thisBadgeClass->description)}}).prop( "disabled", true );
        $('div.langheader.' + originalLang + '').children('div.mb-3').children('textarea.form-control#badgeclass_criteria').val({{Illuminate\Support\Js::from($thisBadgeClass->criteria)}}).prop( "disabled", true );
        $('div.langheader.' + originalLang + '').children('div.mb-3').children('input.form-control#badgeclass_tags').val({{Illuminate\Support\Js::from($tagtext)}}).prop( "disabled", true );
        $('div.langheader.' + originalLang + '').children('div.mb-3').children('input.form-control#badgeclass_image_author_name').val({{Illuminate\Support\Js::from($thisBadgeImage->imgauthorname)}}).prop( "disabled", true );
        $('div.langheader.' + originalLang + '').children('div.mb-3').children('input.form-control#badgeclass_image_caption').val({{Illuminate\Support\Js::from($thisBadgeImage->imgcaption)}}).prop( "disabled", true );
    });
        
    </script>

@endsection
