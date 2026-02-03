@extends('layouts.app')

@section('title', 'Dispatcher | vMonarch EFB')
@section('header', 'Dispatcher')

@section('content')
    @if ($error)
        <div class="alert error">vAMSYS error: {{ $error }}</div>
    @endif

    <div class="card">
        <h2>Active Bookings</h2>
        <p class="muted">Only pilots with current vAMSYS bookings are listed here.</p>

        @if (empty($bookings))
            <div class="empty-state">No active bookings found.</div>
        @else
            <div class="dispatcher-cards">
                @foreach ($bookings as $booking)
                    <div class="dispatcher-card">
                        <div class="dispatcher-card-header">
                            <div>
                                <div class="booking-id">
                                    <a href="{{ $booking['booking_url'] }}" target="_blank" rel="noopener noreferrer">
                                        Booking #{{ $booking['id'] }}
                                    </a>
                                </div>
                                <div class="muted">Pilot ID: {{ data_get($booking, 'pilot_id') }}</div>
                            </div>
                            <div class="badge-group">
                                @if ($booking['efb_ready'])
                                    <span class="badge success">DM Enabled</span>
                                @else
                                    <span class="badge warning">DM Disabled</span>
                                @endif
                                <span class="badge neutral">{{ data_get($booking, 'callsign') ?? 'No callsign' }}</span>
                            </div>
                        </div>

                        <div class="dispatcher-card-body">
                            <div class="pilot-block">
                                <div class="label">Pilot</div>
                                <div class="value">
                                    {{ data_get($booking, 'pilot.name') ?? data_get($booking, 'pilot.username') ?? 'Unknown' }}
                                </div>
                                <div class="muted">
                                    {{ data_get($booking, 'pilot.username') ? 'Callsign: '.data_get($booking, 'pilot.username') : '' }}
                                </div>
                            </div>
                            <div class="pilot-block">
                                <div class="label">Discord</div>
                                <div class="value">
                                    @if ($booking['discord_id'])
                                        {{ $booking['discord_id'] }}
                                    @else
                                        —
                                    @endif
                                </div>
                                <div class="muted">
                                    @if (! $booking['discord_id'])
                                        No Discord ID on record
                                    @endif
                                </div>
                            </div>
                            <div class="pilot-block">
                                <div class="label">Flight</div>
                                <div class="value">{{ data_get($booking, 'departure_id') ?? '—' }} → {{ data_get($booking, 'arrival_id') ?? '—' }}</div>
                                <div class="muted">Network: {{ data_get($booking, 'network') ?? 'Offline' }}</div>
                            </div>
                        </div>

                        <div class="dispatcher-card-footer">
                            @if ($booking['efb_ready'])
                                <form method="POST" action="{{ route('dispatcher.message') }}" class="dispatcher-form">
                                    @csrf
                                    <input type="hidden" name="booking_id" value="{{ $booking['id'] }}" />
                                    <input type="hidden" name="pilot_id" value="{{ $booking['pilot_id'] }}" />
                                    <input type="hidden" name="discord_id" value="{{ $booking['discord_id'] }}" />
                                    <textarea name="message" rows="2" placeholder="Message to pilot..." required></textarea>
                                    <button class="btn btn-primary" type="submit">Send DM</button>
                                </form>
                            @else
                                <div class="muted">Pilot must log in to EFB and enable Discord DMs before messaging.</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="card">
        <h2>Recent Dispatcher Messages</h2>
        @if ($messages->isEmpty())
            <div class="empty-state">No messages sent yet.</div>
        @else
            <div class="message-list">
                @foreach ($messages as $message)
                    <div class="message-item">
                        <div class="message-meta">
                            <span class="badge {{ $message->status === 'sent' ? 'success' : ($message->status === 'failed' ? 'danger' : 'warning') }}">
                                {{ strtoupper($message->status) }}
                            </span>
                            <span class="muted">Booking #{{ $message->booking_id }}</span>
                            <span class="muted">Pilot {{ $message->pilot_id }}</span>
                            <span class="muted">{{ $message->created_at->format('Y-m-d H:i') }} UTC</span>
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
