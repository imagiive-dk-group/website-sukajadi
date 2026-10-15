<?php

namespace App\Http\Controllers;

use App\Models\WaClick;
use Illuminate\Http\Request;

/**
 * Mencatat klik tombol WhatsApp (untuk monitoring minat pengunjung),
 * lalu meneruskan ke wa.me. Tujuan selalu wa.me, jadi tidak bisa
 * dipakai untuk mengalihkan ke situs lain.
 */
class WhatsappController extends Controller
{
    public const CONTEXTS = ['general', 'package', 'attraction', 'umkm', 'product', 'partner', 'offer', 'booking'];

    public function __invoke(Request $request)
    {
        $type = in_array($request->query('c'), self::CONTEXTS, true) ? $request->query('c') : 'general';
        $number = wa_number((string) $request->query('n')) ?: wa_number(setting('whatsapp'));
        $text = mb_substr((string) $request->query('t'), 0, 1500);

        WaClick::create([
            'context_type' => $type,
            'context_id' => $request->integer('i') ?: null,
            'page' => mb_substr((string) $request->headers->get('referer'), 0, 255) ?: null,
            'locale' => in_array($request->query('l'), ['id', 'en'], true) ? $request->query('l') : 'id',
        ]);

        return redirect()->away('https://wa.me/'.$number.($text !== '' ? '?text='.rawurlencode($text) : ''));
    }
}
