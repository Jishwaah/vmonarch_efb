@extends('layouts.app')

@section('title', 'Dashboard | vMonarch EFB')
@section('header', 'Dashboard')

@section('content')
    <div class="card">
        <h2>Current SimBrief Flight</h2>
        <form method="POST" action="{{ route('dashboard.simbrief.refresh') }}" style="margin-bottom: 12px;">
            @csrf
            <button class="btn btn-secondary" type="submit">Force Refresh</button>
        </form>

        @if ($simbrief['status'] !== 'ok')
            <p class="muted">{{ $simbrief['message'] ?? 'No SimBrief flight available yet.' }}</p>
        @else
            <div class="flight-grid">
                <div class="flight-block">
                    <div class="label">Origin</div>
                    <div class="value">
                        {{ $flight['origin_iata'] ?? '�' }}
                        <span class="icao">{{ $flight['origin_icao'] ?? '�' }}</span>
                    </div>
                </div>
                <div class="flight-block">
                    <div class="label">Destination</div>
                    <div class="value">
                        {{ $flight['destination_iata'] ?? '�' }}
                        <span class="icao">{{ $flight['destination_icao'] ?? '�' }}</span>
                    </div>
                </div>
                <div class="flight-block">
                    <div class="label">Cruise Altitude</div>
                    <div class="value">{{ $flight['cruise_altitude'] ?? '�' }}</div>
                </div>                <div class="flight-block">
                    <div class="label">Alternate Airport</div>
                    <div class="value">
                        @if (!empty($flight['alternate_iata']) || !empty($flight['alternate_icao']))
                            {{ $flight['alternate_iata'] ?? '�' }}
                            <span class="icao">{{ $flight['alternate_icao'] ?? '�' }}</span>
                        @elseif (!empty($flight['alternates']))
                            {{ $flight['alternates'][0] }}
                        @else
                            �
                        @endif
                    </div>
                    @if (!empty($flight['alternate2_iata']) || !empty($flight['alternate2_icao']))
                        <div class="muted">
                            Secondary:
                            {{ $flight['alternate2_iata'] ?? '�' }}
                            <span class="icao">{{ $flight['alternate2_icao'] ?? '�' }}</span>
                        </div>
                    @endif
                </div>
                <div class="flight-block">
                    <div class="label">ETD</div>
                    <div class="value">{{ $flight['etd'] ?? '�' }}</div>
                </div>
                <div class="flight-block">
                    <div class="label">ETA</div>
                    <div class="value">{{ $flight['eta'] ?? '�' }}</div>
                </div>
                <div class="flight-block">
                    <div class="label">ETE</div>
                    <div class="value">{{ $flight['ete'] ?? '�' }}</div>
                </div>
            </div>
        @endif
    </div>

    @if (!empty($booking))
        <div class="card">
            <h2>Active vAMSYS Booking</h2>
            <p class="muted">
                Booking ID:
                <a href="{{ $booking['booking_url'] }}" target="_blank" rel="noopener noreferrer">
                    #{{ $booking['id'] }}
                </a>
            </p>
        </div>
    @endif
    <div class="card">
        <h2>Quick Links</h2>
        <div class="quick-links">
            <a class="btn btn-secondary" href="{{ route('ofp') }}">View OFP</a>
            <a class="btn btn-secondary" href="{{ route('performance') }}">Performance</a>
            <a class="btn btn-secondary" href="{{ route('weather') }}">Weather</a>
            <a class="btn btn-secondary" href="{{ route('acars') }}">ACARS</a>
            <button class="btn btn-secondary" type="button" data-modal-open="crew-modal">Flight Crew</button>
        </div>
    </div>

    <div class="modal-overlay" data-modal="crew-modal" aria-hidden="true">
        <div class="modal">
            <div class="modal-header">
                <h2>Flight Crew</h2>
                <button class="btn btn-secondary" type="button" data-modal-close="crew-modal">Close</button>
            </div>
            <div class="modal-body">
                @php
                    $crew = $flight['crew'] ?? [];
                    $pilotId = data_get($crew, 'pilot_id');
                @endphp
                @if (empty($crew))
                    <div class="empty-state">No crew information available.</div>
                @else
                    <div class="crew-list">
                        <div class="crew-item">
                            <div class="label">Pilot in Command</div>
                            <div class="value">
                                {{ strtoupper(data_get($crew, 'cpt') ?? 'Unknown') }}
                                @if ($pilotId)
                                    <span class="muted">({{ $pilotId }})</span>
                                @endif
                            </div>
                        </div>
                        <div class="crew-item">
                            <div class="label">First Officer</div>
                            <div class="value">{{ data_get($crew, 'fo') ?? 'Unknown' }}</div>
                        </div>
                        <div class="crew-item">
                            <div class="label">Dispatcher</div>
                            <div class="value">{{ data_get($crew, 'dx') ?? 'Unknown' }}</div>
                        </div>
                        <div class="crew-item">
                            <div class="label">Cabin Manager</div>
                            <div class="value">{{ data_get($crew, 'pu') ?? 'Unknown' }}</div>
                        </div>
                        <div class="crew-item">
                            <div class="label">Cabin Crew</div>
                            <div class="value">
                                @php $fa = data_get($crew, 'fa', []); @endphp
                                @if (! empty($fa))
                                    <div class="crew-tags">
                                        @foreach ($fa as $name)
                                            <span class="badge neutral">{{ $name }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    �
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection


