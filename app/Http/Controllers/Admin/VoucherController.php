<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = Voucher::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->whereRaw('is_active = ?', [$status === 'active' ? true : false]);
        }

        if ($expiry = $request->get('expiry')) {
            if ($expiry === 'valid') {
                $query->where('valid_from', '<=', now())
                    ->where('valid_to', '>=', now())
                    ->whereRaw('is_active = true');
            } elseif ($expiry === 'expired') {
                $query->where('valid_to', '<', now());
            }
        }

        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $allowedSorts = ['code', 'discount_percentage', 'discount_amount', 'valid_from', 'valid_to', 'created_at'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest();
        }

        $vouchers = $query->paginate(15);
        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        return view('admin.vouchers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'expires_at' => 'required|date',
            'usage_limit' => 'nullable|integer|min:1',
        ], [
            'code.required' => 'Mã voucher không được để trống',
            'code.max' => 'Mã voucher không được vượt quá 50 ký tự',
            'code.unique' => 'Mã voucher đã tồn tại',
            'discount_amount.min' => 'Số tiền giảm phải lớn hơn hoặc bằng 0',
            'discount_percentage.min' => 'Phần trăm giảm phải lớn hơn hoặc bằng 0',
            'discount_percentage.max' => 'Phần trăm giảm không được vượt quá 100',
            'max_discount_amount.min' => 'Số tiền giảm tối đa phải lớn hơn hoặc bằng 0',
            'expires_at.required' => 'Ngày hết hạn không được để trống',
            'usage_limit.min' => 'Giới hạn sử dụng phải lớn hơn 0',
        ]);

        Voucher::create([
            'code' => $request->code,
            'discount_amount' => $request->discount_amount,
            'discount_percentage' => $request->discount_percentage,
            'max_discount_amount' => $request->max_discount_amount,
            'expires_at' => $request->expires_at,
            'usage_limit' => $request->usage_limit,
            'usage_count' => 0,
        ]);

        return redirect()->route('admin.vouchers.index')->with('success', 'Thêm voucher thành công!');
    }

    public function edit(Voucher $voucher)
    {
        return view('admin.vouchers.edit', compact('voucher'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code,' . $voucher->id,
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'expires_at' => 'required|date',
            'usage_limit' => 'nullable|integer|min:1',
        ], [
            'code.required' => 'Mã voucher không được để trống',
            'code.max' => 'Mã voucher không được vượt quá 50 ký tự',
            'code.unique' => 'Mã voucher đã tồn tại',
            'discount_amount.min' => 'Số tiền giảm phải lớn hơn hoặc bằng 0',
            'discount_percentage.min' => 'Phần trăm giảm phải lớn hơn hoặc bằng 0',
            'discount_percentage.max' => 'Phần trăm giảm không được vượt quá 100',
            'max_discount_amount.min' => 'Số tiền giảm tối đa phải lớn hơn hoặc bằng 0',
            'expires_at.required' => 'Ngày hết hạn không được để trống',
            'usage_limit.min' => 'Giới hạn sử dụng phải lớn hơn 0',
        ]);

        $voucher->update($request->only(['code', 'discount_amount', 'discount_percentage', 'max_discount_amount', 'expires_at', 'usage_limit']));

        return redirect()->route('admin.vouchers.index')->with('success', 'Cập nhật voucher thành công!');
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return redirect()->route('admin.vouchers.index')->with('success', 'Xóa voucher thành công!');
    }
}
