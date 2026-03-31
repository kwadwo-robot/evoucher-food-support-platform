@extends('layouts.dashboard')
@section('title', 'Broadcast Message')
@section('page-title', 'Broadcast Message')

@section('content')
<div class="page-hd">
    <h1>{{ $broadcast->title }}</h1>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <h5 class="card-title">{{ $broadcast->title }}</h5>
                        <small class="text-muted">
                            Sent on {{ $broadcast->sent_at ? $broadcast->sent_at->format('M d, Y H:i') : 'N/A' }}
                        </small>
                    </div>
                    
                    <div class="broadcast-message">
                        <p>{{ $broadcast->message }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.broadcast-message {
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 5px;
    line-height: 1.6;
    white-space: pre-wrap;
    word-wrap: break-word;
}
</style>
@endsection
