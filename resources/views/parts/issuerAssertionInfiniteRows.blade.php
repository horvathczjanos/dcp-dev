@foreach ($assertions as $item)
    <tr>


        {{-- ===================================================== --}}
        {{-- SORSZÁM ÉS CHECKBOX                                  --}}
        {{-- ===================================================== --}}

        <td scope="col" class="issuer-assertion-number">

            <span class="issuer-assertion-number-value"></span>


            @if ($isAdvancedIssuer)
                <div class="form-check mt-2">

                    <input class="form-check-input issuer-assertion-checkbox" type="checkbox"
                        value="{{ $item->assertionID }}" id="issuerAssertionCheckbox_{{ $item->assertionID }}">

                </div>
            @endif

        </td>



        {{-- ===================================================== --}}
        {{-- KIÁLLÍTÁS DÁTUMA                                     --}}
        {{-- ===================================================== --}}

        <td scope="col">

            {{ $item->issuedOn }}

        </td>



        {{-- ===================================================== --}}
        {{-- CÍMZETT                                               --}}
        {{-- ===================================================== --}}

        <td scope="col">

            @if ($item->originalIdentity)
                {{ $item->originalIdentity }}
            @else
                NA
            @endif

        </td>



        {{-- ===================================================== --}}
        {{-- BADGE KÉP                                             --}}
        {{-- ===================================================== --}}

        <td scope="col">

            <div class="bg-image position-relative"
                style="
                    width: 75px;
                    min-height: 75px;
                    border: solid 1px #ededed;
                ">

                <a href="{{ url('/assertions/' . $item->assertionID . '/badge') }}" target="_blank"
                    rel="noopener noreferrer">

                    <img src="{{ url('/assertions/' . $item->assertionID . '/badge') }}" width="75" loading="lazy"
                        decoding="async" alt="digital badge">

                </a>



                {{-- ============================================= --}}
                {{-- VISSZAVONT BADGE JELZÉSE                     --}}
                {{-- ============================================= --}}

                @if ($item->revoked == 'on')
                    <div class="position-absolute"
                        style="
                            rotate: 45deg;
                            top: 20px;
                            left: -10px;
                        ">

                        <p>

                            <small
                                class="
                                    px-2
                                    py-1
                                    fw-semibold
                                    text-light
                                    bg-danger
                                    bg-opacity-80
                                    border-start
                                    border-2
                                    border-danger
                                ">

                                {{ __('issuerAllAssertions.aa_flag-revoked') }}

                            </small>

                        </p>

                    </div>
                @endif

            </div>

        </td>



        {{-- ===================================================== --}}
        {{-- MŰVELETEK                                             --}}
        {{-- ===================================================== --}}

        <td>


            {{-- INFO --}}

            <a class="btn btn-primary" href="{{ url('/assertions/' . $item->assertionID . '/info') }}" role="button">

                {{ __('issuerAllAssertions.aa_btn-info') }}

                &nbsp;&nbsp;

                <i class="bi-info-circle"></i>

            </a>



            {{-- ================================================= --}}
            {{-- EMAIL KÜLDÉS                                     --}}
            {{-- ================================================= --}}

            @if ($isAdvancedIssuer)
                <a class="btn btn-primary" href="{{ url('/assertions/' . $item->assertionID . '/send2') }}"
                    role="button">

                    {{ __('issuerAllAssertions.aa_btn-sendby-plus') }}

                </a>
            @else
                <a class="btn btn-primary" href="{{ url('/assertions/' . $item->assertionID . '/send') }}"
                    role="button">

                    {{ __('issuerAllAssertions.aa_btn-sendby') }}

                </a>
            @endif

        </td>

    </tr>
@endforeach
