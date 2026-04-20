<?php
namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function loginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->role !== 'admin') {
                Auth::logout();
                return back()->withErrors(['email' => 'Unauthorized.']);
            }
            return redirect()->route('admin.dashboard');
        }
        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function index()
    {
        return view('admin.dashboard');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/admin/login');
    }
}
