@extends('layouts.app')

@section('title', 'Manajemen Kuis - Admin')
@section('page-title', 'Manajemen Kuis')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="fas fa-question-circle me-2"></i>
            Daftar Kuis
        </h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuizModal">
            <i class="fas fa-plus me-2"></i>Tambah Kuis
        </button>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul Kuis</th>
                        <th>Materi</th>
                        <th>Durasi</th>
                        <th>Passing Grade</th>
                        <th>Jumlah Soal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quizzes as $index => $quiz)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $quiz->title }}</strong>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $quiz->material->title }}</span>
                        </td>
                        <td>
                            <span class="badge bg-warning">{{ $quiz->duration }} menit</span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $quiz->passing_grade >= 70 ? 'success' : 'danger' }}">
                                {{ $quiz->passing_grade }}%
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $quiz->questions_count ?? 0 }} soal</span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewQuizModal{{ $quiz->id }}">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editQuizModal{{ $quiz->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="{{ route('admin.quizzes.questions', $quiz->id) }}" class="btn btn-sm btn-success">
                                <i class="fas fa-list"></i>
                            </a>
                            <button class="btn btn-sm btn-danger" onclick="deleteQuiz({{ $quiz->id }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data kuis</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $quizzes->links() }}
        </div>
    </div>
</div>

<!-- Add Quiz Modal -->
<div class="modal fade" id="addQuizModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kuis Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.quizzes.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Judul Kuis</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Materi</label>
                        <select class="form-select" name="material_id" required>
                            <option value="">Pilih Materi</option>
                            @foreach(\App\Models\Material::all() as $material)
                                <option value="{{ $material->id }}">{{ $material->title }} ({{ $material->category->name }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Durasi (menit)</label>
                                <input type="number" class="form-control" name="duration" value="30" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Passing Grade (%)</label>
                                <input type="number" class="form-control" name="passing_grade" value="70" min="0" max="100" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function deleteQuiz(quizId) {
    if (confirm('Apakah Anda yakin ingin menghapus kuis ini?')) {
        fetch(`/admin/quizzes/${quizId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).then(response => {
            if (response.ok) {
                location.reload();
            }
        });
    }
}
</script>
@endsection 