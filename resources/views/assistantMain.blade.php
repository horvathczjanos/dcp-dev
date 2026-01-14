@extends('layouts.general')

@section('title', __('assistantManageInst.manage_inst_title'))

@section('content')
    <h2 class="section-title"><span>{{ __('assistantManageInst.manage_inst_title') }}</span></h2>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">{{ __('assistantManageInst.manage_inst_sum-th-name') }}</th>
                <th scope="col">{{ __('assistantManageInst.manage_inst_sum-th-email') }}</th>
                <th scope="col">{{ __('assistantManageInst.manage_inst_sum-th-logo') }}</th>
                <th scope="col">{{ __('assistantManageInst.manage_inst_sum-th-functions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($companies as $company)
                <tr>
                    <td scope="row">{{ $loop->index + 1 }}</td>

                    <td><a href="{{ $company->url }}" target="_blank">{{ $company->name }}</a></td>
                    <td>{{ $company->email }}</td>
                    <?php $imageData64 = base64_encode(Storage::get('public/images/' . $company->logofile));
                    $ext = pathinfo(storage_path() . '/public/images/' . $company->logofile, PATHINFO_EXTENSION);
                    
                    ?>
                    <td scope="col"><img src="data:image/{{ $ext }};base64,{{ $imageData64 }}"
                            style="width: 50px;"></td>
                    <td><a class="btn btn-primary" href="assistantcompany/{{ $company->issuerID }}" role="button">{{ __('assistantManageInst.manage_inst_btn-manage') }}&nbsp;&nbsp;<i class="bi-pencil"></i></a></td>
                </tr>
            @endforeach

        </tbody>
    </table>

    <h2 class="section-title"><span>
            <p class="text-center"><a href="home" class="btn btn-action">{{ __('assistantManageInst.manage_inst_back') }}</a></p>
        </span></h2>
@endsection
