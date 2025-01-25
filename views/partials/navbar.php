<nav class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 py-3">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-8">

                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="/FolioFlow/dashboard" class="text-xl font-semibold text-blue-900">FolioFlow</a>
                    <!-- Primary Navigation -->
                    <div class="hidden md:flex items-center space-x-1">
                        <!-- Core Features -->
                        <a href="/FolioFlow/dashboard"
                           class="px-3 py-2 rounded-md text-sm font-medium <?= urlIs('/FolioFlow/dashboard') ? 'bg-blue-50 text-blue-900' : 'text-gray-600 hover:text-blue-900 hover:bg-blue-50' ?>">
                            Dashboard
                        </a>
                        <a href="/FolioFlow/investments"
                           class="px-3 py-2 rounded-md text-sm font-medium <?= urlIs('/FolioFlow/investments') ? 'bg-blue-50 text-blue-900' : 'text-gray-600 hover:text-blue-900 hover:bg-blue-50' ?>">
                            Portfolio
                        </a>
                        <!-- Investment Management -->
                        <div class="relative group">
                            <button class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-blue-900 hover:bg-blue-50 inline-flex items-center">
                                <span>Manage</span>
                                <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <!-- Add padding-top to create hover space -->
                            <div class="absolute left-0 pt-2 w-48 opacity-0 translate-y-1 invisible group-hover:opacity-100 group-hover:translate-y-0 group-hover:visible transition-all duration-300">
                                <div class="bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5">
                                    <div class="py-1">
                                        <a href="/FolioFlow/add-investment"
                                           class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50">
                                            Add Investment
                                        </a>
                                        <a href="/FolioFlow/closed-positions"
                                           class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50">
                                            Closed Positions
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Analytics -->
                        <a href="/FolioFlow/analytics"
                           class="px-3 py-2 rounded-md text-sm font-medium <?= urlIs('/FolioFlow/analytics') ? 'bg-blue-50 text-blue-900' : 'text-gray-600 hover:text-blue-900 hover:bg-blue-50' ?>">
                            Advanced Analytics
                        </a>
                        <!-- Documentation -->
                        <a href="/FolioFlow/documentation"
                           class="px-3 py-2 rounded-md text-sm font-medium <?= urlIs('/FolioFlow/documentation') ? 'bg-blue-50 text-blue-900' : 'text-gray-600 hover:text-blue-900 hover:bg-blue-50' ?>">
                            Documentation
                        </a>
                    </div>

                <?php else :?>
                    <a href="/FolioFlow" class="text-xl font-semibold text-blue-900">FolioFlow</a>
                    <!-- Documentation for Visitors -->
                    <a href="/FolioFlow/documentation"
                       class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-blue-900 hover:bg-blue-50">
                        Documentation
                    </a>
                <?php endif; ?>

            </div>

            <div class="flex items-center space-x-4">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">
                            <?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>
                        </span>
                        <a href="/FolioFlow/logout"
                           class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">
                            Logout
                        </a>
                    </div>
                <?php else: ?>
                    <a href="/FolioFlow/login"
                       class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">
                        Login
                    </a>
                    <a href="/FolioFlow/register"
                       class="px-4 py-2 text-sm font-medium text-white bg-blue-900 rounded-lg hover:bg-blue-800">
                        Sign Up
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
