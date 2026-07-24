{{-- resources/views/clearance/partials/revision-modal.blade.php --}}
{{-- Variables: $cl, $jenisLabel, $revisionApproval, $approvalSteps --}}

@if ($cl->status === 'revision')
    <div id="revisi-{{ $cl->id }}" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4"
        onclick="if(event.target===this) toggleModal('revisi-{{ $cl->id }}', false)">

        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col">

            <form action="{{ route('karyawan.clearance.revisi', $cl->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Header --}}
                <div class="flex items-center justify-between px-5 py-4 border-b flex-shrink-0">
                    <div>
                        <h2 class="font-bold text-gray-800">Upload Revisi</h2>
                        <p class="text-xs text-gray-400">{{ $jenisLabel[$cl->jenis] ?? '-' }}</p>
                    </div>
                    <button type="button" onclick="toggleModal('revisi-{{ $cl->id }}', false)"
                        class="w-8 h-8 rounded-lg hover:bg-gray-100 text-gray-400 flex items-center justify-center transition">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-5 overflow-y-auto flex-1 space-y-4">

                    @if ($revisionApproval?->notes)
                        <div
                            class="flex items-start gap-2 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-xs text-yellow-800">
                            <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-0.5"></i>
                            <div>
                                <p class="font-semibold mb-0.5">
                                    Catatan dari
                                    {{ $approvalSteps[($revisionApproval->step_order ?? 1) - 1] ?? 'Approver' }}:
                                </p>
                                <p>{{ $revisionApproval->notes }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-3">
                        @foreach ($cl->clearanceAset->whereIn('status_pengembalian', ['missing', 'damaged']) as $ca)
                            @php
                                $asb = [
                                    'pending' => ['label' => 'Menunggu', 'class' => 'bg-gray-100 text-gray-600'],
                                    'returned' => ['label' => 'Dikembalikan', 'class' => 'bg-green-100 text-green-700'],
                                    'missing' => ['label' => 'Revisi', 'class' => 'bg-red-100 text-red-700'],
                                    'damaged' => ['label' => 'Rusak', 'class' => 'bg-orange-100 text-orange-700'],
                                ][$ca->status_pengembalian];
                            @endphp

                            <div class="border border-gray-200 rounded-lg p-3">
                                <div class="flex items-center justify-between mb-2.5">
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">{{ $ca->aset->nama ?? '-' }}</p>
                                        <p class="text-xs text-gray-400">
                                            {{ $ca->aset->kategori->nama ?? '-' }}
                                            @if ($ca->aset->asset_no)
                                                · <span class="font-mono">{{ $ca->aset->asset_no }}</span>
                                            @endif
                                        </p>
                                    </div>
                                    <span class="text-xs font-semibold {{ $asb['class'] }} px-2 py-0.5 rounded-full">
                                        {{ $asb['label'] }}
                                    </span>
                                </div>

                                <label class="block text-xs text-gray-500 mb-1">Upload bukti baru</label>
                                <input type="file" name="bukti[{{ $ca->aset_id }}]" accept=".jpg,.jpeg,.png,.pdf"
                                    class="w-full text-sm text-gray-600 border border-gray-200 rounded-lg px-3 py-2
                                           file:mr-3 file:py-1 file:px-3 file:rounded file:border-0
                                           file:text-xs file:font-medium cursor-pointer">
                            </div>
                        @endforeach
                    </div>

                </div>

                {{-- Footer --}}
                <div class="flex justify-end gap-2 px-5 py-4 border-t bg-gray-50 rounded-b-xl flex-shrink-0">
                    <button type="button" onclick="toggleModal('revisi-{{ $cl->id }}', false)"
                        class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition">
                        <i class="bi bi-upload"></i> Upload Revisi
                    </button>
                </div>

            </form>
        </div>
    </div>
@endif
