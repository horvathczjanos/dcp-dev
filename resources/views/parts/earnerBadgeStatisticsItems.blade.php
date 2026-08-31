@foreach ($badges as $badge)
    <div class="row earner-badge-item">

        <div class="col-4">

            <p>
                {{ $badge->earner_name }}
            </p>

        </div>


        <div class="col-8">

            <p>

                <a href="{{ url('/earnerbadges/' . $badge->earnerbadgeID . '/info') }}">

                    <img src="{{ url('/earnerbadges/' . $badge->earnerbadgeID . '/badge') }}" width="75" loading="lazy"
                        decoding="async" alt="" />

                </a>

                <br />

                {{ $badge->badge_name }}

                <br />


                <strong>
                    {{ __('adminStatistics.statistics_upload-at') }}:
                </strong>

                {{ $badge->created_at }}

                <br />


                <strong>
                    {{ __('adminStatistics.statistics_badge-id') }}:
                </strong>

                {{ $badge->earnerbadgeID }}

            </p>

        </div>

    </div>

    <hr />
@endforeach
