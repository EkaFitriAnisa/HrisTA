@php

    $route = Route::currentRouteName();

    $pageMap = [
        'dashboard' => [
            'title' => 'Dashboard',
            'subtitle' => 'Overview data perusahaan',
        ],

        'clearance.index' => [
            'title' => 'Clearance',
            'subtitle' => 'Approval clearance karyawan',
        ],

        'riwayat.index' => [
            'title' => 'Riwayat',
            'subtitle' => 'Riwayat clearance yang telah selesai',
        ],

        'karyawan.clearance.index' => [
            'title' => 'Ajukan Clearance',
            'subtitle' => 'Pengajuan pengembalian aset',
        ],
    ];

    $page = collect($pageMap)->first(fn($value, $key) => str_contains($route, $key)) ?? [
        'title' => 'Portal',
        'subtitle' => '',
    ];

@endphp

<header class="sticky top-0 z-30 flex h-16 items-center gap-4 border-b border-gray-200 bg-white px-4 sm:px-6">

    {{-- Sidebar Toggle --}}
    <button type="button" onclick="toggleSidebar()" class="shrink-0 text-gray-500 transition hover:text-gray-700">

        <i class="bi bi-list text-2xl"></i>

    </button>

    {{-- Page Info --}}
    <div class="min-w-0">

        <h1 class="text-base font-bold leading-none text-gray-800">

            {{ $page['title'] }}

        </h1>

        @if ($page['subtitle'])
            <p class="mt-0.5 hidden truncate text-xs text-gray-400 sm:block">

                {{ $page['subtitle'] }}

            </p>
        @endif

    </div>

    {{-- Profile --}}
    <div class="relative ml-auto shrink-0">

        <button type="button" onclick="toggleProfileMenu()"
            class="flex items-center gap-2 rounded-xl border border-gray-200 px-2 py-1.5 transition hover:bg-gray-100">

            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->karyawan->nama ?? 'User') }}&background=2563eb&color=fff&rounded=true"
                alt="Profile" class="h-9 w-9 rounded-xl object-cover ring-1 ring-gray-200">

            <div class="hidden text-left sm:block">

                <p class="truncate text-sm font-semibold leading-none text-gray-700">

                    {{ auth()->user()->karyawan->nama ?? 'User' }}

                </p>

                <p class="mt-1 truncate text-xs leading-none text-gray-400">

                    {{ auth()->user()->role ?? 'Role' }}

                </p>

            </div>

            <i class="bi bi-chevron-down text-xs text-gray-400"></i>

        </button>

        {{-- Dropdown --}}
        <div id="profile-menu"
            class="absolute right-0 top-14 z-50 hidden w-56 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg">

            <div class="border-b border-gray-100 px-4 py-3">

                <p class="truncate text-sm font-semibold text-gray-800">

                    {{ auth()->user()->karyawan->nama ?? 'User' }}

                </p>

                <p class="mt-0.5 text-xs text-gray-400">

                    {{ auth()->user()->role ?? 'Role' }}

                </p>

            </div>

            <form method="POST" action="{{ route('logout') }}">

                @csrf

                <button type="submit"
                    class="flex w-full items-center gap-3 px-4 py-3 text-sm text-red-500 transition hover:bg-red-50">

                    <i class="bi bi-box-arrow-right text-base"></i>

                    Logout

                </button>

            </form>

        </div>

    </div>

</header>

<script>
    const SIDEBAR_FULL = '256px';
    const SIDEBAR_MINI = '72px';

    function toggleProfileMenu() {
        document
            .getElementById('profile-menu')
            ?.classList.toggle('hidden');
    }

    function applySidebarState(collapsed) {
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('main-content');

        if (!sidebar || !content) return;

        sidebar.classList.toggle(
            'is-collapsed',
            collapsed
        );

        sidebar.style.width =
            collapsed ?
            SIDEBAR_MINI :
            SIDEBAR_FULL;

        content.style.marginLeft =
            collapsed ?
            SIDEBAR_MINI :
            SIDEBAR_FULL;

        localStorage.setItem(
            'sidebar-collapsed',
            collapsed ? '1' : '0'
        );
    }

    function openSidebar() {
        document
            .getElementById('sidebar')
            ?.classList.replace(
                '-translate-x-full',
                'translate-x-0'
            );

        document
            .getElementById('sidebar-overlay')
            ?.classList.remove('hidden');
    }

    function closeSidebar() {
        document
            .getElementById('sidebar')
            ?.classList.replace(
                'translate-x-0',
                '-translate-x-full'
            );

        document
            .getElementById('sidebar-overlay')
            ?.classList.add('hidden');
    }

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');

        if (!sidebar) return;

        if (window.innerWidth >= 1024) {

            applySidebarState(
                !sidebar.classList.contains('is-collapsed')
            );

        } else {

            sidebar.classList.contains('-translate-x-full') ?
                openSidebar() :
                closeSidebar();
        }
    }

    function initSidebar() {
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('main-content');

        if (!sidebar || !content) return;

        if (window.innerWidth >= 1024) {

            sidebar.style.transition = 'width .25s';
            content.style.transition = 'margin-left .25s';

            applySidebarState(
                localStorage.getItem('sidebar-collapsed') === '1'
            );

        } else {

            sidebar.style.width = '';
            content.style.marginLeft = '';

            closeSidebar();
        }
    }

    document.addEventListener('DOMContentLoaded', initSidebar);

    window.addEventListener('resize', initSidebar);

    document.addEventListener('click', function(e) {

        const menu = document.getElementById('profile-menu');

        if (
            menu &&
            !menu.contains(e.target) &&
            !e.target.closest('[onclick="toggleProfileMenu()"]')
        ) {
            menu.classList.add('hidden');
        }
    });

    document.addEventListener('keydown', function(e) {

        if (e.key !== 'Escape') return;

        closeSidebar();

        document
            .getElementById('profile-menu')
            ?.classList.add('hidden');
    });
</script>