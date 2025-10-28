<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Actor;
use Illuminate\Http\Request;

class ActorController extends Controller
{
    public function index(Request $request)
    {
        $query = Actor::query();

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

        $actors = $query->paginate(15);
        return view('admin.actors.index', compact('actors'));
    }

    public function create()
    {
        return view('admin.actors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Tên diễn viên không được để trống',
        ]);

        Actor::create($validated);
        return redirect()->route('admin.actors.index')->with('success', 'Thêm diễn viên thành công!');
    }

    public function edit(Actor $actor)
    {
        return view('admin.actors.edit', compact('actor'));
    }

    public function update(Request $request, Actor $actor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Tên diễn viên không được để trống',
        ]);

        $actor->update($validated);
        return redirect()->route('admin.actors.index')->with('success', 'Cập nhật diễn viên thành công!');
    }

    public function destroy(Actor $actor)
    {
        $actor->delete();
        return redirect()->route('admin.actors.index')->with('success', 'Xóa diễn viên thành công!');
    }
}
