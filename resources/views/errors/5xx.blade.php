@include('errors.layout', [
    'code' => $exception->getStatusCode(),
    'title' => $exception->getStatusCode() === 503 ? 'Sedang dalam perawatan' : 'Terjadi kesalahan di server',
    'message' => $exception->getStatusCode() === 503
        ? 'Toko sedang diperbarui. Coba lagi beberapa menit lagi.'
        : 'Permintaan Anda gagal diproses. Coba lagi; bila masih gagal, hubungi admin lewat WhatsApp.',
])
