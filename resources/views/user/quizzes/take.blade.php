@extends('layouts.app')

@section('title', 'Ambil Kuis: ' . $quiz->title . ' - Edukasi Platform')
@section('page-title', 'Ambil Kuis: ' . $quiz->title)

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <!-- Quiz Header -->
        <div class="card mb-4">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="card-title mb-1">{{ $quiz->title }}</h5>
                        <p class="text-muted mb-0">{{ $quiz->material->title }}</p>
                    </div>
                    <div class="col-md-4 text-end">
                        @if($quiz->time_limit)
                        <div class="timer-container">
                            <div class="timer-display">
                                <i class="fas fa-clock text-warning me-2"></i>
                                <span id="timer" class="fw-bold">--:--</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <small class="text-muted d-block">Total Pertanyaan</small>
                        <strong>{{ $quiz->questions->count() }}</strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Nilai Minimum</small>
                        <strong>{{ $quiz->passing_grade }}%</strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Durasi</small>
                        <strong>{{ $quiz->time_limit ?? 'Tidak Terbatas' }}</strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Progress</small>
                        <strong><span id="currentQuestion">1</span>/{{ $quiz->questions->count() }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quiz Form -->
        <form id="quizForm" method="POST" action="{{ route('quizzes.submit', $quiz->id) }}">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div id="questionsContainer">
                        @foreach($quiz->questions as $index => $question)
                        <div class="question-item {{ $index === 0 ? 'active' : 'd-none' }}" data-question="{{ $index + 1 }}">
                            <div class="question-header mb-4">
                                <h5 class="mb-3">
                                    <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                    {{ $question->question }}
                                </h5>
                                @if($question->image)
                                <div class="question-image mb-3">
                                    <img src="{{ asset('storage/' . $question->image) }}" 
                                         alt="Question Image" 
                                         class="img-fluid rounded" 
                                         style="max-height: 200px;">
                                </div>
                                @endif
                            </div>

                            <div class="options-container">
                                @foreach(['A', 'B', 'C', 'D'] as $option)
                                @php
                                    $optionKey = 'option_' . strtolower($option);
                                    $optionValue = $question->$optionKey;
                                @endphp
                                @if($optionValue)
                                <div class="option-item mb-3">
                                    <input type="radio" 
                                           name="answers[{{ $question->id }}]" 
                                           id="q{{ $question->id }}_{{ $option }}" 
                                           value="{{ $option }}" 
                                           class="btn-check">
                                    <label class="btn btn-outline-primary w-100 text-start" 
                                           for="q{{ $question->id }}_{{ $option }}">
                                        <span class="option-label me-3">{{ $option }}.</span>
                                        {{ $optionValue }}
                                    </label>
                                </div>
                                @endif
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-secondary" id="prevBtn" style="display: none;">
                            <i class="fas fa-arrow-left me-2"></i>Sebelumnya
                        </button>
                        <div class="ms-auto">
                            <button type="button" class="btn btn-primary" id="nextBtn">
                                Selanjutnya<i class="fas fa-arrow-right ms-2"></i>
                            </button>
                            <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                                <i class="fas fa-check me-2"></i>Selesai
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Question Navigation -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Navigasi Pertanyaan</h6>
            </div>
            <div class="card-body">
                <div class="question-nav">
                    @foreach($quiz->questions as $index => $question)
                    <button type="button" 
                            class="btn btn-sm btn-outline-secondary question-nav-btn" 
                            data-question="{{ $index + 1 }}">
                        {{ $index + 1 }}
                    </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Result Modal -->
<div class="modal fade" id="resultModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hasil Kuis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div id="resultContent">
                    <!-- Result content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('quizzes') }}" class="btn btn-secondary">Kembali ke Daftar Kuis</a>
                <a href="{{ route('materials.show', $quiz->material_id) }}" class="btn btn-primary">Lihat Materi</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let currentQuestion = 1;
    const totalQuestions = {{ $quiz->questions->count() }};
    let timeLeft = {{ $quiz->time_limit ? $quiz->time_limit * 60 : 0 }};
    let timer;

    // Initialize timer
    if (timeLeft > 0) {
        updateTimer();
        timer = setInterval(function() {
            timeLeft--;
            updateTimer();
            if (timeLeft <= 0) {
                clearInterval(timer);
                submitQuiz();
            }
        }, 1000);
    }

    function updateTimer() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        $('#timer').text(`${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`);
        
        if (timeLeft <= 300) { // 5 minutes warning
            $('#timer').addClass('text-danger');
        }
    }

    function showQuestion(questionNumber) {
        $('.question-item').addClass('d-none').removeClass('active');
        $(`.question-item[data-question="${questionNumber}"]`).removeClass('d-none').addClass('active');
        
        $('#currentQuestion').text(questionNumber);
        updateNavigationButtons();
        updateQuestionNav();
    }

    function updateNavigationButtons() {
        $('#prevBtn').toggle(currentQuestion > 1);
        $('#nextBtn').toggle(currentQuestion < totalQuestions);
        $('#submitBtn').toggle(currentQuestion === totalQuestions);
    }

    function updateQuestionNav() {
        $('.question-nav-btn').removeClass('btn-primary btn-secondary').addClass('btn-outline-secondary');
        $(`.question-nav-btn[data-question="${currentQuestion}"]`).removeClass('btn-outline-secondary').addClass('btn-primary');
    }

    // Navigation buttons
    $('#nextBtn').click(function() {
        if (currentQuestion < totalQuestions) {
            currentQuestion++;
            showQuestion(currentQuestion);
        }
    });

    $('#prevBtn').click(function() {
        if (currentQuestion > 1) {
            currentQuestion--;
            showQuestion(currentQuestion);
        }
    });

    // Question navigation
    $('.question-nav-btn').click(function() {
        currentQuestion = parseInt($(this).data('question'));
        showQuestion(currentQuestion);
    });

    // Form submission
    $('#quizForm').submit(function(e) {
        e.preventDefault();
        submitQuiz();
    });

    function submitQuiz() {
        const formData = new FormData($('#quizForm')[0]);
        
        $.ajax({
            url: $('#quizForm').attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showResult(response);
                }
            },
            error: function() {
                alert('Terjadi kesalahan. Silakan coba lagi.');
            }
        });
    }

    function showResult(result) {
        let resultHtml = '';
        
        if (result.passed) {
            resultHtml = `
                <div class="text-success mb-4">
                    <i class="fas fa-trophy fa-4x mb-3"></i>
                    <h3>Selamat! Anda Lulus!</h3>
                    <p class="text-muted">Anda berhasil menyelesaikan kuis dengan baik</p>
                </div>
            `;
        } else {
            resultHtml = `
                <div class="text-warning mb-4">
                    <i class="fas fa-exclamation-triangle fa-4x mb-3"></i>
                    <h3>Belum Lulus</h3>
                    <p class="text-muted">Jangan menyerah, coba lagi!</p>
                </div>
            `;
        }

        resultHtml += `
            <div class="row text-center">
                <div class="col-md-4">
                    <h4 class="text-primary">${result.score}%</h4>
                    <small class="text-muted">Skor Anda</small>
                </div>
                <div class="col-md-4">
                    <h4 class="text-success">${result.correct_answers}/${result.total_questions}</h4>
                    <small class="text-muted">Jawaban Benar</small>
                </div>
                <div class="col-md-4">
                    <h4 class="text-info">{{ $quiz->passing_grade }}%</h4>
                    <small class="text-muted">Nilai Minimum</small>
                </div>
            </div>
        `;

        $('#resultContent').html(resultHtml);
        $('#resultModal').modal('show');
    }

    // Initialize first question
    showQuestion(1);
});
</script>
@endsection

@section('styles')
<style>
.timer-container {
    background: #f8f9fa;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    border: 2px solid #ffc107;
}

.timer-display {
    font-size: 1.25rem;
}

.question-item {
    min-height: 400px;
}

.option-item label {
    padding: 1rem;
    border-radius: 0.375rem;
    transition: all 0.2s;
}

.option-item label:hover {
    background-color: #e9ecef;
}

.option-label {
    font-weight: bold;
    color: #6c757d;
}

.question-nav {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.question-nav-btn {
    min-width: 40px;
}

.question-image {
    text-align: center;
}
</style>
@endsection 