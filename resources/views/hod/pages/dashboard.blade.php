@extends('hod.layouts.app')

@section('title', 'Dashboard')

@php
    $activePage = 'dashboard';

    $statusMeta = [
        'pending' => [
            'label' => 'Menunggu',
            'icon' => 'bi-clock-history',
            'pill' => 'bg-slate-100 text-slate-600 border border-slate-200',
        ],
        'process' => [
            'label' => 'Diproses',
            'icon' => 'bi-arrow-repeat',
            'pill' => 'bg-blue-50 text-blue-700 border border-blue-200',
        ],
        'revision' => [
            'label' => 'Revisi',
            'icon' => 'bi-exclamation-circle-fill',
            'pill' => 'bg-amber-50 text-amber-700 border border-amber-200',
        ],
        'approved' => [
            'label' => 'Selesai',
            'icon' => 'bi-check-circle-fill',
            'pill' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
        ],
        'rejected' => [
            'label' => 'Ditolak',
            'icon' => 'bi-x-circle-fill',
            'pill' => 'bg-red-50 text-red-600 border border-red-200',
        ],
    ];
@endphp

@section('content')
    <div class="space-y-5">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-700 via-blue-600 to-indigo-600 px-6 py-7 shadow-lg">
            <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                {{-- Greeting --}}
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-blue-200">
                        {{ now()->translatedFormat('l, d F Y') }}
                    </p>
                    <h1 class="mt-1.5 text-2xl font-bold leading-tight text-white sm:text-3xl">
                        Selamat datang, {{ auth()->user()->karyawan?->nama ?? auth()->user()->name }}👋
                    </h1>
                    <div class="mt-2.5 flex flex-wrap items-center gap-x-3 gap-y-1 text-lg text-blue-300">
                        <span class="flex items-center gap-1.5">
                            <i class="bi bi-building"></i>
                            {{ auth()->user()->karyawan?->depart?->nama ?? '-' }}
                        </span>
                        <span class="hidden h-3.5 w-px bg-blue-500 sm:block"></span>
                        <span class="flex items-center gap-1.5">
                            <i class="bi bi-person-badge"></i>
                            {{ auth()->user()->role }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 xl:grid-cols-4">

            {{-- Total Clearance --}}
            <div class="relative overflow-hidden rounded-2xl border border-gray-100 bg-white px-5 py-5 shadow-sm">
                <div class="absolute inset-x-0 top-0 h-0.5 bg-gradient-to-r from-violet-500 to-violet-400 rounded-t-2xl">
                </div>
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total</p>
                        <p class="mt-2 text-4xl font-black text-gray-800" data-count="{{ $totalClearance }}">
                            {{ $totalClearance }}</p>
                        <p class="mt-1.5 text-sm text-gray-500">Semua clearance</p>
                    </div>
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-violet-100">
                        <i class="bi bi-folder2-open text-2xl text-violet-600"></i>
                    </div>
                </div>
            </div>

            {{-- Menunggu --}}
            <div class="relative overflow-hidden rounded-2xl border border-gray-100 bg-white px-5 py-5 shadow-sm">
                <div class="absolute inset-x-0 top-0 h-0.5 bg-gradient-to-r from-slate-400 to-slate-300 rounded-t-2xl">
                </div>
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Menunggu</p>
                        <p class="mt-2 text-4xl font-black text-gray-800" data-count="{{ $pendingClearance }}">
                            {{ $pendingClearance }}</p>
                        <p class="mt-1.5 text-sm text-gray-500">Belum diproses</p>
                    </div>
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-slate-100">
                        <i class="bi bi-clock-history text-2xl text-slate-500"></i>
                    </div>
                </div>
            </div>

            {{-- Diproses --}}
            <div class="relative overflow-hidden rounded-2xl border border-gray-100 bg-white px-5 py-5 shadow-sm">
                <div class="absolute inset-x-0 top-0 h-0.5 bg-gradient-to-r from-blue-500 to-blue-400 rounded-t-2xl"></div>
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Diproses</p>
                        <p class="mt-2 text-4xl font-black text-gray-800" data-count="{{ $processClearance }}">
                            {{ $processClearance }}</p>
                        <p class="mt-1.5 text-sm text-gray-500">Sedang berjalan</p>
                    </div>
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-blue-100">
                        <i class="bi bi-arrow-repeat text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            {{-- Revisi --}}
            <div class="relative overflow-hidden rounded-2xl border border-gray-100 bg-white px-5 py-5 shadow-sm">
                <div class="absolute inset-x-0 top-0 h-0.5 bg-gradient-to-r from-amber-500 to-amber-400 rounded-t-2xl">
                </div>
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Revisi</p>
                        <p class="mt-2 text-4xl font-black text-gray-800" data-count="{{ $revisionClearance }}">
                            {{ $revisionClearance }}</p>
                        <p class="mt-1.5 text-sm text-gray-500">Perlu perbaikan</p>
                    </div>
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-amber-100">
                        <i class="bi bi-exclamation-circle-fill text-2xl text-amber-500"></i>
                    </div>
                </div>
            </div>

        </div>
        
        <div class="rounded-2xl border border-gray-100 bg-white shadow-sm">

            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50">
                        <i class="bi bi-activity text-base text-blue-500"></i>
                    </div>
                    <div>
                        <p class="text-base font-semibold leading-tight text-gray-800">Aktivitas Terbaru</p>
                        <p class="text-xs text-gray-400">Perubahan status clearance departemen</p>
                    </div>
                </div>
                <a href="{{ route('hod.clearance.index') }}"
                    class="flex items-center gap-1.5 text-sm font-medium text-blue-500 transition-colors hover:text-blue-700">
                    Lihat semua <i class="bi bi-arrow-right text-xs"></i>
                </a>
            </div>

            {{-- List --}}
            <div class="divide-y divide-gray-50">
                @forelse ($activities as $activity)
                    @php $meta = $statusMeta[$activity->status] ?? $statusMeta['pending']; @endphp

                    <div class="flex items-start gap-4 px-6 py-4">

                        {{-- Status icon --}}
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $meta['pill'] }}">
                            <i class="bi {{ $meta['icon'] }} text-sm"></i>
                        </div>

                        {{-- Text --}}
                        <div class="min-w-0 flex-1 pt-0.5">
                            <p class="text-sm leading-snug text-gray-700">
                                Clearance
                                <span
                                    class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold {{ $meta['pill'] }}">
                                    {{ $meta['label'] }}
                                </span>
                                untuk
                                <span class="font-semibold text-gray-900">{{ $activity->karyawan?->nama }}</span>
                            </p>
                            <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1">
                                <span class="flex items-center gap-1.5 text-xs text-gray-400">
                                    <i class="bi bi-building text-[11px]"></i>
                                    {{ $activity->karyawan?->depart?->nama ?? '-' }}
                                </span>
                                <span class="flex items-center gap-1.5 text-xs text-gray-400">
                                    <i class="bi bi-briefcase text-[11px]"></i>
                                    {{ $activity->karyawan?->jabatan ?? '-' }}
                                </span>
                            </div>
                        </div>

                        {{-- Timestamp --}}
                        <span class="shrink-0 whitespace-nowrap text-xs text-gray-400">
                            {{ $activity->updated_at->diffForHumans(null, true) }} lalu
                        </span>

                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center px-6 py-20 text-center">
                        <div
                            class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl border border-gray-100 bg-gray-50">
                            <i class="bi bi-inbox text-3xl text-gray-300"></i>
                        </div>
                        <p class="text-base font-semibold text-gray-500">Belum ada aktivitas</p>
                        <p class="mt-1 text-sm text-gray-400">Aktivitas clearance akan muncul di sini</p>
                    </div>
                @endforelse
            </div>

        </div>

    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-count]').forEach(el => {
                const target = parseInt(el.dataset.count, 10);
                if (!target) return;
                let frame = 0;
                const total = 45;
                const easeOut = t => 1 - Math.pow(1 - t, 3);
                const tick = () => {
                    frame++;
                    el.textContent = Math.round(easeOut(frame / total) * target);
                    if (frame < total) requestAnimationFrame(tick);
                };
                requestAnimationFrame(tick);
            });
        });
    </script>
@endpush
