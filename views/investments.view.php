<?php require('partials/header.php') ?>
<?php require('partials/navbar.php') ?>

    <main class="max-w-6xl mx-auto px-4 py-8">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?= htmlspecialchars($_SESSION['success_message']) ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?= htmlspecialchars($_SESSION['error_message']) ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">My Investments</h1>
            <div class="flex gap-4">
                <button onclick="refreshPrices()"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Refresh Prices
                </button>
                <a href="/FolioFlow/add-investment"
                   class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                    Add Investment
                </a>
            </div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($investments)): ?>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-600">You haven't added any investments yet.</p>
                <a href="/FolioFlow/add-investment"
                   class="text-indigo-600 hover:text-indigo-800 mt-2 inline-block">
                    Add your first investment
                </a>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buy Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profit/Loss</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($investments as $investment): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?= htmlspecialchars($investment['name']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?= number_format($investment['amount'], 2) ?>
                                <?php if (!empty($investment['total_added_amount'])): ?>
                                    <span class="text-xs text-gray-500">
                                    (+<?= number_format($investment['total_added_amount'], 2) ?>)
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                $<?= number_format($investment['buy_price'], 2) ?>
                                <?php if (!empty($investment['is_averaged'])): ?>
                                    <span class="text-xs text-indigo-600">(avg)</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($investment['current_price']): ?>
                                    $<?= number_format($investment['current_price'], 2) ?>
                                <?php else: ?>
                                    <span class="text-gray-400">Updating...</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($investment['current_price']): ?>
                                    <span class="<?= $investment['profit_loss'] >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                                    $<?= number_format($investment['profit_loss'], 2) ?>
                                    (<?= number_format(($investment['profit_loss'] / ($investment['buy_price'] * $investment['amount'])) * 100, 2) ?>%)
                                </span>
                                <?php else: ?>
                                    <span class="text-gray-400">Calculating...</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php if ($investment['last_updated']): ?>
                                    <?= date('H:i', strtotime($investment['last_updated'])) ?>
                                <?php else: ?>
                                    Never
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="/FolioFlow/investment?id=<?= $investment['id'] ?>"
                                   class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                                <button onclick="closePosition(<?= $investment['id'] ?>)"
                                        class="text-red-600 hover:text-red-900">Close</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
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