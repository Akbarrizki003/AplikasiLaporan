<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>PLN Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    rel="stylesheet"
  />
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Montserrat:wght@700;800&display=swap"
    rel="stylesheet"
  />
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'pln-blue': '#0a4a5a',
            'pln-blue-dark': '#083b48',
            'pln-blue-light': '#1a7a8c',
            'pln-yellow': '#ffc107',
            'pln-gray': '#e6e9e8',
          },
          fontFamily: {
            'poppins': ['Poppins', 'sans-serif'],
            'montserrat': ['Montserrat', 'sans-serif'],
          },
          boxShadow: {
            'header': '0 4px 6px -1px rgba(10, 74, 90, 0.1)',
          }
        },
      },
    };
  </script>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }
    .logo-text {
      background: linear-gradient(135deg, #0a4a5a 0%, #1a7a8c 60%, #0c556a 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      color: transparent;
      letter-spacing: 0.05em;
      text-shadow: 0px 1px 2px rgba(10, 74, 90, 0.15);
    }
    .nav-link:after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      bottom: -4px;
      left: 0;
      background-color: #0a4a5a;
      transition: width 0.3s ease;
    }
    .nav-link:hover:after {
      width: 100%;
    }
    .nav-link.active:after {
      width: 100%;
    }
  </style>
</head>
<body class="bg-pln-gray min-h-screen font-poppins">
  <header class="bg-white border-b-4 border-pln-blue shadow-header sticky top-0 z-50">
    <div class="container mx-auto px-4 py-3">
      <div class="flex items-center justify-between">
        <!-- Logo and Brand -->
        <div class="flex items-center space-x-4">
          <div>
            <img
              src="{{ asset('uploads/logo/pln1.png') }}"
              alt="PLN logo"
              class="w-16 h-16 object-contain"
            />
          </div>
          <div>
            <h1 class="font-montserrat font-black text-2xl tracking-wider logo-text relative pb-1">
              <span class="inline-block">Pengajuan</span>
              <span class="inline-block ml-1 font-montserrat">Laporan</span>
              <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-pln-blue via-pln-blue-light to-transparent rounded-full"></span>
            </h1>
          </div>
        </div>
        
        <!-- Navigation -->
        <nav class="hidden md:flex space-x-8 text-gray-700 font-medium">
          <a href="#" class="nav-link active relative py-2 px-1 text-pln-blue-dark hover:text-pln-blue transition-colors">
            <i class="fas fa-home mr-1.5"></i>Home
          </a>
          <a href="#" class="nav-link relative py-2 px-1 text-gray-600 hover:text-pln-blue transition-colors">
            <i class="fas fa-user-circle mr-1.5"></i>Profile
          </a>
        </nav>
        
        <!-- User Actions -->
        <div class="flex items-center space-x-4">
          
          <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button
              type="submit"
              class="bg-pln-blue text-white text-sm font-semibold px-5 py-2 rounded-lg shadow-md hover:bg-pln-blue-dark transition-colors duration-300 flex items-center"
            >
              <i class="fas fa-sign-out-alt mr-1.5"></i>Logout
            </button>
          </form>
          
          <button
            type="button"
            aria-label="Menu"
            class="md:hidden text-pln-blue text-xl hover:text-pln-blue-dark transition-colors"
          >
            <i class="fas fa-bars"></i>
          </button>
        </div>
      </div>
    </div>
  </header>

  <!-- Mobile Navigation Menu (Hidden by default) -->
  <div class="hidden bg-white shadow-md rounded-b-lg mx-4 md:hidden">
    <nav class="flex flex-col py-2">
      <a href="#" class="px-4 py-3 text-pln-blue-dark font-medium hover:bg-gray-50 flex items-center">
        <i class="fas fa-home w-6"></i>Home
      </a>
      <a href="#" class="px-4 py-3 text-gray-600 font-medium hover:bg-gray-50 flex items-center">
        <i class="fas fa-user-circle w-6"></i>Profile
      </a>
    </nav>
  </div>

  <main class="px-6 py-8 w-full">
    @yield('content')
  </main>
</body>
</html>