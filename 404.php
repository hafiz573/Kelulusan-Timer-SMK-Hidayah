<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found - Kelulusan SMK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-bg: #f4f7fe;
            --card-bg: rgba(255, 255, 255, 0.85);
            --card-border: rgba(255, 255, 255, 0.5);
            --text-main: #2b3674;
            --text-muted: #a3aed1;
            --primary-color: #4318ff;
            --glass-shadow: 0 18px 40px rgba(112, 144, 176, 0.12);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--primary-bg);
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,0.03) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(225,39%,30%,0.03) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(339,49%,30%,0.03) 0, transparent 50%);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }

        /* Card Styling */
        .glass-card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            box-shadow: var(--glass-shadow);
            padding: 3rem 2rem;
            max-width: 600px;
            width: 100%;
            text-align: center;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .glass-card::before {
            content: '';
            position: absolute;
            top: -50px;
            left: -50px;
            width: 150px;
            height: 150px;
            background: linear-gradient(135deg, #4318ff 0%, #00d5ff 100%);
            border-radius: 50%;
            opacity: 0.1;
            z-index: -1;
            filter: blur(20px);
        }

        .glass-card::after {
            content: '';
            position: absolute;
            bottom: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: linear-gradient(135deg, #ff007b 0%, #ff8c00 100%);
            border-radius: 50%;
            opacity: 0.1;
            z-index: -1;
            filter: blur(30px);
        }

        h1.error-code {
            font-size: 8rem;
            font-weight: 900;
            margin: 0;
            line-height: 1;
            background: linear-gradient(135deg, var(--primary-color) 0%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 10px 30px rgba(67, 24, 255, 0.15);
        }

        h2.error-message {
            font-size: 2rem;
            font-weight: 700;
            margin-top: 1rem;
            margin-bottom: 1rem;
            color: var(--text-main);
        }

        p.error-description {
            color: var(--text-muted);
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 2.5rem;
            line-height: 1.6;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Buttons */
        .btn-modern {
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
            background: var(--primary-color);
            color: white;
            border: none;
            box-shadow: 0 4px 15px rgba(67, 24, 255, 0.3);
        }
        
        .btn-modern:hover {
            transform: translateY(-3px);
            background: #3311cc;
            color: white;
            box-shadow: 0 8px 25px rgba(67, 24, 255, 0.4);
        }

        .floating-icon {
            font-size: 4rem;
            color: rgba(67, 24, 255, 0.1);
            position: absolute;
            animation: float 6s ease-in-out infinite;
        }

        .icon-1 { top: 10%; right: 10%; animation-delay: 0s; }
        .icon-2 { bottom: 15%; left: 10%; animation-delay: 2s; font-size: 3rem; }

        @keyframes float {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(10deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }

    </style>
</head>
<body>
    
    <div class="glass-card">
        <i class="fas fa-graduation-cap floating-icon icon-1"></i>
        <i class="fas fa-search floating-icon icon-2"></i>
        
        <h1 class="error-code">404</h1>
        <h2 class="error-message">Halaman Tidak Ditemukan</h2>
        <p class="error-description">Maaf, halaman yang Anda tuju mungkin telah dipindahkan, dihapus, atau Anda salah memasukkan alamat URL.</p>
        
        <a href="/Kelulusan-Timer-SMK-Hidayah/index.php" class="btn-modern">
            <i class="fas fa-home"></i>
            Kembali ke Beranda
        </a>
    </div>

</body>
</html>
