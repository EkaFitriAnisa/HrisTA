@props(['active' => ''])

@php

    $role = auth()->user()->role ?? '';

    $menus = [
        'HRD' => [
            'portal' => 'HRD Portal',

            'items' => [
                [
                    'route' => 'hrd.dashboard.index',
                    'key' => 'dashboard',
                    'icon' => 'bi-speedometer2',
                    'label' => 'Dashboard',
                ],

                [
                    'route' => 'hrd.clearance.index',
                    'key' => 'clearance',
                    'icon' => 'bi-clipboard2-check',
                    'label' => 'Clearance',
                ],

                [
                    'route' => 'hrd.riwayat.index',
                    'key' => 'riwayat',
                    'icon' => 'bi-clock-history',
                    'label' => 'Riwayat',
                ],
            ],
        ],

        'HOD' => [
            'portal' => 'HOD Portal',

            'items' => [
                [
                    'route' => 'hod.dashboard.index',
                    'key' => 'dashboard',
                    'icon' => 'bi-speedometer2',
                    'label' => 'Dashboard',
                ],

                [
                    'route' => 'hod.clearance.index',
                    'key' => 'clearance',
                    'icon' => 'bi-clipboard2-check',
                    'label' => 'Clearance',
                ],

                [
                    'route' => 'hod.riwayat.index',
                    'key' => 'riwayat',
                    'icon' => 'bi-clock-history',
                    'label' => 'Riwayat',
                ],
            ],
        ],

        'MIS' => [
            'portal' => 'MIS Portal',

            'items' => [
                [
                    'route' => 'mis.dashboard.index',
                    'key' => 'dashboard',
                    'icon' => 'bi-speedometer2',
                    'label' => 'Dashboard',
                ],

                [
                    'route' => 'mis.clearance.index',
                    'key' => 'clearance',
                    'icon' => 'bi-clipboard2-check',
                    'label' => 'Clearance',
                ],

                [
                    'route' => 'mis.riwayat.index',
                    'key' => 'riwayat',
                    'icon' => 'bi-clock-history',
                    'label' => 'Riwayat',
                ],
            ],
        ],

        'Karyawan' => [
            'portal' => 'Employee Portal',

            'items' => [
                [
                    'route' => 'karyawan.dashboard',
                    'key' => 'dashboard',
                    'icon' => 'bi-speedometer2',
                    'label' => 'Dashboard',
                ],

                [
                    'route' => 'karyawan.clearance.index',
                    'key' => 'clearance',
                    'icon' => 'bi-clipboard2-check',
                    'label' => 'Ajukan Clearance',
                ],
            ],
        ],
    ];

    $menu = $menus[$role] ?? [
        'portal' => 'Portal',
        'items' => [],
    ];

@endphp

<style>
    #sidebar {
        transition: width .25s;
    }

    #sidebar.is-collapsed .sidebar-label,
    #sidebar.is-collapsed .sidebar-logo-text {
        display: none;
    }

    #sidebar.is-collapsed .sidebar-logo,
    #sidebar.is-collapsed .nav-item {
        justify-content: center;
    }

    #sidebar.is-collapsed .nav-item {
        position: relative;
        padding-left: .75rem;
        padding-right: .75rem;
    }

    #sidebar.is-collapsed .nav-item:hover::after {
        content: attr(data-label);
        position: absolute;
        left: calc(100% + 10px);
        top: 50%;
        transform: translateY(-50%);
        background: #1f2937;
        color: white;
        font-size: 12px;
        font-weight: 500;
        padding: 4px 10px;
        border-radius: 8px;
        white-space: nowrap;
        z-index: 999;
    }

    #sidebar.is-collapsed .nav-item:hover::before {
        content: '';
        position: absolute;
        left: calc(100% + 4px);
        top: 50%;
        transform: translateY(-50%);
        border: 5px solid transparent;
        border-right-color: #1f2937;
        z-index: 999;
    }
</style>

<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col border-r border-gray-200 bg-white lg:translate-x-0">

    {{-- Logo --}}
    <div class="sidebar-logo flex h-16 shrink-0 items-center gap-3 border-b border-gray-200 px-5">

        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-600">

            <i class="bi bi-grid-fill text-sm text-white"></i>

        </div>

        <div class="sidebar-logo-text min-w-0">

            <p class="truncate text-xs font-bold uppercase tracking-wide text-blue-600">

                {{ $menu['portal'] }}

            </p>

        </div>

    </div>

    {{-- Navigation --}}
    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">

        @foreach ($menu['items'] as $item)
            @php

                $isActive = $active === $item['key'];

                $classes = $isActive ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100';

            @endphp

            <a href="{{ route($item['route']) }}" data-label="{{ $item['label'] }}"
                class="nav-item flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ $classes }}">

                <i class="bi {{ $item['icon'] }} w-5 shrink-0 text-center text-base"></i>

                <span class="sidebar-label">

                    {{ $item['label'] }}

                </span>

            </a>
        @endforeach

    </nav>

</aside>

{{-- Overlay --}}
<div id="sidebar-overlay" onclick="closeSidebar()" class="fixed inset-0 z-40 hidden bg-black/40 lg:hidden"></div>