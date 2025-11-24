@extends('layouts.general')

@section('title', __('adminEditUser.edit_user_title'))


@section('content')

    <h2 class="section-title"><span>{{ __('adminEditUser.edit_user_title') }}</span></h2>

    <form method="POST" action="./../../updateUserByAdmin" enctype="multipart/form-data">
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
            <p>{{ __('adminEditUser.edit_user_name-lbl') }}</p>
        </div>
        <div class="mb-3">
            <label for="user_name_text" class="form-label">{{ __('adminEditUser.edit_user_name') }}</label>
            <textarea style="resize: none;" name="user_name_text" class="form-control" id="user_name_text" rows="1" required>{{ $user->name }}</textarea>
        </div>

        {{-- email of User --}}
        <div class="bd-callout bd-callout-info">
            <p>{{ __('adminEditUser.edit_user_email-lbl') }}</p>
        </div>
        <div class="mb-3">
            <label for="user_email_text" class="form-label">{{ __('adminEditUser.edit_user_email') }}</label>
            <textarea style="resize: none;" name="user_email_text" class="form-control" id="user_email_text" rows="1" required>{{ $user->email }}</textarea>
        </div>

        {{-- roles of User --}}
        <div class="bd-callout bd-callout-info">
            <p>{{ __('adminEditUser.edit_user_roles-lbl') }}</p>
        </div>
        <div class="mb-3">
            <label for="user_email_text" class="form-label">{{ __('adminEditUser.edit_user_roles') }}</label>

            <?php
            $generalRoles = ['issuer', 'assistant','earner', 'verifier', 'admin'];
            ?>

                @foreach ($generalRoles as $role)
		    <?php
			switch ($role) {
			    case "issuer":
				$role_lbl = __('adminEditUser.edit_user_lbl-issuer');
				break;
			    case "assistant":
				$role_lbl = __('adminEditUser.edit_user_lbl-assistant');
				break;
			    case "earner":
				$role_lbl = __('adminEditUser.edit_user_lbl-earner');
				break;
			    case "verifier":
				$role_lbl = __('adminEditUser.edit_user_lbl-verifier');
				break;
			    case "admin":
				$role_lbl = __('adminEditUser.edit_user_lbl-admin');
				break;
			}
		    ?>
                  
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="{{ $role }}" id="{{ $role }}"
                            name="{{ $role }}" @if (in_array($role, $user->roles)) Checked @endif />
                        <label class="form-check-label" for="flexCheckChecked">{{ $role_lbl }}</label>
                    </div>
                @endforeach

        </div>


        {{-- Update button --}}
        <div class="bd-callout bd-callout-info">
            <p>{{ __('adminEditUser.edit_user_btn-lbl') }}</p>
        </div>
        <p class="text-center">
            <button type="submit" class="btn btn-primary" value="update">
	    {{ __('adminEditUser.edit_user_button') }}&nbsp;&nbsp;<i class="bi-check-circle-fill"></i>
	    </button>
        </p>

    </form>

    <h2 class="section-title"><span>
            <p class="text-center"><a href="./../../manageUsers" class="btn btn-action">{{ __('adminEditUser.edit_user_back-2-manus') }}</a></p>
        </span></h2>


@endsection
