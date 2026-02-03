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
                    @php
                        $bookingMessages = $messagesByBooking->get($booking['id'], ['inbound' => collect(), 'outbound' => collect()]);
                    @endphp
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

                        <div class="dispatcher-card-messages">
                            <div class="message-column">
                                <div class="label">Inbound (last 5)</div>
                                @forelse ($bookingMessages['inbound'] as $message)
                                    <div class="message-item compact">
                                        <div class="message-meta">
                                            <span class="badge neutral">INBOUND</span>
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
                                    </div>
                                @empty
                                    <div class="muted">No inbound messages.</div>
                                @endforelse
                            </div>
                            <div class="message-column">
                                <div class="label">Outbound (last 5)</div>
                                @forelse ($bookingMessages['outbound'] as $message)
                                    <div class="message-item compact">
                                        <div class="message-meta">
                                            <span class="badge success">OUTBOUND</span>
                                            <span class="muted">{{ $message->created_at->format('Y-m-d H:i') }} UTC</span>
                                        </div>
                                        <div class="muted">MONOPS</div>
                                        <div class="message-body">{{ $message->message }}</div>
                                    </div>
                                @empty
                                    <div class="muted">No outbound messages.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="card">
        <div class="dispatcher-history-header">
            <h2>Dispatcher History</h2>
            <a class="btn btn-secondary" href="{{ route('dispatcher.history') }}">View last 48 hours</a>
        </div>
        <p class="muted">Use the history view to see all messages within the last 48 hours.</p>
    </div>
@endsection
