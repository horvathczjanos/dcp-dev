@extends('layouts.general')

@section('title', __('adminManageusers.admin_manageusers_title'))


@section('content')
    <h2 class="section-title"><span>{{ __('adminManageusers.admin_manageusers_title') }}</span></h2>

    <div class="container">
        <div class="alert alert-success mt-5" role="alert">
            <p class="text-center"><strong>{{ __('adminManageusers.admin_manageusers_num-o-users') }}:</strong> {{ App\Models\User::count() }}</p>
            <p><a class="btn btn-primary" href="./../addNewUser">{{ __('adminManageusers.admin_manageusers_add-new-btn') }}&nbsp;&nbsp;<i class="bi-person-plus-fill"></i></a></p>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">{{ __('adminManageusers.admin_manageusers_sum-th-user') }}</th>
                <th scope="col">{{ __('adminManageusers.admin_manageusers_sum-th-roles') }}</th>
                <th scope="col">{{ __('adminManageusers.admin_manageusers_sum-th-functions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach (App\Models\User::get() as $user)
                <tr>
                    <td scope="col">{{ $loop->index + 1 }}</td>
                    <td scope="col">{{ $user->name }}<br />{{ $user->email }}</td>
                    <td>
                            @if (count($user->roles))
                                @foreach ($user->roles as $role)
				    <?php
				    switch ($role) {
					case "issuer":
					    $role_lbl = __('adminManageusers.admin_manageusers_lbl-issuer');
					    break;
					case "assistant":
					    $role_lbl = __('adminManageusers.admin_manageusers_lbl-assistant');
					    break;
					case "earner":
					    $role_lbl = __('adminManageusers.admin_manageusers_lbl-earner');
					    break;
					case "verifier":
					    $role_lbl = __('adminManageusers.admin_manageusers_lbl-verifier');
					    break;
					case "admin":
					    $role_lbl = __('adminManageusers.admin_manageusers_lbl-admin');
					    break;
				    }
				    ?>
                                    <span class="badge text-body bg-info">{{ $role_lbl }}</span>
                                @endforeach
                            @else
                                -
                            @endif
                    </td>
                    <td scope="col">
                        <a class="btn btn-primary" href="users/{{ $user['id'] }}/editbyadmin" role="button">{{ __('adminManageusers.admin_manageusers_btn-edit') }}&nbsp;&nbsp;<i class="bi-pencil"></i></a>
                        <a class="btn btn-primary" href="users/{{ $user['id'] }}/senddatabyadmin" role="button">{{ __('adminManageusers.admin_manageusers_btn-send-login') }}&nbsp;&nbsp;<i class="bi-envelope-paper-fill"></i></a>
                    </td>
                </tr>
            @endforeach


        </tbody>
    </table>

    <h2 class="section-title"><span>
            <p class="text-center"><a href="adminMain" class="btn btn-action">{{ __('adminManageusers.admin_manageusers_back') }}</a></p>
        </span></h2>
@endsection
