@props(['compact' => false])
{{-- Peran dalam tim penggiat. Dipakai selama belum ada profil anggota (nama & foto) di admin. --}}
<ul data-stagger @class(['grid gap-3 sm:gap-4', 'grid-cols-2' => $compact, 'sm:grid-cols-2 lg:grid-cols-3' => ! $compact])>
    @foreach(__('site.team.roles') as $role)
        <li class="card p-4 sm:p-5">
            <span class="flex size-10 items-center justify-center rounded-xl bg-leaf-100 text-leaf-700">
                <x-dynamic-component :component="'heroicon-o-'.$role['icon']" class="size-5" />
            </span>
            <h3 class="mt-3 font-display text-sm font-bold text-leaf-900 sm:text-base">{{ $role['title'] }}</h3>
            @unless($compact)
                <p class="mt-1 text-sm text-leaf-950/65">{{ $role['text'] }}</p>
            @endunless
        </li>
    @endforeach
</ul>
