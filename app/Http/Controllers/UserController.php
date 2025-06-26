<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Quiz;
use App\Models\UserProgress;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Progress belajar
        $totalMaterials = Material::count();
        $completedMaterials = UserProgress::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();
        $progressPercentage = $totalMaterials > 0 ? ($completedMaterials / $totalMaterials) * 100 : 0;

        // Rata-rata skor kuis
        $averageScore = UserProgress::where('user_id', $user->id)
            ->whereNotNull('quiz_score')
            ->avg('quiz_score') ?? 0;
        
        $totalQuizzesTaken = UserProgress::where('user_id', $user->id)
            ->whereNotNull('quiz_score')
            ->count();

        // Waktu belajar (dummy data untuk sekarang)
        $totalStudyTime = rand(30, 180);

        // Materi terbaru
        $recentMaterials = Material::with('category')
            ->latest()
            ->take(4)
            ->get();

        // Kuis tersedia
        $availableQuizzes = Quiz::with('material')
            ->whereDoesntHave('material.userProgress', function($query) use ($user) {
                $query->where('user_id', $user->id)->whereNotNull('quiz_score');
            })
            ->take(5)
            ->get();

        // Data untuk grafik progress (dummy data untuk sekarang)
        $progressChartLabels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        $progressChartData = [1, 2, 1, 3, 2, 1, 2];

        return view('user.dashboard', compact(
            'progressPercentage',
            'completedMaterials',
            'totalMaterials',
            'averageScore',
            'totalQuizzesTaken',
            'totalStudyTime',
            'recentMaterials',
            'availableQuizzes',
            'progressChartLabels',
            'progressChartData'
        ));
    }

    public function materials(Request $request)
    {
        $query = Material::with(['category', 'userProgress' => function($q) {
            $q->where('user_id', Auth::id());
        }]);

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Search
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        $materials = $query->paginate(12);
        $categories = Category::all();

        return view('user.materials.index', compact('materials', 'categories'));
    }

    public function showMaterial($id)
    {
        $material = Material::with(['category', 'quiz.questions'])->findOrFail($id);
        $userProgress = UserProgress::where('user_id', Auth::id())
            ->where('material_id', $id)
            ->first();

        // Related materials
        $relatedMaterials = Material::with('category')
            ->where('category_id', $material->category_id)
            ->where('id', '!=', $material->id)
            ->take(3)
            ->get();

        return view('user.materials.show', compact('material', 'userProgress', 'relatedMaterials'));
    }

    public function markAsCompleted(Request $request, $id)
    {
        $userProgress = UserProgress::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'material_id' => $id
            ],
            [
                'status' => 'completed',
                'finished_at' => now()
            ]
        );

        return response()->json(['success' => true]);
    }

    public function quizzes(Request $request)
    {
        $query = Quiz::with(['material.category', 'questions', 'material.userProgress' => function($q) {
            $q->where('user_id', Auth::id());
        }]);

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('material', function($q) use ($request) {
                $q->where('category_id', $request->category);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'available':
                    $query->whereDoesntHave('material.userProgress', function($q) {
                        $q->where('user_id', Auth::id())->whereNotNull('quiz_score');
                    });
                    break;
                case 'completed':
                    $query->whereHas('material.userProgress', function($q) {
                        $q->where('user_id', Auth::id())->whereNotNull('quiz_score');
                    });
                    break;
                case 'passed':
                    $query->whereHas('material.userProgress', function($q) {
                        $q->where('user_id', Auth::id())
                          ->whereNotNull('quiz_score')
                          ->whereRaw('quiz_score >= (SELECT passing_grade FROM quizzes WHERE quizzes.material_id = user_progress.material_id)');
                    });
                    break;
                case 'failed':
                    $query->whereHas('material.userProgress', function($q) {
                        $q->where('user_id', Auth::id())
                          ->whereNotNull('quiz_score')
                          ->whereRaw('quiz_score < (SELECT passing_grade FROM quizzes WHERE quizzes.material_id = user_progress.material_id)');
                    });
                    break;
            }
        }

        // Search
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $quizzes = $query->paginate(10);
        $categories = Category::all();

        // Stats
        $totalQuizzes = Quiz::count();
        $completedQuizzes = UserProgress::where('user_id', Auth::id())
            ->whereNotNull('quiz_score')
            ->count();
        $averageScore = UserProgress::where('user_id', Auth::id())
            ->whereNotNull('quiz_score')
            ->avg('quiz_score') ?? 0;
        $passedQuizzes = UserProgress::where('user_id', Auth::id())
            ->whereNotNull('quiz_score')
            ->whereHas('material.quiz', function($q) {
                $q->whereRaw('user_progress.quiz_score >= quizzes.passing_grade');
            })
            ->count();

        return view('user.quizzes.index', compact(
            'quizzes', 
            'categories', 
            'totalQuizzes', 
            'completedQuizzes', 
            'averageScore', 
            'passedQuizzes'
        ));
    }

    public function takeQuiz($id)
    {
        $quiz = Quiz::with(['material', 'questions'])->findOrFail($id);
        return view('user.quizzes.take', compact('quiz'));
    }

    public function submitQuiz(Request $request, $id)
    {
        $quiz = Quiz::with('questions')->findOrFail($id);
        $answers = $request->input('answers', []);
        
        $correctAnswers = 0;
        $totalQuestions = $quiz->questions->count();

        foreach ($quiz->questions as $question) {
            if (isset($answers[$question->id]) && $answers[$question->id] === $question->correct_answer) {
                $correctAnswers++;
            }
        }

        $score = ($correctAnswers / $totalQuestions) * 100;

        // Update progress
        UserProgress::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'material_id' => $quiz->material_id
            ],
            [
                'status' => 'completed',
                'quiz_score' => $score,
                'finished_at' => now()
            ]
        );

        return response()->json([
            'success' => true,
            'score' => $score,
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions,
            'passed' => $score >= $quiz->passing_grade
        ]);
    }

    public function progress()
    {
        $user = Auth::user();
        
        // Overall stats
        $totalMaterials = Material::count();
        $completedMaterials = UserProgress::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();
        $overallProgress = $totalMaterials > 0 ? ($completedMaterials / $totalMaterials) * 100 : 0;
        $averageScore = UserProgress::where('user_id', $user->id)
            ->whereNotNull('quiz_score')
            ->avg('quiz_score') ?? 0;

        // Progress by category
        $progressByCategory = [];
        $categories = Category::with(['materials.userProgress' => function($q) use ($user) {
            $q->where('user_id', $user->id);
        }])->get();

        foreach ($categories as $category) {
            $total = $category->materials->count();
            $completed = $category->materials->filter(function($material) {
                return $material->userProgress && $material->userProgress->status === 'completed';
            })->count();
            
            $avgScore = $category->materials->filter(function($material) {
                return $material->userProgress && $material->userProgress->quiz_score !== null;
            })->avg('userProgress.quiz_score') ?? 0;

            $progressByCategory[$category->name] = [
                'total' => $total,
                'completed' => $completed,
                'percentage' => $total > 0 ? ($completed / $total) * 100 : 0,
                'average_score' => $avgScore
            ];
        }

        // Detailed progress
        $detailedProgress = UserProgress::with('material.category')
            ->where('user_id', $user->id)
            ->get();

        // Chart data (dummy for now)
        $chartLabels = ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'];
        $chartData = [2, 4, 3, 6];

        // Achievements
        $achievements = [
            [
                'title' => 'Pemula',
                'description' => 'Selesaikan 5 materi pertama',
                'icon' => 'star',
                'color' => 'warning',
                'unlocked' => $completedMaterials >= 5
            ],
            [
                'title' => 'Pembelajar Aktif',
                'description' => 'Selesaikan 10 materi',
                'icon' => 'book',
                'color' => 'primary',
                'unlocked' => $completedMaterials >= 10
            ],
            [
                'title' => 'Ahli',
                'description' => 'Raih rata-rata skor 80%',
                'icon' => 'trophy',
                'color' => 'success',
                'unlocked' => $averageScore >= 80
            ]
        ];

        return view('user.progress.index', compact(
            'totalMaterials',
            'completedMaterials',
            'overallProgress',
            'averageScore',
            'progressByCategory',
            'detailedProgress',
            'chartLabels',
            'chartData',
            'achievements'
        ));
    }

    public function profile()
    {
        $user = Auth::user();
        
        // Stats
        $stats = [
            'materials_completed' => UserProgress::where('user_id', $user->id)
                ->where('status', 'completed')
                ->count(),
            'average_score' => UserProgress::where('user_id', $user->id)
                ->whereNotNull('quiz_score')
                ->avg('quiz_score') ?? 0,
            'quizzes_passed' => UserProgress::where('user_id', $user->id)
                ->whereNotNull('quiz_score')
                ->whereHas('material.quiz', function($q) {
                    $q->whereRaw('user_progress.quiz_score >= quizzes.passing_grade');
                })
                ->count(),
            'total_score' => UserProgress::where('user_id', $user->id)
                ->whereNotNull('quiz_score')
                ->sum('quiz_score')
        ];

        return view('user.profile.index', compact('user', 'stats'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string|max:500',
            'bio' => 'nullable|string|max:1000',
        ]);

        $user->update($request->only(['name', 'email', 'phone', 'birth_date', 'address', 'bio']));

        return back()->with('success', 'Profile berhasil diperbarui');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Password berhasil diubah');
    }
}
