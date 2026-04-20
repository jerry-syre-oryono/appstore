<?php
namespace App\Http\Controllers\API\Admin;

use App\Models\Submission;
use App\Models\App;
use App\Models\AppVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class SubmissionReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $submissions = Submission::with('user')->where('status', 'pending')->get();
        return response()->json($submissions);
    }

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
