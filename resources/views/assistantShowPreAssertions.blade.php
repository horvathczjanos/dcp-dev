@extends('layouts.general')

@section('title', __('assistantShowPreAssertions.spa_page-title'))


@section('content')

    <h2 class="section-title"><span>{{ __('assistantShowPreAssertions.spa_page-title') }}</span></h2>

    <div class="container">
        <div class="alert alert-success mt-5" role="alert">
            <p class="text-center"><b>{{ __('assistantShowPreAssertions.spa_num-o-pa') }}:</b> {{ $numberofpreassertions }}
            </p>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">{{ __('assistantShowPreAssertions.spa_th-brief') }}</th>
                <th scope="col">{{ __('assistantShowPreAssertions.spa_th-func') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($preassertionlist as $item)
                <tr>
                    <td scope="col">{{ $loop->index + 1 }}</td>

                    <td scope="col">
                        <a href="{{ env('APP_URL') }}/badgeclasses/{{ $item['badgeclassID'] }}" target="_blank"
                            rel="noopener noreferrer"><img
                                src="{{ env('APP_URL') }}/badgeclasses/{{ $item['badgeclassID'] }}/image" width="75px"
                                alt="digital badge"></a><br />

                        <span>{{ $item->badge_name }}</span><br />
                      
                        <?php
                        $assertionType = json_decode($item['assertiontype'], true);
                        
                        $firstname = $assertionType['form'][0]['fields'][0]['content'];
                        $lastname = $assertionType['form'][0]['fields'][1]['content'];
                        ?>

                        @if (session('locale') !== 'hu')
                            <strong>{{ $firstname . ' ' . $lastname }}</strong>
                        @else
                            <strong>{{ $lastname . ' ' . $firstname }}</strong>
                        @endif

                        <br />

                        @if ($item['originalIdentity'])
                            {{ $item['originalIdentity'] }}
                        @else
                            <i>NA</i>
                        @endif <br />

                        {{ $item['issuedOn'] }}<br />

                    </td>


                    <td><a class="btn btn-primary"
                            href="{{ env('APP_URL') }}/assistantPreAssertions/{{ $item['id'] }}/edit"
                            role="button">{{ __('assistantShowPreAssertions.spa_btn-edit') }}&nbsp;&nbsp;<i
                                class="bi-pencil"></i></a>
                        {{-- <a class="btn btn-primary" href="{{ env('APP_URL') }}/preAssertions/{{ $item['id'] }}/delete"
                            role="button">Delete</a> --}}
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>

    <h2 class="section-title"><span>
            <p class="text-center"><a href="{{ env('APP_URL') }}/backtoassistantcompany"
                    class="btn btn-action">{{ __('assistantShowPreAssertions.spa_back-2-inst') }}</a></p>
        </span></h2>

@endsection
