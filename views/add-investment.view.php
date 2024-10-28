<?php require('partials/header.php') ?>
<?php require('partials/navbar.php') ?>

    <main class="max-w-lg mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Add New Investment</h1>

            <?php if (!empty($errors['general'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($errors['general']) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/FolioFlow/add-investment" id="investmentForm">
                <!-- Investment Type -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="investment_type">
                        Investment Type
                    </label>
                    <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="investment_type"
                            name="investment_type"
                            required
                            onchange="toggleSymbolInput()">
                        <option value="">Select Investment Type</option>
                        <option value="crypto" <?= $investmentType === 'crypto' ? 'selected' : '' ?>>Cryptocurrency</option>
                        <option value="stock" <?= $investmentType === 'stock' ? 'selected' : '' ?>>Stock</option>
                    </select>
                    <?php if (!empty($errors['investment_type'])): ?>
                        <p class="text-red-500 text-xs italic mt-1"><?= htmlspecialchars($errors['investment_type']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Symbol -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Symbol</label>

                    <!-- Crypto Select -->
                    <div id="crypto_container" class="hidden">
                        <select id="crypto_symbol"
                                name="crypto_symbol"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
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
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                               placeholder="Enter stock symbol"
                               value="<?= $investmentType === 'stock' ? htmlspecialchars($_POST['stock_symbol'] ?? '') : '' ?>">
                    </div>

                    <?php if (!empty($errors['symbol'])): ?>
                        <p class="text-red-500 text-xs italic mt-1"><?= htmlspecialchars($errors['symbol']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Buy Price -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="buy_price">
                        Buy Price
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           type="number"
                           id="buy_price"
                           name="buy_price"
                           step="0.00000001"
                           value="<?= htmlspecialchars($_POST['buy_price'] ?? '') ?>"
                           required>
                    <?php if (!empty($errors['buy_price'])): ?>
                        <p class="text-red-500 text-xs italic mt-1"><?= htmlspecialchars($errors['buy_price']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Amount -->
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="amount">
                        Amount
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           type="number"
                           id="amount"
                           name="amount"
                           step="0.00000001"
                           value="<?= htmlspecialchars($_POST['amount'] ?? '') ?>"
                           required>
                    <?php if (!empty($errors['amount'])): ?>
                        <p class="text-red-500 text-xs italic mt-1"><?= htmlspecialchars($errors['amount']) ?></p>
                    <?php endif; ?>
                </div>

                <input type="hidden" name="symbol" id="final_symbol" value="">

                <div class="flex items-center justify-between">
                    <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                            onclick="return prepareSubmission()">
                        Add Investment
                    </button>
                    <a href="/FolioFlow/investments"
                       class="inline-block align-baseline font-bold text-sm text-indigo-600 hover:text-indigo-800">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>

    <script>
        function toggleSymbolInput() {
            const investmentType = document.getElementById('investment_type').value;
            const cryptoContainer = document.getElementById('crypto_container');
            const stockContainer = document.getElementById('stock_container');
            const cryptoSymbol = document.getElementById('crypto_symbol');
            const stockSymbol = document.getElementById('stock_symbol');

            // Hide both initially
            cryptoContainer.classList.add('hidden');
            stockContainer.classList.add('hidden');

            // Show appropriate container based on selection
            if (investmentType === 'crypto') {
                cryptoContainer.classList.remove('hidden');
                stockSymbol.value = '';  // Clear stock input
            } else if (investmentType === 'stock') {
                stockContainer.classList.remove('hidden');
                cryptoSymbol.value = '';  // Clear crypto selection
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