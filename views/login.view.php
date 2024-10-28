<?php require('partials/header.php') ?>
<?php require('partials/navbar.php') ?>

    <main class="min-h-screen bg-slate-50 py-12">
        <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Login Card -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-900 to-blue-800 px-6 py-8">
                    <h1 class="text-2xl font-bold text-white text-center">Welcome Back</h1>
                    <p class="mt-2 text-blue-100 text-center">Sign in to access your portfolio</p>
                </div>

                <div class="p-6">
                    <?php if (!empty($errors['general'])): ?>
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700"><?php echo htmlspecialchars($errors['general']); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="/FolioFlow/login">
                        <!-- Email Input -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="email">
                                Email Address
                            </label>
                            <input
                                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    id="email"
                                    name="email"
                                    type="email"
                                    required
                                    placeholder="you@example.com"
                            >
                        </div>

                        <!-- Password Input -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="password">
                                Password
                            </label>
                            <input
                                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    id="password"
                                    name="password"
                                    type="password"
                                    required
                                    placeholder="••••••••"
                            >
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-between mb-6">
                            <button type="submit"
                                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-4 rounded-md shadow-sm transition duration-150">
                                Sign In
                            </button>
                        </div>

                        <!-- Register Link -->
                        <p class="text-center text-sm text-gray-600">
                            Don't have an account?
                            <a href="/FolioFlow/register" class="text-blue-900 hover:text-blue-700 font-semibold">
                                Create one now
                            </a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </main>

<?php require('partials/footer.php') ?>