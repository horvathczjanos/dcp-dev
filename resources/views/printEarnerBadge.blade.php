<!DOCTYPE html>
<html>




<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
        }

        p .small {
            font-size: 6px
        }

        .tag {
            font-family: Helvetica, Arial, sans-serif;
            display: inline-block;
            color: #fff;
            position: relative;
            padding: 1px 3px 1px 3px;
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
            margin: 5px 4px 2px 0px;
            text-decoration: none;
            -webkit-transition: right 0.2s ease-in;
            -moz-transition: right 0.2s ease-in;
            -ms-transition: right 0.2s ease-in;
            background: #d63838;
        }

    </style>
</head>


<body>
    <h1>Earner badge Print page</h1>
    <div>
        <div>
            <p><strong>Badge image</strong><br />
                <img src="data:image/png;base64,{{ base64_encode($image) }}" style="width: 150px;" alt="badge image">
            </p>

            <p><img src="data:image/png;base64,{{ base64_encode($discovetImage) }}" style="width: 300px;"
                    alt="discovet image"></p>

        </div>
        <div style="color:black;font-size:6px;position:absolute;top: 110px;left:330px;width:350px;">
            <p><strong>user name</strong><br />
                <strong>{{ $printEarnerBadge['user']['name'] }}</strong>
            </p>
            <p><strong>user e-mail</strong><br />
                <i>{{ $printEarnerBadge['user']['email'] }}</i>
            </p>
            <p><strong>user memo</strong><br />
                {{ $memo }}</p>
            <hr />
            @if (count($earnerBadgeTags) > 0)
                <p><strong>user tags</strong><br />
                    @foreach ($earnerBadgeTags as $tag)
                        <span class="tag">{{ $tag }}</span>
                    @endforeach
                </p>
                <hr />
            @endif
            <p><strong>name of badge</strong><br />
                <strong>{{ $printEarnerBadge['badgeClass']['name'] }}</strong>
            </p>
            <p><strong>description of badge</strong><br />
                {{ $printEarnerBadge['badgeClass']['description'] }}</p>
            <p><strong>date of issue</strong><br />
                {{ $issuedOn }}</p>
            <hr />
            <p><strong>issuer name & e-mail</strong><br />
                {{ $printEarnerBadge['issuer']['name'] }} <br /> {{ $printEarnerBadge['issuer']['email'] }}</p>
            <p><strong>issuer image & URL</strong><br />
                {{-- <a href="{{ $printEarnerBadge['issuer']['url'] }}"><img
                        src="{{ $printEarnerBadge['issuer']['image'] }}" style="width: 150px;"
                        alt="issuer's image"></a> --}}
            </p>
        </div>
    </div>




    {{-- <p class="text-center fs-1"> <strong>{{ __('hello.print_earnerbadge_title') }} </strong> Badge from DCP </p> --}}


    <p> <img src="data:image/png;base64,{{ base64_encode($qrcode) }}" style="width: 100px;" alt="badge image"></p>
    <p style="color:black;font-size:6px;">Badge Info link: {{ $earnerBadgeURL }}</p>

    <p>*</p>

</body>

</html>
