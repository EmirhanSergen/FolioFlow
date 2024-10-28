<nav class="bg-white shadow-sm">
    <div class="max-w-6xl mx-auto px-4 py-3">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-8">
                <a href="/FolioFlow" class="text-xl font-semibold text-indigo-600">FolioFlow</a>

                <?php if(isset($_SESSION['user_id'])): ?>
                    <!-- Show these links only if user is logged in -->
                    <div class="hidden md:flex space-x-6">
                        <a href="/FolioFlow/dashboard" class="text-gray-600 hover:text-indigo-600">Dashboard</a>
                        <a href="/FolioFlow/investments" class="text-gray-600 hover:text-indigo-600">My Investments</a>
                        <a href="/FolioFlow/add-investment" class="text-gray-600 hover:text-indigo-600">Add Investment</a>
                        <a href="/FolioFlow/closed-positions" class="text-gray-600 hover:text-indigo-600">Closed Positions</a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="flex items-center space-x-4">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <!-- Show these when user is logged in -->
                    <span class="text-gray-600">Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></span>
                    <a href="/FolioFlow/logout" class="px-4 py-2 text-gray-600 hover:text-gray-900">Logout</a>
                <?php else: ?>
                    <!-- Show these when user is not logged in -->
                    <a href="/FolioFlow/login" class="px-4 py-2 text-gray-600 hover:text-gray-900">Login</a>
                    <a href="/FolioFlow/register" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Sign Up
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>