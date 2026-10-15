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
            'story_title' => [
                'id' => 'Cerita dari Sukajadi',
                'en' => 'Stories from Sukajadi',
            ],
            'story_body' => [
                'id' => '<p>Di Kecamatan Tamansari, di antara perbukitan hijau yang mengarah ke Gunung Salak, Desa Sukajadi hidup dari tanahnya. Pagi dimulai di sawah dan kebun: padi, ubi jalar, stroberi, hingga anggrek bulan yang dirawat dengan sabar. Di halaman rumah, warga beternak kelinci dan mengolah buah pala menjadi minuman segar.</p><p>Anak-anak masih bermain egrang dan gasing di lapangan desa, dapur-dapur menyiapkan kue noga dan nasi liwet, dan air terjun Curug Aman mengalir di balik rimbunnya hutan. Semua itu kini kami buka untuk tamu yang ingin belajar, berjalan pelan, dan pulang membawa cerita.</p><p><em>Draf cerita, silakan disesuaikan oleh penggiat desa wisata.</em></p>',
                'en' => '<p>In Tamansari, among green hills rising towards Mount Salak, Sukajadi Village lives from its land. Mornings begin in the rice fields and gardens: rice, sweet potatoes, strawberries and moon orchids tended with patience. In their yards, families raise rabbits and turn nutmeg fruit into a refreshing drink.</p><p>Children still play on bamboo stilts and spin tops in the village field, kitchens prepare noga cakes and nasi liwet, and Curug Aman waterfall flows behind the forest. We now open all of this to guests who want to learn, slow down and go home with a story.</p><p><em>Draft story, to be adjusted by the tourism village team.</em></p>',
            ],
            'managed_by' => [
                'id' => 'Dikelola oleh penggiat Desa Wisata Sukajadi',
                'en' => 'Run by the Sukajadi tourism village team',
            ],
            'hero_video' => null,
            'hero_slides' => null,
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
            'partner_min_lead_days' => 1,
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
