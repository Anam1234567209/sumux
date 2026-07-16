@php
    $breadcrumbs = \App\Helpers\BreadcrumbHelper::getBreadcrumbs();
@endphp

<div class="flex items-center gap-2 text-l text-slate-400">
    @foreach ($breadcrumbs as $breadcrumb)
        @if (!$breadcrumb['isLast'] && Route::has($breadcrumb['route']))
            <a href="{{ route($breadcrumb['route']) }}" class="hover:text-slate-600 transition">
                {{ $breadcrumb['label'] }}
            </a>
            <span class="text-slate-600">/</span>
        @elseif (!$breadcrumb['isLast'])
            <span class="hover:text-slate-600 transition">
                {{ $breadcrumb['label'] }}
            </span>
            <span class="text-slate-600">/</span>
        @else
            <span class="text-slate-700 font-medium">
                {{ $breadcrumb['label'] }}
            </span>
        @endif
    @endforeach
</div>
