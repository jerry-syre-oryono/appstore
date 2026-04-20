<?php
namespace App\Http\Controllers\API;

use App\Models\App;
use App\Models\UserInstalledApp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AppController extends Controller
{
    public function index()
    {
        $apps = App::with('latestVersion')->where('is_active', true)->get();
        return response()->json($apps);
    }

    public function show($id)
    {
        $app = App::with('versions')->findOrFail($id);
        return response()->json($app);
    }

    public function checkUpdate(Request $request)
    {
        $request->validate(['package' => 'required', 'version_code' => 'required|integer']);
        $app = App::where('package_name', $request->package)->first();
        if (!$app) return response()->json(['update' => false]);

        $latest = $app->versions()->where('is_active', true)->orderByDesc('version_code')->first();
        if (!$latest) return response()->json(['update' => false]);

        if ($latest->version_code > $request->version_code) {
            return response()->json([
                'update' => true,
                'apk_url' => $latest->apk_url,
                'version_name' => $latest->version_name,
                'changelog' => $latest->changelog,
                'is_force' => $latest->is_force,
                'file_hash' => $latest->file_hash,
                'version_code' => $latest->version_code,
            ]);
        }
        return response()->json(['update' => false]);
    }

    public function reportInstalled(Request $request)
    {
        $request->validate(['app_id' => 'required|exists:apps,id', 'version_code' => 'required|integer']);
        UserInstalledApp::updateOrCreate(
            ['user_id' => $request->user()->id, 'app_id' => $request->app_id],
            ['current_version_code' => $request->version_code]
        );
        return response()->json(['success' => true]);
    }
}
