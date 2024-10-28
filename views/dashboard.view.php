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
                <!-- Total Investments Card -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="bg-blue-100 rounded-md p-3">
                                <svg class="w-6 h-6 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total Investments</p>
                                <p class="text-2xl font-semibold text-blue-900">
                                    <?= $investmentCount ?? 0 ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Portfolio Value Card -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="bg-emerald-100 rounded-md p-3">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Portfolio Value</p>
                                <p class="text-2xl font-semibold text-emerald-600">
                                    $<?= number_format($totalValue ?? 0, 2) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profit/Loss Card -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="bg-blue-100 rounded-md p-3">
                                <svg class="w-6 h-6 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total Profit/Loss</p>
                                <p class="text-2xl font-semibold <?= ($totalProfitLoss ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-600' ?>">
                                    $<?= number_format($totalProfitLoss ?? 0, 2) ?>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">ROI</p>
                                <p class="text-2xl font-semibold <?= ($totalValue > 0 ? ($totalProfitLoss/$totalValue * 100) : 0) >= 0 ? 'text-emerald-600' : 'text-red-600' ?>">
                                    <?= number_format($totalValue > 0 ? ($totalProfitLoss/$totalValue * 100) : 0, 2) ?>%
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions & Recent Activity -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-blue-900 mb-4">Quick Actions</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <a href="/FolioFlow/add-investment"
                               class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Investment
                            </a>
                            <a href="/FolioFlow/investments"
                               class="inline-flex items-center justify-center px-4 py-2 border border-blue-900 text-sm font-medium rounded-md text-blue-900 bg-white hover:bg-blue-50">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                </svg>
                                View All
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-blue-900 mb-4">Performance Summary</h2>
                        <div class="space-y-4">
                            <!-- Best Performing -->
                            <div class="flex items-center justify-between p-3 bg-emerald-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="bg-emerald-100 rounded-full p-2">
                                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Best Performing</p>
                                        <p class="text-sm text-gray-500">Last 30 days</p>
                                    </div>
                                </div>
                                <div class="text-emerald-600 font-semibold">
                                    +<?= number_format(($totalProfitLoss ?? 0) > 0 ? ($totalProfitLoss ?? 0) : 0, 2) ?>%
                                </div>
                            </div>

                            <!-- Active Investments -->
                            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="bg-blue-100 rounded-full p-2">
                                        <svg class="w-5 h-5 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Active Investments</p>
                                        <p class="text-sm text-gray-500">Currently tracking</p>
                                    </div>
                                </div>
                                <div class="text-blue-900 font-semibold">
                                    <?= $investmentCount ?? 0 ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php require('partials/footer.php') ?>