@extends('layouts.app')

@section('title', $material->title . ' - Edukasi Platform')
@section('page-title', $material->title)

@section('content')
<div class="row">
    <!-- Material Content -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">{{ $material->title }}</h5>
                        <small class="text-muted">{{ $material->category->name }}</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-primary me-2">{{ ucfirst($material->type) }}</span>
                        @if($material->duration)
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>{{ $material->duration }} menit
                            </small>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Material Description -->
                <div class="mb-4">
                    <h6>Deskripsi Materi</h6>
                    <p class="text-muted">{{ $material->description }}</p>
                </div>

                <!-- Material Content -->
                <div class="material-content mb-4">
                    <h6>Konten Materi</h6>
                    
                    @if($material->type === 'video')
                        <div class="video-container">
                            @if($material->file_path)
                                <video controls class="w-100" style="max-height: 400px;">
                                    <source src="{{ asset('storage/' . $material->file_path) }}" type="video/mp4">
                                    Browser Anda tidak mendukung tag video.
                                </video>
                            @elseif($material->content)
                                <div class="embed-responsive embed-responsive-16by9">
                                    {!! $material->content !!}
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Video materi akan segera tersedia
                                </div>
                            @endif
                        </div>
                    @elseif($material->type === 'pdf')
                        <div class="pdf-container">
                            @if($material->file_path)
                                <iframe src="{{ asset('storage/' . $material->file_path) }}" 
                                        width="100%" 
                                        height="500px" 
                                        class="border">
                                </iframe>
                            @elseif($material->content)
                                <div class="pdf-content">
                                    {!! $material->content !!}
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    PDF materi akan segera tersedia
                                </div>
                            @endif
                        </div>
                    @elseif($material->type === 'image')
                        <div class="image-container">
                            @if($material->file_path)
                                <img src="{{ asset('storage/' . $material->file_path) }}" 
                                     alt="{{ $material->title }}" 
                                     class="img-fluid rounded">
                            @elseif($material->content)
                                <div class="image-content">
                                    {!! $material->content !!}
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Gambar materi akan segera tersedia
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-content">
                            {!! $material->content !!}
                        </div>
                    @endif
                </div>

                <!-- Progress Tracking -->
                <div class="progress-section">
                    @if($userProgress && $userProgress->status === 'completed')
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            Materi telah selesai dipelajari
                            @if($userProgress->finished_at)
                                <small class="d-block mt-1">
                                    Selesai pada: {{ $userProgress->finished_at->format('d M Y H:i') }}
                                </small>
                            @endif
                        </div>
                    @else
                        <div class="d-grid">
                            <button type="button" class="btn btn-success btn-lg" id="completeMaterial">
                                <i class="fas fa-check me-2"></i>
                                Tandai Sebagai Selesai
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Material Info -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Informasi Materi</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-folder me-2 text-primary"></i>
                        <strong>Kategori:</strong> {{ $material->category->name }}
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-file me-2 text-info"></i>
                        <strong>Tipe:</strong> {{ ucfirst($material->type) }}
                    </li>
                    @if($material->duration)
                    <li class="mb-2">
                        <i class="fas fa-clock me-2 text-warning"></i>
                        <strong>Durasi:</strong> {{ $material->duration }} menit
                    </li>
                    @endif
                    <li class="mb-2">
                        <i class="fas fa-eye me-2 text-success"></i>
                        <strong>Dilihat:</strong> {{ $material->views ?? 0 }} kali
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-calendar me-2 text-secondary"></i>
                        <strong>Dibuat:</strong> {{ $material->created_at->format('d M Y') }}
                    </li>
                </ul>
            </div>
        </div>

        <!-- Related Materials -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Materi Terkait</h6>
            </div>
            <div class="card-body">
                @forelse($relatedMaterials as $related)
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <i class="fas fa-{{ $related->type === 'video' ? 'video' : ($related->type === 'pdf' ? 'file-pdf' : 'image') }} text-primary"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">
                            <a href="{{ route('materials.show', $related->id) }}" class="text-decoration-none">
                                {{ $related->title }}
                            </a>
                        </h6>
                        <small class="text-muted">{{ $related->category->name }}</small>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center">Tidak ada materi terkait</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Quiz Section -->
@if($material->quiz)
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-question-circle me-2"></i>
                    Kuis: {{ $material->quiz->title }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <p class="mb-0">{{ $material->quiz->description }}</p>
                        <small class="text-muted">
                            {{ $material->quiz->questions->count() }} pertanyaan • 
                            Nilai minimum: {{ $material->quiz->passing_grade }}%
                        </small>
                    </div>
                    <div class="col-md-4 text-end">
                        @if($userProgress && $userProgress->quiz_score !== null)
                            <div class="alert alert-info mb-0">
                                <strong>Skor Anda: {{ number_format($userProgress->quiz_score, 1) }}%</strong>
                                @if($userProgress->quiz_score >= $material->quiz->passing_grade)
                                    <span class="badge bg-success ms-2">Lulus</span>
                                @else
                                    <span class="badge bg-danger ms-2">Tidak Lulus</span>
                                @endif
                            </div>
                        @else
                            <a href="{{ route('quizzes.take', $material->quiz->id) }}" class="btn btn-primary">
                                <i class="fas fa-play me-2"></i>Mulai Kuis
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#completeMaterial').click(function() {
        if (confirm('Apakah Anda yakin telah menyelesaikan materi ini?')) {
            $.ajax({
                url: '{{ route("materials.complete", $material->id) }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                }
            });
        }
    });
});
</script>
@endsection

@section('styles')
<style>
.video-container, .pdf-container, .image-container {
    border: 1px solid #e9ecef;
    border-radius: 0.375rem;
    overflow: hidden;
}

.material-content {
    min-height: 300px;
}

.progress-section {
    border-top: 1px solid #e9ecef;
    padding-top: 1.5rem;
}
</style>
@endsection 