<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="google-adsense-account" content="ca-pub-9715601387910750">
    <title>@yield('title', 'Dashboard - Earnings')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-Y7LH4X5QCM"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
    
      gtag('config', 'G-Y7LH4X5QCM');
    </script>

    <style>
      :root {
        --primary: #169CF9;
        --primary-light: #b8e1fd;
        --primary-dark: #0C9EEA;
      }

      .sidebar-link {
        transition: all 0.2s ease;
      }

      .sidebar-link:hover {
        transform: translateX(4px);
      }

      .wallet-card {
        transition: all 0.3s ease;
      }

      .wallet-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
      }

      /* Colorful Sidebar Menu Items */
      .menu-dashboard { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
      .menu-tree { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
      .menu-team { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
      .menu-wallet { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
      .menu-notice { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
      .menu-kyc { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); }
      .menu-referral { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
      .menu-shop { background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); }
      .menu-pin { background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); }
      
      .menu-item-active {
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        transform: scale(1.02);
      }

      .mobile-menu {
        transition: transform 0.3s ease;
      }

      .mobile-menu.hidden {
        transform: translateX(-100%);
      }

      .mobile-menu:not(.hidden) {
        transform: translateX(0);
      }
      @media (min-width: 768px) {
        aside {
          position: fixed !important;
          top: 0;
          left: 0;
          height: 100vh;
          max-height: 100vh;
          overflow-y: auto;
          overflow-x: hidden;
        }
      }
      
      aside::-webkit-scrollbar {
        width: 6px;
      }
      
      aside::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 10px;
      }
      
      aside::-webkit-scrollbar-thumb {
        background: rgba(139, 92, 246, 0.5);
        border-radius: 10px;
      }
      
      aside::-webkit-scrollbar-thumb:hover {
        background: rgba(139, 92, 246, 0.7);
      }

    </style>

    @yield('styles')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
  </head>

  <body class="flex min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside class="w-64 shadow-lg p-6 fixed z-10 h-full hidden md:flex flex-col border-r border-purple-300" style="background: linear-gradient(135deg, #e0e7ff 0%, #ddd6fe 25%, #fce7f3 50%, #fef3c7 75%, #dbeafe 100%);">

      <div class="flex-1 overflow-y-auto pr-2 space-y-6">
        <!-- Logo -->
        <div class="flex items-center space-x-2 mb-6">
          <div class="w-10 h-10 rounded-full bg-cyan-100 flex items-center justify-center">
            <i class="fas fa-rocket text-cyan-500"></i>
          </div>
          <h1 class="text-xl font-bold text-[var(--primary)]">Dream Life</h1>
        </div>
    
        <!-- Nav Links -->
        <nav class="flex flex-col space-y-3">
          @if(auth()->check() && !auth()->guard('management')->check() && !auth()->guard('shop')->check())
            <a href="{{ route('showEarnings') }}" class="sidebar-link menu-dashboard flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('showEarnings') ? 'menu-item-active text-white' : 'text-white hover:opacity-90' }} font-medium shadow-md">
              <i class="fas fa-home w-5 text-center"></i>
              <span>Dashboard</span>
            </a>
            <a href="{{ route('tree') }}" class="sidebar-link menu-tree flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('tree') ? 'menu-item-active text-white' : 'text-white hover:opacity-90' }} shadow-md">
              <i class="fas fa-project-diagram w-5 text-center"></i>
              <span>My Tree</span>
            </a>
            
            <a href="{{ route('table') }}" class="sidebar-link menu-team flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('table') ? 'menu-item-active text-white' : 'text-white hover:opacity-90' }} shadow-md">
              <i class="fas fa-users w-5 text-center"></i>
              <span>MY ALL TEAM</span>
            </a>

            <a href="{{ route('cashback') }}" class="sidebar-link menu-wallet flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('cashback') ? 'menu-item-active text-white' : 'text-white hover:opacity-90' }} shadow-md">
              <i class="fas fa-money-bill-wave w-5 text-center"></i>
              <span>MY SHOPPING WALLET</span>
            </a>
            
            <a href="{{ route('notifications.index') }}" class="sidebar-link menu-notice flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('notifications.*') ? 'menu-item-active text-white' : 'text-white hover:opacity-90' }} shadow-md">
              <i class="fas fa-bullhorn w-5 text-center"></i>
              <span>Notice Board</span>
            </a>
            @if(!auth()->user()->kyc)
              <a href="{{ route('kyc.create') }}" class="sidebar-link menu-kyc flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('kyc.*') ? 'menu-item-active text-white' : 'text-white hover:opacity-90' }} shadow-md">
                <i class="fas fa-file-alt w-5 text-center"></i>
                <span>Submit KYC</span>
              </a>
            @else
              <a href="{{ route('kyc.edit') }}" class="sidebar-link menu-kyc flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('kyc.*') ? 'menu-item-active text-white' : 'text-white hover:opacity-90' }} shadow-md">
                <i class="fas fa-user-shield w-5 text-center"></i>
                <span>KYC MANAGER</span>
              </a>
              <a href="{{ route('register') }}" class="sidebar-link menu-referral flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('register') ? 'menu-item-active text-gray-800' : 'text-gray-800 hover:opacity-90' }} shadow-md">
                  <i class="fas fa-user-plus w-5 text-center"></i>
                  <span>REFERRAL FORM</span>
                </a>
              <a href="{{ route('shop.create') }}" class="sidebar-link menu-shop flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('shop.create') ? 'menu-item-active text-white' : 'text-white hover:opacity-90' }} shadow-md">
                <i class="fas fa-store w-5 text-center"></i>
                <span>NEW SHOP</span>
              </a>
                <a href="{{ route('activation-keys.user.index') }}"
                   class="sidebar-link menu-pin flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('activation-keys.user.index') ? 'menu-item-active text-gray-800' : 'text-gray-800 hover:opacity-90' }} shadow-md">
                   <i class="fas fa-lock w-5 text-center"></i>
                   <span>E-PIN</span>
                </a>
            @endif

          @endif
    
          @if(auth()->guard('management')->check())
            <div class="pt-4 border-t border-gray-200">
              <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Management</p>
              <a href="{{ route('management.dashboard') }}" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('management.dashboard') ? 'bg-cyan-50 text-[var(--primary)]' : 'text-gray-600 hover:bg-cyan-50 hover:text-[var(--primary)]' }}">
                <i class="fas fa-chart-pie w-5 text-center"></i>
                <span>Dashboard</span>
              </a>
              
              <a href="{{ route('admin.users.index') }}" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-cyan-50 text-[var(--primary)]' : 'text-gray-600 hover:bg-cyan-50 hover:text-[var(--primary)]' }}">
                <i class="fas fa-users-cog w-5 text-center"></i>
                <span>Users</span>
              </a>
              <a href="{{ route('products.index') }}" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('products.*') ? 'bg-cyan-50 text-[var(--primary)]' : 'text-gray-600 hover:bg-cyan-50 hover:text-[var(--primary)]' }}">
                    <i class="fas fa-cube w-5 text-center"></i>
                    <span>Products</span>
              </a>
              
              <a href="{{ route('management.shops.index') }}" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('management.shops.*') ? 'bg-cyan-50 text-[var(--primary)]' : 'text-gray-600 hover:bg-cyan-50 hover:text-[var(--primary)]' }}">
                <i class="fas fa-shopping-bag w-5 text-center"></i>
                <span>Shops</span>
              </a>
              
              <a href="{{ route('shop.create') }}" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('shop.create') ? 'bg-cyan-50 text-[var(--primary)]' : 'text-gray-600 hover:bg-cyan-50 hover:text-[var(--primary)]' }}">
                <i class="fas fa-store-alt w-5 text-center"></i>
                <span>Create Shop</span>
              </a>
              
              <a href="{{ route('management.create') }}" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('management.create') ? 'bg-cyan-50 text-[var(--primary)]' : 'text-gray-600 hover:bg-cyan-50 hover:text-[var(--primary)]' }}">
                <i class="fas fa-user-shield w-5 text-center"></i>
                <span>Create Management</span>
              </a>
              
              <a href="{{ route('admin.notifications.index') }}" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('admin.notifications.*') ? 'bg-cyan-50 text-[var(--primary)]' : 'text-gray-600 hover:bg-cyan-50 hover:text-[var(--primary)]' }}">
                <i class="fas fa-envelope w-5 text-center"></i>
                <span>Notifications</span>
              </a>

              <a href="{{ route('commission-levels.index') }}" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('commission-levels.*') ? 'bg-cyan-50 text-[var(--primary)]' : 'text-gray-600 hover:bg-cyan-50 hover:text-[var(--primary)]' }}">
                <i class="fas fa-layer-group w-5 text-center"></i>
                <span>Commission Levels</span>
              </a>
              
              <a href="{{ route('income-settings.index') }}" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('income-settings.*') ? 'bg-cyan-50 text-[var(--primary)]' : 'text-gray-600 hover:bg-cyan-50 hover:text-[var(--primary)]' }}">
                <i class="fas fa-sliders-h w-5 text-center"></i>
                <span>Income Settings</span>
              </a>
              
                <a href="{{ route('activation-keys.index') }}"
                class="sidebar-link flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('activation-keys.index') ? 'bg-cyan-50 text-[var(--primary)]' : 'text-gray-600 hover:bg-cyan-50 hover:text-[var(--primary)]' }}">
                <i class="fas fa-key w-5 text-center"></i>
                <span>Activation Keys</span>
                </a>
                
                <a href="{{ route('activation-keys.assign.form') }}"
                class="sidebar-link flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('activation-keys.assign.form') ? 'bg-cyan-50 text-[var(--primary)]' : 'text-gray-600 hover:bg-cyan-50 hover:text-[var(--primary)]' }}">
                <i class="fas fa-hand-holding-usd w-5 text-center"></i>
                <span>Assign Keys</span>
                </a>
                
                <a href="{{ route('management.withdrawals') }}"
                class="sidebar-link flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('management.withdrawals') ? 'bg-cyan-50 text-[var(--primary)]' : 'text-gray-600 hover:bg-cyan-50 hover:text-[var(--primary)]' }}">
                <i class="fas fa-money-check-alt w-5 text-center"></i>
                <span>Withdrawals</span>
                </a>
            </div>
          @endif
    
          @if(auth()->guard('shop')->check())
            <div class="pt-4 border-t border-gray-200">
              <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Shop</p>
            <a href="{{ route('shop.dashboard') }}" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('shop.dashboard') ? 'bg-cyan-50 text-[var(--primary)]' : 'text-gray-600 hover:bg-cyan-50 hover:text-[var(--primary)]' }}">
              <i class="fas fa-chart-bar w-5 text-center"></i>
              <span>Dashboard</span>
            </a>
            </div>
          @endif
        </nav>
    
        <!-- Bottom Section -->
        <div class="mt-6 pt-6 border-t border-purple-200">
        <!-- User Info -->
        <div class="flex items-center space-x-3 p-3 rounded-lg bg-gray-100 text-gray-600 mb-3">
          <div class="w-8 h-8 rounded-full bg-cyan-100 flex items-center justify-center">
            <i class="fas fa-user text-cyan-500 text-sm"></i>
          </div>
          <div>
            <p class="text-sm font-medium">
              @if(auth()->guard('management')->check())
                Management User
              @elseif(auth()->guard('shop')->check())
                {{ auth()->guard('shop')->user()->name }}
              @else
                {{ auth()->user()->name }}
                {{ auth()->user()->referral_code }}
              @endif
            </p>
            <p class="text-xs text-gray-500">
              @if(auth()->guard('management')->check())
                Admin
              @elseif(auth()->guard('shop')->check())
                Shop Account
              @else
                {{ auth()->user()->kyc && auth()->user()->kyc->status === 'approved' ? 'Verified' : 'Member' }}
              @endif
            </p>
          </div>
        </div>
    
        <!-- Logout -->
        @php
          $logoutRoute = route('logout');
          if (auth()->guard('management')->check()) {
            $logoutRoute = route('management.logout');
          } elseif (auth()->guard('shop')->check()) {
            $logoutRoute = route('shop.logout');
          }
        @endphp
    
        <form method="POST" action="{{ $logoutRoute }}">
          @csrf
          <button type="submit" class="w-full text-left sidebar-link flex items-center space-x-3 p-3 rounded-lg text-gray-600 hover:bg-cyan-50 hover:text-[var(--primary)]">
            <i class="fas fa-sign-out-alt w-5 text-center"></i>
            <span>Logout</span>
          </button>
        </form>
      </div>
    </aside>


    <!-- Top Bar for Mobile -->
    <div class="md:hidden fixed top-0 left-0 right-0 bg-white shadow z-20 flex items-center justify-between px-4 h-14">
      <!-- Menu Button -->
      <button onclick="toggleMenu()" class="w-10 h-10 rounded-full bg-white shadow flex items-center justify-center text-[var(--primary)] text-xl">
        <i class="fas fa-bars"></i>
      </button>
    
      <!-- Logo -->
      <div class="flex items-center space-x-2">
        <div class="w-9 h-9 rounded-full bg-cyan-100 flex items-center justify-center">
          <i class="fas fa-rocket text-cyan-500"></i>
        </div>
        <span class="text-lg font-bold text-[var(--primary)]">Dream Life</span>
      </div>
    
      <!-- Right side actions -->
      <div class="flex items-center space-x-2">
        <!-- Notification Bell -->
        @if(auth()->check() && !auth()->guard('management')->check())
          <x-notification-bell :guard="auth()->guard('shop')->check() ? 'shop' : 'web'" />
        @endif
        
        <!-- User Avatar -->
        <div class="w-10 h-10 rounded-full bg-cyan-100 flex items-center justify-center">
          <i class="fas fa-user text-cyan-500"></i>
        </div>
      </div>
    </div>


    <!-- Mobile Sidebar -->
    <div id="mobileMenu" class="mobile-menu fixed inset-0 p-6 z-30 hidden md:hidden" style="background: linear-gradient(135deg, #e0e7ff 0%, #ddd6fe 25%, #fce7f3 50%, #fef3c7 75%, #dbeafe 100%);">
      <div class="flex justify-between items-center mb-8">
        <div class="flex items-center space-x-2">
          <div class="w-10 h-10 rounded-full bg-cyan-100 flex items-center justify-center">
            <i class="fas fa-rocket text-cyan-500"></i>
          </div>
          <h1 class="text-xl font-bold text-[var(--primary)]">Dream Life</h1>
        </div>
        <button onclick="toggleMenu()" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 text-xl">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <nav class="flex flex-col space-y-4">
        <!-- User Dashboard Links -->
        <a href="{{ route('showEarnings') }}" class="sidebar-link menu-dashboard flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('showEarnings') ? 'menu-item-active text-white' : 'text-white hover:opacity-90' }} font-medium shadow-md">
          <i class="fas fa-home w-5 text-center"></i>
          <span>Earnings</span>
        </a>

        <a href="{{ route('tree') }}" class="sidebar-link menu-tree flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('tree') ? 'menu-item-active text-white' : 'text-white hover:opacity-90' }} shadow-md">
          <i class="fas fa-project-diagram w-5 text-center"></i>
          <span>Referral Tree</span>
        </a>
        
        <a href="{{ route('table') }}" class="sidebar-link menu-team flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('table') ? 'menu-item-active text-white' : 'text-white hover:opacity-90' }} shadow-md">
          <i class="fas fa-users w-5 text-center"></i>
          <span>MY ALL TEAM</span>
        </a>

        <a href="{{ route('cashback') }}" class="sidebar-link menu-wallet flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('cashback') ? 'menu-item-active text-white' : 'text-white hover:opacity-90' }} shadow-md">
          <i class="fas fa-money-bill-wave w-5 text-center"></i>
          <span>MY SHOPPING WALLET</span>
        </a>

        <!-- KYC Section -->
        @if(auth()->check() && !auth()->user()->kyc)
          <a href="{{ route('kyc.create') }}" class="sidebar-link menu-kyc flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('kyc.*') ? 'menu-item-active text-white' : 'text-white hover:opacity-90' }} shadow-md">
            <i class="fas fa-file-alt w-5 text-center"></i>
            <span>Submit KYC</span>
          </a>
        @elseif(auth()->check() && auth()->user()->kyc)
          <a href="{{ route('kyc.edit') }}" class="sidebar-link menu-kyc flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('kyc.*') ? 'menu-item-active text-white' : 'text-white hover:opacity-90' }} shadow-md">
            <i class="fas fa-user-shield w-5 text-center"></i>
            <span>KYC MANAGER</span>
          </a>
        <a href="{{ route('register') }}" class="sidebar-link menu-referral flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('register') ? 'menu-item-active text-gray-800' : 'text-gray-800 hover:opacity-90' }} shadow-md">
        <i class="fas fa-user-plus w-5 text-center"></i>
        <span>REFERRAL FORM</span>
        </a>
        <a href="{{ route('shop.create') }}" class="sidebar-link menu-shop flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('shop.create') ? 'menu-item-active text-white' : 'text-white hover:opacity-90' }} shadow-md">
          <i class="fas fa-store w-5 text-center"></i>
          <span>NEW SHOP</span>
        </a>
        <a href="{{ route('activation-keys.user.index') }}"
        class="sidebar-link menu-pin flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('activation-keys.user.index') ? 'menu-item-active text-gray-800' : 'text-gray-800 hover:opacity-90' }} shadow-md">
        <i class="fas fa-lock w-5 text-center"></i>
        <span>E-PIN</span>
        </a>
        @endif



        <!-- Management Links (only for management users) -->
        @if(auth()->guard('management')->check())
          <div class="pt-4 border-t border-gray-200">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Management</p>
            
            <a href="{{ route('management.dashboard') }}" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('management.dashboard') ? 'bg-cyan-50 text-[var(--primary)]' : 'text-gray-600 hover:bg-cyan-50 hover:text-[var(--primary)]' }}">
              <i class="fas fa-chart-pie w-5 text-center"></i>
              <span>Dashboard</span>
            </a>

            <a href="{{ route('management.shops.index') }}" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('management.shops.*') ? 'bg-cyan-50 text-[var(--primary)]' : 'text-gray-600 hover:bg-cyan-50 hover:text-[var(--primary)]' }}">
              <i class="fas fa-shopping-bag w-5 text-center"></i>
              <span>Shops</span>
            </a>

            <a href="{{ route('shop.create') }}" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('shop.create') ? 'bg-cyan-50 text-[var(--primary)]' : 'text-gray-600 hover:bg-cyan-50 hover:text-[var(--primary)]' }}">
              <i class="fas fa-store-alt w-5 text-center"></i>
              <span>Create Shop</span>
            </a>

            <a href="{{ route('commission-levels.index') }}" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('commission-levels.*') ? 'bg-cyan-50 text-[var(--primary)]' : 'text-gray-600 hover:bg-cyan-50 hover:text-[var(--primary)]' }}">
              <i class="fas fa-layer-group w-5 text-center"></i>
              <span>Commission Levels</span>
            </a>
            
            <a href="{{ route('income-settings.index') }}" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('income-settings.*') ? 'bg-cyan-50 text-[var(--primary)]' : 'text-gray-600 hover:bg-cyan-50 hover:text-[var(--primary)]' }}">
              <i class="fas fa-sliders-h w-5 text-center"></i>
              <span>Income Settings</span>
            </a>
            
            <a href="{{ route('activation-keys.index') }}"
               class="sidebar-link flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('activation-keys.index') ? 'bg-cyan-50 text-[var(--primary)]' : 'text-gray-600 hover:bg-cyan-50 hover:text-[var(--primary)]' }}">
               <i class="fas fa-key w-5 text-center"></i>
               <span>Activation Keys</span>
            </a>
            
            <a href="{{ route('activation-keys.assign.form') }}"
               class="sidebar-link flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('activation-keys.assign.form') ? 'bg-cyan-50 text-[var(--primary)]' : 'text-gray-600 hover:bg-cyan-50 hover:text-[var(--primary)]' }}">
               <i class="fas fa-hand-holding-usd w-5 text-center"></i>
               <span>Assign Keys</span>
            </a>
            
            <a href="{{ route('management.withdrawals') }}"
               class="sidebar-link flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('management.withdrawals') ? 'bg-cyan-50 text-[var(--primary)]' : 'text-gray-600 hover:bg-cyan-50 hover:text-[var(--primary)]' }}">
               <i class="fas fa-money-check-alt w-5 text-center"></i>
               <span>Withdrawals</span>
            </a>
          </div>
        @endif

        <!-- Shop Links (only for shop users) -->
        @if(auth()->guard('shop')->check())
          <div class="pt-4 border-t border-gray-200">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Shop</p>
            
            <a href="{{ route('shop.dashboard') }}" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('shop.dashboard') ? 'bg-cyan-50 text-[var(--primary)]' : 'text-gray-600 hover:bg-cyan-50 hover:text-[var(--primary)]' }}">
              <i class="fas fa-chart-bar w-5 text-center"></i>
              <span>Dashboard</span>
            </a>
          </div>
        @endif
      </nav>

      <div class="absolute bottom-6 left-6 right-6">
        <div class="flex items-center space-x-3 p-3 rounded-lg bg-gray-100 text-gray-600">
          <div class="w-8 h-8 rounded-full bg-cyan-100 flex items-center justify-center">
            <i class="fas fa-user text-cyan-500 text-sm"></i>
          </div>
          <div>
            <p class="text-sm font-medium">
              @if(auth()->guard('management')->check())
                Management User
              @elseif(auth()->guard('shop')->check())
                {{ auth()->guard('shop')->user()->name }}
              @else
                {{ auth()->user()->name }}
              @endif
            </p>
            <p class="text-xs text-gray-500">
              @if(auth()->guard('management')->check())
                Admin
              @elseif(auth()->guard('shop')->check())
                Shop Account
              @else
                {{ auth()->user()->kyc && auth()->user()->kyc->status === 'approved' ? 'Verified' : 'Member' }}
              @endif
            </p>
          </div>
        </div>
      </div>

      <form method="POST" action="{{ 
        auth()->guard('management')->check() ? route('management.logout') : 
        (auth()->guard('shop')->check() ? route('shop.logout') : route('logout')) 
      }}">
        @csrf
        <button type="submit" class="w-full text-left sidebar-link flex items-center space-x-3 p-3 rounded-lg text-gray-600 hover:bg-cyan-50 hover:text-[var(--primary)]">
          <i class="fas fa-sign-out-alt w-5 text-center"></i>
          <span>Logout</span>
        </button>
      </form>
    </div>
    
    <!-- Main Content -->
    <main class="flex-1 p-4 md:p-6 md:ml-64 pt-16 md:pt-6">
        {{-- Validation Errors --}}
        @if ($errors->any())
          <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <ul class="list-disc pl-5">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

      @yield('content')
    </main>

    <!-- Static Scripts -->
    <script>
      function toggleMenu() {
        const menu = document.getElementById('mobileMenu');
        menu.classList.toggle('hidden');
        document.body.style.overflow = menu.classList.contains('hidden') ? 'auto' : 'hidden';
      }

      document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('#mobileMenu a').forEach(link => {
          link.addEventListener('click', () => {
            document.getElementById('mobileMenu').classList.add('hidden');
            document.body.style.overflow = 'auto';
          });
        });
      });
    </script>

    <!-- Dynamic Page Scripts -->
    @stack('scripts')
    <script src="//unpkg.com/alpinejs" defer></script>

  </body>
</html>