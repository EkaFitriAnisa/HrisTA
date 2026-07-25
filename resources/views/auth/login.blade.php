<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login | HRIS Employee Clearances</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                    }
                }
            }
        }
    </script>

</head>

<body class="min-h-screen bg-[#f1f5f9] flex items-center justify-center p-6">

    <!-- OUTER CARD -->
    <div class="w-full max-w-6xl bg-white rounded-3xl shadow-xl p-4">

        <div class="grid lg:grid-cols-2 rounded-2xl overflow-hidden">

            <!-- LEFT -->
            <div class="bg-white px-14 flex flex-col justify-center">

                <h1 class="text-3xl font-semibold text-gray-800 text-center lg:text-left">
                    Selamat Datang Kembali di Website Ini
                </h1>

                <p class="text-gray-500 mt-2 text-center lg:text-left">
                    Masuk ke sistem HRIS Employee Clearance 
                    
                </p>

                {{-- FORM LOGIN --}}
                <form method="POST" action="{{ route('login.process') }}" class="mt-8 space-y-5">

                    @csrf
                    @if ($errors->any())
                        <div class="px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-500">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <div>
                        <label class="text-sm text-gray-700">
                            Badge ID
                        </label>
                        <input type="text" name="badge_id" value="{{ old('badge_id') }}" placeholder="Masukkan Badge ID" autocomplete="off"
                            class="mt-1 w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>

                        <label class="text-sm text-gray-700">
                            Password
                        </label>

                        <div class="relative mt-1">

                            <input type="password" name="kata_sandi" id="password" placeholder="••••••••"
                                class="w-full px-4 py-3 pr-12 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:outline-none">

                            <!-- BUTTON EYE -->
                            <button type="button" onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-400 hover:text-blue-600 transition">

                                <!-- EYE OPEN -->
                                <svg id="eye-open" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>

                                <!-- EYE SLASH -->
                                <svg id="eye-close" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.956 9.956 0 012.042-3.368M6.223 6.223A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.965 9.965 0 01-4.293 5.568M15 12a3 3 0 00-4.243-4.243M3 3l18 18" />

                                </svg>

                            </button>

                        </div>

                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold">
                        Masuk
                    </button>

                </form>

                <p class="text-xs text-gray-400 mt-8 text-center lg:text-left">
                    © 2026 HRIS Employee Clearance
                </p>

            </div>

            <!-- RIGHT PANEL -->
            <div class="p-3">

                <div class="h-full rounded-2xl overflow-hidden bg-gradient-to-br from-blue-600 via-blue-500 to-sky-400
                flex flex-col justify-between">

                    <!-- TEXT -->
                    <div class="p-10 text-white">

                        <h2 class="text-3xl font-semibold leading-tight text-white">
                            Manage employee clearance
                            with confidence
                        </h2>

                        <p class="mt-4 text-white">
                            Pantau status clearance, approval antar departemen,
                            dan pastikan semua proses HR berjalan rapi dan transparan.
                        </p>

                    </div>

                    <!-- IMAGE CONTAINER -->
                    <div class="flex items-end justify-center flex-1 pb-0">

                        <img src="{{ asset('images/hris.png') }}" class="w-full max-w-xl object-contain"
                            alt="HRIS Illustration">

                    </div>

                </div>

            </div>

        </div>

    </div>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');

            const eyeOpen = document.getElementById('eye-open');

            const eyeClose = document.getElementById('eye-close');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';

                eyeOpen.classList.add('hidden');

                eyeClose.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';

                eyeOpen.classList.remove('hidden');

                eyeClose.classList.add('hidden');
            }
        }
    </script>
</body>

</html>
