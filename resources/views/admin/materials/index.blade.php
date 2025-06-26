@extends('layouts.app')

@section('title', 'Manajemen Materi - Admin')
@section('page-title', 'Manajemen Materi')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="fas fa-book me-2"></i>
            Daftar Materi
        </h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMaterialModal">
            <i class="fas fa-plus me-2"></i>Tambah Materi
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
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Tipe</th>
                        <th>Urutan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materials as $index => $material)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $material->title }}</strong>
                            <br>
                            <small class="text-muted">{{ Str::limit($material->description, 50) }}</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $material->category->name }}</span>
                        </td>
                        <td>
                            @php
                                $typeIcons = [
                                    'pdf' => 'fas fa-file-pdf',
                                    'video' => 'fas fa-video',
                                    'image' => 'fas fa-image',
                                    'text' => 'fas fa-file-alt'
                                ];
                                $typeColors = [
                                    'pdf' => 'danger',
                                    'video' => 'primary',
                                    'image' => 'success',
                                    'text' => 'info'
                                ];
                            @endphp
                            <span class="badge bg-{{ $typeColors[$material->type] }}">
                                <i class="{{ $typeIcons[$material->type] }} me-1"></i>
                                {{ strtoupper($material->type) }}
                            </span>
                        </td>
                        <td>{{ $material->order }}</td>
                        <td>
                            @if($material->file_path)
                                <span class="badge bg-success">File Tersedia</span>
                            @else
                                <span class="badge bg-warning">Belum Upload</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewMaterialModal{{ $material->id }}">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editMaterialModal{{ $material->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteMaterial({{ $material->id }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data materi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $materials->links() }}
        </div>
    </div>
</div>

<!-- Add Material Modal -->
<div class="modal fade" id="addMaterialModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Materi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.materials.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Judul Materi</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Urutan</label>
                                <input type="number" class="form-control" name="order" value="0" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kategori</label>
                                <select class="form-select" name="category_id" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach(\App\Models\Category::all() as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tipe Materi</label>
                                <select class="form-select" name="type" required>
                                    <option value="text">Teks</option>
                                    <option value="pdf">PDF</option>
                                    <option value="video">Video</option>
                                    <option value="image">Gambar</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File Materi (Opsional)</label>
                        <input type="file" class="form-control" name="file">
                        <small class="text-muted">Upload file PDF, video, atau gambar sesuai tipe materi</small>
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
function deleteMaterial(materialId) {
    if (confirm('Apakah Anda yakin ingin menghapus materi ini?')) {
        fetch(`/admin/materials/${materialId}`, {
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