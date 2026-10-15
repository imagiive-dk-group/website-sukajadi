<?php

namespace Database\Seeders;

use App\Models\Partner;
use Illuminate\Database\Seeder;

/**
 * Contoh mitra bisnis (dijual dengan komisi). Semua harga & detail masih contoh
 * dan ditandai "harga sementara" sampai ada perjanjian kerja sama dengan mitra.
 */
class PartnerSeeder extends Seeder
{
    protected function t(string $id, string $en): array
    {
        return ['id' => $id, 'en' => $en];
    }

    public function run(): void
    {
        $note = $this->t(
            '<p><em>Data contoh. Detail, foto, dan harga akan diperbarui setelah kerja sama dengan mitra disepakati.</em></p>',
            '<p><em>Sample listing. Details, photos and prices will be updated once the partnership is agreed.</em></p>'
        );

        $rows = [
            [
                'slug' => 'curug-nangka', 'slug_en' => 'curug-nangka-waterfall',
                'name' => 'Curug Nangka', 'category' => 'wisata', 'is_featured' => true,
                'summary' => $this->t('Air terjun di kawasan Tamansari yang dikelilingi hutan, cocok untuk trekking ringan dan bermain air.', 'A forest waterfall in the Tamansari area, great for light trekking and splashing around.'),
                'description' => $this->t('<p>Curug Nangka adalah salah satu air terjun populer di kawasan Tamansari. Perjalanan menuju curug melewati jalur hutan yang sejuk.</p>', '<p>Curug Nangka is one of the popular waterfalls in the Tamansari area, reached by a cool forest trail.</p>'),
                'highlights' => [$this->t('Trekking hutan', 'Forest trekking'), $this->t('Air terjun', 'Waterfall'), $this->t('Cocok untuk rombongan', 'Good for groups')],
                'placeholder_label' => 'Air terjun Curug Nangka di tengah hutan',
                'offers' => [
                    ['name' => $this->t('Tiket masuk', 'Entrance ticket'), 'price' => 20000, 'unit' => 'per_ticket', 'min_qty' => 1],
                    ['name' => $this->t('Tiket + pemandu trekking (rombongan)', 'Ticket + trekking guide (groups)'), 'price' => 45000, 'unit' => 'per_pax', 'min_qty' => 10],
                ],
            ],
            [
                'slug' => 'highland-tamansari', 'slug_en' => 'highland-tamansari',
                'name' => 'Highland Tamansari', 'category' => 'wisata', 'is_featured' => true,
                'summary' => $this->t('Kawasan wisata dataran tinggi dengan pemandangan pegunungan, glamping, dan aktivitas alam.', 'A highland destination with mountain views, glamping and outdoor activities.'),
                'description' => $this->t('<p>Highland Tamansari menawarkan suasana pegunungan yang sejuk untuk berlibur bersama keluarga maupun rombongan, mulai dari menikmati pemandangan hingga menginap di glamping.</p>', '<p>Highland Tamansari offers cool mountain air for family and group getaways, from enjoying the views to staying overnight in glamping tents.</p>'),
                'highlights' => [$this->t('Pemandangan pegunungan', 'Mountain views'), $this->t('Glamping', 'Glamping'), $this->t('Outbound & gathering', 'Outbound & gatherings')],
                'placeholder_label' => 'Panorama Highland Tamansari & tenda glamping',
                'offers' => [
                    ['name' => $this->t('Tiket masuk kawasan', 'Area entrance ticket'), 'price' => 35000, 'unit' => 'per_ticket', 'min_qty' => 1],
                    ['name' => $this->t('Glamping (2–4 orang)', 'Glamping (2–4 guests)'), 'price' => 850000, 'unit' => 'per_night', 'min_qty' => 1],
                    ['name' => $this->t('Paket outbound rombongan', 'Group outbound package'), 'price' => 150000, 'unit' => 'per_pax', 'min_qty' => 20],
                ],
            ],
            [
                'slug' => 'vila-contoh-sukajadi', 'slug_en' => 'sample-villa-sukajadi',
                'name' => 'Vila Contoh Sukajadi', 'category' => 'penginapan', 'is_featured' => true,
                'summary' => $this->t('Vila keluarga dengan halaman luas dan udara sejuk, dekat lokasi wisata desa.', 'A family villa with a large garden and cool air, close to the village attractions.'),
                'description' => $this->t('<p>Vila untuk keluarga atau rombongan kecil yang ingin menginap setelah berkegiatan di desa wisata.</p>', '<p>A villa for families or small groups who want to stay overnight after a day in the tourism village.</p>'),
                'highlights' => [$this->t('3 kamar tidur', '3 bedrooms'), $this->t('Dapur & area BBQ', 'Kitchen & BBQ area'), $this->t('Parkir mobil', 'Car parking')],
                'placeholder_label' => 'Vila dengan halaman rumput & pemandangan bukit',
                'offers' => [
                    ['name' => $this->t('Sewa vila (maks. 10 orang)', 'Villa rental (max. 10 guests)'), 'price' => 1500000, 'unit' => 'per_night', 'min_qty' => 1],
                ],
            ],
            [
                'slug' => 'homestay-warga', 'slug_en' => 'village-homestay',
                'name' => 'Homestay Warga Sukajadi', 'category' => 'penginapan',
                'summary' => $this->t('Menginap di rumah warga dan merasakan keseharian desa.', 'Stay with a local family and experience everyday village life.'),
                'description' => $this->t('<p>Homestay sederhana dan bersih bersama keluarga warga, cocok untuk pelajar dan wisatawan yang ingin pengalaman otentik.</p>', '<p>Simple, clean homestays with local families, ideal for students and travellers looking for an authentic experience.</p>'),
                'highlights' => [$this->t('Sarapan khas desa', 'Village breakfast'), $this->t('Tuan rumah warga lokal', 'Local host family')],
                'placeholder_label' => 'Kamar homestay rumah warga',
                'offers' => [
                    ['name' => $this->t('Kamar homestay + sarapan', 'Homestay room + breakfast'), 'price' => 250000, 'unit' => 'per_night', 'min_qty' => 1],
                ],
            ],
            [
                'slug' => 'kafe-contoh-tamansari', 'slug_en' => 'sample-cafe-tamansari',
                'name' => 'Kafe Contoh Tamansari', 'category' => 'kafe', 'is_featured' => true,
                'summary' => $this->t('Kafe dengan pemandangan sawah dan menu kopi lokal.', 'A café overlooking the rice fields with local coffee.'),
                'description' => $this->t('<p>Tempat bersantai setelah berkegiatan, menyajikan kopi lokal dan menu ringan. Bisa reservasi untuk rombongan.</p>', '<p>A place to unwind after your activities, serving local coffee and light meals. Group reservations available.</p>'),
                'highlights' => [$this->t('Pemandangan sawah', 'Rice field views'), $this->t('Kopi lokal', 'Local coffee'), $this->t('Reservasi rombongan', 'Group reservations')],
                'placeholder_label' => 'Kafe dengan meja menghadap sawah',
                'offers' => [
                    ['name' => $this->t('Paket makan rombongan', 'Group meal package'), 'price' => 55000, 'unit' => 'per_pax', 'min_qty' => 10],
                ],
            ],
        ];

        foreach ($rows as $sort => $row) {
            $offers = $row['offers'];
            unset($row['offers']);

            $partner = Partner::create($row + [
                'description' => $row['description'],
                'commission_rate' => 10,
                'agreement_status' => 'draft',
                'opening_hours' => $this->t('Setiap hari (perlu konfirmasi)', 'Daily (to be confirmed)'),
                'sort' => $sort,
            ]);
            $partner->update(['description' => [
                'id' => $row['description']['id'].$note['id'],
                'en' => $row['description']['en'].$note['en'],
            ]]);

            foreach ($offers as $i => $offer) {
                $partner->offers()->create($offer + ['is_dummy_price' => true, 'sort' => $i, 'placeholder_label' => $row['placeholder_label']]);
            }

            foreach (['Suasana utama', 'Fasilitas', 'Aktivitas pengunjung', 'Pemandangan sekitar'] as $i => $label) {
                $partner->photos()->create(['placeholder_label' => $row['name'].': '.$label, 'sort' => $i]);
            }
        }
    }
}
