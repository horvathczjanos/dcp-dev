<form method="GET" action="{{ url('/badge-connect/hosts/' . $host->id . '/connect-write')  }}">
    @csrf

    <button type="submit">
        Consumer Write OAuth kapcsolódás
    </button>
</form>