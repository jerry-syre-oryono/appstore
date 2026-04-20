<?php
namespace App\Http\Controllers\API\Admin;

use App\Models\App;
use App\Models\User;
use App\Models\Submission;
use App\Models\UserInstalledApp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;

class StatsController extends Controller
{
    #[OA\Get(
        path: "/api/admin/stats",
        summary: "Get backend stats (Admin)",
        security: [["bearerAuth" => []]],
        tags: ["Admin Stats"],
        responses: [
            new OA\Response(response: 200, description: "Stats object")
        ]
    )]
    public function index()
    {
        $totalApps = App::count();
        $totalUsers = User::count();
        $pendingSubmissions = Submission::where('status', 'pending')->count();
        
        // Last 7 days installs
        $installs = UserInstalledApp::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->get();
        
        return response()->json([
            'total_apps' => $totalApps,
            'total_users' => $totalUsers,
            'pending_submissions' => $pendingSubmissions,
            'installs' => $installs,
        ]);
    }
}
