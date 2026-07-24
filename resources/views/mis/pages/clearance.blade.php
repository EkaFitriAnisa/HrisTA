@extends('mis.layouts.app')

@section('title', 'Clearance')

@php
    $activePage = 'clearance';
    $currentRole = auth()->user()->role ?? '';

    $jenisLabel = [
        'resign' => 'Resign',
        'mutasi_internal' => 'Mutasi Internal',
    ];
@endphp

@section('content')
    @include('clearance.partials.flash')

    <div class="space-y-6">
        {{-- Header --}}
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Manajemen Clearance</h2>
            <p class="text-sm text-gray-500 mt-0.5">
                Pantau dan proses clearance aset &mdash; departemen
                <span class="font-medium text-gray-700">{{ auth()->user()->karyawan?->depart?->nama ?? '-' }}</span>
            </p>
        </div>

        {{-- Stat Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ([
            [
                'label' => 'Total',
                'value' => $totalClearance,
                'icon' => 'bi-folder2-open',
                'color' => 'bg-violet-50 border-violet-200 text-violet-700',
                'desc' => 'Semua clearance',
                'line' => 'from-violet-500 to-violet-400',
            ],
            [
                'label' => 'Menunggu',
                'value' => $pendingClearance,
                'icon' => 'bi-clock-history',
                'color' => 'bg-gray-100 border-gray-200 text-gray-600',
                'desc' => 'Menunggu persetujuan',
                'line' => 'from-gray-500 to-gray-400',
            ],
            [
                'label' => 'Diproses',
                'value' => $processClearance,
                'icon' => 'bi-arrow-repeat',
                'color' => 'bg-blue-50 border-blue-200 text-blue-700',
                'desc' => 'Sedang diproses',
                'line' => 'from-blue-500 to-blue-400',
            ],
            [
                'label' => 'Revisi',
                'value' => $revisiClearance,
                'icon' => 'bi-exclamation-circle-fill',
                'color' => 'bg-yellow-50 border-yellow-200 text-yellow-700',
                'desc' => 'Perlu revisi',
                'line' => 'from-yellow-500 to-yellow-400',
            ],
        ] as $card)
                <div class="relative overflow-hidden rounded-2xl border border-gray-100 bg-white px-5 py-5 shadow-sm">
                    <div class="absolute inset-x-0 top-0 h-0.5 bg-gradient-to-r {{ $card['line'] }} rounded-t-2xl"></div>
                    <div class="flex items-center justify-between gap-3">

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                                {{ $card['label'] }}
                            </p>

                            <p class="mt-2 text-4xl font-black text-gray-800">
                                {{ $card['value'] }}
                            </p>

                            <p class="mt-1.5 text-sm text-gray-500">
                                {{ $card['desc'] }}
                            </p>
                        </div>

                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl {{ $card['color'] }}">
                            <i class="bi {{ $card['icon'] }} text-2xl"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Table Card --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            {{-- Filter Bar --}}
            <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50/50">
                <form method="GET" action="{{ route('mis.clearance.index') }}" class="flex flex-wrap gap-2 items-center">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">

                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama karyawan..."
                            class="pl-2 pr-10 h-9 w-64 text-sm border border-gray-200 rounded-lg bg-white
                            focus:outline-none focus:ring-2 focus:ring-blue-400">

                        {{-- Submit icon kanan --}}
                        <button type="submit"
                            class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-md
                            flex items-center justify-center text-white bg-blue-600 hover:bg-blue-700">
                            <i class="bi bi-search text-sm"></i>
                        </button>

                    </div>

                    <select name="status" onchange="this.form.submit()"
                        class="h-9 px-3 text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">Semua Status</option>
                        @foreach (['pending' => 'Pending', 'process' => 'Diproses', 'revision' => 'Revisi', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $val => $lbl)
                            <option value="{{ $val }}" @selected(request('status') === $val)>{{ $lbl }}</option>
                        @endforeach
                    </select>

                    @if (request('search') || request('status'))
                        <a href="{{ route('mis.clearance.index') }}"
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
                            @foreach (['No', 'Karyawan', 'Aset', 'Jenis Clearance', 'Progres', 'Tanggal Pengajuan', 'Status'] as $th)
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

                                $myPendingAssets = $cl->clearanceAset->filter(
                                    fn($ca) => $ca->aset?->kategori?->managed_role === $currentRole &&
                                        $ca->status_pengembalian === 'pending',
                                );
                            @endphp

                            <tr class="hover:bg-gray-50/50 transition-colors align-top">
                                <td class="px-5 py-4 text-sm text-gray-500 font-medium whitespace-nowrap">
                                    {{ $loop->iteration }}
                                </td>
                                {{-- Karyawan --}}
                                <td class="px-5 py-4 min-w-[180px]">
                                    <p class="font-semibold text-gray-800">{{ $cl->karyawan?->nama ?? '-' }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $cl->karyawan->user?->badge_id ?? '-' }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $cl->karyawan?->jabatan ?? '-' }}</p>
                                    <p class="text-xs text-gray-400">{{ $cl->karyawan?->depart?->nama ?? '-' }}</p>
                                </td>

                                {{-- Aset --}}
                                <td class="px-5 py-4 min-w-[200px]">
                                    <div class="space-y-1.5">
                                        @foreach ($cl->clearanceAset->take(3) as $ca)
                                            @php
                                                $dot = match ($ca->status_pengembalian) {
                                                    'returned' => 'bg-emerald-500',
                                                    'missing' => 'bg-red-400',
                                                    default => 'bg-amber-400',
                                                };
                                            @endphp
                                            <div class="flex items-center gap-2">
                                                <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $dot }}"></span>
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

                                {{-- Progress --}}
                                <td class="px-5 py-4 min-w-[170px]">
                                    <div class="flex items-center gap-1">
                                        @foreach ($approvalSteps as $index => $role)
                                            @php
                                                $ap = $approvals->get($index + 1);
                                                $st = $ap?->status ?? 'pending';

                                                [$stepBg, $stepIcon] = match ($st) {
                                                    'approved' => [
                                                        'bg-emerald-100 border-emerald-300',
                                                        '<i class="bi bi-check text-emerald-600 text-[11px]"></i>',
                                                    ],
                                                    'revision' => [
                                                        'bg-yellow-100 border-yellow-300',
                                                        '<i class="bi bi-exclamation text-yellow-700 text-[11px]"></i>',
                                                    ],
                                                    'rejected' => [
                                                        'bg-red-100 border-red-300',
                                                        '<i class="bi bi-x text-red-600 text-[11px]"></i>',
                                                    ],
                                                    default => [
                                                        'bg-gray-100 border-gray-200',
                                                        '<span class="text-[9px] font-bold text-gray-400">' .
                                                        strtoupper(substr($role, 0, 1)) .
                                                        '</span>',
                                                    ],
                                                };

                                                $stepTitle = match ($st) {
                                                    'approved' => "$role: Disetujui",
                                                    'revision' => "$role: Revisi",
                                                    'rejected' => "$role: Ditolak",
                                                    default => "$role: Menunggu",
                                                };
                                            @endphp

                                            <div class="flex items-center gap-1">
                                                <div class="w-7 h-7 rounded-full border-2 flex items-center justify-center {{ $stepBg }}"
                                                    title="{{ $stepTitle }}">
                                                    {!! $stepIcon !!}
                                                </div>

                                                @if ($index < count($approvalSteps) - 1)
                                                    <div
                                                        class="w-4 h-px {{ $st === 'approved' ? 'bg-emerald-300' : 'bg-gray-200' }}">
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>

                                    <p class="text-[10px] text-gray-400 mt-1.5 tracking-wide">
                                        {{ count($approvalSteps) ? implode(' · ', $approvalSteps) : '-' }}
                                    </p>
                                </td>

                                {{-- TGL Pengajuan --}}
                                <td class="px-5 py-4">
                                    {{ $cl->created_at->format('d M Y') }}
                                </td>

                                {{-- Status --}}
                                <td class="px-5 py-4">
                                    @include('clearance.partials.status-badge', ['status' => $cl->status])

                                    @if ($myPendingAssets->count() > 0)
                                        <p class="text-[11px] text-emerald-600 mt-2 flex items-center gap-1">
                                            <i class="bi bi-lightning-charge-fill text-[10px]"></i>
                                            {{ $myPendingAssets->count() }} item perlu diproses
                                        </p>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-5 py-4 text-right">
                                    @if ($myPendingAssets->count() > 0)
                                        <button onclick="openDetailModal({{ $cl->id }})"
                                            class="h-8 px-3 rounded-lg border border-emerald-200
                                            bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold flex items-center gap-1.5 transition-colors ml-auto">
                                            <i class="bi bi-check2-circle"></i>
                                            Process
                                        </button>
                                    @else
                                        <button onclick="openDetailModal({{ $cl->id }})"
                                            class="h-8 px-3 rounded-lg border border-gray-200
                                            hover:bg-gray-50 text-gray-500 text-xs font-medium flex items-center gap-1.5 transition-colors ml-auto">
                                            <i class="bi bi-eye"></i>
                                            Detail
                                        </button>
                                    @endif

                                </td>
                            </tr>

                            {{-- Proses and Detail Modal --}}
                            @include('clearance.partials.detail-modal', [
                                'clearance' => $cl,
                                'currentRole' => $currentRole,
                                'showAction' => true,
                            ])
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-14 text-center">
                                    <i class="bi bi-inbox text-5xl text-gray-200 block mb-3"></i>
                                    <p class="text-sm text-gray-400">Tidak ada clearance ditemukan.</p>
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
                        {{ $clearances->total() }} clearance
                    </p>
                    <div class="flex items-center gap-2">
                        <select onchange="window.location.href=this.value"
                            class="h-8 px-2 text-xs border border-gray-200 rounded-lg bg-white focus:outline-none">
                            @foreach ([5, 10, 25, 50] as $n)
                                <option value="{{ request()->fullUrlWithQuery(['per_page' => $n]) }}"
                                    @selected($perPage == $n)>{{ $n }} / hal</option>
                            @endforeach
                        </select>
                        {{ $clearances->onEachSide(1)->links('pagination::tailwind') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- DETAIL MODAL GLOBAL --}}
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

    {{-- Hidden Forms --}}
    <form id="form-approve" method="POST" class="hidden">@csrf</form>
    <form id="form-reject" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="notes" id="reject-notes-input">
    </form>
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

        document.addEventListener('keydown', e => e.key === 'Escape' && closeDetailModal());

        function confirmApprove(id, name) {
            Swal.fire({
                title: 'Setujui aset ini?',
                html: `Aset <strong>${name}</strong> akan ditandai sebagai <em>dikembalikan</em>.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="bi bi-check-lg me-1"></i> Ya, Setuju',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then(r => {
                if (!r.isConfirmed) return;
                const form = document.getElementById('form-approve');
                form.action = `{{ url('mis/clearance/aset') }}/${id}/approve`;
                form.submit();
            });
        }

        function confirmReject(id, name) {
            Swal.fire({
                title: 'Tolak aset ini?',
                html: `<p class="text-sm text-gray-500 mb-3">Aset: <strong>${name}</strong></p>`,
                input: 'textarea',
                inputPlaceholder: 'Tulis alasan penolakan / revisi...',
                inputAttributes: {
                    rows: 3
                },
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="bi bi-x-circle me-1"></i> Kirim Revisi',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                inputValidator: v => !v?.trim() && 'Catatan wajib diisi.',
            }).then(r => {
                if (!r.isConfirmed) return;
                document.getElementById('reject-notes-input').value = r.value;
                const form = document.getElementById('form-reject');
                form.action = `{{ url('mis/clearance/aset') }}/${id}/reject`;
                form.submit();
            });
        }
    </script>
@endpush