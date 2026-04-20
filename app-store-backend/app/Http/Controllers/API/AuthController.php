<?php
namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;

#[OA\Info(title: "App Store API", version: "1.0.0", description: "API documentation for the App Store backend")]
class AuthController extends Controller
{
    #[OA\Post(
        path: "/api/register",
        summary: "Register a new user",
        tags: ["Auth"],
        responses: [
            new OA\Response(response: 200, description: "Success"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        $token = $user->createToken('app-token')->plainTextToken;
        return response()->json(['user' => $user, 'token' => $token]);
    }

    #[OA\Post(
        path: "/api/login",
        summary: "Login user",
        tags: ["Auth"],
        responses: [
            new OA\Response(response: 200, description: "Success"),
            new OA\Response(response: 401, description: "Unauthorized")
        ]
    )]
    public function login(Request $request)
    {
        $request->validate(['email' => 'required|email', 'password' => 'required']);
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        $token = $user->createToken('app-token')->plainTextToken;
        return response()->json(['user' => $user, 'token' => $token]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
