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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 4v5h.582m15.356 2A8.001 8.001 0
                                      004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003
                                      8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Refresh Prices
                        </button>
                        <a href="/FolioFlow/add-investment"
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
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
        <!-- Success Message -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div id="successAlert" class="rounded-md bg-green-50 p-4 mb-6 transition-opacity duration-1000 ease-in-out">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0
                                  11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="ml-3 text-sm font-medium text-green-800">
                            <?= htmlspecialchars($_SESSION['success_message']) ?>
                        </p>
                    </div>
                    <button onclick="closeAlert('successAlert')"
                            class="text-green-500 hover:text-green-700 focus:outline-none">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <!-- Error Messages -->
        <?php if (!empty($errors)): ?>
            <div id="errorAlert" class="rounded-md bg-red-50 p-4 mb-6 transition-opacity duration-1000 ease-in-out">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0
                                      2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464
                                      0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <h3 class="ml-3 text-sm font-medium text-red-800">
                                There were errors with your submission:
                            </h3>
                        </div>
                        <ul class="mt-2 text-sm text-red-700">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <button onclick="closeAlert('errorAlert')"
                            class="text-red-500 hover:text-red-700 focus:outline-none">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        <?php endif; ?>

        <!-- Client-Side Error Box (replaces alerts) -->
        <div id="clientErrorBox" class="hidden p-4 bg-red-50 border-l-4 border-red-500 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0
                              2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464
                              0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p id="clientErrorText" class="text-sm text-red-700"></p>
                </div>
                <div class="ml-auto pl-3">
                    <button onclick="hideClientErrorMessage()" class="inline-flex rounded-md p-1.5 text-red-500 hover:bg-red-100">
                        <span class="sr-only">Dismiss</span>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1
                            1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0
                            01-1.414 1.414L10 11.414l-4.293 4.293a1
                            1 0 01-1.414-1.414L8.586 10 4.293
                            5.707a1 1 0 010-1.414z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Investments Table -->
        <?php if (empty($investments)): ?>
            <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                <div class="flex flex-col items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0
                              01-2-2V5a2 2 0 012-2h5.586a1 1 0
                              01.707.293l5.414 5.414a1 1 0
                              01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No investments</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new investment.</p>
                    <div class="mt-6">
                        <a href="/FolioFlow/add-investment"
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm
                                  font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
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
                            <th scope="col"
                                class="px-6 py-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </th>
                            <th scope="col"
                                class="px-6 py-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount
                            </th>
                            <th scope="col"
                                class="px-6 py-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Buy Price
                            </th>
                            <th scope="col"
                                class="px-6 py-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Current Price
                            </th>
                            <th scope="col"
                                class="px-6 py-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Profit/Loss
                            </th>
                            <th scope="col"
                                class="px-6 py-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Last Updated
                            </th>
                            <th scope="col"
                                class="px-6 py-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($investments as $investment): ?>
                            <tr class="hover:bg-gray-100">
                                <td class="px-6 py-6 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= htmlspecialchars($investment['name']) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-6 whitespace-nowrap text-left">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= number_format($investment['amount'], 2) ?>
                                        <?php if (!empty($investment['total_added_amount'])): ?>
                                            <span class="text-sm text-gray-500">
                                                (+<?= number_format($investment['total_added_amount'], 2) ?>)
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-6 whitespace-nowrap text-left">
                                    <div class="text-sm text-gray-900">
                                        $<?= number_format($investment['buy_price'], 2) ?>
                                        <?php if (!empty($investment['is_averaged'])): ?>
                                            <span class="text-xs text-blue-600">(avg)</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-6 whitespace-nowrap text-left">
                                    <div class="text-sm text-gray-900">
                                        $<?= number_format($investment['current_price'], 2) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-6 whitespace-nowrap text-left">
                                    <div class="text-sm <?= $investment['profit_loss'] >= 0
                                        ? 'text-emerald-600' : 'text-red-600' ?>">
                                        $<?= number_format($investment['profit_loss'], 2) ?>
                                        (<?= number_format(
                                            ($investment['profit_loss'] / ($investment['buy_price'] * $investment['amount'])) * 100,
                                            2
                                        ) ?>%)
                                    </div>
                                </td>
                                <td class="px-6 py-6 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">
                                        <?php if ($investment['last_updated']): ?>
                                            <?= date('H:i', strtotime($investment['last_updated'])) ?>
                                        <?php else: ?>
                                            Never
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-6 whitespace-nowrap text-left text-sm font-medium">
                                    <a href="/FolioFlow/investment?id=<?= $investment['id'] ?>"
                                       class="text-blue-900 hover:text-blue-700 mr-4">
                                        Edit
                                    </a>
                                    <button
                                            onclick="showCloseModal(
                                            <?= $investment['id'] ?>,
                                                    '<?= htmlspecialchars($investment['name']) ?>',
                                            <?= $investment['current_price'] ?>,
                                            <?= $investment['amount'] ?>,
                                            <?= $investment['buy_price'] ?>
                                                    )"
                                            class="text-red-600 hover:text-red-900"
                                    >
                                        Close
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Close Position Modal -->
    <div id="closeModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-[480px] shadow-lg rounded-md bg-white">
            <!-- Loading Overlay -->
            <div id="loadingOverlay"
                 class="hidden absolute inset-0 bg-white bg-opacity-90 flex items-center justify-center rounded-md z-50">
                <div class="flex flex-col items-center space-y-3">
                    <svg class="animate-spin h-8 w-8 text-blue-900" xmlns="http://www.w3.org/2000/svg"
                         fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8V0C5.373
                              0 0 5.373 0 12h4zm2 5.291A7.962 7.962
                              0 014 12H0c0 3.042 1.135 5.824 3
                              7.938l3-2.647z"/>
                    </svg>
                    <span class="text-blue-900 font-medium">Closing Position...</span>
                </div>
            </div>

            <!-- Modal Content -->
            <div class="mt-3">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900">Close Position</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Investment Details -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Investment</p>
                            <p id="modalSymbol" class="text-lg font-semibold text-gray-900"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Current Price</p>
                            <p class="text-lg font-semibold text-gray-900">
                                $<span id="modalCurrentPrice"></span>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Initial Investment</p>
                            <p id="modalInitialInvestment" class="text-lg font-semibold text-gray-900"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Current Value</p>
                            <p id="modalCurrentValue" class="text-lg font-semibold text-gray-900"></p>
                        </div>
                    </div>
                </div>

                <!-- Sell Price Input -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sell Price</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">$</span>
                        </div>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input
                                    type="number"
                                    id="modalSellPrice"
                                    step="0.01"
                                    class="pl-7 block w-full border-2 border-gray-300 rounded-md
                                       shadow-sm focus:ring-0 focus:outline-none h-12 bg-white
                                       transition-colors duration-200"
                            />
                            <button
                                    type="button"
                                    onclick="useCurrentPrice()"
                                    class="absolute inset-y-0 right-0 px-3 flex items-center
                                       bg-gray-50 hover:bg-gray-100 border-l border-gray-300
                                       rounded-r-md text-sm text-blue-600 hover:text-blue-800"
                            >
                                Use Current
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Estimated Profit/Loss -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500">Estimated Profit/Loss</span>
                        <span id="modalProfitLoss" class="text-lg font-semibold"></span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3">
                    <button onclick="closeModal()"
                            class="px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700
                                   text-sm font-medium rounded-md focus:outline-none focus:ring-2
                                   focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button onclick="submitClose()"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm
                                   font-medium rounded-md focus:outline-none focus:ring-2
                                   focus:ring-offset-2 focus:ring-red-500">
                        Close Position
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Final Confirmation Modal (Custom Tailwind, no browser confirm) -->
    <div id="finalConfirmModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h3 id="finalConfirmTitle" class="text-lg font-semibold text-gray-900 mb-4">Confirm Close</h3>
            <p id="finalConfirmText" class="text-sm text-gray-600 mb-6">
                Are you sure you want to close this position? This action cannot be undone.
            </p>
            <div class="flex justify-end space-x-3">
                <button class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                        onclick="hideFinalConfirmationModal()">Cancel</button>
                <button id="finalConfirmYesBtn" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
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

    // Replaces old alerts in refreshPrices
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
                showClientErrorMessage('Failed to update prices. Please try again.');
            }
        } catch (error) {
            showClientErrorMessage('Error updating prices. Please try again.');
        }
    }

    // Variables for the close modal
    let currentInvestmentId = null;
    let currentPrice = 0;
    let currentAmount = 0;
    let buyPrice = 0;

    // We'll store the user-chosen sell price so that we can confirm it
    let chosenSellPrice = 0;

    function showCloseModal(id, symbol, price, amount, initialPrice) {
        currentInvestmentId = id;
        currentPrice = price;
        currentAmount = amount;
        buyPrice = initialPrice;

        document.getElementById('modalSymbol').textContent = symbol;
        document.getElementById('modalCurrentPrice').textContent = price.toFixed(2);
        document.getElementById('modalSellPrice').value = price.toFixed(2);

        // Calculate and display initial investment and current value
        const initialInvestment = buyPrice * amount;
        const currentValue = price * amount;
        document.getElementById('modalInitialInvestment').textContent = `$${initialInvestment.toFixed(2)}`;
        document.getElementById('modalCurrentValue').textContent = `$${currentValue.toFixed(2)}`;

        // Update the style + profit/loss right away
        updateSellPriceAppearance(price);
        document.getElementById('closeModal').classList.remove('hidden');
    }

    // Color-coded profit/loss
    function updateSellPriceAppearance(sellPrice) {
        sellPrice = parseFloat(sellPrice) || 0;

        const input = document.getElementById('modalSellPrice');
        const element = document.getElementById('modalProfitLoss');

        // Calculate numeric profit/loss
        const profitLoss = (sellPrice - buyPrice) * currentAmount;
        const profitLossPercentage = (buyPrice && currentAmount)
            ? (profitLoss / (buyPrice * currentAmount)) * 100
            : 0;

        // Update text
        element.textContent =
            `${profitLoss >= 0 ? '+' : ''}$${profitLoss.toFixed(2)} ` +
            `(${profitLossPercentage.toFixed(2)}%)`;

        // Reset classes
        input.classList.remove('border-emerald-600', 'border-red-600', 'bg-emerald-50', 'bg-red-50');
        element.classList.remove('text-emerald-600', 'text-red-600');

        // Apply color-coded styling
        if (profitLoss >= 0) {
            input.classList.add('border-emerald-600', 'bg-emerald-50');
            element.classList.add('text-emerald-600');
        } else {
            input.classList.add('border-red-600', 'bg-red-50');
            element.classList.add('text-red-600');
        }
    }

    // Listen for user typing changes
    document.getElementById('modalSellPrice').addEventListener('input', function(e) {
        updateSellPriceAppearance(e.target.value);
    });

    function useCurrentPrice() {
        const input = document.getElementById('modalSellPrice');
        input.value = currentPrice.toFixed(2);
        updateSellPriceAppearance(currentPrice);
    }

    function closeModal() {
        document.getElementById('closeModal').classList.add('hidden');
        document.getElementById('loadingOverlay').classList.add('hidden');
        currentInvestmentId = null;
    }

    // Step 1: user clicks "Close Position" -> we show second confirmation modal
    function submitClose() {
        chosenSellPrice = parseFloat(document.getElementById('modalSellPrice').value) || 0;

        if (!chosenSellPrice || chosenSellPrice <= 0) {
            showClientErrorMessage('Please enter a valid sell price.');
            return;
        }
        // Show custom final confirmation
        showFinalConfirmationModal();
    }

    // Step 2: final confirmation modal
    function showFinalConfirmationModal() {
        document.getElementById('finalConfirmModal').classList.remove('hidden');
    }

    function hideFinalConfirmationModal() {
        document.getElementById('finalConfirmModal').classList.add('hidden');
    }

    // Step 3: if user confirms, do the actual "close" request
    async function doConfirmClose() {
        try {
            hideFinalConfirmationModal();
            document.getElementById('loadingOverlay').classList.remove('hidden');
            // small delay for smooth overlay
            await new Promise(resolve => setTimeout(resolve, 500));

            window.location.href =
                `/FolioFlow/close-position?id=${currentInvestmentId}&price=${chosenSellPrice}`;
        } catch (error) {
            document.getElementById('loadingOverlay').classList.add('hidden');
            showClientErrorMessage('Error closing position. Please try again.');
        }
    }

    // Confirm button inside final modal
    document.getElementById('finalConfirmYesBtn').onclick = doConfirmClose;

    // Close final modal if clicking outside
    document.getElementById('finalConfirmModal').addEventListener('click', function(e) {
        if (e.target === this) {
            hideFinalConfirmationModal();
        }
    });

    // Backdrop click for closeModal
    document.getElementById('closeModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Handle Escape key
    document.addEventListener('keydown', function(e) {
        // If we're in the "Close Position" modal
        if (e.key === 'Escape' && !document.getElementById('closeModal').classList.contains('hidden')) {
            closeModal();
        }
        // If we're in the final confirmation modal
        if (e.key === 'Escape' && !document.getElementById('finalConfirmModal').classList.contains('hidden')) {
            hideFinalConfirmationModal();
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        // Auto-hide success alert after 3s
        const successAlert = document.getElementById("successAlert");
        if (successAlert) {
            setTimeout(() => {
                successAlert.style.opacity = "0";
                setTimeout(() => successAlert.remove(), 1000);
            }, 3000);
        }

        // Auto-hide error alert after 3s
        const errorAlert = document.getElementById("errorAlert");
        if (errorAlert) {
            setTimeout(() => {
                errorAlert.style.opacity = "0";
                setTimeout(() => errorAlert.remove(), 1000);
            }, 3000);
        }
    });

    function closeAlert(alertId) {
        const alertElement = document.getElementById(alertId);
        if (alertElement) {
            alertElement.style.opacity = "0";
            setTimeout(() => alertElement.remove(), 1000);
        }
    }
</script>

<?php require('partials/footer.php') ?>
