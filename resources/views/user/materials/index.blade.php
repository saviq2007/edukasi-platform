@extends('layouts.app')

@section('title', 'Materi Belajar - Edukasi Platform')
@section('page-title', 'Materi Belajar')

@section('content')
<div class="row">
    <!-- Filter Section -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('materials') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="category" class="form-label">Kategori</label>
                        <select name="category" id="category" class="form-select">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="type" class="form-label">Tipe Materi</label>
                        <select name="type" id="type" class="form-select">
                            <option value="">Semua Tipe</option>
                            <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>Video</option>
                            <option value="pdf" {{ request('type') == 'pdf' ? 'selected' : '' }}>PDF</option>
                            <option value="image" {{ request('type') == 'image' ? 'selected' : '' }}>Gambar</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="search" class="form-label">Cari Materi</label>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Cari judul materi..." value="{{ request('search') }}">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Filter
                        </button>
                        <a href="{{ route('materials') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Materials Grid -->
    <div class="col-12">
        <div class="row">
            @forelse($materials as $material)
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card h-100 material-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge bg-primary">{{ $material->category->name }}</span>
                            <div class="material-type-icon">
                                @if($material->type === 'video')
                                    <i class="fas fa-video text-danger"></i>
                                @elseif($material->type === 'pdf')
                                    <i class="fas fa-file-pdf text-danger"></i>
                                @else
                                    <i class="fas fa-image text-success"></i>
                                @endif
                            </div>
                        </div>
                        
                        <h5 class="card-title">{{ $material->title }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($material->description, 120) }}</p>
                        
                        <div class="material-meta mb-3">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                {{ $material->duration ?? 'N/A' }} menit
                            </small>
                            <small class="text-muted ms-3">
                                <i class="fas fa-eye me-1"></i>
                                {{ $material->views ?? 0 }} dilihat
                            </small>
                        </div>

                        @if($material->userProgress && $material->userProgress->status === 'completed')
                            <div class="alert alert-success py-2 mb-3">
                                <i class="fas fa-check-circle me-2"></i>
                                Materi telah selesai
                                @if($material->userProgress->quiz_score)
                                    <span class="badge bg-light text-dark ms-2">
                                        Skor: {{ number_format($material->userProgress->quiz_score, 1) }}
                                    </span>
                                @endif
                            </div>
                        @endif

                        <div class="d-grid">
                            <a href="{{ route('materials.show', $material->id) }}" class="btn btn-primary">
                                <i class="fas fa-play me-2"></i>
                                {{ $material->userProgress && $material->userProgress->status === 'completed' ? 'Lihat Ulang' : 'Mulai Belajar' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-book fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak ada materi ditemukan</h5>
                    <p class="text-muted">Coba ubah filter pencarian Anda</p>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($materials->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $materials->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@section('styles')
<style>
.material-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border: 1px solid #e9ecef;
}

.material-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.material-type-icon {
    font-size: 1.5rem;
}

.material-meta {
    border-top: 1px solid #e9ecef;
    padding-top: 0.75rem;
}
</style>
@endsection 