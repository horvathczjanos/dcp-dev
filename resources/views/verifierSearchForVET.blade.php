@extends('layouts.general')

@section('title', __('verifierSearchForVET.searchpage_title'))


@section('content')

    <h2 class="section-title"><span>{{ __('verifierSearchForVET.searchpage_title') }} </span></h2>


    <div class="container">
        <div class="alert alert-success mt-5" role="alert">
            <p class="text-center"><b>{{ __('verifierSearchForVET.lookup_earnerbadges_DCP_information') }}</b>
                {{ $earnerBadgeDCPs->count() }}
            </p>

        </div>
    </div>
    @if (!is_null($typenamesCount))
        <form method="POST" action="startSearchForVET" enctype="multipart/form-data">
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

            @foreach ($typenamesCount as $typename => $typenumber)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="{{ $typename }}" id="{{ $typename }}"
                        name="typename[]" checked>
                    <label class="form-check-label" for="flexCheckDefault">
                        <strong>{{ $typename }}</strong>({{ $typenumber }})
                    </label>

                </div>
            @endforeach


            {{-- Update button --}}
            <div class="bd-callout bd-callout-info">
                <p>{{ __('verifierSearchForVET.searchbutton_info') }}</p>
            </div>
            <p class="text-center ">
                <button type="submit" class="btn btn-primary" value="Search">
                    {{ __('verifierSearchForVET.searchbutton_info') }} &nbsp;&nbsp;<i class="bi-search"></i>
                </button>
            </p>

        </form>
    @endif
    <h2 class="section-title"><span>
            <p class="text-center"><a href="{{ env('APP_URL') }}/verifierMain"
                    class="btn btn-action">{{ __('general.back-to-verifier') }}</a></p>
        </span></h2>


@endsection
