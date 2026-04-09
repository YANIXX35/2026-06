@extends('layouts.app')
@section('title', 'Contact')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5">
                <h1>Contactez-nous</h1>
                <p class="text-muted lead">Une question ? Nous sommes là pour vous aider.</p>
            </div>
            <div class="row g-4 mb-5">
                <div class="col-md-4 text-center">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto mb-3" style="width:60px;height:60px;">
                        <i class="fas fa-map-marker-alt text-white"></i>
                    </div>
                    <h6 class="fw-bold">Adresse</h6>
                    <p class="text-muted small">Abidjan, Plateau, Côte d'Ivoire</p>
                </div>
                <div class="col-md-4 text-center">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto mb-3" style="width:60px;height:60px;">
                        <i class="fas fa-phone text-white"></i>
                    </div>
                    <h6 class="fw-bold">Téléphone</h6>
                    <p class="text-muted small">(+225) 07 00 00 00 00</p>
                </div>
                <div class="col-md-4 text-center">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto mb-3" style="width:60px;height:60px;">
                        <i class="fas fa-envelope text-white"></i>
                    </div>
                    <h6 class="fw-bold">Email</h6>
                    <p class="text-muted small">contact@antigaspi-ci.com</p>
                </div>
            </div>
            <div class="card border-0 shadow-sm rounded-3 p-5">
                <h5 class="fw-bold mb-4">Envoyer un message</h5>
                <form>
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control" placeholder="Votre nom">
                        </div>
                        <div class="col-md-6">
                            <input type="email" class="form-control" placeholder="Votre email">
                        </div>
                        <div class="col-12">
                            <input type="text" class="form-control" placeholder="Sujet">
                        </div>
                        <div class="col-12">
                            <textarea class="form-control" rows="5" placeholder="Votre message..."></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary rounded-pill px-5 py-3">
                                <i class="fas fa-paper-plane me-2"></i>Envoyer
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
