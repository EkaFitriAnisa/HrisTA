{{-- resources/views/karyawan/pages/clearance.blade.php --}}
@extends('karyawan.layouts.app')

@section('title', 'Clearance Saya')

@php
    $activePage = 'clearance';

    $jenisLabel = [
        'resign' => 'Resign',
        'mutasi_internal' => 'Mutasi Internal',
    ];

    $jenisIcon = [
        'resign' => 'bi-door-open',
        'mutasi_internal' => 'bi-arrow-left-right',
    ];
@endphp

@section('content')
    @include('clearance.partials.flash')

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Clearance Saya</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola & pantau pengajuan clearance aset kamu</p>
        </div>

        @if (!$hasActiveClearance)
            <a href="#modalAjukan" onclick="toggleModal('modalAjukan', true); return false;"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                <i class="bi bi-file-earmark-plus"></i> Ajukan Clearance
            </a>
        @else
            <span
                class="inline-flex items-center gap-2 px-3 py-2 bg-blue-50 text-blue-700 text-sm rounded-lg border border-blue-100">
                <i class="bi bi-clock-history"></i>
                Clearance sedang berjalan
            </span>
        @endif
    </div>

    {{-- DAFTAR CLEARANCE --}}
    @if ($clearances->isEmpty())
        <div class="bg-white border border-gray-200 rounded-xl py-20 text-center">
            <i class="bi bi-inbox text-5xl text-gray-300 block mb-3"></i>
            <p class="text-gray-500 font-medium">Belum ada clearance yang diajukan</p>
            <p class="text-sm text-gray-400 mt-1">Klik tombol "Ajukan Clearance" untuk memulai</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($clearances as $cl)
                @php
                    $approvalSteps = $cl->clearanceAset
                        ->pluck('aset.kategori.managed_role')
                        ->filter()
                        ->unique()
                        ->values()
                        ->all();

                    $approvals = $cl->approvals->keyBy('step_order');
                    $approvedCount = $cl->approvals->where('status', 'approved')->count();
                    $totalSteps = count($approvalSteps);
                    $revisionApproval = $cl->approvals->firstWhere('status', 'revision');
                    $rejectedApproval = $cl->approvals->firstWhere('status', 'rejected');
                @endphp

                <div
                    class="bg-white border border-gray-200 rounded-xl overflow-hidden
                    @if ($cl->status === 'rejected') border-l-4 border-l-red-400
                    @elseif($cl->status === 'revision') border-l-4 border-l-yellow-400
                    @elseif($cl->status === 'approved') border-l-4 border-l-green-400
                    @elseif($cl->status === 'process')  border-l-4 border-l-blue-400 @endif">

                    {{-- Header card --}}
                    <div
                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                <i class="bi {{ $jenisIcon[$cl->jenis] ?? 'bi-file-text' }} text-gray-600 text-lg"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $jenisLabel[$cl->jenis] ?? '-' }}</p>
                                <p class="text-xs text-gray-400">Diajukan {{ $cl->created_at->format('d M Y') }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            @include('clearance.partials.status-badge', ['status' => $cl->status])

                            <button onclick="openDetailModal({{ $cl->id }})"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition">
                                <i class="bi bi-eye"></i> Detail
                            </button>
                        </div>
                    </div>

                    {{-- Ringkasan --}}
                    <div class="px-5 py-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Tanggal Efektif</p>
                            <p class="font-medium text-gray-800">{{ $cl->tanggal_efektif->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Jumlah Aset</p>
                            <p class="font-medium text-gray-800">{{ $cl->clearanceAset->count() }} aset</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Progress Approval</p>
                            <p class="font-medium text-gray-800">
                                {{ $approvedCount }} / {{ max($totalSteps, 1) }} disetujui
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Alasan</p>
                            <p class="font-medium text-gray-700 truncate" title="{{ $cl->alasan }}">
                                {{ \Illuminate\Support\Str::limit($cl->alasan, 40) }}
                            </p>
                        </div>
                    </div>

                    {{-- Banner revisi / rejected --}}
                    @if ($cl->status === 'revision')
                        <div
                            class="mx-5 mb-4 flex items-start gap-2 bg-yellow-50 border border-yellow-200 rounded-lg px-3 py-2.5 text-xs text-yellow-800">
                            <i class="bi bi-exclamation-triangle-fill mt-0.5 flex-shrink-0"></i>
                            <div>
                                <span class="font-semibold">Perlu revisi</span>
                                @if ($revisionApproval?->notes)
                                    — {{ $revisionApproval->notes }}
                                @endif
                                <button onclick="toggleModal('revisi-{{ $cl->id }}', true)"
                                    class="ml-2 underline font-semibold text-yellow-700 hover:text-yellow-900">
                                    Upload Revisi →
                                </button>
                            </div>
                        </div>
                    @elseif ($cl->status === 'rejected')
                        <div
                            class="mx-5 mb-4 flex items-start gap-2 bg-red-50 border border-red-200 rounded-lg px-3 py-2.5 text-xs text-red-800">
                            <i class="bi bi-x-circle-fill mt-0.5 flex-shrink-0"></i>
                            <div>
                                <span class="font-semibold">Clearance ditolak</span>
                                @if ($rejectedApproval?->notes)
                                    — {{ $rejectedApproval->notes }}
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Compact approval progress --}}
                    @if (!empty($approvalSteps))
                        <div class="px-5 pb-4 flex items-center gap-0">
                            @foreach ($approvalSteps as $idx => $role)
                                @php
                                    $ap = $approvals->get($idx + 1);
                                    $st = $ap?->status ?? 'pending';
                                    $isLast = $idx === count($approvalSteps) - 1;
                                @endphp

                                <div class="flex items-center gap-0">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold ring-2
                                            @if ($st === 'approved') bg-green-100 text-green-700 ring-green-300
                                            @elseif($st === 'rejected') bg-red-100 text-red-700 ring-red-300
                                            @elseif($st === 'revision') bg-yellow-100 text-yellow-700 ring-yellow-300
                                            @else bg-gray-100 text-gray-500 ring-gray-200 @endif">
                                            @if ($st === 'approved')
                                                <i class="bi bi-check text-xs"></i>
                                            @elseif($st === 'rejected')
                                                <i class="bi bi-x text-xs"></i>
                                            @elseif($st === 'revision')
                                                <i class="bi bi-pencil text-xs"></i>
                                            @else
                                                {{ $ap?->step_order ?? $idx + 1 }}
                                            @endif
                                        </div>
                                        <span class="text-[10px] text-gray-500 mt-1">{{ strtoupper($role) }}</span>
                                    </div>

                                    @if (!$isLast)
                                        <div
                                            class="h-0.5 w-8 sm:w-12 mb-4
                                            @if ($st === 'approved') bg-green-300 @else bg-gray-200 @endif">
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Shared detail template --}}
                @include('clearance.partials.detail-modal', [
                    'clearance' => $cl,
                    'showAction' => false,
                ])
            @endforeach
        </div>
    @endif
    
    {{-- Revision upload modal --}}
    @foreach ($clearances as $cl)
    @include( 'clearance.partials.revision-modal', compact('cl', 'jenisLabel', 'revisionApproval', 'approvalSteps'))
    @endforeach

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

    {{-- MODAL AJUKAN CLEARANCE --}}
    @if (!$hasActiveClearance)
        @include('clearance.partials.ajukan-modal', compact('jenisLabel', 'asetAktif', 'departemens'))
    @endif

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

        function toggleModal(id, open) {
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.toggle('hidden', !open);
            el.classList.toggle('flex', open);
            document.body.style.overflow = open ? 'hidden' : '';
        }

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

        document.getElementById('selectJenis')?.addEventListener('change', function() {
            const wrap = document.getElementById('wrapDepartTujuan');
            const need = ['mutasi_internal', 'mutasi_eksternal'].includes(this.value);
            wrap.classList.toggle('hidden', !need);
        });

        @if ($errors->any())
            toggleModal('modalAjukan', true);
        @endif

        document.addEventListener('DOMContentLoaded', () => {
            const fields = document.querySelectorAll('input, select, textarea');

            fields.forEach(field => {
                field.addEventListener('input', () => clearFieldError(field));
                field.addEventListener('change', () => clearFieldError(field));
            });

            function clearFieldError(field) {
                const errorText = field.parentElement.querySelector('.field-error');
                field.classList.remove('border-red-300', 'focus:ring-red-500');
                if (field.tagName === 'SELECT' || field.tagName === 'TEXTAREA' || field.type !== 'file') {
                    field.classList.add('border-gray-300', 'focus:ring-blue-500');
                }
                if (errorText) errorText.remove();
            }
        });
    </script>
@endpush