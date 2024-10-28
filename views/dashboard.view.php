<?php require('partials/header.php') ?>
<?php require('partials/navbar.php') ?>

    <main class="max-w-6xl mx-auto px-4 py-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-600">Welcome back, <?= htmlspecialchars($_SESSION['username'] ?? "User") ?></p>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Investments Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-2">Total Investments</h2>
                <p class="text-3xl font-bold text-indigo-600">
                    <?= $investmentCount ?? 0 ?>
                </p>
            </div>

            <!-- Total Value Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-2">Portfolio Value</h2>
                <p class="text-3xl font-bold text-indigo-600">
                    $<?= number_format($totalValue ?? 0, 2) ?>
                </p>
            </div>

            <!-- Profit/Loss Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-2">Total Profit/Loss</h2>
                <p class="text-3xl font-bold <?= ($totalProfitLoss ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                    $<?= number_format($totalProfitLoss ?? 0, 2) ?>
                </p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Quick Actions</h2>
            <div class="flex space-x-4">
                <a href="/FolioFlow/add-investment"
                   class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                    Add New Investment
                </a>
                <a href="/FolioFlow/investments"
                   class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200">
                    View All Investments
                </a>
            </div>
        </div>
    </main>

<?php require('partials/footer.php') ?>