@extends('layouts.staff')

@section('title', 'Quản lý hoàn tiền')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="mb-0"><i class="bi bi-arrow-return-left me-2"></i>Quản lý hoàn tiền vé</h2>
            </div>
        </div>

        <div class="row">
            <!-- Search Section -->
            <div class="col-md-4 mb-4">
                <!-- QR Scanner Card -->
                <div class="card shadow mb-3">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-qr-code-scan me-2"></i>Quét mã QR</h5>
                    </div>
                    <div class="card-body">
                        <!-- Camera View -->
                        <div id="camera-container" class="mb-3">
                            <video id="qr-video" style="width: 100%; border-radius: 8px; display: none;"></video>
                            <canvas id="qr-canvas" style="display: none;"></canvas>
                            <div id="camera-placeholder" class="text-center p-4 bg-light rounded">
                                <i class="bi bi-camera-fill text-secondary" style="font-size: 3rem;"></i>
                                <p class="mt-2 mb-0">Nhấn "Bật camera" để quét QR</p>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button id="start-camera" class="btn btn-success">
                                <i class="bi bi-camera-fill me-2"></i>Bật camera
                            </button>
                            <button id="stop-camera" class="btn btn-danger" style="display: none;">
                                <i class="bi bi-camera-video-off me-2"></i>Tắt camera
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Search Card -->
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-search me-2"></i>Tìm kiếm vé</h5>
                    </div>
                    <div class="card-body">
                        <form id="search-form">
                            <div class="mb-3">
                                <label class="form-label">Tìm theo:</label>
                                <input type="text" class="form-control" id="search-input"
                                    placeholder="Mã vé, Email, Tên KH, Số ghế..." autocomplete="off">
                                <small class="text-muted">VD: BK-20250118-XXXX, user@email.com, A12, B5</small>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search me-2"></i>Tìm kiếm
                            </button>
                        </form>

                        <div id="search-results" class="mt-3"></div>
                    </div>
                </div>

                <!-- Refund Policy Info -->
                <div class="card shadow mt-3">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Chính sách hoàn tiền</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success"></i>
                                <strong>Trước 24h:</strong> Miễn phí (0%)
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-exclamation-circle text-warning"></i>
                                <strong>Từ 2h - 24h:</strong> Phí 10%
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-x-circle text-danger"></i>
                                <strong>Dưới 2h:</strong> Không được hoàn
                            </li>
                            <li>
                                <i class="bi bi-ban text-danger"></i>
                                <strong>Sau khi chiếu:</strong> Không được hoàn
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Booking List & Details -->
            <div class="col-md-8 mb-4">
                <!-- Booking List -->
                <div class="card shadow" id="booking-list-card" style="display: none;">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Danh sách vé tìm được</h5>
                    </div>
                    <div class="card-body" id="booking-list">
                        <!-- Booking list will be loaded here -->
                    </div>
                </div>

                <!-- Booking Details -->
                <div class="card shadow" id="booking-details-card" style="display: none;">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-ticket-detailed me-2"></i>Chi tiết vé & Hoàn tiền</h5>
                    </div>
                    <div class="card-body" id="booking-details">
                        <!-- Booking details will be loaded here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Refund History -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Lịch sử hoàn tiền</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã hoàn tiền</th>
                                        <th>Mã vé</th>
                                        <th>Khách hàng</th>
                                        <th>Số tiền gốc</th>
                                        <th>Phí</th>
                                        <th>Hoàn lại</th>
                                        <th>Trạng thái</th>
                                        <th>Thời gian</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($refunds as $refund)
                                        <tr>
                                            <td><strong class="text-primary">{{ $refund->refund_code }}</strong></td>
                                            <td>{{ $refund->booking->booking_code }}</td>
                                            <td>{{ $refund->booking->user->name }}</td>
                                            <td>{{ number_format($refund->original_amount, 0, ',', '.') }}đ</td>
                                            <td class="text-danger">{{ number_format($refund->refund_fee, 0, ',', '.') }}đ
                                            </td>
                                            <td class="text-success">
                                                <strong>{{ number_format($refund->refund_amount, 0, ',', '.') }}đ</strong>
                                            </td>
                                            <td>
                                                @if ($refund->status === 'completed')
                                                    <span class="badge bg-success">Hoàn thành</span>
                                                @elseif($refund->status === 'pending')
                                                    <span class="badge bg-warning">Chờ xử lý</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $refund->status }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $refund->processed_at ? $refund->processed_at->format('H:i d/m/Y') : '-' }}
                                            </td>
                                            <td>
                                                <a href="{{ route('staff.refund.download', $refund->id) }}"
                                                    class="btn btn-sm btn-outline-primary me-1" target="_blank">
                                                    <i class="bi bi-printer"></i> In phiếu
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted">Chưa có giao dịch hoàn tiền
                                                nào</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $refunds->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/jsqr@1.4.0/dist/jsQR.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchForm = document.getElementById('search-form');
            const searchInput = document.getElementById('search-input');
            const searchResults = document.getElementById('search-results');
            const bookingListCard = document.getElementById('booking-list-card');
            const bookingList = document.getElementById('booking-list');
            const bookingDetailsCard = document.getElementById('booking-details-card');
            const bookingDetails = document.getElementById('booking-details');

            // QR Scanner variables
            const video = document.getElementById('qr-video');
            const canvas = document.getElementById('qr-canvas');
            const canvasContext = canvas.getContext('2d');
            const startBtn = document.getElementById('start-camera');
            const stopBtn = document.getElementById('stop-camera');
            const placeholder = document.getElementById('camera-placeholder');
            let stream = null;
            let scanning = false;
            let lastScannedCode = null;

            // Start camera
            startBtn.addEventListener('click', async function() {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: 'environment'
                        }
                    });
                    video.srcObject = stream;
                    video.play();

                    video.style.display = 'block';
                    placeholder.style.display = 'none';
                    startBtn.style.display = 'none';
                    stopBtn.style.display = 'block';

                    scanning = true;
                    requestAnimationFrame(scanQRCode);

                    showAlert(searchResults, 'info', 'Camera đã bật. Hướng QR code vào camera...');
                } catch (err) {
                    showAlert(searchResults, 'danger', 'Không thể truy cập camera: ' + err.message);
                }
            });

            // Stop camera
            stopBtn.addEventListener('click', function() {
                stopScanning();
            });

            function stopScanning() {
                scanning = false;
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
                video.style.display = 'none';
                placeholder.style.display = 'block';
                startBtn.style.display = 'block';
                stopBtn.style.display = 'none';
            }

            // Scan QR code from video
            function scanQRCode() {
                if (!scanning) return;

                if (video.readyState === video.HAVE_ENOUGH_DATA) {
                    canvas.height = video.videoHeight;
                    canvas.width = video.videoWidth;
                    canvasContext.drawImage(video, 0, 0, canvas.width, canvas.height);

                    const imageData = canvasContext.getImageData(0, 0, canvas.width, canvas.height);
                    const code = jsQR(imageData.data, imageData.width, imageData.height);

                    if (code && code.data) {
                        // Avoid scanning same code multiple times
                        if (code.data !== lastScannedCode) {
                            lastScannedCode = code.data;
                            handleScannedCode(code.data);
                        }
                    }
                }

                requestAnimationFrame(scanQRCode);
            }

            // Handle scanned QR code
            function handleScannedCode(bookingCode) {
                showAlert(searchResults, 'success', 'Đã quét được mã: ' + bookingCode);
                stopScanning();
                scanBookingByQR(bookingCode);
            }

            // Scan booking by QR
            function scanBookingByQR(bookingCode) {
                showLoading(bookingDetails);
                bookingDetailsCard.style.display = 'block';
                bookingListCard.style.display = 'none';

                fetch('{{ route('staff.refund.scan') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            booking_code: bookingCode
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            displayBookingDetails(data.booking, data.refund_info);
                            lastScannedCode = null; // Reset for next scan
                        } else {
                            showAlert(bookingDetails, 'danger', data.message);
                        }
                    })
                    .catch(error => {
                        showAlert(bookingDetails, 'danger', 'Có lỗi xảy ra: ' + error.message);
                    });
            }

            // Search form submit
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const searchTerm = searchInput.value.trim();
                if (!searchTerm) return;

                searchTickets(searchTerm);
            });

            // Search tickets
            function searchTickets(searchTerm) {
                showLoading(searchResults);

                fetch('{{ route('staff.refund.search') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            search_term: searchTerm
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            displayBookingList(data.bookings);
                        } else {
                            showAlert(searchResults, 'warning', data.message);
                            bookingListCard.style.display = 'none';
                            bookingDetailsCard.style.display = 'none';
                        }
                    })
                    .catch(error => {
                        showAlert(searchResults, 'danger', 'Có lỗi xảy ra: ' + error.message);
                    });
            }

            // Display booking list
            function displayBookingList(bookings) {
                bookingDetailsCard.style.display = 'none';
                searchResults.innerHTML = '';

                let html = '<div class="list-group">';
                bookings.forEach(booking => {
                    const statusClass = booking.can_refund ? 'success' : 'danger';
                    html += `
                <a href="#" class="list-group-item list-group-item-action" onclick="viewBookingDetails(${booking.id}); return false;">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">${booking.booking_code}</h6>
                        <small class="badge bg-${statusClass}">${booking.refund_message}</small>
                    </div>
                    <p class="mb-1"><strong>${booking.movie_title}</strong></p>
                    <small>${booking.customer_name} | ${booking.showtime} | Ghế: ${booking.seats}</small>
                </a>
            `;
                });
                html += '</div>';

                bookingList.innerHTML = html;
                bookingListCard.style.display = 'block';
            }

            // View booking details
            window.viewBookingDetails = function(bookingId) {
                showLoading(bookingDetails);
                bookingDetailsCard.style.display = 'block';

                fetch(`/staff/refund/show/${bookingId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            displayBookingDetails(data.booking, data.refund_info);
                        } else {
                            showAlert(bookingDetails, 'danger', data.message);
                        }
                    })
                    .catch(error => {
                        showAlert(bookingDetails, 'danger', 'Có lỗi xảy ra: ' + error.message);
                    });
            };

            // Display booking details with refund info
            function displayBookingDetails(booking, refundInfo) {
                let actionButton = '';
                let alertClass = 'info';
                let alertMessage = refundInfo.message;

                if (refundInfo.can_refund) {
                    actionButton = `
                <button class="btn btn-danger btn-lg w-100 mt-3" onclick="processRefund(${booking.id})">
                    <i class="bi bi-arrow-return-left me-2"></i>Xác nhận hoàn tiền
                </button>
            `;
                    alertClass = 'success';
                } else {
                    alertClass = 'danger';
                }

                bookingDetails.innerHTML = `
            <div class="alert alert-${alertClass}">
                <strong><i class="bi bi-info-circle me-2"></i>${alertMessage}</strong>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-3">Thông tin vé</h5>
                    <table class="table table-borderless">
                        <tr>
                            <th width="150">Mã vé:</th>
                            <td><strong class="text-primary">${booking.booking_code}</strong></td>
                        </tr>
                        <tr>
                            <th>Khách hàng:</th>
                            <td>${booking.customer_name}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>${booking.customer_email}</td>
                        </tr>
                        <tr>
                            <th>Phim:</th>
                            <td><strong>${booking.movie_title}</strong></td>
                        </tr>
                        <tr>
                            <th>Rạp:</th>
                            <td>${booking.cinema_name}</td>
                        </tr>
                        <tr>
                            <th>Phòng:</th>
                            <td>${booking.theater_name}</td>
                        </tr>
                        <tr>
                            <th>Suất chiếu:</th>
                            <td>${booking.showtime}</td>
                        </tr>
                        <tr>
                            <th>Ghế:</th>
                            <td><span class="badge bg-secondary">${booking.seats}</span></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5 class="mb-3">Thông tin hoàn tiền</h5>
                    <table class="table table-borderless">
                        <tr>
                            <th width="150">Số tiền gốc:</th>
                            <td><strong>${booking.total_amount}</strong></td>
                        </tr>
                        <tr>
                            <th>Phí hoàn tiền:</th>
                            <td class="text-danger">
                                <strong>${refundInfo.refund_fee.toLocaleString()}đ</strong>
                                ${refundInfo.fee_percentage > 0 ? `(${refundInfo.fee_percentage}%)` : ''}
                            </td>
                        </tr>
                        <tr>
                            <th>Số tiền hoàn:</th>
                            <td class="text-success">
                                <h4><strong>${refundInfo.refund_amount.toLocaleString()}đ</strong></h4>
                            </td>
                        </tr>
                    </table>

                    ${refundInfo.can_refund ? `
                                            <div class="mt-3">
                                                <label class="form-label">Lý do hoàn tiền:</label>
                                                <textarea id="refund-reason" class="form-control" rows="3" placeholder="Nhập lý do hoàn tiền (tùy chọn)"></textarea>
                                                
                                                <label class="form-label mt-2">Ghi chú của nhân viên:</label>
                                                <textarea id="staff-notes" class="form-control" rows="2" placeholder="Ghi chú nội bộ (tùy chọn)"></textarea>
                                            </div>
                                            ` : ''}
                </div>
            </div>

            ${actionButton}
        `;
            }

            // Process refund
            window.processRefund = function(bookingId) {
                const reason = document.getElementById('refund-reason').value;
                const staffNotes = document.getElementById('staff-notes').value;

                if (!confirm('Xác nhận hoàn tiền cho vé này?\nSố tiền sẽ được hoàn lại cho khách hàng.')) {
                    return;
                }

                fetch(`/staff/refund/process/${bookingId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            reason: reason,
                            staff_notes: staffNotes
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Hoàn tiền thành công!\nMã giao dịch: ' + data.refund.refund_code);
                            location.reload();
                        } else {
                            alert('Lỗi: ' + data.message);
                        }
                    })
                    .catch(error => {
                        alert('Có lỗi xảy ra: ' + error.message);
                    });
            };

            function showLoading(container) {
                container.innerHTML =
                    '<div class="text-center p-3"><div class="spinner-border" role="status"></div></div>';
            }

            function showAlert(container, type, message) {
                container.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
            }
        });
    </script>

    <style>
        .card {
            border-radius: 10px;
            border: none;
        }

        .card-header {
            border-radius: 10px 10px 0 0 !important;
        }

        .list-group-item {
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
        }

        .list-group-item:hover {
            border-left-color: #0d6efd;
            background-color: #f8f9fa;
        }

        .table th {
            color: #6c757d;
            font-weight: 600;
        }

        #qr-video {
            border: 3px solid #198754;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        #camera-placeholder {
            min-height: 200px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 2px dashed #dee2e6;
        }
    </style>
@endsection
