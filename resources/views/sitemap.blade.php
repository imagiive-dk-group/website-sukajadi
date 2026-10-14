{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
@foreach($entries as $entry)
@foreach(['id', 'en'] as $loc)
    <url>
        <loc>{{ $entry[$loc] }}</loc>
        <xhtml:link rel="alternate" hreflang="id" href="{{ $entry['id'] }}"/>
        <xhtml:link rel="alternate" hreflang="en" href="{{ $entry['en'] }}"/>
@if($entry['updated'])
        <lastmod>{{ $entry['updated']->toAtomString() }}</lastmod>
@endif
    </url>
@endforeach
@endforeach
</urlset>
