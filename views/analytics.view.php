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

        <!-- Display Error Message if Exists -->
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong class="font-bold">Error:</strong>
                <span class="block sm:inline"><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <!-- 2x2 Grid for Analytics Sections -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Overall Portfolio Performance (Top-Left) -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden flex flex-col">
                <div class="p-6 flex-1">
                    <h2 class="text-lg font-semibold text-blue-900 mb-4">Overall Portfolio Performance</h2>
                    <div class="aspect-[16/9]">
                        <?php if (!empty($chartData['portfolioChart']['labels']) && !empty($chartData['portfolioChart']['values'])): ?>
                            <canvas id="portfolioChart"></canvas>
                        <?php else: ?>
                            <p class="text-center text-gray-500">No portfolio data available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Investment Distribution (Top-Right) -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden flex flex-col">
                <div class="p-6 flex-1">
                    <h2 class="text-lg font-semibold text-blue-900 mb-4">Investment Distribution</h2>
                    <div class="aspect-[16/9]">
                        <?php if (!empty($chartData['distribution']['labels']) && !empty($chartData['distribution']['values'])): ?>
                            <canvas id="distributionChart"></canvas>
                        <?php else: ?>
                            <p class="text-center text-gray-500">No investment distribution data available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Monthly Performance (Bottom-Left) -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden flex flex-col">
                <div class="p-6 flex-1">
                    <h2 class="text-lg font-semibold text-blue-900 mb-4">Monthly Performance</h2>
                    <div class="aspect-[16/9]">
                        <?php if (!empty($chartData['monthlyPerformance']['labels']) && !empty($chartData['monthlyPerformance']['values'])): ?>
                            <canvas id="monthlyPerformanceChart"></canvas>
                        <?php else: ?>
                            <p class="text-center text-gray-500">No monthly performance data available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Trading Statistics & Additional Metrics (Bottom-Right) -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden flex flex-col">
                <div class="p-6 flex-1">
                    <h2 class="text-lg font-semibold text-blue-900 mb-4">Trading Statistics</h2>

                    <!-- Summary Cards -->
                    <div class="grid grid-cols-2 sm:grid-cols-2 gap-4 mb-6">
                        <div class="p-4 bg-emerald-50 rounded-lg">
                            <p class="text-sm font-medium text-gray-600">Win Rate</p>
                            <p class="text-2xl font-semibold text-emerald-600">
                                <?= isset($winLossRatio['wins'], $winLossRatio['losses']) && ($winLossRatio['wins'] + $winLossRatio['losses']) > 0
                                    ? number_format(($winLossRatio['wins'] / ($winLossRatio['wins'] + $winLossRatio['losses'])) * 100, 1)
                                    : '0.0' ?>%
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                <?= intval($winLossRatio['wins'] ?? 0) ?> wins / <?= intval($winLossRatio['losses'] ?? 0) ?> losses
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
                                    <span class="text-gray-900"><?= number_format($avgHoldTime ?? 0, 2) ?> days</span>
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

        <!-- Include Chart.js Library -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Overall Portfolio Performance Chart (Invested, Profit, and Current)
            let profitColor;

            // Check the total profit value and set the corresponding color
            if (<?= json_encode($chartData['portfolioChart']['values'][1]) ?> >= 0) {
                profitColor = 'rgb(16, 185, 129)'; // Green for positive profit
            } else {
                profitColor = 'rgb(229, 57, 53)'; // Red for negative profit
            }
            // Overall Portfolio Performance Chart (Invested , Profit ,and Current)
            new Chart(document.getElementById('portfolioChart'), {
                type: 'bar',
                data: {
                    labels: <?= json_encode($chartData['portfolioChart']['labels']) ?>,
                    datasets: [{
                        label: 'Value',
                        data: <?= json_encode($chartData['portfolioChart']['values']) ?>,
                        backgroundColor: [
                            'rgb(59, 130, 246)', // Invested - blue
                            profitColor,
                            'rgb(59, 130, 246)'  // Current - blue
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `$${context.parsed.y.toLocaleString()}`;
                                }
                            }
                        }
                    }
                }
            });

            // Investment Distribution Chart
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
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.label}: $${context.parsed}`;
                                }
                            }
                        }
                    }
                }
            });

            // Monthly Performance Chart
            new Chart(document.getElementById('monthlyPerformanceChart'), {
                type: 'line',
                data: {
                    labels: <?= json_encode($chartData['monthlyPerformance']['labels']) ?>,
                    datasets: [{
                        label: 'Monthly Profit',
                        data: <?= json_encode($chartData['monthlyPerformance']['values']) ?>,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: true,
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true }
                    },
                    plugins: {
                        legend: { display: true },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `$${context.parsed.y.toLocaleString()}`;
                                }
                            }
                        }
                    }
                }
            });
        </script>
</main>

<?php require('partials/footer.php') ?>
