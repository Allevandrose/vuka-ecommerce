{{-- resources/views/auth/vendor-apply.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Become a Vendor · {{ config('app.name', 'Vuka Shop') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink: '#14231C',
                        moss: '#20503C',
                        gold: '#E7A93B',
                        fill: '#F3F3F0',
                        muted: '#6C7268',
                    },
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                },
            },
        }
    </script>

    <style type="text/tailwindcss">
        @layer components {
            .label { @apply block text-[13px] font-semibold text-ink mb-2; }
            .label .opt { @apply text-muted font-normal ml-1; }
            .wrap { @apply relative; }
            .input {
                @apply w-full rounded-xl bg-fill border border-transparent px-4 py-3.5 text-base sm:text-sm text-ink
                       placeholder:text-muted/60 transition
                       focus:outline-none focus:bg-white focus:border-moss focus:ring-4 focus:ring-moss/10;
            }
            .input.has-icon { @apply pl-11; }
            .input.is-invalid { @apply bg-red-50 border-red-300 focus:border-red-500 focus:ring-red-100; }
            .icon { @apply absolute left-4 top-1/2 -translate-y-1/2 text-muted/70 text-sm pointer-events-none; }
            .wrap:focus-within .icon { @apply text-moss; }
            .tick { @apply absolute right-4 top-1/2 -translate-y-1/2 text-emerald-500 text-sm opacity-0 scale-75 transition pointer-events-none; }
            .tick.show { @apply opacity-100 scale-100; }
            .err { @apply mt-1.5 text-xs text-red-600; }
            .group-title { @apply text-xs font-semibold text-muted mb-3 flex items-center gap-3; }
            .group-title::after { content: ''; @apply flex-1 h-px bg-black/[0.07]; }
        }
        body { font-family: 'Inter', sans-serif; }
        @media (prefers-reduced-motion: reduce) { * { transition: none !important; } }
    </style>
</head>
<body class="bg-[#121a16] text-ink antialiased min-h-screen sm:flex sm:items-center sm:justify-center sm:p-6 lg:p-10">

    <div class="w-full max-w-6xl bg-white sm:rounded-[28px] p-2 sm:p-3 lg:grid lg:grid-cols-2 lg:gap-3 shadow-2xl">

        {{-- ================= LEFT: brand panel ================= --}}
        <aside class="relative overflow-hidden rounded-[22px] text-white px-6 py-7 sm:px-9 sm:py-9 lg:p-10 flex flex-col justify-between lg:self-start lg:sticky lg:top-3 lg:h-[calc(100vh-1.5rem)] lg:max-h-[760px] lg:min-h-[560px]"
               style="background: radial-gradient(90% 70% at 20% 0%, #4c9a76 0%, rgba(76,154,118,0) 60%), linear-gradient(160deg, #2b6b50 0%, #20503C 45%, #0f2a1e 100%);">

            <div class="pointer-events-none absolute -top-24 -right-16 w-72 h-72 rounded-full bg-gold/25 blur-3xl"></div>

            <a href="{{ route('home') }}" class="relative inline-flex items-center gap-2 font-semibold text-lg tracking-tight w-fit">
                <span class="w-7 h-7 rounded-lg bg-white/15 border border-white/20 flex items-center justify-center text-sm"><i class="fas fa-store text-gold"></i></span>
                Vuka<span class="text-gold -ml-1.5">Shop</span>
            </a>

            <div class="relative mt-10 lg:mt-0">
                <span class="inline-flex items-center gap-2 text-xs font-medium bg-white/15 border border-white/20 backdrop-blur rounded-full px-3.5 py-1.5">
                    Join us to sell <span aria-hidden="true">🛍️</span>
                </span>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-semibold tracking-tight leading-[1.05] mt-4">
                    Start selling<br class="hidden sm:inline"> with VukaShop
                </h1>
                <p class="text-white/70 text-sm mt-3 max-w-sm">
                    Follow these simple steps to open your shop. We handle payments, logistics and support.
                </p>

                {{-- Steps --}}
                <div class="hidden sm:grid grid-cols-3 gap-2.5 mt-8 lg:mt-10">
                    <div class="rounded-2xl bg-white text-ink p-3.5 lg:p-4 min-h-[110px] flex flex-col justify-between">
                        <span class="w-6 h-6 rounded-full bg-moss text-white text-[11px] font-bold flex items-center justify-center">1</span>
                        <p class="text-xs font-semibold leading-snug mt-6">Apply with your shop details</p>
                    </div>
                    <div class="rounded-2xl bg-white/15 border border-white/15 backdrop-blur-md p-3.5 lg:p-4 min-h-[110px] flex flex-col justify-between">
                        <span class="w-6 h-6 rounded-full border border-white/40 text-white/80 text-[11px] font-bold flex items-center justify-center">2</span>
                        <p class="text-xs text-white/70 leading-snug mt-6">We review and verify you</p>
                    </div>
                    <div class="rounded-2xl bg-white/15 border border-white/15 backdrop-blur-md p-3.5 lg:p-4 min-h-[110px] flex flex-col justify-between">
                        <span class="w-6 h-6 rounded-full border border-white/40 text-white/80 text-[11px] font-bold flex items-center justify-center">3</span>
                        <p class="text-xs text-white/70 leading-snug mt-6">List products and get orders</p>
                    </div>
                </div>
            </div>
        </aside>

        {{-- ================= RIGHT: form ================= --}}
        <main class="px-5 sm:px-10 lg:px-12 py-8 sm:py-10 lg:py-12">
            <div class="w-full max-w-md mx-auto">

                <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight text-center">Become a vendor</h2>
                <p class="text-sm text-muted text-center mt-2">Tell us about you and your shop. It takes about two minutes.</p>

                <div class="mt-7 space-y-3 empty:hidden">
                    @if (session('success'))
                        <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl flex items-start gap-3" role="status">
                            <i class="fas fa-check-circle text-emerald-600 mt-0.5"></i>
                            <p class="text-sm text-emerald-800">{{ session('success') }}</p>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="p-3.5 bg-red-50 border border-red-200 rounded-xl flex items-start gap-3" role="alert">
                            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
                            <p class="text-sm text-red-800">{{ session('error') }}</p>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="p-3.5 bg-red-50 border border-red-200 rounded-xl" role="alert">
                            <p class="text-sm font-semibold text-red-800 mb-1 flex items-center gap-2">
                                <i class="fas fa-exclamation-triangle"></i> Please fix the following
                            </p>
                            <ul class="text-sm text-red-700 list-disc list-inside space-y-0.5">
                                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <form id="vendor-form" method="POST" action="{{ route('vendor.apply.store') }}" novalidate class="mt-7 space-y-7">
                    @csrf

                    {{-- Your details --}}
                    <section>
                        <p class="group-title">Your details</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="label">Full name</label>
                                <div class="wrap">
                                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                                           autocomplete="name" placeholder="Jane Wanjiru" data-tick
                                           class="input pr-10 @error('name') is-invalid @enderror">
                                    <i class="fas fa-check-circle tick"></i>
                                </div>
                                @error('name') <p class="err">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="email" class="label">Email address</label>
                                <div class="wrap">
                                    <input id="email" name="email" type="email" value="{{ old('email') }}" required
                                           autocomplete="email" inputmode="email" placeholder="you@example.com" data-tick
                                           class="input pr-10 @error('email') is-invalid @enderror">
                                    <i class="fas fa-check-circle tick"></i>
                                </div>
                                @error('email') <p class="err">{{ $message }}</p> @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label for="phone" class="label">Phone number<span class="opt">optional</span></label>
                                <div class="wrap">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-ink/80 pointer-events-none flex items-center gap-2">
                                        <span aria-hidden="true">🇰🇪</span><span class="text-muted">+254</span>
                                    </span>
                                    <input id="phone" name="phone" type="tel" value="{{ old('phone') }}"
                                           autocomplete="tel" inputmode="tel" placeholder="700 000 000"
                                           class="input pl-[5.5rem] @error('phone') is-invalid @enderror">
                                </div>
                                @error('phone') <p class="err">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </section>

                    {{-- Your shop --}}
                    <section>
                        <p class="group-title">Your shop</p>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label for="shop_name" class="label">Shop name</label>
                                <div class="wrap">
                                    <input id="shop_name" name="shop_name" type="text" value="{{ old('shop_name') }}" required
                                           placeholder="Wanjiru Electronics" data-tick
                                           class="input pr-10 @error('shop_name') is-invalid @enderror">
                                    <i class="fas fa-check-circle tick"></i>
                                </div>
                                @error('shop_name') <p class="err">{{ $message }}</p> @enderror
                            </div>
                            <div class="col-span-2">
                                <label for="shop_address" class="label">Shop address</label>
                                <div class="wrap">
                                    <i class="fas fa-location-dot icon"></i>
                                    <input id="shop_address" name="shop_address" type="text" value="{{ old('shop_address') }}" required
                                           autocomplete="street-address" placeholder="e.g. 123 Kenyatta Ave, Nairobi" data-tick
                                           class="input has-icon pr-10 @error('shop_address') is-invalid @enderror">
                                    <i class="fas fa-check-circle tick"></i>
                                </div>
                                @error('shop_address') <p class="err">{{ $message }}</p> @enderror
                            </div>

                            <div class="col-span-2 flex items-center justify-between gap-3 -mb-1">
                                <span class="label !mb-0">Map location<span class="opt">optional</span></span>
                                <button type="button" id="use-location"
                                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-moss bg-moss/10 hover:bg-moss/15 rounded-full px-3 py-1.5 transition-colors disabled:opacity-60">
                                    <i class="fas fa-crosshairs"></i> Use my location
                                </button>
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label for="shop_latitude" class="sr-only">Latitude</label>
                                <input id="shop_latitude" name="shop_latitude" type="text" inputmode="decimal"
                                       value="{{ old('shop_latitude') }}" placeholder="Latitude, e.g. -1.2920660"
                                       class="input @error('shop_latitude') is-invalid @enderror">
                                @error('shop_latitude') <p class="err">{{ $message }}</p> @enderror
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label for="shop_longitude" class="sr-only">Longitude</label>
                                <input id="shop_longitude" name="shop_longitude" type="text" inputmode="decimal"
                                       value="{{ old('shop_longitude') }}" placeholder="Longitude, e.g. 36.8219460"
                                       class="input @error('shop_longitude') is-invalid @enderror">
                                @error('shop_longitude') <p class="err">{{ $message }}</p> @enderror
                            </div>
                            <p id="location-status" class="col-span-2 text-xs text-muted -mt-2 hidden"></p>
                        </div>
                    </section>

                    {{-- Products --}}
                    <section>
                        <p class="group-title">About your products</p>
                        <div class="space-y-4">
                            <div>
                                <label for="product_categories" class="label">What do you sell?<span class="opt">optional</span></label>
                                <textarea id="product_categories" name="product_categories" rows="2"
                                          placeholder="e.g. Electronics, phone accessories, home appliances"
                                          class="input resize-y @error('product_categories') is-invalid @enderror">{{ old('product_categories') }}</textarea>
                                @error('product_categories') <p class="err">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="message" class="label">Anything else?<span class="opt">optional</span></label>
                                <textarea id="message" name="message" rows="3" maxlength="1000"
                                          placeholder="Tell us about your shop or why you want to join"
                                          class="input resize-y @error('message') is-invalid @enderror">{{ old('message') }}</textarea>
                                <div class="flex justify-between gap-3">
                                    <div>@error('message') <p class="err">{{ $message }}</p> @enderror</div>
                                    <p class="mt-1.5 text-xs text-muted tabular-nums"><span id="msg-count">0</span>/1000</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Actions --}}
                    <div class="space-y-4">
                        <button type="submit" id="submit-btn"
                                class="w-full inline-flex items-center justify-center gap-2 bg-moss text-white font-semibold py-3.5 rounded-xl hover:bg-moss/90 active:scale-[0.99] transition text-sm focus:outline-none focus:ring-4 focus:ring-moss/25 disabled:opacity-70 disabled:cursor-not-allowed">
                            <span id="submit-label">Submit application</span>
                            <i id="submit-icon" class="fas fa-arrow-right text-xs"></i>
                        </button>

                        <p class="text-center text-xs text-muted">
                            Already have an account?
                            <a href="{{ route('login') }}" class="text-moss font-semibold hover:underline">Log in</a>
                        </p>

                        <div class="flex items-center gap-3 text-xs text-muted">
                            <span class="flex-1 h-px bg-black/[0.08]"></span>Or<span class="flex-1 h-px bg-black/[0.08]"></span>
                        </div>

                        <a href="{{ route('vendor.apply.pending') }}"
                           class="w-full inline-flex items-center justify-center gap-2 border border-black/10 rounded-xl py-3.5 text-sm font-semibold hover:bg-fill transition-colors">
                            <i class="fas fa-hourglass-half text-moss text-xs"></i> Check application status
                        </a>

                        <p class="text-center text-[11px] leading-relaxed text-muted px-2">
                            We review every application. If approved, you'll get an email with a private link to create your vendor account.
                            <a href="{{ route('home') }}" class="block mt-2 text-moss font-semibold hover:underline">Back to home</a>
                        </p>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        (function () {
            const form = document.getElementById('vendor-form');

            // Green tick once a field is valid
            const ticked = form.querySelectorAll('[data-tick]');
            const check = el => {
                const ok = el.value.trim() !== '' && el.checkValidity();
                const t = el.parentElement.querySelector('.tick');
                if (t) t.classList.toggle('show', ok);
            };
            ticked.forEach(el => { check(el); el.addEventListener('input', () => { check(el); el.classList.remove('is-invalid'); }); });
            form.querySelectorAll('.is-invalid').forEach(el =>
                el.addEventListener('input', () => el.classList.remove('is-invalid'), { once: true }));

            // Character counter
            const msg = document.getElementById('message'), count = document.getElementById('msg-count');
            const sync = () => count.textContent = msg.value.length;
            msg.addEventListener('input', sync); sync();

            // Use my location
            const btn = document.getElementById('use-location'), status = document.getElementById('location-status');
            const say = (t, err) => {
                status.textContent = t;
                status.classList.remove('hidden', 'text-red-600', 'text-muted');
                status.classList.add(err ? 'text-red-600' : 'text-muted');
            };
            btn.addEventListener('click', () => {
                if (!navigator.geolocation) return say('Location is not supported on this device.', true);
                btn.disabled = true; say('Finding your location…');
                navigator.geolocation.getCurrentPosition(pos => {
                    document.getElementById('shop_latitude').value = pos.coords.latitude.toFixed(7);
                    document.getElementById('shop_longitude').value = pos.coords.longitude.toFixed(7);
                    say('Coordinates filled in. Check they match your shop, not where you are now.');
                    btn.disabled = false;
                }, () => {
                    say('Could not get your location. Allow access or enter the coordinates manually.', true);
                    btn.disabled = false;
                }, { enableHighAccuracy: true, timeout: 10000 });
            });

            // Prevent double submit
            form.addEventListener('submit', () => {
                document.getElementById('submit-btn').disabled = true;
                document.getElementById('submit-label').textContent = 'Submitting…';
                document.getElementById('submit-icon').className = 'fas fa-circle-notch fa-spin text-xs';
            });
        })();
    </script>
</body>
</html>