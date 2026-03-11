<?php
/**
 * Asmira Ops - Giriş Sayfası
 */
require_once __DIR__ . '/../includes/auth.php';

$dbAvailable = false;
try {
    require_once __DIR__ . '/../api/db.php';
    $dbAvailable = function_exists('getDb');
} catch (\Throwable $e) {
    // DB bağlantısı yoksa login hala çalışır
}

// Zaten giriş yapmışsa dashboard'a yönlendir
if (isset($_SESSION['user_id'])) {
    header('Location: /dashboard');
    exit;
}

$error = '';

// POST - Login işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        if (!$dbAvailable) {
            $error = 'Veritabanı bağlantısı kurulamadı. Lütfen db_config.php ayarlarını kontrol edin.';
        } else {
            try {
                $db = getDb();
                $stmt = $db->prepare("SELECT id, username, password, name, role FROM users WHERE LOWER(username) = LOWER(?)");
                $stmt->execute([$username]);
                $user = $stmt->fetch();

                if ($user && verifyPassword($password, $user['password'])) {
                    // Şifre hash upgrade
                    if (PASSWORD_HASH_ENABLED && !password_verify($password, $user['password'])) {
                        $newHash = hashPassword($password);
                        $db->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$newHash, $user['id']]);
                    }

                    loginUser($user);
                    header('Location: /dashboard');
                    exit;
                } else {
                    $error = 'Kullanıcı adı veya şifre hatalı';
                }
            } catch (\Throwable $e) {
                $error = 'Veritabanı hatası: ' . $e->getMessage();
            }
        }
    } else {
        $error = 'Kullanıcı adı ve şifre gerekli';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asmira Ops - Giriş</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        .animate-shimmer {
            background-size: 200% 100%;
            animation: shimmer 3s linear infinite;
        }
        @keyframes float-slow {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(3deg); }
        }
        @keyframes float-medium {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(-2deg); }
        }
        @keyframes float-fast {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        .animate-float-slow { animation: float-slow 8s ease-in-out infinite; }
        .animate-float-medium { animation: float-medium 6s ease-in-out infinite; }
        .animate-float-fast { animation: float-fast 4s ease-in-out infinite; }
        @keyframes fadeInUp {
            from { transform: translateY(24px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .fade-in-up { animation: fadeInUp 0.7s ease-out forwards; }
    </style>
</head>
<body class="h-screen flex items-center justify-center p-4 relative overflow-hidden bg-[#0a0f1a]">
    <!-- Background -->
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-b from-[#0a1525] via-[#0c1a2e] to-[#071018]"></div>
        <!-- Wave pattern -->
        <div class="absolute bottom-0 left-0 right-0 h-[40%] opacity-[0.06]">
            <svg viewBox="0 0 1440 320" class="absolute bottom-0 w-full" preserveAspectRatio="none">
                <path fill="currentColor" class="text-cyan-400" d="M0,192L48,197.3C96,203,192,213,288,229.3C384,245,480,267,576,250.7C672,235,768,181,864,181.3C960,181,1056,235,1152,234.7C1248,235,1344,181,1392,154.7L1440,128L1440,320L0,320Z"/>
            </svg>
        </div>
        <!-- Floating icons -->
        <div class="absolute top-[8%] left-[8%] text-cyan-500/[0.08] animate-float-slow">
            <i data-lucide="ship" class="w-32 h-32" stroke-width="1"></i>
        </div>
        <div class="absolute top-[15%] right-[12%] text-blue-500/[0.09] animate-float-medium">
            <i data-lucide="anchor" class="w-24 h-24" stroke-width="1"></i>
        </div>
        <div class="absolute bottom-[25%] left-[5%] text-cyan-400/[0.07] animate-float-fast">
            <i data-lucide="fuel" class="w-20 h-20" stroke-width="1"></i>
        </div>
        <div class="absolute bottom-[15%] right-[8%] text-blue-400/[0.08] animate-float-slow">
            <i data-lucide="waves" class="w-28 h-28" stroke-width="1"></i>
        </div>
        <!-- Gradient orbs -->
        <div class="absolute -top-[20%] -right-[10%] w-[600px] h-[600px] rounded-full bg-gradient-to-br from-blue-600/10 to-transparent blur-3xl"></div>
        <div class="absolute -bottom-[30%] -left-[15%] w-[700px] h-[700px] rounded-full bg-gradient-to-tr from-cyan-600/[0.08] to-transparent blur-3xl"></div>
    </div>

    <!-- Login Card -->
    <div class="relative z-10 w-full max-w-[480px] mx-auto fade-in-up">
        <div class="relative bg-[#111827]/80 backdrop-blur-2xl rounded-3xl border border-white/[0.08] shadow-2xl shadow-black/40 overflow-hidden">
            <!-- Top accent -->
            <div class="absolute top-0 left-8 right-8 h-px bg-gradient-to-r from-transparent via-cyan-500/50 to-transparent"></div>
            
            <div class="p-6 sm:p-8 lg:p-10">
                <!-- Logo -->
                <div class="flex justify-center mb-6 lg:mb-8">
                    <img src="/assets/img/asmira-energy-logo.png" alt="Asmira Energy" class="h-16 sm:h-20 lg:h-24 object-contain">
                </div>

                <!-- Title -->
                <div class="text-center mb-6 lg:mb-8">
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-light text-white mb-1">
                        Operasyon
                        <span class="font-bold bg-gradient-to-r from-cyan-400 to-blue-500 bg-clip-text text-transparent">Yönetim Sistemi</span>
                    </h1>
                    <p class="text-white/40 text-sm">Hesabınıza giriş yapın</p>
                </div>

                <!-- Form -->
                <form method="POST" class="space-y-4" id="loginForm">
                    <!-- Username -->
                    <div class="space-y-2">
                        <label class="block text-xs font-medium text-white/40 uppercase tracking-wider">Kullanıcı Adı</label>
                        <div class="relative flex items-center rounded-2xl border-2 border-white/[0.08] bg-white/[0.03] hover:border-white/[0.12] focus-within:border-cyan-500/60 focus-within:bg-white/[0.06] transition-all duration-300">
                            <div class="pl-5">
                                <i data-lucide="user" class="h-5 w-5 text-white/30"></i>
                            </div>
                            <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" placeholder="kullanıcı adı" class="w-full h-11 lg:h-14 bg-transparent px-4 text-white text-sm lg:text-[15px] outline-none placeholder:text-white/25" required autofocus>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <label class="block text-xs font-medium text-white/40 uppercase tracking-wider">Şifre</label>
                        <div class="relative flex items-center rounded-2xl border-2 border-white/[0.08] bg-white/[0.03] hover:border-white/[0.12] focus-within:border-cyan-500/60 focus-within:bg-white/[0.06] transition-all duration-300">
                            <div class="pl-5">
                                <i data-lucide="lock" class="h-5 w-5 text-white/30"></i>
                            </div>
                            <input type="password" name="password" id="passwordInput" placeholder="••••••••" class="w-full h-11 lg:h-14 bg-transparent px-4 text-white text-sm lg:text-[15px] outline-none placeholder:text-white/25" required>
                            <button type="button" onclick="togglePassword()" class="pr-5 text-white/30 hover:text-white/60 transition-colors">
                                <i data-lucide="eye" class="h-5 w-5" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Error -->
                    <?php if ($error): ?>
                    <div class="flex items-center gap-3 px-5 py-4 rounded-2xl bg-red-500/10 border border-red-500/20">
                        <div class="h-2.5 w-2.5 rounded-full bg-red-500 animate-pulse"></div>
                        <span class="text-sm text-red-400"><?= htmlspecialchars($error) ?></span>
                    </div>
                    <?php endif; ?>

                    <!-- Submit -->
                    <div class="pt-2">
                        <button type="submit" id="submitBtn" class="group relative w-full h-11 lg:h-14 rounded-2xl text-white font-semibold text-sm lg:text-[15px] transition-all duration-300 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-cyan-500 via-blue-500 to-cyan-500 animate-shimmer"></div>
                            <div class="absolute inset-0 bg-white/0 group-hover:bg-white/10 transition-colors duration-300"></div>
                            <div class="relative flex items-center justify-center gap-3" id="btnContent">
                                <i data-lucide="log-in" class="h-5 w-5"></i>
                                <span>Giriş Yap</span>
                            </div>
                            <div class="relative items-center justify-center gap-3 hidden" id="btnLoading">
                                <div class="h-5 w-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                                <span>Giriş Yapılıyor...</span>
                            </div>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="px-6 lg:px-10 pb-4 lg:pb-8 text-center">
                <div class="h-px w-full bg-gradient-to-r from-transparent via-white/10 to-transparent mb-4 lg:mb-6"></div>
                <p class="text-xs text-white">Asmira Lojistik Operasyon Yönetim Sistemi</p>
                <p class="text-[10px] text-white/60 mt-1">v2.0.0 • © 2026</p>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
        
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            const icon = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                input.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }

        document.getElementById('loginForm').addEventListener('submit', function() {
            document.getElementById('btnContent').classList.add('hidden');
            document.getElementById('btnLoading').classList.remove('hidden');
            document.getElementById('btnLoading').classList.add('flex');
            document.getElementById('submitBtn').disabled = true;
        });
    </script>
</body>
</html>
