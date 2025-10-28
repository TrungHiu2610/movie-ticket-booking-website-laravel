<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cinema;
use App\Models\Seat;
use App\Models\SeatType;
use App\Models\Theater;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TheaterController extends Controller
{
    public function index(Request $request)
    {
        $query = Theater::with(['cinema', 'seats']);

        // Search
        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        // Filter by cinema
        if ($cinemaId = $request->get('cinema_id')) {
            $query->where('cinema_id', $cinemaId);
        }

        // Filter by screen type
        if ($screenType = $request->get('screen_type')) {
            $query->where('screen_type', $screenType);
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $allowedSorts = ['name', 'total_seats', 'screen_type', 'created_at'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest();
        }

        $theaters = $query->paginate(15);

        // Get cinemas for filter
        $cinemas = Cinema::orderBy('name')->pluck('name', 'id');

        return view('admin.theaters.index', compact('theaters', 'cinemas'));
    }

    public function create()
    {
        $cinemas = Cinema::all();
        $seatTypes = SeatType::all();
        return view('admin.theaters.create', compact('cinemas', 'seatTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cinema_id' => 'required|exists:cinemas,id',
            'name' => 'required|string|max:255',
            'screen_type' => 'required|string|max:255',
            'rows' => 'required|integer|min:1|max:26',
            'columns' => 'required|integer|min:1|max:30',
            'seat_type_id' => 'required|exists:seat_types,id',
        ], [
            'cinema_id.required' => 'Vui lòng chọn cụm rạp',
            'cinema_id.exists' => 'Cụm rạp không tồn tại',
            'name.required' => 'Tên phòng chiếu không được để trống',
            'name.max' => 'Tên phòng chiếu không được vượt quá 255 ký tự',
            'screen_type.required' => 'Loại màn hình không được để trống',
            'rows.required' => 'Số hàng không được để trống',
            'rows.min' => 'Số hàng phải lớn hơn 0',
            'rows.max' => 'Số hàng không được vượt quá 26',
            'columns.required' => 'Số cột không được để trống',
            'columns.min' => 'Số cột phải lớn hơn 0',
            'columns.max' => 'Số cột không được vượt quá 30',
            'seat_type_id.required' => 'Vui lòng chọn loại ghế',
        ]);

        DB::beginTransaction();
        try {
            $theater = Theater::create([
                'cinema_id' => $request->cinema_id,
                'name' => $request->name,
                'screen_type' => $request->screen_type,
            ]);

            for ($row = 0; $row < $request->rows; $row++) {
                $rowChar = chr(65 + $row);
                for ($col = 1; $col <= $request->columns; $col++) {
                    Seat::create([
                        'theater_id' => $theater->id,
                        'seat_type_id' => $request->seat_type_id,
                        'row_char' => $rowChar,
                        'column_number' => $col,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.theaters.index')->with('success', 'Thêm phòng chiếu thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit(Theater $theater)
    {
        $theater->load('cinema');
        $cinemas = Cinema::all();
        return view('admin.theaters.edit', compact('theater', 'cinemas'));
    }

    public function update(Request $request, Theater $theater)
    {
        $request->validate([
            'cinema_id' => 'required|exists:cinemas,id',
            'name' => 'required|string|max:255',
            'screen_type' => 'required|string|max:255',
        ], [
            'cinema_id.required' => 'Vui lòng chọn cụm rạp',
            'name.required' => 'Tên phòng chiếu không được để trống',
            'screen_type.required' => 'Loại màn hình không được để trống',
        ]);

        $theater->update($request->only(['cinema_id', 'name', 'screen_type']));

        return redirect()->route('admin.theaters.index')->with('success', 'Cập nhật phòng chiếu thành công!');
    }

    public function destroy(Theater $theater)
    {
        $theater->seats()->delete();
        $theater->delete();
        return redirect()->route('admin.theaters.index')->with('success', 'Xóa phòng chiếu thành công!');
    }
}
