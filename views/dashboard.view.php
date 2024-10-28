<?php require('partials/header.php') ?>
<?php require('partials/navbar.php') ?>

    <main class="bg-slate-50 min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
                <div class="bg-gradient-to-r from-blue-900 to-blue-800 px-8 py-12">
                    <h1 class="text-3xl font-bold text-white">
                        Welcome back, <?= htmlspecialchars($_SESSION['username'] ?? "User") ?>
                    </h1>
                    <p class="mt-2 text-blue-100">
                        Here's an overview of your investment portfolio
                    </p>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 gap-6 mb-8 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Portfolio Value Card -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="bg-emerald-100 rounded-md p-3">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Portfolio Value</p>
                                <p class="text-2xl font-semibold text-emerald-600">
                                    $<?= number_format($totalValue ?? 0, 2) ?>
                                </p>
                                <p class="text-sm text-gray-500">
                                    Initial: $<?= number_format($totalInitialInvestment ?? 0, 2) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Investments Card -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="bg-blue-100 rounded-md p-3">
                                <svg class="w-6 h-6 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 20v-4m4 4v-8m4 8v-12m4 12v-16"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Active Investments</p>
                                <p class="text-2xl font-semibold text-blue-900">
                                    <?= $investmentCount ?? 0 ?>
                                </p>
                                <p class="text-sm text-gray-500">
                                    Avg: $<?= number_format($investmentCount > 0 ? ($totalValue ?? 0) / $investmentCount : 0, 2) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Profit/Loss Card -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="bg-blue-100 rounded-md p-3">
                                <svg class="w-6 h-6 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total Profit/Loss</p>
                                <p class="text-2xl font-semibold <?= ($totalProfitLoss ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-600' ?>">
                                    <?= ($totalProfitLoss ?? 0) >= 0 ? '+' : '-' ?>$<?= number_format(abs($totalProfitLoss ?? 0), 2) ?>
                                </p>
                                <p class="text-sm <?= ($totalProfitLoss ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-600' ?>">
                                    24h: <?= ($dayChange ?? 0) >= 0 ? '+' : '-' ?><?= number_format(abs($dayChange ?? 0), 2) ?>%
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ROI Card -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="bg-blue-100 rounded-md p-3">
                                <svg class="w-6 h-6 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">ROI</p>
                                <p class="text-2xl font-semibold <?= ($roi ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-600' ?>">
                                    <?= ($roi ?? 0) >= 0 ? '+' : '-' ?><?= number_format(abs($roi ?? 0), 2) ?>%
                                </p>
                                <p class="text-sm text-gray-500">All Time</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Summary & Quick Actions -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Performance Summary -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-blue-900 mb-4">Performance Summary</h2>
                        <div class="space-y-4">
                            <!-- Best Performing -->
                            <?php if ($bestPerforming): ?>
                                <div class="flex items-center justify-between p-3 bg-emerald-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="bg-emerald-100 rounded-full p-2">
                                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($bestPerforming['name']) ?>
                                            </p>
                                            <p class="text-sm text-gray-500">Best Performing</p>
                                        </div>
                                    </div>
                                    <div class="text-emerald-600 font-semibold">
                                        +<?= number_format($bestReturn, 2) ?>%
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Worst Performing -->
                            <?php if ($worstPerforming): ?>
                                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="bg-red-100 rounded-full p-2">
                                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"/>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($worstPerforming['name']) ?>
                                            </p>
                                            <p class="text-sm text-gray-500">Worst Performing</p>
                                        </div>
                                    </div>
                                    <div class="text-red-600 font-semibold">
                                        <?= number_format($worstReturn, 2) ?>%
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-blue-900 mb-4">Quick Actions</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <a href="/FolioFlow/add-investment"
                               class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Add Investment
                            </a>
                            <a href="/FolioFlow/investments"
                               class="inline-flex items-center justify-center px-4 py-2 border border-blue-900 text-sm font-medium rounded-md text-blue-900 bg-white hover:bg-blue-50">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                </svg>
                                View All
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php require('partials/footer.php') ?>