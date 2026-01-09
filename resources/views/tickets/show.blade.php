@extends('tickets::layouts.app')

@section('content')
<div class="container" style="max-width: 1100px;">
    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:16px;">
        <div>
            <div style="opacity:.75;">Ticket #{{ $ticket->id }}</div>
            <h1 style="margin:4px 0 0;">{{ $ticket->title }}</h1>
            <div style="margin-top:8px; display:flex; gap:8px; flex-wrap:wrap;">
                <span class="badge">{{ strtoupper($ticket->type) }}</span>
                <span class="badge">{{ strtoupper($ticket->status) }}</span>
                <span class="badge">PRIORITY: {{ $ticket->priority }}</span>
                @if($ticket->escalation_requested)
                    <span class="badge" style="background:#fff3cd;">ESCALATION REQUESTED</span>
                @endif
            </div>
        </div>

        <div style="display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end;">
            <a href="{{ route('tickets.index') }}" class="btn btn-outline">Back</a>

            @can('escalate', $ticket)
                <form method="POST" action="{{ route('tickets.escalate', $ticket) }}">
                    @csrf
                    <button class="btn btn-warning" type="submit">Request Escalation</button>
                </form>
            @endcan
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success" style="margin-bottom: 16px;">
            {{ session('status') }}
        </div>
    @endif

    <div style="display:grid; grid-template-columns: 1fr 360px; gap:14px;">
        {{-- Left: description + comments --}}
        <div class="card" style="padding: 16px;">
            <h3 style="margin-top:0;">Description</h3>
            <div style="white-space:pre-wrap; line-height:1.5;">{{ $ticket->description }}</div>

            <hr style="margin:18px 0;">

            <h3 style="margin-top:0;">Activity</h3>

            <div style="display:flex; flex-direction:column; gap:10px;">
                @forelse($ticket->comments as $c)
                    <div style="border:1px solid #eee; padding:10px; border-radius:8px;">
                        <div style="display:flex; justify-content:space-between; gap:10px;">
                            <div style="opacity:.8; font-size: 13px;">
                                {{ $c->is_system ? 'System' : ($users[$c->user_id]->name ?? ('User #'.$c->user_id)) }}
                            </div>
                            <div style="opacity:.6; font-size: 12px;">
                                {{ $c->created_at?->format('Y-m-d H:i') }}
                            </div>
                        </div>
                        <div style="margin-top:6px; white-space:pre-wrap;">{{ $c->body }}</div>
                    </div>
                @empty
                    <div style="opacity:.7;">No activity yet.</div>
                @endforelse
            </div>

            <hr style="margin:18px 0;">

            @can('view', $ticket)
                <h3 style="margin-top:0;">Add Comment</h3>
                <form method="POST" action="{{ route('tickets.comments.store', $ticket) }}">
                    @csrf
                    <div style="margin-bottom:10px;">
                        <textarea name="body" rows="4" style="width:100%; padding:10px;"
                            placeholder="Write a comment...">{{ old('body') }}</textarea>
                        @error('body') <div style="color:#c00; margin-top:6px;">{{ $message }}</div> @enderror
                    </div>
                    <button class="btn btn-primary" type="submit">Post Comment</button>
                </form>
            @endcan
        </div>

        {{-- Right: actions --}}
        <div class="card" style="padding: 16px;">
            <h3 style="margin-top:0;">Actions</h3>

            {{-- Update title/description (Level 2+) --}}
            @can('update', $ticket)
                <div style="margin-bottom:14px;">
                    <h4 style="margin:0 0 8px;">Edit</h4>
                    <form method="POST" action="{{ route('tickets.update', $ticket) }}">
                        @csrf
                        @method('PATCH')

                        <div style="margin-bottom:10px;">
                            <label style="display:block; font-weight:600; margin-bottom:6px;">Title</label>
                            <input name="title" value="{{ old('title', $ticket->title) }}" style="width:100%; padding:10px;">
                            @error('title') <div style="color:#c00; margin-top:6px;">{{ $message }}</div> @enderror
                        </div>

                        <div style="margin-bottom:10px;">
                            <label style="display:block; font-weight:600; margin-bottom:6px;">Description</label>
                            <textarea name="description" rows="4" style="width:100%; padding:10px;">{{ old('description', $ticket->description) }}</textarea>
                            @error('description') <div style="color:#c00; margin-top:6px;">{{ $message }}</div> @enderror
                        </div>

                        <button class="btn btn-primary" type="submit">Save</button>
                    </form>
                </div>
                <hr style="margin:16px 0;">
            @endcan

            {{-- Priority (Level 2/3, cap enforced in policy) --}}
            @can('changePriority', [$ticket, 1])
                <div style="margin-bottom:14px;">
                    <h4 style="margin:0 0 8px;">Priority</h4>
                    <form method="POST" action="{{ route('tickets.priority', $ticket) }}">
                        @csrf
                        <div style="display:flex; gap:8px; align-items:center;">
                            <input type="number"
                                   name="priority"
                                   min="1"
                                   max="{{ auth()->user()->ticketPriorityCap() }}"
                                   value="{{ $ticket->priority }}"
                                   style="width:120px; padding:10px;">
                            <button class="btn btn-outline" type="submit">Update</button>
                        </div>
                        @error('priority') <div style="color:#c00; margin-top:6px;">{{ $message }}</div> @enderror
                    </form>
                    <div style="opacity:.7; margin-top:6px;">Your max: {{ auth()->user()->ticketPriorityCap() }}</div>
                </div>
                <hr style="margin:16px 0;">
            @endcan

            {{-- Verify bug --}}
            @can('verifyBug', $ticket)
                <div style="margin-bottom:10px;">
                    <form method="POST" action="{{ route('tickets.verify', $ticket) }}">
                        @csrf
                        <button class="btn btn-warning" type="submit">Verify Bug</button>
                    </form>
                </div>
            @endcan

            {{-- Assign --}}
            @can('assign', $ticket)
                <div style="margin-bottom:14px;">
                    <h4 style="margin:0 0 8px;">Assign</h4>
                    <form method="POST" action="{{ route('tickets.assign', $ticket) }}">
                        @csrf
                        <div style="display:flex; gap:8px; align-items:center;">
                            <input type="number" name="assigned_to" placeholder="User ID" style="width:140px; padding:10px;">
                            <button class="btn btn-outline" type="submit">Assign</button>
                        </div>
                        @error('assigned_to') <div style="color:#c00; margin-top:6px;">{{ $message }}</div> @enderror
                    </form>
                    <div style="opacity:.7; margin-top:6px;">
                        Current: {{ $ticket->assigned_to ? 'User #'.$ticket->assigned_to : 'Unassigned' }}
                    </div>
                </div>
            @endcan

            {{-- Resolve --}}
            @can('resolve', $ticket)
                <div style="margin-top:10px;">
                    <form method="POST" action="{{ route('tickets.resolve', $ticket) }}">
                        @csrf
                        <button class="btn btn-success" type="submit">Mark Resolved</button>
                    </form>
                </div>
            @endcan
        </div>
    </div>
</div>
@endsection
