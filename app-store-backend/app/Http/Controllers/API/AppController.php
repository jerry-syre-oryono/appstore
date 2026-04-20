<?php
namespace App\Http\Controllers\API;

use App\Models\App;
use App\Models\UserInstalledApp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;

class AppController extends Controller
{
    #[OA\Get(
        path: "/api/apps",
        summary: "List all active apps",
        security: [["bearerAuth" => []]],
        tags: ["Apps"],
        responses: [
            new OA\Response(response: 200, description: "App list")
        ]
    )]
    public function index()
    {
        $apps = App::with('latestVersion')->where('is_active', true)->get();
        return response()->json($apps);
    }

    #[OA\Get(
        path: "/api/apps/{id}",
        summary: "Get app details with all versions",
        security: [["bearerAuth" => []]],
        tags: ["Apps"],
        parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
        responses: [
            new OA\Response(response: 200, description: "App details"),
            new OA\Response(response: 404, description: "Not found")
        ]
    )]
    public function show($id)
    {
        $app = App::with('versions')->findOrFail($id);
        return response()->json($app);
    }

    #[OA\Get(
        path: "/api/apps/check-update",
        summary: "Check for app updates",
        security: [["bearerAuth" => []]],
        tags: ["Apps"],
        parameters: [
            new OA\Parameter(name: "package", in: "query", required: true, schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "version_code", in: "query", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Update status")
        ]
    )]
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

    #[OA\Post(
        path: "/api/user/installed",
        summary: "Report an installed app version",
        security: [["bearerAuth" => []]],
        tags: ["Apps"],
        requestBody: new OA\RequestBody(content: new OA\JsonContent(properties: [
            new OA\Property(property: "app_id", type: "integer"),
            new OA\Property(property: "version_code", type: "integer")
        ])),
        responses: [
            new OA\Response(response: 200, description: "Reported successfully")
        ]
    )]
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
