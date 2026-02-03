@extends('layouts.app')

@section('title', 'ACARS | vMonarch EFB')
@section('header', 'ACARS')

@section('content')
    <div class="card">
        <h2>Dispatcher Messages</h2>
        <p class="muted">
            Messages you send here will appear in the dispatcher console.
        </p>
        @if (!empty($booking))
            <p class="muted">
                Active booking:
                <a href="{{ $booking['booking_url'] }}" target="_blank" rel="noopener noreferrer">
                    #{{ $booking['id'] }}
                </a>
            </p>
        @endif

        @if ($messages->isEmpty())
            <div class="empty-state">No ACARS messages yet.</div>
        @else
            <div class="message-list">
                @foreach ($messages as $message)
                    <div class="message-item">
                        <div class="message-meta">
                            @if ($message->direction === 'outbound')
                                <span class="badge success">FROM DISPATCH</span>
                            @else
                                <span class="badge neutral">TO DISPATCH</span>
                            @endif
                            <span class="muted">{{ $message->created_at->format('Y-m-d H:i') }} UTC</span>
                        </div>
                        <div class="message-body">{{ $message->message }}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="card">
        <h2>Send Message</h2>
        <form class="form" method="POST" action="{{ route('acars.send') }}">
            @csrf
            <label>
                Message
                <textarea name="message" rows="4" placeholder="Message to dispatch..." required></textarea>
            </label>
            <button class="btn btn-primary" type="submit">Send</button>
        </form>
    </div>
@endsection
