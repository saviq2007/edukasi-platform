@extends('layouts.app')

@section('title', 'Admin Dashboard - Edukasi Platform')
@section('page-title', 'Dashboard Admin')

@section('content')
<div class="row">
    <!-- Statistik Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title text-white-50">Total Users</h5>
                        <h2 class="text-white mb-0">{{ $totalUsers }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title text-white-50">Total Materi</h5>
                        <h2 class="text-white mb-0">{{ $totalMaterials }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-book fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title text-white-50">Total Kuis</h5>
                        <h2 class="text-white mb-0">{{ $totalQuizzes }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-question-circle fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title text-white-50">Kategori</h5>
                        <h2 class="text-white mb-0">{{ $totalCategories }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-tags fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Grafik Aktivitas -->
    <div class="col-xl-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Aktivitas Belajar (7 Hari Terakhir)
                </h5>
            </div>
            <div class="card-body">
                <canvas id="activityChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Progress Terbaru -->
    <div class="col-xl-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-clock me-2"></i>
                    Progress Terbaru
                </h5>
            </div>
            <div class="card-body">
                @forelse($recentProgress as $progress)
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center">
                            <i class="fas fa-user text-white"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">{{ $progress->user->name }}</h6>
                        <small class="text-muted">{{ $progress->material->title }}</small>
                        <div class="progress mt-1" style="height: 4px;">
                            <div class="progress-bar" style="width: {{ $progress->quiz_score ?? 0 }}%"></div>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center">Belum ada progress</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Materi Terpopuler -->
    <div class="col-xl-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-star me-2"></i>
                    Materi Terpopuler
                </h5>
            </div>
            <div class="card-body">
                @forelse($popularMaterials as $material)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="mb-0">{{ $material->title }}</h6>
                        <small class="text-muted">{{ $material->category->name }}</small>
                    </div>
                    <span class="badge bg-primary">{{ $material->user_progress_count }} users</span>
                </div>
                @empty
                <p class="text-muted text-center">Belum ada materi</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- User Teraktif -->
    <div class="col-xl-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-trophy me-2"></i>
                    User Teraktif
                </h5>
            </div>
            <div class="card-body">
                @forelse($activeUsers as $user)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-success rounded-circle d-flex align-items-center justify-content-center me-3">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $user->name }}</h6>
                            <small class="text-muted">{{ $user->email }}</small>
                        </div>
                    </div>
                    <span class="badge bg-success">{{ $user->progress_count }} materi</span>
                </div>
                @empty
                <p class="text-muted text-center">Belum ada user aktif</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Grafik Aktivitas
const ctx = document.getElementById('activityChart').getContext('2d');
const activityChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($chartLabels) !!},
        datasets: [{
            label: 'Aktivitas Belajar',
            data: {!! json_encode($chartData) !!},
            borderColor: '#4f46e5',
            backgroundColor: 'rgba(79, 70, 229, 0.1)',
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