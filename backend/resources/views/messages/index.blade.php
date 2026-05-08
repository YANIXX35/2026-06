@extends('layouts.app')
@section('title', 'Mes messages')
@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-primary text-white p-4">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-comments me-2"></i>Conversations</h6>
                </div>
                <div class="card-body p-0">
                    @forelse($conversations as $conv)
                        @php
                            $autre = $conv->user_1_id === auth()->id() ? $conv->user2 : $conv->user1;
                            $dernier = $conv->dernierMessage;
                            $nonLus = $conv->nonLus(auth()->id());
                        @endphp
                        <a href="{{ route('messages.show', $conv) }}" class="text-decoration-none">
                            <div class="d-flex align-items-center p-3 border-bottom hover-bg-light {{ $nonLus > 0 ? 'bg-light' : '' }}">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width:44px;height:44px;">
                                    <span class="text-white fw-bold">{{ strtoupper(substr($autre->prenom,0,1)) }}</span>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-semibold small text-dark">{{ $autre->prenom }} {{ $autre->nom }}</span>
                                        @if($dernier)
                                            <span class="text-muted" style="font-size:11px;">{{ $dernier->created_at->diffForHumans() }}</span>
                                        @endif
                                    </div>
                                    @if($dernier)
                                        <div class="text-muted small text-truncate">{{ Str::limit($dernier->contenu, 45) }}</div>
                                    @else
                                        <div class="text-muted small fst-italic">Aucun message</div>
                                    @endif
                                </div>
                                @if($nonLus > 0)
                                    <span class="badge bg-primary rounded-pill ms-2">{{ $nonLus }}</span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-comment-slash fa-3x mb-3 opacity-25"></i>
                            <p>Aucune conversation</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 h-100 d-flex align-items-center justify-content-center" style="min-height:400px;">
                <div class="text-center text-muted p-5">
                    <i class="fas fa-comments fa-4x mb-4 opacity-25"></i>
                    <h5>Sélectionnez une conversation</h5>
                    <p>Choisissez une conversation dans la liste pour afficher les messages.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
