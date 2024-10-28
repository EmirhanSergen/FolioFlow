<?php require('partials/header.php') ?>
<?php require('partials/navbar.php') ?>

    <main class="min-h-screen bg-slate-50 py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Card -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-blue-900 to-blue-800 px-6 py-8">
                    <h1 class="text-2xl font-bold text-white">Add New Investment</h1>
                    <p class="mt-2 text-blue-100">Track a new investment in your portfolio</p>
                </div>

                <?php if (!empty($errors['general'])): ?>
                    <div class="p-4 bg-red-50 border-l-4 border-red-500">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700"><?= htmlspecialchars($errors['general']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="p-6">
                    <form method="POST" action="/FolioFlow/add-investment" id="investmentForm">
                        <!-- Investment Type -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="investment_type">
                                Investment Type
                            </label>
                            <select class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-md"
                                    id="investment_type"
                                    name="investment_type"
                                    required
                                    onchange="toggleSymbolInput()">
                                <option value="">Select Investment Type</option>
                                <option value="crypto" <?= $investmentType === 'crypto' ? 'selected' : '' ?>>Cryptocurrency</option>
                                <option value="stock" <?= $investmentType === 'stock' ? 'selected' : '' ?>>Stock</option>
                            </select>
                            <?php if (!empty($errors['investment_type'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($errors['investment_type']) ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Symbol -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Symbol</label>

                            <!-- Crypto Select -->
                            <div id="crypto_container" class="hidden">
                                <select id="crypto_symbol"
                                        name="crypto_symbol"
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-md">
                                    <option value="">Select Cryptocurrency</option>
                                    <?php foreach ($availableCryptos as $crypto): ?>
                                        <option value="<?= htmlspecialchars($crypto['symbol']) ?>"
                                            <?= (isset($_POST['crypto_symbol']) && $_POST['crypto_symbol'] === $crypto['symbol']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($crypto['symbol']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Stock Input -->
                            <div id="stock_container" class="hidden">
                                <input type="text"
                                       id="stock_symbol"
                                       name="stock_symbol"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Enter stock symbol"
                                       value="<?= $investmentType === 'stock' ? htmlspecialchars($_POST['stock_symbol'] ?? '') : '' ?>">
                            </div>

                            <?php if (!empty($errors['symbol'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($errors['symbol']) ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Buy Price -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="buy_price">
                                Buy Price
                                <span class="text-gray-500 text-xs">in USD</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="number"
                                       id="buy_price"
                                       name="buy_price"
                                       step="0.00000001"
                                       class="pl-7 mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       value="<?= htmlspecialchars($_POST['buy_price'] ?? '') ?>"
                                       required>
                            </div>
                            <?php if (!empty($errors['buy_price'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($errors['buy_price']) ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Amount -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="amount">
                                Amount
                            </label>
                            <input type="number"
                                   id="amount"
                                   name="amount"
                                   step="0.00000001"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                   value="<?= htmlspecialchars($_POST['amount'] ?? '') ?>"
                                   required>
                            <?php if (!empty($errors['amount'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($errors['amount']) ?></p>
                            <?php endif; ?>
                        </div>

                        <input type="hidden" name="symbol" id="final_symbol" value="">

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between">
                            <button type="submit"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-4 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                                    onclick="return prepareSubmission()">
                                Add Investment
                            </button>
                            <a href="/FolioFlow/investments"
                               class="text-blue-900 hover:text-blue-700 font-semibold">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        function toggleSymbolInput() {
            const investmentType = document.getElementById('investment_type').value;
            const cryptoContainer = document.getElementById('crypto_container');
            const stockContainer = document.getElementById('stock_container');
            const cryptoSymbol = document.getElementById('crypto_symbol');
            const stockSymbol = document.getElementById('stock_symbol');

            cryptoContainer.classList.add('hidden');
            stockContainer.classList.add('hidden');

            if (investmentType === 'crypto') {
                cryptoContainer.classList.remove('hidden');
                stockSymbol.value = '';
            } else if (investmentType === 'stock') {
                stockContainer.classList.remove('hidden');
                cryptoSymbol.value = '';
            }
        }

        function prepareSubmission() {
            const investmentType = document.getElementById('investment_type').value;
            const cryptoSymbol = document.getElementById('crypto_symbol');
            const stockSymbol = document.getElementById('stock_symbol');
            const finalSymbol = document.getElementById('final_symbol');

            if (investmentType === 'crypto') {
                finalSymbol.value = cryptoSymbol.value;
            } else if (investmentType === 'stock') {
                finalSymbol.value = stockSymbol.value;
            }

            return true;
        }

        // Initialize form on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleSymbolInput();
        });
    </script>

<?php require('partials/footer.php') ?>