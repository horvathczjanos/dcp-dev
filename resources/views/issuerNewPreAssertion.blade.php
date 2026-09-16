@extends('layouts.general')

@section('title', __('issuerNewPreAssertion.npa_page-title'))


@section('content')


    <style>
        .preassertion-badge-scroll {
            max-height: clamp(300px, 48vh, 520px);

            overflow-y: auto;
            overflow-x: auto;
        }



        .preassertion-badge-scroll thead th {
            position: sticky;
            top: 0;

            background-color: white;

            z-index: 10;

            box-shadow:
                0 1px 0 rgba(0, 0, 0, 0.10);
        }

        .preassertion-badge-table {
            margin-bottom: 0;
        }

        .preassertion-badge-table td,
        .preassertion-badge-table th {
            vertical-align: middle;
        }


        .preassertion-accordion-scroll {
            max-height: clamp(300px, 48vh, 520px);

            overflow-y: auto;
            overflow-x: auto;
        }
    </style>



    <form method="POST" action="{{ env('APP_URL') }}/recordPreAssertion" enctype="multipart/form-data">

        @csrf



        <h2 class="section-title">

            <span>
                {{ __('issuerNewPreAssertion.npa_page-title') }}
            </span>

        </h2>



        <div class="accordion" id="accordionExample">

            {{-- Badgeclass selection --}}

            <div class="accordion-item">


                <h2 class="accordion-header" id="headingOne">

                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                        aria-expanded="true" aria-controls="collapseOne">

                        {{ __('issuerNewPreAssertion.npa_select-bc') }}

                    </button>

                </h2>



                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                    data-bs-parent="#accordionExample">


                    <div class="accordion-body p-0">

                        <div class="preassertion-badge-scroll">


                            <table class="table preassertion-badge-table">


                                <thead>


                                    <tr>


                                        <th scope="col">
                                            #
                                        </th>


                                        <th scope="col">

                                            {{ __('issuerNewPreAssertion.npa_th-sel') }}

                                        </th>


                                        <th scope="col">

                                            {{ __('issuerNewPreAssertion.npa_th-bi') }}

                                        </th>


                                        <th scope="col">

                                            {{ __('issuerNewPreAssertion.npa_th-bcsd') }}

                                        </th>


                                        <th scope="col">

                                            {{ __('issuerNewPreAssertion.npa_th-func') }}

                                        </th>


                                    </tr>


                                </thead>



                                <tbody>


                                    @foreach ($badgeclasslist as $item)
                                        @if ($item->badgeclassID !== env('GREENCERT_ID'))
                                            <tr>
                                                <td scope="col">

                                                    {{ $loop->index + 1 }}

                                                </td>



                                                <td scope="col">


                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="badgeclass"
                                                            id="{{ $item->badgeclassID }}"
                                                            value="{{ $item->badgeclassID }}">
                                                    </div>
                                                </td>


                                                <td scope="col">
                                                    <a href="{{ env('APP_URL') }}/badgeclasses/{{ $item->badgeclassID }}/image"
                                                        target="_blank" rel="noopener noreferrer">

                                                        <img src="{{ env('APP_URL') }}/badgeclasses/{{ $item->badgeclassID }}/image"
                                                            width="100px" alt="badge image">
                                                    </a>
                                                </td>

                                                <td scope="col">
                                                    {{ $item->name }}
                                                    <br />
                                                    {{ $item->created_at }}
                                                    <br />
                                                    {{ $item->description }}
                                                </td>

                                                <td>
                                                    <a class="btn btn-primary"
                                                        href="/issuerbadges/{{ $item->badgeclassID }}/info" role="button">
                                                        {{ __('issuerNewPreAssertion.npa_btn-more') }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            {{-- Earner's data and date --}}

            <div class="accordion-item">

                <h2 class="accordion-header" id="headingTwo">

                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">

                        {{ __('issuerNewPreAssertion.npa_awardee-id_date') }}

                    </button>
                </h2>

                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                    data-bs-parent="#accordionExample">
                    <div class="accordion-body preassertion-accordion-scroll">
                        <div class="bd-callout bd-callout-info">
                            <p>
                                {{ __('issuerNewPreAssertion.npa_a-name') }}
                            </p>
                        </div>

                        @if (session('locale') !== 'hu')
                            <div class="mb-3">
                                <label for="firstname" class="form-label">
                                    {{ __('issuerNewPreAssertion.npa_a-fname-lbl') }}
                                </label>
                                <input type="text" name="firstname" class="form-control" id="firstname">
                            </div>

                            <div class="mb-3">
                                <label for="lastname" class="form-label">
                                    {{ __('issuerNewPreAssertion.npa_a-lname-lbl') }}
                                </label>
                                <input type="text" name="lastname" class="form-control" id="lastname">
                            </div>
                        @else
                            <div class="mb-3">
                                <label for="lastname" class="form-label">
                                    {{ __('issuerNewPreAssertion.npa_a-lname-lbl') }}

                                </label>
                                <input type="text" name="lastname" class="form-control" id="lastname">
                            </div>

                            <div class="mb-3">
                                <label for="firstname" class="form-label">
                                    {{ __('issuerNewPreAssertion.npa_a-fname-lbl') }}
                                </label>
                                <input type="text" name="firstname" class="form-control" id="firstname">
                            </div>
                        @endif

                        <div class="bd-callout bd-callout-info">
                            <p>
                                {{ __('issuerNewPreAssertion.npa_a-email') }}
                            </p>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label" required>
                                {{ __('issuerNewPreAssertion.npa_a-email-lbl') }}
                            </label>
                            <input type="email" name="email" class="form-control" id="email" required>
                        </div>

                        <div class="bd-callout bd-callout-info">
                            <p>
                                {{ __('issuerNewPreAssertion.npa_a-date') }}
                            </p>
                        </div>

                        <div class="mb-3">
                            <label for="date" class="form-label" required>
                                {{ __('issuerNewPreAssertion.npa_a-date-lbl') }}
                            </label>

                            <input type="datetime-local" name="date" class="form-control" id="date" required>
                        </div>

                        <div class="bd-callout bd-callout-info">
                            <p>
                                {{ __('issuerNewPreAssertion.npa_a-date_exp') }}
                            </p>
                        </div>

                        <div class="mb-3">
                            <input type="checkbox" name="neverExpires" id="neverExpires" checked>
                            <label for="neverExpires">
                                {{ __('issuerNewPreAssertion.npa_a-date_exp-chkbox-lbl') }}
                            </label>
                        </div>

                        <div class="mb-3 date_2" style="display: none">
                            <label for="date_2" class="form-label" required>
                                {{ __('issuerNewPreAssertion.npa_a-date_exp-lbl') }}
                            </label>
                            <input type="datetime-local" name="date_2" class="form-control" id="date_2">
                        </div>

                    </div>
                </div>
            </div>

            {{-- Badge type --}}

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">

                    <button class="accordion-button collapsed text-muted" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree" disabled>
                        {{ __('issuerNewPreAssertion.npa_badge-type') }}
                    </button>
                </h2>

                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                    data-bs-parent="#accordionExample">
                    <div class="accordion-body preassertion-accordion-scroll">


                    </div>
                </div>
            </div>
        </div>


        {{-- Save --}}

        <div class="bd-callout bd-callout-info">
            <p>
                {{ __('issuerNewPreAssertion.npa_btn-lbl') }}
            </p>
        </div>


        <p class="text-center">
            <button type="submit" class="btn btn-primary" value="create">
                {{ __('issuerNewPreAssertion.npa_btn') }}
                &nbsp;&nbsp;
                <i class="bi-save"></i>
            </button>
        </p>

        <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
            crossorigin="anonymous"></script>
        <script type="application/javascript">

        $(function() {


            $('#neverExpires').change(function(e) {

                if ($(this).prop('checked')) {

                    $('.date_2')
                        .children('label')
                        .attr('required', false);

                    $('.date_2')
                        .children('input')
                        .attr('required', false);

                    $('.date_2').hide();

                } else {
                    $('.date_2')
                        .children('label')
                        .attr('required', true);

                    $('.date_2')
                        .children('input')
                        .attr('required', true);

                    $('.date_2').show();
                }

            });



            // Selecting badgeclass

            $('.form-check-input').change(function() {


                var actBadgeID =
                    $(this).attr('id');

                $('div#collapseThree div.accordion-body div')
                    .remove();


                $('button[aria-controls=collapseThree]')
                    .prop(
                        'disabled',
                        false
                    );


                var allBadges =
                    {!! json_encode($badgeclasslist->toArray()) !!};


                $.each(
                    allBadges,
                    function(index, value) {


                        if (
                            value.badgeclassID == actBadgeID
                        ) {

                            var badgeType = JSON.parse(value.type);

                            $.each(
                                badgeType.form,
                                function(i2, v2) {


                                    if (
                                        v2.selected == "true") {


                                        var tmpFields =
                                            $(

                                                '<div ' +

                                                'class="row typefields" ' +

                                                'id="' +
                                                v2.id +
                                                '">' +

                                                '<h3>' +
                                                v2.name +
                                                '</h3>' +

                                                '<i>' +
                                                v2.description +
                                                '</i>' +

                                                '</div>'

                                            );



                                        var tmpUL = $('<span></span>');


                                        $.each(
                                            v2.fields,
                                            function(i3, v3) {

                                                var disabled =  '';


                                                if (
                                                    (v3.id).slice(15, 17) != '00'
                                                ) {
                                                    disabled = 'disabled';
                                                }

                                                if (
                                                    v3.content ==null) {
                                                    v3.content ='';
                                                }

                                                tmpUL.append(

                                                    '<p>' +

                                                    '<strong>' +
                                                    v3.name +
                                                    '</strong>' +

                                                    '(<i>' +
                                                    v3.type +
                                                    '</i>)' +

                                                    '<br />' +

                                                    '<textarea ' +

                                                    'style="resize: none;" ' +

                                                    'name="' +
                                                    v3.id +
                                                    '" ' +

                                                    'class="form-control" ' +

                                                    'id="' +
                                                    v3.id +
                                                    '" ' +

                                                    'rows="1" ' +

                                                    disabled +

                                                    '>' +

                                                    v3.content +

                                                    '</textarea>' +

                                                    '</p>'

                                                );
                                            }
                                        );


                                        tmpFields.append(tmpUL);



                                        $('div#collapseThree div.accordion-body').append(tmpFields);
                                    }
                                }
                            );
                        }
                    }
                );
            });
        });

    </script>

    </form>


    <h2 class="section-title">
        <span>
            <p class="text-center">
                <a href="{{ env('APP_URL') }}/backtocompany" class="btn btn-action">
                    {{ __('issuerNewPreAssertion.npa_back-2-inst') }}
                </a>
            </p>
        </span>
    </h2>

@endsection
