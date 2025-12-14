<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="google-adsense-account" content="ca-pub-9715601387910750">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Sign in • Referral Platform</title>

<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-Y7LH4X5QCM"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-Y7LH4X5QCM');
</script>

<script src="https://cdn.tailwindcss.com"></script>
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9715601387910750"
  crossorigin="anonymous"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
:root{
  --brand:#4f46e5;
}
.input-focus:focus{
  box-shadow:0 0 0 3px rgba(79,70,229,.25);
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
      <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-indigo-600 text-white" style="display: none;">
        <i class="fas fa-network-wired text-xl"></i>
      </div>
    </div>
    <h1 class="text-2xl font-semibold text-slate-900">
      Sign in to your account
    </h1>
    <p class="text-sm text-slate-500 mt-1">
      Access your referrals, keys, and dashboard
    </p>
  </div>

  <!-- CARD -->
  <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6">

    <form method="POST" action="{{ route('login.submit') }}" class="space-y-5">
      @csrf

      <!-- EMAIL -->
      <div>
        <label class="text-sm font-medium text-slate-700">Email</label>
        <input
          type="email"
          name="email"
          required
          placeholder="you@example.com"
          class="mt-1 w-full px-4 py-3 border border-slate-300 rounded-xl focus:outline-none input-focus">
      </div>

      <!-- PASSWORD -->
      <div>
        <div class="flex justify-between items-center">
          <label class="text-sm font-medium text-slate-700">Password</label>
          <a href="{{ route('resendPasswordform') }}"
             class="text-xs text-indigo-600 hover:underline">
            Forgot?
          </a>
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
        <input type="checkbox" class="rounded border-slate-300 text-indigo-600">
        <span class="text-sm text-slate-600">Keep me signed in</span>
      </div>

      <!-- SUBMIT -->
      <button
        type="submit"
        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl font-semibold transition">
        Sign In
      </button>
    </form>
  </div>

  <!-- FOOTER -->
  <p class="mt-6 text-center text-sm text-slate-600">
    New here?
    <a href="{{ route('register') }}"
       class="text-indigo-600 font-semibold hover:underline">
      Create an account
    </a>
  </p>

  <!-- TRUST -->
  <p class="mt-4 text-center text-xs text-slate-400">
    Secured & encrypted login
  </p>
</div>

<script>
document.querySelectorAll('.toggle-password').forEach(btn=>{
  btn.addEventListener('click',()=>{
    const input = btn.closest('.relative').querySelector('input');
    const icon = btn.querySelector('i');
    if(input.type === 'password'){
      input.type = 'text';
      icon.classList.replace('fa-eye','fa-eye-slash');
    }else{
      input.type = 'password';
      icon.classList.replace('fa-eye-slash','fa-eye');
    }
  });
});
</script>

</body>
</html>
