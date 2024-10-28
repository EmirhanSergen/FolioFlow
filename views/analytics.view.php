<?php require('partials/header.php') ?>
<?php require('partials/navbar.php') ?>

    <main class="min-h-screen bg-slate-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-blue-900 to-blue-800 px-6 py-8">
                    <h1 class="text-2xl font-bold text-white">Portfolio Analytics</h1>
                    <p class="mt-2 text-blue-100">Detailed analysis of your investment performance</p>
                </div>
            </div>

            <!-- Portfolio Value Chart -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-blue-900 mb-4">Portfolio Value Over Time</h2>
                        <div class="aspect-[16/9]">
                            <canvas id="portfolioChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Investment Distribution -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-blue-900 mb-4">Investment Distribution</h2>
                        <div class="aspect-[16/9]">
                            <canvas id="distributionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Performance & Win/Loss -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-blue-900 mb-4">Monthly Performance</h2>
                        <div class="aspect-[16/9]">
                            <canvas id="performanceChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Trading Statistics Section -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-blue-900 mb-4">Trading Statistics</h2>

                        <!-- Summary Cards -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                            <div class="p-4 bg-emerald-50 rounded-lg">
                                <p class="text-sm font-medium text-gray-600">Win Rate</p>
                                <p class="text-2xl font-semibold text-emerald-600">
                                    <?php
                                    $total = ($winLossRatio['wins'] ?? 0) + ($winLossRatio['losses'] ?? 0);
                                    echo number_format(($total > 0 ? ($winLossRatio['wins'] / $total) * 100 : 0), 1) . '%';
                                    ?>
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    <?= ($winLossRatio['wins'] ?? 0) ?> wins / <?= ($winLossRatio['losses'] ?? 0) ?> losses
                                </p>
                            </div>

                            <div class="p-4 bg-blue-50 rounded-lg">
                                <p class="text-sm font-medium text-gray-600">Avg Profit</p>
                                <p class="text-2xl font-semibold text-blue-600">
                                    $<?= number_format($averageProfitPerTrade ?? 0, 2) ?>
                                </p>
                                <p class="text-xs text-gray-500 mt-1">Per winning trade</p>
                            </div>

                            <div class="p-4 bg-red-50 rounded-lg">
                                <p class="text-sm font-medium text-gray-600">Avg Loss</p>
                                <p class="text-2xl font-semibold text-red-600">
                                    $<?= number_format($averageLossPerTrade ?? 0, 2) ?>
                                </p>
                                <p class="text-xs text-gray-500 mt-1">Per losing trade</p>
                            </div>

                            <div class="p-4 bg-purple-50 rounded-lg">
                                <p class="text-sm font-medium text-gray-600">Profit Factor</p>
                                <p class="text-2xl font-semibold text-purple-600">
                                    <?= number_format($profitFactor ?? 0, 2) ?>
                                </p>
                                <p class="text-xs text-gray-500 mt-1">Gains/Losses ratio</p>
                            </div>
                        </div>

                        <!-- Additional Statistics -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Time Analysis -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Time Analysis</h3>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-blue-800">Avg Hold Time</span>
                                        <span class="text-gray-900"><?= $avgHoldTime ?? 0 ?> days</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-blue-800">Best Month</span>
                                        <span class="text-emerald-600">$<?= number_format(abs($bestMonthProfit ?? 0), 2) ?></span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-blue-800">Worst Month</span>
                                        <span class="text-red-600">-$<?= number_format(abs($worstMonthLoss ?? 0), 2) ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Risk Metrics -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Risk Metrics</h3>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-blue-800">Largest Win</span>
                                        <span class="text-emerald-600">+$<?= number_format(abs($largestWin ?? 0), 2) ?></span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-blue-800">Largest Loss</span>
                                        <span class="text-red-600">-$<?= number_format(abs($largestLoss ?? 0), 2) ?></span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-blue-800">Risk/Reward</span>
                                        <span class="text-gray-900"><?= number_format($riskRewardRatio ?? 0, 2) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Portfolio Value Chart
        new Chart(document.getElementById('portfolioChart'), {
            type: 'line',
            data: {
                labels: <?= json_encode($chartData['portfolioHistory']['labels']) ?>,
                datasets: [{
                    label: 'Current Value',
                    data: <?= json_encode($chartData['portfolioHistory']['values']['current']) ?>,
                    borderColor: 'rgb(59, 130, 246)',
                    tension: 0.1
                }, {
                    label: 'Invested Value',
                    data: <?= json_encode($chartData['portfolioHistory']['values']['invested']) ?>,
                    borderColor: 'rgb(156, 163, 175)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Distribution Chart
        new Chart(document.getElementById('distributionChart'), {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($chartData['distribution']['labels']) ?>,
                datasets: [{
                    data: <?= json_encode($chartData['distribution']['values']) ?>,
                    backgroundColor: [
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(239, 68, 68)',
                        'rgb(245, 158, 11)',
                        'rgb(168, 85, 247)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Performance Chart
        new Chart(document.getElementById('performanceChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($chartData['monthlyPerformance']['labels']) ?>,
                datasets: [{
                    label: 'Profit/Loss',
                    data: <?= json_encode($chartData['monthlyPerformance']['values']) ?>,
                    backgroundColor: function(context) {
                        return context.raw >= 0 ? 'rgb(16, 185, 129)' : 'rgb(239, 68, 68)';
                    }
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    </script>

<?php require('partials/footer.php') ?>