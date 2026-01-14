@extends('layouts.general')

@section('title', __('issuerEditPreAssertion.epa_page-title'))


@section('content')

    <form method="POST" action="{{ env('APP_URL') }}/recordPreAssertion" enctype="multipart/form-data">
        @csrf

        <h2 class="section-title"><span>{{ __('issuerEditPreAssertion.epa_page-title') }}</span></h2>
        <div class="accordion" id="accordionExample">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button " type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                        aria-expanded="true" aria-controls="collapseOne">
                        {{ __('issuerEditPreAssertion.epa_selected-bc') }}
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                    data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('issuerEditPreAssertion.epa_th-bi') }}</th>
                                    <th scope="col">{{ __('issuerEditPreAssertion.epa_th-bcsd') }}</th>
                                    <th scope="col">{{ __('issuerEditPreAssertion.epa_th-func') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td scope="col">
                                        <a href="{{ env('APP_URL') }}/badgeclasses/{{ $thisPreAssertion['badgeclassID'] }}/image"
                                            target="_blank" rel="noopener noreferrer"><img
                                                src="{{ env('APP_URL') }}/badgeclasses/{{ $thisPreAssertion['badgeclassID'] }}/image"
                                                width="100px" alt="badge image"></a>
                                    </td>
                                    <?php
                                    
                                    $thisBadgeclass = App\Models\Badgeclass::where('badgeclassID', '=', $thisPreAssertion['badgeclassID'])->first();
                                    
                                    ?>

                                    <td scope="col">
                                        {{ $thisBadgeclass->name }}<br />{{ $thisBadgeclass->created_at }}<br />{{ $thisBadgeclass->description }}
                                    </td>
                                    <td><a class="btn btn-primary"
                                            href="/issuerbadges/{{ $thisPreAssertion['badgeclassID'] }}/info"
                                            role="button">{{ __('issuerEditPreAssertion.epa_btn-more') }}</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        {{ __('issuerEditPreAssertion.epa_awardee-id_date') }}
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                    data-bs-parent="#accordionExample">
                    <div class="accordion-body">

                        <div class="bd-callout bd-callout-info">
                            <p>{{ __('issuerNewPreAssertion.npa_a-name') }}</p>
                        </div>


                        <!-- A ZD miatt új kiegészítő űrlap készült, 1.2 változatszámmal 20240423 -->

                        <?php
                        
                        $assertionType = json_decode($thisPreAssertion->assertiontype, true);
                        ?>

                        <?php
                        $firtname = $assertionType['form'][0]['fields'][0]['content'];
                        $lastname = $assertionType['form'][0]['fields'][1]['content'];
                        
                        ?>


                        @if (session('locale') !== 'hu')
                            <div class="mb-3">
                                <label for="firstname"
                                    class="form-label">{{ __('issuerNewPreAssertion.npa_a-fname-lbl') }}</label>
                                <input type="text" name="firstname" class="form-control" id="firstname"
                                    value="{{ $firtname }}">
                            </div>
                            <div class="mb-3">
                                <label for="lastname"
                                    class="form-label">{{ __('issuerNewPreAssertion.npa_a-lname-lbl') }}</label>
                                <input type="text" name="lastname" class="form-control" id="lastname"
                                    value="{{ $lastname }}">
                            </div>
                        @else
                            <div class="mb-3">
                                <label for="lastname"
                                    class="form-label">{{ __('issuerNewPreAssertion.npa_a-lname-lbl') }}</label>
                                <input type="text" name="lastname" class="form-control" id="lastname"
                                    value="{{ $lastname }}">
                            </div>
                            <div class="mb-3">
                                <label for="firstname"
                                    class="form-label">{{ __('issuerNewPreAssertion.npa_a-fname-lbl') }}</label>
                                <input type="text" name="firstname" class="form-control" id="firstname"
                                    value="{{ $firtname }}">
                            </div>
                        @endif







                        <div class="bd-callout bd-callout-info">
                            <p>{{ __('issuerEditPreAssertion.epa_a-email') }}</p>
                        </div>
                        <div class="mb-3">
                            <label for="email"
                                class="form-label">{{ __('issuerEditPreAssertion.epa_a-email-lbl') }}</label>
                            <input type="email" name="email" class="form-control" id="email" required
                                value="{{ $thisPreAssertion['originalIdentity'] }}">
                        </div>
                        <div class="bd-callout bd-callout-info">
                            <p>{{ __('issuerEditPreAssertion.epa_a-date') }}</p>
                        </div>
                        <div class="mb-3">
                            <label for="date"
                                class="form-label">{{ __('issuerEditPreAssertion.epa_a-date-lbl') }}</label>
                            <?php
                            $issuedOn = substr($thisPreAssertion['issuedOn'], 0, 16);
                            ?>

                            <input type="datetime-local" name="date" class="form-control" id="date" required
                                value="{{ $issuedOn }}">


                        </div>

                        <div class="bd-callout bd-callout-info">
                            <p>{{ __('issuerNewPreAssertion.npa_a-date_2') }}</p>
                        </div>


                        <?php
                        $expiresAt = '';
                        if ($thisPreAssertion['neverexpires'] == 'off') {
                            $expiresAt = substr($thisPreAssertion['expiresAt'], 0, 16);
                        }
                        //$issuedOn = substr($thisPreAssertion['issuedOn'], 0, 16);
                        ?>

                        <div class="mb-3">
                            <input type="checkbox" name="neverExpires" id="neverExpires"
                                @if ($thisPreAssertion['neverexpires'] == 'on') checked @endif>
                            <label for="neverExpires">{{ __('issuerNewPreAssertion.never_expires_checkbox') }}</label>
                        </div>

                        <div class="mb-3 date_2" @if ($thisPreAssertion['neverexpires'] == 'on') style="display: none" @endif>
                            <label for="date" class="form-label"
                                required>{{ __('issuerNewPreAssertion.npa_a-date-lbl_2') }}</label>
                            <input type="datetime-local" name="date_2" class="form-control" id="date_2"
                                value="{{ $expiresAt }}">
                        </div>





                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        {{ __('issuerEditPreAssertion.epa_badge-type') }}
                    </button>
                </h2>

                <?php
                $assertionType = json_decode($thisPreAssertion->assertiontype, true);
                ?>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                    data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        @foreach ($assertionType['form'] as $item)
                            <div class="row typefields" id="{{ $item['id'] }}"
                                @if ($item['selected'] == 'false') style="display:none" @endif>
                                <h3>{{ $item['name'] }}</h3>
                                <p><i>{{ $item['description'] }}</i></p>
                                <!--<ul>-->
                                @foreach ($item['fields'] as $field)
                                    <!--<li>-->
                                    <p><strong>{{ $field['name'] }}</strong>
                                        (<i>{{ $field['type'] }}</i>)
                                        <br />{{ $field['description'] }}<br />
                                        <?php $tmp = substr($field['id'], 15, 2); ?>
                                        <textarea style="resize: none;" name="{{ $field['id'] }}" class="form-control" id="{{ $field['id'] }}"
                                            rows="1" @if ($tmp != '00' || in_array($field['id'], ['Badgecustom_01_0001', 'badgecustom_01_0002'])) disabled @endif>{{ $field['content'] }}</textarea>
                                        <!--</li>-->
                                    </p>
                                @endforeach
                                <!--</ul>-->
                            </div>
                            <!--<br />-->
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

        <div class="bd-callout bd-callout-info">
            <p>{{ __('issuerEditPreAssertion.epa_btn-lbl') }}</p>
        </div>
        <p class="text-center ">
            <button type="submit" class="btn btn-primary" value="save">
                {{ __('issuerEditPreAssertion.epa_btn') }}&nbsp;&nbsp;<i class="bi-save"></i>
            </button>
        </p>
        </div>


        <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
            crossorigin="anonymous"></script>
        <script type="application/javascript">
            $(function() {
                

                $('#neverExpires').change(function(e){

                if ($(this).prop('checked')){
                        $('.date_2').children('label').attr('required', false);
                        $('.date_2').children('input').attr('required', false);
                        $('.date_2').hide();
                    }else{
                        $('.date_2').children('label').attr('required', true);
                        $('.date_2').children('input').attr('required', true);
                        $('.date_2').show();
                    }
                });
            });
        </script>

    </form>


    <h2 class="section-title"><span>
            <p class="text-center"><a href="{{ env('APP_URL') }}/showPreAssertions"
                    class="btn btn-action">{{ __('issuerEditPreAssertion.epa_back-2-pa') }}</a></p>
        </span></h2>

@endsection
