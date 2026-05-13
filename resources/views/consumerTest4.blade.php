<form method="GET" action="{{ url('/badge-connect/hosts/' . $host->id . '/connect') }}">
    @csrf

    <button type="submit">
        Kapcsolódás OAuth + PKCE használatával
    </button>
</form>