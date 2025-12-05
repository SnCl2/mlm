<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="google-adsense-account" content="ca-pub-9715601387910750">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Referral System</title>
      <!-- Google tag (gtag.js) -->
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
    :root {
        --primary: #169CF9;
        --primary-light: #b8e1fd;
        --primary-dark: #0C9EEA;
    }
    
    body {
  background-color: #f8fafc;
    }
    
    .input-focus-effect:focus {
      box-shadow: 0 0 0 3px #169CF9;
    }
    
    .btn-primary {
      transition: all 0.3s ease;
      box-shadow: 0 4px 6px -1px #F973161A, 0 2px 4px -1px #F973160F;
    }
    
    .btn-primary:hover {
      transform: translateY(-1px);
      box-shadow: 0 10px 15px -3px #F973161A, 0 4px 6px -2px #F973160D;
    }
    
    .btn-primary:active {
      transform: translateY(0);
    }

  </style>
</head>
<body class="min-h-screen flex flex-col md:flex-row">

  <!-- Left Column: Image (Hidden on mobile) -->
  <div class="hidden md:flex md:w-1/2 items-center justify-center bg-gradient-to-br from-cyan-50 to-cyan-100 p-8">
    <div class="max-w-lg text-center">
      <img src="https://source.unsplash.com/800x800/?business,network,people" alt="Referral Graphic" class="w-full rounded-xl shadow-lg mb-6">
      <h2 class="text-2xl font-bold text-gray-800 mb-2">Grow Your Network</h2>
      <p class="text-gray-600">Join thousands of users who are expanding their reach through our referral system.</p>
    </div>
  </div>

  <!-- Right Column: Form -->
  <div class="w-full md:w-1/2 flex items-center justify-center p-4 md:p-8">
    <div class="w-full max-w-md bg-white rounded-xl shadow-sm p-6 md:p-8">
      <div class="flex justify-center mb-6">
        <div class="w-16 h-16 rounded-full bg-cyan-100 flex items-center justify-center">
          <i class="fas fa-user-lock text-cyan-500 text-2xl"></i>
        </div>
      </div>
      
      <h2 class="text-2xl font-bold text-center text-gray-800 mb-1">Welcome Back</h2>
      <p class="text-center text-gray-500 mb-6">Login to access your referral dashboard</p>
      

      <form method="POST" action="{{ route('login.submit') }}" class="space-y-5">

        @csrf
        <div>
          <label class="block mb-2 text-sm font-medium text-gray-700">Email Address</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-envelope text-gray-400"></i>
            </div>
            <input 
              type="email" 
              name="email"
              placeholder="you@example.com" 
              class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg focus:outline-none input-focus-effect focus:border-cyan-300 placeholder-gray-400"
              required
            >
          </div>
        </div>

        <div>
        <div class="flex justify-between items-center mb-2">
          <label class="block text-sm font-medium text-gray-700">Password</label>
          <a href="{{ route('resendPasswordform') }}" class="text-xs text-cyan-500 hover:underline">Resend Password?</a>
        </div>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-lock text-gray-400"></i>
            </div>
            <input 
              type="password" 
              name="password"
              placeholder="••••••••" 
              class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg focus:outline-none input-focus-effect focus:border-cyan-300 placeholder-gray-400"
              required
            >
            <button type="button" class="absolute right-3 top-3 text-gray-400 hover:text-gray-500">
              <i class="far fa-eye"></i>
            </button>
          </div>
        </div>

        <div class="flex items-center">
          <input id="remember-me" type="checkbox" class="h-4 w-4 text-cyan-500 focus:ring-cyan-400 border-gray-300 rounded">
          <label for="remember-me" class="ml-2 block text-sm text-gray-700">Remember me</label>
        </div>

        <button 
          type="submit" 
          
          class="w-full bg-cyan-500 hover:bg-cyan-600 text-white py-3 px-4 rounded-lg font-semibold btn-primary"
        >
          Login
        </button>

        <div class="relative my-6">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200"></div>
          </div>
          <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white text-gray-500">Or continue with</span>
          </div>
        </div>

        <!--<div class="grid grid-cols-2 gap-3">-->
        <!--  <button type="button" class="flex items-center justify-center py-2 px-4 border border-gray-200 rounded-lg hover:bg-gray-50">-->
        <!--    <i class="fab fa-google text-red-500 mr-2"></i>-->
        <!--    <span class="text-sm font-medium text-gray-700">Google</span>-->
        <!--  </button>-->
        <!--  <button type="button" class="flex items-center justify-center py-2 px-4 border border-gray-200 rounded-lg hover:bg-gray-50">-->
        <!--    <i class="fab fa-facebook-f text-blue-600 mr-2"></i>-->
        <!--    <span class="text-sm font-medium text-gray-700">Facebook</span>-->
        <!--  </button>-->
        <!--</div>-->
      </form>

      <p class="mt-6 text-center text-sm text-gray-600">
        Don't have an account?
        <a href="Register.blade.php.html" class="text-cyan-500 font-semibold hover:underline ml-1">Sign up</a>
      </p>
    </div>
  </div>

  <script>
    // Simple password visibility toggle
    document.querySelectorAll('.fa-eye').forEach(icon => {
      icon.addEventListener('click', function() {
        const input = this.closest('.relative').querySelector('input');
        if (input.type === 'password') {
          input.type = 'text';
          this.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
          input.type = 'password';
          this.classList.replace('fa-eye-slash', 'fa-eye');
        }
      });
    });
  </script>
</body>
</html>