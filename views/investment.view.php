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

            <!-- Server-Side Success Message -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="p-4 bg-green-50 border-l-4 border-green-500">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700"><?= htmlspecialchars($_SESSION['success_message']) ?></p>
                        </div>
                        <div class="ml-auto pl-3">
                            <div class="-mx-1.5 -my-1.5">
                                <button
                                        onclick="this.parentElement.parentElement.parentElement.parentElement.remove()"
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

            <!-- Server-Side Error Messages -->
            <?php if (!empty($errors)): ?>
                <div class="p-4 bg-red-50 border-l-4 border-red-500">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
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

        <!-- Client-Side Error Box (replaces alert) -->
        <div id="clientErrorBox" class="hidden p-4 bg-red-50 border-l-4 border-red-500 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p id="clientErrorText" class="text-sm text-red-700"></p>
                </div>
                <div class="ml-auto pl-3">
                    <button onclick="hideClientErrorMessage()" class="inline-flex rounded-md p-1.5 text-red-500 hover:bg-red-100">
                        <span class="sr-only">Dismiss</span>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0
                            111.414 1.414L11.414 10l4.293 4.293a1 1 0
                            01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0
                            01-1.414-1.414L8.586 10 4.293 5.707a1 1
                            0 010-1.414z"/>
                        </svg>
                    </button>
                </div>
            </div>
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
                                Last Updated:
                                <?= $investmentData['last_updated']
                                    ? date('Y-m-d H:i:s', strtotime($investmentData['last_updated']))
                                    : 'Never' ?>
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
                                    class="mt-2 inline-flex items-center px-4 py-2 border border-blue-900
                                           rounded-md shadow-sm text-sm font-medium text-blue-900 bg-white hover:bg-blue-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0
                                          a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
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
                    <!-- Give the form an ID to fix the "submit" logic -->
                    <form id="updateForm" method="POST">
                        <input type="hidden" name="action" value="update">

                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700" for="name">Symbol</label>
                                <input class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                           focus:ring-blue-500 focus:border-blue-500"
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
                                    <input class="pl-7 block w-full border-gray-300 rounded-md shadow-sm
                                               focus:ring-blue-500 focus:border-blue-500"
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
                                <input class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                           focus:ring-blue-500 focus:border-blue-500"
                                       type="number"
                                       step="0.01"
                                       id="amount"
                                       name="amount"
                                       value="<?= htmlspecialchars($investmentData['amount']) ?>"
                                       required>
                            </div>

                            <div class="flex justify-end">
                                <button type="button"
                                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-4 rounded-md shadow-sm"
                                        onclick="showConfirmationModal('update')">
                                    Update Investment
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Close Investment Form (only if status=active) -->
            <?php if ($investmentData['status'] === 'active'): ?>
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-blue-900 mb-4">Close Position</h2>
                        <!-- Give the form an ID to fix "close" logic -->
                        <form id="closeForm" method="POST">
                            <input type="hidden" name="action" value="close">

                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700" for="sell_price">Sell Price</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input
                                            id="sell_price"
                                            name="sell_price"
                                            type="number"
                                            step="0.01"
                                            class="pl-7 block w-full border-2 border-gray-300 rounded-md
                                               shadow-sm focus:ring-0 focus:outline-none h-12 bg-white
                                               transition-colors duration-200"
                                            value="<?= number_format($investmentData['current_price'] ?? 0, 2, '.', '') ?>"
                                            required
                                            oninput="updateSellPriceAppearance(this.value, <?= $investmentData['buy_price'] ?>)"
                                    >
                                    <button type="button"
                                            onclick="useCurrentPrice(<?= $investmentData['current_price'] ?>)"
                                            class="absolute inset-y-0 right-0 px-3 flex items-center bg-gray-50
                                                   hover:bg-gray-100 border-l border-gray-300 rounded-r-md
                                                   text-sm text-blue-600 hover:text-blue-800">
                                        Use Current
                                    </button>
                                </div>
                                <div id="profitLossDisplay" class="text-right text-sm mt-2"></div>
                            </div>

                            <div class="flex justify-between items-center">
                                <div class="text-sm text-gray-500">
                                    Current Price:
                                    $<?= number_format($investmentData['current_price'] ?? 0, 2, '.', ',') ?>
                                </div>
                                <button type="button"
                                        class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4
                                               rounded-md shadow-sm"
                                        onclick="showConfirmationModal('close')">
                                    Close Investment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <!-- Already closed -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-blue-900 mb-4">Closing Details</h2>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <?= htmlspecialchars($investmentData['status']) ?>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Sell Price</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        $<?= number_format($investmentData['sell_price'], 2) ?>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Closed At</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <?= htmlspecialchars($investmentData['closed_at']) ?>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmationModal"
         class="fixed inset-0 bg-gray-500 bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h3 id="modalTitle" class="text-lg font-semibold text-gray-900 mb-4">Confirm Action</h3>
            <p id="modalText" class="text-sm text-gray-600 mb-6"></p>
            <div class="flex justify-end space-x-3">
                <button id="cancelButton"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                        onclick="hideConfirmationModal()">
                    Cancel
                </button>
                <button id="confirmButton"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</main>

<script>
    // Show/hide a styled client-side error message
    function showClientErrorMessage(message) {
        const errorBox = document.getElementById('clientErrorBox');
        const errorText = document.getElementById('clientErrorText');
        errorBox.classList.remove('hidden');
        errorText.textContent = message;
    }

    function hideClientErrorMessage() {
        document.getElementById('clientErrorBox').classList.add('hidden');
    }

    // Refresh price without using alert
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
                showClientErrorMessage('Failed to update price. Please try again.');
            }
        } catch (error) {
            showClientErrorMessage('Error updating price. Please try again.');
        }
    }

    // Function to update the sell price appearance and profit/loss dynamically
    function updateSellPriceAppearance(sellPrice, buyPrice) {
        const input = document.getElementById('sell_price');
        const profitLossDisplay = document.getElementById('profitLossDisplay');
        sellPrice = parseFloat(sellPrice) || 0;

        // Reset styles
        input.classList.remove('border-emerald-600', 'border-red-600',
            'bg-emerald-50', 'bg-red-50');
        profitLossDisplay.classList.remove('text-emerald-600', 'text-red-600');

        // Update styles based on profit/loss
        if (sellPrice > buyPrice) {
            input.classList.add('border-emerald-600', 'bg-emerald-50');
            profitLossDisplay.classList.add('text-emerald-600');
        } else {
            input.classList.add('border-red-600', 'bg-red-50');
            profitLossDisplay.classList.add('text-red-600');
        }

        // Calculate and display profit/loss
        const profitLoss = (sellPrice - buyPrice) * <?= $investmentData['amount'] ?>;
        profitLossDisplay.textContent = `Profit/Loss: ${profitLoss >= 0 ? '+' : ''}$${profitLoss.toFixed(2)}`;
    }

    // Use the current price for sell price
    function useCurrentPrice(currentPrice) {
        const input = document.getElementById('sell_price');
        input.value = parseFloat(currentPrice).toFixed(2);
        updateSellPriceAppearance(currentPrice, <?= $investmentData['buy_price'] ?>);
    }

    // Confirmation Modal logic
    function showConfirmationModal(actionType) {
        const modal = document.getElementById('confirmationModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalText = document.getElementById('modalText');
        const confirmButton = document.getElementById('confirmButton');

        if (actionType === 'update') {
            modalTitle.textContent = 'Confirm Update';
            modalText.textContent = 'Are you sure you want to update this investment?';
            confirmButton.textContent = 'Update';
            confirmButton.className = 'px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700';
            confirmButton.onclick = () => {
                // Submit the form with id="updateForm"
                document.getElementById('updateForm').submit();
            };
        } else if (actionType === 'close') {
            modalTitle.textContent = 'Confirm Close';
            modalText.textContent = 'Are you sure you want to close this investment? This action cannot be undone.';
            confirmButton.textContent = 'Close';
            confirmButton.className = 'px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700';
            confirmButton.onclick = () => {
                // Submit the form with id="closeForm"
                document.getElementById('closeForm').submit();
            };
        }

        modal.classList.remove('hidden');
    }

    function hideConfirmationModal() {
        const modal = document.getElementById('confirmationModal');
        modal.classList.add('hidden');
    }
</script>

<?php require 'partials/footer.php'; ?>
