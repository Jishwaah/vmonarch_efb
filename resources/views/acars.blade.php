@extends('layouts.app')

@section('title', 'ACARS | vMonarch EFB')
@section('header', 'ACARS')

@section('content')
    <div class="card">
        <h2>Dispatcher Messages</h2>
        <p class="muted">
            ACARS integration is queued. Messages will sync with the dispatcher tool and Discord DMs if enabled.
        </p>
        <div class="empty-state">No ACARS messages yet.</div>
    </div>

    <div class="card">
        <h2>Send Message</h2>
        <form class="form disabled-form">
            <label>
                Message
                <textarea rows="4" placeholder="Message to dispatch..." disabled></textarea>
            </label>
            <button class="btn btn-disabled" type="button" disabled>Send</button>
        </form>
    </div>
@endsection
