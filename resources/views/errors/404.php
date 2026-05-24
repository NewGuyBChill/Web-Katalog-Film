<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Halaman Tidak Ditemukan | CelesView</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@200;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #0e0e12; color: #fff; font-family: 'Inter', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .error-container { text-align: center; padding: 2rem; }
        .error-code { font-family: 'Barlow Condensed', sans-serif; font-size: 10rem; font-weight: 800; line-height: 1; background: linear-gradient(135deg, #00d2ff, #0072ff); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .error-title { font-size: 1.5rem; color: #a0a0a0; margin-bottom: 1rem; font-weight: 300; }
        .error-desc { color: #666; max-width: 400px; margin: 0 auto 2.5rem; line-height: 1.6; }
        .error-btn { display: inline-flex; align-items: center; gap: 8px; padding: 0.8rem 2rem; background: linear-gradient(135deg, #00d2ff, #0072ff); color: #fff; border-radius: 30px; text-decoration: none; font-weight: 600; font-size: 0.95rem; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0, 210, 255, 0.25); }
        .error-btn:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0, 210, 255, 0.4); }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <h1 class="error-title">Halaman Tidak Ditemukan</h1>
        <p class="error-desc">Halaman yang Anda cari mungkin sudah dipindahkan, dihapus, atau tidak pernah ada.</p>
        <a href="<?= url('/') ?>" class="error-btn">
            ← Kembali ke Home
        </a>
    </div>
</body>
</html>
