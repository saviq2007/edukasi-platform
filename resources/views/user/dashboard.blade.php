@extends('layouts.app')

@section('title', 'Dashboard - Edukasi Platform')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Welcome Card -->
    <div class="col-12 mb-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="text-white mb-2">Selamat datang, {{ auth()->user()->name }}! 👋</h3>
                        <p class="text-white-50 mb-0">Mari lanjutkan perjalanan belajar Anda hari ini</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <i class="fas fa-graduation-cap fa-4x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Progress Overview -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card progress-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Progress Belajar</h5>
                    <i class="fas fa-chart-pie text-primary"></i>
                </div>
                <div class="progress mb-3" style="height: 10px;">
                    <div class="progress-bar bg-success" style="width: {{ $progressPercentage }}%"></div>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Materi Selesai</span>
                    <span class="fw-bold">{{ $completedMaterials }}/{{ $totalMaterials }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quiz Score -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card progress-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Rata-rata Skor</h5>
                    <i class="fas fa-star text-warning"></i>
                </div>
                <h2 class="text-primary mb-2">{{ number_format($averageScore, 1) }}</h2>
                <p class="text-muted mb-0">Dari {{ $totalQuizzesTaken }} kuis yang diambil</p>
            </div>
        </div>
    </div>

    <!-- Study Time -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card progress-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Waktu Belajar</h5>
                    <i class="fas fa-clock text-info"></i>
                </div>
                <h2 class="text-info mb-2">{{ $totalStudyTime }} menit</h2>
                <p class="text-muted mb-0">Minggu ini</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Materi Terbaru -->
    <div class="col-xl-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-book me-2"></i>
                    Materi Terbaru
                </h5>
                <a href="{{ route('materials') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($recentMaterials as $material)
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title mb-0">{{ $material->title }}</h6>
                                    <span class="badge bg-primary">{{ $material->category->name }}</span>
                                </div>
                                <p class="card-text text-muted small">{{ Str::limit($material->description, 100) }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-{{ $material->type === 'video' ? 'video' : ($material->type === 'pdf' ? 'file-pdf' : 'image') }} me-1"></i>
                                        {{ ucfirst($material->type) }}
                                    </small>
                                    <a href="{{ route('materials.show', $material->id) }}" class="btn btn-sm btn-outline-primary">
                                        Mulai Belajar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <p class="text-muted text-center">Belum ada materi tersedia</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Kuis Tersedia -->
    <div class="col-xl-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-question-circle me-2"></i>
                    Kuis Tersedia
                </h5>
            </div>
            <div class="card-body">
                @forelse($availableQuizzes as $quiz)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="mb-0">{{ $quiz->title }}</h6>
                        <small class="text-muted">{{ $quiz->material->title }}</small>
                    </div>
                    <a href="{{ route('quizzes.take', $quiz->id) }}" class="btn btn-sm btn-primary">
                        Mulai
                    </a>
                </div>
                @empty
                <p class="text-muted text-center">Belum ada kuis tersedia</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Grafik Progress -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Progress Belajar Mingguan
                </h5>
            </div>
            <div class="card-body">
                <canvas id="progressChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Grafik Progress
const ctx = document.getElementById('progressChart').getContext('2d');
const progressChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($progressChartLabels) !!},
        datasets: [{
            label: 'Materi Selesai',
            data: {!! json_encode($progressChartData) !!},
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>
@endsection 