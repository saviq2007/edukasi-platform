@extends('layouts.app')

@section('title', 'Kuis - Edukasi Platform')
@section('page-title', 'Kuis')

@section('content')
<div class="row">
    <!-- Quiz Stats -->
    <div class="col-12 mb-4">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-question-circle fa-2x text-white mb-2"></i>
                        <h4 class="text-white mb-1">{{ $totalQuizzes }}</h4>
                        <p class="text-white-50 mb-0">Total Kuis</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x text-white mb-2"></i>
                        <h4 class="text-white mb-1">{{ $completedQuizzes }}</h4>
                        <p class="text-white-50 mb-0">Selesai</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-star fa-2x text-white mb-2"></i>
                        <h4 class="text-white mb-1">{{ number_format($averageScore, 1) }}%</h4>
                        <p class="text-white-50 mb-0">Rata-rata Skor</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-trophy fa-2x text-white mb-2"></i>
                        <h4 class="text-white mb-1">{{ $passedQuizzes }}</h4>
                        <p class="text-white-50 mb-0">Lulus</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('quizzes') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="category" class="form-label">Kategori Materi</label>
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
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Belum Diambil</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="passed" {{ request('status') == 'passed' ? 'selected' : '' }}>Lulus</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Tidak Lulus</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="search" class="form-label">Cari Kuis</label>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Cari judul kuis..." value="{{ request('search') }}">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Filter
                        </button>
                        <a href="{{ route('quizzes') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Quizzes Grid -->
    <div class="col-12">
        <div class="row">
            @forelse($quizzes as $quiz)
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card h-100 quiz-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge bg-primary">{{ $quiz->material->category->name }}</span>
                            <div class="quiz-status">
                                @if($quiz->userProgress && $quiz->userProgress->quiz_score !== null)
                                    @if($quiz->userProgress->quiz_score >= $quiz->passing_grade)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Lulus
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times me-1"></i>Tidak Lulus
                                        </span>
                                    @endif
                                @else
                                    <span class="badge bg-warning">
                                        <i class="fas fa-clock me-1"></i>Belum Diambil
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <h5 class="card-title">{{ $quiz->title }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($quiz->description, 100) }}</p>
                        
                        <div class="quiz-meta mb-3">
                            <div class="row text-center">
                                <div class="col-4">
                                    <small class="text-muted d-block">Pertanyaan</small>
                                    <strong>{{ $quiz->questions->count() }}</strong>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted d-block">Nilai Min</small>
                                    <strong>{{ $quiz->passing_grade }}%</strong>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted d-block">Durasi</small>
                                    <strong>{{ $quiz->time_limit ?? '∞' }} min</strong>
                                </div>
                            </div>
                        </div>

                        @if($quiz->userProgress && $quiz->userProgress->quiz_score !== null)
                            <div class="alert alert-info py-2 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="fas fa-star me-2"></i>
                                        Skor: {{ number_format($quiz->userProgress->quiz_score, 1) }}%
                                    </span>
                                    <small>{{ $quiz->userProgress->finished_at->format('d M Y') }}</small>
                                </div>
                            </div>
                        @endif

                        <div class="d-grid">
                            @if($quiz->userProgress && $quiz->userProgress->quiz_score !== null)
                                <a href="{{ route('quizzes.take', $quiz->id) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-redo me-2"></i>Ambil Ulang
                                </a>
                            @else
                                <a href="{{ route('quizzes.take', $quiz->id) }}" class="btn btn-primary">
                                    <i class="fas fa-play me-2"></i>Mulai Kuis
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-question-circle fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak ada kuis ditemukan</h5>
                    <p class="text-muted">Coba ubah filter pencarian Anda</p>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($quizzes->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $quizzes->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@section('styles')
<style>
.quiz-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border: 1px solid #e9ecef;
}

.quiz-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.quiz-meta {
    border-top: 1px solid #e9ecef;
    border-bottom: 1px solid #e9ecef;
    padding: 1rem 0;
}

.quiz-status {
    font-size: 0.875rem;
}
</style>
@endsection 