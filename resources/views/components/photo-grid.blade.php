@props(['photos', 'gallery' => 'gallery'])
{{-- Grid foto dengan lightbox (GLightbox). Placeholder dibuka sebagai SVG berlabel. --}}
<div class="columns-2 gap-3 sm:gap-4 md:columns-3 [&>*]:mb-3 sm:[&>*]:mb-4">
    @foreach($photos as $photo)
        @php
            $caption = $photo->tr('caption') ?? $photo->placeholder_label;
            $ratios = ['4/5', '1/1', '3/2', '4/3', '3/4', '16/10'];
            $ratio = $ratios[$loop->index % count($ratios)];
            $full = \App\Support\Images::url($photo->path, 'lg') ?? route('placeholder', ['w' => 1600, 'h' => 1066, 'label' => $photo->placeholder_label]);
        @endphp
        <a href="{{ $full }}" class="glightbox group relative block break-inside-avoid overflow-hidden rounded-2xl" data-gallery="{{ $gallery }}" data-type="image" @if($caption) data-description="{{ $caption }}" @endif>
            <x-img :path="$photo->path" :label="$photo->placeholder_label" :alt="$caption" :ratio="$ratio" sizes="(min-width: 768px) 33vw, 50vw" class="transition duration-500 group-hover:scale-105" />
            <span class="absolute inset-0 bg-leaf-950/0 transition group-hover:bg-leaf-950/15"></span>
        </a>
    @endforeach
</div>
