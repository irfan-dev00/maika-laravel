# Instruksi Agen Proyek

## Cakupan

- Bekerja hanya di dalam repositori ini kecuali diberi wewenang secara eksplisit.
- Jangan mengubah file yang tidak terkait dengan tugas yang diminta.
- Pertahankan perubahan pengguna yang sudah ada.
- Tanyakan sebelum mengubah arsitektur atau memperluas cakupan yang telah disepakati.

## Lingkungan dan rahasia

- Jangan pernah membaca, mengubah, mengungkapkan, menyalin, atau melakukan commit pada `.env` asli.
- File yang cocok dengan `.env.*` hanya boleh dibaca setelah ada persetujuan eksplisit dari pengguna.
- Gunakan `.env.example` atau `.env-example` sebagai referensi konfigurasi.
- Jangan pernah mengungkapkan kredensial, kata sandi, token, kunci privat, atau endpoint produksi.

## Keamanan Git

- Periksa `git status` sebelum mengedit.
- Pertahankan perubahan yang belum di-commit dan sudah ada.
- Jangan pernah menjalankan `git reset`, `git clean`, force push, atau perintah yang membuang perubahan.
- Jangan melakukan commit atau push kecuali diminta secara eksplisit.
- Tinjau diff akhir sebelum menyatakan pekerjaan selesai.

## Keamanan database

- Konfirmasi lingkungan dan database aktif sebelum menjalankan perintah database.
- Jangan pernah menjalankan operasi destruktif seperti fresh, reset, drop, truncate, atau bulk delete tanpa persetujuan eksplisit.
- Utamakan pemeriksaan read-only saat mendiagnosis masalah database.

## Alur kerja

- Pahami implementasi yang ada sebelum mengedit.
- Jaga perubahan tetap minimal dan konsisten dengan arsitektur saat ini.
- Jalankan pengujian, linting, atau validasi yang relevan.
- Sebelum selesai, laporkan:
  - file yang diubah;
  - perintah dan pengujian yang dijalankan;
  - validasi yang gagal atau dilewati;
  - risiko yang belum terselesaikan.

## Serah terima pekerjaan

- Sebelum melanjutkan pekerjaan yang ada, baca `AI-HANDOFF.md` jika tersedia.
- Verifikasi isinya terhadap `git status`, `git diff`, dan kode sumber saat ini.
- Perbarui `AI-HANDOFF.md` sebelum menyerahkan pekerjaan kepada agen lain atau mengakhiri tugas yang belum selesai.
- Jangan perlakukan file serah-terima sebagai sumber yang lebih otoritatif daripada keadaan repositori.
