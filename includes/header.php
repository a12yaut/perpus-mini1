<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Perpustakaan Mini' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-indigo-800 text-white shadow-lg">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center flex-wrap">
            <a href="/perpus-mini/api/index.php" class="text-xl font-bold">📚 Perpus Mini</a>
            <div class="flex items-center gap-4 text-sm">
                <a href="/perpus-mini/api/katalog.php" class="hover:text-indigo-200">Katalog</a>
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <a href="/perpus-mini/api/admin/dashboard.php" class="hover:text-indigo-200">Dashboard</a>
                    <?php else: ?>
                        <a href="/perpus-mini/api/dashboard.php" class="hover:text-indigo-200">Dashboard</a>
                        <a href="/perpus-mini/api/cart.php" class="relative hover:text-indigo-200">
                            🛒
                            <?php $cartCount = count(getCart()); ?>
                            <?php if ($cartCount > 0): ?>
                                <span class="absolute -top-1 -right-2 bg-amber-400 text-indigo-900 text-xs rounded-full px-1.5 py-0.5"><?= $cartCount ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>
                    <span class="text-indigo-300">|</span>
                    <span><?= htmlspecialchars($_SESSION['nama'] ?? '') ?></span>
                    <a href="/perpus-mini/api/logout.php" class="bg-red-600 px-3 py-1 rounded hover:bg-red-700 text-sm">Logout</a>
                <?php else: ?>
                    <a href="/perpus-mini/api/login.php" class="hover:text-indigo-200">Masuk</a>
                    <a href="/perpus-mini/api/register.php" class="bg-amber-400 text-indigo-900 px-3 py-1 rounded hover:bg-amber-300">Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-6">
        <?php if (isset($_SESSION['flash'])): ?>
            <div class="mb-4 px-4 py-3 rounded text-sm <?= $_SESSION['flash']['tipe'] === 'sukses' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                <?= htmlspecialchars($_SESSION['flash']['pesan']) ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>