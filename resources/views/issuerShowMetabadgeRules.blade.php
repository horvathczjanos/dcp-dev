@extends('layouts.general')

@section('title', 'Issuer Metabadge Rules')

@section('content')

    <h1>RUN Issuer MEtabadge Rules</h1>

    @if ($ruleContainer->count() == 0)
        <p><strong>You have not got rules for metabadge!</strong></p>
    @else
        @php
            echo '<div><strong>' . 'Issuer: ' . '</strong>' . $issuerName . '</div>';
            echo '<div><strong>' . 'Number of rules: ' . '</strong>' . $ruleContainer->count() . '</div>';

            foreach ($ruleContainer as $key => $rule) {
                $ruleContent = json_decode($rule->rulecontent, true);

                echo '<hr />';
                echo '<div><strong>' . 'Name of rule: ' . '</strong>' . $ruleContent['name'] . '</div>';

                echo '<div><strong>' . 'Type of rule: ' . '</strong>' . $ruleContent['rule'] . '</div>';

                $badgeMustHaveRule = $ruleContent['badgeMustHaveRule'];

                $metaBadge = $badgeMustHaveRule['metaBadge'];

                $metabadgeName = App\Models\Badgeclass::where('badgeclassID', '=', $metaBadge)
                    ->get('name')
                    ->toArray()[0]['name'];

                echo '<div><strong>' . 'MetaBagde: ' . '</strong>' . $metabadgeName . '</div>';

                echo '<div>' .
                    '<a href="' .
                    env('APP_URL') .
                    '/issuerbadges/' .
                    $metaBadge .
                    '/info" target="_blank">' .
                    '<img src="' .
                    env('APP_URL') .
                    '/badgeclasses/' .
                    $metaBadge .
                    '/image" style="width: 50px">' .
                    '</a>' .
                    '</div>';

                $badgeCount = $badgeMustHaveRule['badgeCount'];
                $subjectBadges = $badgeMustHaveRule['subjectBadge'];

                foreach ($subjectBadges as $key => $subjectBadge) {
                    $subjectBadgeName = App\Models\Badgeclass::where('badgeclassID', '=', $subjectBadge)
                        ->get('name')
                        ->toArray()[0]['name'];
                    echo '<div><strong>' .
                        '(' .
                        $badgeCount[$key] .
                        ') Conditional Badge: ' .
                        '</strong>' .
                        $subjectBadgeName .
                        '</div>';

                    echo '<div>' .
                        '<a href="' .
                        env('APP_URL') .
                        '/issuerbadges/' .
                        $subjectBadge .
                        '/info" target="_blank">' .
                        '<img src="' .
                        env('APP_URL') .
                        '/badgeclasses/' .
                        $subjectBadge .
                        '/image" style="width: 50px">' .
                        '</a>' .
                        '</div>';
                }

                foreach ($ruleContent['stopBadges'] as $key => $stopBadge) {
                    $badgeName = App\Models\Badgeclass::where('badgeclassID', '=', $stopBadge)
                        ->get('name')
                        ->toArray()[0]['name'];
                    echo '<div><strong>' . 'StopBadge: ' . '</strong>' . $badgeName . '</div>';

                    echo '<div>' .
                        '<a href="' .
                        env('APP_URL') .
                        '/issuerbadges/' .
                        $stopBadge .
                        '/info" target="_blank">' .
                        '<img src="' .
                        env('APP_URL') .
                        '/badgeclasses/' .
                        $stopBadge .
                        '/image" style="width: 50px">' .
                        '</a>' .
                        '</div>';
                }
            }

        @endphp

        <div>
            <livewire:messagebox />
        </div>
    @endif







    <h2 class="section-title"><span>
            <p class="text-center"><a href="{{ env('APP_URL') }}/backtocompany"
                    class="btn btn-action">{{ __('issuerNewPreAssertion.npa_back-2-inst') }}</a></p>
        </span>
    </h2>

@endsection
