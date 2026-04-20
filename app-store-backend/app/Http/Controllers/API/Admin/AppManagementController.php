<?php
namespace App\Http\Controllers\API\Admin;

use App\Models\App;
use App\Models\AppVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Services\ApkVerifier;
use OpenApi\Attributes as OA;

class AppManagementController extends Controller
{
    #[OA\Post(
        path: "/api/admin/apps",
        summary: "Create a new app (Admin)",
        security: [["bearerAuth" => []]],
        tags: ["Admin App Management"],
        requestBody: new OA\RequestBody(content: new OA\JsonContent(properties: [
            new OA\Property(property: "name", type: "string"),
            new OA\Property(property: "package_name", type: "string"),
            new OA\Property(property: "description", type: "string"),
            new OA\Property(property: "icon_url", type: "string")
        ])),
        responses: [
            new OA\Response(response: 201, description: "App created"),
            new OA\Response(response: 403, description: "Unauthorized")
        ]
    )]
    public function storeApp(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'package_name' => 'required|string|unique:apps',
            'description' => 'nullable|string',
            'icon_url' => 'nullable|url',
        ]);
        $app = App::create($validated);
        return response()->json($app, 201);
    }

    #[OA\Post(
        path: "/api/admin/apps/{appId}/versions",
        summary: "Upload new app version (Admin)",
        security: [["bearerAuth" => []]],
        tags: ["Admin App Management"],
        parameters: [new OA\Parameter(name: "appId", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
        requestBody: new OA\RequestBody(content: new OA\MediaType(mediaType: "multipart/form-data", schema: new OA\Schema(properties: [
            new OA\Property(property: "apk", type: "string", format: "binary"),
            new OA\Property(property: "version_code", type: "integer"),
            new OA\Property(property: "version_name", type: "string"),
            new OA\Property(property: "changelog", type: "string"),
            new OA\Property(property: "is_force", type: "boolean")
        ]))),
        responses: [
            new OA\Response(response: 200, description: "Version uploaded")
        ]
    )]
    public function uploadVersion(Request $request, $appId)
    {
        $request->validate([
            'apk' => 'required|file|mimes:apk|max:204800',
            'version_code' => 'required|integer',
            'version_name' => 'required|string',
            'changelog' => 'nullable|string',
            'is_force' => 'boolean',
        ]);
        $app = App::findOrFail($appId);
        $apkFile = $request->file('apk');
        $hash = ApkVerifier::hash($apkFile);
        $path = $apkFile->store("apps/{$app->package_name}", 's3');
        $url = Storage::disk('s3')->url($path);
        $version = AppVersion::create([
            'app_id' => $app->id,
            'version_code' => $request->version_code,
            'version_name' => $request->version_name,
            'apk_url' => $url,
            'file_hash' => $hash,
            'changelog' => $request->changelog,
            'is_force' => $request->is_force ?? false,
            'file_size' => $apkFile->getSize(),
        ]);
        return response()->json($version);
    }
}
