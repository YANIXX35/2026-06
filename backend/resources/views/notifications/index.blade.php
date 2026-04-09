@extends('layouts.app')
@section('title', 'Mes notifications')

@section('content')
<div class="container py-5" style="max-width:680px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold"><i class="fas fa-bell me-2 text-primary"></i>Mes notifications</h4>
    </div>

    @forelse($notifications as $notif)
    <div style="background:{{ $notif->read_at ? '#fff' : '#f0fdf4' }};border:1px solid {{ $notif->read_at ? '#f0f0f0' : '#bbf7d0' }};border-radius:14px;padding:16px 20px;margin-bottom:12px;display:flex;gap:14px;align-items:flex-start;">
        <span style="font-size:1.5rem;flex-shrink:0;">{{ $notif->data['icone'] ?? '🔔' }}</span>
        <div style="flex:1;">
            <div style="font-size:.88rem;font-weight:700;color:#1a1a2e;">{{ $notif->data['titre'] ?? '' }}</div>
            <div style="font-size:.82rem;color:#555;margin-top:3px;line-height:1.5;">{{ $notif->data['message'] ?? '' }}</div>
            <div style="font-size:.72rem;color:#aaa;margin-top:5px;">{{ $notif->created_at->diffForHumans() }}</div>
        </div>
        @if(isset($notif->data['url']))
        <a href="{{ $notif->data['url'] }}" class="btn btn-sm btn-outline-primary rounded-pill px-3" style="flex-shrink:0;align-self:center;">
            Voir →
        </a>
        @endif
    </div>
    @empty
    <div class="text-center py-5 text-muted">
        <i class="fas fa-bell-slash fa-3x mb-3"></i>
        <p>Aucune notification pour l'instant.</p>
    </div>
    @endforelse

    <div class="d-flex justify-content-center mt-4">
        {{ $notifications->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
