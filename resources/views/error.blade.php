@extends('layouts.general')

@section('title', 'General Error Page')

@section('content')



<div class="row section featured topspace">
    <h2 class="section-title"><span>General Error Page</span></h2>

    <div class="col-sm-6 col-md-3 mx-auto">
        <h3 class="text-center">Go Home Page</h3>
        <p>This is a landing page when an error has occured in somewhere in DCP!
            &nbsp;
            &nbsp;
        </p>
        <p class="text-center"><a href="home" class="btn btn-action">Back to the Main Page!</a></p>
    </div>


</div> <!-- / section -->


<p> <img class="rounded mx-auto d-block" src="discovet_logo.png" width="50" height="50" /></p>

@endsection