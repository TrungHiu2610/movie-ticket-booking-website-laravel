@extends('layouts.staff')

@section('title', 'Soát vé')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="mb-0"><i class="bi bi-qr-code-scan me-2"></i>Soát vé điện tử</h2>
            </div>
        </div>

        <div class="row">
            <!-- Camera Scanner Section -->
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-camera-video me-2"></i>Quét mã QR</h5>
                    </div>
                    <div class="card-body">
                        <!-- Camera View -->
                        <div id="camera-container" class="mb-3">
                            <video id="qr-video"
                                style="width: 100%; max-width: 500px; border-radius: 8px; display: none;"></video>
                            <canvas id="qr-canvas" style="display: none;"></canvas>
                            <div id="camera-placeholder" class="text-center p-5 bg-light rounded">
                                <i class="bi bi-camera-fill text-secondary" style="font-size: 4rem;"></i>
                                <p class="mt-3 mb-0">Nhấn "Bật camera" để quét QR code</p>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button id="start-camera" class="btn btn-success btn-lg">
                                <i class="bi bi-camera-fill me-2"></i>Bật camera
                            </button>
                            <button id="stop-camera" class="btn btn-danger btn-lg" style="display: none;">
                                <i class="bi bi-camera-video-off me-2"></i>Tắt camera
                            </button>
                        </div>

                        <div id="scan-result" class="mt-3"></div>
                    </div>
                </div>
            </div>

            <!-- Manual Input Section -->
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="bi bi-keyboard me-2"></i>Nhập mã thủ công</h5>
                    </div>
                    <div class="card-body">
                        <form id="manual-scan-form">
                            <div class="mb-3">
                                <label for="booking-code" class="form-label">Mã đặt vé:</label>
                                <input type="text" class="form-control form-control-lg" id="booking-code"
                                    placeholder="BK-20250118-XXXX" autocomplete="off">
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-search me-2"></i>Tìm kiếm
                            </button>
                        </form>

                        <div id="manual-result" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Details Section -->
        <div class="row" id="booking-details" style="display: none;">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-ticket-detailed me-2"></i>Thông tin vé</h5>
                    </div>
                    <div class="card-body" id="booking-info">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/jsqr@1.4.0/dist/jsQR.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const video = document.getElementById('qr-video');
            const canvas = document.getElementById('qr-canvas');
            const canvasContext = canvas.getContext('2d');
            const startBtn = document.getElementById('start-camera');
            const stopBtn = document.getElementById('stop-camera');
            const placeholder = document.getElementById('camera-placeholder');
            const scanResult = document.getElementById('scan-result');
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

                    showAlert(scanResult, 'info', 'Camera đã bật. Hướng QR code vào camera...');
                } catch (err) {
                    showAlert(scanResult, 'danger', 'Không thể truy cập camera: ' + err.message);
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
                showAlert(scanResult, 'success', 'Đã quét được mã: ' + bookingCode);
                stopScanning();
                lookupBooking(bookingCode);
            }

            // Manual form submit
            document.getElementById('manual-scan-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const bookingCode = document.getElementById('booking-code').value.trim();
                if (bookingCode) {
                    lookupBooking(bookingCode);
                }
            });

            // Lookup booking by code
            function lookupBooking(bookingCode) {
                fetch('{{ route('staff.scanner.scan') }}', {
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
                            displayBookingInfo(data.booking);
                            lastScannedCode = null; // Reset for next scan
                        } else {
                            showAlert(scanResult, 'danger', data.message);
                            document.getElementById('booking-details').style.display = 'none';
                        }
                    })
                    .catch(error => {
                        showAlert(scanResult, 'danger', 'Có lỗi xảy ra: ' + error.message);
                    });
            }

            // Display booking information
            function displayBookingInfo(booking) {
                const detailsSection = document.getElementById('booking-details');
                const infoContainer = document.getElementById('booking-info');

                let statusBadge = '';
                let actionButton = '';

                if (booking.is_checked_in) {
                    statusBadge = '<span class="badge bg-success fs-6">✓ Đã soát vé</span>';
                } else if (booking.can_check_in) {
                    statusBadge = '<span class="badge bg-warning text-dark fs-6">Chờ soát vé</span>';
                    actionButton = `
                <button class="btn btn-success btn-lg w-100" onclick="checkInBooking(${booking.id})">
                    <i class="bi bi-check-circle me-2"></i>Xác nhận soát vé
                </button>
            `;
                } else {
                    statusBadge = '<span class="badge bg-danger fs-6">Không thể soát vé</span>';
                }

                const seats = booking.tickets.map(t => t.seat_name).join(', ');

                infoContainer.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h4 class="mb-3">${statusBadge}</h4>
                    <table class="table table-borderless">
                        <tr>
                            <th width="150">Mã đặt vé:</th>
                            <td><strong class="text-primary">${booking.booking_code}</strong></td>
                        </tr>
                        <tr>
                            <th>Khách hàng:</th>
                            <td>${booking.user_name}</td>
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
                            <td><span class="badge bg-secondary">${seats}</span></td>
                        </tr>
                        <tr>
                            <th>Tổng tiền:</th>
                            <td><strong class="text-success">${booking.total_amount}</strong></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    ${booking.message ? `<div class="alert alert-warning">${booking.message}</div>` : ''}
                    ${actionButton}
                    ${booking.is_checked_in ? `
                                            <div class="alert alert-info mt-3">
                                                <strong>Đã soát vé lúc:</strong> ${booking.checked_in_at}<br>
                                                <strong>Nhân viên:</strong> ${booking.checked_in_by}
                                            </div>
                                        ` : ''}
                </div>
            </div>
        `;

                detailsSection.style.display = 'block';
            }

            // Check in booking
            window.checkInBooking = function(bookingId) {
                if (!confirm('Xác nhận soát vé cho khách hàng này?')) return;

                fetch(`/staff/scanner/check-in/${bookingId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlert(scanResult, 'success', data.message);
                            displayBookingInfo(data.booking);
                        } else {
                            showAlert(scanResult, 'danger', data.message);
                        }
                    })
                    .catch(error => {
                        showAlert(scanResult, 'danger', 'Có lỗi xảy ra: ' + error.message);
                    });
            };

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
        #qr-video {
            border: 3px solid #0d6efd;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        #camera-placeholder {
            min-height: 300px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 2px dashed #dee2e6;
        }

        .card {
            border-radius: 10px;
        }

        .card-header {
            border-radius: 10px 10px 0 0 !important;
        }
    </style>
@endsection

@section('title', 'Soát vé')

@section('page-title', 'Soát vé điện tử')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card scanner-card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-qr-code-scan"></i> Quét mã QR đặt vé</h4>
                    </div>
                    <div class="card-body">
                        <!-- Manual Input -->
                        <div class="mb-4">
                            <label class="form-label">Nhập mã đặt vé:</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                                <input type="text" id="bookingCodeInput" class="form-control"
                                    placeholder="VD: BK-20251118-A1B2" autofocus>
                                <button class="btn btn-primary" onclick="scanBooking()">
                                    <i class="bi bi-search"></i> Quét
                                </button>
                            </div>
                        </div>

                        <div class="text-center text-muted">
                            <p>- HOẶC -</p>
                            <p>Quét mã QR trên vé của khách hàng</p>
                        </div>

                        <!-- Camera Scanner (Optional - requires additional library) -->
                        <div id="qr-reader" class="mt-4" style="display: none;"></div>

                        <!-- Result -->
                        <div id="scanResult" class="mt-4"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .scanner-card {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .booking-info {
            background: var(--card-bg);
            border: 2px solid var(--primary-color);
            border-radius: 8px;
            padding: 20px;
        }

        .booking-info-item {
            padding: 10px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .booking-info-item:last-child {
            border-bottom: none;
        }

        .booking-info-label {
            font-weight: bold;
            color: var(--text-muted);
            display: inline-block;
            width: 150px;
        }

        .booking-info-value {
            color: var(--text-primary);
        }

        .alert-custom {
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }
    </style>

    <script>
        let currentBooking = null;

        // Auto scan when enter is pressed
        document.getElementById('bookingCodeInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                scanBooking();
            }
        });

        function scanBooking() {
            const bookingCode = document.getElementById('bookingCodeInput').value.trim();

            if (!bookingCode) {
                showAlert('Vui lòng nhập mã đặt vé!', 'warning');
                return;
            }

            // Show loading
            document.getElementById('scanResult').innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Đang kiểm tra...</p>
        </div>
    `;

            // Call API to scan
            fetch('{{ route('staff.scanner.scan') }}', {
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
                        currentBooking = data.booking;
                        showBookingInfo(data.booking);
                    } else {
                        showAlert(data.message, 'danger');
                        currentBooking = null;
                    }
                })
                .catch(error => {
                    showAlert('Có lỗi xảy ra: ' + error.message, 'danger');
                    currentBooking = null;
                });
        }

        function showBookingInfo(booking) {
            document.getElementById('scanResult').innerHTML = `
        <div class="booking-info">
            <h5 class="text-success mb-3">
                <i class="bi bi-check-circle-fill"></i> Vé hợp lệ!
            </h5>
            <div class="booking-info-item">
                <span class="booking-info-label">Mã đặt vé:</span>
                <span class="booking-info-value"><strong>${booking.code}</strong></span>
            </div>
            <div class="booking-info-item">
                <span class="booking-info-label">Khách hàng:</span>
                <span class="booking-info-value">${booking.customer_name}</span>
            </div>
            <div class="booking-info-item">
                <span class="booking-info-label">Phim:</span>
                <span class="booking-info-value">${booking.movie}</span>
            </div>
            <div class="booking-info-item">
                <span class="booking-info-label">Rạp:</span>
                <span class="booking-info-value">${booking.cinema}</span>
            </div>
            <div class="booking-info-item">
                <span class="booking-info-label">Phòng:</span>
                <span class="booking-info-value">${booking.theater}</span>
            </div>
            <div class="booking-info-item">
                <span class="booking-info-label">Suất chiếu:</span>
                <span class="booking-info-value">${booking.showtime}</span>
            </div>
            <div class="booking-info-item">
                <span class="booking-info-label">Ghế:</span>
                <span class="booking-info-value"><strong>${booking.seats}</strong></span>
            </div>
            <div class="booking-info-item">
                <span class="booking-info-label">Tổng tiền:</span>
                <span class="booking-info-value"><strong class="text-primary">${booking.total_amount}</strong></span>
            </div>
            
            <div class="text-center mt-4">
                <button class="btn btn-success btn-lg" onclick="confirmCheckIn()">
                    <i class="bi bi-check-circle"></i> XÁC NHẬN SOÁT VÉ
                </button>
                <button class="btn btn-secondary btn-lg ms-2" onclick="resetScanner()">
                    <i class="bi bi-x-circle"></i> Hủy
                </button>
            </div>
        </div>
    `;
        }

        function confirmCheckIn() {
            if (!currentBooking) {
                showAlert('Không có thông tin vé!', 'warning');
                return;
            }

            if (!confirm('Xác nhận soát vé cho khách hàng ' + currentBooking.customer_name + '?')) {
                return;
            }

            fetch('{{ route('staff.scanner.check-in') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        booking_id: currentBooking.id
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('Soát vé thành công! ✅', 'success');
                        playSuccessSound();
                        setTimeout(resetScanner, 2000);
                    } else {
                        showAlert(data.message, 'danger');
                    }
                })
                .catch(error => {
                    showAlert('Có lỗi xảy ra: ' + error.message, 'danger');
                });
        }

        function resetScanner() {
            document.getElementById('bookingCodeInput').value = '';
            document.getElementById('scanResult').innerHTML = '';
            currentBooking = null;
            document.getElementById('bookingCodeInput').focus();
        }

        function showAlert(message, type) {
            const alertClass = type === 'success' ? 'alert-success' :
                type === 'danger' ? 'alert-danger' :
                type === 'warning' ? 'alert-warning' : 'alert-info';

            const icon = type === 'success' ? 'check-circle-fill' :
                type === 'danger' ? 'x-circle-fill' :
                type === 'warning' ? 'exclamation-triangle-fill' : 'info-circle-fill';

            document.getElementById('scanResult').innerHTML = `
        <div class="alert ${alertClass} alert-custom" role="alert">
            <i class="bi bi-${icon}"></i> ${message}
        </div>
    `;
        }

        function playSuccessSound() {
            // Optional: play success beep
            const audio = new Audio(
                'data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYHGGS56e2YTgwOUKXh8LJnGwU7k9r0yX0sBS1+zO/glEILElyx6OqnWBQKRp/g8r5sIQUrgc7y2Yk2BxhkuOntmE4MDlCl4fCyZxsFO5Pa9Ml9LAUtfszv4JRCCxJctejqp1gUCkaf4PK+bCEFK4HO8tmJNgcYZLjp7ZhODA5QpeHwsmcbBTuT2vTJfSwFLX7M7+CUQgsSXLXo6qdYFApGn+DyvmwhBSuBzvLZiTYHGGS46e2YTgwOUKXh8LJnGwU7k9r0yX0sBS1+zO/glEILElyx6OqnWBQKRp/g8r5sIQUrgc7y2Yk2BxhkuOntmE4MDlCl4fCyZxsFO5Pa9Ml9LAUtfszv4JRCCxJctejqp1gUCkaf4PK+bCEFK4HO8tmJNgcYZLjp7ZhODA5QpeHwsmcbBTuT2vTJfSwFLX7M7+CUQgsSXLXo6qdYFApGn+DyvmwhBSuBzvLZiTYHGGS46e2YTgwOUKXh8LJnGwU7k9r0yX0sBS1+zO/glEILElyx6OqnWBQKRp/g8r5sIQUrgc7y2Yk2BxhkuOntmE4MDlCl4fCyZxsFO5Pa9Ml9LAUtfszv4JRCCxJctejqp1gUCkaf4PK+bCEFK4HO8tmJNgcYZLjp7ZhODA5QpeHwsmcbBTuT2vTJfSwFLX7M7+CUQgsSXLXo6qdYFApGn+DyvmwhBSuBzvLZiTYHGGS46e2YTgwOUKXh8LJnGwU7k9r0yX0sBS1+zO/glEILElyx6OqnWBQKRp/g8r5sIQUrgc7y2Yk2BxhkuOntmE4MDlCl4fCyZxsFO5Pa9Ml9LAUtfszv4JRCCxJctejqp1gUCkaf4PK+bCEFK4HO8tmJNgcYZLjp7ZhODA5QpeHwsmcbBTuT2vTJfSwFLX7M7+CUQgsSXLXo6qdYFApGn+DyvmwhBSuBzvLZiTYHGGS46e2YTgwOUKXh8LJnGwU7k9r0yX0sBS1+zO/glEILElyx6OqnWBQKRp/g8r5sIQUrgc7y2Yk2BxhkuOntmE4MDlCl4fCyZxsFO5Pa9Ml9LAUtfszv4JRCCxJctejqp1gU='
            );
            audio.play().catch(e => console.log('Audio play failed'));
        }
    </script>
@endsection
