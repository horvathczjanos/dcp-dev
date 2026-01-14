@extends('layouts.general')

@section('title', __('issuerGreencertPreassertionUpload.bpau_page_title'))


@section('content')

    <h2 class="section-title"><span>{{ __('issuerGreencertPreassertionUpload.bpau_page_title') }} </span></h2>



    <!-- 2024-ben átállunk XLSX bementi állományra, ZD kérésére -->
    <form method="POST" action="{{ env('APP_URL') }}/greencertPreAssertionUploadXLSX" enctype="multipart/form-data">
        @csrf


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

        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif




        <div class="bd-callout bd-callout-info">
            <p>{{ __('issuerGreencertPreassertionUpload.bpau_file-browse_lbl') }}</p>
        </div>
        <div class="custom-file">
            <input type="file" name="file" class="custom-file-input" id="chooseFile" required>
            <label class="custom-file-label" for="chooseFile"> {{ __('issuerGreencertPreassertionUpload.bpau_file-browse') }} </label>
        </div>

        <div class="bd-callout bd-callout-info">
            <p>{{ __('issuerGreencertPreassertionUpload.bpau_btn-lbl') }}</p>
	</div>
        <p class="text-center ">
            <button type="submit" class="btn btn-primary" value="upload">
	    {{ __('issuerGreencertPreassertionUpload.bpau_btn') }}&nbsp;&nbsp;<i class="bi-upload"></i>
	    </button>
        </p>
        </div>

    </form>

    <h2 class="section-title"><span>
            <p class="text-center"><a href="{{ env('APP_URL') }}/backtocompany" class="btn btn-action">{{ __('issuerGreencertPreassertionUpload.bpau_back-2-inst') }}</a></p>
        </span></h2>

@endsection
