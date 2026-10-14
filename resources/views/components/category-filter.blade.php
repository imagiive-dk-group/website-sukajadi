@props(['categories', 'active' => null, 'route', 'labels' => 'site.categories'])
<div class="-mx-4 overflow-x-auto px-4 pb-1 sm:mx-0 sm:px-0">
    <div class="flex w-max gap-2">
        <a href="{{ lroute($route) }}" @class(['chip px-4 py-2 text-sm transition', 'bg-leaf-600 text-white' => ! $active, 'bg-white text-leaf-800 ring-1 ring-leaf-900/10 hover:bg-leaf-50' => $active])>{{ __('site.common.all') }}</a>
        @foreach($categories as $cat)
            <a href="{{ lroute($route, ['kategori' => $cat]) }}" @class(['chip px-4 py-2 text-sm transition', 'bg-leaf-600 text-white' => $active === $cat, 'bg-white text-leaf-800 ring-1 ring-leaf-900/10 hover:bg-leaf-50' => $active !== $cat])>{{ __($labels.'.'.$cat) }}</a>
        @endforeach
    </div>
</div>
