<form method="POST" action="{{ url('/badge-connect/hosts/' . $host->id . '/import-profile') }}">
    @csrf

    <button type="submit">
        Profile lekérése
    </button>
</form>