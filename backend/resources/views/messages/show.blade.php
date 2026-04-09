@extends('layouts.app')
@section('title', 'Conversation')
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
                            $autre2 = $conv->user_1_id === auth()->id() ? $conv->user2 : $conv->user1;
                            $dernier2 = $conv->dernierMessage();
                            $nonLus2 = $conv->nonLus(auth()->id());
                        @endphp
                        <a href="{{ route('messages.show', $conv) }}" class="text-decoration-none">
                            <div class="d-flex align-items-center p-3 border-bottom {{ $conv->id === $conversation->id ? 'bg-primary bg-opacity-10' : '' }}">
                                <div class="rounded-circle bg-{{ $conv->id === $conversation->id ? 'primary' : 'secondary' }} d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width:40px;height:40px;">
                                    <span class="text-white fw-bold small">{{ strtoupper(substr($autre2->prenom,0,1)) }}</span>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold small text-dark">{{ $autre2->prenom }} {{ $autre2->nom }}</div>
                                    @if($dernier2)
                                        <div class="text-muted" style="font-size:11px;">{{ Str::limit($dernier2->contenu, 35) }}</div>
                                    @endif
                                </div>
                                @if($nonLus2 > 0)
                                    <span class="badge bg-primary rounded-pill ms-1">{{ $nonLus2 }}</span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-4 text-muted small">Aucune conversation</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white p-4 border-bottom d-flex align-items-center">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width:44px;height:44px;">
                        <span class="text-white fw-bold">{{ strtoupper(substr($interlocuteur->prenom,0,1)) }}</span>
                    </div>
                    <div>
                        <div class="fw-bold">{{ $interlocuteur->prenom }} {{ $interlocuteur->nom }}</div>
                        <div class="text-muted small">{{ $interlocuteur->role }}</div>
                    </div>
                </div>
                <div class="card-body p-4" style="height:420px;overflow-y:auto;" id="messagesContainer">
                    @forelse($messages as $msg)
                        @if($msg->user_id === auth()->id())
                            <div class="d-flex justify-content-end mb-3">
                                <div class="bg-primary text-white rounded-3 px-3 py-2" style="max-width:70%;">
                                    <div>{{ $msg->contenu }}</div>
                                    <div style="font-size:10px;opacity:0.75;text-align:right;" class="mt-1">{{ $msg->created_at->format('H:i') }}</div>
                                </div>
                            </div>
                        @else
                            <div class="d-flex justify-content-start mb-3">
                                <div class="bg-light rounded-3 px-3 py-2" style="max-width:70%;">
                                    <div class="text-dark">{{ $msg->contenu }}</div>
                                    <div style="font-size:10px;color:#999;text-align:right;" class="mt-1">{{ $msg->created_at->format('H:i') }}</div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-comment fa-3x mb-3 opacity-25"></i>
                            <p>Démarrez la conversation !</p>
                        </div>
                    @endforelse
                </div>
                <div class="card-footer bg-white p-3 border-top">
                    <form action="{{ route('messages.envoyer', $conversation) }}" method="POST" class="d-flex gap-2">
                        @csrf
                        <input type="text" name="contenu" class="form-control rounded-pill" placeholder="Écrire un message..." required autocomplete="off">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    const container = document.getElementById('messagesContainer');
    if(container) container.scrollTop = container.scrollHeight;
</script>
@endsection
