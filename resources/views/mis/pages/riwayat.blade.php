{{-- resources/views/hod/pages/riwayat.blade.php --}}
@extends('mis.layouts.app')

@section('title', 'Riwayat Clearance')

@php
    $activePage = 'riwayat';

    $jenisLabel = [
        'resign' => 'Resign',
        'mutasi_internal' => 'Mutasi Internal',
    ];

    $approvalFlow = ['HOD', 'MIS', 'HRD'];
@endphp

@section('content')
    @include('clearance.partials.flash')

    <div class="space-y-6">
        {{-- Header --}}
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Riwayat Clearance</h2>
            <p class="text-sm text-gray-500 mt-0.5">
                Clearance yang telah selesai diproses &mdash; departemen
                <span class="font-medium text-gray-700">
                    {{ auth()->user()->karyawan?->depart?->nama ?? '-' }}
                </span>
            </p>
        </div>

        <div class="grid grid-cols-2 gap-4 xl:grid-cols-4">

            {{-- Total Selesai --}}
            <div class="relative overflow-hidden rounded-2xl border border-gray-100 bg-white px-5 py-5 shadow-sm">

                <div class="absolute inset-x-0 top-0 h-0.5 bg-gradient-to-r from-blue-500 to-blue-400 rounded-t-2xl"></div>

                <div class="flex items-center justify-between gap-3">

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">

                            Total Selesai

                        </p>

                        <p class="mt-2 text-4xl font-black text-gray-800">

                            {{ $totalApproved }}

                        </p>

                        <p class="mt-1.5 text-sm text-gray-500">

                            Clearance selesai

                        </p>

                    </div>

                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-blue-100">

                        <i class="bi bi-clipboard2-check text-2xl text-blue-600"></i>

                    </div>

                </div>

            </div>

            {{-- Resign --}}
            <div class="relative overflow-hidden rounded-2xl border border-gray-100 bg-white px-5 py-5 shadow-sm">

                <div class="absolute inset-x-0 top-0 h-0.5 bg-gradient-to-r from-rose-500 to-rose-400 rounded-t-2xl"></div>

                <div class="flex items-center justify-between gap-3">

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">

                            Resign

                        </p>

                        <p class="mt-2 text-4xl font-black text-gray-800">

                            {{ $totalResign }}

                        </p>

                        <p class="mt-1.5 text-sm text-gray-500">

                            Karyawan resign

                        </p>

                    </div>

                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-rose-100">

                        <i class="bi bi-box-arrow-right text-2xl text-rose-600"></i>

                    </div>

                </div>

            </div>

            {{-- Mutasi --}}
            <div class="relative overflow-hidden rounded-2xl border border-gray-100 bg-white px-5 py-5 shadow-sm">

                <div class="absolute inset-x-0 top-0 h-0.5 bg-gradient-to-r from-violet-500 to-violet-400 rounded-t-2xl">
                </div>

                <div class="flex items-center justify-between gap-3">

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">

                            Mutasi Internal

                        </p>

                        <p class="mt-2 text-4xl font-black text-gray-800">

                            {{ $totalMutasi }}

                        </p>

                        <p class="mt-1.5 text-sm text-gray-500">

                            Perpindahan depart

                        </p>

                    </div>

                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-violet-100">

                        <i class="bi bi-arrow-left-right text-2xl text-violet-600"></i>

                    </div>

                </div>

            </div>

            {{-- Bulan Ini --}}
            <div class="relative overflow-hidden rounded-2xl border border-gray-100 bg-white px-5 py-5 shadow-sm">

                <div class="absolute inset-x-0 top-0 h-0.5 bg-gradient-to-r from-emerald-500 to-emerald-400 rounded-t-2xl">
                </div>

                <div class="flex items-center justify-between gap-3">

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">

                            Bulan Ini

                        </p>

                        <p class="mt-2 text-4xl font-black text-gray-800">

                            {{ $approvedThisMonth }}

                        </p>

                        <p class="mt-1.5 text-sm text-gray-500">

                            Persetujuan bulan ini

                        </p>

                    </div>

                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-emerald-100">

                        <i class="bi bi-calendar2-check text-2xl text-emerald-600"></i>

                    </div>

                </div>

            </div>

        </div>

        {{-- Table Card --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            {{-- Filter Bar --}}
            <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50/50">
                <form method="GET" action="{{ route('mis.riwayat.index') }}" class="flex flex-wrap gap-2 items-center">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">

                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama karyawan..."
                            class="pl-2 pr-10 h-9 w-64 text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-400">

                        <button type="submit"
                            class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-md flex items-center justify-center text-white bg-blue-600 hover:bg-blue-700">
                            <i class="bi bi-search text-sm"></i>
                        </button>
                    </div>

                    <select name="jenis" onchange="this.form.submit()"
                        class="h-9 px-3 text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">Semua Jenis</option>
                        @foreach (['resign' => 'Resign', 'mutasi_internal' => 'Mutasi Internal'] as $val => $lbl)
                            <option value="{{ $val }}" @selected(request('jenis') === $val)>{{ $lbl }}</option>
                        @endforeach
                    </select>

                    <select name="bulan" onchange="this.form.submit()"
                        class="h-9 px-3 text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">Semua Bulan</option>
                        @foreach (range(1, 12) as $m)
                            <option value="{{ $m }}" @selected((string) request('bulan') === (string) $m)>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>

                    <select name="tahun" onchange="this.form.submit()"
                        class="h-9 px-3 text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">Semua Tahun</option>
                        @foreach (range(now()->year, now()->year - 4) as $y)
                            <option value="{{ $y }}" @selected((string) request('tahun') === (string) $y)>{{ $y }}</option>
                        @endforeach
                    </select>

                    @if (request('search') || request('jenis') || request('bulan') || request('tahun'))
                        <a href="{{ route('mis.riwayat.index') }}"
                            class="h-9 px-3 inline-flex items-center gap-1.5 border border-red-200 bg-red-50 hover:bg-red-100 text-red-500 text-sm rounded-lg transition-colors">
                            <i class="bi bi-arrow-counterclockwise text-xs"></i> Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto min-h-[360px]">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            @foreach (['No', 'Karyawan', 'Aset', 'Jenis Clearance', 'Tanggal Pengajuan', 'Tanggal Disetujui', 'Status'] as $th)
                                <th
                                    class="text-left px-5 py-3 text-[13px] font-semibold text-gray-400 uppercase tracking-wide">
                                    {{ $th }}
                                </th>
                            @endforeach
                            <th
                                class="text-right px-5 py-3 text-[13px] font-semibold text-gray-400 uppercase tracking-wide">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-50">
                        @forelse ($clearances as $cl)
                            @php
                                $approvalSteps = $cl->clearanceAset
                                    ->pluck('aset.kategori.managed_role')
                                    ->filter()
                                    ->unique()
                                    ->values()
                                    ->all();

                                $approvals = $cl->approvals->keyBy('step_order');
                            @endphp

                            <tr class="hover:bg-gray-50/50 transition-colors align-top text-md">
                                {{-- No --}}
                                <td class="px-5 py-4 text-sm text-gray-500 font-medium whitespace-nowrap">
                                    {{ $clearances->firstItem() + $loop->index }}
                                </td>

                                {{-- Karyawan --}}
                                <td class="px-5 py-4 min-w-[180px]">
                                    <p class="font-semibold text-gray-800">{{ $cl->karyawan?->nama ?? '-' }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $cl->karyawan?->user?->badge_id ?? '-' }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $cl->karyawan?->jabatan ?? '-' }}</p>
                                    <p class="text-xs text-gray-400">{{ $cl->karyawan?->depart?->nama ?? '-' }}</p>
                                </td>

                                {{-- Aset --}}
                                <td class="px-5 py-4 min-w-[200px]">
                                    <div class="space-y-1.5">
                                        @foreach ($cl->clearanceAset->take(3) as $ca)
                                            <div class="flex items-center gap-2">
                                                <span class="w-1.5 h-1.5 rounded-full shrink-0 bg-emerald-500"></span>
                                                <span class="text-xs text-gray-700 truncate max-w-[140px]">
                                                    {{ $ca->aset?->nama ?? '-' }}
                                                </span>
                                                <span class="shrink-0 text-[10px] text-gray-400">
                                                    ({{ $ca->aset?->kategori?->managed_role ?? '-' }})
                                                </span>
                                            </div>
                                        @endforeach

                                        @if ($cl->clearanceAset->count() > 3)
                                            <p class="text-[11px] text-gray-400 pl-3.5">
                                                +{{ $cl->clearanceAset->count() - 3 }} lainnya
                                            </p>
                                        @endif
                                    </div>
                                </td>

                                {{-- Jenis Clearance --}}
                                <td class="px-5 py-4 whitespace-nowrap">
                                    {{ $jenisLabel[$cl->jenis] ?? $cl->jenis }}
                                </td>

                                {{-- Tanggal Pengajuan --}}
                                <td class="px-5 py-4 whitespace-nowrap">
                                    {{ $cl->created_at->format('d M Y') }}
                                </td>

                                {{-- Tanggal Selesai --}}
                                <td class="px-5 py-4 whitespace-nowrap">
                                    {{ $cl->updated_at->format('d M Y') }}
                                </td>

                                {{-- Status --}}
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="bi bi-check-circle-fill text-[10px]"></i> Selesai
                                    </span>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-5 py-4 text-right">
                                    <button onclick="openDetailModal({{ $cl->id }})"
                                        class="h-8 px-3 rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-500 text-xs font-medium flex items-center gap-1.5 transition-colors ml-auto">
                                        <i class="bi bi-eye"></i> Detail
                                    </button>
                                </td>
                            </tr>

                            {{-- Detail Modal per row --}}
                            @include('clearance.partials.detail-modal', [
                                'clearance' => $cl,
                                'currentRole' => auth()->user()->role,
                                'showAction' => false,
                            ])
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-14 text-center">
                                    <i class="bi bi-inbox text-5xl text-gray-200 block mb-3"></i>
                                    <p class="text-sm text-gray-400">Belum ada riwayat clearance yang selesai.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($clearances->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between gap-4 flex-wrap">
                    <p class="text-sm text-gray-500">
                        Menampilkan {{ $clearances->firstItem() }}–{{ $clearances->lastItem() }} dari
                        {{ $clearances->total() }} riwayat
                    </p>

                    <div class="flex items-center gap-2">
                        <select onchange="window.location.href=this.value"
                            class="h-8 px-2 text-xs border border-gray-200 rounded-lg bg-white focus:outline-none">
                            @foreach ([5, 10, 25, 50] as $n)
                                <option value="{{ request()->fullUrlWithQuery(['per_page' => $n]) }}"
                                    @selected($perPage == $n)>
                                    {{ $n }} / hal
                                </option>
                            @endforeach
                        </select>

                        {{ $clearances->onEachSide(1)->links('pagination::tailwind') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Detail Modal Global --}}
    <div id="detail-modal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeDetailModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none">
            <div
                class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[88vh] flex flex-col pointer-events-auto">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
                    <h3 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                        <i class="bi bi-clipboard-data text-emerald-500"></i> Detail Clearance
                    </h3>
                    <button onclick="closeDetailModal()"
                        class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                        <i class="bi bi-x-lg text-base"></i>
                    </button>
                </div>
                <div id="detail-content" class="flex-1 overflow-y-auto px-6 py-5"></div>
            </div>
        </div>
    </div>
@endsection

@include('clearance.partials.preview-modal')

@push('scripts')
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });

        @if (session('success'))
            Toast.fire({
                icon: 'success',
                title: @json(session('success'))
            });
        @endif

        @if (session('error'))
            Toast.fire({
                icon: 'error',
                title: @json(session('error'))
            });
        @endif

        function openDetailModal(id) {
            const tpl = document.getElementById('detail-template-' + id);
            if (!tpl) return;

            document.getElementById('detail-content').innerHTML = tpl.innerHTML;
            document.getElementById('detail-modal').classList.remove('hidden');
            document.getElementById('detail-modal').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeDetailModal() {
            document.getElementById('detail-modal').classList.add('hidden');
            document.getElementById('detail-modal').classList.remove('flex');
            document.getElementById('detail-content').innerHTML = '';
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeDetailModal();
        });
    </script>
@endpush
