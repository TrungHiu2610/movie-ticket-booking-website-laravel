<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Trang chủ') - UniCine</title>

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        :root {
            /* Cinematic Dark Theme - Netflix inspired */
            --primary-color: #E50914;
            /* Đỏ Netflix */
            --secondary-color: #B20710;
            /* Đỏ đậm hơn */
            --dark-bg: #141414;
            /* Đen đậm - background chính */
            --darker-bg: #0a0a0a;
            /* Đen đậm hơn cho header/footer */
            --card-bg: #221F1F;
            /* Đen than - background card */
            --card-hover: #2d2a2a;
            /* Card hover state */
            --text-primary: #FFFFFF;
            /* Text sáng */
            --text-secondary: #F2F2F2;
            /* Text sáng nhẹ hơn */
            --text-muted: #b3b3b3;
            /* Text mờ */
            --accent-gold: #FFD700;
            /* Vàng kim - giá tiền, rating */
            --border-color: rgba(255, 255, 255, 0.1);
            --shadow-color: rgba(229, 9, 20, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--dark-bg);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar Styling */
        .navbar-custom {
            background: linear-gradient(to bottom, var(--darker-bg), rgba(10, 10, 10, 0.95));
            backdrop-filter: blur(10px);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
            border-bottom: 1px solid var(--border-color);
        }

        .navbar-custom.scrolled {
            background: var(--darker-bg);
            box-shadow: 0 2px 20px var(--shadow-color);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--primary-color) !important;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .navbar-brand:hover {
            color: var(--secondary-color) !important;
        }

        .nav-link {
            color: var(--text-primary) !important;
            font-weight: 500;
            margin: 0 0.5rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 50%;
            background-color: var(--primary-color);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 80%;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(229, 9, 20, 0.4);
        }

        /* Main Content */
        main {
            flex: 1;
            padding-top: 0;
        }

        /* Footer Styling */
        footer {
            background: linear-gradient(to top, #000000, var(--darker-bg));
            color: var(--text-muted);
            padding: 3rem 0 1rem;
            margin-top: 5rem;
            border-top: 1px solid var(--border-color);
        }

        footer h5 {
            color: var(--text-primary);
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        footer a {
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.3s ease;
            display: block;
            margin-bottom: 0.5rem;
        }

        footer a:hover {
            color: var(--primary-color);
            padding-left: 5px;
        }

        .social-icons a {
            display: inline-block;
            width: 40px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            border-radius: 50%;
            background: var(--card-bg);
            color: var(--text-primary);
            margin-right: 10px;
            transition: all 0.3s ease;
        }

        .social-icons a:hover {
            background: var(--primary-color);
            transform: translateY(-3px);
        }

        /* Scroll to top button */
        .scroll-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 999;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px var(--shadow-color);
        }

        .scroll-top:hover {
            background: var(--secondary-color);
            transform: translateY(-5px);
            box-shadow: 0 8px 25px var(--shadow-color);
        }

        .scroll-top.show {
            display: flex;
        }

        /* User dropdown */
        .dropdown-menu-custom {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.8);
        }

        .dropdown-item {
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: var(--primary-color);
            color: white;
        }

        /* Card styling for dark theme */
        .card {
            background-color: var(--card-bg);
            border-color: var(--border-color);
            color: var(--text-primary);
        }

        .card-header {
            background-color: var(--card-hover) !important;
            border-bottom-color: var(--border-color);
            color: var(--text-primary) !important;
        }

        .card-header.bg-white {
            background-color: var(--card-hover) !important;
        }

        .card-body {
            color: var(--text-primary);
        }

        .card-footer {
            background-color: var(--card-bg) !important;
            border-top-color: var(--border-color);
            color: var(--text-muted) !important;
        }

        .card-footer.bg-light {
            background-color: var(--card-bg) !important;
        }

        /* Table styling */
        .table {
            color: var(--text-primary);
        }

        .table-light thead {
            background-color: var(--card-hover) !important;
            color: var(--text-primary) !important;
        }

        .table-light thead th {
            color: var(--text-primary) !important;
        }

        /* Loading animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, .3);
            border-radius: 50%;
            border-top-color: var(--primary-color);
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Responsive */
        @media (max-width: 991px) {
            .navbar-brand {
                font-size: 1.5rem;
            }

            .nav-link {
                margin: 0.5rem 0;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="bi bi-film"></i> UniCine
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">
                            <i class="bi bi-house-door"></i> Trang chủ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('movies.index') }}">
                            <i class="bi bi-camera-reels"></i> Phim
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('cinemas.index') }}">
                            <i class="bi bi-geo-alt"></i> Rạp chiếu
                        </a>
                    </li>

                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('bookings.history') }}">
                                        <i class="bi bi-clock-history"></i> Lịch sử đặt vé
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="bi bi-person"></i> Tài khoản
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-box-arrow-right"></i> Đăng xuất
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right"></i> Đăng nhập
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary-custom ms-2" href="{{ route('register') }}">
                                Đăng ký
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5><i class="bi bi-film"></i> UniCine</h5>
                    <p class="text-secondary">
                        Hệ thống rạp chiếu phim hiện đại, mang đến trải nghiệm giải trí đỉnh cao
                        với công nghệ âm thanh và hình ảnh tiên tiến nhất.
                    </p>
                    <div class="social-icons mt-3">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-youtube"></i></a>
                        <a href="#"><i class="bi bi-tiktok"></i></a>
                    </div>
                </div>

                <div class="col-md-2 mb-4">
                    <h5>Phim</h5>
                    <a href="{{ route('movies.index') }}?filter=now_showing">Phim đang chiếu</a>
                    <a href="{{ route('movies.index') }}?filter=coming_soon">Phim sắp chiếu</a>
                    <a href="{{ route('movies.index') }}">Tất cả phim</a>
                </div>

                <div class="col-md-2 mb-4">
                    <h5>Dịch vụ</h5>
                    <a href="#">Giá vé</a>
                    <a href="#">Khuyến mãi</a>
                    <a href="#">Thẻ thành viên</a>
                </div>

                <div class="col-md-4 mb-4">
                    <h5>Liên hệ</h5>
                    <p><i class="bi bi-telephone"></i> Hotline: 1900 xxxx</p>
                    <p><i class="bi bi-envelope"></i> Email: support@UniCine.vn</p>
                    <p><i class="bi bi-geo-alt"></i> Hà Nội - TP.HCM - Đà Nẵng</p>
                </div>
            </div>

            <hr style="border-color: rgba(255,255,255,0.1)">

            <div class="row">
                <div class="col-12 text-center">
                    <p class="mb-0">&copy; {{ date('Y') }} UniCine. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to top button -->
    <div class="scroll-top" id="scrollTop">
        <i class="bi bi-arrow-up"></i>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar-custom');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }

            // Scroll to top button
            const scrollTop = document.getElementById('scrollTop');
            if (window.scrollY > 300) {
                scrollTop.classList.add('show');
            } else {
                scrollTop.classList.remove('show');
            }
        });

        // Scroll to top functionality
        document.getElementById('scrollTop').addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
