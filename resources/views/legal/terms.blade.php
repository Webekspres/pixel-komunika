@php
    $partaiMinimum = app(\App\Domains\Pricing\PriceCalculator::class)->partaiMinimumQuantity();
    $freeShippingThreshold = 'Rp '.number_format(config('store.shipping.free_store_courier_threshold'), 0, ',', '.');
    $autoCompleteDays = config('store.fulfillment.auto_complete_workdays');
@endphp
@component('layouts.storefront', ['title' => 'Syarat dan Ketentuan - Pixel Komunika', 'metaDescription' => 'Syarat dan ketentuan belanja di toko online grosir Pixel Komunika untuk reseller terverifikasi.'])
<x-legal-document heading="Syarat dan Ketentuan">
    <p>
        Syarat dan Ketentuan ini mengatur penggunaan toko online Pixel Komunika (selanjutnya "kami") dan setiap pembelian yang dilakukan di dalamnya.
        Dengan mendaftar atau membuat pesanan, Anda menyetujui ketentuan ini. Pemrosesan data pribadi diatur dalam
        <a href="{{ route('legal.privacy') }}">Kebijakan Privasi</a>.
    </p>

    <h2>1. Layanan</h2>
    <p>Toko online ini melayani penjualan grosir aksesoris elektronik, kartu data, voucher, dan pulsa kepada pelaku usaha (toko, konter, atau reseller) yang akunnya sudah diverifikasi admin. Data produk, harga, dan stok berasal dari sistem kasir (POS) toko.</p>

    <h2>2. Akun dan verifikasi</h2>
    <ul>
        <li>Pendaftar wajib memberikan data yang benar: nama, nama usaha, email, dan nomor telepon yang aktif.</li>
        <li>Akun baru berstatus menunggu verifikasi. Harga, checkout, dan riwayat pesanan terbuka setelah admin menyetujui akun.</li>
        <li>Admin dapat menolak pendaftaran atau menangguhkan akun, misalnya bila data tidak dapat diverifikasi atau ada penyalahgunaan. Alasannya ditampilkan di halaman akun bila tersedia.</li>
        <li>Anda bertanggung jawab menjaga kerahasiaan kata sandi dan atas semua aktivitas yang terjadi di akun Anda.</li>
    </ul>

    <h2>3. Harga</h2>
    <ul>
        <li>Harga per unit ditentukan oleh jumlah pembelian:
            <ol>
                <li>harga partai berlaku untuk seluruh pesanan bila sedikitnya satu produk dibeli {{ $partaiMinimum }} unit atau lebih (jumlah produk berbeda tidak dijumlahkan), dan harga partai didahulukan daripada harga grosir;</li>
                <li>harga grosir berlaku untuk produk yang jumlahnya mencapai minimum grosir produk tersebut;</li>
            </ol>
        </li>
        <li>Pesanan baru dapat dibuat bila sedikitnya satu produk dibeli {{ $partaiMinimum }} unit atau lebih. Produk lain dalam pesanan yang sama boleh dibeli kurang dari jumlah itu.</li>
        <li>Harga yang berlaku untuk setiap produk ditampilkan di keranjang dan halaman checkout sebelum pesanan dibuat. Harga pada pesanan yang sudah dibuat tidak berubah walaupun harga produk kemudian berubah.</li>
        <li>PPh 22 dihitung sesuai ketentuan dan tarif yang berlaku untuk klasifikasi produk, lalu dicantumkan terpisah di keranjang, checkout, dan invoice.</li>
    </ul>

    <h2>4. Stok</h2>
    <p>Stok mengikuti data sistem kasir toko dan dapat berubah karena penjualan di toko fisik. Ketersediaan diperiksa ulang saat checkout; pesanan tidak dapat dibuat bila stok tidak mencukupi.</p>

    <h2>5. Pemesanan dan pembayaran</h2>
    <ul>
        <li>Pembayaran dilakukan dengan transfer bank ke rekening yang tercantum di halaman detail pesanan, sebesar total tagihan termasuk PPh 22 dan ongkos kirim.</li>
        <li>Setelah transfer, unggah bukti pembayaran di halaman detail pesanan. Admin memverifikasi bukti tersebut sebelum pesanan diproses.</li>
        <li>Pesanan yang belum dibayar hanya berlaku pada hari pesanan dibuat dan dibatalkan otomatis oleh sistem pada hari kalender berikutnya (Waktu Indonesia Barat).</li>
        <li>Bila bukti pembayaran ditolak, alasannya ditampilkan di detail pesanan dan Anda dapat mengunggah bukti yang benar.</li>
    </ul>

    <h2>6. Pembatalan dan pengembalian dana</h2>
    <ul>
        <li>Admin dapat membatalkan pesanan pada tanggal yang sama dengan tanggal transaksi, dengan alasan yang dicatat.</li>
        <li>Bila pesanan yang sudah dibayar dibatalkan, dana dikembalikan melalui transfer ke rekening pengirim. Waktu pengembalian dikonfirmasi admin melalui WhatsApp.</li>
    </ul>

    <h2>7. Pengiriman</h2>
    <ul>
        <li>Kurir toko melayani Kota dan Kabupaten Bandung dengan target pengiriman H+1 hari kerja. Ongkir kurir toko Rp0 bila subtotal barang ditambah PPh 22 mencapai {{ $freeShippingThreshold }}; di bawah itu berlaku tarif per area yang tampil saat checkout.</li>
        <li>Untuk alamat lain atau layanan instan, tersedia ekspedisi melalui Biteship dengan tarif dan estimasi yang ditampilkan saat checkout. Estimasi waktu dari ekspedisi bukan jaminan.</li>
        <li>Pastikan alamat dan kontak penerima benar. Keterlambatan atau kegagalan akibat alamat yang salah menjadi tanggung jawab pemesan.</li>
    </ul>

    <h2>8. Penerimaan barang dan komplain</h2>
    <ul>
        <li>Anda dapat mengonfirmasi penerimaan melalui tautan yang dikirim lewat WhatsApp. Bila tidak ada konfirmasi atau laporan kendala, pesanan yang sudah dikirim otomatis dianggap selesai setelah {{ $autoCompleteDays }} hari kerja.</li>
        <li>Bila barang rusak, kurang, atau tidak sesuai pesanan, laporkan ke admin melalui WhatsApp dengan nomor pesanan serta foto atau video barang. Penggantian, pengembalian barang, atau pengembalian dana diputuskan admin per kasus.</li>
    </ul>

    <h2>9. Batasan tanggung jawab</h2>
    <p>Kami berupaya menampilkan informasi produk, harga, dan stok dengan benar. Bila terjadi kesalahan tampilan yang nyata (misalnya salah ketik harga), kami dapat membatalkan pesanan terkait dan mengembalikan dana yang sudah dibayar sesuai bagian 6.</p>

    <h2>10. Hukum yang berlaku dan penyelesaian sengketa</h2>
    <p>Ketentuan ini tunduk pada hukum Republik Indonesia, termasuk Undang-Undang Informasi dan Transaksi Elektronik dan Peraturan Pemerintah Nomor 80 Tahun 2019 tentang Perdagangan Melalui Sistem Elektronik. Sengketa diselesaikan lebih dulu secara musyawarah. Bila tidak tercapai kesepakatan, sengketa diselesaikan melalui Pengadilan Negeri Bandung.</p>

    <h2>11. Perubahan ketentuan</h2>
    <p>Ketentuan ini dapat diperbarui. Versi terbaru berlaku sejak tanggal yang tertera di bagian atas halaman dan berlaku untuk pesanan yang dibuat setelah tanggal tersebut.</p>
</x-legal-document>
@endcomponent
