@extends('layouts.main')

@section('title', $page->title . ' - Bayan AI Quran')

@section('content')
<div class="py-5 bg-sand">
    <div class="container py-lg-4">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-emerald text-decoration-none">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $page->title }}</li>
                    </ol>
                </nav>

                <div class="card card-glass p-4 p-md-5 border-0 shadow-sm">
                    <h1 class="fw-bold text-emerald mb-4 border-bottom pb-3">{{ $page->title }}</h1>
                    
                    <div class="page-content text-secondary lh-lg fs-5">
                        {!! $page->content !!}
                    </div>

                    <div class="mt-5 pt-4 border-top d-flex justify-content-between align-items-center">
                        <small class="text-muted">Last updated: {{ $page->updated_at->format('F d, Y') }}</small>
                        <a href="{{ route('home') }}" class="btn btn-outline-emerald rounded-pill px-4">
                            <i class="bi bi-arrow-left me-1"></i> Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
