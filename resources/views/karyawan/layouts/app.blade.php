<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $tabTitles = [
            'karyawan.dashboard'       => 'Dashboard',
            'karyawan.clearance.index' => 'Clearance Saya',
            'karyawan.clearance.create'=> 'Ajukan Clearance',
            'karyawan.clearance.show'  => 'Detail Clearance',
            'karyawan.clearance.revisi'=> 'Revisi Clearance',
        ];
        $tabTitle = $tabTitles[Route::currentRouteName()] ?? 'Employee Portal';
    @endphp

    <title>{{ $tabTitle }} | Employee Clearance</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        #main-content {
            transition: margin-left .25s cubic-bezier(.4, 0, .2, 1);
        }

        @media (min-width: 1024px) {
            #main-content {
                margin-left: 256px;
            }
        }

        @media (max-width: 1023px) {
            #main-content {
                margin-left: 0 !important;
            }
        }
    </style>

    @stack('styles')
</head>

<body class="bg-gray-50 text-gray-800 antialiased">

    <x-sidebar :active="$activePage ?? ''" />

    <div id="main-content" class="min-h-screen flex flex-col">

        <x-topbar />

        <main class="flex-1 p-4 sm:p-6">

            {{-- Flash success --}}
            @if (session('success'))
                <div class="mb-4 flex items-center gap-2 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm">
                    <i class="bi bi-check-circle-fill shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            {{-- Flash error --}}
            @if (session('error'))
                <div class="mb-4 flex items-center gap-2 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
                    <i class="bi bi-exclamation-circle-fill shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')

        </main>

    </div>

    {{-- SweetAlert dari session --}}
    @if (session('swal'))
        @php $swal = session('swal'); @endphp
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: '{{ $swal['icon'] ?? 'info' }}',
                    title: '{{ $swal['title'] ?? '' }}',
                    text: '{{ $swal['text'] ?? '' }}',
                    confirmButtonColor: '#10b981',
                    timer: {{ $swal['timer'] ?? 0 }},
                    timerProgressBar: {{ isset($swal['timer']) ? 'true' : 'false' }},
                });
            });
        </script>
    @endif

    @stack('scripts')
</body>

</html>