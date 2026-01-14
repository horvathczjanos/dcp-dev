@extends('layouts.general')

@section('title', 'TEMP')

@section('content')

    <h1>TEMPORARY</h1>

    @if (auth()->check())
        <p>Hello: {{ Auth::user()->name }} {{ in_array('issuer', Auth::user()->roles) }} </p>
    @else
        <p>Hello, Anonymous!</p>
    @endif

@endsection
