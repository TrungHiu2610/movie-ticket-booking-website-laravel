<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Surcharge;
use Illuminate\Http\Request;

class SurchargeController extends Controller
{
    public function index(Request $request)
    {
        $query = Surcharge::query();

        // Search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $allowedSorts = ['name', 'amount', 'type', 'created_at'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest();
        }

        $surcharges = $query->paginate(15);
        return view('admin.surcharges.index', compact('surcharges'));
    }

    public function create()
    {
        return view('admin.surcharges.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|string|in:percentage,fixed',
            'apply_condition' => 'required|string',
        ], [
            'name.required' => 'Tên phụ thu không được để trống',
            'name.max' => 'Tên phụ thu không được vượt quá 255 ký tự',
            'amount.required' => 'Số tiền không được để trống',
            'amount.min' => 'Số tiền phải lớn hơn hoặc bằng 0',
            'type.required' => 'Loại phụ thu không được để trống',
            'type.in' => 'Loại phụ thu không hợp lệ',
            'apply_condition.required' => 'Điều kiện áp dụng không được để trống',
        ]);

        Surcharge::create($request->only(['name', 'amount', 'type', 'apply_condition']));

        return redirect()->route('admin.surcharges.index')->with('success', 'Thêm phụ thu thành công!');
    }

    public function edit(Surcharge $surcharge)
    {
        return view('admin.surcharges.edit', compact('surcharge'));
    }

    public function update(Request $request, Surcharge $surcharge)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|string|in:percentage,fixed',
            'apply_condition' => 'required|string',
        ], [
            'name.required' => 'Tên phụ thu không được để trống',
            'name.max' => 'Tên phụ thu không được vượt quá 255 ký tự',
            'amount.required' => 'Số tiền không được để trống',
            'amount.min' => 'Số tiền phải lớn hơn hoặc bằng 0',
            'type.required' => 'Loại phụ thu không được để trống',
            'type.in' => 'Loại phụ thu không hợp lệ',
            'apply_condition.required' => 'Điều kiện áp dụng không được để trống',
        ]);

        $surcharge->update($request->only(['name', 'amount', 'type', 'apply_condition']));

        return redirect()->route('admin.surcharges.index')->with('success', 'Cập nhật phụ thu thành công!');
    }

    public function destroy(Surcharge $surcharge)
    {
        $surcharge->delete();
        return redirect()->route('admin.surcharges.index')->with('success', 'Xóa phụ thu thành công!');
    }
}
