<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreActorRequest;
use App\Http\Requests\UpdateActorRequest;
use App\Models\Actor;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

class ActorController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }
    public function index(Request $request)
    {
        $query = Actor::query();

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

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

    public function store(StoreActorRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('photo_url')) {
            $validated['photo_url'] = $this->fileUploadService->uploadPhoto($request->file('photo_url'));
        }

        Actor::create($validated);
        return redirect()->route('admin.actors.index')->with('success', 'Thêm diễn viên thành công!');
    }

    public function edit(Actor $actor)
    {
        return view('admin.actors.edit', compact('actor'));
    }

    public function update(UpdateActorRequest $request, Actor $actor)
    {
        $validated = $request->validated();

        if ($request->hasFile('photo_url')) {
            $validated['photo_url'] = $this->fileUploadService->uploadPhoto(
                $request->file('photo_url'),
                $actor->photo_url
            );
        } else {
            unset($validated['photo_url']);
        }

        $actor->update($validated);
        return redirect()->route('admin.actors.index')->with('success', 'Cập nhật diễn viên thành công!');
    }

    public function destroy(Actor $actor)
    {
        if ($actor->photo_url) {
            $this->fileUploadService->deleteFromS3($actor->photo_url);
        }

        $actor->delete();
        return redirect()->route('admin.actors.index')->with('success', 'Xóa diễn viên thành công!');
    }
}


