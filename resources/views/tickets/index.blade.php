@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 1100px;">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:16px;">
        <div>
            <h1 style="margin:0;">Change Requests</h1>
            <p style="margin:6px 0 0; opacity:.8;">Submit and track bug reports and feature requests.</p>
        </div>

        <a href="{{ route('tickets.create') }}" class="btn btn-primary">
            + New Ticket
        </a>
    </div>

    @if (session('status'))
        <div class="alert alert-success" style="margin-bottom: 16px;">
            {{ session('status') }}
        </div>
    @endif

    <div class="card" style="padding: 14px;">
        <div style="overflow:auto;">
            <table class="table" style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="text-align:left; border-bottom:1px solid #ddd;">
                        <th style="padding:10px;">#</th>
                        <th style="padding:10px;">Title</th>
                        <th style="padding:10px;">Type</th>
                        <th style="padding:10px;">Status</th>
                        <th style="padding:10px;">Priority</th>
                        <th style="padding:10px;">Created</th>
                        <th style="padding:10px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tickets as $ticket)
                        <tr style="border-bottom:1px solid #f0f0f0;">
                            <td style="padding:10px;">{{ $ticket->id }}</td>
                            <td style="padding:10px;">
                                <a href="{{ route('tickets.show', $ticket) }}">
                                    {{ $ticket->title }}
                                </a>
                            </td>
                            <td style="padding:10px;">
                                <span class="badge">{{ strtoupper($ticket->type) }}</span>
                            </td>
                            <td style="padding:10px;">
                                <span class="badge">{{ strtoupper($ticket->status) }}</span>
                            </td>
                            <td style="padding:10px;">
                                <strong>{{ $ticket->priority }}</strong>
                            </td>
                            <td style="padding:10px;">
                                {{ $ticket->created_at?->format('Y-m-d H:i') }}
                            </td>
                            <td style="padding:10px; text-align:right;">
                                <a class="btn btn-sm btn-outline" href="{{ route('tickets.show', $ticket) }}">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding:16px; opacity:.7; text-align:center;">
                                No tickets yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 12px;">
            {{ $tickets->links() }}
        </div>
    </div>
</div>
@endsection
