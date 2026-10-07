@component('layouts.storefront', ['title' => 'Kebijakan Privasi - Pixel Komunika', 'metaDescription' => 'Cara Pixel Komunika mengumpulkan, memakai, menyimpan, dan melindungi data pribadi pelanggan.'])
<x-legal-document heading="Kebijakan Privasi">
    <p>
        Kebijakan ini menjelaskan data pribadi apa yang dikumpulkan Pixel Komunika (selanjutnya "kami") melalui toko online ini,
        untuk apa data itu dipakai, dengan siapa dibagikan, berapa lama disimpan, dan hak Anda atas data tersebut.
        Kebijakan ini disusun dengan mengacu pada Undang-Undang Nomor 27 Tahun 2022 tentang Pelindungan Data Pribadi,
        Undang-Undang Informasi dan Transaksi Elektronik (UU 11/2008 sebagaimana diubah terakhir dengan UU 1/2024),
        Peraturan Pemerintah Nomor 71 Tahun 2019 tentang Penyelenggaraan Sistem dan Transaksi Elektronik, dan
        Peraturan Pemerintah Nomor 80 Tahun 2019 tentang Perdagangan Melalui Sistem Elektronik.
    </p>

    <h2>1. Pengendali data</h2>
    <p>Pengendali data pribadi untuk toko online ini adalah Pixel Komunika. Kontak untuk semua urusan data pribadi tercantum di bagian akhir halaman ini.</p>

    <h2>2. Data yang kami kumpulkan</h2>
    <ul>
        <li><strong>Data akun:</strong> nama, nama usaha, alamat email, nomor telepon, dan kata sandi (disimpan dalam bentuk hash, tidak pernah dalam teks asli).</li>
        <li><strong>Data pengiriman:</strong> nama dan nomor telepon penerima, alamat lengkap, kecamatan, kota/kabupaten, provinsi, dan kode pos.</li>
        <li><strong>Data transaksi:</strong> pesanan, produk, jumlah, harga, PPh 22, ongkos kirim, invoice, dan status pesanan.</li>
        <li><strong>Data pembayaran:</strong> nama bank pengirim, nama pemilik rekening, nominal transfer, dan file bukti transfer yang Anda unggah. Kami tidak meminta atau menyimpan PIN, kata sandi perbankan, atau data kartu.</li>
        <li><strong>Data teknis:</strong> alamat IP, jenis peramban (user agent), dan waktu login atau aksi penting, untuk keamanan dan jejak audit.</li>
    </ul>

    <h2>3. Tujuan dan dasar pemrosesan</h2>
    <ul>
        <li>Memverifikasi pendaftaran dan menentukan status akun reseller.</li>
        <li>Menampilkan harga, memproses pesanan, membuat invoice, dan mengirim barang. Dasarnya adalah pelaksanaan perjanjian jual beli dengan Anda.</li>
        <li>Menghitung dan mencantumkan PPh 22 serta menyimpan dokumen transaksi. Dasarnya adalah kewajiban hukum perpajakan dan pembukuan.</li>
        <li>Mengirim konfirmasi pesanan atau penerimaan barang melalui WhatsApp dan menghubungi Anda bila ada kendala pesanan.</li>
        <li>Menjaga keamanan akun dan sistem, mencegah penyalahgunaan, dan mencatat jejak audit aksi penting. Dasarnya adalah kepentingan yang sah.</li>
    </ul>
    <p>Kami tidak memakai data Anda untuk iklan pihak ketiga dan tidak menjual data pribadi.</p>

    <h2>4. Pihak yang menerima data</h2>
    <ul>
        <li><strong>Biteship dan kurir mitranya</strong> (misalnya ekspedisi reguler, Grab, Gojek): alamat dan kontak penerima serta berat paket, untuk menghitung ongkir dan mengirim barang bila Anda memilih ekspedisi selain kurir toko.</li>
        <li><strong>Penyedia layanan WhatsApp (Fonnte):</strong> nomor telepon dan isi pesan notifikasi pesanan.</li>
        <li><strong>Sistem kasir (POS) toko:</strong> data pesanan untuk pencatatan penjualan dan stok.</li>
        <li><strong>Penyedia hosting</strong> tempat sistem ini berjalan.</li>
        <li><strong>Instansi pemerintah atau aparat penegak hukum</strong> bila diwajibkan oleh peraturan perundang-undangan.</li>
    </ul>

    <h2>5. Penyimpanan dan keamanan</h2>
    <ul>
        <li>Bukti transfer disimpan sekurang-kurangnya {{ config('store.payment_proof_retain_years') }} tahun sejak diunggah.</li>
        <li>Data pesanan, invoice, dan pembayaran disimpan selama diperlukan untuk kewajiban pembukuan dan perpajakan, termasuk setelah akun ditutup.</li>
        <li>Data akun dan alamat disimpan selama akun aktif atau sampai Anda meminta penghapusan, dengan batasan pada poin sebelumnya.</li>
        <li>File bukti transfer disimpan di penyimpanan privat yang hanya dapat dibuka admin. Akses panel admin dibatasi dan aksi penting tercatat di log audit.</li>
    </ul>

    <h2>6. Cookie</h2>
    <p>Kami hanya memakai cookie yang diperlukan agar situs berfungsi: cookie sesi login, cookie keamanan formulir (CSRF), dan cookie "Ingat saya" bila Anda mencentangnya saat masuk. Kami tidak memakai cookie analitik atau iklan.</p>

    <h2>7. Hak Anda</h2>
    <p>Sesuai UU Pelindungan Data Pribadi, Anda berhak:</p>
    <ul>
        <li>meminta informasi dan salinan data pribadi Anda;</li>
        <li>memperbaiki data yang tidak akurat (nama, nama usaha, telepon, dan alamat dapat diubah sendiri di menu Akun);</li>
        <li>meminta penghapusan atau pemusnahan data, kecuali data yang wajib kami simpan menurut hukum;</li>
        <li>menarik persetujuan atau mengajukan keberatan atas pemrosesan tertentu;</li>
        <li>meminta data Anda dalam format yang dapat dibaca mesin.</li>
    </ul>
    <p>Ajukan permintaan melalui WhatsApp atau email di bawah. Kami dapat meminta verifikasi identitas lebih dulu. Permintaan ditindaklanjuti paling lambat 3 x 24 jam sejak identitas terverifikasi, sesuai jangka waktu dalam UU Pelindungan Data Pribadi.</p>

    <h2>8. Bila terjadi kegagalan pelindungan data</h2>
    <p>Bila terjadi kegagalan pelindungan data pribadi, kami memberi tahu pemilik data yang terdampak secara tertulis paling lambat 3 x 24 jam, berisi data yang terungkap, waktu dan cara kejadian, serta langkah penanganan dan pemulihan yang kami lakukan.</p>

    <h2>9. Perubahan kebijakan</h2>
    <p>Kebijakan ini dapat diperbarui mengikuti perubahan layanan atau peraturan. Tanggal berlaku di bagian atas halaman menunjukkan versi terakhir. Perubahan yang memengaruhi hak Anda akan diumumkan di situs ini.</p>

    <p>Lihat juga <a href="{{ route('legal.terms') }}">Syarat dan Ketentuan</a>.</p>
</x-legal-document>
@endcomponent
