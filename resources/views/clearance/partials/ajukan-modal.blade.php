{{-- resources/views/clearance/partials/ajukan-modal.blade.php --}}
{{-- Variables: $jenisLabel, $asetAktif, $departemens --}}

<div id="modalAjukan" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4"
    onclick="if(event.target===this) toggleModal('modalAjukan', false)">

    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">

        <form action="{{ route('karyawan.clearance.store') }}" method="POST" enctype="multipart/form-data"
            class="flex flex-col flex-1 min-h-0">
            @csrf

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b flex-shrink-0">
                <div>
                    <h2 class="font-bold text-gray-800">Ajukan Clearance</h2>
                    <p class="text-xs text-gray-400">Isi form berikut untuk mengajukan clearance</p>
                </div>
                <button type="button" onclick="toggleModal('modalAjukan', false)"
                    class="w-8 h-8 rounded-lg hover:bg-gray-100 text-gray-400 flex items-center justify-center transition">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-5 overflow-y-auto flex-1 space-y-4">

                {{-- Jenis + Tanggal --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Jenis Clearance <span class="text-red-500">*</span>
                        </label>
                        <select name="jenis" id="selectJenis" required
                            class="w-full border rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2
                                @error('jenis') border-red-300 focus:ring-red-500 @else border-gray-300 focus:ring-blue-500 @enderror">
                            <option value="">- Pilih jenis -</option>
                            @foreach ($jenisLabel as $val => $lbl)
                                <option value="{{ $val }}" {{ old('jenis') == $val ? 'selected' : '' }}>
                                    {{ $lbl }}
                                </option>
                            @endforeach
                        </select>
                        @error('jenis')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Tanggal Efektif <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_efektif" required
                            min="{{ now()->addDay()->toDateString() }}" value="{{ old('tanggal_efektif') }}"
                            class="w-full border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2
                                @error('tanggal_efektif') border-red-300 focus:ring-red-500 @else border-gray-300 focus:ring-blue-500 @enderror">
                        @error('tanggal_efektif')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- Departemen Tujuan (conditional) --}}
                <div id="wrapDepartTujuan"
                    class="{{ in_array(old('jenis'), ['mutasi_internal', 'mutasi_eksternal']) ? '' : 'hidden' }}">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Departemen Tujuan <span class="text-red-500">*</span>
                    </label>
                    <select name="depart_tujuan_id"
                        class="w-full border rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2
                            @error('depart_tujuan_id') border-red-300 focus:ring-red-500 @else border-gray-300 focus:ring-blue-500 @enderror">
                        <option value="">— Pilih departemen —</option>
                        @foreach ($departemens as $dep)
                            <option value="{{ $dep->id }}"
                                {{ old('depart_tujuan_id') == $dep->id ? 'selected' : '' }}>
                                {{ $dep->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('depart_tujuan_id')
                        <p class="field-error mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Alasan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Alasan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="alasan" rows="3" required placeholder="Jelaskan alasan pengajuan clearance..."
                        class="w-full border rounded-lg px-3 py-2.5 text-sm resize-none focus:outline-none focus:ring-2
                            @error('alasan') border-red-300 focus:ring-red-500 @else border-gray-300 focus:ring-blue-500 @enderror">{{ old('alasan') }}</textarea>
                    @error('alasan')
                        <p class="field-error mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Upload Bukti Pengembalian --}}
                @if ($asetAktif->isNotEmpty())
                    <div>
                        <div class="mb-2">
                            <label class="text-sm font-semibold text-gray-700">Upload Bukti Pengembalian</label>
                        </div>
                        @php

                            $groupedAssets = $asetAktif->groupBy(
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

                                'Lainnya' => [
                                    'header' => 'bg-gray-50 border-gray-200',
                                    'text' => 'text-gray-700',
                                    'icon' => 'text-gray-500',
                                ],
                            ];

                        @endphp

                        <div class="space-y-3">

                            @foreach ($groupedAssets as $category => $assets)
                                @php

                                    $accordionId = 'upload-accordion-' . md5($category);

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
                                            class="bi bi-chevron-down rotate-180
                    text-sm transition-transform duration-200
                    {{ $style['icon'] }}">
                                        </i>
                                    </button>

                                    {{-- Body --}}
                                    <div id="{{ $accordionId }}" class="border-t border-white/50 bg-white">

                                        <div class="p-3 space-y-3">

                                            @foreach ($assets as $assign)
                                                <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">

                                                    <div class="flex items-center justify-between mb-2">

                                                        <div>

                                                            <p class="text-sm font-medium text-gray-800">

                                                                {{ $assign->aset->nama }}

                                                            </p>

                                                            <p class="text-xs text-gray-400">

                                                                {{ $assign->aset->kategori->nama }}

                                                                @if ($assign->aset->asset_no)
                                                                    ·

                                                                    <span class="font-mono">

                                                                        {{ $assign->aset->asset_no }}

                                                                    </span>
                                                                @endif

                                                            </p>

                                                        </div>

                                                    </div>

                                                    {{-- Upload --}}
                                                    <input type="file" required accept=".jpg,.jpeg,.png"
                                                        name="bukti[{{ $assign->aset_id }}]"
                                                        class="w-full text-sm border rounded-lg px-3 py-2
                                file:mr-3 file:py-1 file:px-3
                                file:rounded file:border-0
                                file:text-xs file:font-medium
                                cursor-pointer bg-white

                                @error('bukti.' . $assign->aset_id)
                                    border-red-300
                                @else
                                    border-gray-300
                                @enderror">

                                                    {{-- Error --}}
                                                    @error('bukti.' . $assign->aset_id)
                                                        <p class="field-error mt-1 text-xs text-red-500">

                                                            {{ $message }}

                                                        </p>
                                                    @enderror

                                                    {{-- Info --}}
                                                    <p class="mt-1 text-[11px] text-gray-400">

                                                        Format JPG/PNG maksimal 2MB

                                                    </p>

                                                </div>
                                            @endforeach

                                        </div>

                                    </div>

                                </div>
                            @endforeach

                        </div>
                    </div>
                @else
                    <div
                        class="flex items-center gap-2 p-3 bg-blue-50 border border-blue-100 rounded-lg text-xs text-blue-700">
                        <i class="bi bi-info-circle"></i>
                        Kamu tidak sedang memegang aset. Tidak perlu upload bukti pengembalian.
                    </div>
                @endif

            </div>

            {{-- Footer --}}
            <div class="flex justify-end gap-2 px-5 py-4 border-t bg-gray-50 rounded-b-xl flex-shrink-0">
                <button type="button" onclick="toggleModal('modalAjukan', false)"
                    class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                    Batal
                </button>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow-sm">
                    <i class="bi bi-send"></i> Ajukan
                </button>
            </div>

        </form>
    </div>
</div>

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
