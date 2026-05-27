<form method="POST" action="{{ url('/badge-connect/hosts/' . $host->id . '/export-assertions/' . $assertion->id) }}">
    @csrf
    <button type="submit">
        Assertion küldése Hostnak
    </button>
</form>