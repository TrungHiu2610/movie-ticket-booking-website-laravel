# HƯỚNG DẪN TÍNH GIÁ VÉ

## 📊 Công thức tính giá

### 1. Giá 1 vé (Ticket)

```
Giá vé = base_price (suất chiếu)
       + surcharge (loại ghế)
       + surcharges (phụ thu khác)
```

**Ví dụ:**

-   Suất chiếu: 100,000đ (base_price)
-   Ghế VIP: +45,000đ (seat_type surcharge)
-   Cuối tuần: +20,000đ (surcharge)
-   **Tổng: 165,000đ**

### 2. Tổng booking

```
Tổng booking = sum(giá các vé) - discount (voucher)
```

**Ví dụ:**

-   2 vé VIP cuối tuần: 165,000đ × 2 = 330,000đ
-   Voucher WELCOME2024 giảm 20%: -66,000đ (max 50,000đ)
-   **Tổng: 280,000đ**

## 🔧 Cách sử dụng

### Tính giá vé:

```php
$ticket = Ticket::find(1);
$price = $ticket->calculatePrice();
```

### Tính tổng booking:

```php
$booking = Booking::with(['tickets', 'vouchers'])->find(1);
$total = $booking->calculateTotal();

// Lưu vào database
$booking->updateTotalAmount();

// Xem chi tiết
$breakdown = $booking->getPriceBreakdown();
// [
//     'tickets_total' => 330000,
//     'discount' => 50000,
//     'final_total' => 280000,
//     'tickets_count' => 2
// ]
```

## 📋 Các loại phụ thu (Surcharge)

### DAY_OF_WEEK - Theo ngày trong tuần

```php
[
    'name' => 'Phụ thu cuối tuần',
    'type' => 'DAY_OF_WEEK',
    'amount' => 20000,
    'apply_condition' => '6,7', // 6=Sat, 7=Sun (Laravel: 0=Sun)
]
```

### SPECIFIC_DATE - Theo ngày cụ thể

```php
[
    'name' => 'Phụ thu ngày lễ 30/4',
    'type' => 'SPECIFIC_DATE',
    'amount' => 30000,
    'apply_condition' => '2025-04-30',
]
```

### SCREEN_TYPE - Theo loại phòng chiếu

```php
[
    'name' => 'Phụ thu IMAX',
    'type' => 'SCREEN_TYPE',
    'amount' => 60000,
    'apply_condition' => 'IMAX', // Tìm trong tên theater
]
```

## 💰 Voucher

### Giảm theo phần trăm:

```php
[
    'code' => 'WELCOME2024',
    'discount_percentage' => 20, // 20%
    'max_discount_amount' => 50000, // Tối đa giảm 50k
]
```

### Giảm theo số tiền cố định:

```php
[
    'code' => 'WEEKEND50K',
    'discount_amount' => 50000, // Giảm thẳng 50k
]
```

## 🎯 Workflow đặt vé

1. User chọn suất chiếu (có base_price)
2. User chọn ghế (có seat_type với surcharge)
3. System tính các surcharge áp dụng (cuối tuần, ngày lễ, 3D, IMAX)
4. Tạo Ticket với giá đã tính
5. User nhập voucher (optional)
6. Tạo Booking và tính tổng tiền
7. Lưu total_amount vào database
8. Chuyển sang thanh toán

## 🚀 Admin Panel

Quản lý tại:

-   `/admin/seat-types` - Quản lý loại ghế
-   `/admin/surcharges` - Quản lý phụ thu
-   `/admin/vouchers` - Quản lý voucher
-   `/admin/showtimes` - Quản lý giá cơ bản suất chiếu

## ⚠️ Lưu ý

-   Giá vé được tính động khi tạo booking, KHÔNG lưu sẵn
-   Khi thay đổi giá base_price, surcharge → CHỈ ảnh hưởng booking mới
-   Booking đã tạo GIỮ NGUYÊN giá đã lưu
-   Voucher chỉ áp dụng 1 lần khi tạo booking
