<?php

// Pesan validasi Bahasa Indonesia (APP_LOCALE=id). Hanya aturan yang dipakai aplikasi;
// aturan lain jatuh ke fallback bahasa Inggris.
return [
    'accepted' => ':Attribute harus disetujui.',
    'array' => ':Attribute harus berupa daftar.',
    'boolean' => ':Attribute harus bernilai ya atau tidak.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'date' => ':Attribute bukan tanggal yang valid.',
    'email' => ':Attribute harus berupa alamat email yang valid.',
    'exists' => ':Attribute yang dipilih tidak valid.',
    'file' => ':Attribute harus berupa file.',
    'image' => ':Attribute harus berupa gambar.',
    'in' => ':Attribute yang dipilih tidak valid.',
    'integer' => ':Attribute harus berupa bilangan bulat.',
    'lowercase' => ':Attribute harus huruf kecil.',
    'max' => [
        'file' => ':Attribute maksimal :max kilobyte.',
        'numeric' => ':Attribute maksimal :max.',
        'string' => ':Attribute maksimal :max karakter.',
    ],
    'mimes' => ':Attribute harus berupa file bertipe: :values.',
    'min' => [
        'file' => ':Attribute minimal :min kilobyte.',
        'numeric' => ':Attribute minimal :min.',
        'string' => ':Attribute minimal :min karakter.',
    ],
    'numeric' => ':Attribute harus berupa angka.',
    'password' => [
        'letters' => ':Attribute harus berisi huruf.',
        'mixed' => ':Attribute harus berisi huruf besar dan kecil.',
        'numbers' => ':Attribute harus berisi angka.',
        'symbols' => ':Attribute harus berisi simbol.',
        'uncompromised' => ':Attribute ini pernah bocor di internet. Gunakan :attribute lain.',
    ],
    'regex' => 'Format :attribute tidak valid.',
    'required' => ':Attribute wajib diisi.',
    'string' => ':Attribute harus berupa teks.',
    'unique' => ':Attribute sudah terdaftar.',

    'custom' => [
        'phone' => [
            'regex' => 'Nomor HP harus diawali 08 dan berisi 10–14 digit.',
        ],
    ],

    'attributes' => [
        'name' => 'nama',
        'business_name' => 'nama usaha',
        'phone' => 'nomor HP',
        'email' => 'email',
        'password' => 'kata sandi',
        'address_line' => 'alamat',
        'recipient_name' => 'nama penerima',
        'recipient_phone' => 'nomor HP penerima',
        'proof_file' => 'bukti pembayaran',
        'bank_name' => 'nama bank',
        'account_name' => 'nama pemilik rekening',
        'amount' => 'jumlah transfer',
    ],
];
