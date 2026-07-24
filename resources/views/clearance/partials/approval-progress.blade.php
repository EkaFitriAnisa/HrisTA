@php
    $statusClass = [
        'approved' => [
            'dot' => 'bg-emerald-500 shadow-sm shadow-emerald-200',
            'label' => 'Disetujui',
            'text' => 'text-emerald-600',
            'icon' => 'bi-check-lg',
        ],
        'revision' => [
            'dot' => 'bg-yellow-400 shadow-sm shadow-yellow-200',
            'label' => 'Revisi',
            'text' => 'text-yellow-700',
            'icon' => 'bi-exclamation',
        ],
        'rejected' => [
            'dot' => 'bg-red-500 shadow-sm shadow-red-200',
            'label' => 'Ditolak',
            'text' => 'text-red-600',
            'icon' => 'bi-x-lg',
        ],
        'pending' => [
            'dot' => 'bg-white border-2 border-gray-200',
            'label' => 'Menunggu',
            'text' => 'text-gray-400',
            'icon' => null,
        ],
    ];

    $approvalSteps = $approvalSteps ?? [];
    $approvals = $approvals ?? collect();
@endphp

@if (count($approvalSteps))
    <div class="mb-6">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">
            Progress Approval
        </p>

        <div class="relative flex justify-between">
            <div class="absolute top-4 left-[14%] right-[14%] h-0.5 bg-gray-200 z-0"></div>

            @foreach ($approvalSteps as $index => $step)
                @php
                    $ap = $approvals->get($index + 1);
                    $st = $ap?->status ?? 'pending';
                    $meta = $statusClass[$st] ?? $statusClass['pending'];

                    $circleCls = match ($st) {
                        'approved' => 'bg-emerald-500 shadow-sm shadow-emerald-200',
                        'revision' => 'bg-yellow-400 shadow-sm shadow-yellow-200',
                        'rejected' => 'bg-red-500 shadow-sm shadow-red-200',
                        default => 'bg-white border-2 border-gray-200',
                    };

                    $circleIcon = match ($st) {
                        'approved' => '<i class="bi bi-check-lg text-white text-sm"></i>',
                        'revision' => '<i class="bi bi-exclamation text-white text-sm font-bold"></i>',
                        'rejected' => '<i class="bi bi-x-lg text-white text-sm"></i>',
                        default => '<span class="text-xs font-bold text-gray-400">' . strtoupper(substr($step, 0, 1)) . '</span>',
                    };
                @endphp

                <div class="relative z-10 flex flex-col items-center flex-1">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center {{ $circleCls }}">
                        {!! $circleIcon !!}
                    </div>

                    <p class="mt-2 text-xs font-bold text-gray-700">
                        {{ strtoupper($step) }}
                    </p>

                    <span class="mt-1 text-[10px] font-medium {{ $meta['text'] }}">
                        {{ $meta['label'] }}
                    </span>

                    @if ($ap?->approved_at)
                        <p class="mt-0.5 text-[10px] text-gray-400 text-center">
                            {{ $ap->approver?->karyawan?->nama ?? '-' }}
                        </p>
                        <p class="text-[9px] text-gray-300">
                            {{ $ap->approved_at->format('d M, H:i') }}
                        </p>
                    @endif

                    @if ($ap?->notes && $st !== 'approved')
                        <p class="mt-1 text-[10px] text-yellow-700 text-center max-w-[100px] italic">
                            "{{ \Illuminate\Support\Str::limit($ap->notes, 40) }}"
                        </p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endif