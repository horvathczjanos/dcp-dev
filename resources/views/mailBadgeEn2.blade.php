@extends(session('controlButtons') ? 'layouts.general' : 'layouts.mail')

@section('title', __('mailBadge.title'))

@section('content')

    <div class="bd-callout bd-callout-info">
        <p>Dear <strong>{{ $details['recipientLastName'] . ' ' . $details['recipientFirstName'] }} Earner</strong>, badge
            recipient,</p>

        <p><strong>{{ $details['issuerName'] }}</strong> has issued you a digital badge titled
            "<strong>{{ $details['badgeName'] }}</strong>", which is attached to this message.</p>

        <p>You have received this badge because:</p>

        <p><i>{{ $details['badgeCriteria'] }}</i></p>

        <p>You can collect and store your digital badges on the <a href="{{ env('APP_URL') }}">DCP platform</a> (developed by
            BME), where you can generate an &lt;embed&gt; code, or a link to upload them to your LinkedIn profile.</p>

        <p>Please accept our congratulations and appreciation!</p>

        <p>The DCP Team says thank you on behalf of {{ $details['issuerName'] }} <br>
            <a href="mailto:dcp@mti.bme.hu">dcp@mti.bme.hu</a>

        </p>
        <p>&nbsp;</p>
        <p>
            <i><strong>Why is a display platform necessary for digital badges?</strong>

                When you receive a digital badge via email, it usually comes as an attached image file—a simple-looking
                picture, perhaps showing only the event title and date. However, digital badges contain much more than what
                is visible: the image file is encoded with detailed information about the event, including its context,
                performance criteria, and the type of learning outcome achieved.
                To access this embedded information, you need a display platform where the badge's full content can be
                viewed. This platform allows you to explore the details "hidden" in the image and makes it easy to share
                your achievement with colleagues or add it to your CV.
                The <a href="{{ env('APP_URL') }}">DCP platform</a>, developed and maintained by the Budapest University of
                Technology and Economics, provides
                this functionality. It is free to use and available also in Hungarian. Visit the <a
                    href="{{ env('APP_URL') }}">link</a>, register, and upload
                the .png file attached to this email—once uploaded, you’ll be able to view all the badge details. You can
                then share your badge via email, print it as a PDF, or upload it to your website.
                We’ve also created a short summary about what digital badges are and how you can collect and share them:
                <a href="https://www.mti.bme.hu/en/what-to-do-with-my-digital-badge/"
                    target="_blank">https://www.mti.bme.hu/en/what-to-do-with-my-digital-badge/</a>

            </i>
        </p>
    </div>


    @if (session('controlButtons'))
        @if (session('whatRole') == 'assistant')
            <p class="text-center ">
                <a class="btn btn-primary" href="/assertions/{{ $details['id'] }}/assistantsend3" role="button">Send byS
                    e-mail+</a>
            </p>

            <h2 class="section-title"><span>
                    <p class="text-center"><a href="{{ env('APP_URL') }}/backtoassistantcompany" class="btn btn-action">Back
                            to
                            Company
                            page</a></p>
                </span></h2>
        @else
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
    @endif
@endsection
