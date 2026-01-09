@extends('tickets::layouts.app')
@section('content')
<div class="container" style="max-width: 900px;">
    <div style="margin-bottom: 16px;">
        <h1 style="margin:0;">New Ticket</h1>
        <p style="margin:6px 0 0; opacity:.8;">Report a bug or request a feature.</p>
    </div>

    <div class="card" style="padding: 16px;">
        <form method="POST" action="{{ route('tickets.store') }}">
            @csrf

            <div style="margin-bottom:12px;">
                <label style="display:block; font-weight:600; margin-bottom:6px;">Title</label>
                <input type="text" name="title" value="{{ old('title') }}" class="form-control" style="width:100%; padding:10px;">
                @error('title') <div style="color:#c00; margin-top:6px;">{{ $message }}</div> @enderror
            </div>

            <div style="margin-bottom:12px;">
                <label style="display:block; font-weight:600; margin-bottom:6px;">Type</label>
                <select name="type" class="form-control" style="width:100%; padding:10px;">
                    <option value="bug" @selected(old('type')==='bug')>Bug</option>
                    <option value="feature" @selected(old('type')==='feature')>Feature</option>
                </select>
                @error('type') <div style="color:#c00; margin-top:6px;">{{ $message }}</div> @enderror
            </div>

            <div style="margin-bottom:12px;">
                <label style="display:block; font-weight:600; margin-bottom:6px;">
                    Priority
                    <span style="opacity:.7; font-weight:400;">
                        (Your max: {{ auth()->user()->ticketPriorityCap() }})
                    </span>
                </label>
                <input type="number"
                       name="priority"
                       min="1"
                       max="{{ auth()->user()->ticketPriorityCap() }}"
                       value="{{ old('priority', 1) }}"
                       class="form-control"
                       style="width:220px; padding:10px;">
                @error('priority') <div style="color:#c00; margin-top:6px;">{{ $message }}</div> @enderror
            </div>

            <div style="margin-bottom:12px;">
                <label style="display:block; font-weight:600; margin-bottom:6px;">Description</label>
                <textarea name="description" rows="8" class="form-control" style="width:100%; padding:10px;">{{ old('description') }}</textarea>
                @error('description') <div style="color:#c00; margin-top:6px;">{{ $message }}</div> @enderror
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <a href="{{ route('tickets.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>
@endsection
