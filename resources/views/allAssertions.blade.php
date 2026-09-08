@extends('layouts.general')

@section('title', __('issuerAllAssertions.aa_page-title'))


@section('content')

    <h2 class="section-title">
        <span>
            {{ __('issuerAllAssertions.aa_page-title') }}
        </span>
    </h2>

    {{-- Header --}}

    <div class="container">
        <div class="alert alert-success mt-5" role="alert">
            <p class="text-center">
                <b>
                    {{ __('issuerAllAssertions.aa_num-o-a') }}:
                </b>
                {{ $numberofassertions }}
            </p>


            <div id="issuerAssertionSelectionBar" class="checkboxBar" style="display: none; margin-right: 5px;">
                <button style="margin-right: 5px;" class="btn btn-primary" type="button" id="issuerAssertionResetSelection">
                    {{ __('issuerAllAssertions.aa_btn-clear-sel') }}
                </button>

                <button style="margin-right: 5px;" class="btn btn-primary" type="button" id="issuerAssertionMultipleSend">
                    {{ __('issuerAllAssertions.aa_btn-send-multi-notif') }}
                </button>
            </div>
        </div>
    </div>

    {{-- Assertions --}}

    <div class="container">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">
                            #
                        </th>
                        <th scope="col">
                            {{ __('issuerAllAssertions.aa_th-date') }}
                        </th>
                        <th scope="col">
                            {{ __('issuerAllAssertions.aa_th-awardee-id') }}
                        </th>
                        <th scope="col">
                            {{ __('issuerAllAssertions.aa_th-image') }}
                        </th>
                        <th scope="col">
                            {{ __('issuerAllAssertions.aa_th-func') }}
                        </th>
                    </tr>
                </thead>



                <tbody id="issuerAssertionInfiniteBody">

                </tbody>

            </table>

        </div>


        {{-- Loading part --}}

        <div id="issuerAssertionInfiniteLoading" class="text-center my-4" style="display: none;">

            <div class="spinner-border" role="status">
                <span class="visually-hidden">
                    Loading...
                </span>
            </div>
            <div class="mt-2">
                Loading assertions...
            </div>
        </div>

        {{-- End part --}}

        <div id="issuerAssertionInfiniteEnd" class="text-center text-muted my-4" style="display: none;">No more assertions.
        </div>

        {{-- Error part --}}

        <div id="issuerAssertionInfiniteError" class="alert alert-danger text-center my-4" style="display: none;">
            Error while loading assertions.
            <br>
            <button type="button" id="issuerAssertionInfiniteRetry" class="btn btn-secondary btn-sm mt-2">
                Retry
            </button>
        </div>

        {{-- Sentinel part --}}

        <div id="issuerAssertionInfiniteSentinel" style="height: 1px;"></div>

    </div>

    {{-- Multiple e-mail sending --}}

    <form id="issuerAssertionMultipleForm" method="POST" action="{{ url('/assertionsMultipleNotification') }}"
        style="display: none;">
        @csrf
        <textarea id="issuerAssertionMultipleInput" name="tmpinput"></textarea>
    </form>

    {{-- Back to Company page --}}

    <h2 class="section-title">
        <span>
            <p class="text-center">
                <a href="{{ url('/backtocompany') }}" class="btn btn-action">
                    {{ __('issuerAllAssertions.aa_back-2-inst') }}
                </a>
            </p>
        </span>
    </h2>

    {{-- Counting rows with CSS, interesting attempt :-)  --}}

    <style>
        #issuerAssertionInfiniteBody {
            counter-reset: issuer-assertion-row;
        }

        #issuerAssertionInfiniteBody>tr {
            counter-increment: issuer-assertion-row;
        }

        #issuerAssertionInfiniteBody>tr>td.issuer-assertion-number>span.issuer-assertion-number-value::before {
            content: counter(issuer-assertion-row);
        }
    </style>

    {{-- -Let's script them all! :-) --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const assertionBody =
                document.getElementById(
                    'issuerAssertionInfiniteBody'
                );

            const loadingElement =
                document.getElementById(
                    'issuerAssertionInfiniteLoading'
                );

            const endElement =
                document.getElementById(
                    'issuerAssertionInfiniteEnd'
                );

            const errorElement =
                document.getElementById(
                    'issuerAssertionInfiniteError'
                );

            const retryButton =
                document.getElementById(
                    'issuerAssertionInfiniteRetry'
                );

            const sentinel =
                document.getElementById(
                    'issuerAssertionInfiniteSentinel'
                );

            const selectionBar =
                document.getElementById(
                    'issuerAssertionSelectionBar'
                );

            const resetSelectionButton =
                document.getElementById(
                    'issuerAssertionResetSelection'
                );

            const multipleSendButton =
                document.getElementById(
                    'issuerAssertionMultipleSend'
                );

            const multipleForm =
                document.getElementById(
                    'issuerAssertionMultipleForm'
                );

            const multipleInput =
                document.getElementById(
                    'issuerAssertionMultipleInput'
                );



            let nextUrl = "{{ route('issuer.assertions.infinite.load') }}";

            let isLoading = false;

            let finished = false;



            // Loading assertions

            async function loadIssuerAssertions() {

                // Watching conditions for loading

                if (
                    isLoading ||
                    finished ||
                    !nextUrl
                ) {
                    return;
                }


                isLoading = true;
                loadingElement.style.display = 'block';
                errorElement.style.display = 'none';


                try {

                    const response =
                        await fetch(
                            nextUrl, {
                                method: 'GET',

                                headers: {

                                    'Accept': 'application/json',

                                    'X-Requested-With': 'XMLHttpRequest'

                                }
                            }
                        );

                    if (!response.ok) {

                        const errorText =
                            await response.text();

                        console.error(
                            'Assertion AJAX server response:',
                            errorText
                        );

                        throw new Error(
                            'HTTP error: ' +
                            response.status
                        );
                    }

                    const data = await response.json();

                    // Inserting new rows into assertion table

                    if (data.html) {

                        assertionBody.insertAdjacentHTML(
                            'beforeend',
                            data.html
                        );

                    }


                    nextUrl = data.next_url;

                    if (!nextUrl) {

                        finished = true;
                        observer.disconnect();
                        endElement.style.display = 'block';

                    }


                } catch (error) {


                    console.error(
                        'Assertion loading error:',
                        error
                    );


                    errorElement.style.display = 'block';


                } finally {

                    loadingElement.style.display = 'none';
                    isLoading = false;

                }

            }

            // Observing sentinel

            const observer =
                new IntersectionObserver(

                    function(entries) {

                        const entry = entries[0];

                        if (
                            entry.isIntersecting &&
                            !isLoading &&
                            !finished
                        ) {

                            loadIssuerAssertions();

                        }

                    },

                    {
                        root: null,
                        rootMargin: '700px 0px 700px 0px',
                        threshold: 0

                    }

                );

            observer.observe(sentinel);


            retryButton.addEventListener(
                'click',
                function() {

                    errorElement.style.display =
                        'none';


                    loadIssuerAssertions();

                }
            );

            // Helper functions for checkboxes

            function getSelectedAssertions() {

                return Array.from(

                    document.querySelectorAll(
                        '.issuer-assertion-checkbox:checked'
                    )

                );

            }

            function updateSelectionBar() {

                const selected =
                    getSelectedAssertions();

                if (selected.length > 0) {

                    selectionBar.style.display =
                        'flex';
                } else {

                    selectionBar.style.display =
                        'none';
                }
            }

            document.addEventListener('change', function(event) {

                if (
                    event.target.classList.contains(
                        'issuer-assertion-checkbox'
                    )
                ) {
                    updateSelectionBar();
                }
            });

            resetSelectionButton.addEventListener(
                'click',
                function() {

                    const checkboxes =
                        document.querySelectorAll(
                            '.issuer-assertion-checkbox'
                        );

                    checkboxes.forEach(
                        function(checkbox) {
                            checkbox.checked =
                                false;
                        }
                    );

                    updateSelectionBar();

                }
            );

            // Multiple e-mail sending

            multipleSendButton.addEventListener('click', function() {

                const selected = getSelectedAssertions();

                if (selected.length === 0) {
                    return;
                }

                const assertionIDs =
                    selected.map(
                        function(checkbox) {
                            return checkbox.value;
                        }
                    );

                multipleInput.value = assertionIDs.join(',');
                multipleForm.submit();
            });

            // Starting with first block of assertions

            loadIssuerAssertions();


        });
    </script>

@endsection
