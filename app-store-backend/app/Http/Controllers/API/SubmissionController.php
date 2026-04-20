<?php
namespace App\Http\Controllers\API;

use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;

class SubmissionController extends Controller
{
    #[OA\Post(
        path: "/api/submissions/upload-apk",
        summary: "Upload APK to temporary storage",
        security: [["bearerAuth" => []]],
        tags: ["Submissions"],
        requestBody: new OA\RequestBody(content: new OA\MediaType(mediaType: "multipart/form-data", schema: new OA\Schema(properties: [
            new OA\Property(property: "apk", type: "string", format: "binary")
        ]))),
        responses: [
            new OA\Response(response: 200, description: "Upload success")
        ]
    )]
    public function uploadApk(Request $request)
    {
        $request->validate(['apk' => 'required|file|mimes:apk|max:204800']);
        $path = $request->file('apk')->store('temp', 's3');
        $url = Storage::disk('s3')->url($path);
        return response()->json(['apk_url' => $url, 'temp_path' => $path]);
    }

    #[OA\Post(
        path: "/api/submissions",
        summary: "Create a new app submission",
        security: [["bearerAuth" => []]],
        tags: ["Submissions"],
        requestBody: new OA\RequestBody(content: new OA\JsonContent(properties: [
            new OA\Property(property: "app_name", type: "string"),
            new OA\Property(property: "package_name", type: "string"),
            new OA\Property(property: "description", type: "string"),
            new OA\Property(property: "apk_url", type: "string"),
            new OA\Property(property: "temp_path", type: "string")
        ])),
        responses: [
            new OA\Response(response: 201, description: "Submission created")
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string',
            'package_name' => 'required|string|unique:apps,package_name',
            'description' => 'nullable|string',
            'apk_url' => 'required|url',
            'temp_path' => 'required|string',
        ]);
        $submission = Submission::create([
            'user_id' => $request->user()->id,
            'app_name' => $validated['app_name'],
            'package_name' => $validated['package_name'],
            'description' => $validated['description'],
            'apk_url' => $validated['apk_url'],
            'temp_path' => $validated['temp_path'],
            'status' => 'pending',
        ]);
        return response()->json($submission, 201);
    }

    #[OA\Get(
        path: "/api/submissions",
        summary: "List current user's submissions",
        security: [["bearerAuth" => []]],
        tags: ["Submissions"],
        responses: [
            new OA\Response(response: 200, description: "Submission list")
        ]
    )]
    public function index(Request $request)
    {
        $submissions = Submission::where('user_id', $request->user()->id)->get();
        return response()->json($submissions);
    }
}
