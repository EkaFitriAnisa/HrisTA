@php
    $asset = $asset ?? null;
    $currentRole = $currentRole ?? null;
    $showAction = $showAction ?? false;
    $managedRole = $asset?->aset?->kategori?->managed_role;
    
    $stepIndex = array_search(
        $managedRole,
        $approvalSteps,
        true
    );

    $previousApproved = true;

    $waitingRole = null;

    if ($stepIndex !== false && $stepIndex > 0) {
        for ($i = 0; $i < $stepIndex; $i++) {
            $prevApproval = $approvals->get($i + 1);
            if (
                ! $prevApproval ||
                $prevApproval->status !== 'approved'
            ) {
                $previousApproved = false;
                $waitingRole = $approvalSteps[$i];
                break;
            }
        }
    }

    $canAct =
        $showAction &&
        $managedRole === $currentRole &&
        $asset?->status_pengembalian === 'pending' &&
        $previousApproved;

    [$badgeClass, $badgeIcon, $badgeLabel] = match (
        $asset?->status_pengembalian
    ) {
        'returned' => [
            'bg-emerald-100 text-emerald-700',
            'bi-check-circle-fill',
            'Dikembalikan',
        ],

        'missing' => [
            'bg-yellow-100 text-yellow-700',
            'bi-exclamation-circle-fill',
            'Revisi',
        ],

        default => [
            'bg-gray-100 text-gray-600',
            'bi-clock-history',
            'Menunggu',
        ],
    };

    $roleBadgeClass = [
        'HOD' => 'bg-blue-100 text-blue-700',
        'MIS' => 'bg-purple-100 text-purple-700',
        'HRD' => 'bg-teal-100 text-teal-700',
    ];

    $detailLabel = null;
    $detailValue = null;

    if ($asset?->aset) {

        foreach (
            [
                'asset_no' => 'Asset No',
                'username' => 'Username',
                'platform' => 'Platform',
                'no_plat' => 'No Plat',
                'kondisi' => 'Kondisi',
            ]
            as $field => $label
        ) {

            if ($asset->aset->$field) {

                $detailLabel = $label;

                $detailValue = $asset->aset->$field;

                break;
            }
        }
    }
@endphp

<div
    class="rounded-xl border overflow-hidden
    {{ $canAct ? 'border-emerald-200 bg-emerald-50/30' : 'border-gray-100 bg-white' }}">

    <div class="px-4 py-3">

        {{-- Header --}}
        <div class="flex items-start justify-between gap-3 mb-2">

            <div class="min-w-0">

                <p class="text-sm font-semibold text-gray-800 truncate">
                    {{ $asset?->aset?->nama ?? '-' }}
                </p>

                <div class="flex flex-wrap items-center gap-1.5 mt-1">

                    {{-- Kategori --}}
                    <span
                        class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px]
                        font-medium bg-gray-100 text-gray-600 border border-gray-200">

                        {{ $asset?->aset?->kategori?->nama ?? '-' }}
                    </span>

                    {{-- Role --}}
                    @if ($managedRole)
                        <span
                            class="inline-flex items-center px-1.5 py-0.5 rounded-full
                            text-[10px] font-semibold
                            {{ $roleBadgeClass[$managedRole] ?? 'bg-gray-100 text-gray-600' }}">

                            {{ $managedRole }}
                        </span>
                    @endif

                    {{-- Detail --}}
                    @if ($detailLabel)
                        <span class="text-[10px] text-gray-400">

                            {{ $detailLabel }}:

                            <span class="text-gray-600">
                                {{ $detailValue }}
                            </span>
                        </span>
                    @endif
                </div>
            </div>

            {{-- Status --}}
            <span
                class="shrink-0 inline-flex items-center gap-1
                px-2 py-0.5 rounded-full text-[10px]
                font-semibold {{ $badgeClass }}">

                <i class="bi {{ $badgeIcon }}"></i>

                {{ $badgeLabel }}
            </span>
        </div>

        {{-- Catatan --}}
        @if ($asset?->catatan)
            <p class="text-xs text-yellow-700 bg-yellow-50 rounded-lg px-3 py-2 mb-2">

                <i class="bi bi-chat-left-text me-1"></i>

                {{ $asset->catatan }}
            </p>
        @endif

        {{-- Footer --}}
        <div class="flex flex-wrap items-center justify-between gap-2 my-2">

            {{-- Bukti --}}
            @if ($asset?->bukti_file)
                <button
                    type="button"
                    onclick="openPreviewModal(
                        '{{ \Illuminate\Support\Facades\Storage::url($asset->bukti_file) }}',
                        '{{ strtolower(pathinfo($asset->bukti_file, PATHINFO_EXTENSION)) }}',
                        {{ Js::from($asset?->aset?->nama ?? 'Preview Bukti') }}
                    )"
                    class="inline-flex items-center gap-1.5
                    text-xs text-blue-600 hover:text-blue-700 transition-colors">

                    <i class="bi bi-paperclip"></i>

                    Lihat Bukti

                </button>
            @else
                <span class="text-xs text-gray-400 italic">
                    Belum ada bukti
                </span>
            @endif

            {{-- Actions --}}
            @if ($canAct)

                <div class="flex items-center gap-2">

                    <button
                        onclick="confirmApprove(
                            {{ $asset->id }},
                            '{{ addslashes($asset?->aset?->nama ?? '') }}'
                        )"

                        class="h-7 px-3 rounded-lg bg-emerald-500
                        hover:bg-emerald-600 text-white text-xs
                        font-medium flex items-center gap-1 transition-colors">

                        <i class="bi bi-check-lg"></i>

                        Setuju
                    </button>

                    <button
                        onclick="confirmReject(
                            {{ $asset->id }},
                            '{{ addslashes($asset?->aset?->nama ?? '') }}'
                        )"

                        class="h-7 px-3 rounded-lg bg-red-500
                        hover:bg-red-600 text-white text-xs
                        font-medium flex items-center gap-1 transition-colors">

                        <i class="bi bi-x-lg"></i>

                        Tolak
                    </button>
                </div>
            @endif
        </div>

        {{-- Waiting Previous Approval --}}
        @if (
            $showAction &&
            $managedRole === $currentRole &&
            ! $previousApproved
        )

            <div class="pb-2">

                <div
                    class="flex items-center gap-2
                    rounded-lg border border-amber-200
                    bg-amber-50 px-3 py-2">

                    <i class="bi bi-hourglass-split text-amber-600 text-xs"></i>

                    <p class="text-[11px] text-amber-700">

                        Menunggu persetujuan

                        <span class="font-semibold">
                            {{ $waitingRole }}
                        </span>

                    </p>

                </div>

            </div>

        @endif
    </div>
</div>