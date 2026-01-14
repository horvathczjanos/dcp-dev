@extends(session('controlButtons') ? 'layouts.general' : 'layouts.mail')

@section('title', __('mailBadge.title3'))

@section('content')

    <div class="bd-callout bd-callout-info">
        <p><strong>Tisztelt {{ $details['recipientLastName'] . ' ' . $details['recipientFirstName'] }} kitűző
                tulajdonos!</strong></p>
        <p>A {{ $details['issuerName'] }} Önnek "<strong>{{ $details['badgeName'] }}</strong>" címmel egy digitális kitűzőt állított ki,
            melyet csatolva megküldünk.</p>
        <p>A kitűzőt azért kapta, mert:</p>
        <p><i>{{ $details['badgeCriteria'] }}</i></p>
        <p>Digitális kitűzőit a <a href="{{ env('APP_URL') }}" target="_blank">DisCoPLAYER</a> oldalon
            gyűjtheti és tárolhatja, az &lt;embed&gt; kóddal beágyazhatja, vagy feltötlheti a Linked-in profiljába.</p>
        <p>Fogadja elismerésünket!</p>
        <p>A {{ $details['issuerName'] }} nevében a DCP csapata<br>
            <a href="mailto:dcp@mti.bme.hu">dcp@mti.bme.hu</a>
        </p>
        <p>&nbsp;</p>
        <p>
            <i><strong>Miért van szükség megjelenítő felületre a digitális kitűzőkhöz?</strong>

                Amikor digitális kitűzőt kap e-mailben, tulajdonképpen egy képfájl érkezik az e-mailhet csatolva: egy
                egyszerűnek tűnő kép, néhány adattal (talán csak az esemény címével és dátummal). A kitűzők viszont ennél
                sokkal többet rejtenek: a képfájl kódolva tartalmazza az esemény részleteit, leírja a kontextust,
                meghatározza a teljesítményt és a tanulási eredmény típusát. Ahhoz, hogy hozzáférjen ehhez az információhoz,
                szüksége van egy megjelenítő platformra, ahol meg lehet tekinteni a "beégetett" tartalmat, és ahonnan meg is
                tudja osztani azt kollégáival vagy hozzáteheti azt az önéletrajzához. A DIScoPLAYER platform a Budapesti
                Műszaki és Gazdaságtudományi Egyetem közreműködésével kifejlesztett és az egyetem által gondozott
                alkalmazás, ami a felhasználók számára ingyenes és magyar nyelven is használható. Látogasson el erre a
                <a href="{{ env('APP_URL') }}" target="_blank">linkre</a>, regisztráljon, majd töltse fel a jelen e-mail mellékletében található .png fájlt és máris látni
                fogja a kitűző teljes tartalmát, amelyet ezt követően megoszthat e-mailben, kinyomtathat pdf-ben vagy
                feltöltheti azt a weboldalára. <br /> A digitális kitűzők mibenlétéről és arról, hogyan tudja azokat
                gyűjteni majd megosztani, összeállítottunk egy rövid összefoglalót:
                <a href="https://www.mti.bme.hu/a-digitalis-kituzokrol/" target="_blank">https://www.mti.bme.hu/a-digitalis-kituzokrol/</a>
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
