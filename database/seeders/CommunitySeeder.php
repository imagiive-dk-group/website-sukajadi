<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Faq;
use App\Models\Official;
use App\Models\Package;
use App\Models\Post;
use App\Models\ProfileSection;
use App\Models\ProfileStat;
use App\Models\Testimonial;
use App\Models\Umkm;
use Illuminate\Database\Seeder;

/**
 * UMKM, profil desa, galeri, berita, FAQ, dan ulasan contoh.
 * Data yang belum pasti ditandai jelas agar mudah diganti dari admin.
 */
class CommunitySeeder extends Seeder
{
    public function run(): void
    {
        $this->seedUmkm();
        $this->seedProfile();
        $this->seedAlbums();
        $this->seedPosts();
        $this->seedFaqs();
        $this->seedTestimonials();
    }

    protected function t(string $id, string $en): array
    {
        return ['id' => $id, 'en' => $en];
    }

    protected function seedUmkm(): void
    {
        $rows = [
            [
                'slug' => 'sari-buah-pala', 'slug_en' => 'nutmeg-fruit-juice',
                'name' => 'Sari Buah Pala Sukajadi', 'category' => 'minuman', 'is_featured' => true, 'accepts_visits' => true,
                'summary' => $this->t('Minuman sari buah pala segar dan berkhasiat, diolah dari buah pala pilihan petani Sukajadi.', 'Fresh, healthy nutmeg fruit juice made from nutmeg grown by Sukajadi farmers.'),
                'description' => $this->t('<p>UMKM ini mengolah buah pala segar menjadi minuman khas desa. Selain menjual produk, UMKM juga menerima kunjungan rombongan untuk belajar proses pembuatan sari buah pala.</p>', '<p>This small business turns fresh nutmeg fruit into the village’s signature drink. Besides selling its products, it welcomes groups to learn how nutmeg juice is made.</p>'),
                'placeholder_label' => 'Botol sari buah pala & buah pala segar',
                'packages' => ['edukasi-umkm'],
                'products' => [
                    ['name' => $this->t('Sari Buah Pala', 'Nutmeg Fruit Juice'), 'price_min' => 10000, 'unit' => $this->t('botol', 'bottle'), 'placeholder_label' => 'Botol sari pala 250 ml'],
                    ['name' => $this->t('Manisan Pala', 'Candied Nutmeg'), 'price_min' => 15000, 'price_max' => 25000, 'unit' => $this->t('bungkus', 'pack'), 'placeholder_label' => 'Manisan pala dalam kemasan'],
                ],
            ],
            [
                'slug' => 'kue-noga', 'slug_en' => 'noga-cakes',
                'name' => 'Kue Noga Sukajadi', 'category' => 'makanan', 'is_featured' => true, 'accepts_visits' => true,
                'summary' => $this->t('Kue tradisional noga yang lezat dan gurih, dibuat dengan resep turun-temurun.', 'Delicious, savoury traditional noga cakes made with a recipe handed down for generations.'),
                'description' => $this->t('<p>Perajin kue noga Sukajadi membuat kue setiap hari untuk dijual dan sebagai oleh-oleh wisatawan. Rombongan dapat ikut praktik membuat kue noga.</p>', '<p>Sukajadi’s noga bakers make cakes daily for sale and as souvenirs for visitors. Groups can join a hands-on noga baking session.</p>'),
                'placeholder_label' => 'Loyang berisi kue noga',
                'packages' => ['edukasi-umkm'],
                'products' => [
                    ['name' => $this->t('Kue Noga', 'Noga Cakes'), 'price_min' => 20000, 'unit' => $this->t('toples', 'jar'), 'placeholder_label' => 'Toples kue noga'],
                ],
            ],
            [
                'slug' => 'kue-tradisional', 'slug_en' => 'traditional-cakes',
                'name' => 'Kue Tradisional Sukajadi', 'category' => 'makanan', 'is_featured' => true, 'accepts_visits' => true,
                'summary' => $this->t('Aneka kue tradisional: lapis, onde-onde, dan kue talam buatan perajin lokal.', 'Traditional cakes such as lapis, onde-onde and talam made by local bakers.'),
                'description' => $this->t('<p>Kelompok perajin kue tradisional yang menyediakan kue untuk acara, oleh-oleh, dan workshop pembuatan kue bagi pengunjung.</p>', '<p>A group of traditional bakers providing cakes for events and souvenirs, plus cake-making workshops for visitors.</p>'),
                'placeholder_label' => 'Aneka kue tradisional di tampah',
                'packages' => ['budidaya-padi'],
                'products' => [
                    ['name' => $this->t('Kue Lapis', 'Lapis Cake'), 'price_min' => 2000, 'unit' => $this->t('potong', 'piece'), 'placeholder_label' => 'Kue lapis warna-warni'],
                    ['name' => $this->t('Onde-onde', 'Onde-onde'), 'price_min' => 2000, 'unit' => $this->t('buah', 'piece'), 'placeholder_label' => 'Onde-onde wijen'],
                    ['name' => $this->t('Kue Talam', 'Talam Cake'), 'price_min' => 2000, 'unit' => $this->t('potong', 'piece'), 'placeholder_label' => 'Kue talam'],
                ],
            ],
            [
                'slug' => 'tempe-sukajadi', 'slug_en' => 'sukajadi-tempeh',
                'name' => 'Tempe Sukajadi', 'category' => 'makanan', 'accepts_visits' => true,
                'summary' => $this->t('Tempe segar bungkus daun pisang, sumber protein nabati kebanggaan Indonesia.', 'Fresh tempeh wrapped in banana leaves, Indonesia’s proud plant protein.'),
                'description' => $this->t('<p>Produsen tempe rumahan yang tetap mempertahankan cara tradisional dengan bungkus daun pisang. Pengunjung dapat melihat proses fermentasi kedelai menjadi tempe.</p>', '<p>A home tempeh producer that keeps the traditional banana-leaf wrapping. Visitors can see how soybeans are fermented into tempeh.</p>'),
                'placeholder_label' => 'Tempe bungkus daun pisang',
                'packages' => ['budidaya-padi'],
                'products' => [
                    ['name' => $this->t('Tempe Daun', 'Leaf-wrapped Tempeh'), 'price_min' => 5000, 'unit' => $this->t('bungkus', 'pack'), 'placeholder_label' => 'Tempe daun pisang'],
                ],
            ],
            [
                'slug' => 'anggrek-bulan', 'slug_en' => 'moon-orchids',
                'name' => 'Kebun Anggrek Bulan Sukajadi', 'category' => 'pertanian', 'is_featured' => true, 'accepts_visits' => true,
                'summary' => $this->t('Petani anggrek bulan yang menjual tanaman anggrek berbunga dan bibit.', 'Moon orchid growers selling flowering orchids and seedlings.'),
                'description' => $this->t('<p>Kebun anggrek bulan dengan ratusan tanaman berbunga ungu, merah muda, dan putih. Melayani penjualan tanaman dan kunjungan agro eduwisata.</p>', '<p>A moon orchid nursery with hundreds of purple, pink and white flowering plants. It sells plants and welcomes agro-edutourism visits.</p>'),
                'placeholder_label' => 'Anggrek bulan berbunga di rumah kaca',
                'packages' => ['anggrek-bulan'],
                'products' => [
                    ['name' => $this->t('Anggrek Bulan Berbunga', 'Flowering Moon Orchid'), 'price_min' => 75000, 'price_max' => 150000, 'unit' => $this->t('pot', 'pot'), 'placeholder_label' => 'Anggrek bulan ungu dalam pot'],
                    ['name' => $this->t('Bibit Anggrek', 'Orchid Seedling'), 'price_min' => 25000, 'unit' => $this->t('pot', 'pot'), 'placeholder_label' => 'Bibit anggrek pot kecil'],
                ],
            ],
            [
                'slug' => 'stroberi-sukajadi', 'slug_en' => 'sukajadi-strawberries',
                'name' => 'Stroberi Sukajadi', 'category' => 'pertanian', 'is_featured' => true, 'accepts_visits' => true,
                'summary' => $this->t('Stroberi segar langsung dari kebun, bisa petik sendiri.', 'Fresh strawberries straight from the garden, pick your own.'),
                'description' => $this->t('<p>Kebun stroberi yang menjual stroberi segar dan menerima pengunjung untuk petik sendiri.</p>', '<p>A strawberry garden selling fresh strawberries and welcoming pick-your-own visitors.</p>'),
                'placeholder_label' => 'Keranjang stroberi segar',
                'packages' => ['budidaya-padi'],
                'products' => [
                    ['name' => $this->t('Stroberi Segar', 'Fresh Strawberries'), 'price_min' => 25000, 'unit' => $this->t('box 250 g', '250 g box'), 'placeholder_label' => 'Box stroberi segar'],
                ],
            ],
        ];

        $packages = Package::query()->pluck('id', 'slug');

        foreach ($rows as $sort => $row) {
            $products = $row['products'];
            $packageSlugs = $row['packages'];
            unset($row['products'], $row['packages']);

            $umkm = Umkm::create($row + [
                'owner_name' => null,
                'opening_hours' => $this->t('Setiap hari, 08.00–16.00 (perlu konfirmasi)', 'Daily, 08:00–16:00 (to be confirmed)'),
                // Kontak dikosongkan sampai pemilik UMKM memberi izin;
                // sementara tombol WA diarahkan ke admin desa.
                'whatsapp' => null,
                'show_contact' => false,
                'sort' => $sort,
            ]);

            foreach ($products as $i => $product) {
                $umkm->products()->create($product + ['sort' => $i]);
            }

            foreach (['Pemilik UMKM di tempat usaha', 'Proses produksi', 'Produk siap jual', 'Pengunjung berbelanja'] as $i => $label) {
                $umkm->photos()->create(['placeholder_label' => $row['name'].': '.$label, 'sort' => $i]);
            }

            $umkm->packages()->sync($packageSlugs ? collect($packageSlugs)->map(fn ($s) => $packages[$s]) : []);
        }
    }

    protected function seedProfile(): void
    {
        $pending = 'Menunggu data kantor desa';
        $pendingEn = 'Awaiting village office data';
        $wiki = 'Wikipedia "Tamansari, Bogor" (data BPS)';

        $stats = [
            ['identitas', $this->t('Nama desa', 'Village name'), $this->t('Sukajadi', 'Sukajadi'), null, null, false],
            ['identitas', $this->t('Kecamatan', 'District'), $this->t('Tamansari', 'Tamansari'), null, null, false],
            ['identitas', $this->t('Kabupaten', 'Regency'), $this->t('Bogor', 'Bogor'), null, null, false],
            ['identitas', $this->t('Provinsi', 'Province'), $this->t('Jawa Barat', 'West Java'), null, null, false],
            ['identitas', $this->t('Kode wilayah (Kemendagri)', 'Area code (Ministry of Home Affairs)'), $this->t('32.01.31.2008', '32.01.31.2008'), $wiki, null, true],
            ['geografi', $this->t('Luas wilayah', 'Area'), $this->t('± 3,04 km²', '± 3.04 km²'), $wiki, null, true],
            ['geografi', $this->t('Batas wilayah', 'Boundaries'), $this->t($pending, $pendingEn), null, null, true],
            ['geografi', $this->t('Ketinggian', 'Elevation'), $this->t($pending, $pendingEn), null, null, true],
            ['kependudukan', $this->t('Jumlah penduduk', 'Population'), $this->t('± 10.569 jiwa (perkiraan pertengahan 2024)', '± 10,569 people (mid-2024 estimate)'), 'BPS, Kecamatan Tamansari Dalam Angka 2025 (via Wikipedia)', '2024', true],
            ['kependudukan', $this->t('Kepadatan penduduk', 'Population density'), $this->t('± 3.477 jiwa/km² (dihitung dari data di atas)', '± 3,477 people/km² (calculated from the data above)'), null, '2024', true],
            ['kependudukan', $this->t('Jumlah kepala keluarga', 'Households'), $this->t($pending, $pendingEn), null, null, true],
            ['kependudukan', $this->t('Mata pencaharian utama', 'Main livelihoods'), $this->t('Pertanian, peternakan, UMKM, pariwisata', 'Farming, livestock, small businesses, tourism'), null, null, true],
            ['pemerintahan', $this->t('Kepala desa', 'Village head'), $this->t($pending, $pendingEn), null, null, true],
            ['pemerintahan', $this->t('Jumlah dusun / RW / RT', 'Hamlets / RW / RT'), $this->t($pending, $pendingEn), null, null, true],
            ['status', $this->t('Status kawasan', 'Area status'), $this->t('Kecamatan Tamansari termasuk Kawasan Strategis Pariwisata Kab. Bogor', 'Tamansari District is a Bogor Regency Strategic Tourism Area'), 'Perda Kab. Bogor No. 3 Tahun 2019 (perubahan RPJPD 2005–2025)', '2019', true],
            ['status', $this->t('Status Indeks Desa (Kemendesa)', 'Village Index status (Ministry of Villages)'), $this->t($pending, $pendingEn), null, null, true],
        ];

        foreach ($stats as $sort => [$group, $label, $value, $source, $year, $verify]) {
            ProfileStat::create([
                'group' => $group,
                'label' => $label,
                'value' => $value,
                'source' => $source,
                'source_year' => $year,
                'needs_verification' => $verify,
                'sort' => $sort,
            ]);
        }

        $note = '<p><em>Bagian ini menunggu data resmi dari Kantor Desa Sukajadi (profil/monografi desa dan RPJMDes).</em></p>';
        $noteEn = '<p><em>This section is awaiting official data from the Sukajadi Village Office (village profile and medium-term development plan).</em></p>';

        $sections = [
            ['sejarah', $this->t('Sejarah Desa', 'Village History'), $this->t($note, $noteEn)],
            ['visi-misi', $this->t('Visi & Misi', 'Vision & Mission'), $this->t($note, $noteEn)],
            ['letak-geografis', $this->t('Letak Geografis', 'Geography'), $this->t(
                '<p>Desa Sukajadi berada di Kecamatan Tamansari, Kabupaten Bogor, Jawa Barat. Kecamatan Tamansari terdiri dari delapan desa dan merupakan salah satu kawasan strategis pariwisata Kabupaten Bogor, dengan bentang alam perbukitan, sawah, kebun, dan air terjun.</p>'.$note,
                '<p>Sukajadi Village lies in Tamansari District, Bogor Regency, West Java. Tamansari District consists of eight villages and is one of Bogor Regency’s strategic tourism areas, with hills, rice fields, gardens and waterfalls.</p>'.$noteEn
            )],
            ['potensi', $this->t('Potensi Desa', 'Village Potential'), $this->t(
                '<ul><li><strong>Pertanian:</strong> padi, ubi jalar, stroberi, anggrek bulan</li><li><strong>Peternakan:</strong> budidaya kelinci</li><li><strong>Wisata alam:</strong> Air Terjun Curug Aman</li><li><strong>Budaya:</strong> permainan tradisional Sunda</li><li><strong>UMKM:</strong> sari buah pala, kue noga, kue tradisional, tempe</li></ul>',
                '<ul><li><strong>Farming:</strong> rice, sweet potatoes, strawberries, moon orchids</li><li><strong>Livestock:</strong> rabbit farming</li><li><strong>Nature:</strong> Curug Aman waterfall</li><li><strong>Culture:</strong> traditional Sundanese games</li><li><strong>Small businesses:</strong> nutmeg juice, noga cakes, traditional cakes, tempeh</li></ul>'
            )],
            ['kelembagaan', $this->t('Kelembagaan Desa', 'Village Institutions'), $this->t(
                '<p>Pemerintah Desa, Badan Permusyawaratan Desa (BPD), Kelompok Sadar Wisata (Pokdarwis), BUMDes, PKK, dan Karang Taruna.</p>'.$note,
                '<p>Village Government, Village Consultative Body (BPD), Tourism Awareness Group (Pokdarwis), village-owned enterprise (BUMDes), PKK women’s group and Karang Taruna youth organisation.</p>'.$noteEn
            )],
        ];

        foreach ($sections as $sort => [$key, $title, $body]) {
            ProfileSection::create(['key' => $key, 'title' => $title, 'body' => $body, 'sort' => $sort]);
        }

        $officials = [
            ['Nama Kepala Desa', $this->t('Kepala Desa', 'Village Head')],
            ['Nama Sekretaris Desa', $this->t('Sekretaris Desa', 'Village Secretary')],
            ['Nama Ketua BPD', $this->t('Ketua BPD', 'Head of Village Consultative Body')],
            ['Nama Ketua Pokdarwis', $this->t('Ketua Pokdarwis', 'Head of Tourism Awareness Group')],
        ];

        foreach ($officials as $sort => [$name, $position]) {
            Official::create(['name' => $name, 'position' => $position, 'sort' => $sort]);
        }
    }

    protected function seedAlbums(): void
    {
        $albums = [
            ['alam-curug', 'nature-waterfall', $this->t('Alam & Curug Aman', 'Nature & Curug Aman'), 'alam', ['Panorama perbukitan desa saat pagi', 'Air terjun Curug Aman', 'Jalan desa di antara sawah', 'Kabut pagi di kebun', 'Sungai kecil berbatu', 'Matahari terbenam di sawah']],
            ['edukasi-pertanian', 'farming-education', $this->t('Edukasi Pertanian', 'Farming Education'), 'edukasi', ['Menanam padi bersama petani', 'Panen ubi jalar', 'Membuat pupuk organik', 'Petik stroberi', 'Kebun anggrek bulan', 'Siswa mencatat materi']],
            ['peternakan-kelinci', 'rabbit-farm', $this->t('Peternakan Kelinci', 'Rabbit Farm'), 'edukasi', ['Kelinci di kandang kayu', 'Memberi pakan kelinci', 'Anak kelinci', 'Peternak & peserta']],
            ['budaya-tradisi', 'culture-traditions', $this->t('Budaya & Permainan Tradisional', 'Culture & Traditional Games'), 'budaya', ['Bermain egrang', 'Gasing kayu', 'Kelom geulis', 'Kesenian Sunda', 'Rumah panggung desa']],
            ['kuliner-umkm', 'food-local-business', $this->t('Kuliner & UMKM', 'Food & Local Businesses'), 'kuliner', ['Nasi liwet di daun pisang', 'Sari buah pala', 'Kue noga', 'Kue tradisional', 'Tempe daun', 'Belanja oleh-oleh']],
            ['kegiatan-rombongan', 'group-visits', $this->t('Kegiatan Rombongan', 'Group Visits'), 'kegiatan', ['Rombongan sekolah tiba di desa', 'Foto bersama di gerbang desa', 'Gathering komunitas', 'Outing perusahaan', 'Keluarga berjalan di jalan desa', 'Sesi penutupan']],
        ];

        foreach ($albums as $sort => [$slug, $slugEn, $title, $category, $photos]) {
            $album = Album::create(['slug' => $slug, 'slug_en' => $slugEn, 'title' => $title, 'category' => $category, 'sort' => $sort]);
            foreach ($photos as $i => $label) {
                $album->photos()->create(['placeholder_label' => $label, 'sort' => $i]);
            }
        }
    }

    protected function seedPosts(): void
    {
        $sample = '<p><em>Artikel contoh. Ganti dengan berita kegiatan asli dari panel admin.</em></p>';
        $sampleEn = '<p><em>Sample article. Replace it with real news from the admin panel.</em></p>';

        $posts = [
            [
                'slug' => 'website-resmi-desa-wisata-sukajadi', 'slug_en' => 'official-website-launch',
                'title' => $this->t('Website Resmi Desa Wisata Sukajadi Resmi Diluncurkan', 'Sukajadi Tourism Village Launches Its Official Website'),
                'excerpt' => $this->t('Kini pengunjung bisa melihat paket wisata, galeri, dan UMKM desa, lalu booking langsung secara online.', 'Visitors can now browse packages, the gallery and local businesses, and book online.'),
                'body' => $this->t('<p>Desa Wisata Sukajadi kini memiliki website resmi di desawisatasukajadi.com. Melalui website ini, sekolah, komunitas, instansi, dan keluarga dapat melihat seluruh paket wisata, galeri foto kegiatan, profil desa, serta direktori UMKM Sukajadi.</p><p>Reservasi dapat dilakukan langsung melalui formulir booking, lalu dilanjutkan dengan konfirmasi melalui WhatsApp.</p>'.$sample, '<p>Sukajadi Tourism Village now has an official website at desawisatasukajadi.com. Schools, communities, companies and families can browse all tour packages, the photo gallery, the village profile and the directory of local businesses.</p><p>Reservations can be made through the booking form and then confirmed via WhatsApp.</p>'.$sampleEn),
                'placeholder_label' => 'Gerbang Desa Wisata Sukajadi',
                'published_at' => now()->subDays(2),
            ],
            [
                'slug' => 'mengenal-kue-noga', 'slug_en' => 'getting-to-know-noga-cakes',
                'title' => $this->t('Mengenal Kue Noga, Camilan Tradisional dari Sukajadi', 'Getting to Know Noga, a Traditional Snack from Sukajadi'),
                'excerpt' => $this->t('Kue noga menjadi salah satu oleh-oleh favorit pengunjung Desa Wisata Sukajadi.', 'Noga cakes are one of the favourite souvenirs for visitors to Sukajadi.'),
                'body' => $this->t('<p>Kue noga dibuat oleh perajin lokal dengan resep turun-temurun. Dalam Paket Edukasi UMKM, pengunjung dapat ikut membuat kue noga dan membawa pulang hasilnya.</p>'.$sample, '<p>Noga cakes are made by local bakers using a recipe handed down through generations. In the Local Business Workshop Package, visitors can help bake noga cakes and take them home.</p>'.$sampleEn),
                'placeholder_label' => 'Perajin kue noga di dapur produksi',
                'published_at' => now()->subDays(9),
            ],
            [
                'slug' => 'panen-ubi-jalar-bersama-pelajar', 'slug_en' => 'sweet-potato-harvest-with-students',
                'title' => $this->t('Serunya Panen Ubi Jalar Bersama Pelajar', 'Harvesting Sweet Potatoes with Students'),
                'excerpt' => $this->t('Pelajar belajar membuat pupuk organik, menanam, dan memanen ubi jalar langsung di kebun.', 'Students learned to make compost, plant and harvest sweet potatoes in the field.'),
                'body' => $this->t('<p>Paket Edukasi Pertanian Ubi Jalar mengajak pelajar mengenal pertanian ramah lingkungan dari hulu ke hilir, ditutup dengan makan siang nasi liwet bersama.</p>'.$sample, '<p>The Sweet Potato Farming Package introduces students to eco-friendly farming from start to finish, ending with a shared nasi liwet lunch.</p>'.$sampleEn),
                'placeholder_label' => 'Pelajar memamerkan ubi jalar hasil panen',
                'published_at' => now()->subDays(20),
            ],
        ];

        foreach ($posts as $post) {
            Post::create($post + ['is_published' => true]);
        }
    }

    protected function seedFaqs(): void
    {
        $faqs = [
            ['booking', $this->t('Bagaimana cara memesan paket wisata?', 'How do I book a tour package?'), $this->t('Pilih paket di halaman Paket Wisata, klik "Booking", isi formulir, lalu kirim. Anda akan mendapat kode booking dan tombol untuk melanjutkan konfirmasi melalui WhatsApp admin.', 'Choose a package on the Packages page, click "Book", fill in the form and submit. You will receive a booking code and a button to continue the confirmation with our admin on WhatsApp.')],
            ['booking', $this->t('Berapa minimal peserta?', 'What is the minimum group size?'), $this->t('Setiap paket memiliki minimal peserta yang berbeda, misalnya 15 pax untuk Paket Kelinci dan 30 pax untuk Paket Ubi Jalar dan Padi. Untuk rombongan lebih kecil, silakan hubungi kami melalui WhatsApp.', 'Each package has its own minimum, e.g. 15 pax for the Rabbit Package and 30 pax for the Sweet Potato and Rice packages. For smaller groups, please contact us on WhatsApp.')],
            ['booking', $this->t('Berapa hari sebelumnya saya harus booking?', 'How far in advance should I book?'), $this->t('Booking paling lambat 3 hari sebelum tanggal kunjungan agar kami dapat menyiapkan pemandu, makanan, dan kegiatan.', 'Please book at least 3 days before your visit so we can prepare guides, meals and activities.')],
            ['pembayaran', $this->t('Bagaimana cara pembayarannya?', 'How do I pay?'), $this->t('Pembayaran dan ketentuan lainnya akan dijelaskan oleh admin melalui WhatsApp setelah booking Anda kami terima.', 'Payment and other terms will be explained by our admin via WhatsApp once we receive your booking.')],
            ['kunjungan', $this->t('Apa saja yang perlu dibawa?', 'What should I bring?'), $this->t('Pakaian nyaman yang boleh kotor, sepatu/sandal outdoor, topi, botol minum, obat pribadi, dan baju ganti untuk kegiatan di sawah, kebun, atau curug.', 'Comfortable clothes that can get dirty, outdoor shoes or sandals, a hat, a water bottle, personal medication and a change of clothes for activities in the fields, gardens or waterfall.')],
            ['kunjungan', $this->t('Apakah cocok untuk anak-anak?', 'Is it suitable for children?'), $this->t('Sangat cocok. Sebagian besar kegiatan dirancang untuk pelajar TK hingga SMA, dengan pendampingan pemandu lokal.', 'Absolutely. Most activities are designed for kindergarten to high school students, accompanied by local guides.')],
            ['kunjungan', $this->t('Apakah paket bisa disesuaikan?', 'Can packages be customised?'), $this->t('Bisa. Gunakan halaman Paket Custom untuk memilih sendiri aktivitas yang diinginkan, atau diskusikan kebutuhan khusus rombongan Anda melalui WhatsApp.', 'Yes. Use the Custom Package page to pick your own activities, or discuss your group’s special needs with us on WhatsApp.')],
            ['lokasi', $this->t('Di mana lokasi Desa Wisata Sukajadi?', 'Where is Sukajadi Tourism Village?'), $this->t('Desa Sukajadi berada di Kecamatan Tamansari, Kabupaten Bogor, Jawa Barat. Lihat peta di halaman Kontak untuk petunjuk arah.', 'Sukajadi Village is in Tamansari District, Bogor Regency, West Java. See the map on the Contact page for directions.')],
            ['lokasi', $this->t('Apakah tersedia area parkir bus?', 'Is there parking for buses?'), $this->t('Mohon informasikan jenis dan jumlah kendaraan saat booking agar kami dapat mengatur lokasi parkir dan transportasi di area desa.', 'Please tell us the type and number of vehicles when booking so we can arrange parking and transport within the village.')],
        ];

        foreach ($faqs as $sort => [$category, $question, $answer]) {
            Faq::create(['category' => $category, 'question' => $question, 'answer' => $answer, 'sort' => $sort]);
        }
    }

    protected function seedTestimonials(): void
    {
        $rows = [
            ['Contoh Ulasan Sekolah', 'Ulasan contoh — ganti dengan ulasan asli', 'Anak-anak sangat antusias memberi makan kelinci dan bermain egrang. Pemandunya ramah dan sabar.'],
            ['Contoh Ulasan Komunitas', 'Ulasan contoh — ganti dengan ulasan asli', 'Panen ubi jalar lalu makan nasi liwet bareng, pengalaman yang seru dan berkesan!'],
            ['Contoh Ulasan Keluarga', 'Ulasan contoh — ganti dengan ulasan asli', 'Udara sejuk, Curug Aman indah, dan oleh-oleh sari buah palanya segar sekali.'],
        ];

        foreach ($rows as [$name, $org, $content]) {
            Testimonial::create([
                'name' => $name,
                'organization' => $org,
                'content' => $content,
                'rating' => 5,
                'is_approved' => true,
                'is_featured' => true,
            ]);
        }
    }
}
