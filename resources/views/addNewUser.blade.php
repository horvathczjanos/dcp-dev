@extends('layouts.general')

@section('title', __('adminNewUser.add_newuser_title'))

@section('content')

    <h2 class="section-title"><span>{{ __('adminNewUser.add_newuser_title') }}</span></h2>

    <form method="POST" action="addNewUser" enctype="multipart/form-data">
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
        <div class="bd-callout bd-callout-info">
            <p>{{ __('adminNewUser.add_newuser_name-lbl') }}</p>
        </div>
        <div class="mb-3">
            <label for="user_name_text" class="form-label">{{ __('adminNewUser.add_newuser_name') }}</label>
            <textarea style="resize: none;" name="user_name_text" class="form-control" id="user_name_text" rows="1" required></textarea>
        </div>

        {{-- email of User --}}
        <div class="bd-callout bd-callout-info">
            <p>{{ __('adminNewUser.add_newuser_email-lbl') }}</p>
        </div>
        <div class="mb-3">
            <label for="user_email_text" class="form-label">{{ __('adminNewUser.add_newuser_email') }}</label>
            <textarea style="resize: none;" name="user_email_text" class="form-control" id="user_email_text" rows="1" required></textarea>
        </div>

        {{-- username of User --}}
        <div class="bd-callout bd-callout-info">
            <p>{{ __('adminNewUser.add_newuser_username-lbl') }}</p>
        </div>
        <div class="mb-3">
            <label for="user_email_text" class="form-label">{{ __('adminNewUser.add_newuser_username') }}</label>
            <textarea style="resize: none;" name="user_username_text" class="form-control" id="user_username_text" rows="1" required></textarea>
        </div>

        {{-- password of User --}}
        <div class="bd-callout bd-callout-info">
            <p>{{ __('adminNewUser.add_newuser_password-lbl') }}</p>
        </div>
        <div class="mb-3">
            <label for="user_email_text" class="form-label">{{ __('adminNewUser.add_newuser_password') }}</label>
            <textarea style="resize: none;" name="user_password_text" class="form-control" id="user_password_text" rows="1"
                required></textarea>
        </div>

	<div class="bd-callout bd-callout-info">
            <p>{{ __('adminNewUser.add_newuser_btn-lbl') }}</p>
	</div>
            <p class="text-center">
                <button type="submit" class="btn btn-primary" value="create">
		{{ __('adminNewUser.add_newuser_button') }}&nbsp;&nbsp;<i class="bi-save"></i>
		</button>
            </p>

    </form>

    <h2 class="section-title"><span>
            <p class="text-center"><a href="./../manageUsers" class="btn btn-action">{{ __('adminNewUser.add_newuser_back-2-manus') }}</a></p>
        </span></h2>

@endsection
