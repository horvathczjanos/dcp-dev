@extends('layouts.general')

@if (auth()->check())
    <?php session()->put('loginPage', false); ?>
@endif


@section('title', __('abcPage.abc_title'))

@section('content')

    {{-- <p> <img class="rounded mx-auto d-block" src="discovet_long_header.png" width="" height="" /></p> 
    <h1>Service selector</h1> --}}


    @if (auth()->check())
        {{-- <p>Hello: {{ Auth::user()->name }} </p>
        <ul>
            @if (count(Auth::user()->roles))

                @foreach (Auth::user()->roles as $role)
                    <li>{{ $role }}</li>
                @endforeach
            @endif
        </ul> --}}

        <?php
        
        //ddd(session()->all());
        $numRoles = count(Auth::user()->roles);
        switch ($numRoles) {
            case 1:
                $col_md_x = 'col-md-12';
                break;
            case 2:
                $col_md_x = 'col-md-6';
                break;
            case 3:
                $col_md_x = 'col-md-4';
                break;
            case 4:
                $col_md_x = 'col-md-3';
                break;
            case 5:
                $col_md_x = 'col-md-2';
                break;
        }
        ?>

        <div class="section featured topspace" style="clear: right;">
            <h2 class="section-title"><span>{{ __('abcPage.roles') }}</span></h2>
            <div class="row d-flex justify-content-center">

                @if (\App\Models\SiteSetting::where('key','servicemode')->first()->value == 'on')
                SERVICE MODE is ON!
                @else
                            <!-- Earner -->
                            @if (in_array('earner', Auth::user()->roles))
                                <div class="col-sm-6 {{ $col_md_x }}">
                                    <h3 class="text-center">
                                        {{ __('abcPage.earner_name') }}</h3>
                                    <p class="text-center short-description">
                                        {{ __('abcPage.earner_short_description') }}
                                    </p>
                                    <p class="text-center"><a href="earnerMain" class="btn btn-action" data-bs-toggle="tooltip"
                                            data-bs-title="{{ __('abcPage.earner-tooltip') }}"><i
                                                class="bi-door-open"></i>&nbsp;{{ __('abcPage.enter') }}</a></p>
                                </div>
                            @endif

                            <!-- Verifier -->
                            @if (in_array('verifier', Auth::user()->roles))
                                <div class="col-sm-6 {{ $col_md_x }}">
                                    <h3 class="text-center">
                                        {{ __('abcPage.verifier_name') }}</h3>
                                    <p class="text-center short-description">
                                        {{ __('abcPage.verifier_short_description') }}
                                    </p>
                                    <p class="text-center"><a href="verifierMain" class="btn btn-action" data-bs-toggle="tooltip"
                                            data-bs-title="{{ __('abcPage.verifier-tooltip') }}"><i
                                                class="bi-door-open"></i>&nbsp;{{ __('abcPage.enter') }}</a></p>
                                </div>
                            @endif

                            <!-- Issuer -->
                            @if (in_array('issuer', Auth::user()->roles))
                                <div class="col-sm-6 {{ $col_md_x }}">
                                    <h3 class="text-center">
                                        {{ __('abcPage.issuer_name') }}</h3>
                                    <p class="text-center short-description">
                                        {{ __('abcPage.issuer_short_description') }}
                                    </p>
                                    <p class="text-center"><a href="issuerMain" class="btn btn-action" data-bs-toggle="tooltip"
                                            data-bs-title="{{ __('abcPage.issuer-tooltip') }}"><i
                                                class="bi-door-open"></i>&nbsp;{{ __('abcPage.enter') }}</a></p>
                                </div>
                            @endif

                            <!-- Assistant -->
                            @if (in_array('assistant', Auth::user()->roles))
                                <div class="col-sm-6 {{ $col_md_x }}">
                                    <h3 class="text-center">
                                        {{ __('abcPage.assistant_name') }}</h3>
                                    <p class="text-center short-description">
                                        {{ __('abcPage.assistant_short_description') }}
                                    </p>
                                    <p class="text-center"><a href="assistantMain" class="btn btn-action" data-bs-toggle="tooltip"
                                            data-bs-title="{{ __('abcPage.assistant-tooltip') }}"><i
                                                class="bi-door-open"></i>&nbsp;{{ __('abcPage.enter') }}</a></p>
                                </div>
                            @endif

                @endif



                <!-- Admin -->
                @if (in_array('admin', Auth::user()->roles))
                    <div class="col-sm-6 {{ $col_md_x }}">
                        <h3 class="text-center">
                            {{ __('abcPage.admin_name') }}</h3>
                        <p class="text-center short-description">
                            {{ __('abcPage.admin_short_description') }}
                        </p>
                        <p class="text-center"><a href="adminMain" class="btn btn-action" data-bs-toggle="tooltip"
                                data-bs-title="{{ __('abcPage.admin-tooltip') }}"><i
                                    class="bi-door-open"></i>&nbsp;{{ __('abcPage.enter') }}</a></p>
                    </div>
                @endif

            </div>
            <h2 class="section-title"><span>
                    <p class="text-center"><a href="logout" class="btn btn-action">{{ __('abcPage.logout') }}</a></p>
                </span></h2>
        @else
            <!-- <p> <img class="rounded mx-auto d-block" src="discovet_long_header.png" width="" height="" /></p> -->
            <h1 style="padding-top: 1em;">{{ __('abcPage.greetings1') }}</h1>
            <p>{!! __('abcPage.greetings2') !!}</p>
            {{-- <p class="alert text-center alert-success"><strong>{{ __('abcPage.contact') }}</strong></p> --}}
            <h2 class="section-title"><span>
                    <p class="text-center"><a href="login"
                            class="btn btn-action">{{ __('abcPage.login') }}</a>&nbsp;&nbsp;&nbsp;<a
                            href="{{ env('APP_URL') }}/users/selfRegistration"
                            class="btn btn-action">{{ __('abcPage.self_registration') }}</a></p>
                </span></h2>
    @endif
    </div> <!-- / section -->



@endsection
