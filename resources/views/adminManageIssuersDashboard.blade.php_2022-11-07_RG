@extends('layouts.general')

@section('title', __('adminManageissuers.admin_manageissuers_title'))


@section('content')
    <h2 class="section-title"><span>{{ __('adminManageissuers.admin_manageissuers_title') }}</span></h2>

    <div class="container">
        <div class="alert alert-success mt-5" role="alert">
            <p class="text-center"><strong>{{ __('adminManageissuers.admin_manageissuers_num-o-issuers') }}:</strong> {{ App\Models\Issuer::count() }}</p>
	    <p><a class="btn btn-primary" href="./../addNewIssuer">{{ __('adminManageissuers.admin_manageissuers_add-new-btn') }}&nbsp;&nbsp;<i class="bi-hospital-fill"></i><i class="bi-plus"></i></a></p>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">{{ __('adminManageissuers.admin_manageissuers_sum-th-name') }}</th>
                <th scope="col">{{ __('adminManageissuers.admin_manageissuers_sum-th-email') }}</th>
                <th scope="col">{{ __('adminManageissuers.admin_manageissuers_sum-th-logo') }}</th>
                <th scope="col">{{ __('adminManageissuers.admin_manageissuers_sum-th-functions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach (App\Models\Issuer::get() as $issuer)
                <tr>
                    <td scope="col">{{ $loop->index + 1 }}</td>
                    <td scope="col"><a href="{{ $issuer->url }}" target="_blank">{{ $issuer->name }}</a></td>
                    <td scope="col">{{ $issuer->email }}</td>
                    <?php $imageData64 = base64_encode(Storage::get('public/images/' . $issuer->logofile));
                    $ext = pathinfo(storage_path() . '/public/images/' . $issuer->logofile, PATHINFO_EXTENSION);
                    
                    ?>
                    <td scope="col"><img src="data:image/{{ $ext }};base64,{{ $imageData64 }}" style="width: 50px;"></td>
                    <td scope="col">
                        <a class="btn btn-primary" href="issuers/{{ $issuer['issuerID'] }}/edit" role="button">{{ __('adminManageissuers.admin_manageissuers_btn-edit') }}&nbsp;&nbsp;<i class="bi-pencil"></i></a>
                        <a class="btn btn-primary" href="issuers/{{ $issuer['issuerID'] }}/issuedbadges" role="button">{{ __('adminManageissuers.admin_manageissuers_btn-ib') }}&nbsp;&nbsp;<i class="bi-award-fill"></i></a>
                    </td>
                </tr>
            @endforeach


        </tbody>
    </table>

    <h2 class="section-title"><span>
            <p class="text-center"><a href="adminMain" class="btn btn-action">{{ __('adminManageissuers.admin_manageissuers_back') }}</a></p>
        </span></h2>
@endsection
