<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - Management Panel</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    :root {
      --primary: #f97316;
      --primary-light: #ffedd5;
      --primary-dark: #ea580c;
    }

    body {
      background-color: #f8fafc;
    }

    .input-focus-effect:focus {
      box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.3);
    }

    .btn-primary {
      transition: all 0.3s ease;
      box-shadow: 0 4px 6px -1px rgba(249, 115, 22, 0.1), 0 2px 4px -1px rgba(249, 115, 22, 0.06);
    }

    .btn-primary:hover {
      transform: translateY(-1px);
      box-shadow: 0 10px 15px -3px rgba(249, 115, 22, 0.1), 0 4px 6px -2px rgba(249, 115, 22, 0.05);
    }

    .btn-primary:active {
      transform: translateY(0);
    }
  </style>
</head>
<body class="min-h-screen flex flex-col md:flex-row">

  <!-- Left Column -->
  <div class="hidden md:flex md:w-1/2 items-center justify-center bg-gradient-to-br from-orange-50 to-orange-100 p-8">
    <div class="max-w-lg text-center">
      <img src="https://source.unsplash.com/800x800/?office,work,team" alt="Graphic" class="w-full rounded-xl shadow-lg mb-6">
      <h2 class="text-2xl font-bold text-gray-800 mb-2">Management Portal</h2>
      <p class="text-gray-600">Login to manage shop accounts, commissions, and transactions securely.</p>
    </div>
  </div>

  <!-- Right Column -->
  <div class="w-full md:w-1/2 flex items-center justify-center p-4 md:p-8">
    <div class="w-full max-w-md bg-white rounded-xl shadow-sm p-6 md:p-8">
      <div class="flex justify-center mb-6">
        <div class="w-16 h-16 rounded-full bg-orange-100 flex items-center justify-center">
          <i class="fas fa-user-shield text-orange-500 text-2xl"></i>
        </div>
      </div>

      <h2 class="text-2xl font-bold text-center text-gray-800 mb-1">Admin Login</h2>
      <p class="text-center text-gray-500 mb-6">Access the admin panel</p>

      {{-- Login Error --}}
      @if ($errors->any())
        <div class="mb-4 text-sm text-red-600 text-center">
          {{ $errors->first() }}
        </div>
      @endif

      <form method="POST" action="{{ route('management.login') }}" class="space-y-5">
        @csrf
        <div>
          <label class="block mb-2 text-sm font-medium text-gray-700">Email</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-envelope text-gray-400"></i>
            </div>
            <input type="email" name="email" placeholder="admin@example.com"
              class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg focus:outline-none input-focus-effect focus:border-orange-300 placeholder-gray-400"
              required>
          </div>
        </div>

        <div>
          <div class="flex justify-between items-center mb-2">
            <label class="block text-sm font-medium text-gray-700">Password</label>
            <a href="#" class="text-xs text-orange-500 hover:underline">Forgot password?</a>
          </div>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-lock text-gray-400"></i>
            </div>
            <input type="password" name="password" placeholder="••••••••"
              class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg focus:outline-none input-focus-effect focus:border-orange-300 placeholder-gray-400"
              required>
            <button type="button" class="absolute right-3 top-3 text-gray-400 hover:text-gray-500 toggle-password">
              <i class="far fa-eye"></i>
            </button>
          </div>
        </div>

        <div class="flex items-center">
          <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-orange-500 focus:ring-orange-400 border-gray-300 rounded">
          <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
        </div>

        <button type="submit"
          class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 px-4 rounded-lg font-semibold btn-primary">
          Login
        </button>
      </form>

      <p class="mt-6 text-center text-sm text-gray-600">
        Not a manager?
        <a href="/" class="text-orange-500 font-semibold hover:underline ml-1">Go to main site</a>
      </p>
    </div>
  </div>

  <script>
    document.querySelectorAll('.toggle-password').forEach(button => {
      button.addEventListener('click', function () {
        const input = this.closest('.relative').querySelector('input');
        if (input.type === 'password') {
          input.type = 'text';
          this.querySelector('i').classList.replace('fa-eye', 'fa-eye-slash');
        } else {
          input.type = 'password';
          this.querySelector('i').classList.replace('fa-eye-slash', 'fa-eye');
        }
      });
    });
  </script>
</body>
</html>
