<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\HasilUjian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics for admin
     */
    public function index()
    {
        // Total counts
        $totalUsers = User::where('role', 'User')->count();
        $totalAdmins = User::where('role', 'Admin')->count();
        $totalKelas = Kelas::count();
        $totalMateri = Materi::count();

        // Calculate active enrollments (unique users with kelas_user)
        $activeEnrollments = DB::table('kelas_user')
            ->distinct('user_id')
            ->count('user_id');

        // Calculate average completion rate based on kelas_user progress
        $completionRate = 0;
        if ($activeEnrollments > 0) {
            $averageProgress = DB::table('kelas_user')
                ->avg('progress');
            $completionRate = round($averageProgress ?? 0, 1);
        }

        // Recent activity - last 7 days enrollments
        $recentEnrollments = DB::table('kelas_user')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(DISTINCT user_id) as count'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Course popularity - top 5 courses by enrollment
        $popularCourses = Kelas::withCount('users as enrollment_count')
            ->orderBy('enrollment_count', 'desc')
            ->take(5)
            ->get(['id', 'nama', 'kode'])
            ->map(function ($kelas) {
                return [
                    'id' => $kelas->id,
                    'nama' => $kelas->nama,
                    'kode' => $kelas->kode,
                    'enrollments' => $kelas->enrollment_count ?? 0
                ];
            });

        // Exam statistics
        $totalExams = HasilUjian::count();
        $averageScore = HasilUjian::avg('nilai') ?? 0;
        $passRate = 0;
        if ($totalExams > 0) {
            $passed = HasilUjian::where('nilai', '>=', 70)->count();
            $passRate = round(($passed / $totalExams) * 100, 1);
        }

        // Monthly user registrations - last 6 months
        $monthlyRegistrations = User::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        return response()->json([
            'data' => [
                'stats' => [
                    'totalUsers' => $totalUsers,
                    'totalAdmins' => $totalAdmins,
                    'totalKelas' => $totalKelas,
                    'totalMateri' => $totalMateri,
                    'activeEnrollments' => $activeEnrollments,
                    'completionRate' => $completionRate,
                    'totalExams' => $totalExams,
                    'averageScore' => round($averageScore, 1),
                    'passRate' => $passRate
                ],
                'charts' => [
                    'recentEnrollments' => $recentEnrollments,
                    'popularCourses' => $popularCourses,
                    'monthlyRegistrations' => $monthlyRegistrations
                ]
            ]
        ]);
    }
}
