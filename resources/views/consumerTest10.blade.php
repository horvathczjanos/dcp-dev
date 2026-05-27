<form method="POST" action="{{ url('/badge-connect/hosts/' . $host->id . '/export-profile') }}">
    @csrf
    <button type="submit">Profile küldése Hostnak</button>
</form>