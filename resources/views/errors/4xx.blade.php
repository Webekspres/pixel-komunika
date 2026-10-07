@php
    $code = $exception->getStatusCode();
    [$title, $message] = match ($code) {
        404 => ['Halaman tidak ditemukan', 'Alamat ini tidak ada atau produknya sudah tidak ditampilkan. Cari produk lain dari katalog.'],
        403 => ['Akses tidak diizinkan', 'Akun Anda belum punya akses ke halaman ini. Masuk dengan akun yang sesuai atau kembali ke beranda.'],
        419 => ['Sesi berakhir', 'Halaman terlalu lama dibuka sehingga sesinya habis. Muat ulang halaman lalu coba lagi.'],
        429 => ['Terlalu banyak permintaan', 'Tunggu sebentar sebelum mencoba lagi.'],
        default => ['Permintaan tidak dapat diproses', 'Periksa kembali alamat atau data yang dikirim, lalu coba lagi.'],
    };
@endphp
@include('errors.layout', ['code' => $code, 'title' => $title, 'message' => $message])
