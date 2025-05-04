<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>PLN Dashboard - Register</title>
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
<body class="min-h-screen flex items-center justify-center p-4 py-8">
  <div class="max-w-md w-full">
    <!-- Logo Header -->
    <div class="text-center mb-8">
      <div class="flex items-center justify-center mb-2">
        <img
          src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/7a/Logo_Perlindungan_Listrik_Nasional.svg/120px-Logo_Perlindungan_Listrik_Nasional.svg.png"
          alt="PLN logo"
          class="w-16 h-16 object-contain"
        />
      </div>
      <h1 class="text-2xl font-bold text-[#0a4a5a]">Sistem Laporan PLN</h1>
      <p class="text-gray-600 text-sm">Buat akun untuk akses sistem</p>
    </div>
    
    <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-gray-100">
      <!-- Card Header -->
      <div class="bg-[#0a4a5a] text-white px-6 py-4">
        <h2 class="text-xl font-bold flex items-center">
          <i class="fas fa-user-plus mr-2"></i> {{ __('Register') }}
        </h2>
      </div>

      <!-- Card Body -->
      <div class="p-6">
        <form method="POST" action="{{ route('register') }}">
          @csrf

          <!-- Name Field -->
          <div class="mb-4">
            <label for="name" class="block text-gray-700 text-sm font-medium mb-2">
              {{ __('Name') }}
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-user text-gray-400"></i>
              </div>
              <input 
                id="name" 
                type="text" 
                name="name" 
                value="{{ old('name') }}" 
                required 
                autocomplete="name" 
                autofocus
                class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#0a4a5a] focus:border-[#0a4a5a]"
                placeholder="Nama Lengkap"
              >
            </div>
            @error('name')
              <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
          </div>

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
                class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#0a4a5a] focus:border-[#0a4a5a]"
                placeholder="email@example.com"
              >
            </div>
            @error('email')
              <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
          </div>

          <!-- Role Field -->
          <div class="mb-4">
            <label for="role" class="block text-gray-700 text-sm font-medium mb-2">
              {{ __('Role') }}
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-user-tag text-gray-400"></i>
              </div>
              <select 
                id="role" 
                name="role" 
                required
                class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#0a4a5a] focus:border-[#0a4a5a] appearance-none bg-white"
              >
                <option value="">-- Pilih Role --</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="unit" {{ old('role') == 'unit' ? 'selected' : '' }}>Unit</option>
                <option value="keuangan" {{ old('role') == 'keuangan' ? 'selected' : '' }}>Keuangan</option>
                <option value="manajer" {{ old('role') == 'manajer' ? 'selected' : '' }}>Manajer</option>
                <option value="atasan" {{ old('role') == 'atasan' ? 'selected' : '' }}>Atasan</option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                <i class="fas fa-chevron-down text-gray-400"></i>
              </div>
            </div>
            @error('role')
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
                autocomplete="new-password"
                class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#0a4a5a] focus:border-[#0a4a5a]"
                placeholder="••••••••"
              >
            </div>
            @error('password')
              <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
          </div>

          <!-- Confirm Password Field -->
          <div class="mb-6">
            <label for="password-confirm" class="block text-gray-700 text-sm font-medium mb-2">
              {{ __('Confirm Password') }}
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-lock text-gray-400"></i>
              </div>
              <input 
                id="password-confirm" 
                type="password" 
                name="password_confirmation" 
                required 
                autocomplete="new-password"
                class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#0a4a5a] focus:border-[#0a4a5a]"
                placeholder="••••••••"
              >
            </div>
          </div>
          
          <!-- Submit Button & Login Link -->
          <div class="flex flex-col space-y-4">
            <button 
              type="submit" 
              class="w-full bg-[#0a4a5a] hover:bg-[#083b4a] text-white font-medium py-2 px-6 rounded-md shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0a4a5a]"
            >
              <i class="fas fa-user-plus mr-2"></i>{{ __('Register') }}
            </button>
            
            <div class="text-center">
              <span class="text-gray-600 text-sm">Already have an account?</span>
              <a 
                href="{{ route('login') }}" 
                class="text-sm text-[#0a4a5a] hover:text-[#083b4a] font-medium transition-colors ml-1"
              >
                {{ __('Login') }}
              </a>
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