@extends('layouts.app')

@section('title', 'Progress Belajar - Edukasi Platform')
@section('page-title', 'Progress Belajar')

@section('content')
<div class="row">
    <!-- Progress Overview -->
    <div class="col-12 mb-4">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-book fa-2x text-white mb-2"></i>
                        <h4 class="text-white mb-1">{{ $totalMaterials }}</h4>
                        <p class="text-white-50 mb-0">Total Materi</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x text-white mb-2"></i>
                        <h4 class="text-white mb-1">{{ $completedMaterials }}</h4>
                        <p class="text-white-50 mb-0">Selesai</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-percentage fa-2x text-white mb-2"></i>
                        <h4 class="text-white mb-1">{{ number_format($overallProgress, 1) }}%</h4>
                        <p class="text-white-50 mb-0">Progress</p>
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
        </div>
    </div>

    <!-- Progress Chart -->
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

    <!-- Progress by Category -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>
                    Progress per Kategori
                </h5>
            </div>
            <div class="card-body">
                @forelse($progressByCategory as $categoryName => $categoryProgress)
                <div class="category-progress mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">{{ $categoryName }}</h6>
                        <span class="badge bg-primary">{{ $categoryProgress['completed'] }}/{{ $categoryProgress['total'] }}</span>
                    </div>
                    <div class="progress mb-2" style="height: 10px;">
                        <div class="progress-bar bg-success" 
                             style="width: {{ $categoryProgress['percentage'] }}%"></div>
                    </div>
                    <small class="text-muted">
                        Progress: {{ number_format($categoryProgress['percentage'], 1) }}% | 
                        Rata-rata Skor: {{ number_format($categoryProgress['average_score'], 1) }}%
                    </small>
                </div>
                @empty
                <div class="text-center py-4">
                    <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">Belum ada progress belajar</h6>
                    <p class="text-muted">Mulai belajar untuk melihat progress Anda</p>
                    <a href="{{ route('materials') }}" class="btn btn-primary">
                        <i class="fas fa-play me-2"></i>Mulai Belajar
                    </a>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Detailed Progress -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-table me-2"></i>
                    Detail Progress Materi
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Materi</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Skor Kuis</th>
                                <th>Tanggal Selesai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($detailedProgress as $progress)
                            <tr>
                                <td>
                                    <div>
                                        <strong>{{ $progress->material->title }}</strong>
                                        <br>
                                        <small class="text-muted">{{ Str::limit($progress->material->description, 50) }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $progress->material->category->name }}</span>
                                </td>
                                <td>
                                    @if($progress->status === 'completed')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Selesai
                                        </span>
                                    @elseif($progress->status === 'in_progress')
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock me-1"></i>Sedang Belajar
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-times me-1"></i>Belum Mulai
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($progress->quiz_score !== null)
                                        <span class="badge {{ $progress->quiz_score >= 70 ? 'bg-success' : 'bg-danger' }}">
                                            {{ number_format($progress->quiz_score, 1) }}%
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($progress->finished_at)
                                        {{ $progress->finished_at->format('d M Y H:i') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('materials.show', $progress->material_id) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>Lihat
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-inbox fa-2x text-muted mb-3 d-block"></i>
                                    <p class="text-muted">Belum ada progress belajar</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Achievement Section -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-trophy me-2"></i>
                    Pencapaian
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($achievements as $achievement)
                    <div class="col-md-4 mb-3">
                        <div class="achievement-item text-center p-3 border rounded">
                            <i class="fas fa-{{ $achievement['icon'] }} fa-2x text-{{ $achievement['color'] }} mb-2"></i>
                            <h6 class="mb-1">{{ $achievement['title'] }}</h6>
                            <p class="text-muted small mb-0">{{ $achievement['description'] }}</p>
                            @if($achievement['unlocked'])
                                <span class="badge bg-success mt-2">Tercapai</span>
                            @else
                                <span class="badge bg-secondary mt-2">Belum Tercapai</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Progress Chart
const ctx = document.getElementById('progressChart').getContext('2d');
const progressChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($chartLabels) !!},
        datasets: [{
            label: 'Materi Selesai',
            data: {!! json_encode($chartData) !!},
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

@section('styles')
<style>
.category-progress {
    padding: 1rem;
    border: 1px solid #e9ecef;
    border-radius: 0.375rem;
    background-color: #f8f9fa;
}

.achievement-item {
    transition: transform 0.2s;
}

.achievement-item:hover {
    transform: translateY(-2px);
}

.table th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}
</style>
@endsection 