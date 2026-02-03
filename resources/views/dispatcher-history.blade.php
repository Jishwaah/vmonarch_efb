@extends('layouts.app')

@section('title', 'Dispatcher History | vMonarch EFB')
@section('header', 'Dispatcher History')

@section('content')
    <div class="card">
        <div class="dispatcher-history-header">
            <div>
                <h2>Messages (last 48 hours)</h2>
                <p class="muted">Showing messages since {{ $since->format('Y-m-d H:i') }} UTC.</p>
            </div>
            <a class="btn btn-secondary" href="{{ route('dispatcher') }}">Back to Dispatcher</a>
        </div>

        @if ($messages->isEmpty())
            <div class="empty-state">No messages in the last 48 hours.</div>
        @else
            <div class="message-list">
                @foreach ($messages as $message)
                    <div class="message-item">
                        <div class="message-meta">
                            <span class="badge {{ $message->status === 'sent' ? 'success' : ($message->status === 'failed' ? 'danger' : 'warning') }}">
                                {{ strtoupper($message->status) }}
                            </span>
                            <span class="badge {{ $message->direction === 'inbound' ? 'neutral' : 'success' }}">
                                {{ strtoupper($message->direction ?? 'OUTBOUND') }}
                            </span>
                            <span class="muted">Booking #{{ $message->booking_id }}</span>
                            <span class="muted">Pilot {{ $message->pilot_id }}</span>
                            <span class="muted">{{ $message->created_at->format('Y-m-d H:i') }} UTC</span>
                        </div>
                        <div class="muted">
                            {{ $message->sender_label ?? 'Pilot' }}
                            @if ($message->pilot_callsign)
                                · {{ $message->pilot_callsign }}
                            @endif
                            @if ($message->discord_username)
                                · {{ $message->discord_username }}
                            @endif
                        </div>
                        <div class="message-body">{{ $message->message }}</div>
                        @if ($message->error_message)
                            <div class="muted">Error: {{ $message->error_message }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
