@if ($cl->status === 'revision')
    <div class="mx-5 mb-4 flex items-start gap-2 bg-yellow-50 border border-yellow-200 rounded-lg px-3 py-2.5 text-xs text-yellow-800">
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
    <div class="mx-5 mb-4 flex items-start gap-2 bg-red-50 border border-red-200 rounded-lg px-3 py-2.5 text-xs text-red-800">
        <i class="bi bi-x-circle-fill mt-0.5 flex-shrink-0"></i>
        <div>
            <span class="font-semibold">Clearance ditolak</span>
            @if ($rejectedApproval?->notes)
                — {{ $rejectedApproval->notes }}
            @endif
        </div>
    </div>
@endif