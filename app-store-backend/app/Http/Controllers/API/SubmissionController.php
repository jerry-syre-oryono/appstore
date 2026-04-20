<?php
namespace App\Http\Controllers\API;

use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class SubmissionController extends Controller
{
    public function uploadApk(Request $request)
    {
        $request->validate(['apk' => 'required|file|mimes:apk|max:204800']);
        $path = $request->file('apk')->store('temp', 's3');
        $url = Storage::disk('s3')->url($path);
        return response()->json(['apk_url' => $url, 'temp_path' => $path]);
    }

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

    public function index(Request $request)
    {
        $submissions = Submission::where('user_id', $request->user()->id)->get();
        return response()->json($submissions);
    }
}
