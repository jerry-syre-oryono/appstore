<?php
namespace App\Http\Controllers\API;

use App\Models\PushToken;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PushTokenController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['token' => 'required|string', 'device_name' => 'nullable|string']);
        PushToken::updateOrCreate(
            ['user_id' => $request->user()->id, 'token' => $request->token],
            ['device_name' => $request->device_name]
        );
        return response()->json(['success' => true]);
    }
}
