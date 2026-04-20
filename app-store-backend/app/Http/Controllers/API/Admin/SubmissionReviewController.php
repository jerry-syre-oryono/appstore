<?php
namespace App\Http\Controllers\API\Admin;

use App\Models\Submission;
use App\Models\App;
use App\Models\AppVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;

class SubmissionReviewController extends Controller
{
    #[OA\Get(
        path: "/api/admin/submissions",
        summary: "List all pending submissions (Admin)",
        security: [["bearerAuth" => []]],
        tags: ["Admin Submissions"],
        responses: [
            new OA\Response(response: 200, description: "Success")
        ]
    )]
    public function index()
    {
        $submissions = Submission::with('user')->where('status', 'pending')->get();
        return response()->json($submissions);
    }

    #[OA\Patch(
        path: "/api/admin/submissions/{id}",
        summary: "Review a submission (Admin)",
        security: [["bearerAuth" => []]],
        tags: ["Admin Submissions"],
        parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
        requestBody: new OA\RequestBody(content: new OA\JsonContent(properties: [
            new OA\Property(property: "status", type: "string", enum: ["approved", "rejected"]),
            new OA\Property(property: "reviewer_notes", type: "string")
        ])),
        responses: [
            new OA\Response(response: 200, description: "Review successful")
        ]
    )]
    public function review(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'reviewer_notes' => 'nullable|string',
        ]);
        $submission = Submission::findOrFail($id);
        $submission->status = $request->status;
        $submission->reviewer_notes = $request->reviewer_notes;
        $submission->reviewed_by = $request->user()->id;
        $submission->save();

        if ($request->status === 'approved') {
            // Move APK from temp to permanent
            $oldPath = $submission->temp_path;
            $newPath = str_replace('temp', 'apps/' . $submission->package_name, $oldPath);
            Storage::disk('s3')->copy($oldPath, $newPath);
            $newUrl = Storage::disk('s3')->url($newPath);
            // Create app
            $app = App::create([
                'name' => $submission->app_name,
                'package_name' => $submission->package_name,
                'description' => $submission->description,
                'is_active' => true,
            ]);
            AppVersion::create([
                'app_id' => $app->id,
                'version_code' => 1,
                'version_name' => '1.0.0',
                'apk_url' => $newUrl,
                'file_hash' => 'pending_recompute',
                'changelog' => 'Initial release from submission',
                'is_force' => false,
            ]);
            // Delete temp
            Storage::disk('s3')->delete($oldPath);
        }
        return response()->json($submission);
    }
}
