@extends('layouts.app')

@section('title', 'Performance | vMonarch EFB')
@section('header', 'Performance')

@section('content')
    <div class="card">
        <h2>Performance Tool</h2>
        <p class="muted">
            This will integrate with the SimBrief performance tools. For now, use the SimBrief link below.
        </p>
        <a class="btn btn-secondary" href="{{ $performanceUrl }}" target="_blank" rel="noopener noreferrer">
            Open SimBrief Performance
        </a>
    </div>
@endsection
