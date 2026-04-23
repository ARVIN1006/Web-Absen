# Deploy Gratis (Demo)

Target: demo publik yang stabil, biaya $0 sebisa mungkin, dan bisa dipakai jangka panjang.

Catatan penting: tidak ada provider yang bisa dijamin "gratis selamanya" tanpa syarat. Pilihan paling dekat adalah **Oracle Cloud Always Free**, tapi tetap bergantung limit dan ketersediaan kapasitas region. citeturn0search0

## Rekomendasi: Oracle Cloud Always Free (OCI)

Kenapa:
- Ada resource **Always Free** yang bisa dipakai tanpa batas waktu selama masih di limit. citeturn0search0
- Cocok untuk Laravel demo + SQLite.

### Setup singkat (VPS)
1. Buat instance OCI Always Free (pilih region yang tersedia kapasitas).
2. Install Docker + Docker Compose di VPS.
3. Upload repo ini ke VPS.
4. Jalankan:

```bash
docker compose up -d --build
```

Aplikasi akan hidup di `http://SERVER_IP:8080` (container menjalankan `php artisan serve`).

### HTTPS
Untuk kamera/geolocation lebih aman pakai HTTPS. Cara termudah:
- Pasang reverse proxy di depan `:8080` (Caddy/Nginx) dan aktifkan Let's Encrypt.

## Alternatif cepat (tapi tidak "selamanya")
- Render/Koyeb free tier biasanya bisa sleep/berubah kebijakan, jadi kurang cocok untuk janji jangka panjang. citeturn0search11
