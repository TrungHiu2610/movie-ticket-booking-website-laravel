@extends('layouts.user')

@section('title', 'Chọn ghế - ' . $showtime->movie->title)

@push('styles')
    <style>
        .booking-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 0;
        }

        /* Movie Info Header */
        .booking-header {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .movie-poster-small {
            width: 100px;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
        }

        .movie-info h3 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .movie-info .meta {
            color: var(--text-secondary);
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        /* Theater Layout */
        .theater-layout {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 2rem;
        }

        .screen {
            background: linear-gradient(to bottom, rgba(229, 9, 20, 0.3), transparent);
            border-radius: 8px;
            height: 60px;
            margin: 0 auto 3rem;
            width: 95%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-weight: 600;
            letter-spacing: 2px;
            border: 2px solid var(--primary-color);
            border-bottom: 4px solid var(--primary-color);
            box-shadow: 0 10px 30px rgba(229, 9, 20, 0.2);
        }

        .seats-container {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            align-items: center;
        }

        .seat-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .row-label {
            width: 30px;
            text-align: center;
            font-weight: 700;
            color: var(--text-secondary);
        }

        .seat {
            width: 35px;
            height: 35px;
            border-radius: 8px 8px 0 0;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.75rem;
            border: 2px solid;
            position: relative;
        }

        .seat.available {
            background: #1f1f1f;
            border-color: #4a4a4a;
            color: #4a4a4a;
        }

        .seat.available:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            transform: scale(1.1);
        }

        .seat.selected {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            animation: seatPulse 0.3s ease;
        }

        .seat.sold {
            background: #555;
            border-color: #555;
            color: #888;
            cursor: not-allowed;
        }

        .seat.reserved {
            background: #ff9800;
            border-color: #ff9800;
            color: white;
            cursor: not-allowed;
            animation: reservedBlink 2s infinite;
        }

        .seat.vip {
            border-color: #ffd700;
        }

        .seat.vip.available {
            background: #2a2a00;
            border-color: #ffd700;
            color: #ffd700;
        }

        .seat.vip.selected {
            background: #ffd700;
            border-color: #ffd700;
            color: #000;
        }

        @keyframes seatPulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.15);
            }
        }

        @keyframes reservedBlink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        /* Legend */
        .seat-legend {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .legend-seat {
            width: 30px;
            height: 30px;
            border-radius: 6px 6px 0 0;
            border: 2px solid;
        }

        /* Booking Summary */
        .booking-summary {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 2rem;
            position: sticky;
            top: 100px;
        }

        .summary-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--primary-color);
        }

        .selected-seats-list {
            margin-bottom: 1.5rem;
        }

        .seat-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem;
            background: var(--dark-bg);
            border-radius: 8px;
            margin-bottom: 0.5rem;
        }

        .seat-item .name {
            font-weight: 600;
        }

        .seat-item .price {
            color: var(--primary-color);
            font-weight: 700;
        }

        .price-breakdown {
            background: var(--dark-bg);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
        }

        .price-row.total {
            border-top: 2px solid var(--primary-color);
            margin-top: 0.5rem;
            padding-top: 1rem;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .timer {
            background: rgba(229, 9, 20, 0.2);
            border: 2px solid var(--primary-color);
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            margin-bottom: 1rem;
        }

        .timer .time {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            font-family: 'Courier New', monospace;
        }

        .timer .label {
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .btn-continue {
            width: 100%;
            padding: 1rem;
            font-size: 1.1rem;
            font-weight: 700;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            transition: all 0.3s ease;
        }

        .btn-continue:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(229, 9, 20, 0.4);
        }

        .btn-continue:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .booking-header {
                flex-direction: column;
                text-align: center;
            }

            .seat {
                width: 28px;
                height: 28px;
                font-size: 0.65rem;
            }

            .row-label {
                width: 25px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container booking-container">
        <!-- Movie Info Header -->
        <div class="booking-header">
            <img src="{{ $showtime->movie->poster_url ? $showtime->movie->poster_url : 'https://via.placeholder.com/100x150' }}"
                alt="{{ $showtime->movie->title }}" class="movie-poster-small">
            <div class="movie-info">
                <h3>{{ $showtime->movie->title }}</h3>
                <div class="meta">
                    <span><i class="bi bi-building"></i> {{ $showtime->theater->cinema->name }}</span>
                    <span><i class="bi bi-tv"></i> {{ $showtime->theater->name }}</span>
                    <span><i class="bi bi-calendar"></i> {{ $showtime->start_time->format('d/m/Y') }}</span>
                    <span><i class="bi bi-clock"></i> {{ $showtime->start_time->format('H:i') }}</span>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Seat Selection -->
            <div class="col-lg-8 mb-4">
                <div class="theater-layout">
                    <div class="screen">MÀN HÌNH</div>

                    <div class="seats-container" id="seatsContainer">
                        @php
                            $seatsByRow = $seats->groupBy('row_char');
                        @endphp

                        @foreach ($seatsByRow as $row => $rowSeats)
                            <div class="seat-row">
                                <div class="row-label">{{ $row }}</div>
                                @foreach ($rowSeats->sortBy('column_number') as $seat)
                                    <div class="seat {{ $seat->seatType->name == 'VIP' ? 'vip' : '' }} 
                                    {{ $seat->is_sold ? 'sold' : ($seat->is_reserved ? 'reserved' : ($seat->reserved_by_me ? 'selected' : 'available')) }}"
                                        data-seat-id="{{ $seat->id }}"
                                        data-seat-name="{{ $row }}{{ $seat->column_number }}"
                                        data-seat-price="{{ $showtime->base_price + $seat->seatType->surcharge }}"
                                        data-seat-type="{{ $seat->seatType->name }}"
                                        @if ($seat->is_sold || $seat->is_reserved) disabled @endif>
                                        {{ $seat->column_number }}
                                    </div>
                                @endforeach
                                <div class="row-label">{{ $row }}</div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Legend -->
                    <div class="seat-legend">
                        <div class="legend-item">
                            <div class="legend-seat available" style="background: #1f1f1f; border-color: #4a4a4a;"></div>
                            <span>Ghế trống</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-seat selected"
                                style="background: var(--primary-color); border-color: var(--primary-color);"></div>
                            <span>Ghế đang chọn</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-seat sold" style="background: #555; border-color: #555;"></div>
                            <span>Đã bán</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-seat reserved" style="background: #ff9800; border-color: #ff9800;"></div>
                            <span>Đang giữ</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-seat vip available" style="background: #2a2a00; border-color: #ffd700;">
                            </div>
                            <span>Ghế VIP</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Summary -->
            <div class="col-lg-4">
                <div class="booking-summary">
                    <h4 class="summary-title">Thông tin đặt vé</h4>

                    <div class="timer" id="timer" style="display: none;">
                        <div class="time" id="timerDisplay">05:00</div>
                        <div class="label">Thời gian giữ ghế</div>
                    </div>

                    <div class="selected-seats-list" id="selectedSeatsList">
                        <p class="text-secondary text-center">Chưa chọn ghế nào</p>
                    </div>

                    <div class="price-breakdown">
                        <div class="price-row">
                            <span>Tổng tiền:</span>
                            <span id="totalPrice">0đ</span>
                        </div>
                        <div class="price-row total">
                            <span>Thành tiền:</span>
                            <span id="finalPrice">0đ</span>
                        </div>
                    </div>

                    <button class="btn btn-primary btn-continue" id="btnContinue" disabled>
                        <i class="bi bi-arrow-right-circle"></i> Tiếp tục thanh toán
                    </button>

                    <div class="mt-3 text-center">
                        <a href="{{ route('movies.show', $showtime->movie) }}" class="text-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // eslint-disable-next-line
        // @ts-nocheck
        let selectedSeats = [];
        let timerInterval = null;
        let expiresAt = null;
        const showtimeId = {{ $showtime->id }};

        console.log('Showtime ID:', showtimeId);

        // Function to attach listener to a seat
        function attachSeatListener(seat) {
            if (seat.hasAttribute('data-listener-attached')) return;

            seat.setAttribute('data-listener-attached', 'true');
            seat.addEventListener('click', function() {
                console.log('Seat clicked:', this.getAttribute('data-seat-name'));

                if (this.classList.contains('sold') || this.classList.contains('reserved')) {
                    console.log('Seat is sold or reserved');
                    return;
                }

                const seatId = this.getAttribute('data-seat-id');
                const seatName = this.getAttribute('data-seat-name');
                const seatPrice = parseFloat(this.getAttribute('data-seat-price'));
                const seatType = this.getAttribute('data-seat-type');

                console.log('Seat data:', {
                    seatId,
                    seatName,
                    seatPrice,
                    seatType
                });

                if (this.classList.contains('selected')) {
                    // Deselect
                    console.log('Deselecting seat');
                    this.classList.remove('selected');
                    selectedSeats = selectedSeats.filter(s => s.id !== seatId);
                } else {
                    // Select
                    console.log('Selecting seat');
                    this.classList.add('selected');
                    selectedSeats.push({
                        id: seatId,
                        name: seatName,
                        price: seatPrice,
                        type: seatType
                    });
                }

                console.log('Selected seats:', selectedSeats);
                updateSummary();
            });
        }

        // Attach listeners to all available seats initially
        const availableSeats = document.querySelectorAll('.seat.available:not([disabled])');
        console.log('Available seats found:', availableSeats.length);

        availableSeats.forEach(seat => {
            console.log('Adding listener to seat:', seat.getAttribute('data-seat-name'));
            attachSeatListener(seat);
        });

        // Update summary
        function updateSummary() {
            updateSummaryOnly();
            // Reserve seats on server
            reserveSeatsOnServer();
        }

        // Update summary UI only (without calling reserve API)
        function updateSummaryOnly() {
            const listContainer = document.getElementById('selectedSeatsList');
            const totalPriceEl = document.getElementById('totalPrice');
            const finalPriceEl = document.getElementById('finalPrice');
            const btnContinue = document.getElementById('btnContinue');

            if (selectedSeats.length === 0) {
                listContainer.innerHTML = '<p class="text-secondary text-center">Chưa chọn ghế nào</p>';
                totalPriceEl.textContent = '0đ';
                finalPriceEl.textContent = '0đ';
                btnContinue.disabled = true;
                return;
            }

            // Render selected seats
            let html = '';
            let total = 0;

            selectedSeats.forEach(seat => {
                total += seat.price;
                html += `
                <div class="seat-item">
                    <div>
                        <div class="name">Ghế ${seat.name}</div>
                        <small class="text-secondary">${seat.type}</small>
                    </div>
                    <div class="price">${seat.price.toLocaleString('vi-VN')}đ</div>
                </div>
            `;
            });

            listContainer.innerHTML = html;
            totalPriceEl.textContent = total.toLocaleString('vi-VN') + 'đ';
            finalPriceEl.textContent = total.toLocaleString('vi-VN') + 'đ';
            btnContinue.disabled = false;
        }

        // Reserve seats on server (AJAX)
        function reserveSeatsOnServer() {
            fetch('{{ route('booking.reserve') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        showtime_id: showtimeId,
                        seat_ids: selectedSeats.map(s => s.id)
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        expiresAt = new Date(data.expires_at);
                        startTimer();
                    } else {
                        // Reservation failed - don't reload, just show message
                        alert(data.message);

                        // Force refresh seat status to update UI
                        fetch(`{{ route('booking.seat-status', $showtime->id) }}`)
                            .then(response => response.json())
                            .then(statusData => {
                                if (statusData.success) {
                                    // Update seat status in UI
                                    Object.keys(statusData.status).forEach(seatId => {
                                        const seatEl = document.querySelector(
                                            `.seat[data-seat-id="${seatId}"]`);
                                        if (!seatEl) return;

                                        const status = statusData.status[seatId];

                                        // Remove selected class from seats that are now taken
                                        if ((status === 'sold' || status === 'reserved') && selectedSeats
                                            .some(s => s.id == seatId)) {
                                            // This seat was selected but is now taken - remove from selection
                                            selectedSeats = selectedSeats.filter(s => s.id != seatId);
                                            seatEl.classList.remove('selected', 'available');
                                            seatEl.classList.add(status === 'sold' ? 'sold' : 'reserved');
                                            seatEl.setAttribute('disabled', 'disabled');
                                            seatEl.style.cursor = 'not-allowed';
                                        }
                                    });

                                    // Update summary with remaining selected seats
                                    updateSummaryOnly();
                                }
                            });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Start countdown timer
        function startTimer() {
            const timerEl = document.getElementById('timer');
            const timerDisplay = document.getElementById('timerDisplay');
            timerEl.style.display = 'block';

            if (timerInterval) clearInterval(timerInterval);

            timerInterval = setInterval(() => {
                const now = new Date().getTime();
                const distance = new Date(expiresAt).getTime() - now;

                if (distance < 0) {
                    clearInterval(timerInterval);
                    alert('Hết thời gian giữ ghế! Vui lòng chọn lại.');
                    location.reload();
                    return;
                }

                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                timerDisplay.textContent =
                    `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }, 1000);
        }

        // Poll seat status every 2 seconds (faster polling)
        setInterval(() => {
            fetch(`{{ route('booking.seat-status', $showtime->id) }}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Object.keys(data.status).forEach(seatId => {
                            const seatEl = document.querySelector(`.seat[data-seat-id="${seatId}"]`);
                            if (!seatEl) return;

                            const status = data.status[seatId];

                            // Don't update if this seat is selected by current user
                            if (selectedSeats.some(s => s.id == seatId)) return;

                            // Remove all status classes first
                            seatEl.classList.remove('available', 'sold', 'reserved', 'selected');

                            if (status === 'sold') {
                                seatEl.classList.add('sold');
                                seatEl.setAttribute('disabled', 'disabled');
                                // Remove click listener
                                seatEl.style.cursor = 'not-allowed';
                            } else if (status === 'reserved') {
                                seatEl.classList.add('reserved');
                                seatEl.setAttribute('disabled', 'disabled');
                                seatEl.style.cursor = 'not-allowed';
                            } else {
                                // Seat is now available
                                seatEl.classList.add('available');
                                seatEl.removeAttribute('disabled');
                                seatEl.style.cursor = 'pointer';

                                // Re-attach click listener if not already attached
                                if (!seatEl.hasAttribute('data-listener-attached')) {
                                    attachSeatListener(seatEl);
                                }
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error polling seat status:', error);
                });
        }, 1000); // Poll every 1 second for faster updates

        // Initial poll on page load
        setTimeout(() => {
            fetch(`{{ route('booking.seat-status', $showtime->id) }}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Object.keys(data.status).forEach(seatId => {
                            const seatEl = document.querySelector(`.seat[data-seat-id="${seatId}"]`);
                            if (!seatEl) return;
                            const status = data.status[seatId];
                            if (selectedSeats.some(s => s.id == seatId)) return;
                            seatEl.classList.remove('available', 'sold', 'reserved', 'selected');
                            if (status === 'sold') {
                                seatEl.classList.add('sold');
                                seatEl.setAttribute('disabled', 'disabled');
                                seatEl.style.cursor = 'not-allowed';
                            } else if (status === 'reserved') {
                                seatEl.classList.add('reserved');
                                seatEl.setAttribute('disabled', 'disabled');
                                seatEl.style.cursor = 'not-allowed';
                            } else {
                                seatEl.classList.add('available');
                                seatEl.removeAttribute('disabled');
                                seatEl.style.cursor = 'pointer';
                                if (!seatEl.hasAttribute('data-listener-attached')) {
                                    attachSeatListener(seatEl);
                                }
                            }
                        });
                    }
                })
                .catch(error => console.error('Initial poll error:', error));
        }, 500);

        // Continue to payment
        document.getElementById('btnContinue').addEventListener('click', function() {
            if (selectedSeats.length === 0) return;

            // Store selected seats in session and redirect to payment
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('payment.show') }}';

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            const showtimeInput = document.createElement('input');
            showtimeInput.type = 'hidden';
            showtimeInput.name = 'showtime_id';
            showtimeInput.value = showtimeId;
            form.appendChild(showtimeInput);

            const seatsInput = document.createElement('input');
            seatsInput.type = 'hidden';
            seatsInput.name = 'seat_ids';
            seatsInput.value = JSON.stringify(selectedSeats.map(s => s.id));
            form.appendChild(seatsInput);

            document.body.appendChild(form);
            form.submit();
        });

        // Restore previously selected seats if any
        // @ts-ignore
        @if (session('selected_seats'))
            const previousSeats = @json(session('selected_seats'));
            previousSeats.forEach(seatId => {
                const seatEl = document.querySelector(`.seat[data-seat-id="${seatId}"]`);
                if (seatEl && !seatEl.classList.contains('sold')) {
                    seatEl.click();
                }
            });
        @endif
    </script>
@endpush
