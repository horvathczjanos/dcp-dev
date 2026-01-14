@extends('layouts.general')

@section('title', __('issuerSetBadgeType.sbt_page-title'))


@section('content')

    <h2 class="section-title"><span>{{ __('issuerSetBadgeType.sbt_page-title') }}</span></h2>

    <div class="row">
        <div class="col-12 text-center">

            <img src="data:image/png;base64,{{ base64_encode($badgeImage) }}" style="width: 150px;" alt="badge image">
            <p><strong>{{ $thisIssuerBadge->name }}</strong><br />

            </p>

        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <h3>
                @if (!is_null($badgeType))
                    {{ __('issuerSetBadgeType.sbt_can-set') }}
                @else
                    {{ __('issuerSetBadgeType.sbt_cannot-set') }}
                @endif
            </h3>
        </div>
    </div>

    @if (!is_null($badgeType))

        <form method="POST" action="./../../recordBadgeType" enctype="multipart/form-data">
            @csrf


            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <strong>{{ $message }}</strong>

                </div>
            @endif

            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <?php
            $badgeType = json_decode(json_encode($badgeType), true);
            ?>

            <!-- A ZD miatt új kiegészítő űrlap készült, 1.2 változatszámmal 20240423 -->

            @php $badgetypeVersion = $badgeType['header']['version']; @endphp
            <p>@php echo($badgetypeVersion); @endphp</p>

            <input class="badgetypeversion" name="badgetypeversion" hidden value="{{ $badgetypeVersion }}">
            <p><button type="button" class="hubutton btn btn-primary" value="Hu">Magyar</button>
                <button type="button" class="enbutton btn btn-primary" value="En">English</button>
            </p>


            @if ($badgetypeVersion == '1.1')
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">{{ __('issuerSetBadgeType.sbt_th-sel') }}</th>
                            <th scope="col">{{ __('issuerSetBadgeType.sbt_th-type-name') }}</th>
                            <th scope="col">{{ __('issuerSetBadgeType.sbt_th-type-descr') }}</th>

                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($badgeType['form'] as $item)
                            <tr>
                                <td scope="col">{{ $loop->index + 1 }}</td>
                                <td>

                                    <div class="form-check">
                                        @if ($item['name'] != 'General')
                                            <input class="form-check-input" type="radio" name="badgetype[]"
                                                id="{{ $item['id'] }}" value="{{ $item['id'] }}"
                                                @if ($item['selected'] == 'true') checked @endif>
                                        @else
                                        @endif
                                    </div>

                                </td>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['description'] }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td></td>
                            <td>
                                <button type="button" class="resetbutton btn btn-primary" value="Reset">
                                    {{ __('issuerSetBadgeType.sbt_reset-btn') }} </button>
                            </td>
                            <td>{{ __('issuerSetBadgeType.sbt_reset-btn-lbl') }}</td>
                        </tr>
                    </tbody>
                </table>

                @foreach ($badgeType['form'] as $item)
                    @if ($item['name'] == 'General')
                        <?php
                        $item['selected'] = 'true';
                        ?>
                    @endif

                    <div class="row typefields" id="{{ $item['id'] }}"
                        @if ($item['selected'] == 'false') style="display:none" @endif>
                        <h3>{{ $item['name'] }}</h3>
                        <i>{{ $item['description'] }}</i>
                        <!--<ul>-->
                        @foreach ($item['fields'] as $field)
                            <!--<li>-->
                            <p><strong>{{ $field['name'] }}</strong>
                                (<i>{{ $field['type'] }}</i>)
                                <br />{{ $field['description'] }}<br />
                                <?php $tmp = substr($field['id'], 15, 2); ?>
                                <textarea style="resize: none;" name="{{ $field['id'] }}" class="form-control" id="{{ $field['id'] }}"
                                    rows="1" @if ($tmp == '00') disabled @endif>{{ $field['content'] }}</textarea>
                                <!--</li>-->
                            </p>
                        @endforeach
                        <!--</ul>-->
                    </div>
                @endforeach
            @elseif ($badgetypeVersion == '1.2')
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>

                            <th scope="col">{{ __('issuerSetBadgeType.sbt_th-type-name') }}</th>
                            <th scope="col">{{ __('issuerSetBadgeType.sbt_th-type-descr') }}</th>

                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($badgeType['form'] as $item)
                            <tr>
                                <td scope="col">{{ $loop->index + 1 }}</td>

                                @php

                                    echo '<td>' .
                                        App\Http\Controllers\ManageIssuerController::getContentWithLangs(
                                            $item['name'],
                                        ) .
                                        '</td>';

                                    echo '<td>' .
                                        App\Http\Controllers\ManageIssuerController::getContentWithLangs(
                                            $item['description'],
                                        ) .
                                        '</td>';

                                    /*  echo '<td>' .
                                        App\Http\Controllers\ManageIssuerController::getContentByLang(
                                            $item['name'],
                                            'hu',
                                        ) .
                                        '</td>';

                                    echo '<td>' .
                                        App\Http\Controllers\ManageIssuerController::getContentByLang(
                                            $item['description'],
                                            'hu',
                                        ) .
                                        '</td>'; */

                                @endphp

                            </tr>
                        @endforeach
                    </tbody>
                </table>

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



            @endif

            <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
                crossorigin="anonymous"></script>

            <script type="application/javascript">
                $(function() {
                    // alert("hello");
                    $('.form-check-input').change(function() {

                        //alert($(this).attr('id'));
                        $('div.typefields').hide();
                        $('div.typefields#' + $(this).attr('id')).show();
                        $('div.typefields#bt_001').show();

                    });

                    $('.resetbutton').click(function() {
                        
                        $( document ).find('input.form-check-input').prop('checked',false);
                        $('div.typefields').hide();
                        $('div.typefields#bt_001').show();
                        //alert('Reset');
                    });

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

            <div class="bd-callout bd-callout-info">
                <p>{{ __('issuerSetBadgeType.sbt_set-btn-lbl') }}</p>
            </div>
            <p class="text-center ">
                <button type="submit" class="btn btn-primary" value="set">
                    {{ __('issuerSetBadgeType.sbt_set-btn') }}
                </button>
            </p>
        </form>

    @endif


    <h2 class="section-title"><span>
            <p class="text-center"><a href="./../../company/{{ session()->get('issuerID') }}"
                    class="btn btn-action">{{ __('issuerSetBadgeType.sbt_back-2-inst') }}</a></p>

        </span></h2>



@endsection
