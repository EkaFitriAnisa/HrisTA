@php
    $clearance = $clearance ?? null;
    $currentRole = $currentRole ?? null;
    $showAction = $showAction ?? false;

    $jenisLabel = [
        'resign' => 'Resign',
        'mutasi_internal' => 'Mutasi Internal',
    ];

    $approvals = $clearance?->approvals?->keyBy('step_order');
    $flow = ['HOD', 'MIS', 'HRD'];
    $usedRoles =
        $clearance?->clearanceAset?->pluck('aset.kategori.managed_role')?->filter()?->unique()?->values()?->all() ?? [];
    $approvalSteps = collect($flow)->filter(fn($role) => in_array($role, $usedRoles, true))->values()->all();
    $statusLabel = [
        'pending' => 'Menunggu',
        'process' => 'Diproses',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
        'revision' => 'Perlu Revisi',
    ];
@endphp
<template id="detail-template-{{ $clearance->id }}">

    {{-- Info --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">

        <div>
            <p class="text-xs text-gray-400 mb-0.5">
                Karyawan
            </p>
            <p class="text-sm font-semibold text-gray-800">
                {{ $clearance->karyawan?->nama }}
            </p>
            <p class="text-xs text-gray-500">
                {{ $clearance->karyawan?->jabatan }}
            </p>
        </div>

        <div>
            <p class="text-xs text-gray-400 mb-0.5">
                Jenis
            </p>

            <p class="text-sm font-medium text-gray-800">
                {{ $jenisLabel[$clearance->jenis] ?? $clearance->jenis }}
            </p>

            @if ($clearance->departTujuan)
                <p class="text-xs text-gray-500">
                    → {{ $clearance->departTujuan->nama }}
                </p>
            @endif
        </div>

        <div>
            <p class="text-xs text-gray-400 mb-0.5">
                Tanggal Efektif
            </p>

            <p class="text-sm font-medium text-gray-800">
                {{ $clearance->tanggal_efektif->format('d M Y') }}
            </p>
        </div>

        <div>
            <p class="text-xs text-gray-400 mb-0.5">
                Status
            </p>

            @include('clearance.partials.status-badge', [
                'status' => $clearance->status,
            ])
        </div>

        @if ($clearance->alasan)
            <div class="col-span-2 sm:col-span-4">

                <p class="text-xs text-gray-400 mb-0.5">
                    Alasan
                </p>

                <p class="text-sm text-gray-700">
                    {{ $clearance->alasan }}
                </p>
            </div>
        @endif
    </div>

    {{-- Progress --}}
    @include('clearance.partials.approval-progress', [
        'approvalSteps' => $approvalSteps,
        'approvals' => $approvals,
    ])

    {{-- Assets --}}
    <div>

        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">

            Daftar Aset
            ({{ $clearance->clearanceAset->count() }})
        </p>

        @php
            $groupedAssets = $clearance->clearanceAset->groupBy(
                fn($item) => $item?->aset?->kategori?->nama ?? 'Lainnya',
            );

            $categoryColors = [
                'Aset Fisik' => [
                    'header' => 'bg-blue-50 border-blue-200',
                    'text' => 'text-blue-700',
                    'icon' => 'text-blue-500',
                ],

                'Aset Kredensial' => [
                    'header' => 'bg-purple-50 border-purple-200',
                    'text' => 'text-purple-700',
                    'icon' => 'text-purple-500',
                ],

                'Aset Fasilitas' => [
                    'header' => 'bg-emerald-50 border-emerald-200',
                    'text' => 'text-emerald-700',
                    'icon' => 'text-emerald-500',
                ],
            ];
        @endphp

        <div class="space-y-3">

            @foreach ($groupedAssets as $category => $assets)
                @php

                    $accordionId = 'accordion-' . md5($category . $clearance->id);

                    $style = $categoryColors[$category] ?? $categoryColors['Lainnya'];

                @endphp

                <div class="rounded-xl border overflow-hidden {{ $style['header'] }}">

                    {{-- Header --}}
                    <button type="button" onclick="toggleAccordion('{{ $accordionId }}')"
                        class="w-full flex items-center justify-between
                px-4 py-3 text-left transition">

                        <div>

                            <p class="text-sm font-semibold {{ $style['text'] }}">

                                {{ $category }}

                            </p>

                            <p class="text-xs mt-0.5 opacity-80 {{ $style['text'] }}">

                                {{ $assets->count() }} aset

                            </p>

                        </div>

                        <i id="icon-{{ $accordionId }}"
                            class="bi bi-chevron-down text-sm
                    transition-transform duration-200
                    rotate-180
                    {{ $style['icon'] }}">
                        </i>
                    </button>

                    {{-- Body --}}
                    <div id="{{ $accordionId }}" class="border-t border-white/50 bg-white">

                        <div class="p-3 space-y-3">

                            @foreach ($assets as $ca)
                                @include('clearance.partials.asset-item', [
                                    'asset' => $ca,
                                
                                    'currentRole' => $currentRole,
                                
                                    'showAction' => $showAction,
                                
                                    'approvalSteps' => $approvalSteps,
                                
                                    'approvals' => $approvals,
                                ])
                            @endforeach

                        </div>

                    </div>

                </div>
            @endforeach

        </div>
    </div>
</template>

@push('scripts')
    <script>
        function toggleAccordion(id) {
            const body = document.getElementById(id);

            const icon = document.getElementById('icon-' + id);

            if (!body) return;

            body.classList.toggle('hidden');

            icon?.classList.toggle('rotate-180');
        }
    </script>
@endpush
