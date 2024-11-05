<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class LoginRegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except([
            'logout', 'dashboard'
        ]);
    }
    public function register()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:250',
            'email' => 'required|email|max:250|unique:users',
            'password' => 'required|confirmed|min:8',
            'photo' => 'image|nullable|max:1999',
        ]);

        if ($request->hasFile('photo')) {
            $filenameWithExt = $request->file('photo')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('photo')->getClientOriginalExtension();
            $filenameSimpan = $filename. '_'. time(). '.'. $extension;
            $path = $request->file('photo')->storeAs('photos', $filenameSimpan);
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'photo' =>$path
        ]);

        $credentials = $request->only('email', 'password');
        Auth::attempt($credentials);
        $request->session()->regenerate();
        return redirect()->route('dashboard')
            ->withSuccess('You have successfully registered & logged in!');
    }

    public function login()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' =>'required|email|max:250',
            'password' => 'required|min:8'
        ]);

        if (Auth::attempt($credentials)){
            $request->session()->regenerate();

            if (Auth::user()->level === 'admin'){
                return redirect()->route('admin.dashboard')
                    ->withSuccess('You have successfully logged in as an admin!');
            }
            return redirect()->route('dashboard')
                ->withSuccess('You have successfully logged in!');
        }

        return back()->withErrors(['email' => 'Invalid email or password'])->onlyInput('email');
    }

    public function dashboard()
    {
        if (Auth::check()) {
            return view('auth.dashboard');
        }

        return redirect()->route('login')->withErrors([
            'email' => 'Please login t access the dashboard'
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')
            ->withSuccess('You have successfully logged out!');
    }

    public function edit()
    {
        $user = Auth::user();
        return view('edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user(); // Mendapatkan pengguna yang sedang login

        // Check if the user is authenticated
        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'You must be logged in to update your profile.']);
        }

        $request->validate([
            'name' => 'required|string|max:250',
            'email' => 'required|email|max:250|unique:users,email,' . $user->id,
            'photo' => 'image|nullable|max:1999',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        // Cek apakah ada file foto baru yang diunggah
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($user->photo) {
                Storage::delete($user->photo);
            }
            // Simpan foto baru
            $filenameWithExt = $request->file('photo')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('photo')->getClientOriginalExtension();
            $filenameSimpan = $filename . '_' . time() . '.' . $extension;
            $path = $request->file('photo')->storeAs('photos', $filenameSimpan);
            $user->photo = $path;
        }

        // Check if $user is a valid instance before saving
        if (!$user instanceof User) {
            return redirect()->route('dashboard')->withErrors(['message' => 'User instance is invalid.']);
        }

        $user->save(); // Simpan perubahan ke database

        return redirect()->route('dashboard')->withSuccess('Profile updated successfully!');
    }




}
