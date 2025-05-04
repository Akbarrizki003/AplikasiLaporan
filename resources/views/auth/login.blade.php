<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>PLN Dashboard - Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
    rel="stylesheet"
  />
  <link
    href="https://fonts.googleapis.com/css2?family=Inter&display=swap"
    rel="stylesheet"
  />
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-image: linear-gradient(135deg, #e6e9e8 0%, #c5d1d9 100%);
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
  <div class="max-w-md w-full">
    <!-- Logo Header -->
    <div class="text-center mb-8">
      <div class="flex items-center justify-center mb-2">
      <img
  src="{{ asset('uploads/logo/b.png') }}"
  alt="PLN logo"
  class="w-32 h-32 object-contain"
/>

      </div>
      <h1 class="text-2xl font-bold text-[#0a4a5a]">Sistem Laporan Unit</h1>
      <p class="text-gray-600 text-sm">Silakan login untuk melanjutkan</p>
    </div>
    
    <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-gray-100">
      <!-- Card Header -->
      <div class="bg-[#0a4a5a] text-white px-6 py-4">
        <h2 class="text-xl font-bold flex items-center">
          <i class="fas fa-user-lock mr-2"></i> {{ __('Login') }}
        </h2>
      </div>

      <!-- Card Body -->
      <div class="p-6">
        @if (session('success'))
          <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert">
            <p>{{ session('success') }}</p>
          </div>
        @endif
        
        @if (session('error'))
          <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
            <p>{{ session('error') }}</p>
          </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
          @csrf

          <!-- Email Field -->
          <div class="mb-4">
            <label for="email" class="block text-gray-700 text-sm font-medium mb-2">
              {{ __('E-Mail Address') }}
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-envelope text-gray-400"></i>
              </div>
              <input 
                id="email" 
                type="email" 
                name="email" 
                value="{{ old('email') }}" 
                required 
                autocomplete="email" 
                autofocus
                class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#0a4a5a] focus:border-[#0a4a5a]"
                placeholder="email@example.com"
              >
            </div>
            @error('email')
              <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
          </div>

          <!-- Password Field -->
          <div class="mb-4">
            <label for="password" class="block text-gray-700 text-sm font-medium mb-2">
              {{ __('Password') }}
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-lock text-gray-400"></i>
              </div>
              <input 
                id="password" 
                type="password" 
                name="password" 
                required 
                autocomplete="current-password"
                class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#0a4a5a] focus:border-[#0a4a5a]"
                placeholder="••••••••"
              >
            </div>
            @error('password')
              <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
          </div>
          
          <!-- Remember Me -->
          <div class="mb-6">
            <div class="flex items-center">
              <input 
                type="checkbox" 
                name="remember" 
                id="remember" 
                {{ old('remember') ? 'checked' : '' }}
                class="h-4 w-4 text-[#0a4a5a] focus:ring-[#0a4a5a] border-gray-300 rounded"
              >
              <label for="remember" class="ml-2 block text-sm text-gray-700">
                {{ __('Remember Me') }}
              </label>
            </div>
          </div>
          
          <!-- Submit Button & Forgot Password Link -->
          <div class="flex flex-col space-y-4">
            <button 
              type="submit" 
              class="w-full bg-[#0a4a5a] hover:bg-[#083b4a] text-white font-medium py-2 px-6 rounded-md shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0a4a5a]"
            >
              <i class="fas fa-sign-in-alt mr-2"></i>{{ __('Login') }}
            </button>
            
            <div class="flex justify-between items-center">
              @if (Route::has('password.request'))
                <a 
                  href="{{ route('password.request') }}" 
                  class="text-sm text-[#0a4a5a] hover:text-[#083b4a] font-medium transition-colors"
                >
                  {{ __('Forgot Your Password?') }}
                </a>
              @endif
              
              @if (Route::has('register'))
                <a 
                  href="{{ route('register') }}" 
                  class="text-sm text-[#0a4a5a] hover:text-[#083b4a] font-medium transition-colors"
                >
                  {{ __('Create an Account') }}
                </a>
              @endif
            </div>
          </div>
        </form>
      </div>
    </div>
    
    <!-- Footer -->
    <div class="mt-6 text-center text-gray-600 text-sm">
      <p>&copy; 2025 PLN. All rights reserved.</p>
    </div>
  </div>
</body>
</html>