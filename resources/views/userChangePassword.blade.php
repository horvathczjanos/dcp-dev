@extends('layouts.general')

@section('title', __('changePassword.chp_page-title'))


@section('content')

    <h2 class="section-title"><span>{{ __('changePassword.chp_page-title') }}</span></h2>

    <form method="POST" action="{{ env('APP_URL') }}/users/changePassword" enctype="multipart/form-data">
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


        {{-- Name of User --}}
        <div class="mb-3">
            <label for="user_name_text" class="form-label">{{ __('changePassword.chp_name-lbl') }}</label>
            <textarea style="resize: none;" name="user_name_text" class="form-control" id="user_name_text" rows="1" disabled>{{ $user->name }}</textarea>
        </div>
        {{-- Email of User --}}
        <div class="mb-3">
            <label for="user_email_text" class="form-label">{{ __('changePassword.chp_email-lbl') }}</label>
            <textarea style="resize: none;" name="user_email_text" class="form-control" id="user_email_text" rows="1" disabled>{{ $user->email }}</textarea>
        </div>

        {{-- New password fields --}}
        <div class="bd-callout bd-callout-info">
            <p>{{ __('changePassword.chp_newpwd-info') }}</p>
        </div>
        <div class="mb-3">
            <label for="user_password_text01" class="form-label">{{ __('changePassword.chp_newpwd-lbl') }}</label>
            <textarea style="resize: none;" name="user_password_text01" class="form-control" id="user_password_text01" rows="1" required></textarea>
        </div>

        {{-- Update button --}}
        <div class="bd-callout bd-callout-info">
            <p>{{ __('changePassword.chp_btn-info') }}</p>
        </div>
        <p class="text-center">
            <button type="submit" class="btn btn-primary" value="update">
		{{ __('changePassword.chp_btn') }}&nbsp;&nbsp;<i class="bi-check-circle-fill"></i>
	    </button>
        </p>
    </div>
    </form>

    <h2 class="section-title"><span>
            <p class="text-center"><a href="{{ env('APP_URL') }}/users/editProfile" class="btn btn-action">{{ __('changePassword.chp_back-2-upp') }}</a></p>
        </span></h2>


@endsection
