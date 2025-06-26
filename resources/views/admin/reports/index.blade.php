@extends('layouts.app')

@section('title', 'Laporan - Admin')
@section('page-title', 'Laporan Progress User')

@section('content')
<div class="row">
    <!-- Filter Card -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-filter me-2"></i>
                    Filter Laporan
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Kategori</label>
                                <select class="form-select" name="category_id">
                                    <option value="">Semua Kategori</option>
                                    @foreach(\App\Models\Category::all() as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="">Semua Status</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Sedang Belajar</option>
                                    <option value="not_started" {{ request('status') == 'not_started' ? 'selected' : '' }}>Belum Mulai</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Filter
                        </button>
                        <a href="{{ route('admin.reports') }}" class="btn btn-secondary">
                            <i class="fas fa-refresh me-2"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Export Card -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-download me-2"></i>
                    Export Laporan
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <a href="{{ route('admin.reports.export', ['format' => 'pdf'] + request()->query()) }}" class="btn btn-danger w-100">
                            <i class="fas fa-file-pdf me-2"></i>Export PDF
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.reports.export', ['format' => 'excel'] + request()->query()) }}" class="btn btn-success w-100">
                            <i class="fas fa-file-excel me-2"></i>Export Excel
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.reports.export', ['format' => 'csv'] + request()->query()) }}" class="btn btn-info w-100">
                            <i class="fas fa-file-csv me-2"></i>Export CSV
                        </a>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-warning w-100" onclick="printReport()">
                            <i class="fas fa-print me-2"></i>Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Table -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-table me-2"></i>
                    Data Progress User
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="reportTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama User</th>
                                <th>Email</th>
                                <th>Materi</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Skor Kuis</th>
                                <th>Tanggal Selesai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($userProgress as $index => $progress)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $progress->user->name }}</strong>
                                </td>
                                <td>{{ $progress->user->email }}</td>
                                <td>{{ $progress->material->title }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $progress->material->category->name }}</span>
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'completed' => 'success',
                                            'in_progress' => 'warning',
                                            'not_started' => 'danger'
                                        ];
                                        $statusLabels = [
                                            'completed' => 'Selesai',
                                            'in_progress' => 'Sedang Belajar',
                                            'not_started' => 'Belum Mulai'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$progress->status] }}">
                                        {{ $statusLabels[$progress->status] }}
                                    </span>
                                </td>
                                <td>
                                    @if($progress->quiz_score !== null)
                                        <span class="badge bg-{{ $progress->quiz_score >= 70 ? 'success' : 'danger' }}">
                                            {{ $progress->quiz_score }}%
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
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal{{ $progress->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center">Tidak ada data progress</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center">
                    {{ $userProgress->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modals -->
@foreach($userProgress as $progress)
<div class="modal fade" id="detailModal{{ $progress->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Progress</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Nama:</strong> {{ $progress->user->name }}</p>
                        <p><strong>Email:</strong> {{ $progress->user->email }}</p>
                        <p><strong>Materi:</strong> {{ $progress->material->title }}</p>
                        <p><strong>Kategori:</strong> {{ $progress->material->category->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Status:</strong> 
                            <span class="badge bg-{{ $statusColors[$progress->status] }}">
                                {{ $statusLabels[$progress->status] }}
                            </span>
                        </p>
                        <p><strong>Skor Kuis:</strong> 
                            @if($progress->quiz_score !== null)
                                {{ $progress->quiz_score }}%
                            @else
                                -
                            @endif
                        </p>
                        <p><strong>Mulai Belajar:</strong> {{ $progress->created_at->format('d M Y H:i') }}</p>
                        @if($progress->finished_at)
                        <p><strong>Selesai:</strong> {{ $progress->finished_at->format('d M Y H:i') }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

@section('scripts')
<script>
function printReport() {
    window.print();
}
</script>
@endsection 