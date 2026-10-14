<?php

namespace Database\Seeders;

use App\Models\Addon;
use App\Models\Attraction;
use App\Models\Package;
use Illuminate\Database\Seeder;

/**
 * Destinasi, paket wisata, dan add-on dari brosur Desa Wisata Sukajadi.
 * Harga bertanda is_dummy_price masih sementara dan perlu diganti dari admin.
 */
class TourismSeeder extends Seeder
{
    public function run(): void
    {
        $attractions = $this->seedAttractions();
        $this->seedPackages($attractions);
        $this->seedAddons();
    }

    protected function t(string $id, string $en): array
    {
        return ['id' => $id, 'en' => $en];
    }

    /** @return array<string, Attraction> */
    protected function seedAttractions(): array
    {
        $rows = [
            'curug-aman' => [
                'slug_en' => 'curug-aman-waterfall',
                'name' => $this->t('Air Terjun Curug Aman', 'Curug Aman Waterfall'),
                'category' => 'alam',
                'custom_price' => 15000,
                'is_featured' => true,
                'summary' => $this->t(
                    'Segarkan diri di keindahan air terjun Curug Aman yang alami, dikelilingi hutan hijau yang asri.',
                    'Refresh yourself at the natural Curug Aman waterfall, surrounded by lush green forest.'
                ),
                'description' => $this->t(
                    '<p>Curug Aman adalah air terjun alami di Desa Sukajadi. Perjalanan menuju curug melewati kebun dan jalan setapak desa, cocok untuk wisata alam dan petualangan ringan bersama keluarga maupun rombongan.</p><p>Di sini pengunjung dapat bersantai, menikmati udara sejuk pegunungan, dan berfoto dengan latar air terjun.</p>',
                    '<p>Curug Aman is a natural waterfall in Sukajadi Village. The walk to the waterfall passes village gardens and footpaths, perfect for nature trips and light adventures with family or groups.</p><p>Visitors can relax, enjoy the cool mountain air and take photos with the waterfall as a backdrop.</p>'
                ),
                'placeholder_label' => 'Air terjun Curug Aman, tampak depan',
                'photos' => ['Curug Aman dari kejauhan', 'Pengunjung bermain air di kolam curug', 'Jalan setapak menuju curug', 'Detail aliran air & bebatuan'],
            ],
            'peternakan-kelinci' => [
                'slug_en' => 'rabbit-farm',
                'name' => $this->t('Peternakan Kelinci', 'Rabbit Farm'),
                'category' => 'edukasi',
                'custom_price' => 25000,
                'is_featured' => true,
                'summary' => $this->t(
                    'Belajar budidaya kelinci langsung dari peternak berpengalaman: pakan, kandang, hingga perkembangbiakan.',
                    'Learn rabbit farming directly from experienced breeders: feeding, housing and breeding.'
                ),
                'description' => $this->t(
                    '<p>Di peternakan kelinci, pengunjung mengenal jenis dan karakter kelinci, belajar memilih pakan sehat, menjaga kebersihan kandang, memahami proses perkembangbiakan, hingga pemanfaatan kelinci sebagai sumber pangan dan ekonomi.</p>',
                    '<p>At the rabbit farm, visitors get to know rabbit breeds and their behaviour, learn to choose healthy feed, keep the hutches clean, understand breeding, and see how rabbits support food and the local economy.</p>'
                ),
                'placeholder_label' => 'Anak-anak memberi makan kelinci di kandang',
                'photos' => ['Kelinci putih di kandang kayu', 'Peternak menjelaskan jenis kelinci', 'Peserta memberi pakan sayuran', 'Anak kelinci di sarang'],
            ],
            'kebun-stroberi' => [
                'slug_en' => 'strawberry-garden',
                'name' => $this->t('Kebun Stroberi', 'Strawberry Garden'),
                'category' => 'edukasi',
                'custom_price' => 30000,
                'is_featured' => true,
                'summary' => $this->t(
                    'Rasakan sensasi memetik stroberi segar langsung dari kebun dan bawa pulang hasil petikanmu.',
                    'Pick fresh strawberries straight from the garden and take your harvest home.'
                ),
                'description' => $this->t(
                    '<p>Belajar budidaya stroberi di greenhouse, mulai dari perawatan tanaman hingga cara memetik buah yang matang. Wisata edukasi agrikultur yang seru untuk anak-anak maupun dewasa.</p>',
                    '<p>Learn how strawberries are grown in the greenhouse, from plant care to picking ripe fruit. A fun agricultural learning experience for kids and adults alike.</p>'
                ),
                'placeholder_label' => 'Keluarga memetik stroberi di greenhouse',
                'photos' => ['Deretan tanaman stroberi di greenhouse', 'Tangan memetik stroberi merah', 'Keranjang hasil panen stroberi', 'Anak tersenyum memegang stroberi'],
            ],
            'kebun-anggrek-bulan' => [
                'slug_en' => 'moon-orchid-garden',
                'name' => $this->t('Kebun Anggrek Bulan', 'Moon Orchid Garden'),
                'category' => 'edukasi',
                'custom_price' => 20000,
                'is_featured' => true,
                'summary' => $this->t(
                    'Agro eduwisata anggrek bulan: belajar budidaya dan perkembangbiakan langsung dari petani.',
                    'Moon orchid agro-edutourism: learn cultivation and propagation directly from the growers.'
                ),
                'description' => $this->t(
                    '<p>Ratusan anggrek bulan (Phalaenopsis) berwarna ungu, merah muda, dan putih tumbuh di kebun ini. Pengunjung belajar cara budidaya, melihat proses perkembangbiakan anggrek, dan dapat membeli tanaman untuk dibawa pulang.</p>',
                    '<p>Hundreds of purple, pink and white moon orchids (Phalaenopsis) grow in this nursery. Visitors learn how they are cultivated, see how orchids are propagated, and can buy plants to take home.</p>'
                ),
                'placeholder_label' => 'Deretan anggrek bulan ungu & putih di kebun',
                'photos' => ['Rumah kaca kebun anggrek', 'Petani menjelaskan budidaya anggrek', 'Close-up bunga anggrek bulan', 'Bibit anggrek dalam pot kecil'],
            ],
            'sawah-padi' => [
                'slug_en' => 'rice-fields',
                'name' => $this->t('Sawah & Budidaya Padi', 'Rice Fields & Rice Farming'),
                'category' => 'edukasi',
                'custom_price' => 25000,
                'is_featured' => true,
                'summary' => $this->t(
                    'Turun ke sawah, belajar menanam padi bersama petani, dan menikmati pemandangan terasering hijau.',
                    'Step into the paddy, plant rice with local farmers and enjoy the green terraced views.'
                ),
                'description' => $this->t(
                    '<p>Pengunjung diajak mengenal siklus budidaya padi, mulai dari menyemai, menanam bibit di sawah, hingga merawat tanaman. Pengalaman langsung yang mengajarkan dari mana nasi di piring kita berasal.</p>',
                    '<p>Visitors learn the rice growing cycle, from seeding and transplanting seedlings in the paddy to caring for the plants. A hands-on lesson in where the rice on our plates comes from.</p>'
                ),
                'placeholder_label' => 'Rombongan menanam padi di sawah',
                'photos' => ['Hamparan sawah hijau desa', 'Peserta menanam bibit padi', 'Petani & caping di tengah sawah', 'Terasering sawah dari atas'],
            ],
            'kebun-ubi-jalar' => [
                'slug_en' => 'sweet-potato-farm',
                'name' => $this->t('Kebun Ubi Jalar', 'Sweet Potato Farm'),
                'category' => 'edukasi',
                'custom_price' => 25000,
                'summary' => $this->t(
                    'Belajar membuat pupuk organik, menanam, dan memanen ubi jalar langsung dari kebunnya.',
                    'Learn to make organic compost, then plant and harvest sweet potatoes right from the field.'
                ),
                'description' => $this->t(
                    '<p>Kebun ubi jalar menjadi kelas terbuka tentang pertanian ramah lingkungan: membuat pupuk organik dari bahan alami, menanam stek ubi, merawat tanaman, hingga serunya panen ubi jalar ungu.</p>',
                    '<p>The sweet potato farm is an open-air classroom on eco-friendly farming: making organic compost from natural materials, planting cuttings, caring for the crop, and the fun of harvesting purple sweet potatoes.</p>'
                ),
                'placeholder_label' => 'Peserta memanen ubi jalar ungu',
                'photos' => ['Membuat pupuk organik di tong', 'Menanam stek ubi jalar', 'Hasil panen ubi jalar', 'Peserta tertawa memegang ubi'],
            ],
            'sari-buah-pala' => [
                'slug_en' => 'nutmeg-juice-making',
                'name' => $this->t('Edukasi Sari Buah Pala', 'Nutmeg Fruit Juice Workshop'),
                'category' => 'kuliner',
                'custom_price' => 20000,
                'summary' => $this->t(
                    'Belajar dan praktik mengolah buah pala segar menjadi minuman khas yang segar dan berkhasiat.',
                    'Learn to turn fresh nutmeg fruit into a refreshing, healthy local drink.'
                ),
                'description' => $this->t(
                    '<p>Bersama pelaku UMKM, peserta mempraktikkan proses pembuatan sari buah pala: memilih dan mengupas buah, merebus, hingga mengemas minuman siap saji.</p>',
                    '<p>Together with local producers, participants practise making nutmeg fruit juice: choosing and peeling the fruit, boiling, and bottling the ready-to-drink juice.</p>'
                ),
                'placeholder_label' => 'Proses merebus buah pala di panci besar',
                'photos' => ['Buah pala segar', 'Peserta mengupas buah pala', 'Panci rebusan sari pala', 'Botol sari pala siap jual'],
            ],
            'kue-noga' => [
                'slug_en' => 'noga-cake-making',
                'name' => $this->t('Edukasi Kue Noga', 'Noga Cake Workshop'),
                'category' => 'kuliner',
                'custom_price' => 20000,
                'summary' => $this->t(
                    'Rahasia resep tradisional dan cara praktis membuat kue noga yang lezat dan gurih.',
                    'Discover the traditional recipe and easy steps to make delicious, savoury noga cakes.'
                ),
                'description' => $this->t(
                    '<p>Kue noga adalah kue tradisional yang dibuat oleh perajin lokal Sukajadi. Peserta belajar menguleni adonan, mencetak, hingga memanggang kue noga bersama perajinnya.</p>',
                    '<p>Noga is a traditional cake made by local Sukajadi bakers. Participants learn to knead, shape and bake noga cakes together with the bakers.</p>'
                ),
                'placeholder_label' => 'Peserta mencetak adonan kue noga',
                'photos' => ['Menguleni adonan kue noga', 'Loyang berisi kue noga', 'Kue noga matang siap dikemas'],
            ],
            'kue-tradisional' => [
                'slug_en' => 'traditional-cakes',
                'name' => $this->t('Edukasi Kue Tradisional', 'Traditional Cake Workshop'),
                'category' => 'kuliner',
                'custom_price' => 20000,
                'summary' => $this->t(
                    'Workshop kue tradisional seperti lapis, onde-onde, dan kue talam bersama perajin lokal.',
                    'Make traditional cakes such as lapis, onde-onde and talam with local bakers.'
                ),
                'description' => $this->t(
                    '<p>Pelajari seni membuat kue tradisional Sunda yang lezat. Peserta ikut membuat kue lapis, onde-onde, dan kue talam, lalu mencicipi hasil karyanya sendiri.</p>',
                    '<p>Learn the art of delicious Sundanese traditional cakes. Participants help make lapis, onde-onde and talam cakes, then taste their own creations.</p>'
                ),
                'placeholder_label' => 'Aneka kue tradisional di tampah bambu',
                'photos' => ['Aneka kue tradisional di tampah', 'Ibu-ibu perajin membuat onde-onde', 'Kue lapis warna-warni'],
            ],
            'tempe' => [
                'slug_en' => 'tempeh-making',
                'name' => $this->t('Edukasi Tempe', 'Tempeh Making'),
                'category' => 'kuliner',
                'custom_price' => 15000,
                'summary' => $this->t(
                    'Mengenal tempe, warisan kuliner kaya gizi: sejarah, proses fermentasi, dan cara membuatnya.',
                    'Get to know tempeh, a nutritious culinary heritage: its history, fermentation and how it is made.'
                ),
                'description' => $this->t(
                    '<p>Tempe adalah sumber protein nabati hasil fermentasi kedelai yang menjadi kebanggaan Indonesia. Peserta melihat langsung proses pembuatan tempe dan belajar membungkusnya dengan daun pisang.</p>',
                    '<p>Tempeh is a plant protein made by fermenting soybeans and a source of Indonesian pride. Participants see how tempeh is made and learn to wrap it in banana leaves.</p>'
                ),
                'placeholder_label' => 'Kedelai dibungkus daun pisang untuk tempe',
                'photos' => ['Kedelai rebus siap difermentasi', 'Membungkus tempe dengan daun pisang', 'Tempe jadi di nampan'],
            ],
            'permainan-tradisional' => [
                'slug_en' => 'traditional-sundanese-games',
                'name' => $this->t('Permainan Tradisional Sunda', 'Traditional Sundanese Games'),
                'category' => 'budaya',
                'custom_price' => 10000,
                'is_featured' => true,
                'summary' => $this->t(
                    'Kembalikan keceriaan masa kecil dengan bermain egrang, gasing, dan kelom geulis.',
                    'Bring back childhood joy with stilts (egrang), spinning tops (gasing) and kelom geulis.'
                ),
                'description' => $this->t(
                    '<p>Anak-anak dan orang dewasa dapat mencoba permainan tradisional Sunda di lapangan desa: berjalan dengan egrang, memutar gasing, hingga lomba kelom geulis. Seru, sehat, dan sarat nilai kebersamaan.</p>',
                    '<p>Children and adults can try traditional Sundanese games on the village field: walking on bamboo stilts, spinning tops and kelom geulis races. Fun, healthy and full of togetherness.</p>'
                ),
                'placeholder_label' => 'Anak-anak bermain egrang di lapangan desa',
                'photos' => ['Anak-anak bermain egrang', 'Gasing kayu berputar', 'Lomba kelom geulis', 'Rombongan bersorak di lapangan'],
            ],
            'nasi-liwet' => [
                'slug_en' => 'nasi-liwet',
                'name' => $this->t('Kuliner Nasi Liwet', 'Nasi Liwet Feast'),
                'category' => 'kuliner',
                'custom_price' => 30000,
                'summary' => $this->t(
                    'Nikmati lezatnya nasi liwet khas desa dengan lauk pilihan yang menggugah selera.',
                    'Enjoy the village’s signature nasi liwet with a selection of tasty side dishes.'
                ),
                'description' => $this->t(
                    '<p>Nasi liwet disajikan di atas daun pisang bersama ayam goreng, tahu, tempe, sambal, dan lalapan. Menu spesial untuk makan siang bersama setelah berkegiatan.</p>',
                    '<p>Nasi liwet is served on banana leaves with fried chicken, tofu, tempeh, chilli sambal and fresh vegetables. A special lunch to share after the activities.</p>'
                ),
                'placeholder_label' => 'Nasi liwet di atas daun pisang dengan lauk',
                'photos' => ['Nasi liwet & lauk di tampah', 'Makan bersama di saung', 'Sambal & lalapan segar'],
            ],
        ];

        $models = [];
        $sort = 0;
        foreach ($rows as $slug => $row) {
            $photos = $row['photos'];
            unset($row['photos']);

            $model = Attraction::create($row + ['slug' => $slug, 'sort' => $sort++]);
            foreach ($photos as $i => $label) {
                $model->photos()->create(['placeholder_label' => $label, 'sort' => $i]);
            }
            $models[$slug] = $model;
        }

        return $models;
    }

    /** @param array<string, Attraction> $attractions */
    protected function seedPackages(array $attractions): void
    {
        $rows = [
            [
                'slug' => 'budidaya-kelinci',
                'slug_en' => 'rabbit-farming',
                'name' => $this->t('Paket Edukasi Budidaya Kelinci', 'Rabbit Farming Education Package'),
                'category' => 'edukasi',
                'is_featured' => true,
                'duration' => $this->t('2 jam – seharian', '2 hours – full day'),
                'summary' => $this->t(
                    'Belajar langsung dari peternak berpengalaman: mengenal kelinci, memberi pakan, merawat kandang, hingga perkembangbiakan.',
                    'Learn straight from experienced breeders: meet the rabbits, feed them, care for the hutches and understand breeding.'
                ),
                'description' => $this->t(
                    '<p>Paket favorit sekolah dan keluarga. Peserta belajar lima materi utama budidaya kelinci: <strong>pengenalan kelinci, pemberian pakan, perawatan kandang, reproduksi & perkembangbiakan, serta panen & pemanfaatan</strong>. Pengalaman langsung, lebih bermakna!</p><p>Tersedia tiga pilihan paket sesuai durasi dan kebutuhan rombongan.</p>',
                    '<p>A favourite of schools and families. Participants learn the five key topics of rabbit farming: <strong>getting to know rabbits, feeding, hutch care, breeding, and harvest & use</strong>. Hands-on experience that sticks!</p><p>Three options are available to suit your group’s time and needs.</p>'
                ),
                'facilities' => ['transport', 'meals', 'tickets', 'guide', 'insurance'],
                'target_groups' => ['school', 'community', 'company', 'family'],
                'placeholder_label' => 'Peserta study tour menggendong kelinci putih',
                'attractions' => ['peternakan-kelinci', 'permainan-tradisional'],
                'itinerary' => [
                    ['time' => '08.00', 'activity' => $this->t('Penyambutan & pembagian kelompok', 'Welcome & group division')],
                    ['time' => '08.30', 'activity' => $this->t('Materi 1: Pengenalan kelinci', 'Topic 1: Getting to know rabbits')],
                    ['time' => '09.15', 'activity' => $this->t('Materi 2: Pemberian pakan', 'Topic 2: Feeding')],
                    ['time' => '10.00', 'activity' => $this->t('Materi 3: Perawatan kandang', 'Topic 3: Hutch care')],
                    ['time' => '10.45', 'activity' => $this->t('Materi 4: Reproduksi & perkembangbiakan', 'Topic 4: Breeding')],
                    ['time' => '11.30', 'activity' => $this->t('Materi 5: Panen & pemanfaatan', 'Topic 5: Harvest & use')],
                    ['time' => '12.00', 'activity' => $this->t('Makan siang (Standar & Premium)', 'Lunch (Standard & Premium)')],
                    ['time' => '13.00', 'activity' => $this->t('Workshop interaktif & keliling desa (Premium)', 'Interactive workshop & village walk (Premium)')],
                    ['time' => '15.30', 'activity' => $this->t('Oleh-oleh khas desa & penutupan', 'Village souvenirs & closing')],
                ],
                'tiers' => [
                    [
                        'name' => $this->t('Hemat', 'Basic'),
                        'duration' => $this->t('2–3 jam', '2–3 hours'),
                        'price' => 65000,
                        'min_pax' => 15,
                        'inclusions' => [
                            $this->t('Edukasi budidaya kelinci', 'Rabbit farming education'),
                            $this->t('Keliling desa', 'Village walk'),
                            $this->t('Snack lokal', 'Local snacks'),
                        ],
                    ],
                    [
                        'name' => $this->t('Standar', 'Standard'),
                        'duration' => $this->t('4–5 jam', '4–5 hours'),
                        'price' => 95000,
                        'min_pax' => 15,
                        'inclusions' => [
                            $this->t('Edukasi budidaya kelinci', 'Rabbit farming education'),
                            $this->t('Keliling desa', 'Village walk'),
                            $this->t('Snack lokal', 'Local snacks'),
                            $this->t('Makan siang', 'Lunch'),
                            $this->t('Souvenir', 'Souvenir'),
                        ],
                    ],
                    [
                        'name' => $this->t('Premium', 'Premium'),
                        'duration' => $this->t('Seharian', 'Full day'),
                        'price' => 125000,
                        'min_pax' => 15,
                        'badge' => $this->t('Best Seller', 'Best Seller'),
                        'inclusions' => [
                            $this->t('Edukasi budidaya kelinci', 'Rabbit farming education'),
                            $this->t('Workshop interaktif', 'Interactive workshop'),
                            $this->t('Keliling desa', 'Village walk'),
                            $this->t('Makan siang', 'Lunch'),
                            $this->t('Oleh-oleh khas desa', 'Village souvenirs'),
                            $this->t('Dokumentasi', 'Documentation'),
                        ],
                    ],
                ],
                'photos' => ['Kelinci putih & cokelat makan rumput', 'Pemandu menjelaskan jenis kelinci', 'Siswa memberi pakan kelinci', 'Membersihkan kandang bersama peternak', 'Anak kelinci di keranjang', 'Foto bersama rombongan sekolah'],
            ],
            [
                'slug' => 'pertanian-ubi-jalar',
                'slug_en' => 'sweet-potato-farming',
                'name' => $this->t('Paket Edukasi Pertanian Ubi Jalar', 'Sweet Potato Farming Package'),
                'category' => 'edukasi',
                'is_featured' => true,
                'duration' => $this->t('Seharian', 'Full day'),
                'summary' => $this->t(
                    'Belajar membuat pupuk organik, menanam, dan memanen ubi jalar, ditutup dengan makan nasi liwet khas desa.',
                    'Make organic compost, plant and harvest sweet potatoes, then enjoy the village’s signature nasi liwet.'
                ),
                'description' => $this->t(
                    '<p>Wisata edukasi alami, seru, dan bermanfaat. Peserta mengikuti tiga sesi praktik di kebun: <strong>pembuatan pupuk organik</strong> dari bahan alami yang ramah lingkungan, <strong>penanaman ubi jalar</strong> dan cara perawatannya, serta <strong>panen ubi jalar</strong> langsung dari kebunnya.</p><p>Menu spesial: nasi liwet dengan lauk pilihan yang menggugah selera.</p>',
                    '<p>A natural, fun and meaningful learning trip. Participants join three hands-on sessions in the field: <strong>making organic compost</strong> from natural, eco-friendly materials, <strong>planting sweet potatoes</strong> and caring for them, and <strong>harvesting sweet potatoes</strong> straight from the field.</p><p>Special menu: nasi liwet with a selection of tasty side dishes.</p>'
                ),
                'facilities' => ['meals', 'guide', 'tickets'],
                'target_groups' => ['school', 'community', 'company', 'family'],
                'placeholder_label' => 'Rombongan memanen ubi jalar ungu di kebun',
                'attractions' => ['kebun-ubi-jalar', 'nasi-liwet'],
                'itinerary' => [
                    ['time' => '08.00', 'activity' => $this->t('Penyambutan', 'Welcome')],
                    ['time' => '08.30', 'activity' => $this->t('Edukasi pembuatan pupuk organik', 'Organic compost making')],
                    ['time' => '10.00', 'activity' => $this->t('Edukasi penanaman ubi jalar', 'Sweet potato planting')],
                    ['time' => '11.30', 'activity' => $this->t('Edukasi panen ubi jalar', 'Sweet potato harvest')],
                    ['time' => '12.30', 'activity' => $this->t('Makan siang nasi liwet', 'Nasi liwet lunch')],
                    ['time' => '13.30', 'activity' => $this->t('Penutupan & foto bersama', 'Closing & group photo')],
                ],
                'tiers' => [
                    [
                        'name' => $this->t('Paket Ubi Jalar', 'Sweet Potato Package'),
                        'duration' => $this->t('Seharian', 'Full day'),
                        'price' => 125000,
                        'min_pax' => 30,
                        'inclusions' => [
                            $this->t('Edukasi pembuatan pupuk organik', 'Organic compost making'),
                            $this->t('Edukasi penanaman ubi jalar', 'Sweet potato planting'),
                            $this->t('Edukasi panen ubi jalar', 'Sweet potato harvest'),
                            $this->t('Makan siang nasi liwet', 'Nasi liwet lunch'),
                            $this->t('Pendamping pemandu lokal', 'Local guide'),
                        ],
                    ],
                ],
                'photos' => ['Mengaduk pupuk organik di tong', 'Menanam bibit ubi jalar berbaris', 'Panen ubi jalar ungu', 'Nasi liwet di tampah bambu', 'Peserta memamerkan hasil panen'],
            ],
            [
                'slug' => 'budidaya-padi',
                'slug_en' => 'rice-farming',
                'name' => $this->t('Paket Edukasi Budidaya Padi', 'Rice Farming Education Package'),
                'category' => 'edukasi',
                'is_featured' => true,
                'duration' => $this->t('Seharian', 'Full day'),
                'summary' => $this->t(
                    'Belajar budidaya padi, memetik stroberi, bermain di Curug Aman, permainan tradisional, hingga membuat kue tradisional dan tempe.',
                    'Learn rice farming, pick strawberries, visit Curug Aman, play traditional games, and make traditional cakes and tempeh.'
                ),
                'description' => $this->t(
                    '<p>Paket lengkap pesona budaya, alam, dan edukasi Desa Sukajadi. Dalam satu hari, peserta belajar budidaya padi di sawah, memetik stroberi, menikmati Air Terjun Curug Aman, bermain permainan tradisional Sunda, serta mengenal UMKM kue tradisional dan tempe.</p>',
                    '<p>A complete package of Sukajadi’s culture, nature and learning. In one day, participants learn rice farming in the paddy, pick strawberries, enjoy Curug Aman waterfall, play traditional Sundanese games, and meet the village’s traditional cake and tempeh makers.</p>'
                ),
                'facilities' => ['meals', 'guide', 'tickets'],
                'target_groups' => ['school', 'community', 'company', 'family'],
                'placeholder_label' => 'Rombongan menanam padi di sawah terasering',
                'attractions' => ['sawah-padi', 'kebun-stroberi', 'curug-aman', 'permainan-tradisional', 'kue-tradisional', 'tempe'],
                'itinerary' => [
                    ['time' => '08.00', 'activity' => $this->t('Penyambutan', 'Welcome')],
                    ['time' => '08.30', 'activity' => $this->t('Edukasi budidaya padi di sawah', 'Rice farming in the paddy')],
                    ['time' => '10.00', 'activity' => $this->t('Petik stroberi', 'Strawberry picking')],
                    ['time' => '11.00', 'activity' => $this->t('Wisata Air Terjun Curug Aman', 'Curug Aman waterfall visit')],
                    ['time' => '12.30', 'activity' => $this->t('Makan siang', 'Lunch')],
                    ['time' => '13.30', 'activity' => $this->t('Permainan tradisional Sunda', 'Traditional Sundanese games')],
                    ['time' => '14.30', 'activity' => $this->t('Edukasi UMKM kue tradisional & tempe', 'Traditional cakes & tempeh workshop')],
                    ['time' => '15.30', 'activity' => $this->t('Penutupan', 'Closing')],
                ],
                'tiers' => [
                    [
                        'name' => $this->t('Paket Budidaya Padi', 'Rice Farming Package'),
                        'duration' => $this->t('Seharian', 'Full day'),
                        'price' => 125000,
                        'min_pax' => 30,
                        'inclusions' => [
                            $this->t('Edukasi budidaya padi', 'Rice farming education'),
                            $this->t('Edukasi & petik stroberi', 'Strawberry picking'),
                            $this->t('Air Terjun Curug Aman', 'Curug Aman waterfall'),
                            $this->t('Permainan tradisional Sunda', 'Traditional Sundanese games'),
                            $this->t('Edukasi UMKM kue tradisional', 'Traditional cake workshop'),
                            $this->t('Edukasi tempe', 'Tempeh making'),
                        ],
                    ],
                ],
                'photos' => ['Rombongan menanam padi', 'Petik stroberi di greenhouse', 'Air terjun Curug Aman', 'Anak-anak bermain egrang', 'Membuat kue tradisional', 'Membungkus tempe dengan daun pisang'],
            ],
            [
                'slug' => 'edukasi-umkm',
                'slug_en' => 'local-business-workshop',
                'name' => $this->t('Paket Edukasi UMKM', 'Local Business Workshop Package'),
                'category' => 'kuliner',
                'is_featured' => true,
                'duration' => $this->t('Seharian', 'Full day'),
                'summary' => $this->t(
                    'Praktik membuat minuman sari buah pala dan kue noga, lalu bertualang ke Curug Aman.',
                    'Make nutmeg fruit juice and noga cakes, then head off on an adventure to Curug Aman.'
                ),
                'description' => $this->t(
                    '<p>Dukung UMKM lokal sambil belajar! Peserta mengolah buah pala segar menjadi minuman khas yang segar dan berkhasiat, mempelajari rahasia resep tradisional kue noga, lalu menikmati keindahan alam Curug Aman yang asri dan menyegarkan.</p>',
                    '<p>Support local businesses while you learn! Participants turn fresh nutmeg into a refreshing local drink, discover the secret recipe of traditional noga cakes, and then enjoy the lush, refreshing nature of Curug Aman.</p>'
                ),
                'facilities' => ['guide', 'tickets'],
                'target_groups' => ['school', 'community', 'company', 'family'],
                'placeholder_label' => 'Peserta membuat sari buah pala bersama UMKM',
                'attractions' => ['sari-buah-pala', 'kue-noga', 'curug-aman'],
                'itinerary' => [
                    ['time' => '08.00', 'activity' => $this->t('Penyambutan', 'Welcome')],
                    ['time' => '08.30', 'activity' => $this->t('Edukasi pembuatan minuman sari buah pala', 'Nutmeg fruit juice making')],
                    ['time' => '10.30', 'activity' => $this->t('Edukasi pembuatan kue noga', 'Noga cake making')],
                    ['time' => '12.30', 'activity' => $this->t('Istirahat & makan siang', 'Break & lunch')],
                    ['time' => '13.30', 'activity' => $this->t('Wisata alam & petualangan Curug Aman', 'Nature walk & adventure to Curug Aman')],
                    ['time' => '15.30', 'activity' => $this->t('Penutupan', 'Closing')],
                ],
                'tiers' => [
                    [
                        'name' => $this->t('Paket UMKM', 'Local Business Package'),
                        'duration' => $this->t('Seharian', 'Full day'),
                        'price' => 100000,
                        'min_pax' => 20,
                        'is_dummy_price' => true,
                        'inclusions' => [
                            $this->t('Edukasi pembuatan minuman sari buah pala', 'Nutmeg fruit juice making'),
                            $this->t('Edukasi pembuatan kue noga', 'Noga cake making'),
                            $this->t('Wisata alam Curug Aman', 'Curug Aman nature trip'),
                            $this->t('Pendamping pemandu lokal', 'Local guide'),
                        ],
                    ],
                ],
                'photos' => ['Merebus buah pala', 'Botol sari pala', 'Menguleni adonan kue noga', 'Loyang kue noga', 'Curug Aman di tengah hutan'],
            ],
            [
                'slug' => 'anggrek-bulan',
                'slug_en' => 'moon-orchid-agrotourism',
                'name' => $this->t('Agro Eduwisata Anggrek Bulan', 'Moon Orchid Agro-Edutourism'),
                'category' => 'edukasi',
                'is_featured' => true,
                'duration' => $this->t('2–3 jam', '2–3 hours'),
                'summary' => $this->t(
                    'Belajar budidaya anggrek bulan langsung dari petani dan melihat proses perkembangbiakannya.',
                    'Learn moon orchid cultivation straight from the growers and see how they are propagated.'
                ),
                'description' => $this->t(
                    '<p>Kunjungan ke kebun anggrek bulan Desa Sukajadi. Peserta belajar budidaya langsung dari petani, melihat proses perkembangbiakan anggrek bulan, dan berkesempatan membawa pulang anggrek pilihan.</p>',
                    '<p>A visit to Sukajadi’s moon orchid nursery. Participants learn cultivation from the growers, see how moon orchids are propagated, and can take home an orchid of their choice.</p>'
                ),
                'facilities' => ['guide', 'tickets'],
                'target_groups' => ['school', 'community', 'family'],
                'placeholder_label' => 'Petani menjelaskan anggrek bulan kepada keluarga',
                'attractions' => ['kebun-anggrek-bulan'],
                'itinerary' => [
                    ['time' => '09.00', 'activity' => $this->t('Penyambutan di kebun anggrek', 'Welcome at the orchid nursery')],
                    ['time' => '09.15', 'activity' => $this->t('Belajar budidaya langsung dari petani', 'Learning cultivation from the growers')],
                    ['time' => '10.15', 'activity' => $this->t('Melihat proses perkembangbiakan anggrek bulan', 'Seeing how moon orchids are propagated')],
                    ['time' => '11.00', 'activity' => $this->t('Belanja anggrek & foto bersama', 'Orchid shopping & group photo')],
                ],
                'tiers' => [
                    [
                        'name' => $this->t('Paket Anggrek Bulan', 'Moon Orchid Package'),
                        'duration' => $this->t('2–3 jam', '2–3 hours'),
                        'price' => 75000,
                        'min_pax' => 15,
                        'is_dummy_price' => true,
                        'inclusions' => [
                            $this->t('Belajar budidaya langsung dari petani', 'Cultivation lesson from the growers'),
                            $this->t('Melihat proses perkembangbiakan anggrek bulan', 'Moon orchid propagation demo'),
                            $this->t('Pendamping pemandu lokal', 'Local guide'),
                        ],
                    ],
                ],
                'photos' => ['Kebun anggrek ungu & putih', 'Petani berbatik menjelaskan', 'Anak-anak mencatat materi', 'Close-up anggrek bulan merah muda'],
            ],
        ];

        foreach ($rows as $sort => $row) {
            $tiers = $row['tiers'];
            $photos = $row['photos'];
            $attractionSlugs = $row['attractions'];
            unset($row['tiers'], $row['photos'], $row['attractions']);

            $package = Package::create($row + ['sort' => $sort]);

            foreach ($tiers as $i => $tier) {
                $package->tiers()->create($tier + ['sort' => $i]);
            }

            foreach ($photos as $i => $label) {
                $package->photos()->create(['placeholder_label' => $label, 'sort' => $i]);
            }

            $package->attractions()->sync(collect($attractionSlugs)->map(fn ($slug) => $attractions[$slug]->id));
        }
    }

    protected function seedAddons(): void
    {
        $rows = [
            ['name' => $this->t('Dokumentasi foto & video', 'Photo & video documentation'), 'description' => $this->t('Fotografer lokal mendampingi seluruh kegiatan.', 'A local photographer covers all activities.'), 'price' => 500000, 'unit' => 'per_group'],
            ['name' => $this->t('Makan siang nasi liwet', 'Nasi liwet lunch'), 'description' => $this->t('Nasi liwet dengan lauk lengkap.', 'Nasi liwet with complete side dishes.'), 'price' => 30000, 'unit' => 'per_pax'],
            ['name' => $this->t('Paket oleh-oleh UMKM', 'Local souvenir bundle'), 'description' => $this->t('Sari buah pala, kue noga, dan camilan khas desa.', 'Nutmeg juice, noga cakes and village snacks.'), 'price' => 25000, 'unit' => 'per_pax'],
            ['name' => $this->t('Kaos souvenir Desa Wisata', 'Tourism village T-shirt'), 'description' => $this->t('Kaos dengan logo Desa Wisata Sukajadi.', 'T-shirt with the Sukajadi Tourism Village logo.'), 'price' => 50000, 'unit' => 'per_pax'],
        ];

        foreach ($rows as $sort => $row) {
            Addon::create($row + ['sort' => $sort]);
        }
    }
}
