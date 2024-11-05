<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')
            ->withErrors([
                'email' => 'Please login to access the dashboard',
            ])->onlyInput('email');
        }
        $users = User::all();
        return view('auth.users')->with('userss', $users);
    }

        // Method untuk menampilkan form edit user
        public function edit($id)
        {
            $user = User::findOrFail($id);
            return view('auth.edit', compact('user'));
        }

        // Method untuk mengupdate data user
        public function update(Request $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect('users')->with ('error', 'User not found');
        }

        // validasi input
        $request->validate([
            'name' => 'required|string|max:205',
            'email' => 'required|email|max:250|unique:users,email,' . $user->id,
            'password' => 'required|min:8|confirmed', // Menambahkan validasi untuk password
            'photo'=>'image|nullable|max:1999' // Menambahkan validasi untuk photo
            // validasi untuk memastikan file yang diupload adalah image.
        ]);

        // update nama dan email
        $user->name = $request->input('name');
        $user->email = $request->input('email');

        if($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        if ($request->hasFile('photo')) {
            if ($user->photo){
                $oldFile = public_path('storage/'. $user->photo);
                if (File::exists($oldFile)) {
                    File::delete($oldFile);
                }
            }
            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('images', $filename, 'public');
            $user->photo='images/'. $filename;
        }
        $user->save();

        return redirect()->route('users')->with('success', 'Data berhasil diperbarui');
    }

        // Method untuk menghapus data user
        public function destroy($id)
        {
            $user = User::findOrFail($id);
            $user->delete();

            return redirect()->route('users')->with('success', 'User deleted successfully.');
        }
}
