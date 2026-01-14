@extends('layouts.general')

@section('title', __('hello.check_badge_title'))


@section('content')

    <p class="text-center fs-1"> <strong>{{ __('hello.check_badge_title') }} </strong></p>


    <div class="container">
        <div class="alert alert-success mt-5" role="alert">
            <p class="text-center"><b>{{ __('hello.check_badge_information') }}</b></p>
        </div>
    </div>


    <div class="container mt-5">
        <form method="POST" action="checkBadge" enctype="multipart/form-data">
            @csrf
            <p class="text-center ">Hello</p>

            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <strong>{{ $message }}</strong>
                    <br />
                    ID: {{ Session::get('id') }}
                    <br />
                    Badge info: {{ Session::get('badge') }}
                    <br />
                    Issued on: {{ Session::get('issuedOn') }}
                </div>
            @endif

            @if ($message2 = Session::get('noOB'))
                <div class="alert alert-danger">)
                    <strong>{{ $message2 }}</strong>
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


            <div class="custom-file text-center">
                <input type="file" name="file" class="custom-file-input" id="chooseFile">
                <label class="custom-file-label" for="chooseFile"> {{ __('hello.do_badge_instruction') }} </label>
            </div>

            <button type="submit" name="submit" class="btn btn-primary btn-block mt-4">
                {{ __('hello.do_badge_check_button') }}
            </button>


        </form>

    </div>

@endsection
