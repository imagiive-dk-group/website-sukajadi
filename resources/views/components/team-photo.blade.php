@props(['member', 'ratio' => '4/5', 'size' => 'md', 'sizes' => null])
{{-- Foto pengurus; bila belum ada foto, tampil inisial (bukan kotak placeholder). --}}
@if($member->photo_path)
    <x-img :path="$member->photo_path" :alt="$member->name" :ratio="$ratio" :size="$size" :sizes="$sizes ?? '(min-width: 1024px) 25vw, 50vw'" />
@else
    @php
        $initials = collect(preg_split('/\s+/', trim($member->name)))
            ->reject(fn ($w) => str_ends_with($w, '.'))
            ->take(2)
            ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
            ->implode('');
    @endphp
    <div role="img" aria-label="{{ $member->name }}" class="grid w-full place-items-center bg-leaf-100 font-display text-4xl font-extrabold text-leaf-700 sm:text-5xl" style="aspect-ratio: {{ str_replace('/', ' / ', $ratio) }}">{{ $initials }}</div>
@endif
