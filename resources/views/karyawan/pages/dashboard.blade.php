{{-- resources/views/karyawan/pages/dashboard.blade.php --}}
@extends('karyawan.layouts.app')

@section('title', 'Dashboard')

@php
    $activePage = 'dashboard';

    $jenisLabel = [
        'resign' => 'Resign',
        'mutasi_internal' => 'Mutasi Internal',
    ];

    $statusBadge = [
        'pending' => ['label' => 'Menunggu', 'class' => 'bg-gray-100 text-gray-600'],
        'process' => ['label' => 'Diproses', 'class' => 'bg-blue-100 text-blue-700'],
        'approved' => ['label' => 'Disetujui', 'class' => 'bg-green-100 text-green-700'],
        'rejected' => ['label' => 'Ditolak', 'class' => 'bg-red-100 text-red-700'],
        'revision' => ['label' => 'Perlu Revisi', 'class' => 'bg-yellow-100 text-yellow-700'],
    ];

    $approvalBadge = [
        'pending' => ['label' => 'Menunggu', 'class' => 'bg-gray-100 text-gray-600'],
        'approved' => ['label' => 'Disetujui', 'class' => 'bg-green-100 text-green-700'],
        'revision' => ['label' => 'Perlu Revisi', 'class' => 'bg-yellow-100 text-yellow-700'],
        'rejected' => ['label' => 'Ditolak', 'class' => 'bg-red-100 text-red-700'],
    ];

    $approvalFlow = ['HOD', 'MIS', 'HRD'];
@endphp

@section('content')
    @include('clearance.partials.flash')

    {{-- HEADER --}}
    <div class="relative overflow-hidden mb-6 rounded-2xl bg-gradient-to-br from-blue-700 via-blue-600 to-indigo-600 px-6 py-7 shadow-lg">
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

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3 mb-6">
        @if ($clearanceAktif)
            @php
                $sb = $statusBadge[$clearanceAktif->status] ?? $statusBadge['pending'];
            @endphp

            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium {{ $sb['class'] }}">
                <i class="bi bi-clipboard-check"></i>
                Clearance: {{ $sb['label'] }}
            </span>
        @else
            <button type="button" onclick="openAjukanModal()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                <i class="bi bi-file-earmark-plus"></i>
                Ajukan Clearance
            </button>
        @endif
    </div>

    {{-- CLEARANCE AKTIF --}}
    @if ($clearanceAktif)
        @php
            $sb = $statusBadge[$clearanceAktif->status] ?? $statusBadge['pending'];

            $usedRoles = $clearanceAktif->clearanceAset
                ->pluck('aset.kategori.managed_role')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $approvalSteps = collect($approvalFlow)
                ->filter(fn($role) => in_array($role, $usedRoles, true))
                ->values()
                ->all();

            $approvals = $clearanceAktif->approvals->keyBy('step_order');
        @endphp

        <div class="bg-white border border-gray-200 rounded-xl p-5 mb-5">
            <div class="flex items-center gap-2 mb-4">
                <i class="bi bi-clipboard-check text-blue-600"></i>
                <h2 class="font-semibold text-gray-800 text-sm">Status Clearance Aktif</h2>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Jenis</p>
                    <p class="font-medium text-gray-800">{{ $jenisLabel[$clearanceAktif->jenis] ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Tanggal Efektif</p>
                    <p class="font-medium text-gray-800">{{ $clearanceAktif->tanggal_efektif->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Status</p>
                    <span class="inline-block px-2.5 py-1 rounded-md text-xs font-semibold {{ $sb['class'] }}">
                        {{ $sb['label'] }}
                    </span>
                </div>
                <div class="md:text-right">
                    <a href="{{ route('karyawan.clearance.index') }}"
                        class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 font-medium">
                        Lihat Detail <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            @if (!empty($approvalSteps))
                <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap gap-2">
                    @foreach ($approvalSteps as $index => $role)
                        @php
                            $approval = $approvals->get($index + 1);
                            $ab = $approvalBadge[$approval->status ?? 'pending'] ?? $approvalBadge['pending'];
                        @endphp

                        <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2">
                            <span
                                class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold {{ $ab['class'] }}">
                                {{ $index + 1 }}
                            </span>
                            <div>
                                <p class="text-xs font-semibold text-gray-700">{{ $role }}</p>
                                <p class="text-xs text-gray-400">{{ $ab['label'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- ASET AKTIF --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100">
            <div class="flex items-center gap-2 text-sm font-semibold text-gray-800">
                <i class="bi bi-box-seam text-blue-600"></i>
                Aset yang Sedang Dipegang
            </div>
            <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-md text-xs font-semibold">
                {{ $asetAktif->count() }} aset
            </span>
        </div>

        @if ($asetAktif->isEmpty())
            <div class="py-12 text-center text-gray-400 text-sm">
                <i class="bi bi-inbox text-3xl block mb-2"></i>
                Kamu tidak sedang memegang aset apapun.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                        <tr>
                            <th class="px-5 py-3 text-left">#</th>
                            <th class="px-5 py-3 text-left">Nama Aset</th>
                            <th class="px-5 py-3 text-left">Kategori</th>
                            <th class="px-5 py-3 text-left">Pengelola</th>
                            <th class="px-5 py-3 text-left">Sejak</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($asetAktif as $i => $assign)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-5 py-3.5 text-gray-400">{{ $i + 1 }}</td>
                                <td class="px-5 py-3.5">
                                    <p class="font-medium text-gray-800">{{ $assign->aset->nama }}</p>
                                    @if ($assign->aset->asset_no)
                                        <p class="text-xs text-gray-400">{{ $assign->aset->asset_no }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-gray-600">{{ $assign->aset->kategori->nama }}</td>
                                <td class="px-5 py-3.5 text-gray-600">{{ $assign->aset->kategori->managed_role }}</td>
                                <td class="px-5 py-3.5 text-gray-500">
                                    {{ $assign->tanggal_assign->format('d M Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- MODAL AJUKAN CLEARANCE --}}
    @if (!$clearanceAktif)
        @include('clearance.partials.ajukan-modal', compact('jenisLabel', 'asetAktif', 'departemens'))
    @endif
@endsection

@push('scripts')
    <script>
        function openAjukanModal() {
            const modal = document.getElementById('modalAjukan');

            if (!modal) return;

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            document.body.style.overflow = 'hidden';
        }

        function closeAjukanModal(reset = false) {
            const modal = document.getElementById('modalAjukan');

            if (!modal) return;

            if (reset) {
                resetAjukanForm();
            }

            modal.classList.add('hidden');
            modal.classList.remove('flex');

            document.body.style.overflow = '';
        }

        function syncDepartTujuan() {
            const jenis = document.getElementById('selectJenis');
            const wrap = document.getElementById('wrapDepartTujuan');
            const depart = document.querySelector('[name="depart_tujuan_id"]');

            if (!jenis || !wrap || !depart) return;

            const need = [
                'mutasi_internal',
                'mutasi_eksternal'
            ].includes(jenis.value);

            wrap.classList.toggle('hidden', !need);

            depart.required = need;

            if (!need) {
                depart.value = '';
            }
        }

        function resetAjukanForm() {
            const modal = document.getElementById('modalAjukan');
            const form = modal?.querySelector('form');

            if (!form) return;

            form.reset();

            form.querySelectorAll('.field-error')
                .forEach(el => el.remove());

            form.querySelectorAll('input, select, textarea')
                .forEach(field => {

                    field.classList.remove(
                        'border-red-300',
                        'focus:ring-red-500'
                    );

                    field.classList.add(
                        'border-gray-300',
                        'focus:ring-blue-500'
                    );
                });

            form.querySelectorAll('input[type="file"]')
                .forEach(input => {
                    input.value = '';
                });

            syncDepartTujuan();
        }

        function clearFieldError(field) {
            field.classList.remove(
                'border-red-300',
                'focus:ring-red-500'
            );

            field.classList.add(
                'border-gray-300',
                'focus:ring-blue-500'
            );

            const container =
                field.closest('.field-wrapper') ||
                field.parentElement;

            const errorText =
                container?.querySelector('.field-error');

            if (errorText) {
                errorText.remove();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {

            syncDepartTujuan();

            const modal = document.getElementById('modalAjukan');
            const form = modal?.querySelector('form');

            if (!form) return;

            modal.addEventListener('click', function(e) {

                if (e.target === modal) {
                    closeAjukanModal(true);
                }
            });

            document.addEventListener('keydown', function(e) {

                if (
                    e.key === 'Escape' &&
                    !modal.classList.contains('hidden')
                ) {
                    closeAjukanModal(true);
                }
            });

            form.querySelectorAll(
                'input, select, textarea'
            ).forEach(field => {

                field.addEventListener('input', () => {
                    clearFieldError(field);
                });

                field.addEventListener('change', () => {

                    clearFieldError(field);

                    if (field.id === 'selectJenis') {
                        syncDepartTujuan();
                    }
                });
            });

            @if ($errors->any())
                openAjukanModal();
            @endif
        });
    </script>
@endpush