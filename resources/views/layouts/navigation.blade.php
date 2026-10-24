@php
    $cartCount = auth()->check() ? auth()->user()->cartCount() : 0;
@endphp

<nav x-data="{ open: false }"
     class="bg-[#F7F5EF]/90 backdrop-blur-md border-b border-[#E4E0D5] sticky top-0 z-30"
     style="font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16 gap-3">

            {{-- Logo --}}
            <div class="shrink-0 flex items-center">
                <a href="{{ route('home') }}" aria-label="VukaShop home">
                    <img src="{{ asset('images/vukashop-logo.svg') }}" alt="VukaShop" class="h-9 sm:h-10 w-auto">
                </a>
            </div>

            {{-- Desktop nav links --}}
            <div class="hidden sm:flex items-center gap-1 sm:ms-6 flex-1">
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium transition-colors
                          {{ request()->routeIs('dashboard')
                              ? 'bg-white text-[#20503C] shadow-sm'
                              : 'text-[#14231C]/80 hover:bg-white/70 hover:text-[#20503C]' }}">
                    Dashboard
                </a>

                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}"
                           class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium transition-colors
                                  {{ request()->routeIs('admin.dashboard')
                                      ? 'bg-white text-[#20503C] shadow-sm'
                                      : 'text-[#14231C]/80 hover:bg-white/70 hover:text-[#20503C]' }}">
                            Admin Panel
                        </a>
                    @endif

                    @if(auth()->user()->isCustomer())
                        <a href="{{ route('home') }}"
                           class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium transition-colors
                                  {{ request()->routeIs('home')
                                      ? 'bg-white text-[#20503C] shadow-sm'
                                      : 'text-[#14231C]/80 hover:bg-white/70 hover:text-[#20503C]' }}">
                            Shop
                        </a>
                        <a href="#"
                           class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium text-[#14231C]/50 hover:bg-white/50 transition-colors cursor-not-allowed"
                           title="Coming soon">
                            Orders
                        </a>
                    @endif

                    @if(auth()->user()->isDelivery())
                        <a href="{{ route('delivery.dashboard') }}"
                           class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium transition-colors
                                  {{ request()->routeIs('delivery.dashboard')
                                      ? 'bg-white text-[#20503C] shadow-sm'
                                      : 'text-[#14231C]/80 hover:bg-white/70 hover:text-[#20503C]' }}">
                            Deliveries
                        </a>
                    @endif

                    @if(auth()->user()->isPickup())
                        <a href="{{ route('pickup.dashboard') }}"
                           class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium transition-colors
                                  {{ request()->routeIs('pickup.dashboard')
                                      ? 'bg-white text-[#20503C] shadow-sm'
                                      : 'text-[#14231C]/80 hover:bg-white/70 hover:text-[#20503C]' }}">
                            Pickups
                        </a>
                    @endif

                    @if(auth()->user()->isVendor())
                        <a href="{{ route('vendor.dashboard') }}"
                           class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium transition-colors
                                  {{ request()->routeIs('vendor.dashboard')
                                      ? 'bg-white text-[#20503C] shadow-sm'
                                      : 'text-[#14231C]/80 hover:bg-white/70 hover:text-[#20503C]' }}">
                            My Shop
                        </a>
                        <a href="{{ route('vendor.products.index') }}"
                           class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium transition-colors
                                  {{ request()->routeIs('vendor.products.*')
                                      ? 'bg-white text-[#20503C] shadow-sm'
                                      : 'text-[#14231C]/80 hover:bg-white/70 hover:text-[#20503C]' }}">
                            My Products
                        </a>
                    @endif
                @endauth
            </div>

            {{-- Right side: cart icon + account dropdown --}}
            <div class="hidden sm:flex items-center gap-2 sm:ms-6">

                {{-- Cart --}}
                <a href="{{ route('cart.index') }}"
                   aria-label="Cart"
                   class="relative h-10 w-10 rounded-full bg-white border border-[#E4E0D5] flex items-center justify-center text-[#14231C]/70 hover:text-[#E7A93B] hover:bg-white transition-colors shadow-sm">
                    <i class="fas fa-shopping-bag text-sm"></i>
                    @if ($cartCount > 0)
                        <span class="absolute -top-1 -right-1 bg-[#E7A93B] text-[#14231C] text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center ring-2 ring-[#F7F5EF]">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>

                <span class="h-6 w-px bg-[#E4E0D5]" aria-hidden="true"></span>

                {{-- Account dropdown --}}
                <div x-data="{ open: false }" @click.outside="open = false" @keydown.escape="open = false" class="relative">
                    <button @click="open = !open"
                            class="h-10 pl-3 pr-3.5 rounded-full bg-white border border-[#E4E0D5] flex items-center gap-2 text-sm font-semibold text-[#14231C] hover:bg-[#E4E0D5]/40 transition-colors shadow-sm"
                            aria-haspopup="true" :aria-expanded="open.toString()">
                        <i class="fas fa-user-circle text-[#20503C] text-base"></i>
                        <span class="max-w-[10rem] truncate">{{ Auth::user()->name }}</span>
                        <i class="fas fa-chevron-down text-[10px] text-[#6C7268] transition-transform"
                           :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="open" x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-60 origin-top-right bg-white border border-[#E4E0D5] rounded-2xl shadow-xl p-1.5 z-40">

                        <div class="px-3 py-2 border-b border-[#E4E0D5]/70 mb-1">
                            <p class="text-sm font-semibold text-[#14231C] truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-[#6C7268] truncate">{{ Auth::user()->email }}</p>
                            <p class="text-[11px] text-[#6C7268] mt-1">
                                Role: <span class="font-semibold text-[#20503C]">{{ ucfirst(Auth::user()->user_type) }}</span>
                            </p>
                        </div>

                        <a href="{{ route('profile.edit') }}"
                           class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium text-[#14231C] hover:bg-[#F7F5EF] transition-colors">
                            <i class="fas fa-user text-[#20503C] w-4 text-center"></i> Profile
                        </a>

                        <a href="{{ route('dashboard') }}"
                           class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium text-[#14231C] hover:bg-[#F7F5EF] transition-colors">
                            <i class="fas fa-gauge text-[#20503C] w-4 text-center"></i> Dashboard
                        </a>

                        <a href="{{ route('cart.index') }}"
                           class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium text-[#14231C] hover:bg-[#F7F5EF] transition-colors">
                            <i class="fas fa-shopping-bag text-[#20503C] w-4 text-center"></i> My Cart
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium text-left text-[#14231C] hover:bg-[#F7F5EF] transition-colors">
                                <i class="fas fa-arrow-right-from-bracket text-[#6C7268] w-4 text-center"></i> Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Mobile right-side actions + hamburger --}}
            <div class="flex sm:hidden items-center gap-2 -me-2">
                <a href="{{ route('cart.index') }}"
                   aria-label="Cart"
                   class="relative h-10 w-10 rounded-full bg-white border border-[#E4E0D5] flex items-center justify-center text-[#14231C]/70 shadow-sm">
                    <i class="fas fa-shopping-bag text-sm"></i>
                    @if ($cartCount > 0)
                        <span class="absolute -top-1 -right-1 bg-[#E7A93B] text-[#14231C] text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center ring-2 ring-[#F7F5EF]">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>
                <button @click="open = ! open"
                        class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-white border border-[#E4E0D5] text-[#14231C]/70 hover:bg-[#E4E0D5]/40 transition-colors shadow-sm"
                        aria-label="Toggle menu">
                    <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-[#E4E0D5] bg-[#F7F5EF]">
        <div class="px-4 py-4 space-y-1">

            <a href="{{ route('dashboard') }}"
               class="block px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ request()->routeIs('dashboard')
                          ? 'bg-white text-[#20503C] shadow-sm'
                          : 'text-[#14231C]/85 hover:bg-white/70' }}">
                Dashboard
            </a>

            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}"
                       class="block px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                              {{ request()->routeIs('admin.dashboard')
                                  ? 'bg-white text-[#20503C] shadow-sm'
                                  : 'text-[#14231C]/85 hover:bg-white/70' }}">
                        Admin Panel
                    </a>
                @endif

                @if(auth()->user()->isCustomer())
                    <a href="{{ route('home') }}"
                       class="block px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                              {{ request()->routeIs('home')
                                  ? 'bg-white text-[#20503C] shadow-sm'
                                  : 'text-[#14231C]/85 hover:bg-white/70' }}">
                        Shop
                    </a>
                    <a href="#"
                       class="block px-3 py-2.5 rounded-xl text-sm font-medium text-[#14231C]/50 cursor-not-allowed"
                       title="Coming soon">
                        Orders <span class="text-[10px] uppercase tracking-wide bg-white text-[#6C7268] px-1.5 py-0.5 rounded-full ml-1">Soon</span>
                    </a>
                @endif

                @if(auth()->user()->isDelivery())
                    <a href="{{ route('delivery.dashboard') }}"
                       class="block px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                              {{ request()->routeIs('delivery.dashboard')
                                  ? 'bg-white text-[#20503C] shadow-sm'
                                  : 'text-[#14231C]/85 hover:bg-white/70' }}">
                        Deliveries
                    </a>
                @endif

                @if(auth()->user()->isPickup())
                    <a href="{{ route('pickup.dashboard') }}"
                       class="block px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                              {{ request()->routeIs('pickup.dashboard')
                                  ? 'bg-white text-[#20503C] shadow-sm'
                                  : 'text-[#14231C]/85 hover:bg-white/70' }}">
                        Pickups
                    </a>
                @endif

                @if(auth()->user()->isVendor())
                    <a href="{{ route('vendor.dashboard') }}"
                       class="block px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                              {{ request()->routeIs('vendor.dashboard')
                                  ? 'bg-white text-[#20503C] shadow-sm'
                                  : 'text-[#14231C]/85 hover:bg-white/70' }}">
                        My Shop
                    </a>
                    <a href="{{ route('vendor.products.index') }}"
                       class="block px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                              {{ request()->routeIs('vendor.products.*')
                                  ? 'bg-white text-[#20503C] shadow-sm'
                                  : 'text-[#14231C]/85 hover:bg-white/70' }}">
                        My Products
                    </a>
                @endif
            @endauth
        </div>

        <div class="px-4 py-4 border-t border-[#E4E0D5] bg-white">
            <div class="mb-3">
                <p class="text-sm font-semibold text-[#14231C] truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-[#6C7268] truncate">{{ Auth::user()->email }}</p>
                <p class="text-[11px] text-[#6C7268] mt-1">
                    Role: <span class="font-semibold text-[#20503C]">{{ ucfirst(Auth::user()->user_type) }}</span>
                </p>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('profile.edit') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-full bg-[#F7F5EF] border border-[#E4E0D5] text-sm font-semibold py-2.5 text-[#14231C] hover:bg-[#E4E0D5]/60 transition-colors">
                    <i class="fas fa-user text-xs"></i> Profile
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 rounded-full bg-[#20503C] text-white text-sm font-semibold py-2.5 hover:bg-[#20503C]/90 transition-colors">
                        <i class="fas fa-arrow-right-from-bracket text-xs"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>