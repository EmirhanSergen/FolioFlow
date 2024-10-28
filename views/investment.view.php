<?php require 'partials/header.php'; ?>

    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Edit Investment</h1>
            <a href="/FolioFlow/investments" class="bg-gray-500 text-white px-4 py-2 rounded">Back to List</a>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div id="success-alert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 relative">
                <span class="block sm:inline"><?= htmlspecialchars($_SESSION['success_message']) ?></span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg onclick="closeAlert()" class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <title>Close</title>
                        <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                    </svg>
                </span>
            </div>

            <script>
                function closeAlert() {
                    document.getElementById('success-alert').style.display = 'none';
                }
                setTimeout(function() {
                    var alert = document.getElementById('success-alert');
                    if(alert) {
                        alert.style.display = 'none';
                    }
                }, 3000);
            </script>

            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($investmentData)): ?>
            <!-- Current Price Display -->
            <?php if (isset($investmentData['current_price'])): ?>
                <div class="bg-white shadow-md rounded px-8 py-6 mb-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-700">Current Price</h2>
                            <p class="text-2xl font-bold text-indigo-600">
                                $<?= number_format($investmentData['current_price'], 2) ?>
                            </p>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-700">Profit/Loss</h2>
                            <?php
                            $profitLoss = ($investmentData['current_price'] - $investmentData['buy_price']) * $investmentData['amount'];
                            $profitLossPercent = ($profitLoss / ($investmentData['buy_price'] * $investmentData['amount'])) * 100;
                            ?>
                            <p class="text-2xl font-bold <?= $profitLoss >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                                $<?= number_format($profitLoss, 2) ?>
                                (<?= number_format($profitLossPercent, 2) ?>%)
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">
                                Last Updated:<br>
                                <?= $investmentData['last_updated'] ? date('Y-m-d H:i:s', strtotime($investmentData['last_updated'])) : 'Never' ?>
                            </p>
                            <button onclick="refreshPrice()" class="mt-2 text-indigo-600 hover:text-indigo-800">
                                Refresh Price
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Update Form -->
            <form method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <input type="hidden" name="action" value="update">

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                        Name
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"
                           type="text"
                           id="name"
                           name="name"
                           value="<?= htmlspecialchars($investmentData['name']) ?>"
                           required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="buy_price">
                        Buy Price
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"
                           type="number"
                           step="0.01"
                           id="buy_price"
                           name="buy_price"
                           value="<?= htmlspecialchars($investmentData['buy_price']) ?>"
                           required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="amount">
                        Amount
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"
                           type="number"
                           step="0.01"
                           id="amount"
                           name="amount"
                           value="<?= htmlspecialchars($investmentData['amount']) ?>"
                           required>
                </div>

                <div class="flex items-center justify-between">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="submit">
                        Update Investment
                    </button>
                </div>
            </form>

            <!-- Close Investment Form -->
            <?php if ($investmentData['status'] === 'active'): ?>
                <form method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    <input type="hidden" name="action" value="close">

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="sell_price">
                            Sell Price
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"
                               type="number"
                               step="0.01"
                               id="sell_price"
                               name="sell_price"
                               value="<?= $investmentData['current_price'] ?? '' ?>"
                               required>
                    </div>

                    <div class="flex items-center justify-between">
                        <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                                type="submit"
                                onclick="return confirm('Are you sure you want to close this investment?')">
                            Close Investment
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="bg-gray-100 rounded p-4 mb-4">
                    <h2 class="font-bold mb-2">Closing Details</h2>
                    <p>Status: <?= htmlspecialchars($investmentData['status']) ?></p>
                    <p>Sell Price: $<?= number_format($investmentData['sell_price'], 2) ?></p>
                    <p>Closed At: <?= htmlspecialchars($investmentData['closed_at']) ?></p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

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