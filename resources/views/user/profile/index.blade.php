@extends('layouts.app')

@section('title', 'Profile - Edukasi Platform')
@section('page-title', 'Profile')

@section('content')
<div class="row">
    <!-- Profile Info -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="profile-avatar mb-3">
                    <div class="avatar-circle">
                        <i class="fas fa-user fa-3x text-white"></i>
                    </div>
                </div>
                <h5 class="card-title">{{ $user->name }}</h5>
                <p class="text-muted">{{ $user->email }}</p>
                <span class="badge bg-primary">{{ ucfirst($user->role) }}</span>
                
                <hr>
                
                <div class="profile-stats">
                    <div class="row text-center">
                        <div class="col-6">
                            <h6 class="text-primary">{{ $stats['materials_completed'] }}</h6>
                            <small class="text-muted">Materi Selesai</small>
                        </div>
                        <div class="col-6">
                            <h6 class="text-success">{{ number_format($stats['average_score'], 1) }}%</h6>
                            <small class="text-muted">Rata-rata Skor</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Statistik Belajar</h6>
            </div>
            <div class="card-body">
                <div class="stat-item d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <i class="fas fa-calendar text-primary me-2"></i>
                        <span>Bergabung Sejak</span>
                    </div>
                    <strong>{{ $user->created_at->format('d M Y') }}</strong>
                </div>
                <div class="stat-item d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <i class="fas fa-clock text-info me-2"></i>
                        <span>Terakhir Login</span>
                    </div>
                    <strong>{{ $user->last_login_at ? $user->last_login_at->format('d M Y') : 'Belum ada' }}</strong>
                </div>
                <div class="stat-item d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <i class="fas fa-trophy text-warning me-2"></i>
                        <span>Kuis Lulus</span>
                    </div>
                    <strong>{{ $stats['quizzes_passed'] }}</strong>
                </div>
                <div class="stat-item d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-star text-success me-2"></i>
                        <span>Total Skor</span>
                    </div>
                    <strong>{{ $stats['total_score'] }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Form -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Edit Profile
                </h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $user->name) }}" 
                                   required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $user->email) }}" 
                                   required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Nomor Telepon</label>
                            <input type="tel" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone', $user->phone) }}">
                            @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="birth_date" class="form-label">Tanggal Lahir</label>
                            <input type="date" 
                                   class="form-control @error('birth_date') is-invalid @enderror" 
                                   id="birth_date" 
                                   name="birth_date" 
                                   value="{{ old('birth_date', $user->birth_date) }}">
                            @error('birth_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Alamat</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  id="address" 
                                  name="address" 
                                  rows="3">{{ old('address', $user->address) }}</textarea>
                        @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="bio" class="form-label">Bio</label>
                        <textarea class="form-control @error('bio') is-invalid @enderror" 
                                  id="bio" 
                                  name="bio" 
                                  rows="3" 
                                  placeholder="Ceritakan sedikit tentang diri Anda...">{{ old('bio', $user->bio) }}</textarea>
                        @error('bio')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                            <i class="fas fa-undo me-2"></i>Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-key me-2"></i>
                    Ubah Password
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.change-password') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="current_password" class="form-label">Password Saat Ini</label>
                            <input type="password" 
                                   class="form-control @error('current_password') is-invalid @enderror" 
                                   id="current_password" 
                                   name="current_password" 
                                   required>
                            @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="new_password" class="form-label">Password Baru</label>
                            <input type="password" 
                                   class="form-control @error('new_password') is-invalid @enderror" 
                                   id="new_password" 
                                   name="new_password" 
                                   required>
                            @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" 
                               class="form-control" 
                               id="new_password_confirmation" 
                               name="new_password_confirmation" 
                               required>
                    </div>

                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-key me-2"></i>Ubah Password
                    </button>
                </form>
            </div>
        </div>

        <!-- Account Settings -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-cog me-2"></i>
                    Pengaturan Akun
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                            <label class="form-check-label" for="emailNotifications">
                                Notifikasi Email
                            </label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="progressReminders" checked>
                            <label class="form-check-label" for="progressReminders">
                                Pengingat Progress
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="quizResults" checked>
                            <label class="form-check-label" for="quizResults">
                                Hasil Kuis
                            </label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="newMaterials">
                            <label class="form-check-label" for="newMaterials">
                                Materi Baru
                            </label>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">Hapus Akun</h6>
                        <small class="text-muted">Tindakan ini tidak dapat dibatalkan</small>
                    </div>
                    <button type="button" class="btn btn-danger" onclick="confirmDeleteAccount()">
                        <i class="fas fa-trash me-2"></i>Hapus Akun
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function resetForm() {
    if (confirm('Apakah Anda yakin ingin mereset form?')) {
        document.querySelector('form').reset();
    }
}

function confirmDeleteAccount() {
    if (confirm('Apakah Anda yakin ingin menghapus akun? Tindakan ini tidak dapat dibatalkan.')) {
        // Implement account deletion
        alert('Fitur penghapusan akun akan segera tersedia.');
    }
}

// Save settings
$('.form-check-input').change(function() {
    // Implement settings save
    console.log('Setting changed:', $(this).attr('id'), $(this).is(':checked'));
});
</script>
@endsection

@section('styles')
<style>
.avatar-circle {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.profile-stats {
    margin-top: 1rem;
}

.stat-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #e9ecef;
}

.stat-item:last-child {
    border-bottom: none;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}
</style>
@endsection 