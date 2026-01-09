<p>{{ $messageLine }}</p>

<p>
    <strong>Ticket #{{ $ticket->id }}:</strong> {{ $ticket->title }}<br>
    <strong>Status:</strong> {{ strtoupper($ticket->status) }}<br>
    <strong>Priority:</strong> {{ $ticket->priority }}<br>
</p>

@if(!empty($meta))
    <p><strong>Details:</strong></p>
    <ul>
        @foreach($meta as $k => $v)
            <li><strong>{{ $k }}:</strong> {{ $v }}</li>
        @endforeach
    </ul>
@endif
