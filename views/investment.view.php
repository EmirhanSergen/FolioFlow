<?php require 'partials/header.php'; ?>
<?php require 'partials/navbar.php'; ?>

    <main class="min-h-screen bg-slate-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-blue-900 to-blue-800 px-6 py-8">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-bold text-white">Edit Investment</h1>
                            <p class="mt-2 text-blue-100">Update or close your investment position</p>
                        </div>
                        <a href="/FolioFlow/investments"
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-blue-900 bg-white hover:bg-blue-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to List
                        </a>
                    </div>
                </div>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="p-4 bg-green-50 border-l-4 border-green-500">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700"><?= htmlspecialchars($_SESSION['success_message']) ?></p>
                            </div>
                            <div class="ml-auto pl-3">
                                <div class="-mx-1.5 -my-1.5">
                                    <button onclick="this.parentElement.parentElement.parentElement.parentElement.remove()"
                                            class="inline-flex rounded-md p-1.5 text-green-500 hover:bg-green-100">
                                        <span class="sr-only">Dismiss</span>
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="p-4 bg-red-50 border-l-4 border-red-500">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <?php foreach ($errors as $error): ?>
                                    <p class="text-sm text-red-700"><?= htmlspecialchars($error) ?></p>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (isset($investmentData)): ?>
                <!-- Current Price Card -->
                <?php if (isset($investmentData['current_price'])): ?>
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x">
                            <div class="p-6">
                                <h2 class="text-sm font-medium text-gray-600">Current Price</h2>
                                <p class="mt-2 text-3xl font-bold text-blue-900">
                                    $<?= number_format($investmentData['current_price'], 2) ?>
                                </p>
                                <p class="mt-2 text-sm text-gray-500">
                                    Last Updated: <?= $investmentData['last_updated'] ? date('Y-m-d H:i:s', strtotime($investmentData['last_updated'])) : 'Never' ?>
                                </p>
                            </div>

                            <div class="p-6">
                                <h2 class="text-sm font-medium text-gray-600">Profit/Loss</h2>
                                <p class="mt-2 text-3xl font-bold <?= $profitLoss >= 0 ? 'text-emerald-600' : 'text-red-600' ?>">
                                    $<?= number_format($profitLoss, 2) ?>
                                </p>
                                <p class="mt-2 text-sm <?= $profitLoss >= 0 ? 'text-emerald-600' : 'text-red-600' ?>">
                                    <?= number_format($profitLossPercent, 2) ?>%
                                </p>
                            </div>

                            <div class="p-6">
                                <h2 class="text-sm font-medium text-gray-600">Actions</h2>
                                <button onclick="refreshPrice()"
                                        class="mt-2 inline-flex items-center px-4 py-2 border border-blue-900 rounded-md shadow-sm text-sm font-medium text-blue-900 bg-white hover:bg-blue-50">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Refresh Price
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Update Form -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-blue-900 mb-4">Update Investment Details</h2>
                        <form method="POST">
                            <input type="hidden" name="action" value="update">

                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700" for="name">Symbol</label>
                                    <input class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                           type="text"
                                           id="name"
                                           name="name"
                                           value="<?= htmlspecialchars($investmentData['name']) ?>"
                                           required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700" for="buy_price">Buy Price</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">$</span>
                                        </div>
                                        <input class="pl-7 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                               type="number"
                                               step="0.01"
                                               id="buy_price"
                                               name="buy_price"
                                               value="<?= htmlspecialchars($investmentData['buy_price']) ?>"
                                               required>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700" for="amount">Amount</label>
                                    <input class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                           type="number"
                                           step="0.01"
                                           id="amount"
                                           name="amount"
                                           value="<?= htmlspecialchars($investmentData['amount']) ?>"
                                           required>
                                </div>

                                <div class="flex justify-end">
                                    <button class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-4 rounded-md shadow-sm" type="submit">
                                        Update Investment
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Close Investment Form -->
                <?php if ($investmentData['status'] === 'active'): ?>
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        <div class="p-6">
                            <h2 class="text-lg font-semibold text-blue-900 mb-4">Close Position</h2>
                            <form method="POST">
                                <input type="hidden" name="action" value="close">

                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700" for="sell_price">Sell Price</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">$</span>
                                        </div>
                                        <input class="pl-7 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                               type="number"
                                               step="0.01"
                                               id="sell_price"
                                               name="sell_price"
                                               value="<?= $investmentData['current_price'] ?? '' ?>"
                                               required>
                                    </div>
                                </div>

                                <div class="flex justify-end">
                                    <button class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-md shadow-sm"
                                            type="submit"
                                            onclick="return confirm('Are you sure you want to close this investment?')">
                                        Close Investment
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        <div class="p-6">
                            <h2 class="text-lg font-semibold text-blue-900 mb-4">Closing Details</h2>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                                        <dd class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($investmentData['status']) ?></dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Sell Price</dt>
                                        <dd class="mt-1 text-sm text-gray-900">$<?= number_format($investmentData['sell_price'], 2) ?></dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Closed At</dt>
                                        <dd class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($investmentData['closed_at']) ?></dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>

    <script>
        async function refreshPrice() {
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
                    alert('Failed to update price. Please try again.');
                }
            } catch (error) {
                alert('Error updating price. Please try again.');
            }
        }
    </script>

<?php require 'partials/footer.php'; ?>