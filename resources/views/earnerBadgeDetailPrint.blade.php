<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Badge as Print</title>
    <style>
        /*        @font-face {
            font-family: 'Rubik Light';
            src: url({{ storage_path('fonts/Rubik-Light.ttf') }} ) format('truetype');
            font-weight: 300;
            font-style: normal;
            font-variant: normal;
        }

        @font-face {
            font-family: 'Rubik Light Italic';
            src: url('/fonts/static/Rubik-LightItalic.ttf') format('truetype');
            font-weight: 300;
            font-style: italic;
            font-variant: normal;
        }
*/
        @font-face {
            font-family: 'Crimson Text Regular';
            src: url({{ storage_path('fonts/CrimsonText-Regular.ttf') }}) format('truetype');
            font-weight: 400;
            font-style: normal;
            font-variant: normal;
        }

        @font-face {
            font-family: 'Rubik Regular';
            src: url({{ storage_path('fonts/Rubik-Regular.ttf') }}) format('truetype');
            font-weight: 400;
            font-style: normal;
            font-variant: normal;
        }

        @font-face {
            font-family: 'Rubik Italic';
            src: url({{ storage_path('fonts/Rubik-Italic.ttf') }}) format('truetype');
            font-weight: 400;
            font-style: italic;
            font-variant: normal;
        }

        /*
        @font-face {
            font-family: 'Rubik Medium';
            src: url('/fonts/static/Rubik-Medium.ttf') format('truetype');
            font-weight: 500;
            font-style: normal;
            font-variant: normal;
        }

        @font-face {
            font-family: 'Rubik Medium Italic';
            src: url('/fonts/static/Rubik-MediumItalic.ttf') format('truetype');
            font-weight: 500;
            font-style: italic;
            font-variant: normal;
        }

        @font-face {
            font-family: 'Rubik SemiBold';
            src: url('/fonts/static/Rubik-SemiBold.ttf') format('truetype');
            font-weight: 600;
            font-style: normal;
            font-variant: normal;
        }

        @font-face {
            font-family: 'Rubik SemiBold Italic';
            src: url('/fonts/static/Rubik-SemiBoldItalic.ttf') format('truetype');
            font-weight: 600;
            font-style: italic;
            font-variant: normal;
        }
*/
        @font-face {
            font-family: 'Rubik Bold';
            src: url({{ storage_path('fonts/Rubik-Bold.ttf') }}) format('truetype');
            font-weight: 700;
            font-style: normal;
            font-variant: normal;
        }

        @font-face {
            font-family: 'Rubik Bold Italic';
            src: url({{ storage_path('fonts/Rubik-BoldItalic.ttf') }}) format('truetype');
            font-weight: 700;
            font-style: italic;
            font-variant: normal;
        }

        /*
       @font-face {
            font-family: 'Rubik Extra Bold';
            src: url('/fonts/static/Rubik-ExtraBold.ttf') format('truetype');
            font-weight: 800;
            font-style: normal;
            font-variant: normal;
        }

        @font-face {
            font-family: 'Rubik Extra Bold Italic';
            src: url('/fonts/static/Rubik-ExtraBoldItalic.ttf') format('truetype');
            font-weight: 800;
            font-style: italic;
            font-variant: normal;
        }

        @font-face {
            font-family: 'Rubik Black';
            src: url('/fonts/static/Rubik-Black.ttf') format('truetype');
            font-weight: 900;
            font-style: normal;
            font-variant: normal;
        }

        @font-face {
            font-family: 'Rubik Black Italic';
            src: url('/fonts/static/Rubik-BlackItalic.ttf') format('truetype');
            font-weight: 900;
            font-style: italic;
            font-variant: normal;
        }
*/

        /*-------------*/
        @page {
            margin: 0;
        }

        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 50mm;
            margin-right: 15mm;
            margin-left: 15mm;
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 35mm;
            margin-right: 15mm;
            margin-left: 15mm;
        }

        /*------------*/

        body {
            font-family: 'Rubik Regular';
            margin: 0;
            margin-top: 50mm;
            margin-bottom: 45mm;
            margin-left: 15mm;
            margin-right: 15mm;
        }

        #page {
            min-width: 320px;
            max-width: 100%;
            margin: auto;
            /*            border: 3px solid green;*/
        }

        #header,
        #footer {
            margin: auto;
            padding-left: 20px;
            padding-right: 20px;
            padding-top: 15px;
            padding-bottom: 15px;
            /*            border: 2px solid blue;*/
        }

        #header>h1 {
            font-family: 'Rubik Bold';
            font-weight: bold;
            font-size: 24pt;
        }

        #content {
            margin: auto;
            /*            border: 2px solid blue;*/
            display: table;
            width: 100%;
        }

        #badge_row {
            display: table-row;
        }

        #badge_image,
        #badge_data {
            padding-left: 15px;
            padding-right: 15px;
            padding-top: 10px;
            padding-bottom: 10px;
            /*            border: 1px solid red;*/
            width: 50%;
            display: table-cell;
        }

        #badge_image {
            width: 33%;
        }

        #badge_data {
            width: 67%;
        }

        #badge_data>p {
            padding: 0;
            margin: 0;
            padding-bottom: 0;
            font-size: 11pt;
            line-height: 1em;
        }

        #badge_data>p.param {
            font-family: 'Rubik Bold';
            font-weight: bold;
        }

        #badge_data>p.value {
            font-family: 'Rubik Italic';
            font-style: italic;
            padding-bottom: 0.5rem;
            padding-left: 2mm;
        }

        #badge_image {
            text-align: center;
            vertical-align: middle;
        }

        .tag {
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

        #footer>p {
            /*	    font-family: 'Crimson Text Regular';*/
            font-size: 8pt;
        }

        .BadgeImg {
            width: 40mm;
            max-width: 40mm;
        }

        .QRCode {
            width: 20mm;
            float: left;
        }

        .DiscoVET_logo {
            width: 100mm;
        }

        .BURL {
            /*	    word-break: break-all;*/
            overflow-wrap: break-word;
            padding-left: 25mm;
            vertical-align: bottom;
        }
    </style>
</head>

<body>
    <header>
        <div id="header">
            <p><img src="data:image/png;base64,{{ base64_encode($discovetImage) }}" class="DiscoVET_logo"
                    alt="discovet image"></p>
            <h1>Earner badge Print page</h1>
        </div>
    </header>
    <footer>
        <div id="footer"> {{-- <p> <strong>{{ __('hello.print_earnerbadge_title') }} </strong> Badge from DCP </p> --}}
            <p><a href="{{ $earnerBadgeURL }}"><img src="data:image/png;base64,{{ base64_encode($qrcode) }}"
                        alt="badge QR code" class="QRCode"></a></p>
            <p class="BURL">Badge Info URL: <a href="{{ $earnerBadgeURL }}">{{ $earnerBadgeURL }}</a></p>
        </div>
    </footer>
    <div id="page">
        <div id="content">
            <div id="badge_row">
                <div id="badge_image">
                    <!--<p class="param">Badge image</p>-->
                    <p class="value"><img src="data:image/png;base64,{{ base64_encode($image) }}" class="BadgeImg"
                            alt="badge image"> </p>
                </div>
                <div id="badge_data">
                    <p class="param">user name</p>
                    <p class="value">{{ $printEarnerBadge['user']['name'] }} </p>
                    <p class="param">user e-mail</p>
                    <p class="value">{{ $printEarnerBadge['user']['email'] }} </p>
                    <p class="param">user memo</p>
                    <p class="value">{!! Str::limit($memo, 255, ' ...') !!}</p>
                    <hr />

                    @if (count($earnerBadgeTags) > 0)
                        <p class="param">user tags</p>
                        <p class="value">
                            @foreach ($earnerBadgeTags as $tag)
                                <span class="tag">{{ $tag }}</span>
                            @endforeach
                        </p>
                        <hr />
                    @endif

                    <?php
                    if (array_key_exists('tags', $printEarnerBadge['badgeClass'])) {
                        $badgeclasstags = $printEarnerBadge['badgeClass']['tags'];
                    } else {
                        $badgeclasstags = false;
                    }
                    ?>

                    @if ($badgeclasstags)
                        <p class="param"><strong>badge tags</strong></p>
                        <p class="value">
                            @foreach ($badgeclasstags as $tag)
                                <span class="tag">{{ $tag }}</span>
                            @endforeach
                        </p>
                        <hr />
                    @endif

                    <p class="param">name of badge</p>
                    <p class="value">{{ $printEarnerBadge['badgeClass']['name'] }} </p>
                    <p class="param">description of badge</p>
                    <p class="value">{!! Str::limit($printEarnerBadge['badgeClass']['description'], 255, ' ...') !!}</p>
                    <p class="param">date of issue</p>
                    <p class="value">{{ $issuedOn }}</p>
                    <hr />
                    <p class="param">issuer name & e-mail</p>
                    <p class="value">{{ $printEarnerBadge['issuer']['name'] }} <br />
                        {{ $printEarnerBadge['issuer']['email'] }}</p>
                    <p class="param">issuer image & URL</p>
                    <p class="value">{{-- <a href="{{ $printEarnerBadge['issuer']['url'] }}"><img
                        src="{{ $printEarnerBadge['issuer']['image'] }}" style="width: 150px;"
                        alt="issuer's image"></a> --}}</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
