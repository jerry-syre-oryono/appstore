<?php
namespace App\Http\Controllers\API\Admin;

use App\Models\App;
use App\Models\AppVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Services\ApkVerifier;

class AppManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

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
