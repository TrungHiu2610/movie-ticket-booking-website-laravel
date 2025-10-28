<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeatType;
use Illuminate\Http\Request;

class SeatTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = SeatType::withCount('seats');

        // Search
        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $allowedSorts = ['name', 'surcharge', 'created_at'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest();
        }

        $seatTypes = $query->paginate(10);
        return view('admin.seat-types.index', compact('seatTypes'));
    }

    public function create()
    {
        return view('admin.seat-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:seat_types,name',
            'surcharge' => 'required|numeric|min:0',
        ]);

        SeatType::create($validated);

        return redirect()
            ->route('admin.seat-types.index')
            ->with('success', 'Loại ghế đã được tạo thành công!');
    }

    public function edit(SeatType $seatType)
    {
        return view('admin.seat-types.edit', compact('seatType'));
    }

    public function update(Request $request, SeatType $seatType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:seat_types,name,' . $seatType->id,
            'surcharge' => 'required|numeric|min:0',
        ]);

        $seatType->update($validated);

        return redirect()
            ->route('admin.seat-types.index')
            ->with('success', 'Loại ghế đã được cập nhật thành công!');
    }

    public function destroy(SeatType $seatType)
    {
        // Kiểm tra xem có ghế nào đang sử dụng loại ghế này không
        if ($seatType->seats()->count() > 0) {
            return redirect()
                ->route('admin.seat-types.index')
                ->with('error', 'Không thể xóa loại ghế này vì đang có ghế sử dụng!');
        }

        $seatType->delete();

        return redirect()
            ->route('admin.seat-types.index')
            ->with('success', 'Loại ghế đã được xóa thành công!');
    }
}
