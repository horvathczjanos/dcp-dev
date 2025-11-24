@extends('layouts.general')

@section('title', 'Issuer Metabadge Rules')

@section('content')

    <h1>Add NEW Issuer MEtabadge Rules</h1>

    @if ($ruleContainer->count() == 0)
        <p><strong>You have not got rules for metabadge assertions!</strong></p>
    @else
        <p><strong>You have some rules for metabadge assertions!</strong></p>
        @php
            echo '<div><strong>' . 'Issuer: ' . '</strong>' . $issuerName . '</div>';
            echo '<div><strong>' . 'Number of rules: ' . '</strong>' . $ruleContainer->count() . '</div>';

            foreach ($ruleContainer as $key => $rule) {
                $ruleContent = json_decode($rule->rulecontent, true);

                echo '<div><strong>' .
                    'Name of rule: ' .
                    '</strong>' .
                    $ruleContent['name'] .
                    ' (' .
                    $ruleContent['rule'] .
                    ') ' .
                    '</div>';
            }

        @endphp
    @endif

    <hr />

    <div class="metacontainer">
        <form method="POST" action="addNewIssuerMetabadge" enctype="multipart/form-data">
            @csrf

            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Select</th>
                        <th scope="col">Rule name</th>
                        <th scope="col">Short description</th>

                    </tr>
                </thead>
                <tr>
                    <td scope="col">1</td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input rulelist" type="radio" name="metabadgerule" id="1"
                                value="1">
                        </div>
                    </td>
                    <td>ExactlyMore</td>
                    <td>It is necessary to obtain at least a certain amount of the selected badge, but it can be more.</td>
                </tr>

                <tr>
                    <td scope="col">2</td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input rulelist" type="radio" name="metabadgerule" id="2"
                                value="2">
                        </div>
                    </td>
                    <td>ExactlyOne</td>
                    <td>It is necessary to obtain an exact quantity of the selected badge.</td>
                </tr>

                <tr>
                    <td scope="col">3</td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input rulelist" type="radio" name="metabadgerule" id="3"
                                value="3">
                        </div>
                    </td>
                    <td>AtLeastMore</td>
                    <td>It is necessary to obtain a precisely specified quantity of various badges.</td>
                </tr>


                <tr>
                    <td scope="col">4</td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input rulelist" type="radio" name="metabadgerule" id="4"
                                value="4">
                        </div>
                    </td>
                    <td>AtLeastOne</td>
                    <td>It is necessary to obtain at least one from set of various badges specified by precisely quantities.
                    </td>
                </tr>



                <tbody>

                </tbody>
            </table>

            {{-- Name of Metabadge rule --}}
            <div class="bd-callout bd-callout-info">
                <p>Add name to new Metabadge Assertion rule!</p>
            </div>
            <div class="mb-3">
                <label for="metabadgerule_name" class="form-label" required>Add a name!</label>
                <textarea style="resize: none;" name="metabadgerule_name" class="form-control" id="metabadgerule_name" rows="1"
                    required></textarea>
            </div>

            {{-- Description of Metabadge rule --}}
            <div class="bd-callout bd-callout-info">
                <p>Add description to new Metabadge Assertion rule!</p>
            </div>
            <div class="mb-3">
                <label for="metabadgerule_description" class="form-label">Description for Metabadge Assertion rule</label>
                <textarea name="metabadgerule_description" class="form-control" id="metabadgerule_description" rows="3"></textarea>
            </div>


            {{-- Rule settings --}}
            {{-- ThisMuchOrMore --}}
            <div class="ruleconfig" id="ThisMuchOrMore">
                <p>ExactlyOne</p>


                <div class="accordion" id="ThisMuchOrMore">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="ThisMuchOrMore01">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                #1 Select Base Badge
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="ThisMuchOrMore01"
                            data-bs-parent="#ThisMuchOrMore">
                            <div class="accordion-body">

                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Select</th>
                                            <th scope="col">Image</th>
                                            <th scope="col">Badgeclass short description</th>
                                            <th scope="col">Quantity</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($badgeclasslist as $item)
                                            <tr>
                                                <td scope="col">{{ $loop->index + 1 }}</td>
                                                <td scope="col">
                                                    <div class="checkforrule01">
                                                        <input class="checkforrule01-input badgec" type="radio"
                                                            name="badgeclass[]" id="{{ $item->badgeclassID }}"
                                                            value="{{ $item->badgeclassID }}">
                                                    </div>

                                                </td>
                                                <td scope="col"><a href="/issuerbadges/{{ $item->badgeclassID }}/info"
                                                        target="_blank" rel="noopener noreferrer"><img
                                                            src="{{ env('APP_URL') }}/badgeclasses/{{ $item->badgeclassID }}/image"
                                                            width="100px" alt="badge image"></a></td>
                                                <td scope="col">
                                                    {{ $item->name }}<br />{{ $item->created_at }}<br />{{ $item->description }}
                                                </td>
                                                <td scope="col" class="quantity">
                                                    <div data-mdb-input-init class="form-outline">
                                                        <input type="number" id="{{ $item->badgeclassID }}"
                                                            class="form-control subjectbadgequantity" name="quantity[]" />

                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>


                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="ThisMuchOrMore02">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                #2 Select Metabadge Badgeclass
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="ThisMuchOrMore02"
                            data-bs-parent="#ThisMuchOrMore">
                            <div class="accordion-body">


                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Select</th>
                                            <th scope="col">Image</th>
                                            <th scope="col">Badgeclass short description</th>


                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($badgeclasslist as $item)
                                            @if (!$item->has_assertions && !$item->is_in_metabadgerule)
                                                <tr>
                                                    <td scope="col">{{ $loop->index + 1 }}</td>
                                                    <td scope="col">
                                                        <div class="checkforrule01meta">
                                                            <input class="checkforrule01meta-input metab" type="radio"
                                                                name="metabadgeclass" id="{{ $item->badgeclassID }}"
                                                                value="{{ $item->badgeclassID }}">
                                                        </div>

                                                    </td>
                                                    <td scope="col"><a
                                                            href="/issuerbadges/{{ $item->badgeclassID }}/info"
                                                            target="_blank" rel="noopener noreferrer"><img
                                                                src="{{ env('APP_URL') }}/badgeclasses/{{ $item->badgeclassID }}/image"
                                                                width="100px" alt="badge image"></a></td>
                                                    <td scope="col">
                                                        {{ $item->name }}<br />{{ $item->created_at }}<br />{{ $item->description }}
                                                    </td>

                                                </tr>
                                            @endif
                                        @endforeach

                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="ThisMuchOrMore03">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                #3 Select Stop Badgeclass(es)
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="ThisMuchOrMore03"
                            data-bs-parent="#ThisMuchOrMore">
                            <div class="accordion-body">


                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Select</th>
                                            <th scope="col">Image</th>
                                            <th scope="col">Badgeclass short description</th>


                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($badgeclasslist as $item)
                                            <tr>
                                                <td scope="col">{{ $loop->index + 1 }}</td>
                                                <td scope="col">
                                                    <div class="checkforrule01stop">
                                                        <input class="checkforrule01stop-input stopb" type="checkbox"
                                                            name="stopbadgeclass[]" id="{{ $item->badgeclassID }}"
                                                            value="{{ $item->badgeclassID }}">
                                                    </div>

                                                </td>
                                                <td scope="col"><a href="/issuerbadges/{{ $item->badgeclassID }}/info"
                                                        target="_blank" rel="noopener noreferrer"><img
                                                            src="{{ env('APP_URL') }}/badgeclasses/{{ $item->badgeclassID }}/image"
                                                            width="100px" alt="badge image"></a></td>
                                                <td scope="col">
                                                    {{ $item->name }}<br />{{ $item->created_at }}<br />{{ $item->description }}
                                                </td>

                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>



                            </div>
                        </div>
                    </div>
                </div>



            </div>

            {{-- ExactlyAsMuch --}}
            <div class="ruleconfig" id="ExactlyAsMuch">
                <p>ExactlyMore</p>

                <div class="accordion" id="ExactlyAsMuch">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="ExactlyAsMuch01">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                #1 Select Base Badgeclass
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="ExactlyAsMuch01"
                            data-bs-parent="#ExactlyAsMuch">
                            <div class="accordion-body">

                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Select</th>
                                            <th scope="col">Image</th>
                                            <th scope="col">Badgeclass short description</th>
                                            <th scope="col">Quantity</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($badgeclasslist as $item)
                                            <tr>
                                                <td scope="col">{{ $loop->index + 1 }}</td>
                                                <td scope="col">
                                                    <div class="checkforrule01">
                                                        <input class="checkforrule02-input badgec" type="radio"
                                                            name="badgeclass[]" id="{{ $item->badgeclassID }}"
                                                            value="{{ $item->badgeclassID }}">
                                                    </div>

                                                </td>
                                                <td scope="col"><a href="/issuerbadges/{{ $item->badgeclassID }}/info"
                                                        target="_blank" rel="noopener noreferrer"><img
                                                            src="{{ env('APP_URL') }}/badgeclasses/{{ $item->badgeclassID }}/image"
                                                            width="100px" alt="badge image"></a></td>
                                                <td scope="col">
                                                    {{ $item->name }}<br />{{ $item->created_at }}<br />{{ $item->description }}
                                                </td>
                                                <td scope="col" class="quantity">
                                                    <div data-mdb-input-init class="form-outline">
                                                        <input type="number" id="{{ $item->badgeclassID }}"
                                                            class="form-control subjectbadgequantity" name="quantity[]" />

                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>


                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="ExactlyAsMuch02">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                #2 Select Metabadgeclass
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="ExactlyAsMuch02"
                            data-bs-parent="#ExactlyAsMuch">
                            <div class="accordion-body">


                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Select</th>
                                            <th scope="col">Image</th>
                                            <th scope="col">Badgeclass short description</th>


                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($badgeclasslist as $item)
                                            @if (!$item->has_assertions && !$item->is_in_metabadgerule)
                                                <tr>
                                                    <td scope="col">{{ $loop->index + 1 }}</td>
                                                    <td scope="col">
                                                        <div class="checkforrule01meta">
                                                            <input class="checkforrule02meta-input metab" type="radio"
                                                                name="metabadgeclass" id="{{ $item->badgeclassID }}"
                                                                value="{{ $item->badgeclassID }}">
                                                        </div>

                                                    </td>
                                                    <td scope="col"><a
                                                            href="/issuerbadges/{{ $item->badgeclassID }}/info"
                                                            target="_blank" rel="noopener noreferrer"><img
                                                                src="{{ env('APP_URL') }}/badgeclasses/{{ $item->badgeclassID }}/image"
                                                                width="100px" alt="badge image"></a></td>
                                                    <td scope="col">
                                                        {{ $item->name }}<br />{{ $item->created_at }}<br />{{ $item->description }}
                                                    </td>

                                                </tr>
                                            @endif
                                        @endforeach

                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="ExactlyAsMuch03">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                #3 Select Stop Badgeclass(es)
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="ExactlyAsMuch03"
                            data-bs-parent="#ExactlyAsMuch">
                            <div class="accordion-body">


                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Select</th>
                                            <th scope="col">Image</th>
                                            <th scope="col">Badgeclass short description</th>


                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($badgeclasslist as $item)
                                            <tr>
                                                <td scope="col">{{ $loop->index + 1 }}</td>
                                                <td scope="col">
                                                    <div class="checkforrule01stop">
                                                        <input class="checkforrule02stop-input stopb" type="checkbox"
                                                            name="stopbadgeclass[]" id="{{ $item->badgeclassID }}"
                                                            value="{{ $item->badgeclassID }}">
                                                    </div>

                                                </td>
                                                <td scope="col"><a href="/issuerbadges/{{ $item->badgeclassID }}/info"
                                                        target="_blank" rel="noopener noreferrer"><img
                                                            src="{{ env('APP_URL') }}/badgeclasses/{{ $item->badgeclassID }}/image"
                                                            width="100px" alt="badge image"></a></td>
                                                <td scope="col">
                                                    {{ $item->name }}<br />{{ $item->created_at }}<br />{{ $item->description }}
                                                </td>

                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>


                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- ThisAndThatMuch --}}
            <div class="ruleconfig" id="ThisAndThatMuch">
                <p>AtLeastOne</p>

                <div class="accordion" id="ThisAndThatMuch">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="ThisAndThatMuch01">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                #1 Select Base Badgeclass
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show"
                            aria-labelledby="ThisAndThatMuch01" data-bs-parent="#ThisAndThatMuch">
                            <div class="accordion-body">

                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Select</th>
                                            <th scope="col">Image</th>
                                            <th scope="col">Badgeclass short description</th>
                                            <th scope="col">Quantity</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($badgeclasslist as $item)
                                            <tr>
                                                <td scope="col">{{ $loop->index + 1 }}</td>
                                                <td scope="col">
                                                    <div class="checkforrule01">
                                                        <input class="checkforrule03-input badgec" type="checkbox"
                                                            name="badgeclass[]" id="{{ $item->badgeclassID }}"
                                                            value="{{ $item->badgeclassID }}">
                                                    </div>

                                                </td>
                                                <td scope="col"><a href="/issuerbadges/{{ $item->badgeclassID }}/info"
                                                        target="_blank" rel="noopener noreferrer"><img
                                                            src="{{ env('APP_URL') }}/badgeclasses/{{ $item->badgeclassID }}/image"
                                                            width="100px" alt="badge image"></a></td>
                                                <td scope="col">
                                                    {{ $item->name }}<br />{{ $item->created_at }}<br />{{ $item->description }}
                                                </td>
                                                <td scope="col" class="quantity">
                                                    <div data-mdb-input-init class="form-outline">
                                                        <input type="number" id="{{ $item->badgeclassID }}"
                                                            class="form-control subjectbadgequantity" name="quantity[]" />

                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>


                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="ThisAndThatMuch02">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                #2 Select Metabadgeclass
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="ThisAndThatMuch02"
                            data-bs-parent="#ThisAndThatMuch">
                            <div class="accordion-body">


                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Select</th>
                                            <th scope="col">Image</th>
                                            <th scope="col">Badgeclass short description</th>


                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($badgeclasslist as $item)
                                            @if (!$item->has_assertions && !$item->is_in_metabadgerule)
                                                <tr>
                                                    <td scope="col">{{ $loop->index + 1 }}</td>
                                                    <td scope="col">
                                                        <div class="checkforrule03meta">
                                                            <input class="checkforrule03meta-input metab" type="radio"
                                                                name="metabadgeclass" id="{{ $item->badgeclassID }}"
                                                                value="{{ $item->badgeclassID }}">
                                                        </div>

                                                    </td>
                                                    <td scope="col"><a
                                                            href="/issuerbadges/{{ $item->badgeclassID }}/info"
                                                            target="_blank" rel="noopener noreferrer"><img
                                                                src="{{ env('APP_URL') }}/badgeclasses/{{ $item->badgeclassID }}/image"
                                                                width="100px" alt="badge image"></a></td>
                                                    <td scope="col">
                                                        {{ $item->name }}<br />{{ $item->created_at }}<br />{{ $item->description }}
                                                    </td>

                                                </tr>
                                            @endif
                                        @endforeach

                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="ThisAndThatMuch03">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                #3 Select Stop Badgeclass(es)
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="ThisAndThatMuch03"
                            data-bs-parent="#ThisAndThatMuch">
                            <div class="accordion-body">


                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Select</th>
                                            <th scope="col">Image</th>
                                            <th scope="col">Badgeclass short description</th>


                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($badgeclasslist as $item)
                                            <tr>
                                                <td scope="col">{{ $loop->index + 1 }}</td>
                                                <td scope="col">
                                                    <div class="checkforrule03stop">
                                                        <input class="checkforrule03stop-input stopb" type="checkbox"
                                                            name="stopbadgeclass[]" id="{{ $item->badgeclassID }}"
                                                            value="{{ $item->badgeclassID }}">
                                                    </div>

                                                </td>
                                                <td scope="col"><a href="/issuerbadges/{{ $item->badgeclassID }}/info"
                                                        target="_blank" rel="noopener noreferrer"><img
                                                            src="{{ env('APP_URL') }}/badgeclasses/{{ $item->badgeclassID }}/image"
                                                            width="100px" alt="badge image"></a></td>
                                                <td scope="col">
                                                    {{ $item->name }}<br />{{ $item->created_at }}<br />{{ $item->description }}
                                                </td>

                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>


                            </div>
                        </div>
                    </div>
                </div>








            </div>


            {{-- ThisOrThatMuch --}}
            <div class="ruleconfig" id="ThisOrThatMuch">
                <p>AtLeastMore</p>

                <div class="accordion" id="ThisOrThatMuch">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="ThisOrThatMuch01">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                #1 Select Base Badgeclass
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="ThisOrThatMuch01"
                            data-bs-parent="#TThisOrThatMuch">
                            <div class="accordion-body">

                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Select</th>
                                            <th scope="col">Image</th>
                                            <th scope="col">Badgeclass short description</th>
                                            <th scope="col">Quantity</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($badgeclasslist as $item)
                                            <tr>
                                                <td scope="col">{{ $loop->index + 1 }}</td>
                                                <td scope="col">
                                                    <div class="checkforrule04">
                                                        <input class="checkforrule04-input badgec" type="checkbox"
                                                            name="badgeclass[]" id="{{ $item->badgeclassID }}"
                                                            value="{{ $item->badgeclassID }}">
                                                    </div>

                                                </td>
                                                <td scope="col"><a href="/issuerbadges/{{ $item->badgeclassID }}/info"
                                                        target="_blank" rel="noopener noreferrer"><img
                                                            src="{{ env('APP_URL') }}/badgeclasses/{{ $item->badgeclassID }}/image"
                                                            width="100px" alt="badge image"></a></td>
                                                <td scope="col">
                                                    {{ $item->name }}<br />{{ $item->created_at }}<br />{{ $item->description }}
                                                </td>
                                                <td scope="col" class="quantity">
                                                    <div data-mdb-input-init class="form-outline">
                                                        <input type="number" id="{{ $item->badgeclassID }}"
                                                            class="form-control subjectbadgequantity" name="quantity[]" />

                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>


                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="ThisOrThatMuch02">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                #2 Select Metabadgeclass
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="ThisOrThatMuch02"
                            data-bs-parent="#ThisOrThatMuch">
                            <div class="accordion-body">


                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Select</th>
                                            <th scope="col">Image</th>
                                            <th scope="col">Badgeclass short description</th>


                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($badgeclasslist as $item)
                                            @if (!$item->has_assertions && !$item->is_in_metabadgerule)
                                                <tr>
                                                    <td scope="col">{{ $loop->index + 1 }}</td>
                                                    <td scope="col">
                                                        <div class="checkforrule04meta">
                                                            <input class="checkforrule04meta-input metab" type="radio"
                                                                name="metabadgeclass" id="{{ $item->badgeclassID }}"
                                                                value="{{ $item->badgeclassID }}">
                                                        </div>

                                                    </td>
                                                    <td scope="col"><a
                                                            href="/issuerbadges/{{ $item->badgeclassID }}/info"
                                                            target="_blank" rel="noopener noreferrer"><img
                                                                src="{{ env('APP_URL') }}/badgeclasses/{{ $item->badgeclassID }}/image"
                                                                width="100px" alt="badge image"></a></td>
                                                    <td scope="col">
                                                        {{ $item->name }}<br />{{ $item->created_at }}<br />{{ $item->description }}
                                                    </td>

                                                </tr>
                                            @endif
                                        @endforeach

                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="ThisOrThatMuch03">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                #3 Select Stop Badgeclass(es)
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="ThisOrThatMuch03"
                            data-bs-parent="#ThisOrThatMuch">
                            <div class="accordion-body">


                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Select</th>
                                            <th scope="col">Image</th>
                                            <th scope="col">Badgeclass short description</th>


                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($badgeclasslist as $item)
                                            <tr>
                                                <td scope="col">{{ $loop->index + 1 }}</td>
                                                <td scope="col">
                                                    <div class="checkforrule04stop">
                                                        <input class="checkforrule04stop-input stopb" type="checkbox"
                                                            name="stopbadgeclass[]" id="{{ $item->badgeclassID }}"
                                                            value="{{ $item->badgeclassID }}">
                                                    </div>

                                                </td>
                                                <td scope="col"><a
                                                        href="/issuerbadges/{{ $item->badgeclassID }}/info"
                                                        target="_blank" rel="noopener noreferrer"><img
                                                            src="{{ env('APP_URL') }}/badgeclasses/{{ $item->badgeclassID }}/image"
                                                            width="100px" alt="badge image"></a></td>
                                                <td scope="col">
                                                    {{ $item->name }}<br />{{ $item->created_at }}<br />{{ $item->description }}
                                                </td>

                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>


                            </div>
                        </div>
                    </div>
                </div>

            </div>




            <div class="bd-callout bd-callout-info">
                <p>Press the ADD button to record a new metabadge assertion rule</p>
            </div>
            <p class="text-center ">
                <button type="submit" class="btn btn-primary" value="create">
                    ADD&nbsp;&nbsp;<i class="bi-save"></i>
                </button>
            </p>




            <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
                crossorigin="anonymous"></script>

            <script type="application/javascript">
                $(function() {
                    //alert("hello");

                    let ruleArray = ['...','ThisMuchOrMore','ExactlyAsMuch','ThisAndThatMuch','ThisOrThatMuch'];

                    function isNumeric(value) {
                        return /^\d+$/.test(value);
                    }


                    $('div.ruleconfig').hide();

                    $('.form-control.subjectbadgequantity').hide();

                    $('.form-check-input.rulelist').change(function() {

                        //alert($(this).attr('id'));
                        //alert(ruleArray[$(this).attr('id')]);
                        $('div.ruleconfig').hide();
                        $('div.ruleconfig#' + ruleArray[$(this).attr('id')]).show();
                        //$('div.typefields#bt_001').show();

                        $('.badgec').prop('checked', false);
                        $('.metab').prop('checked', false);
                        $('.stopb').prop('checked', false);

                    });

                    $('.checkforrule01meta-input.metab').change(function() {

                        //alert($(this).attr('id').substring(7));
                        $('.stopb').prop('checked', false);
                        $('.checkforrule01stop-input.stopb[id*='+$(this).attr('id').substring(7)+']').prop('checked', true);

                    
                    });

                    $('.checkforrule02meta-input.metab').change(function() {

                        
                        $('.stopb').prop('checked', false);
                        $('.checkforrule02stop-input.stopb[id*='+$(this).attr('id').substring(7)+']').prop('checked', true);

                    
                    });


                    $('.checkforrule03meta-input.metab').change(function() {

                        //alert($(this).attr('id').substring(7));
                        $('.stopb').prop('checked', false);
                        $('.checkforrule03stop-input.stopb[id*='+$(this).attr('id').substring(7)+']').prop('checked', true);

                    
                    });


                    $('.checkforrule04meta-input.metab').change(function() {

                        //alert($(this).attr('id').substring(7));
                        $('.stopb').prop('checked', false);
                        $('.checkforrule04stop-input.stopb[id*='+$(this).attr('id').substring(7)+']').prop('checked', true);

                    
                    });

                    $('.checkforrule01-input').change(function() {

                        //alert($(this).attr('id'));
                        $('.form-control.subjectbadgequantity').hide();
                        $('.form-control.subjectbadgequantity').val('');
                        $(this).parent().parent().parent().children('td.quantity').children('div').children('input').show(); 
                        
                     
                    
                    });



                    $('.checkforrule02-input').change(function() {

                        //alert($(this).attr('id'));
                        $('.form-control.subjectbadgequantity').hide();
                        $('.form-control.subjectbadgequantity').val('');
                        $(this).parent().parent().parent().children('td.quantity').children('div').children('input').show(); 
                        
                   
                    
                    });

                    $('.checkforrule03-input').change(function() {

                        if($(this).prop('checked')){
                            $(this).parent().parent().parent().children('td.quantity').children('div').children('input').show();
                        }else{
                            $(this).parent().parent().parent().children('td.quantity').children('div').children('input').val('');
                            $(this).parent().parent().parent().children('td.quantity').children('div').children('input').hide();
                        }
                        //$('.form-control.subjectbadgequantity').hide();
                        //$('.form-control.subjectbadgequantity').val('');
                        //$(this).parent().parent().parent().children('td.quantity').children('div').children('input').show(); 
                     
                    });


                    $('.checkforrule04-input').change(function() {

                        if($(this).prop('checked')){
                            $(this).parent().parent().parent().children('td.quantity').children('div').children('input').show();
                        }else{
                            $(this).parent().parent().parent().children('td.quantity').children('div').children('input').val('');
                            $(this).parent().parent().parent().children('td.quantity').children('div').children('input').hide();
                        }
                        
                    });


                    $('.form-control.subjectbadgequantity').change(function(event) {

                        event.preventDefault();

                        if(!isNumeric($(this).val())){
                            $(this).val('');
                          }else{
                            if ($(this).val() == 0){
                                $(this).val('');
                            }
                          }             
                    });

                    $('.form-control.subjectbadgequantity').keyup(function(event) {
                        
                        event.preventDefault();
                        
                        if(!isNumeric($(this).val())){
                            $(this).val('');
                          }
                    });

                });
            </script>


        </form>
    </div> {{-- End of div.metacontainer --}}






    <h2 class="section-title"><span>
            <p class="text-center"><a href="{{ env('APP_URL') }}/backtocompany"
                    class="btn btn-action">{{ __('issuerNewPreAssertion.npa_back-2-inst') }}</a></p>
        </span>
    </h2>

@endsection
