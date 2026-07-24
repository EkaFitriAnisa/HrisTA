@php
    $statusMeta = [

        'approved' => [
            'class' => 'bg-emerald-50 border-emerald-200 text-emerald-700',
            'label' => 'Selesai',
            'icon'  => 'bi-check-circle-fill',
        ],

        'rejected' => [
            'class' => 'bg-red-50 border-red-200 text-red-600',
            'label' => 'Ditolak',
            'icon'  => 'bi-x-circle-fill',
        ],

        'revision' => [
            'class' => 'bg-yellow-50 border-yellow-200 text-yellow-700',
            'label' => 'Revisi',
            'icon'  => 'bi-exclamation-circle-fill',
        ],

        'process' => [
            'class' => 'bg-blue-50 border-blue-200 text-blue-700',
            'label' => 'Diproses',
            'icon'  => 'bi-arrow-repeat',
        ],

        'pending' => [
            'class' => 'bg-gray-100 border-gray-200 text-gray-600',
            'label' => 'Menunggu',
            'icon'  => 'bi-clock-history',
        ],
    ];

    $meta = $statusMeta[$status ?? 'pending']
        ?? $statusMeta['pending'];
@endphp

<span
    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border text-xs font-semibold {{ $meta['class'] }}">

    <i class="bi {{ $meta['icon'] }}"></i>

    {{ $meta['label'] }}
</span>