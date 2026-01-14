<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Certificate print</title>
</head>
<style>
    @font-face {
        font-family: 'Crimson Text Regular';
        src: url( 'http://badgr2.mti.bme.hu:3000/fonts/CrimsonText-Regular.ttf' ) format('truetype');
        font-weight: 400;
        font-style: normal;
        font-variant: normal;
    }
    @font-face {
        font-family: 'Crimson Text Italic';
        src: url( 'http://badgr2.mti.bme.hu:3000/fonts/CrimsonText-Italic.ttf' ) format('truetype');
        font-weight: 400;
        font-style: italic;
        font-variant: normal;
    }
    @font-face {
        font-family: 'Crimson Text Bold';
        src: url( 'http://badgr2.mti.bme.hu:3000/fonts/CrimsonText-Bold.ttf' ) format('truetype');
        font-weight: 700;
        font-style: normal;
        font-variant: normal;
    }
    @font-face {
        font-family: 'Crimson Text Bold Italic';
        src: url( 'http://badgr2.mti.bme.hu:3000/fonts/CrimsonText-BoldItalic.ttf' ) format('truetype');
        font-weight: 700;
        font-style: italic;
        font-variant: normal;
    }
    @font-face {
        font-family: 'Rubik Bold';
        src: url( 'http://badgr2.mti.bme.hu:3000/fonts/Rubik-Bold.ttf' ) format('truetype');
        font-weight: 700;
        font-style: normal;
        font-variant: normal;
    }
@page {
        margin: 0;
    }
    body {
        font-family: 'Crimson Text Regular';
        margin: 0;
        padding: 0;
        background-position: top;
        background-repeat: no-repeat;
        background-size: cover;
        background-image: url(data:image/jpeg;base64,{{ base64_encode($certBgImage) }});
    }
    #page {
        margin: 20mm;
    }

    footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: auto;
        margin-right: 40mm;
        margin-left: 40mm;
        margin-bottom: 20mm;
    }
    p {
        margin: 0;
        padding: 0;
    }
    .BadgeImg {
        width: 40mm;
        max-width: 40mm;
    }
    .DiscoVET_logo {
        width: 10mm;
    }
    #badge_img {
        margin: auto;
/*        border: solid 1px black;*/
    }
    #badge_img > p{
        text-align: center;
        padding-bottom: 5mm;
    }
    #badge_data {
        width: 100%;
/*        border: solid 1px black;*/
    }
    #badge_data tr td {
/*        border: solid 1px #D7D7D7;*/
    }
    #badge_data p {
        text-align: center;
    }
    #badge_name {
        margin: auto;
        text-align: center;
/*        border: solid 1px black;*/
    }
    #badge_name p {
        font-family: 'Crimson Text Bold';
        font-size: 24pt;
        font-weight: bold;
    }
    #badge_data p.value {
        border-bottom: solid 2px black;
        font-size: 14pt;
        padding-bottom: 0.5em;
        padding-top: 1em;
        font-family: 'Crimson Text Italic';
        font-style: italic;
    }
    #badge_data p.param {
        font-size: 10pt;
        font-family: 'Crimson Text Bold';
        font-variant: small-caps;
        font-weight: bold;
    }
    .centred {
        text-align: center;
    }
    #badge_qr {
        width: 20%;
    }
    .QRCode {
        width: 20mm;
    }
    .badge_data_column {
        max-width: 40%;
        width: 40%;
    }
    </style>
</head>
<body>
<footer>
  <table id="badge_data">
    <tr>
      <td colspan="3"><p class="value">{{ $thisEarnerbadge->first()->badge_issuer_name }}</p>
        <p class="param">Issuer</p></td>
    </tr>
    <tr>
      <td class="badge_data_column"><p class="value">{{ $user['name'] }}</p>
        <p class="param">Recipient Name</p>
        <p class="value">{{ $thisEarnerbadge->first()->badge_issuedon }}</p>
        <p class="param">Awarded on</p></td>
      <td id="badge_qr"><p class="centred"><img src="data:image/png;base64,{{ base64_encode($qrCode) }}" alt="qr code image" class="QRCode"></p></td>
      <td class="badge_data_column"><p class="value">{{ $user['email'] }}</p>
        <p class="param">Recipient id</p>
        <p class="value">???{{ storage_path('fonts/CrimsonText-BoldItalic.ttf') }}</p>
        <p class="param">Expires on</p></td>
    </tr>
    <tr>
      <td colspan="3">
          <p class="centred">
              <img src="data:image/png;base64,{{ base64_encode($discovetLogo) }}" alt="discovet Logo" class="DiscoVET_logo"></p></td>
    </tr>
  </table>
</footer>
<div id="page">
  <div id="badge_img">
    <p><a href="{{ $thisEarnerbadge->first()->badge_issuer_url }}" target="_blank">{{ $thisEarnerbadge->first()->badge_issuer_url }}</a></p>
    <p><img src="data:image/png;base64,{{ base64_encode($badgeImage) }}" alt="badge image" class="BadgeImg"></p>
  </div>
  <div id="badge_name">
    <p>{{ $thisEarnerbadge->first()->badge_name }}</p>
  </div>
</div>
</body>
</html>
