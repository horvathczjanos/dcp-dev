@extends('layouts.general')

@section('title', __('adminDashboard.admin_dashboard_title'))


@section('content')
<h2 class="section-title"><span>{{ __('adminDashboard.admin_dashboard_title') }}</span></h2>

    <div class="container">
        <div class="alert alert-success mt-5" role="alert">
	    <div class="row d-flex justify-content-center">
		<div class="col-md-4 col-sm-6">
		    <p class="text-center"><a class="btn btn-primary" href="./../manageUsers" role="button">{{ __('adminDashboard.admin_dashboard_manageusers') }}&nbsp;&nbsp;<i class="bi-people-fill"></i></a></p>
		</div>
        	<div class="col-md-4 col-sm-6">
		    <p class="text-center"><a class="btn btn-primary" href="./../manageIssuers" role="button">{{ __('adminDashboard.admin_dashboard_manageissuers') }}&nbsp;&nbsp;<i class="bi-hospital-fill"></i></a></p>
		</div>
		<div class="col-md-4 col-sm-6">
		    <p class="text-center"><a class="btn btn-primary" href="./../earnerStatistics" role="button">{{ __('adminDashboard.admin_dashboard_statistics') }}&nbsp;&nbsp;<i class="bi-bar-chart-line-fill"></i></a></p>
		</div>
		<div class="col-md-4 col-sm-6">
		    <p class="text-center"><a class="btn btn-primary" href="./../changeSiteSettings" role="button">{{ __('adminDashboard.change_site_states') }}&nbsp;&nbsp;<i class="bi-bar-chart-line-fill"></i></a></p>
		</div>
	    </div>
        </div>
    </div>

    <h2 class="section-title"><span>
            <p class="text-center"><a href="home" class="btn btn-action">{{ __('general.back-to-home') }}</a></p>
        </span></h2>
@endsection
