<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Shop Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    :root {
      /* Maintaining the Orange theme for Shop to distinguish from User/Admin */
      --brand: #f97316; 
    }
    .input-focus:focus {
      box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.25);
    }
  </style>
</head>
<body class="min-h-screen bg-slate-50 flex items-center justify-center px-4">

<!-- CONTAINER -->
<div class="w-full max-w-md">

  <!-- BRAND -->
  <div class="text-center mb-8">
    <div class="inline-flex items-center justify-center mb-4">
      <img src="{{ asset('public/storage/logo.png') }}" 
            alt="Logo" 
            class="h-[200px] w-[200px] object-contain"
            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
      <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-orange-500 text-white" style="display: none;">
        <i class="fas fa-store text-xl"></i>
      </div>
    </div>
    <h1 class="text-2xl font-semibold text-slate-900">
      Shop Portal
    </h1>
    <p class="text-sm text-slate-500 mt-1">
      Login to manage your shop
    </p>
  </div>

  <!-- CARD -->
  <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6">

    {{-- Login Error --}}
    @if ($errors->any())
      <div class="mb-4 text-sm text-red-600 text-center">
        {{ $errors->first() }}
      </div>
    @endif

    <form method="POST" action="{{ route('shop.login.submit') }}" class="space-y-5">
      @csrf

      <!-- EMAIL -->
      <div>
        <label class="text-sm font-medium text-slate-700">Email</label>
        <input
          type="email"
          name="email"
          required
          placeholder="shop@example.com"
          class="mt-1 w-full px-4 py-3 border border-slate-300 rounded-xl focus:outline-none input-focus">
      </div>

      <!-- PASSWORD -->
      <div>
        <div class="flex justify-between items-center">
          <label class="text-sm font-medium text-slate-700">Password</label>
          <a href="#" class="text-xs text-orange-600 hover:underline">Forgot?</a>
        </div>

        <div class="relative mt-1">
          <input
            type="password"
            name="password"
            required
            placeholder="••••••••"
            class="w-full px-4 py-3 pr-10 border border-slate-300 rounded-xl focus:outline-none input-focus">

          <button type="button"
                  class="absolute right-3 top-3 text-slate-400 toggle-password">
            <i class="fa-regular fa-eye"></i>
          </button>
        </div>
      </div>

      <!-- REMEMBER -->
      <div class="flex items-center gap-2">
        <input id="remember" name="remember" type="checkbox" class="rounded border-slate-300 text-orange-600 focus:ring-orange-500">
        <label for="remember" class="text-sm text-slate-600">Remember me</label>
      </div>

      <!-- SUBMIT -->
      <button
        type="submit"
        class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-xl font-semibold transition">
        Login
      </button>
    </form>
  </div>

  <!-- FOOTER -->
  <p class="mt-6 text-center text-sm text-slate-600">
    Not a shop?
    <a href="/" class="text-orange-600 font-semibold hover:underline">
      Go to main site
    </a>
  </p>

  <!-- TRUST -->
  <p class="mt-4 text-center text-xs text-slate-400">
    Secured & encrypted login
  </p>
</div>

<script>
  document.querySelectorAll('.toggle-password').forEach(btn => {
    btn.addEventListener('click', () => {
      const input = btn.closest('.relative').querySelector('input');
      const icon = btn.querySelector('i');
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
      }
    });
  });
</script>

</body>
</html>
