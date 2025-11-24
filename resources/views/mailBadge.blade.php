@extends((session('controlButtons') ? 'layouts.general' : 'layouts.mail'))

@section('title', __('mailBadge.title'))

@section('content')

    <div class="bd-callout bd-callout-info">
        <h1>Hi, {{ $details['originalIdentity'] }}</h1>

        <p>You got a badge in DCP: {{ $details['badgeName'] }}</p>

        <p>Thank you! </p>
        <p>Horváth Cz. János @ DCP, BME MTI </p>
    </div>


    @if (session('controlButtons'))
        <p class="text-center ">
            <a class="btn btn-primary" href="/assertions/{{ $details['id'] }}/send3" role="button">Send by
                e-mail+</a>
        </p>

        <h2 class="section-title"><span>
                <p class="text-center"><a href="{{ env('APP_URL') }}/backtocompany" class="btn btn-action">Back to
                        Company
                        page</a></p>
            </span></h2>
    @endif


@endsection
