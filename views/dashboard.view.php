<?php require('partials/header.php') ?>
<?php require('partials/navbar.php') ?>

    <main class="bg-slate-50 min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
                <div class="bg-gradient-to-r from-blue-900 to-blue-800 px-8 py-12 ">
                    <h1 class="text-3xl font-bold text-white">
                        Welcome back, <?= htmlspecialchars($_SESSION['username'] ?? "User") ?>
                    </h1>
                    <p class="mt-2 text-blue-100">
                        Here's an overview of your investment portfolio
                    </p>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 gap-8 mb-12 sm:grid-cols-2 lg:grid-cols-4">
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
                                    $<?= number_format($totalValue, 2) ?>
                                </p>
                                <p class="text-sm text-gray-500">
                                    Initial: $<?= number_format($totalInitialInvestment, 2) ?>
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
                                    <?= $activeInvestmentCount ?>
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
                                <p class="text-2xl font-semibold <?= $totalProfitLoss >= 0 ? 'text-emerald-600' : 'text-red-600' ?>">
                                    <?= $totalProfitLoss >= 0 ? '+' : '-' ?>$<?= number_format(abs($totalProfitLoss), 2) ?>
                                </p>
                                <p class="text-sm text-gray-500">All Positions</p>
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
                                <p class="text-2xl font-semibold <?= $roi >= 0 ? 'text-emerald-600' : 'text-red-600' ?>">
                                    <?= $roi >= 0 ? '+' : '-' ?><?= number_format(abs($roi), 2) ?>%
                                </p>
                                <p class="text-sm text-gray-500">All Time</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Summary & Quick Actions Section -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Performance Summary -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-blue-900 mb-4">Performance Summary</h2>
                        <?php if ($bestPerforming): ?>
                            <div class="mb-4 rounded-lg bg-emerald-50/50 p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="flex items-center gap-3">
                                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                            </svg>
                                            <div>
                                                <div class="font-medium text-gray-900">
                                                    <?= htmlspecialchars($bestPerforming['name']) ?>
                                                </div>
                                                <div class="text-sm text-gray-500">Best Performing</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-emerald-600 font-medium">
                                        +<?= number_format($bestReturn, 2) ?>%
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($worstPerforming): ?>
                            <div class="rounded-lg bg-red-50/50 p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="flex items-center gap-3">
                                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"/>
                                            </svg>
                                            <div>
                                                <div class="font-medium text-gray-900">
                                                    <?= htmlspecialchars($worstPerforming['name']) ?>
                                                </div>
                                                <div class="text-sm text-gray-500">Worst Performing</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-red-600 font-medium">
                                        <?= number_format($worstReturn, 2) ?>%
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-blue-900 mb-6">Quick Actions</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Add Investment -->
                            <a href="/FolioFlow/add-investment"
                               class="group p-4 border border-emerald-200 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition-colors">
                                <div class="flex flex-col items-center justify-center gap-3 text-center">
                                    <div class="p-2 rounded-lg bg-blue-50 text-blue-600 group-hover:bg-blue-100 transition-colors">
                                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-medium text-gray-900">Add Investment</h3>
                                        <p class="text-sm mt-4 text-gray-500">Track a new position</p>
                                    </div>
                                </div>

                            </a>
                            <!-- View Investments -->
                            <a href="/FolioFlow/investments"
                               class="group p-4 border border-gray-200 rounded-lg hover:border-blue-200 hover:bg-blue-50 transition-all">
                                <div class="flex flex-col items-center justify-center gap-3 text-center">
                                    <div class="p-2 rounded-lg bg-blue-50 text-blue-600 group-hover:bg-blue-100 transition-colors">
                                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-medium text-gray-900">Investments</h3>
                                        <p class="text-sm mt-4 text-gray-500">Manage your positions</p>
                                    </div>
                                </div>
                            </a>

                            <!-- Analytics -->
                            <a href="/FolioFlow/analytics"
                               class="group p-4 border border-gray-200 rounded-lg hover:border-blue-200 hover:bg-blue-50 transition-all">
                                <div class="flex flex-col items-center justify-center gap-3 text-center">
                                    <div class="p-2 rounded-lg bg-blue-50 text-blue-600 group-hover:bg-blue-100 transition-colors">
                                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-medium text-gray-900">Analytics</h3>
                                        <p class="text-sm mt-4 text-gray-500">Detailed performance metrics</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
    </main>

<?php require('partials/footer.php') ?>