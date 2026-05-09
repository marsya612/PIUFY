<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f5f5f5;
        }

        .app-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .content {
            flex: 1;
        }
        .main {
            flex: 1;
            display: flex;
            align-items: stretch;
        }

        .sidebar {
            width: 240px;
            background: #fff;
            border-right: 1px solid #ddd;
        }



        .sidebar-menu .nav-link {
            color: #555;
            border-radius: 6px;
            padding: 8px 12px;
            transition: background-color 0.2s;
        }

        /* HOVER */
        .sidebar-menu .nav-link:hover {
            background-color: #f1f3f5;
            color: #000;
        }

        /* ACTIVE (menu yang sedang dibuka) */
        .sidebar-menu .nav-link.active {
            background-color: #e9ecef;
            font-weight: 600;
            color: #000;
        }    


        .badge-status {
            border-radius: 12px;
            padding: 5px 10px;
            font-size: 12px;
        }

        .table td, .table th {
            vertical-align: middle;
        }

        /* .profile-hover {
            transition: all 0.2s ease;
            cursor: pointer;
        } */

        /* hover lebih kontras dari bg-light */
        /* .profile-hover:hover {
            background-color: #dee2e6; /* lebih gelap dari bg-light */
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        } */

        /* efek klik */
        /* .profile-hover:active {
            transform: scale(0.96);
        } */

        /* foto ikut efek */
        /* .profile-hover:hover .profile-img {
            transform: scale(1.1);
            transition: 0.2s;
        } */
        .profile-hover {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .profile-hover:hover {
            background-color: #dee2e6;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .profile-hover:active {
            transform: scale(0.96);
        }
        .notif-bell {
            color: #6c757d;
            transition: color 0.2s;
        }
        
        .notif-bell:hover {
            color: #212529 !important;
        }
        
        .notif-bell-icon {
            font-size: 20px;
        }
    </style>
</head>
<body>

<div class="app-wrapper">

    {{-- HEADER --}}
    @include('header')

    <!-- MAIN FULL WIDTH -->
    <div class="main d-flex">

        {{-- SIDEBAR (FIX KIRI) --}}
        @include('sidebar')

        {{-- CONTENT --}}
        <div class="content flex-grow-1">
            @yield('content')
        </div>

    </div>

    {{-- FOOTER --}}
    @include('footer')

</div>

</body>
</html>
