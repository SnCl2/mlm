<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="google-adsense-account" content="ca-pub-9715601387910750">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login • Referral System</title>

  <!-- Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-Y7LH4X5QCM"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-Y7LH4X5QCM');
  </script>

  <script src="https://cdn.tailwindcss.com"></script>
  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9715601387910750" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    :root {
      --primary: #06b6d4;
      --primary-dark: #0891b2;
    }

    body {
      background-color: #f8fafc;
    }

    .input-focus:focus {
      box-shadow: 0 0 0 3px rgba(6,182,212,.35);
    }

    .btn-primary {
      transition: all .25s ease;
      box-shadow: 0 6px 18px rgba(6,182,212,.25);
    }

    .btn-primary:hover {
      transform: translateY(-1px);
      box-shadow: 0 10px 30px rgba(6,182,212,.35);
    }
  </style>
</head>

<body class="min-h-screen flex">

<!-- LEFT SIDE (DESKTOP ONLY) -->
<div class="hidden lg:flex w-1/2 items-center justify-center bg-gradient-to-br from-cyan-50 to-cyan-100 px-10">
  <div class="max-w-md text-center">
    <img src="https://source.unsplash.com/700x700/?network,technology" class="rounded-2xl shadow-lg mb-6">
    <h2 class="text-2xl font-bold text-slate-800 mb-2">Build Your Referral Network</h2>
    <p class="text-slate-600 text-sm">
      Access your dashboard, manage referrals, and grow faster with our secure system.
    </p>
  </div>
</div>

<!-- RIGHT SIDE -->
<div class="w-full lg:w-1/2 flex items-center justify-center px-4 py-8">
  <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">

    <!-- ICON -->
    <div class="flex justify-center mb-4">
      <div class="w-14 h-14 rounded-full bg-cyan-100 flex items-center justify-center">
        <i class="fas fa-user-shield text-cyan-600 text-xl"></i>
      </div>
    </div>

    <!-- TITLE -->
    <h1 class="text-2xl font-semibold text-center text-slate-800">
      Welcome Back
    </h1>
    <p class="text-center text-slate-500 text-sm mb-6">
      Sign in to your referral dashboard
    </p>

    <!-- FORM -->
    <form method="POST" action="{{ route('login.submit') }}" class="space-y-5">
      @csrf

      <!-- EMAIL -->
      <div>
        <label class="text-sm font-medium text-slate-700 mb-1 block">Email Address</label>
        <div class="relative">
          <i class="fas fa-envelope absolute left-3 top-3.5 text-slate-400"></i>
          <input
            type="email"
            name="email"
            placeholder="you@example.com"
            required
            class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl focus:outline-none input-focus"
          >
        </div>
      </div>

      <!-- PASSWORD -->
      <div>
        <div class="flex justify-between items-center mb-1">
          <label class="text-sm font-medium text-slate-700">Password</label>
          <a href="{{ route('resendPasswordform') }}" class="text-xs text-cyan-600 hover:underline">
            Forgot password?
          </a>
        </div>
        <div class="relative">
          <i class="fas fa-lock absolute left-3 top-3.5 text-slate-400"></i>
          <input
            type="password"
            name="password"
            placeholder="••••••••"
            required
            class="w-full pl-10 pr-10 py-3 border border-slate-200 rounded-xl focus:outline-none input-focus"
          >
          <button type="button" class="absolute right-3 top-3.5 text-slate-400 toggle-password">
            <i class="far fa-eye"></i>
          </button>
        </div>
      </div>

      <!-- REMEMBER -->
      <div class="flex items-center gap-2">
        <input type="checkbox" class="h-4 w-4 text-cyan-600 border-slate-300 rounded">
        <span class="text-sm text-slate-600">Remember me</span>
      </div>

      <!-- SUBMIT -->
      <button
        type="submit"
        class="w-full bg-cyan-600 hover:bg-cyan-700 text-white py-3 rounded-xl font-semibold btn-primary">
        Login
      </button>
    </form>

    <!-- FOOTER -->
    <p class="mt-6 text-center text-sm text-slate-600">
      Don’t have an account?
      <a href="{{ route('register') }}" class="text-cyan-600 font-semibold hover:underline">
        Sign up
      </a>
    </p>
  </div>
</div>

<script>
document.querySelectorAll('.toggle-password').forEach(btn => {
  btn.addEventListener('click', function () {
    const input = this.closest('.relative').querySelector('input');
    const icon = this.querySelector('i');

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
