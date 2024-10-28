<?php require('partials/header.php') ?>
<?php require('partials/navbar.php') ?>

    <main class="min-h-screen bg-slate-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-blue-900 to-blue-800 px-6 py-8">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-bold text-white">My Investments</h1>
                            <p class="mt-2 text-blue-100">Track and manage your investment portfolio</p>
                        </div>
                        <div class="flex gap-4">
                            <button onclick="refreshPrices()"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-blue-900 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Refresh Prices
                            </button>
                            <a href="/FolioFlow/add-investment"
                               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Add Investment
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
                    <div class="bg-slate-50 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-600">Total Investment</p>
                        <p class="text-2xl font-bold text-blue-900">
                            $<?= number_format($pageData['total_investment'], 2) ?>
                        </p>
                    </div>
                    <div class="bg-slate-50 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-600">Current Value</p>
                        <p class="text-2xl font-bold text-blue-900">
                            $<?= number_format($pageData['total_value'], 2) ?>
                        </p>
                    </div>
                    <div class="bg-slate-50 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-600">Total Profit/Loss</p>
                        <p class="text-2xl font-bold <?= $pageData['total_profit_loss'] >= 0 ? 'text-emerald-600' : 'text-red-600' ?>">
                            $<?= number_format($pageData['total_profit_loss'], 2) ?>
                            (<?= number_format($pageData['profit_loss_percentage'], 2) ?>%)
                        </p>
                    </div>
                </div>
            </div>

            <!-- Alerts -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="rounded-md bg-green-50 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                <?= htmlspecialchars($_SESSION['success_message']) ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="rounded-md bg-red-50 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">There were errors with your submission:</h3>
                            <ul class="mt-2 text-sm text-red-700">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Investments Table -->
            <?php if (empty($investments)): ?>
                <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No investments</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new investment.</p>
                        <div class="mt-6">
                            <a href="/FolioFlow/add-investment" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Add Investment
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Buy Price</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Current Price</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Profit/Loss</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($investments as $investment): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($investment['name']) ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm text-gray-900">
                                            <?= number_format($investment['amount'], 2) ?>
                                            <?php if (!empty($investment['total_added_amount'])): ?>
                                                <span class="text-xs text-gray-500">(+<?= number_format($investment['total_added_amount'], 2) ?>)</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm text-gray-900">
                                            $<?= number_format($investment['buy_price'], 2) ?>
                                            <?php if (!empty($investment['is_averaged'])): ?>
                                                <span class="text-xs text-blue-600">(avg)</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <?php if ($investment['current_price']): ?>
                                            <div class="text-sm text-gray-900">
                                                $<?= number_format($investment['current_price'], 2) ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-sm text-gray-500">
                                                Updating...
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <?php if ($investment['current_price']): ?>
                                            <div class="text-sm <?= $investment['profit_loss'] >= 0 ? 'text-emerald-600' : 'text-red-600' ?>">
                                                $<?= number_format($investment['profit_loss'], 2) ?>
                                                (<?= number_format(($investment['profit_loss'] / ($investment['buy_price'] * $investment['amount'])) * 100, 2) ?>%)
                                            </div>
                                        <?php else: ?>
                                            <div class="text-sm text-gray-500">
                                                Calculating...
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">
                                            <?php if ($investment['last_updated']): ?>
                                                <?= date('H:i', strtotime($investment['last_updated'])) ?>
                                            <?php else: ?>
                                                Never
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="/FolioFlow/investment?id=<?= $investment['id'] ?>"
                                           class="text-blue-900 hover:text-blue-700 mr-4">Edit</a>
                                        <button onclick="closePosition(<?= $investment['id'] ?>)"
                                                class="text-red-600 hover:text-red-900">Close</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        async function refreshPrices() {
            try {
                const response = await fetch('/FolioFlow/api/update-prices.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Failed to update prices. Please try again.');
                }
            } catch (error) {
                alert('Error updating prices. Please try again.');
            }
        }

        function closePosition(id) {
            const sellPrice = prompt("Enter the selling price:");
            if (sellPrice !== null && !isNaN(sellPrice)) {
                window.location.href = `/FolioFlow/close-position?id=${id}&price=${sellPrice}`;
            }
        }
    </script>

<?php require('partials/footer.php') ?>