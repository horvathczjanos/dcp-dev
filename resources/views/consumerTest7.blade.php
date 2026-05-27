<form method="POST" action="{{ url('/badge-connect/hosts/' . $host->id . '/register-write-client')  }}">
    @csrf

    <button type="submit">
        Consumer Write auto-registration
    </button>
</form>