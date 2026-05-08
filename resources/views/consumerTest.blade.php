<form method="POST" action=" {{ url('/badge-connect/hosts/' . $host->id . '/fetch-manifest') }}">
    @csrf

    <button type="submit">
        Manifest lekérése
    </button>
</form>