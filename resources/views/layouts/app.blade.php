<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Nish Auto Limited')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('./images/logo.png') }}">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
          <meta name="csrf-token" content="{{ csrf_token() }}">

    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="//unpkg.com/alpinejs" defer></script>
    
    <style>
        .sidebar {
            transition: all 0.3s ease;
        }
        
        .sidebar-mobile {
            transform: translateX(-100%);
        }
        
        .sidebar-mobile.open {
            transform: translateX(0);
        }
        
        @media (min-width: 768px) {
            .sidebar-mobile {
                transform: translateX(0);
            }
        }
        
        .stat-card {
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Mobile Overlay -->
    <div id="overlay" class="overlay fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden" style="display: none;"></div>

    <div class="flex h-screen">
        <!-- Sidebar Component -->
        @include('components.partials.sidebar')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Header Component -->
            @include('components.partials.navbar')

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Flash Messages -->
                @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex justify-between items-center mb-4">
                    <span>{{ session('success') }}</span>
                    <button class="text-green-700 hover:text-green-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                @endif

                @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg flex justify-between items-center mb-4">
                    <span>{{ session('error') }}</span>
                    <button class="text-red-700 hover:text-red-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                @endif

                <!-- Page Content -->
                @yield('content')
            </main>

            <!-- Footer Component -->
            @include('components.partials.footer')
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar functionality
            const sidebar = document.getElementById('sidebar');
            const menuToggle = document.getElementById('menuToggle');
            const closeSidebar = document.getElementById('closeSidebar');
            const overlay = document.getElementById('overlay');

            function toggleSidebar() {
                sidebar.classList.toggle('open');
                overlay.style.display = sidebar.classList.contains('open') ? 'block' : 'none';
            }

            function closeSidebarFunc() {
                sidebar.classList.remove('open');
                overlay.style.display = 'none';
            }

            if (menuToggle) menuToggle.addEventListener('click', toggleSidebar);
            if (closeSidebar) closeSidebar.addEventListener('click', closeSidebarFunc);
            if (overlay) overlay.addEventListener('click', closeSidebarFunc);

            // Close sidebar when clicking on nav links (mobile)
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 768) {
                        closeSidebarFunc();
                    }
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>