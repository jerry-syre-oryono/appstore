<?php
namespace App\Http\Controllers\API;

use App\Models\PushToken;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;

class PushTokenController extends Controller
{
    #[OA\Post(
        path: "/api/push-token",
        summary: "Register FCM token",
        security: [["bearerAuth" => []]],
        tags: ["Notifications"],
        requestBody: new OA\RequestBody(content: new OA\JsonContent(properties: [
            new OA\Property(property: "token", type: "string"),
            new OA\Property(property: "device_name", type: "string")
        ])),
        responses: [
            new OA\Response(response: 200, description: "Success")
        ]
    )]
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
