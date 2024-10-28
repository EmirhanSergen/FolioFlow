<?php require('partials/header.php') ?>
<?php require('partials/navbar.php') ?>

    <main class="max-w-lg mx-auto mt-10">
        <form method="POST" action="/FolioFlow/register" class="bg-white p-8 rounded-lg shadow">
            <h1 class="text-3xl font-bold mb-6 text-center">Create Account</h1>

            <?php if (!empty($errors['general'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <?php echo htmlspecialchars($errors['general']); ?>
                </div>
            <?php endif; ?>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                    Username
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="username"
                       name="username"
                       type="text"
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                       required>
                <?php if (!empty($errors['username'])): ?>
                    <p class="text-red-500 text-xs italic"><?php echo htmlspecialchars($errors['username']); ?></p>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                    Email
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="email"
                       name="email"
                       type="email"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                       required>
                <?php if (!empty($errors['email'])): ?>
                    <p class="text-red-500 text-xs italic"><?php echo htmlspecialchars($errors['email']); ?></p>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    Password
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="password"
                       name="password"
                       type="password"
                       required>
                <?php if (!empty($errors['password'])): ?>
                    <p class="text-red-500 text-xs italic"><?php echo htmlspecialchars($errors['password']); ?></p>
                <?php endif; ?>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="confirm_password">
                    Confirm Password
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="confirm_password"
                       name="confirm_password"
                       type="password"
                       required>
                <?php if (!empty($errors['confirm_password'])): ?>
                    <p class="text-red-500 text-xs italic"><?php echo htmlspecialchars($errors['confirm_password']); ?></p>
                <?php endif; ?>
            </div>

            <div class="flex items-center justify-between">
                <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                        type="submit">
                    Create Account
                </button>
                <a class="inline-block align-baseline font-bold text-sm text-indigo-600 hover:text-indigo-800"
                   href="/FolioFlow/login">
                    Already have an account?
                </a>
            </div>
        </form>
    </main>

<?php require('partials/footer.php') ?>