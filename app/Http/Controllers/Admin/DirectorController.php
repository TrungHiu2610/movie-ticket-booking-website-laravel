<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDirectorRequest;
use App\Http\Requests\UpdateDirectorRequest;
use App\Models\Director;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

class DirectorController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }
    public function index(Request $request)
    {
        $query = Director::query();

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

        $directors = $query->paginate(15);
        return view('admin.directors.index', compact('directors'));
    }

    public function create()
    {
        return view('admin.directors.create');
    }

    public function store(StoreDirectorRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('photo_url')) {
            $data['photo_url'] = $this->fileUploadService->uploadPhoto($request->file('photo_url'));
        }

        Director::create($data);

        return redirect()->route('admin.directors.index')->with('success', 'Thêm đạo diễn thành công!');
    }

    public function edit(Director $director)
    {
        return view('admin.directors.edit', compact('director'));
    }

    public function update(UpdateDirectorRequest $request, Director $director)
    {
        $data = $request->validated();

        if ($request->hasFile('photo_url')) {
            $data['photo_url'] = $this->fileUploadService->uploadPhoto(
                $request->file('photo_url'),
                $director->photo_url
            );
        } else {
            unset($data['photo_url']);
        }

        $director->update($data);

        return redirect()->route('admin.directors.index')->with('success', 'Cập nhật đạo diễn thành công!');
    }

    public function destroy(Director $director)
    {
        if ($director->photo_url) {
            $this->fileUploadService->deleteFromS3($director->photo_url);
        }

        $director->delete();
        return redirect()->route('admin.directors.index')->with('success', 'Xóa đạo diễn thành công!');
    }
}


