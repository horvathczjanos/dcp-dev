@extends('layouts.general')

@section('title', __('hello.add_verifierbadge_title'))


@section('content')

    <p class="text-center fs-1"> <strong>{{ __('hello.add_verifierbadge_title') }} </strong></p>

    <form method="POST" action="addVerifierBadge" enctype="multipart/form-data">
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
            <div class="alert alert-warning mt-5" role="alert">
                <p class="text-center"><b>{{ __('hello.verifieradge_description') }}</b> </p>
            </div>
        </div>
        <div class="mb-3">
            <label for="verifierbadge_description" class="form-label">Description</label>
            <textarea name="verifierbadge_description" class="form-control" id="verifierbadge_description" rows="3"
                required></textarea>
        </div>


        <div class="container">
            <div class="alert alert-warning mt-5" role="alert">
                <p class="text-center"><b>{{ __('hello.set_verifierbadge_file') }}</b> </p>
            </div>
        </div>
        <div class="custom-file text-center">
            <input type="file" name="file" class="custom-file-input" id="chooseFile">
            <label class="custom-file-label" for="chooseFile"> {{ __('hello.do_badge_instruction') }} </label>
        </div>



        <div class="container">
            <div class="alert alert-warning mt-5" role="alert">
                <p class="text-center"><b>{{ __('hello.add_verifierbadge_info') }}</b></p>
                <p class="text-center ">
                    <input type="submit" class="btn btn-success" value="{{ __('hello.add_verifierbadge_button') }}" />
                </p>
            </div>
        </div>

    </form>

    <h2 class="section-title"><span>
            <p class="text-center"><a href="./../verifierMain" class="btn btn-action">Back to Verifier main page</a></p>
        </span></h2>

@endsection
