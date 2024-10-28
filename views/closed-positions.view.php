<?php require('partials/header.php') ?>
<?php require('partials/navbar.php') ?>

    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Closed Positions</h1>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($positions)): ?>
            <div class="bg-white shadow-md rounded my-6 overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold text-gray-600">Name</th>
                        <th class="py-3 px-4 text-right font-semibold text-gray-600">Buy Price</th>
                        <th class="py-3 px-4 text-right font-semibold text-gray-600">Sell Price</th>
                        <th class="py-3 px-4 text-right font-semibold text-gray-600">Profit</th>
                        <th class="py-3 px-4 text-right font-semibold text-gray-600">Amount</th>
                        <th class="py-3 px-4 text-left font-semibold text-gray-600">Closed At</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    <?php foreach ($positions as $position): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="py-4 px-4 text-gray-800">
                                <?= htmlspecialchars($position['name']) ?>
                            </td>
                            <td class="py-4 px-4 text-right text-gray-800">
                                $<?= number_format($position['buy_price'], 2) ?>
                            </td>
                            <td class="py-4 px-4 text-right text-gray-800">
                                $<?= number_format($position['sell_price'], 2) ?>
                            </td>
                            <td class="py-4 px-4 text-right <?= $position['profit'] >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                                $<?= number_format($position['profit'], 2) ?>
                            </td>
                            <td class="py-4 px-4 text-right text-gray-800">
                                <?= number_format($position['amount'], 2) ?>
                            </td>
                            <td class="py-4 px-4 text-gray-800">
                                <?= date('M d, Y', strtotime($position['closed_at'])) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="bg-white shadow-md rounded px-8 py-6 mb-4 text-gray-600">
                <p>No closed positions found.</p>
            </div>
        <?php endif; ?>
    </div>

<?php require('partials/footer.php') ?>