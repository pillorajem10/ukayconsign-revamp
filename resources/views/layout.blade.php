<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="USC Admin dashboard for managing suppliers, transactions, and store inventory efficiently.">
    <meta name="keywords" content="ukay ukay, ukay, suppliers, transactions, store inventory, USC, ukay consign, consign, retail, clothes">
    <meta name="author" content="Your Name">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="icon" href="{{ asset('images/USC.ico') }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pinyon+Script&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bungee&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="canonical" href="{{ url()->current() }}">
    <script>
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}'
        };

        window.messages = {
            success: @json(session('success')),
            error: @json($errors->first())
        };
    </script>
    <style>
        html, body {
            height: 100%;
            width: 100%;
            margin: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column; /* Stack children vertically */
        }

        .navbar {
            background: linear-gradient(90deg, #004d00, #007f00);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed; /* Use fixed positioning */
            top: 0; /* Stick to the top */
            left: 0; /* Align to the left */
            width: 100%; /* Full width */
            z-index: 1000; /* Ensure it stays above other content */
        }

        .navbar-brand {
            color: white !important; /* Changed to white */
            font-size: 1.5rem;
            font-family: "Pinyon Script", cursive;
        }

        .navbar-brand img {
            width: 5rem;
        }

        .navbar-nav .nav-link {
            color: white !important;
            padding: 10px 15px;
            transition: background-color 0.3s;
        }

        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }

        .navbar-toggler {
            border: none;
            outline: none;
        }

        .navbar-toggler-icon {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 30px;
            height: 30px;
        }

        .navbar-toggler-icon span {
            display: block;
            width: 100%;
            height: 4px;
            background-color: white;
            margin: 2px 0;
            transition: all 0.3s ease;
        }

        .navbar-toggler.collapsed .bar1,
        .navbar-toggler.collapsed .bar3 {
            transform: rotate(0);
        }
        .navbar-toggler.collapsed .bar2 {
            opacity: 1;
        }

        .navbar-toggler:not(.collapsed) .bar1 {
            transform: rotate(45deg) translate(5px, 5px);
        }
        .navbar-toggler:not(.collapsed) .bar2 {
            opacity: 0;
        }
        .navbar-toggler:not(.collapsed) .bar3 {
            transform: rotate(-45deg) translate(5px, -5px);
        }

        .navbar-toggler:focus {
            outline: none;
        }

        .content {
            flex: 1; /* Allow the content area to grow and fill available space */
            padding: 20px;
            margin-top: 5rem; /* Space below the navbar */
        }

        footer {
            background: linear-gradient(90deg, #004d00, #007f00);
            color: white;
            text-align: center;
            padding: 10px;
            width: 100%;
        }
    </style>
    @yield('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="/">
            <img src="{{ asset('images/USC_logo.png') }}" alt="Logo">
        </a>
        <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <div class="navbar-toggler-icon">
                <span class="bar1"></span>
                <span class="bar2"></span>
                <span class="bar3"></span>
            </div>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                {{--<li class="nav-item">
                    <a class="nav-link" href="/how-to-use-usc">How to use USC</a>
                </li>--}}
                <li class="nav-item">
                    <a class="nav-link" href="/cart">
                        <i class="fas fa-shopping-cart box menu-icon"></i> Cart
                    </a>
                </li>                   
                @if(Auth::check())
                    <li class="nav-item">
                        <a class="nav-link" href="/orders">
                            <i class="fas fa-box menu-icon"></i> Orders
                        </a>                        
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard">
                            <i class="fas fa-tachometer-alt box menu-icon"></i> Dashboard
                        </a>                        
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/pos/choose">
                            <i class="fas fa-cash-register box menu-icon"></i> POS
                        </a>                        
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('logout') }}" 
                           onclick="event.preventDefault(); localStorage.removeItem('hasShownPromo'); document.getElementById('logout-form').submit();">
                           <i class="fas fa-sign-out-alt box menu-icon"></i>Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>                    
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="/login">
                            <i class="fas fa-sign-in-alt box menu-icon"></i> Login
                        </a>                        
                    </li>
                @endif
            </ul>
        </div>        
    </nav>    
    <div class="content">
        @yield('content')
    </div>

    <footer>
        &copy; {{ date('Y') }} Ukay Supplier. All Rights Reserved.
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
