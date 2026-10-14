<figure class="card flex h-full flex-col p-6">
    <div class="flex gap-0.5 text-sun-500" aria-label="{{ $testimonial->rating }}/5">
        @for($i = 1; $i <= 5; $i++)
            <x-heroicon-s-star @class(['size-5', 'opacity-25' => $i > $testimonial->rating]) />
        @endfor
    </div>
    <blockquote class="mt-4 flex-1 text-leaf-950/80">“{{ $testimonial->content }}”</blockquote>
    <figcaption class="mt-5 flex items-center gap-3 border-t border-leaf-900/5 pt-4">
        @if($testimonial->photo_path)
            <x-img :path="$testimonial->photo_path" ratio="1/1" size="sm" class="size-11 rounded-full" :alt="$testimonial->name" />
        @else
            <span class="flex size-11 items-center justify-center rounded-full bg-leaf-100 font-display font-bold text-leaf-700">{{ \Illuminate\Support\Str::of($testimonial->name)->substr(0, 1)->upper() }}</span>
        @endif
        <span>
            <span class="block font-semibold text-leaf-900">{{ $testimonial->name }}</span>
            @if($testimonial->organization)<span class="block text-xs text-leaf-950/55">{{ $testimonial->organization }}</span>@endif
        </span>
    </figcaption>
</figure>
