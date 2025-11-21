<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cinema;
use Illuminate\Http\Request;

class CinemaController extends Controller
{
    public function index(Request $request)
    {
        $query = Cinema::withCount('theaters');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($city = $request->get('city')) {
            $query->where('city', $city);
        }

        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $allowedSorts = ['name', 'city', 'created_at'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest();
        }

        $cinemas = $query->paginate(15);

        $cities = Cinema::select('city')->distinct()->orderBy('city')->pluck('city', 'city');

        return view('admin.cinemas.index', compact('cinemas', 'cities'));
    }

    public function create()
    {
        return view('admin.cinemas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:255',
        ], [
            'name.required' => 'Tên cụm rạp không được để trống',
            'name.max' => 'Tên cụm rạp không được vượt quá 255 ký tự',
            'address.required' => 'Địa chỉ không được để trống',
            'address.max' => 'Địa chỉ không được vượt quá 500 ký tự',
            'city.required' => 'Thành phố không được để trống',
            'city.max' => 'Thành phố không được vượt quá 255 ký tự',
        ]);

        Cinema::create($request->only(['name', 'address', 'city']));

        return redirect()->route('admin.cinemas.index')->with('success', 'Thêm cụm rạp thành công!');
    }

    public function edit(Cinema $cinema)
    {
        return view('admin.cinemas.edit', compact('cinema'));
    }

    public function update(Request $request, Cinema $cinema)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:255',
        ], [
            'name.required' => 'Tên cụm rạp không được để trống',
            'name.max' => 'Tên cụm rạp không được vượt quá 255 ký tự',
            'address.required' => 'Địa chỉ không được để trống',
            'address.max' => 'Địa chỉ không được vượt quá 500 ký tự',
            'city.required' => 'Thành phố không được để trống',
            'city.max' => 'Thành phố không được vượt quá 255 ký tự',
        ]);

        $cinema->update($request->only(['name', 'address', 'city']));

        return redirect()->route('admin.cinemas.index')->with('success', 'Cập nhật cụm rạp thành công!');
    }

    public function destroy(Cinema $cinema)
    {
        $cinema->delete();
        return redirect()->route('admin.cinemas.index')->with('success', 'Xóa cụm rạp thành công!');
    }
}


