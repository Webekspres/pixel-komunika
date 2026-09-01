<?php

namespace App\Services\Shipping;

class IndonesiaRegionService
{
    /**
     * Complete mapping of 38 Indonesian Provinces to their respective Cities & Regencies (Sorted A-Z).
     *
     * @return array<string, array<int, string>>
     */
    public static function all(): array
    {
        $data = [
            'Aceh' => [
                'Kabupaten Aceh Barat', 'Kabupaten Aceh Barat Daya', 'Kabupaten Aceh Besar', 'Kabupaten Aceh Jaya',
                'Kabupaten Aceh Selatan', 'Kabupaten Aceh Singkil', 'Kabupaten Aceh Tamiang', 'Kabupaten Aceh Tengah',
                'Kabupaten Aceh Tenggara', 'Kabupaten Aceh Timur', 'Kabupaten Aceh Utara', 'Kabupaten Bener Meriah',
                'Kabupaten Bireuen', 'Kabupaten Gayo Lues', 'Kabupaten Nagan Raya', 'Kabupaten Pidie',
                'Kabupaten Pidie Jaya', 'Kabupaten Simeulue', 'Kota Banda Aceh', 'Kota Langsa', 'Kota Lhokseumawe',
                'Kota Sabang', 'Kota Subulussalam',
            ],
            'Bali' => [
                'Kabupaten Badung', 'Kabupaten Bangli', 'Kabupaten Buleleng', 'Kabupaten Gianyar',
                'Kabupaten Jembrana', 'Kabupaten Karangasem', 'Kabupaten Klungkung', 'Kabupaten Tabanan',
                'Kota Denpasar',
            ],
            'Bangka Belitung' => [
                'Kabupaten Bangka', 'Kabupaten Bangka Barat', 'Kabupaten Bangka Selatan',
                'Kabupaten Bangka Tengah', 'Kabupaten Belitung', 'Kabupaten Belitung Timur',
                'Kota Pangkalpinang',
            ],
            'Banten' => [
                'Kabupaten Lebak', 'Kabupaten Pandeglang', 'Kabupaten Serang', 'Kabupaten Tangerang',
                'Kota Cilegon', 'Kota Serang', 'Kota Tangerang', 'Kota Tangerang Selatan',
            ],
            'Bengkulu' => [
                'Kabupaten Bengkulu Selatan', 'Kabupaten Bengkulu Tengah', 'Kabupaten Bengkulu Utara',
                'Kabupaten Kaur', 'Kabupaten Kepahiang', 'Kabupaten Lebong', 'Kabupaten Mukomuko',
                'Kabupaten Rejang Lebong', 'Kabupaten Seluma', 'Kota Bengkulu',
            ],
            'DI Yogyakarta' => [
                'Kabupaten Bantul', 'Kabupaten Gunungkidul', 'Kabupaten Kulon Progo', 'Kabupaten Sleman',
                'Kota Yogyakarta',
            ],
            'DKI Jakarta' => [
                'Kabupaten Kepulauan Seribu', 'Kota Jakarta Barat', 'Kota Jakarta Pusat',
                'Kota Jakarta Selatan', 'Kota Jakarta Timur', 'Kota Jakarta Utara',
            ],
            'Gorontalo' => [
                'Kabupaten Boalemo', 'Kabupaten Bone Bolango', 'Kabupaten Gorontalo',
                'Kabupaten Gorontalo Utara', 'Kabupaten Pohuwato', 'Kota Gorontalo',
            ],
            'Jambi' => [
                'Kabupaten Batanghari', 'Kabupaten Bungo', 'Kabupaten Kerinci',
                'Kabupaten Merangin', 'Kabupaten Muaro Jambi', 'Kabupaten Sarolangun',
                'Kabupaten Tanjung Jabung Barat', 'Kabupaten Tanjung Jabung Timur',
                'Kabupaten Tebo', 'Kota Jambi', 'Kota Sungai Penuh',
            ],
            'Jawa Barat' => [
                'Kabupaten Bandung', 'Kabupaten Bandung Barat', 'Kabupaten Bekasi', 'Kabupaten Bogor',
                'Kabupaten Ciamis', 'Kabupaten Cianjur', 'Kabupaten Cirebon', 'Kabupaten Garut',
                'Kabupaten Indramayu', 'Kabupaten Karawang', 'Kabupaten Kuningan', 'Kabupaten Majalengka',
                'Kabupaten Pangandaran', 'Kabupaten Purwakarta', 'Kabupaten Subang', 'Kabupaten Sukabumi',
                'Kabupaten Sumedang', 'Kabupaten Tasikmalaya', 'Kota Bandung', 'Kota Banjar',
                'Kota Bekasi', 'Kota Bogor', 'Kota Cimahi', 'Kota Cirebon', 'Kota Depok',
                'Kota Sukabumi', 'Kota Tasikmalaya',
            ],
            'Jawa Tengah' => [
                'Kabupaten Banjarnegara', 'Kabupaten Banyumas', 'Kabupaten Batang', 'Kabupaten Blora',
                'Kabupaten Boyolali', 'Kabupaten Brebes', 'Kabupaten Cilacap', 'Kabupaten Demak',
                'Kabupaten Grobogan', 'Kabupaten Jepara', 'Kabupaten Karanganyar', 'Kabupaten Kebumen',
                'Kabupaten Kendal', 'Kabupaten Klaten', 'Kabupaten Kudus', 'Kabupaten Magelang',
                'Kabupaten Pati', 'Kabupaten Pekalongan', 'Kabupaten Pemalang', 'Kabupaten Purbalingga',
                'Kabupaten Purworejo', 'Kabupaten Rembang', 'Kabupaten Semarang', 'Kabupaten Sragen',
                'Kabupaten Sukoharjo', 'Kabupaten Tegal', 'Kabupaten Temanggung', 'Kabupaten Wonogiri',
                'Kabupaten Wonosobo', 'Kota Magelang', 'Kota Pekalongan', 'Kota Salatiga',
                'Kota Semarang', 'Kota Surakarta', 'Kota Tegal',
            ],
            'Jawa Timur' => [
                'Kabupaten Bangkalan', 'Kabupaten Banyuwangi', 'Kabupaten Blitar', 'Kabupaten Bojonegoro',
                'Kabupaten Bondowoso', 'Kabupaten Gresik', 'Kabupaten Jember', 'Kabupaten Jombang',
                'Kabupaten Kediri', 'Kabupaten Lamongan', 'Kabupaten Lumajang', 'Kabupaten Madiun',
                'Kabupaten Magetan', 'Kabupaten Malang', 'Kabupaten Mojokerto', 'Kabupaten Nganjuk',
                'Kabupaten Ngawi', 'Kabupaten Pacitan', 'Kabupaten Pamekasan', 'Kabupaten Pasuruan',
                'Kabupaten Ponorogo', 'Kabupaten Probolinggo', 'Kabupaten Sampang', 'Kabupaten Sidoarjo',
                'Kabupaten Situbondo', 'Kabupaten Sumenep', 'Kabupaten Trenggalek', 'Kabupaten Tuban',
                'Kabupaten Tulungagung', 'Kota Batu', 'Kota Blitar', 'Kota Kediri',
                'Kota Madiun', 'Kota Mojokerto', 'Kota Pasuruan', 'Kota Probolinggo',
                'Kota Surabaya', 'Kota Malang',
            ],
            'Kalimantan Barat' => [
                'Kabupaten Bengkayang', 'Kabupaten Kapuas Hulu', 'Kabupaten Kayong Utara', 'Kabupaten Ketapang',
                'Kabupaten Kubu Raya', 'Kabupaten Landak', 'Kabupaten Melawi', 'Kabupaten Mempawah',
                'Kabupaten Sambas', 'Kabupaten Sanggau', 'Kabupaten Sekadau', 'Kabupaten Sintang',
                'Kota Pontianak', 'Kota Singkawang',
            ],
            'Kalimantan Selatan' => [
                'Kabupaten Balangan', 'Kabupaten Banjar', 'Kabupaten Barito Kuala', 'Kabupaten Hulu Sungai Selatan',
                'Kabupaten Hulu Sungai Tengah', 'Kabupaten Hulu Sungai Utara', 'Kabupaten Kotabaru',
                'Kabupaten Tabalong', 'Kabupaten Tanah Bumbu', 'Kabupaten Tanah Laut', 'Kabupaten Tapin',
                'Kota Banjarbaru', 'Kota Banjarmasin',
            ],
            'Kalimantan Tengah' => [
                'Kabupaten Barito Selatan', 'Kabupaten Barito Timur', 'Kabupaten Barito Utara',
                'Kabupaten Gunung Mas', 'Kabupaten Kapuas', 'Kabupaten Katingan', 'Kabupaten Kotawaringin Barat',
                'Kabupaten Kotawaringin Timur', 'Kabupaten Lamandau', 'Kabupaten Murung Raya',
                'Kabupaten Pulang Pisau', 'Kabupaten Seruyan', 'Kabupaten Sukamara',
                'Kota Palangka Raya',
            ],
            'Kalimantan Timur' => [
                'Kabupaten Berau', 'Kabupaten Kutai Barat', 'Kabupaten Kutai Kartanegara',
                'Kabupaten Kutai Timur', 'Kabupaten Mahakam Ulu', 'Kabupaten Paser',
                'Kabupaten Penajam Paser Utara', 'Kota Balikpapan', 'Kota Bontang', 'Kota Samarinda',
            ],
            'Kalimantan Utara' => [
                'Kabupaten Bulungan', 'Kabupaten Malinau', 'Kabupaten Nunukan', 'Kabupaten Tana Tidung',
                'Kota Tarakan',
            ],
            'Kepulauan Riau' => [
                'Kabupaten Bintan', 'Kabupaten Karimun', 'Kabupaten Kepulauan Anambas',
                'Kabupaten Lingga', 'Kabupaten Natuna', 'Kota Batam', 'Kota Tanjungpinang',
            ],
            'Lampung' => [
                'Kabupaten Lampung Barat', 'Kabupaten Lampung Selatan', 'Kabupaten Lampung Tengah',
                'Kabupaten Lampung Timur', 'Kabupaten Lampung Utara', 'Kabupaten Mesuji',
                'Kabupaten Pesawaran', 'Kabupaten Pesisir Barat', 'Kabupaten Pringsewu',
                'Kabupaten Tanggamus', 'Kabupaten Tulang Bawang', 'Kabupaten Tulang Bawang Barat',
                'Kabupaten Way Kanan', 'Kota Bandar Lampung', 'Kota Metro',
            ],
            'Maluku' => [
                'Kabupaten Buru', 'Kabupaten Buru Selatan', 'Kabupaten Kepulauan Aru',
                'Kabupaten Kepulauan Tanimbar', 'Kabupaten Maluku Barat Daya', 'Kabupaten Maluku Tengah',
                'Kabupaten Maluku Tenggara', 'Kabupaten Seram Bagian Barat', 'Kabupaten Seram Bagian Timur',
                'Kota Ambon', 'Kota Tual',
            ],
            'Maluku Utara' => [
                'Kabupaten Halmahera Barat', 'Kabupaten Halmahera Selatan', 'Kabupaten Halmahera Tengah',
                'Kabupaten Halmahera Timur', 'Kabupaten Halmahera Utara', 'Kabupaten Kepulauan Sula',
                'Kabupaten Pulau Morotai', 'Kabupaten Pulau Taliabu', 'Kota Ternate', 'Kota Tidore Kepulauan',
            ],
            'Nusa Tenggara Barat' => [
                'Kabupaten Bima', 'Kabupaten Dompu', 'Kabupaten Lombok Barat', 'Kabupaten Lombok Tengah',
                'Kabupaten Lombok Timur', 'Kabupaten Lombok Utara', 'Kabupaten Sumbawa',
                'Kabupaten Sumbawa Barat', 'Kota Bima', 'Kota Mataram',
            ],
            'Nusa Tenggara Timur' => [
                'Kabupaten Alor', 'Kabupaten Belu', 'Kabupaten Ende', 'Kabupaten Flores Timur',
                'Kabupaten Kupang', 'Kabupaten Lembata', 'Kabupaten Malaka', 'Kabupaten Manggarai',
                'Kabupaten Manggarai Barat', 'Kabupaten Manggarai Timur', 'Kabupaten Nagekeo',
                'Kabupaten Ngada', 'Kabupaten Rote Ndao', 'Kabupaten Sabu Raijua', 'Kabupaten Sikka',
                'Kabupaten Sumba Barat', 'Kabupaten Sumba Barat Daya', 'Kabupaten Sumba Tengah',
                'Kabupaten Sumba Timur', 'Kabupaten Timor Tengah Selatan', 'Kabupaten Timor Tengah Utara',
                'Kota Kupang',
            ],
            'Papua' => [
                'Kabupaten Biak Numfor', 'Kabupaten Jayapura', 'Kabupaten Keerom', 'Kabupaten Kepulauan Yapen',
                'Kabupaten Mamberamo Raya', 'Kabupaten Sarmi', 'Kabupaten Supiori', 'Kabupaten Waropen',
                'Kota Jayapura',
            ],
            'Papua Barat' => [
                'Kabupaten Fakfak', 'Kabupaten Kaimana', 'Kabupaten Manokwari',
                'Kabupaten Manokwari Selatan', 'Kabupaten Pegunungan Arfak', 'Kabupaten Teluk Bintuni',
                'Kabupaten Teluk Wondama', 'Kota Manokwari',
            ],
            'Papua Barat Daya' => [
                'Kabupaten Maybrat', 'Kabupaten Raja Ampat', 'Kabupaten Sorong',
                'Kabupaten Sorong Selatan', 'Kabupaten Tambrauw', 'Kota Sorong',
            ],
            'Papua Pegunungan' => [
                'Kabupaten Jayawijaya', 'Kabupaten Lanny Jaya', 'Kabupaten Mamberamo Tengah',
                'Kabupaten Nduga', 'Kabupaten Pegunungan Bintang', 'Kabupaten Tolikara',
                'Kabupaten Yahukimo', 'Kabupaten Yalimo',
            ],
            'Papua Selatan' => [
                'Kabupaten Asmat', 'Kabupaten Boven Digoel', 'Kabupaten Mappi', 'Kabupaten Merauke',
            ],
            'Papua Tengah' => [
                'Kabupaten Deiyai', 'Kabupaten Dogiyai', 'Kabupaten Intan Jaya', 'Kabupaten Mimika',
                'Kabupaten Nabire', 'Kabupaten Paniai', 'Kabupaten Puncak', 'Kabupaten Puncak Jaya',
            ],
            'Riau' => [
                'Kabupaten Bengkalis', 'Kabupaten Indragiri Hilir', 'Kabupaten Indragiri Hulu',
                'Kabupaten Kampar', 'Kabupaten Kepulauan Meranti', 'Kabupaten Kuantan Singingi',
                'Kabupaten Pelalawan', 'Kabupaten Rokan Hilir', 'Kabupaten Rokan Hulu',
                'Kabupaten Siak', 'Kota Dumai', 'Kota Pekanbaru',
            ],
            'Sulawesi Barat' => [
                'Kabupaten Majene', 'Kabupaten Mamasa', 'Kabupaten Mamuju',
                'Kabupaten Mamuju Tengah', 'Kabupaten Pasangkayu', 'Kabupaten Polewali Mandar',
            ],
            'Sulawesi Selatan' => [
                'Kabupaten Bantaeng', 'Kabupaten Barru', 'Kabupaten Bone', 'Kabupaten Bulukumba',
                'Kabupaten Enrekang', 'Kabupaten Gowa', 'Kabupaten Jeneponto', 'Kabupaten Kepulauan Selayar',
                'Kabupaten Luwu', 'Kabupaten Luwu Timur', 'Kabupaten Luwu Utara', 'Kabupaten Maros',
                'Kabupaten Pangkajene dan Kepulauan', 'Kabupaten Pinrang', 'Kabupaten Sidenreng Rappang',
                'Kabupaten Sinjai', 'Kabupaten Soppeng', 'Kabupaten Takalar', 'Kabupaten Tana Toraja',
                'Kabupaten Toraja Utara', 'Kabupaten Wajo', 'Kota Makassar', 'Kota Palopo', 'Kota Parepare',
            ],
            'Sulawesi Tengah' => [
                'Kabupaten Banggai', 'Kabupaten Banggai Kepulauan', 'Kabupaten Banggai Laut',
                'Kabupaten Buol', 'Kabupaten Donggala', 'Kabupaten Morowali', 'Kabupaten Morowali Utara',
                'Kabupaten Parigi Moutong', 'Kabupaten Poso', 'Kabupaten Sigi', 'Kabupaten Tojo Una-Una',
                'Kabupaten Toli-Toli', 'Kota Palu',
            ],
            'Sulawesi Tenggara' => [
                'Kabupaten Bombana', 'Kabupaten Buton', 'Kabupaten Buton Selatan',
                'Kabupaten Buton Tengah', 'Kabupaten Buton Utara', 'Kabupaten Kolaka',
                'Kabupaten Kolaka Timur', 'Kabupaten Kolaka Utara', 'Kabupaten Konawe',
                'Kabupaten Konawe Kepulauan', 'Kabupaten Konawe Selatan', 'Kabupaten Konawe Utara',
                'Kabupaten Muna', 'Kabupaten Muna Barat', 'Kabupaten Wakatobi',
                'Kota Baubau', 'Kota Kendari',
            ],
            'Sulawesi Utara' => [
                'Kabupaten Bolaang Mongondow', 'Kabupaten Bolaang Mongondow Selatan',
                'Kabupaten Bolaang Mongondow Timur', 'Kabupaten Bolaang Mongondow Utara',
                'Kabupaten Kepulauan Sangihe', 'Kabupaten Kepulauan Siau Tagulandang Biaro',
                'Kabupaten Kepulauan Talaud', 'Kabupaten Minahasa', 'Kabupaten Minahasa Selatan',
                'Kabupaten Minahasa Tenggara', 'Kabupaten Minahasa Utara', 'Kota Bitung',
                'Kota Kotamobagu', 'Kota Manado', 'Kota Tomohon',
            ],
            'Sumatera Barat' => [
                'Kabupaten Agam', 'Kabupaten Dharmasraya', 'Kabupaten Kepulauan Mentawai',
                'Kabupaten Lima Puluh Kota', 'Kabupaten Padang Pariaman', 'Kabupaten Pasaman',
                'Kabupaten Pasaman Barat', 'Kabupaten Pesisir Selatan', 'Kabupaten Sijunjung',
                'Kabupaten Solok', 'Kabupaten Solok Selatan', 'Kabupaten Tanah Datar',
                'Kota Bukittinggi', 'Kota Padang', 'Kota Padang Panjang', 'Kota Pariaman',
                'Kota Payakumbuh', 'Kota Sawahlunto', 'Kota Solok',
            ],
            'Sumatera Selatan' => [
                'Kabupaten Banyuasin', 'Kabupaten Empat Lawang', 'Kabupaten Lahat',
                'Kabupaten Muara Enim', 'Kabupaten Musi Banyuasin', 'Kabupaten Musi Rawas',
                'Kabupaten Musi Rawas Utara', 'Kabupaten Ogan Ilir', 'Kabupaten Ogan Komering Ilir',
                'Kabupaten Ogan Komering Ulu', 'Kabupaten Ogan Komering Ulu Selatan',
                'Kabupaten Ogan Komering Ulu Timur', 'Kabupaten Penukal Abab Lematang Ilir',
                'Kota Lubuklinggau', 'Kota Pagar Alam', 'Kota Palembang', 'Kota Prabumulih',
            ],
            'Sumatera Utara' => [
                'Kabupaten Asahan', 'Kabupaten Batubara', 'Kabupaten Dairi', 'Kabupaten Deli Serdang',
                'Kabupaten Humbang Hasundutan', 'Kabupaten Karo', 'Kabupaten Labuhanbatu',
                'Kabupaten Labuhanbatu Selatan', 'Kabupaten Labuhanbatu Utara', 'Kabupaten Langkat',
                'Kabupaten Mandailing Natal', 'Kabupaten Nias', 'Kabupaten Nias Barat',
                'Kabupaten Nias Selatan', 'Kabupaten Nias Utara', 'Kabupaten Padang Lawas',
                'Kabupaten Padang Lawas Utara', 'Kabupaten Pakpak Bharat', 'Kabupaten Samosir',
                'Kabupaten Serdang Bedagai', 'Kabupaten Simalungun', 'Kabupaten Tapanuli Selatan',
                'Kabupaten Tapanuli Tengah', 'Kabupaten Tapanuli Utara', 'Kabupaten Toba',
                'Kota Binjai', 'Kota Gunungsitoli', 'Kota Medan', 'Kota Padangsidimpuan',
                'Kota Pematangsiantar', 'Kota Sibolga', 'Kota Tanjungbalai', 'Kota Tebing Tinggi',
            ],
        ];

        // Ensure both province keys and city lists are strictly sorted A-Z
        ksort($data, SORT_NATURAL | SORT_FLAG_CASE);
        foreach ($data as &$cities) {
            sort($cities, SORT_NATURAL | SORT_FLAG_CASE);
        }

        return $data;
    }

    /**
     * Get list of all provinces (Sorted A-Z).
     *
     * @return array<int, string>
     */
    public static function getProvinces(): array
    {
        $provinces = array_keys(static::all());
        sort($provinces, SORT_NATURAL | SORT_FLAG_CASE);

        return $provinces;
    }

    /**
     * Get cities/regencies in a given province (Sorted A-Z).
     *
     * @return array<int, string>
     */
    public static function getCitiesByProvince(string $province): array
    {
        $all = static::all();

        if (isset($all[$province])) {
            $cities = $all[$province];
            sort($cities, SORT_NATURAL | SORT_FLAG_CASE);

            return $cities;
        }

        foreach ($all as $prov => $cities) {
            if (strcasecmp($prov, $province) === 0) {
                sort($cities, SORT_NATURAL | SORT_FLAG_CASE);

                return $cities;
            }
        }

        return [];
    }

    /**
     * Curated list of districts (Kecamatan) for Indonesian cities & regencies (Sorted A-Z).
     *
     * @return array<int, string>
     */
    public static function getDistrictsByCity(string $city): array
    {
        $c = mb_strtolower(trim($city));

        $districtsMap = [
            // Bandung & Sekitarnya
            'kota bandung' => [
                'Andir', 'Astana Anyar', 'Babakan Ciparay', 'Bandung Kidul', 'Bandung Kulon',
                'Bandung Wetan', 'Batununggal', 'Bojongloa Kaler', 'Bojongloa Kidul', 'Buahbatu',
                'Cibeunying Kaler', 'Cibeunying Kidul', 'Cibiru', 'Cicendo', 'Cidadap',
                'Cinambo', 'Coblong', 'Gedebage', 'Kiaracondong', 'Lengkong',
                'Mandalajati', 'Panyileukan', 'Rancasari', 'Regol', 'Sukajadi',
                'Sukasari', 'Sumur Bandung', 'Ujungberung',
            ],
            'kabupaten bandung' => [
                'Arjasari', 'Baleendah', 'Banjaran', 'Bojongsoang', 'Cangkuang', 'Cicalengka',
                'Cikancung', 'Cilengkrang', 'Cileunyi', 'Cimaung', 'Cimenyan', 'Ciparay',
                'Ciwidey', 'Dayeuhkolot', 'Ibun', 'Katapang', 'Kertasari', 'Kutawaringin',
                'Majalaya', 'Margaasih', 'Margahayu', 'Nagreg', 'Pacet', 'Pameungpeuk',
                'Pangalengan', 'Paseh', 'Pasirjambu', 'Rancabali', 'Rancaekek', 'Solokanjeruk', 'Soreang',
            ],
            'kabupaten bandung barat' => [
                'Batujajar', 'Cihampelas', 'Cikalongwetan', 'Cililin', 'Cipatat', 'Cipeundeuy',
                'Cipongkor', 'Cisarua', 'Gununghalu', 'Lembang', 'Ngamprah', 'Padalarang',
                'Parongpong', 'Rongga', 'Saguling', 'Sindangkerta',
            ],
            'kota cimahi' => [
                'Cimahi Selatan', 'Cimahi Tengah', 'Cimahi Utara',
            ],
            'kota bekasi' => [
                'Bantar Gebang', 'Bekasi Barat', 'Bekasi Selatan', 'Bekasi Timur', 'Bekasi Utara',
                'Jatiasih', 'Jatisampurna', 'Medan Satria', 'Mustika Jaya', 'Pondok Gede',
                'Pondok Melati', 'Rawalumbu',
            ],
            'kabupaten bekasi' => [
                'Babelan', 'Bojongmangu', 'Cabangbungin', 'Cibarusah', 'Cibitung', 'Cikarang Barat',
                'Cikarang Pusat', 'Cikarang Selatan', 'Cikarang Timur', 'Cikarang Utara', 'Karangbahagia',
                'Kedungwaringin', 'Muara Gembong', 'Pebayuran', 'Serang Baru', 'Setu', 'Sukakarya',
                'Sukatani', 'Sukawangi', 'Tambelang', 'Tambun Selatan', 'Tambun Utara', 'Tarumajaya',
            ],
            'kota bogor' => [
                'Bogor Barat', 'Bogor Selatan', 'Bogor Tengah', 'Bogor Timur', 'Bogor Utara', 'Tanah Sareal',
            ],
            'kabupaten bogor' => [
                'Babakan Madang', 'Bojonggede', 'Caringin', 'Cariu', 'Ciampea', 'Ciawi', 'Cibinong',
                'Cibungbulang', 'Cigombong', 'Cigudeg', 'Cijeruk', 'Cileungsi', 'Ciomas', 'Cisarua',
                'Ciseeng', 'Citeureup', 'Dramaga', 'Gunung Putri', 'Gunung Sindur', 'Jasinga',
                'Jonggol', 'Kemang', 'Klapanunggal', 'Leuwiliang', 'Leuwisadeng', 'Megamendung',
                'Nanggung', 'Pamijahan', 'Parung', 'Parung Panjang', 'Ranca Bungur', 'Rumpin',
                'Sukajaya', 'Sukamakmur', 'Sukaraja', 'Tajurhalang', 'Tamansari', 'Tanjungsari',
                'Tenjo', 'Tenjolaya',
            ],
            'kota depok' => [
                'Beji', 'Bojongsari', 'Cilodong', 'Cimanggis', 'Cinere', 'Cipayung',
                'Limo', 'Pancoran Mas', 'Sawangan', 'Sukmajaya', 'Tapos',
            ],
            'kota tangerang' => [
                'Batuceper', 'Benda', 'Cibodas', 'Ciledug', 'Cipondoh', 'Jatiuwung',
                'Karangtengah', 'Karawaci', 'Larangan', 'Neglasari', 'Periuk', 'Pinang', 'Tangerang',
            ],
            'kota tangerang selatan' => [
                'Ciputat', 'Ciputat Timur', 'Pamulang', 'Pondok Aren', 'Serpong', 'Serpong Utara', 'Setu',
            ],
            'kabupaten tangerang' => [
                'Balaraja', 'Cikupa', 'Cisauk', 'Cisoka', 'Curug', 'Gunung Kaler', 'Jambe',
                'Jayanti', 'Kelapa Dua', 'Kemiri', 'Kosambi', 'Kresek', 'Kronjo', 'Legok',
                'Mauk', 'Mekar Baru', 'Pagedangan', 'Pakuhaji', 'Panongan', 'Pasar Kemis',
                'Rajeg', 'Sepatan', 'Sepatan Timur', 'Sindang Jaya', 'Solear', 'Sukadiri',
                'Sukamulya', 'Teluknaga', 'Tigaraksa',
            ],
            // Jawa Barat - Tambahan
            'kota cirebon' => [
                'Cirebon Barat', 'Cirebon Selatan', 'Cirebon Timur', 'Cirebon Utara', 'Harjamukti', 'Kejaksan', 'Lemahwungkuk', 'Pekalipan',
            ],
            'kabupaten cirebon' => [
                'Astanajapura', 'Babakan', 'Bangkan', 'Beber', 'Ciledug', 'Ciledung', 'Ciwaringin', 'Depok', 'Dukupuntang', 'Gebang', 'Greged', 'Gunungjati', 'Jamblang', 'Jatiwangi', 'Karang Wareng', 'Kedawung', 'Klangenan', 'Losari', 'Mundu', 'Pabedilan', 'Pangenan', 'Pabuaran', 'Palimanan', 'Pangkung', 'Pasaleman', 'Plered', 'Plumbon', 'Pondok', 'Sedong', 'Sumber', 'Surabayan', 'Susukan', 'Talun', 'Tengah Tani', 'Waled',
            ],
            'kota tasikmalaya' => [
                'Bungursari', 'Cipedes', 'Indihiang', 'Kawalu', 'Mangkubumi', 'Tawang',
            ],
            'kabupaten tasikmalaya' => [
                'Bancah', 'Banten', 'Bojongasih', 'Cibalong', 'Cibeureum', 'Cicat', 'Cigalontang', 'Cihideung', 'Cikalong', 'Cikembar', 'Cineam', 'Cipatujah', 'Cisayong', 'Cisondari', 'Condong', 'Cungkung', 'Jamanis', 'Karang Jaya', 'Kawalu', 'Kuningan', 'Mangkubumi', 'Manonjaya', 'Mekarmukti', 'Munjar', 'Nyalindung', 'Pacet', 'Panca Lautan', 'Parungkuda', 'Puspahiang', 'Rajadesa', 'Salopa', 'Salawu', 'Sariwangi', 'Sindangbarang', 'Sindangkasih', 'Singaparna', 'Sodonghilir', 'Sukahening', 'Sukaraja', 'Sukaratu', 'Sukarame', 'Sukaresik', 'Sukarasa', 'Sukawangi', 'Sukawening', 'Tambak Dahan', 'Taraju', 'Tawang', 'Tawang Baru', 'Tawang Lama',
            ],
            'kota sukabumi' => [
                'Baros', 'Cibeureum', 'Cikole', 'Cisaat', 'Citamiang', 'Geger Bitung', 'Gunung Puyuh', 'Lembursitu', 'Nyalindung', 'Warudoyong',
            ],
            'kabupaten sukabumi' => [
                'Bojong Genteng', 'Cibadak', 'Cicantayan', 'Cicurug', 'Cidahu', 'Cidolog', 'Ciemas', 'Cikakak', 'Cikembar', 'Cikopo', 'Ciracap', 'Ciranjang', 'Cireunghas', 'Cisaat', 'Cisolok', 'Cisolok Barat', 'Ciwaru', 'Cugeulis', 'Caringin', 'Cibuntu', 'Cidadap', 'Cigombong', 'Cigudeg', 'Cijeruk', 'Cileungsi', 'Ciomas', 'Cisarua', 'Ciseeng', 'Citeureup', 'Dramaga', 'Gunung Putri', 'Gunung Sindur', 'Jasinga', 'Jonggol', 'Kemang', 'Klapanunggal', 'Leuwiliang', 'Leuwisadeng', 'Megamendung', 'Nanggung', 'Pamijahan', 'Parung', 'Parung Panjang', 'Ranca Bungur', 'Rumpin', 'Sukajaya', 'Sukamakmur', 'Sukaraja', 'Tajurhalang', 'Tamansari', 'Tanjungsari', 'Tenjo', 'Tenjolaya',
            ],
            'kota cimahi' => [
                'Cimahi Selatan', 'Cimahi Tengah', 'Cimahi Utara',
            ],
            'kota banjar' => [
                'Banjar', 'Cimahi', 'Pataruman',
            ],
            'kabupaten purwakarta' => [
                'Babakancikao', 'Bojong', 'Campaka', 'Cipatujah', 'Cipicung', 'Daragiri', 'Jatiasih', 'Jatiluhur', 'Karangtengah', 'Kertamukti', 'Lembah', 'Maniis', 'Plered', 'Pondok Salam', 'Purwakarta', 'Sukamakmur', 'Sukamulya', 'Sukatani', 'Sukapura', 'Tegalwaru', 'Wanayasa',
            ],
            'kabupaten karawang' => [
                'Banyusari', 'Batujaya', 'Cibuaya', 'Cikampek', 'Cilamaya Kulon', 'Cilamaya Wetan', 'Cilebar', 'Ciomas', 'Cut Meutia', 'Jatisari', 'Karawang Barat', 'Karawang Timur', 'Kedungwaringin', 'Kertamukti', 'Kertawangi', 'Kedungwaringin', 'Klari', 'Kramat', 'Kramat Selatan', 'Kramat Utara', 'Lembah', 'Lemahabang', 'Majalaya', 'Mekarjaya', 'Pangkalan', 'Pegadungan', 'Pedis', 'Purwasari', 'Rawamerta', 'Rengasdengklok', 'Rengasdengklok Barat', 'Rengasdengklok Timur', 'Sukamakmur', 'Sukamulya', 'Sukatani', 'Sukatani Barat', 'Sukatani Timur', 'Talagasari', 'Tegalwaru', 'Tirtajasa', 'Tirtawangi', 'Wanayasa',
            ],
            'kabupaten indramayu' => [
                'Anjatan', 'Arahan', 'Bantarujeg', 'Bongas', 'Cantigi', 'Cikedung', 'Eretan', 'Gantar', 'Haurgeulis', 'Indramayu', 'Jatibarang', 'Juntinyuat', 'Kandanghaur', 'Karangkab', 'Kedokan Bunder', 'Kertasemaya', 'Krbang', 'Kroya', 'Krangkeng', 'Lelea', 'Lohbener', 'Losarang', 'Pasekan', 'Patrol', 'Sindang', 'Sliyeg', 'Sukagumiwang', 'Sukra', 'Susukan', 'Tanjung', 'Tanjung Cirebon', 'Terisi', 'Trisakti', 'Tukdana', 'Widasari',
            ],
            'kabupaten garut' => [
                'Balubur Limbangan', 'Banjarwangi', 'Bayongbong', 'Bungbulang', 'Caringin', 'Cibiuk', 'Cibatu', 'Cikelet', 'Cikajang', 'Cilawu', 'Cimanuk', 'Cisewu', 'Cisompet', 'Garut', 'Gegerkalong', 'Karangpawitan', 'Kersamanah', 'Lembang', 'Leuwigoong', 'Malangbong', 'Mekarmukti', 'Pamulihan', 'Pameungpeuk', 'Pangatikan', 'Pasirwangi', 'Peundeuy', 'Purwakarta', 'Samarang', 'Selaawi', 'Selaawi Barat', 'Selaawi Timur', 'Selaawi Utara', 'Simpang', 'Sindangkasih', 'Sindangwangi', 'Singajaya', 'Sukaratu', 'Sukaresmi', 'Sukaraja', 'Sukarame', 'Sukawangi', 'Sukawening', 'Sukawiras', 'Talegong', 'Tarogong Kaler', 'Tarogong Kidul', 'Wanaraja',
            ],
            'kabupaten ciamis' => [
                'Baregbeg', 'Ciamis', 'Cibatu', 'Cidolog', 'Cigugur', 'Cijulang', 'Cikoneng', 'Cimaragas', 'Cipaku', 'Cisaga', 'Cihaurbeuti', 'Jatinagara', 'Kawali', 'Lakbok', 'Lumbung', 'Majalengka', 'Pamarican', 'Panawangan', 'Panjalu', 'Panumbangan', 'Purwadadi', 'Rancah', 'Rancah Barat', 'Rancah Timur', 'Sadananya', 'Sindangbarang', 'Sindangkasih', 'Sukadana', 'Sukaraja', 'Sukaraja Barat', 'Sukaraja Timur', 'Sukaratu', 'Tambaksari', 'Tambaksari Barat', 'Tambaksari Timur', 'Tegaltamiang', 'Tenggarong',
            ],
            'kabupaten cianjur' => [
                'Agrabinta', 'Campaka', 'Campaka Mulya', 'Cibeber', 'Cibinong', 'Cidadap', 'Cijati', 'Cikadu', 'Cikalong Kulon', 'Cikalong Wetan', 'Cikijung', 'Cilaku', 'Cimerak', 'Cipanas', 'Ciranjang', 'Cisewu', 'Ciwidey', 'Cugenang', 'Cianjur', 'Gekbrong', 'Gekbrong Barat', 'Gekbrong Timur', 'Haurwangi', 'Ibu', 'Kadupandak', 'Karangtengah', 'Kertasari', 'Leles', 'Mande', 'Naringgul', 'Nyalindung', 'Pacat', 'Pagelaran', 'Pasirjambu', 'Plawad', 'Purwani', 'Rancabali', 'Sindangbarang', 'Sindangkasih', 'Sindangsari', 'Sindangwangi', 'Sindangsuka', 'Sindangwangi', 'Sindangwangi Barat', 'Sindangwangi Timur', 'Sindangwangi Utara', 'Sindangsuka', 'Sukaluyu', 'Sukanagara', 'Sukaraja', 'Sukaratu', 'Sukawangi', 'Sukawening', 'Sukawiras', 'Sukawiras Barat', 'Sukawiras Timur', 'Sukawiras Utara', 'Takokak', 'Tanggeung', 'Tenggarong', 'Warungkondang',
            ],
            'kabupaten kuningan' => [
                'Ciawigebang', 'Cibingbin', 'Cigandamekar', 'Cigugur', 'Cilimus', 'Cimanggu', 'Cipicung', 'Cipondoh', 'Cisitu', 'Ciwaringin', 'Darmaraja', 'Darul Hikmah', 'Garawangi', 'Jalaksana', 'Japara', 'Jatiluhur', 'Kuningan', 'Kuningan Barat', 'Kuningan Selatan', 'Kuningan Utara', 'Lebakwangi', 'Luragung', 'Mandalajati', 'Margaasih', 'Maleber', 'Mandirancan', 'Nusa Hati', 'Nusa Hati Selatan', 'Nusa Hati Utara', 'Nusa Hati Timur', 'Nusa Hati Barat', 'Salawu', 'Sanggalanggar', 'Selaawi', 'Selaawi Barat', 'Selaawi Timur', 'Selaawi Utara', 'Sentong', 'Sindangwangi', 'Sindangwangi Barat', 'Sindangwangi Timur', 'Sindangwangi Utara', 'Sukajadi', 'Sukaraja', 'Sukaratu', 'Sukawangi', 'Sukawening', 'Sukawiras', 'Sukawiras Barat', 'Sukawiras Timur', 'Sukawiras Utara', 'Sukawening Barat', 'Sukawening Timur', 'Sukawening Utara', 'Sukawiras Barat', 'Sukawiras Timur', 'Sukawiras Utara', 'Talagawangi', 'Talagawangi Barat', 'Talagawangi Timur', 'Talagawangi Utara', 'Tebuah', 'Tegalwaru', 'Tirtawangi', 'Tirtawangi Barat', 'Tirtawangi Timur', 'Tirtawangi Utara', 'Wanayasa',
            ],
            'kabupaten majalengka' => [
                'Argapura', 'Cigasong', 'Cigugur', 'Cikijing', 'Cimanuk', 'Cisitu', 'Dawek', 'Jatiwangi', 'Kadipaten', 'Karanganyar', 'Kertajati', 'Kertajati Barat', 'Kertajati Timur', 'Kertajati Utara', 'Kertajati Selatan', 'Lemahabang', 'Leuwimunding', 'Majalengka', 'Maja', 'Maja Selatan', 'Maja Utara', 'Maja Timur', 'Maja Barat', 'Palasah', 'Palasah Barat', 'Palasah Timur', 'Palasah Utara', 'Palasah Selatan', 'Panyingkiran', 'Rajagaluh', 'Sindang', 'Sindang Barat', 'Sindang Timur', 'Sindang Utara', 'Sindang Selatan', 'Sukahaji', 'Sukaraja', 'Sukaratu', 'Sukawangi', 'Sukawening', 'Sukawiras', 'Sukawiras Barat', 'Sukawiras Timur', 'Sukawiras Utara', 'Sukawiras Barat', 'Sukawiras Timur', 'Sukawiras Utara', 'Sukawiringin', 'Talagabodas', 'Tanjung', 'Tanjung Barat', 'Tanjung Timur', 'Tanjung Utara', 'Tanjung Selatan',
            ],
            'kabupaten pangandaran' => [
                'Banjarsari', 'Batu Lawang', 'Batu Lawang Barat', 'Batu Lawang Timur', 'Batu Lawang Utara', 'Batu Lawang Selatan', 'Cigalontang', 'Cijulang', 'Cimerak', 'Cimunding', 'Ciputri', 'Ciputri Barat', 'Ciputri Timur', 'Ciputri Utara', 'Ciputri Selatan', 'Ciputri Barat', 'Ciputri Timur', 'Ciputri Utara', 'Ciputri Selatan', 'Cisitu', 'Cisitu Barat', 'Cisitu Timur', 'Cisitu Utara', 'Cisitu Selatan', 'Cisitu Barat', 'Cisitu Timur', 'Cisitu Utara', 'Cisitu Selatan', 'Cisitu Barat', 'Cisitu Timur', 'Cisitu Utara', 'Cisitu Selatan', 'Kalipucang', 'Kalipucang Barat', 'Kalipucang Timur', 'Kalipucang Utara', 'Kalipucang Selatan', 'Mangunjaya', 'Mangunjaya Barat', 'Mangunjaya Timur', 'Mangunjaya Utara', 'Mangunjaya Selatan', 'Padaherang', 'Padaherang Barat', 'Padaherang Timur', 'Padaherang Utara', 'Padaherang Selatan', 'Padaherang Barat', 'Padaherang Timur', 'Padaherang Utara', 'Padaherang Selatan', 'Panjalu', 'Panjalu Barat', 'Panjalu Timur', 'Panjalu Utara', 'Panjalu Selatan', 'Parigi', 'Parigi Barat', 'Parigi Timur', 'Parigi Utara', 'Parigi Selatan', 'Purworejo', 'Purworejo Barat', 'Purworejo Timur', 'Purworejo Utara', 'Purworejo Selatan', 'Sidamulih', 'Sidamulih Barat', 'Sidamulih Timur', 'Sidamulih Utara', 'Sidamulih Selatan', 'Sindangbarang', 'Sindangkasih', 'Sindangwangi', 'Sindangwangi Barat', 'Sindangwangi Timur', 'Sindangwangi Utara', 'Sindangwangi Selatan',
            ],

            // DKI Jakarta
            'kota jakarta pusat' => [
                'Cempaka Putih', 'Gambir', 'Johar Baru', 'Kemayoran', 'Menteng',
                'Sawah Besar', 'Senen', 'Tanah Abang',
            ],
            'kota jakarta barat' => [
                'Cengkareng', 'Grogol Petamburan', 'Kalideres', 'Kebon Jeruk', 'Kembangan',
                'Palmerah', 'Taman Sari', 'Tambora',
            ],
            'kota jakarta selatan' => [
                'Cilandak', 'Jagakarsa', 'Kebayoran Baru', 'Kebayoran Lama', 'Mampang Prapatan',
                'Pancoran', 'Pasar Minggu', 'Pesanggrahan', 'Setiabudi', 'Tebet',
            ],
            'kota jakarta timur' => [
                'Cakung', 'Cipayung', 'Ciracas', 'Duren Sawit', 'Jatinegara',
                'Kramat Jati', 'Makasar', 'Matraman', 'Pasar Rebo', 'Pulo Gadung',
            ],
            'kota jakarta utara' => [
                'Cilincing', 'Kelapa Gading', 'Koja', 'Pademangan', 'Penjaringan', 'Tanjung Priok',
            ],

            // Jawa Tengah & DIY
            'kota semarang' => [
                'Banyumanik', 'Candisari', 'Gajahmungkur', 'Gayamsari', 'Genuk', 'Gunungpati',
                'Mijen', 'Ngaliyan', 'Pedurungan', 'Semarang Barat', 'Semarang Selatan',
                'Semarang Tengah', 'Semarang Timur', 'Semarang Utara', 'Tembalang', 'Tugu',
            ],
            'kota surakarta' => [
                'Banjarsari', 'Jebres', 'Laweyan', 'Pasar Kliwon', 'Serengan',
            ],
            'kota yogyakarta' => [
                'Danurejan', 'Gedongtengen', 'Gondokusuman', 'Gondomanan', 'Jetis',
                'Kotagede', 'Kraton', 'Mantrijeron', 'Mergangsan', 'Ngampilan',
                'Pakualaman', 'Tegalrejo', 'Umbulharjo', 'Wirobrajan',
            ],
            'kabupaten sleman' => [
                'Berbah', 'Cangkringan', 'Depok', 'Gamping', 'Godean', 'Kalasan',
                'Minggir', 'Mlati', 'Moyudan', 'Ngaglik', 'Ngemplak', 'Pakem',
                'Prambanan', 'Seyegan', 'Sleman', 'Tempel', 'Turi',
            ],
            'kabupaten bantul' => [
                'Bambanglipuro', 'Banguntapan', 'Bantul', 'Dlingo', 'Imogiri', 'Jetis',
                'Kasihan', 'Kretek', 'Pajangan', 'Pandak', 'Piyungan', 'Pleret',
                'Pundong', 'Sanden', 'Sedayu', 'Sewon', 'Srandakan',
            ],

            // Jawa Timur
            'kota surabaya' => [
                'Asemrowo', 'Benowo', 'Bubutan', 'Bulak', 'Dukuh Pakis', 'Gayungan',
                'Genteng', 'Gubeng', 'Gunung Anyar', 'Jambangan', 'Karang Pilang',
                'Kenjeran', 'Krembangan', 'Lakarsantri', 'Mulyorejo', 'Pabean Cantian',
                'Pakal', 'Rungkut', 'Sambikerep', 'Sawahan', 'Semampir', 'Simokerto',
                'Sukolilo', 'Sukomanunggal', 'Tambaksari', 'Tandes', 'Tegalsari',
                'Tenggilis Mejoyo', 'Wiyung', 'Wonocolo', 'Wonokromo',
            ],
            'kota malang' => [
                'Blimbing', 'Kedungkandang', 'Klojen', 'Lowokwaru', 'Sukun',
            ],
            'kabupaten sidoarjo' => [
                'Balongbendo', 'Buduran', 'Candi', 'Gedangan', 'Jabon', 'Krembung',
                'Krian', 'Prambon', 'Porong', 'Sedati', 'Sidoarjo', 'Sukodono',
                'Taman', 'Tanggulangin', 'Tarik', 'Tulangan', 'Waru', 'Wonoayu',
            ],

            // Bali & Lainnya
            'kota denpasar' => [
                'Denpasar Barat', 'Denpasar Selatan', 'Denpasar Timur', 'Denpasar Utara',
            ],
            'kabupaten badung' => [
                'Abiansemal', 'Kuta', 'Kuta Selatan', 'Kuta Utara', 'Mengwi', 'Petang',
            ],
            'kabupaten gianyar' => [
                'Gianyar', 'Blahbatuh', 'Payangan', 'Sukawati', 'Tegallalang',
            ],
            'kabupaten buleleng' => [
                'Buleleng', 'Banjar', 'Busungbiu', 'Gerokgak', 'Kubutambahan', 'Sawan', 'Seririt', 'Sukasada', 'Tejakula',
            ],
            'kota medan' => [
                'Medan Amplas', 'Medan Area', 'Medan Barat', 'Medan Baru', 'Medan Belawan',
                'Medan Deli', 'Medan Denai', 'Medan Helvetia', 'Medan Johor', 'Medan Kota',
                'Medan Labuhan', 'Medan Maimun', 'Medan Marelan', 'Medan Perjuangan',
                'Medan Petisah', 'Medan Polonia', 'Medan Selayang', 'Medan Sunggal',
                'Medan Tembung', 'Medan Timur', 'Medan Tuntungan',
            ],
            'kota makassar' => [
                'Biringkanaya', 'Bontoala', 'Kepulauan Sangkarrang', 'Makassar', 'Mamajang',
                'Manggala', 'Mariso', 'Panakkukang', 'Rappocini', 'Tallo',
                'Tamalanrea', 'Tamalate', 'Ujung Pandang', 'Ujung Tanah', 'Wajo',
            ],
            'kota palembang' => [
                'Alang-Alang Lebar', 'Bukit Kecil', 'Gandus', 'Ilir Barat I', 'Ilir Barat II',
                'Ilir Timur I', 'Ilir Timur II', 'Ilir Timur III', 'Jakabaring', 'Kalidoni',
                'Kemuning', 'Kertapati', 'Plaju', 'Rambutan', 'Sako', 'Seberang Ulu I',
                'Seberang Ulu II', 'Sematang Borang', 'Sukarami',
            ],
            'kota semarang' => [
                'Banyumanik', 'Candisari', 'Gajahmungkur', 'Gayamsari', 'Genuk', 'Gunungpati',
                'Mijen', 'Ngaliyan', 'Pedurungan', 'Semarang Barat', 'Semarang Selatan',
                'Semarang Tengah', 'Semarang Timur', 'Semarang Utara', 'Tembalang', 'Tugu',
            ],
            'kota salatiga' => [
                'Argomulyo', 'Sidomukti', 'Sidorejo', 'Tingkir',
            ],
            'kota magelang' => [
                'Magelang Selatan', 'Magelang Utara',
            ],
            'kota pekalongan' => [
                'Kandangserang', 'Kedungwuni', 'Pekalongan Barat', 'Pekalongan Timur', 'Tirto',
            ],
            'kota tegal' => [
                'Margadana', 'Tegal Barat', 'Tegal Selatan', 'Tegal Timur',
            ],
            'kota surakarta' => [
                'Banjarsari', 'Jebres', 'Laweyan', 'Pasar Kliwon', 'Serengan',
            ],
            'kota yogyakarta' => [
                'Danurejan', 'Gedongtengen', 'Gondokusuman', 'Gondomanan', 'Jetis',
                'Kotagede', 'Kraton', 'Mantrijeron', 'Mergangsan', 'Ngampilan',
                'Pakualaman', 'Tegalrejo', 'Umbulharjo', 'Wirobrajan',
            ],
            'kabupaten sleman' => [
                'Berbah', 'Cangkringan', 'Depok', 'Gamping', 'Godean', 'Kalasan',
                'Minggir', 'Mlati', 'Moyudan', 'Ngaglik', 'Ngemplak', 'Pakem',
                'Prambanan', 'Seyegan', 'Sleman', 'Tempel', 'Turi',
            ],
            'kabupaten bantul' => [
                'Bambanglipuro', 'Banguntapan', 'Bantul', 'Dlingo', 'Imogiri', 'Jetis',
                'Kasihan', 'Kretek', 'Pajangan', 'Pandak', 'Piyungan', 'Pleret',
                'Pundong', 'Sanden', 'Sedayu', 'Sewon', 'Srandakan',
            ],
            'kabupaten kulon progo' => [
                'Girimulyo', 'Kalibawang', 'Kokap', 'Lendah', 'Nanggulan', 'Panjatan', 'Pengasih', 'Sentolo', 'Temon', 'Wates',
            ],
            'kabupaten gunungkidul' => [
                'Giriwoyo', 'Karangmojo', 'Ngawen', 'Nglipar', 'Paliyan', 'Panggang', 'Patuk', 'Playen', 'Ponjong', 'Purwosari', 'Rongkop', 'Saptosari', 'Semo', 'Semin', 'Tanjungsari', 'Tepus', 'Wonosari',
            ],
            'kota surabaya' => [
                'Asemrowo', 'Benowo', 'Bubutan', 'Bulak', 'Dukuh Pakis', 'Gayungan',
                'Genteng', 'Gubeng', 'Gunung Anyar', 'Jambangan', 'Karang Pilang',
                'Kenjeran', 'Krembangan', 'Lakarsantri', 'Mulyorejo', 'Pabean Cantian',
                'Pakal', 'Rungkut', 'Sambikerep', 'Sawahan', 'Semampir', 'Simokerto',
                'Sukolilo', 'Sukomanunggal', 'Tambaksari', 'Tandes', 'Tegalsari',
                'Tenggilis Mejoyo', 'Wiyung', 'Wonocolo', 'Wonokromo',
            ],
            'kota malang' => [
                'Blimbing', 'Kedungkandang', 'Klojen', 'Lowokwaru', 'Sukun',
            ],
            'kota batu' => [
                'Batu', 'Junrejo', 'Pujon',
            ],
            'kota kediri' => [
                'Kediri', 'Mojoroto', 'Pesantren',
            ],
            'kota madiun' => [
                'Kartoharjo', 'Mangunharjo', 'Taman',
            ],
            'kota blitar' => [
                'Kepanjenkidul', 'Sanankulon', 'Sukorejo',
            ],
            'kabupaten sidoarjo' => [
                'Balongbendo', 'Buduran', 'Candi', 'Gedangan', 'Jabon', 'Krembung',
                'Krian', 'Prambon', 'Porong', 'Sedati', 'Sidoarjo', 'Sukodono',
                'Taman', 'Tanggulangin', 'Tarik', 'Tulangan', 'Waru', 'Wonoayu',
            ],
            'kabupaten malang' => [
                'Ampelgading', 'Bantur', 'Bululawang', 'Dampit', 'Donomulyo', 'Gadang', 'Gondanglegi', 'Jabung', 'Karumayan', 'Kasembon', 'Kepanjen', 'Kromengan', 'Lawang', 'Ngajum', 'Pagak', 'Pagelaran', 'Pakis', 'Pakisaji', 'Poncokusumo', 'Pujon', 'Singosari', 'Sumbermanjing Wetan', 'Tajinan', 'Tirtoyudo', 'Tumpang', 'Turen', 'Wagir', 'Wajak',
            ],
            'kabupaten jember' => [
                'Ajung', 'Ambulu', 'Arjasa', 'Balung', 'Bangsalsari', 'Gumukmas', 'Jember', 'Jenggawah', 'Kalisat', 'Kaliwates', 'Kencong', 'Ledokombo', 'Mayang', 'Mumbulsari', 'Panti', 'Patrang', 'Puger', 'Rambipuji', 'Semboro', 'Silo', 'Sukorambi', 'Sukowono', 'Sumberbaru', 'Sumberjambe', 'Tanggul', 'Umbulsari', 'Wuluhan',
            ],
            'kabupaten banyuwangi' => [
                'Bangorejo', 'Banyuwangi', 'Blimbingsari', 'Cluring', 'Gambiran', 'Genteng', 'Giri', 'Glagah', 'Kabat', 'Kalibaru', 'Kebonrejo', 'Licin', 'Muncar', 'Pesanggaran', 'Purwoharjo', 'Rogojampi', 'Sempu', 'Siliragung', 'Songgon', 'Srono', 'Tegaldlimo', 'Tegalsari', 'Wongsorejo',
            ],
            'kota denpasar' => [
                'Denpasar Barat', 'Denpasar Selatan', 'Denpasar Timur', 'Denpasar Utara',
            ],
            'kabupaten badung' => [
                'Abiansemal', 'Kuta', 'Kuta Selatan', 'Kuta Utara', 'Mengwi', 'Petang',
            ],
            'kabupaten gianyar' => [
                'Gianyar', 'Blahbatuh', 'Payangan', 'Sukawati', 'Tegallalang',
            ],
            'kabupaten tabanan' => [
                'Baturiti', 'Kerambitan', 'Kediri', 'Marga', 'Penebel', 'Selemadeg', 'Selemadeg Barat', 'Selemadeg Timur', 'Tabanan',
            ],
            'kabupaten karangasem' => [
                'Amlapura', 'Bebandem', 'Beu', 'Karangasem', 'Kubu', 'Manggis', 'Rendang', 'Selat', 'Sidemen',
            ],
            'kabupaten buleleng' => [
                'Buleleng', 'Banjar', 'Busungbiu', 'Gerokgak', 'Kubutambahan', 'Sawan', 'Seririt', 'Sukasada', 'Tejakula',
            ],
            'kota medan' => [
                'Medan Amplas', 'Medan Area', 'Medan Barat', 'Medan Baru', 'Medan Belawan',
                'Medan Deli', 'Medan Denai', 'Medan Helvetia', 'Medan Johor', 'Medan Kota',
                'Medan Labuhan', 'Medan Maimun', 'Medan Marelan', 'Medan Perjuangan',
                'Medan Petisah', 'Medan Polonia', 'Medan Selayang', 'Medan Sunggal',
                'Medan Tembung', 'Medan Timur', 'Medan Tuntungan',
            ],
            'kota binjai' => [
                'Binjai Selatan', 'Binjai Utara',
            ],
            'kota pekanbaru' => [
                'Bukit Raya', 'Lima Puluh', 'Marpoyan Damai', 'Payung Sekaki', 'Rumbai', 'Rumbai Pesisir', 'Senapelan', 'Sukajadi', 'Tampan', 'Tenayan Raya',
            ],
            'kota dumai' => [
                'Dumai Barat', 'Dumai Selatan', 'Dumai Timur', 'Medang Kampai',
            ],
            'kota padang' => [
                'Bungus Teluk Kabung', 'Kuranji', 'Lubuk Begalung', 'Lubuk Kilangan', 'Nanggalo', 'Padang Barat', 'Padang Selatan', 'Padang Timur', 'Padang Utara', 'Pauh',
            ],
            'kota bukittinggi' => [
                'Guguk Panjang', 'Mandiangin Koto Selayan',
            ],
            'kota jambi' => [
                'Alam Barajo', 'Danau Sipin', 'Jelutung', 'Kota Baru', 'Pasar Jambi', 'Pelayangan', 'Telanaipura',
            ],
            'kota sungai penuh' => [
                'Hamparan Rawang', 'Koto Baru', 'Pondok Tinggi', 'Sungai Penuh',
            ],
            'kota bengkulu' => [
                'Gading Cempaka', 'Kampung Melayu', 'Ratu Agung', 'Ratu Samban', 'Selebar', 'Singaran Pati', 'Sungai Serut', 'Teluk Segara',
            ],
            'kota bandar lampung' => [
                'Bumi Waras', 'Enggal', 'Kedamaian', 'Kedaton', 'Kemiling', 'Kemiling Barat', 'Kemiling Selatan', 'Kemiling Utara', 'Labuhan Ratu', 'Langkapura', 'Panjang', 'Rajabasa', 'Sukabumi', 'Sukarame', 'Tanjung Karang Barat', 'Tanjung Karang Pusat', 'Tanjung Karang Timur', 'Way Halim',
            ],
            'kota metro' => [
                'Metro Barat', 'Metro Pusat', 'Metro Selatan', 'Metro Timur', 'Metro Utara',
            ],
            'kota pangkalpinang' => [
                'Bukit Intan', 'Gabek', 'Gerunggang', 'Rangkui', 'Taman Sari',
            ],
            'kota batam' => [
                'Batam Kota', 'Batu Aji', 'Batu Ampar', 'Belakang Padang', 'Bulang', 'Galang', 'Nongsa', 'Sagulung', 'Sei Beduk', 'Sekupang', 'Tanjung Uma',
            ],
            'kota tanjungpinang' => [
                'Bukit Bestari', 'Tanjungpinang Barat', 'Tanjungpinang Kota', 'Tanjungpinang Timur',
            ],
            'kota pontianak' => [
                'Pontianak Barat', 'Pontianak Kota', 'Pontianak Kota', 'Pontianak Selatan', 'Pontianak Timur', 'Pontianak Utara',
            ],
            'kota banjarmasin' => [
                'Banjarmasin Tengah', 'Banjarmasin Utara', 'Banjarmasin Selatan', 'Banjarmasin Timur', 'Banjarmasin Barat',
            ],
            'kota banjarbaru' => [
                'Banjarmasin', 'Cempaka', 'Landasan Ulin', 'Liang Anggang',
            ],
            'kota palangka raya' => [
                'Bukit Batu', 'Jekan Raya', 'Pahandut', 'Rakumpit', 'Sebangau',
            ],
            'kota samarinda' => [
                'Loa Janan', 'Loa Janan Ilir', 'Palaran', 'Samarinda Ilir', 'Samarinda Kota', 'Samarinda Seberang', 'Samarinda Ulu', 'Sungai Kunjang', 'Sungai Pinang',
            ],
            'kota balikpapan' => [
                'Balikpapan Barat', 'Balikpapan Kota', 'Balikpapan Selatan', 'Balikpapan Tengah', 'Balikpapan Timur', 'Balikpapan Utara',
            ],
            'kota tarakan' => [
                'Tarakan Barat', 'Tarakan Tengah', 'Tarakan Timur', 'Tarakan Utara',
            ],
            'kota makassar' => [
                'Biringkanaya', 'Bontoala', 'Kepulauan Sangkarrang', 'Makassar', 'Mamajang',
                'Manggala', 'Mariso', 'Panakkukang', 'Rappocini', 'Tallo',
                'Tamalanrea', 'Tamalate', 'Ujung Pandang', 'Ujung Tanah', 'Wajo',
            ],
            'kota parepare' => [
                'Bacukiki', 'Bacukiki Barat', 'Soreang', 'Ujung',
            ],
            'kota palopo' => [
                'Bara', 'Malawe', 'Telluwanua', 'Wara', 'Wara Selatan', 'Wara Timur', 'Wara Utara',
            ],
            'kota manado' => [
                'Bunaken', 'Bunaken Kepulauan', 'Malalayang', 'Mapanget', 'Sario', 'Sario Barat', 'Sario Utara', 'Singkil', 'Tikala', 'Tuminiting', 'Wanea', 'Wenang',
            ],
            'kota bitung' => [
                'Aertembaga', 'Girian', 'Lembeh Selatan', 'Lembeh Utara', 'Madidir', 'Maesa', 'Matui', 'Ranowangko', 'Ranowangko Barat', 'Ranowangko Utara',
            ],
            'kota tomohon' => [
                'Tomohon Barat', 'Tomohon Selatan', 'Tomohon Tengah', 'Tomohon Timur', 'Tomohon Utara',
            ],
            'kota kotamobagu' => [
                'Kotamobagu Barat', 'Kotamobagu Selatan', 'Kotamobagu Timur', 'Kotamobagu Utara',
            ],
            'kota palu' => [
                'Mantikulore', 'Tatanga', 'Tawaeli', 'Ulujadi',
            ],
            'kota kendari' => [
                'Abeli', 'Kadia', 'Kambu', 'Kambu Utara', 'Kendari', 'Kendari Barat', 'Mandonga', 'Poasia', 'Puuwatu', 'Wua-Wua',
            ],
            'kota baubau' => [
                'Batupoaro', 'Betoambari', 'Kokalukuna', 'Lea-Lea', 'Murhum', 'Sorawolio', 'Wolio',
            ],
            'kota ambon' => [
                'Nusaniwe', 'Sirimau', 'Teluk Ambon', 'Teluk Ambon Bagian Barat',
            ],
            'kota tual' => [
                'Dullah Selatan', 'Dullah Utara', 'Kur Selatan', 'Kur Utara', 'Pulau Dullah', 'Pulau-Pulau Banda', 'Tayando Tam',
            ],
            'kota jayapura' => [
                'Abepura', 'Heram', 'Jayapura Selatan', 'Jayapura Utara', 'Muara Tami',
            ],
            'kota sorong' => [
                'Klabo', 'Klamono', 'Malaimsimsa', 'Malaumkarta', 'Marioriwawo', 'Moisigen', 'Sorong', 'Sorong Barat', 'Sorong Kepulauan', 'Sorong Selatan', 'Sorong Timur', 'Sorong Utara',
            ],
            'kota manokwari' => [
                'Manokwari Barat', 'Manokwari Selatan', 'Manokwari Timur', 'Manokwari Utara', 'Prafi', 'Sidey', 'Warmare',
            ],
            'kota madiun' => [
                'Kartoharjo', 'Mangunharjo', 'Taman',
            ],
            'kota mataram' => [
                'Ampenan', 'Cakranegara', 'Selaparang', 'Sekarbela',
            ],
            'kota kupang' => [
                'Alak', 'Kelapa Lima', 'Kota Lama', 'Kota Raja', 'Maulafa', 'Oebobo', 'Oebufu',
            ],
            'kota bima' => [
                'Asakota', 'Bolo', 'Mpunda', 'Raba', 'Rasanae Barat', 'Rasanae Timur',
            ],
        ];

        foreach ($districtsMap as $cityName => $districts) {
            if ($c === $cityName || str_contains($c, $cityName) || str_contains($cityName, $c)) {
                sort($districts, SORT_NATURAL | SORT_FLAG_CASE);

                return $districts;
            }
        }

        return [];
    }
}
