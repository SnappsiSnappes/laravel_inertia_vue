<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // validate
        $fields = $request->validate([
            'avatar' => ['file', 'nullable', 'max:300'],
            'name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed'],
        ]);

        if ($request->hasFile('avatar')) {
            $fields['avatar'] = Storage::disk('public')->put('avatars', $request->avatar);
        }

        // Register
        $user = User::create($fields);

        // Login
        Auth::login($user);

        // Redirect
        return redirect()->route('dashboard')->with('message', 'Greetings Sir!');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard')->with('message', 'Greetings Sir!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.'
        ])->onlyInput('email');
    }

    public function edit(Request $request)
    {
        // Validate input
        $fields = $request->validate([
            'avatar' => ['file', 'nullable', 'max:300'],
            'name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . Auth::id()],
            'password' => ['nullable', 'confirmed'],
        ]);
        // Get the authenticated user
        $user = Auth::user();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete the old avatar if it exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            // Store the new avatar
            $fields['avatar'] = Storage::disk('public')->put('avatars', $request->avatar);
            $user->avatar = $fields['avatar'];
        } else {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $fields['avatar'];
        }


        // Manually update user profile
        $user->name = $fields['name'];
        $user->email = $fields['email'];
        if (!empty($fields['password'])) {
            $user->password = bcrypt($fields['password']);
        }

        $user->save();

        return Response('', 200);
    }


    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function delete_user(Request $request)
    {
        $fields = $request->validate([
            'id' => ['required', 'exists:users,id'],
        ]);
        $user = User::find($fields['id']);

        // Delete the user's avatar if it exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Delete the user
        $user->delete();
        return redirect()->route('home')->with('message', 'Account deleted successfuly!');
    }
}
