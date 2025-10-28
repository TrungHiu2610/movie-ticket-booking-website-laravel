<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Director;
use Illuminate\Http\Request;

class DirectorController extends Controller
{
    public function index(Request $request)
    {
        $query = Director::query();

        // Search
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

        $directors = $query->paginate(15);
        return view('admin.directors.index', compact('directors'));
    }

    public function create()
    {
        return view('admin.directors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Tên đạo diễn không được để trống',
            'name.max' => 'Tên đạo diễn không được vượt quá 255 ký tự',
        ]);

        Director::create($request->only('name'));

        return redirect()->route('admin.directors.index')->with('success', 'Thêm đạo diễn thành công!');
    }

    public function edit(Director $director)
    {
        return view('admin.directors.edit', compact('director'));
    }

    public function update(Request $request, Director $director)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Tên đạo diễn không được để trống',
            'name.max' => 'Tên đạo diễn không được vượt quá 255 ký tự',
        ]);

        $director->update($request->only('name'));

        return redirect()->route('admin.directors.index')->with('success', 'Cập nhật đạo diễn thành công!');
    }

    public function destroy(Director $director)
    {
        $director->delete();
        return redirect()->route('admin.directors.index')->with('success', 'Xóa đạo diễn thành công!');
    }
}
