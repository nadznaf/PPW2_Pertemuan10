<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'id' => "posts",
            'menu' => "Gallery",
            'galleries' => Post::where('picture', '!=', '')->whereNotNull('picture')
                ->orderBy('created_at', 'desc')
                ->paginate(30)
        ];
        return view('gallery.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('gallery.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'picture' => 'image|nullable|max:1999'
        ]);

        if ($request->hasFile('picture')) {
            $fileNameWithExt = $request->file('picture')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('picture')->getClientOriginalExtension();
            $filenameSimpan = uniqid().time().".{$extension}";
            $path = $request->file('picture')->storeAs('posts_image', $filenameSimpan);
        } else {
            $filenameSimpan = 'noimage.png';
        }

        $post = new Post();
        $post->title = $request->input('title');
        $post->description = $request->input('description');
        $post->picture = $filenameSimpan;
        $post->save();

        return redirect('gallery')->with('success', 'Berhasil menambahkan data baru');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $gallery = Post::findOrFail($id);
        return view('gallery.edit')->with('gallery', $gallery);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $gallery = Post::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'picture' => 'image|nullable|max:1999'
        ]);

        $gallery->title = $request->input('title');
        $gallery->description = $request->input('description');

        if ($request->hasFile('picture')) {
            if ($gallery->picture != 'noimage.png') {
                Storage::delete('posts_image/' . $gallery->picture);
            }
            $fileNameWithExt = $request->file('picture')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('picture')->getClientOriginalExtension();
            $filenameSimpan = uniqid().time().".{$extension}";
            $path = $request->file('picture')->storeAs('posts_image', $filenameSimpan);
            $gallery->picture = $filenameSimpan;
        }

        $gallery->save();
        return redirect('gallery')->with('success', 'Data berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $gallery = Post::findOrFail($id);
        $gallery->delete();

            return redirect('gallery')->with('success', 'User deleted successfully.');
    }
}
