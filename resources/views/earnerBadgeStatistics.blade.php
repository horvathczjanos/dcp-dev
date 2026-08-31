@extends('layouts.general')

@section('title', __('adminStatistics.statistics_title'))


@section('content')
    <h2 class="section-title">
        <span>{{ __('adminStatistics.statistics_title') }}</span>
    </h2>
    <div class="container">
        <div class="alert alert-success mt-5" role="alert">
            <strong>All Issuers: </strong> {{ $statistics['allissuerNum'] }}<br />
            <strong>All Badge Classes: </strong> {{ $statistics['allbadgeclassNum'] }}<br />
            <strong>All Earners who upload at least one badge: </strong> {{ $statistics['alluploaderearnerNum'] }}<br />
            <strong>All uploaded earner Badges : </strong> {{ $statistics['alluploadedearnerbadgeNum'] }}<br />
            <strong>All Issued Assertions: </strong> {{ $statistics['allissuedassertionNum'] }}<br />
            <strong>All expiring Assertions: </strong> {{ $statistics['allexpiringassertionNum'] }}<br />
            <strong>All revoked Assertions: </strong> {{ $statistics['allrevokedassertionNum'] }}<br />
            <strong>Number of Earners: </strong> {{ $statistics['earnerNum'] }}<br />
            <strong>Maximum uploaded badge number by earner: </strong>
            {{ $statistics['maxuploadedbyearnerbadgeNum'] }}<br />
            <strong>Number of NOT DCP badges at earner section: </strong> {{ $statistics['allnotDCPbadgeNum'] }}<br />

        </div>
    </div>

    <div class="container mt-4">
        <h3 class="mb-4"> Uploaded Earner Badges </h3> {{-- Ide kerülnek AJAX-szal a badge-ek --}}
        <div id="earner-badge-list"></div>
        {{-- Betöltés kijelzése --}}
        <div id="earner-badge-loading" class="text-center my-4" style="display: none;">
            <div class="spinner-border" role="status"> <span class="visually-hidden"> Loading... </span>
            </div>
            <div class="mt-2"> Loading badges... </div>
        </div> {{-- Ha elfogytak a rekordok --}}
        <div id="earner-badge-end" class="text-center text-muted my-4" style="display: none;">
            No more badges. </div> {{-- AJAX hiba --}} <div id="earner-badge-error"
            class="alert alert-danger text-center my-4" style="display: none;"> Error while loading badges. <br />
            <button type="button" id="earner-badge-retry" class="btn btn-sm btn-secondary mt-2"> Retry </button>
        </div>
        {{-- Ezt az elemet figyeli az IntersectionObserver. Ha a böngésző ehhez közel ér, betöltjük a következő adag badge-et. --}}
        <div id="earner-badge-sentinel" style="height: 1px;"></div>
    </div>





    <h2 class="section-title"><span>
            <p class="text-center"><a href="adminMain"
                    class="btn btn-action">{{ __('adminStatistics.statistics_back') }}</a>
            </p>
        </span>
    </h2>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            /* * HTML elemek */
            const badgeList = document.getElementById('earner-badge-list');
            const loading = document.getElementById('earner-badge-loading');
            const endMessage = document.getElementById('earner-badge-end');
            const errorMessage = document.getElementById('earner-badge-error');
            const retryButton = document.getElementById('earner-badge-retry');
            const sentinel = document.getElementById(
                'earner-badge-sentinel'
                ); /* * Az első AJAX kérés URL-je. * * A controller következő oldal URL-jeit * a JSON next_url mezőben fogja visszaadni. */
            let nextUrl = "{{ route('admin.earner-statistics.badges') }}"; /* * Állapotváltozók */
            let isLoading = false;
            let finished = false; /* * Badge-ek betöltése */

            async function loadBadges() {
                /* * Ne induljon több párhuzamos kérés. */
                if (isLoading || finished || !nextUrl) {
                    return;
                }
                isLoading = true;
                loading.style.display = 'block';
                errorMessage.style.display = 'none';
                try {
                    /* * AJAX kérés */
                    const response = await fetch(nextUrl, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }); /* * HTTP hiba ellenőrzése */
                    if (!response.ok) {
                        throw new Error('HTTP error: ' + response.status);
                    } /* * JSON feldolgozása */
                    const data = await response
                        .json(); /* * A controller által elkészített * HTML hozzáfűzése a listához. */
                    if (data.html) {
                        badgeList.insertAdjacentHTML('beforeend', data.html);
                    } /* * Következő cursor URL. */
                    nextUrl = data.next_url; /* * Ha nincs következő oldal, * akkor elfogytak a badge-ek. */
                    if (!nextUrl) {
                        finished = true;
                        observer.disconnect();
                        endMessage.style.display = 'block';
                    }
                } catch (error) {
                    console.error('Badge loading error:', error);
                    errorMessage.style.display = 'block';
                } finally {
                    loading.style.display = 'none';
                    isLoading = false;
                }
            } /* * IntersectionObserver * * Nem várjuk meg, amíg a felhasználó * ténylegesen eléri a lista végét. * * Már 600 pixellel előtte elindítjuk * a következő badge-csomag betöltését. */
            const observer = new IntersectionObserver(function(entries) {
                const entry = entries[0];
                if (entry.isIntersecting && !isLoading && !finished) {
                    loadBadges();
                }
            }, {
                root: null,
                rootMargin: '600px 0px 600px 0px',
                threshold: 0
            });

            /* * Sentinel figyelése */
            observer.observe(sentinel); /* * Hiba esetén Retry gomb */

            retryButton.addEventListener('click', function() {
                errorMessage.style.display = 'none';
                loadBadges();
            }); /* * Első badge-csomag betöltése. * * Így maga a statisztikai oldal * nem várja meg a badge-listát. */

            loadBadges();
        });
    </script>


@endsection
