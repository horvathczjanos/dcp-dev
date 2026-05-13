@if (session('success'))
    <div style="color: green;">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div style="color: red;">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<h1>Badge Connect Hosts</h1>

@foreach ($hosts as $host)
    <div style="border: 1px solid #ccc; padding: 12px; margin-bottom: 12px;">
        <h2>{{ $host->name }}</h2>

        <p>Base URL: {{ $host->base_url }}</p>
        <p>API Base: {{ $host->api_base ?? '-' }}</p>
        <p>Authorization URL: {{ $host->authorization_url ?? '-' }}</p>

        @if ($host->remoteClient)
            <p>Client ID: {{ $host->remoteClient->client_id }}</p>
            <p>Scope: {{ $host->remoteClient->scope }}</p>
        @else
            <p>Nincs remote client registration.</p>
        @endif
    </div>
@endforeach