<form method="POST" action="{{ url('/badge-connect/hosts/' . $host->id . '/register-client') }}">
    @csrf

    <button type="submit">
        Auto-registration indítása
    </button>
</form>