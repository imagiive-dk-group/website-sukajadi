@props(['photos'])
{{-- Pita foto berjalan (berhenti saat disentuh/diarahkan kursor). --}}
@if($photos->isNotEmpty())
    @php($rows = $photos->values()->split(2))
    <div class="marquee space-y-3 overflow-hidden sm:space-y-4" aria-hidden="true">
        @foreach($rows as $r => $row)
            <div class="marquee-track flex w-max gap-3 sm:gap-4 {{ $r === 1 ? 'reverse' : '' }}">
                @foreach([0, 1] as $copy)
                    @foreach($row as $photo)
                        <div class="relative h-36 w-56 shrink-0 overflow-hidden rounded-2xl sm:h-48 sm:w-72">
                            <x-img :path="$photo->path" :label="$photo->placeholder_label" fill size="sm" sizes="288px" />
                        </div>
                    @endforeach
                @endforeach
            </div>
        @endforeach
    </div>
@endif
