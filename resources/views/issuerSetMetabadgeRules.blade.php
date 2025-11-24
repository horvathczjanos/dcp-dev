@extends('layouts.general')

@section('title', 'Issuer Metabadge Rules')

@section('content')

    <h1>SET Issuer MEtabadge Rules</h1>

    @if ($ruleContainer->count() == 0)
        <p><strong>You have not got rules for metabadge!</strong></p>
    @else
        <form method="POST" action="recordIssuerMetabadgeSettings" enctype="multipart/form-data">
            @csrf

            @php
                echo '<div><strong>' . 'Issuer: ' . '</strong>' . $issuerName . '</div>';
                echo '<div><strong>' . 'Number of rules: ' . '</strong>' . $ruleContainer->count() . '</div>';

                //ddd($ruleContainer);

                foreach ($ruleContainer as $key => $rule) {
                    try {
                        $ruleContent = json_decode($rule->rulecontent, true);
                    } catch (Exception $e) {
                        ddd($ruleContent);
                    }

                    echo '<div class="metarule" id="' . $rule->id . '">';
                    echo '<hr />';
                    echo '<div><strong>' . 'Name of rule: ' . '</strong>' . $ruleContent['name'] . '</div>';

                    echo '<div><strong>' . 'Type of rule: ' . '</strong>' . $ruleContent['rule'] . '</div>';

                    $badgeMustHaveRule = $ruleContent['badgeMustHaveRule'];

                    $metaBadge = $badgeMustHaveRule['metaBadge'];

                    $metabadgeName = App\Models\Badgeclass::where('badgeclassID', '=', $metaBadge)
                        ->get('name')
                        ->toArray()[0]['name'];

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

                    $checked = '';
                    if ($rule->enabled == '1') {
                        $checked = 'checked';
                    }
                    echo '<div style="background-color: #FBD603;"><strong>' .
                        'Enable to run: </strong>' .
                        '<div class="enable-check">
                                <input class="enable-check-input enablethisrule" type="checkbox" value="' .
                        $rule->id .
                        '" name="chkbx[]" id="' .
                        $rule->id .
                        '" ' .
                        $checked .
                        '>
                                <label class="enable-check-label" for="flexCheckDefault">
                                    Enable or disable.
                                </label>
                                </div>' .
                        '</div>';

                    echo '<div class="deleterule" id="' .
                        $rule->id .
                        '" style="background-color: #fb7b03;"><strong>' .
                        'Delete Metabadge rule: </strong>' .
                        '<div class="delete-check">
                                <input class="delete-check-input deletethisrule" type="checkbox" value="' .
                        $rule->id .
                        '" name="chkbx_del[]" id="' .
                        $rule->id .
                        '" ' .
                        '>
                                <label class="delete-check-label" for="flexCheckDefault">
                                    Delete this rule.
                                </label>
                                </div>' .
                        '</div>';

                    echo '</div>';
                }

            @endphp

            <div class="bd-callout bd-callout-info">
                <p>Press the SAVE button to record settings of runnable metabadge rule(s)</p>
            </div>
            <p class="text-center ">
                <button type="submit" class="btn btn-primary" value="create">
                    SAVE&nbsp;&nbsp;<i class="bi-save"></i>
                </button>
            </p>
            </div>

            <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
                crossorigin="anonymous"></script>

            <script type="application/javascript">
                $(function() {
                    //alert("hello");

                    $('div.deleterule').hide();

                    $('input.enablethisrule').each(function(index){
                        if (!this.checked){

                            let id = this.id;
                            $("div.deleterule[id='"+id+"']").show();
                        }
                        
                    });

                    $('input.enablethisrule').change(function() {

                        //alert($(this).attr('id'));
                        
                        let id = $(this).attr('id');

                        if($(this).prop('checked')){
                            
                            $("div.deleterule[id='"+id+"']").children('div.delete-check').children('input').prop('checked',false);
                            $("div.deleterule[id='"+id+"']").hide();
                        }else{
                            $("div.deleterule[id='"+id+"']").show();
                        }
                   
                    
                    });


                });
            </script>


        </form>
    @endif






    <h2 class="section-title"><span>
            <p class="text-center"><a href="{{ env('APP_URL') }}/backtocompany"
                    class="btn btn-action">{{ __('issuerNewPreAssertion.npa_back-2-inst') }}</a></p>
        </span>
    </h2>

@endsection
