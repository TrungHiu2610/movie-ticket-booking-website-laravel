<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class GenreController extends Controller
{
    public function index(Request $request)
    {
        $query = Genre::query();

        // Search by name
        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $allowedSorts = ['name', 'created_at'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest();
        }

        $genres = $query->paginate(15);
        return view('admin.genres.index', compact('genres'));
    }

    public function create()
    {
        return view('admin.genres.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:genres,name',
        ], [
            'name.required' => 'Tên thể loại không được để trống',
            'name.unique' => 'Thể loại này đã tồn tại',
        ]);

        Genre::create($validated);
        return redirect()->route('admin.genres.index')->with('success', 'Thêm thể loại thành công!');
    }

    public function edit(Genre $genre)
    {
        return view('admin.genres.edit', compact('genre'));
    }

    public function update(Request $request, Genre $genre)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:genres,name,' . $genre->id,
        ], [
            'name.required' => 'Tên thể loại không được để trống',
            'name.unique' => 'Thể loại này đã tồn tại',
        ]);

        $genre->update($validated);
        return redirect()->route('admin.genres.index')->with('success', 'Cập nhật thể loại thành công!');
    }

    public function destroy(Genre $genre)
    {
        $genre->delete();
        return redirect()->route('admin.genres.index')->with('success', 'Xóa thể loại thành công!');
    }
}
