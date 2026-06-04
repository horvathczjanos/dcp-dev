@extends('layouts.general')

@section('title', __('adminSelfRegistrationUser.page_title'))


@section('content')

    <h2 class="section-title"><span>Site Settings</span></h2>

    <form method="POST" action="{{ env('APP_URL') }}/admin/setSettings" enctype="multipart/form-data">
        @csrf


        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <strong>{{ $message }}</strong>
            </div>
        @endif

        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <div class="container">

            <div class="form-check">

                @php
                    $servicemode = \App\Models\SiteSetting::where('key', 'servicemode')->first()->value;
                    //dd($seviceChecked);
                @endphp

                <input class="form-check-input" type="checkbox" id="servicemode" name="servicemode" value="1" @if ($servicemode == 'on') checked @endif>


                <label class="form-label" for="servicemode">
                    Changing Service Mode (Present mode is: {{ $servicemode }})
                </label>
            </div>

            <div class="form-text">
                You can switch DCP between service and normal mode.
            </div>

            {{-- Update button --}}

            <p class="text-center">
                <button type="submit" class="btn btn-primary" value="update">
                    Save settings &nbsp;&nbsp;<i class="bi-save"></i>
                </button>
            </p>
        </div>
    </form>

    <h2 class="section-title"><span>
            <a href="{{ env('APP_URL') }}/adminMain" class="btn btn-action">{{ __('general.back') }}</a>
        </span></h2>

@endsection