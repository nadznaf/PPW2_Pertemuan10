@extends('auth.layouts')

@section('content')
<form action="{{ route('gallery.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-3 row mt-5">
        <label for="title" class="col-md-4 col-form-label text-md-end">Title</label>
        <div class="col-md-6">
            <input type="text" class="form-control" id="title" name="title" required>
            @error('title')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
    </div>

    <div class="mb-3 row">
        <label for="description" class="col-md-4 col-form-label text-md-end">Description</label>
        <div class="col-md-6">
            <textarea class="form-control" id="description" rows="5" name="description"></textarea>
            @error('description')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
    </div>

    <div class="mb-3 row">
        <label for="input-file" class="col-md-4 col-form-label text-md-end">File Input</label>
        <div class="col-md-6">
            <div class="input-group">
                <input type="file" class="form-control" id="input-file" name="picture" aria-describedby="input-file-label">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 offset-md-4">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
</form>
@endsection
