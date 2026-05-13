<form method="POST" action="{{ url('/badge-connect/hosts/' . $host->id . '/import-assertions') }}">
    @csrf

    <button type="submit">
        Assertion-ök lekérése
    </button>
</form>