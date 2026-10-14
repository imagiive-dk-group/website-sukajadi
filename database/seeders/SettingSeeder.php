<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'site_name' => 'Desa Wisata Sukajadi',
            'tagline' => ['id' => 'Belajar • Berwisata • Berdaya', 'en' => 'Learn • Explore • Empower'],
            'motto' => ['id' => 'Wisata Ramah! Eta Pisan!', 'en' => 'Friendly tourism, truly Sundanese!'],
            'hero_title' => ['id' => 'Desa Wisata Sukajadi', 'en' => 'Sukajadi Tourism Village'],
            'hero_subtitle' => [
                'id' => 'Nikmati pengalaman seru dan edukatif di desa yang asri, ramah, dan penuh inspirasi di Kecamatan Tamansari, Bogor.',
                'en' => 'Enjoy fun, hands-on learning experiences in a green, friendly and inspiring village in Tamansari, Bogor.',
            ],
            'about_intro' => [
                'id' => 'Desa Wisata Sukajadi adalah desa wisata edukasi di Kecamatan Tamansari, Kabupaten Bogor. Di sini pengunjung belajar langsung dari petani, peternak, dan pelaku UMKM, sambil menikmati alam pegunungan, budaya Sunda, dan kuliner khas desa.',
                'en' => 'Sukajadi Tourism Village is an educational village destination in Tamansari, Bogor Regency. Visitors learn directly from local farmers, breeders and small producers while enjoying the mountain scenery, Sundanese culture and village cuisine.',
            ],
            'about_body' => [
                'id' => '<p>Desa Wisata Sukajadi dikelola oleh Kelompok Sadar Wisata (Pokdarwis) bersama warga desa. Setiap paket wisata dirancang agar pengunjung tidak hanya melihat, tetapi ikut <strong>belajar, mencoba, dan merasakan</strong> kehidupan desa: memberi makan kelinci, menanam dan memanen ubi jalar, memetik stroberi, membuat kue tradisional, hingga bermain permainan tradisional Sunda.</p><p>Setiap kunjungan juga membantu menggerakkan ekonomi warga, mulai dari pemandu lokal, petani, peternak, hingga UMKM yang menyiapkan makanan dan oleh-oleh khas desa.</p><p><em>Teks ini adalah draf awal. Silakan sesuaikan dari menu Pengaturan di panel admin.</em></p>',
                'en' => '<p>Sukajadi Tourism Village is managed by the local tourism awareness group (Pokdarwis) together with the villagers. Every package is designed so visitors do not just look, but <strong>learn, try and experience</strong> village life: feeding rabbits, planting and harvesting sweet potatoes, picking strawberries, making traditional cakes and playing traditional Sundanese games.</p><p>Every visit also supports the local economy, from village guides, farmers and breeders to the small businesses preparing meals and local souvenirs.</p><p><em>This is a draft text. Please adjust it from the Settings menu in the admin panel.</em></p>',
            ],
            'whatsapp' => '+62 857-7519-4575',
            'instagram' => 'desawisatasukajadi',
            'email' => 'info@desawisatasukajadi.com',
            'address' => [
                'id' => 'Desa Sukajadi, Kec. Tamansari, Kab. Bogor, Jawa Barat',
                'en' => 'Sukajadi Village, Tamansari District, Bogor Regency, West Java',
            ],
            'opening_hours' => [
                'id' => 'Setiap hari, 08.00–17.00 WIB (dengan reservasi)',
                'en' => 'Daily, 08:00–17:00 WIB (by reservation)',
            ],
            'maps_embed_url' => 'https://www.google.com/maps?q=Desa+Sukajadi,+Tamansari,+Bogor,+Jawa+Barat&output=embed',
            'maps_link' => 'https://www.google.com/maps/search/?api=1&query=Desa+Sukajadi+Tamansari+Bogor',
            'min_lead_days' => 3,
            'max_advance_days' => 365,
            'daily_capacity' => null,
            'custom_min_pax' => 15,
            'custom_base_price' => 35000,
            'notification_email' => null,
            'ga_measurement_id' => null,
        ];

        foreach ($settings as $key => $value) {
            Setting::put($key, $value);
        }
    }
}
