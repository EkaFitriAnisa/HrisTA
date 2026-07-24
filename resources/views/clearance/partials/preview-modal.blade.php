{{-- resources/views/clearance/partials/preview-modal.blade.php --}}

<div id="preview-modal" onclick="if(event.target === this) closePreviewModal()" class="fixed inset-0 bg-black/70 hidden items-center justify-center z-[999] p-4">

    <div class="relative w-full max-w-5xl">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-3">

            <h3 id="preview-title" class="text-white text-sm font-semibold">
            </h3>

            <button type="button" onclick="closePreviewModal()" class="text-white hover:text-gray-300 transition">

                <i class="bi bi-x-circle text-2xl"></i>

            </button>

        </div>

        {{-- Content --}}
        <div class="bg-white rounded-2xl overflow-hidden shadow-2xl">

            {{-- Image --}}
            <img id="preview-image" class="hidden w-full max-h-[85vh] object-contain bg-black">

            {{-- PDF --}}
            <iframe id="preview-pdf" class="hidden w-full h-[85vh]">
            </iframe>

        </div>

    </div>
</div>

@push('scripts')
    <script>
        function openPreviewModal(url, ext, title = 'Preview') {
            const modal = document.getElementById('preview-modal');

            const image = document.getElementById('preview-image');

            const pdf = document.getElementById('preview-pdf');

            const titleEl = document.getElementById('preview-title');

            titleEl.textContent = title;

            image.classList.add('hidden');
            pdf.classList.add('hidden');

            if (ext === 'pdf') {

                pdf.src = url;

                pdf.classList.remove('hidden');

            } else {

                image.src = url;

                image.classList.remove('hidden');
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            document.body.style.overflow = 'hidden';
        }

        function closePreviewModal() {
            const modal = document.getElementById('preview-modal');

            const image = document.getElementById('preview-image');

            const pdf = document.getElementById('preview-pdf');

            image.src = '';
            pdf.src = '';

            modal.classList.add('hidden');
            modal.classList.remove('flex');

            document.body.style.overflow = '';
        }
    </script>
@endpush