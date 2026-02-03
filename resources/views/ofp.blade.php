@extends('layouts.app')

@section('title', 'OFP | vMonarch EFB')
@section('header', 'OFP')

@section('content')
    <div class="card">
        <h2>Operational Flight Plan</h2>

        @if ($simbrief['status'] !== 'ok')
            <p class="muted">{{ $simbrief['message'] ?? 'No SimBrief flight available yet.' }}</p>
        @else
            <p class="muted">Open the SimBrief PDF for your latest flight plan.</p>
            @if (!empty($flight['ofp_pdf_url']))
                <div class="ofp-actions">
                    <a class="btn btn-primary" href="{{ $flight['ofp_pdf_url'] }}" target="_blank" rel="noopener noreferrer">
                        Open OFP PDF
                    </a>
                </div>
                <div class="ofp-embed">
                    <iframe
                        title="SimBrief OFP PDF"
                        src="{{ $flight['ofp_pdf_url'] }}"
                        loading="lazy"
                    ></iframe>
                </div>
            @else
                <p class="muted">No PDF link available from SimBrief yet.</p>
            @endif
        @endif
    </div>
@endsection
