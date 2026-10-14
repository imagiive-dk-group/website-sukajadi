<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Gambar placeholder SVG berlabel (dipakai lightbox galeri & og:image)
 * selama foto asli belum diunggah.
 */
class PlaceholderController extends Controller
{
    public function __invoke(Request $request)
    {
        $w = min(2400, max(100, $request->integer('w', 1600)));
        $h = min(2400, max(100, $request->integer('h', 1000)));
        $label = e(mb_strimwidth((string) $request->query('label', ''), 0, 90, '…'));
        $title = e(__('site.common.photo'));
        $cx = $w / 2;
        $cy = $h / 2;
        $fs = max(18, round($w / 34));

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$w}" height="{$h}" viewBox="0 0 {$w} {$h}">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#d7ecd2"/><stop offset="1" stop-color="#cfeef4"/></linearGradient>
    <pattern id="p" width="28" height="28" patternUnits="userSpaceOnUse" patternTransform="rotate(45)"><line x1="0" y1="0" x2="0" y2="28" stroke="#277721" stroke-opacity=".06" stroke-width="10"/></pattern>
  </defs>
  <rect width="100%" height="100%" fill="url(#g)"/><rect width="100%" height="100%" fill="url(#p)"/>
  <g transform="translate({$cx} {$cy})" fill="none" stroke="#277721" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" opacity=".75">
    <path d="M-48 -70 h28 l10 -14 h20 l10 14 h28 a10 10 0 0 1 10 10 v52 a10 10 0 0 1 -10 10 h-96 a10 10 0 0 1 -10 -10 v-52 a10 10 0 0 1 10 -10z"/>
    <circle cx="0" cy="-35" r="20"/>
  </g>
  <text x="{$cx}" y="{$cy}" dy="{$fs}" text-anchor="middle" font-family="Poppins, Arial, sans-serif" font-weight="700" font-size="{$fs}" letter-spacing="4" fill="#1b4d17">{$title}</text>
  <text x="{$cx}" y="{$cy}" dy="{$fs}" text-anchor="middle" font-family="Inter, Arial, sans-serif" font-size="{$fs}" fill="#1b4d17" transform="translate(0 {$fs}) translate(0 12)">{$label}</text>
</svg>
SVG;

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
