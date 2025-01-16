<?php
// views/analytics-view.php
// Minimal example with no partials, just pure HTML/PHP:

session_start();
// Suppose you must be logged in. If not, redirect or show an error.
if (empty($_SESSION['user_id'])) {
    die('Not logged in');
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Portfolio Analytics</title>
    <!-- We can include any CSS you like here, or a tailwind link if you prefer -->
</head>
<body>

<h1>Portfolio Analytics</h1>

<!-- Dropdowns for selecting period -->
<div>
    <select id="portfolioPeriod">
        <option value="daily">Daily</option>
        <option value="weekly">Weekly</option>
        <option value="monthly" selected>Monthly</option>
    </select>

    <select id="performancePeriod">
        <option value="daily">Daily</option>
        <option value="weekly">Weekly</option>
        <option value="monthly" selected>Monthly</option>
    </select>
</div>

<!-- Canvas elements for the three charts -->
<canvas id="portfolioChart" width="400" height="200" style="border:1px solid #ccc;"></canvas>
<canvas id="distributionChart" width="400" height="200" style="border:1px solid #ccc;"></canvas>
<canvas id="performanceChart" width="400" height="200" style="border:1px solid #ccc;"></canvas>

<!-- Trading stats displayed as text -->
<div>
    <p><strong>Win Rate:</strong> <span id="winRate">--</span></p>
    <p><strong>Profit Factor:</strong> <span id="profitFactor">--</span></p>
    <p><strong>Average Profit:</strong> <span id="averageProfit">--</span></p>
    <p><strong>Average Loss:</strong> <span id="averageLoss">--</span></p>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let portfolioChart, distributionChart, performanceChart;

    async function fetchAnalyticsData(portfolioPeriod, performancePeriod) {
        try {
            const response = await fetch('../controllers/analytics.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    portfolio_period: portfolioPeriod,
                    performance_period: performancePeriod
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error: ${response.status}`);
            }

            const { chartData, winLossRatio, tradeMetrics } = await response.json();
            // Update charts
            updateCharts(chartData);
            // Update text stats
            updateStatistics(winLossRatio, tradeMetrics);

        } catch (err) {
            console.error('Error fetching analytics data:', err);
        }
    }

    function updateCharts(chartData) {
        // Destroy old charts if they exist
        if (portfolioChart)    portfolioChart.destroy();
        if (distributionChart) distributionChart.destroy();
        if (performanceChart)  performanceChart.destroy();

        // 1) Portfolio Chart
        const ctxPortfolio = document.getElementById('portfolioChart').getContext('2d');
        portfolioChart = new Chart(ctxPortfolio, {
            type: 'line',
            data: {
                labels: chartData.portfolioHistory.labels,
                datasets: [
                    {
                        label: 'Current Value',
                        data: chartData.portfolioHistory.values.current,
                        borderColor: 'blue',
                        backgroundColor: 'transparent',
                    },
                    {
                        label: 'Invested Value',
                        data: chartData.portfolioHistory.values.invested,
                        borderColor: 'gray',
                        backgroundColor: 'transparent',
                    }
                ]
            }
        });

        // 2) Distribution Chart
        const ctxDistribution = document.getElementById('distributionChart').getContext('2d');
        // Check if we have "No Data"
        if (chartData.distribution.labels.length === 1 && chartData.distribution.labels[0] === 'No Data') {
            console.log('No distribution data available.');
        } else {
            distributionChart = new Chart(ctxDistribution, {
                type: 'doughnut',
                data: {
                    labels: chartData.distribution.labels,
                    datasets: [{
                        data: chartData.distribution.values,
                        backgroundColor: [
                            '#4F46E5',
                            '#16A34A',
                            '#F59E0B',
                            '#EF4444',
                            '#10B981',
                            '#E11D48'
                        ]
                    }]
                }
            });
        }

        // 3) Performance Chart
        const ctxPerformance = document.getElementById('performanceChart').getContext('2d');
        performanceChart = new Chart(ctxPerformance, {
            type: 'bar',
            data: {
                labels: chartData.monthlyPerformance.labels,
                datasets: [
                    {
                        label: 'Profit/Loss',
                        data: chartData.monthlyPerformance.values,
                        backgroundColor: chartData.monthlyPerformance.values.map(v =>
                            v >= 0 ? 'green' : 'red'
                        )
                    }
                ]
            }
        });
    }

    function updateStatistics(winLossRatio, tradeMetrics) {
        document.getElementById('winRate').textContent =
            `${winLossRatio.wins} Wins / ${winLossRatio.losses} Losses`;

        document.getElementById('profitFactor').textContent =
            tradeMetrics.profitFactor;

        document.getElementById('averageProfit').textContent =
            `$${tradeMetrics.averageProfitPerTrade}`;

        document.getElementById('averageLoss').textContent =
            `$${tradeMetrics.averageLossPerTrade}`;
    }

    // Event listeners
    document.getElementById('portfolioPeriod').addEventListener('change', (e) => {
        fetchAnalyticsData(e.target.value, document.getElementById('performancePeriod').value);
    });
    document.getElementById('performancePeriod').addEventListener('change', (e) => {
        fetchAnalyticsData(document.getElementById('portfolioPeriod').value, e.target.value);
    });

    // On page load, fetch monthly-monthly
    fetchAnalyticsData('monthly', 'monthly');
</script>

</body>
</html>
