<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Material;
use App\Models\Quiz;
use App\Models\User;
use App\Models\UserProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Statistik
        $totalUsers = User::where('role', 'user')->count();
        $totalMaterials = Material::count();
        $totalQuizzes = Quiz::count();
        $totalCategories = Category::count();

        // Data untuk grafik aktivitas (7 hari terakhir)
        $activityData = UserProgress::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [now()->subDays(6), now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $chartLabels = [];
        $chartData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = now()->subDays($i)->format('d M');
            $chartData[] = $activityData->where('date', $date)->first()->count ?? 0;
        }

        // Progress terbaru
        $recentProgress = UserProgress::with(['user', 'material'])
            ->latest()
            ->take(5)
            ->get();

        // Materi terpopuler
        $popularMaterials = Material::with('category')
            ->withCount('userProgress')
            ->orderBy('user_progress_count', 'desc')
            ->take(5)
            ->get();

        // User teraktif
        $activeUsers = User::where('role', 'user')
            ->withCount('progress')
            ->orderBy('progress_count', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalMaterials', 
            'totalQuizzes',
            'totalCategories',
            'chartLabels',
            'chartData',
            'recentProgress',
            'popularMaterials',
            'activeUsers'
        ));
    }

    // Users Management
    public function users()
    {
        $users = User::where('role', 'user')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,user'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        return redirect()->route('admin.users')->with('success', 'User berhasil ditambahkan');
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:admin,user'
        ]);

        $user->update($request->only(['name', 'email', 'role']));

        return redirect()->route('admin.users')->with('success', 'User berhasil diperbarui');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['success' => true]);
    }

    // Categories Management
    public function categories()
    {
        $categories = Category::withCount('materials')->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string'
        ]);

        Category::create($request->all());

        return redirect()->route('admin.categories')->with('success', 'Kategori berhasil ditambahkan');
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string'
        ]);

        $category->update($request->all());

        return redirect()->route('admin.categories')->with('success', 'Kategori berhasil diperbarui');
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['success' => true]);
    }

    // Materials Management
    public function materials()
    {
        $materials = Material::with('category')->paginate(10);
        return view('admin.materials.index', compact('materials'));
    }

    public function storeMaterial(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:text,pdf,video,image',
            'order' => 'nullable|integer|min:0',
            'file' => 'nullable|file|max:10240' // 10MB max
        ]);

        $data = $request->except('file');
        
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('materials', $filename, 'public');
            $data['file_path'] = $path;
        }

        Material::create($data);

        return redirect()->route('admin.materials')->with('success', 'Materi berhasil ditambahkan');
    }

    public function updateMaterial(Request $request, $id)
    {
        $material = Material::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:text,pdf,video,image',
            'order' => 'nullable|integer|min:0',
            'file' => 'nullable|file|max:10240'
        ]);

        $data = $request->except('file');
        
        if ($request->hasFile('file')) {
            // Delete old file
            if ($material->file_path) {
                Storage::disk('public')->delete($material->file_path);
            }
            
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('materials', $filename, 'public');
            $data['file_path'] = $path;
        }

        $material->update($data);

        return redirect()->route('admin.materials')->with('success', 'Materi berhasil diperbarui');
    }

    public function deleteMaterial($id)
    {
        $material = Material::findOrFail($id);
        
        // Delete file if exists
        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }
        
        $material->delete();

        return response()->json(['success' => true]);
    }

    // Quizzes Management
    public function quizzes()
    {
        $quizzes = Quiz::with('material')->paginate(10);
        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function storeQuiz(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'material_id' => 'required|exists:materials,id',
            'duration' => 'required|integer|min:1',
            'passing_grade' => 'required|integer|min:0|max:100'
        ]);

        Quiz::create($request->all());

        return redirect()->route('admin.quizzes')->with('success', 'Kuis berhasil ditambahkan');
    }

    public function updateQuiz(Request $request, $id)
    {
        $quiz = Quiz::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'material_id' => 'required|exists:materials,id',
            'duration' => 'required|integer|min:1',
            'passing_grade' => 'required|integer|min:0|max:100'
        ]);

        $quiz->update($request->all());

        return redirect()->route('admin.quizzes')->with('success', 'Kuis berhasil diperbarui');
    }

    public function deleteQuiz($id)
    {
        $quiz = Quiz::findOrFail($id);
        $quiz->delete();

        return response()->json(['success' => true]);
    }

    public function quizQuestions($id)
    {
        $quiz = Quiz::with(['material', 'questions'])->findOrFail($id);
        return view('admin.quizzes.questions', compact('quiz'));
    }

    // Reports
    public function reports()
    {
        // Laporan progress user
        $userProgress = UserProgress::with(['user', 'material'])
            ->where('status', 'completed')
            ->latest()
            ->paginate(15);

        return view('admin.reports.index', compact('userProgress'));
    }

    public function exportReport(Request $request)
    {
        $format = $request->get('format', 'pdf');
        
        // For now, just redirect back with a message
        // In a real application, you would implement actual export functionality
        return redirect()->route('admin.reports')->with('success', "Export {$format} akan segera tersedia");
    }
}
