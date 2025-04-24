<nav class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-5 ">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-12">

                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="/FolioFlow/dashboard" class="text-2xl font-bold text-blue-900">FolioFlow</a>
                    <!-- Primary Navigation -->
                    <div class="hidden md:flex items-center space-x-2">
                        <!-- Core Features -->
                        <a href="/FolioFlow/dashboard"
                           class="px-4 py-3 rounded-md text-base font-semibold <?= urlIs('/FolioFlow/dashboard') ? 'bg-blue-50 text-blue-900' : 'text-gray-600 hover:text-blue-900 hover:bg-blue-50' ?>">
                            Dashboard
                        </a>
                        <a href="/FolioFlow/investments"
                           class="px-4 py-3 rounded-md text-base font-semibold <?= urlIs('/FolioFlow/investments') ? 'bg-blue-50 text-blue-900' : 'text-gray-600 hover:text-blue-900 hover:bg-blue-50' ?>">
                            Portfolio
                        </a>

                        <!-- Investment Management -->
                        <div class="relative group">
                            <button class="px-4 py-3 rounded-md text-base font-semibold text-gray-600 hover:text-blue-900 hover:bg-blue-50 inline-flex items-center">
                                <span>Manage</span>
                                <svg class="ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="absolute left-0 pt-2 w-52 opacity-0 translate-y-1 invisible group-hover:opacity-100 group-hover:translate-y-0 group-hover:visible transition-all duration-300">
                                <div class="bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5">
                                    <div class="py-1">
                                        <a href="/FolioFlow/add-investment"
                                           class="block px-4 py-3 text-base text-gray-700 hover:bg-blue-50">
                                            Add Investment
                                        </a>
                                        <a href="/FolioFlow/closed-positions"
                                           class="block px-4 py-3 text-base text-gray-700 hover:bg-blue-50">
                                            Closed Positions
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Analytics -->
                        <a href="/FolioFlow/analytics"
                           class="px-4 py-3 rounded-md text-base font-semibold <?= urlIs('/FolioFlow/analytics') ? 'bg-blue-50 text-blue-900' : 'text-gray-600 hover:text-blue-900 hover:bg-blue-50' ?>">
                            Advanced Analytics
                        </a>
                        <!-- Documentation -->
                        <a href="/FolioFlow/documentation"
                           class="px-4 py-3 rounded-md text-base font-semibold <?= urlIs('/FolioFlow/documentation') ? 'bg-blue-50 text-blue-900' : 'text-gray-600 hover:text-blue-900 hover:bg-blue-50' ?>">
                            Documentation
                        </a>
                    </div>

                <?php else :?>
                    <a href="/FolioFlow" class="text-2xl font-bold text-blue-900">FolioFlow</a>
                    <a href="/FolioFlow/documentation"
                       class="px-4 py-3 rounded-md text-base font-semibold text-gray-600 hover:text-blue-900 hover:bg-blue-50">
                        Documentation
                    </a>
                <?php endif; ?>

            </div>

            <div class="flex items-center space-x-6">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="flex items-center space-x-4">
                        <span class="text-base text-gray-600">
                            <?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>
                        </span>
                        <a href="/FolioFlow/logout"
                           class="px-4 py-2 text-base font-medium text-gray-600 hover:text-gray-900">
                            Logout
                        </a>
                    </div>
                <?php else: ?>
                    <a href="/FolioFlow/login"
                       class="px-4 py-2 text-base font-medium text-gray-600 hover:text-gray-900">
                        Login
                    </a>
                    <a href="/FolioFlow/register"
                       class="px-4 py-2 text-base font-medium text-white bg-blue-900 rounded-lg hover:bg-blue-800">
                        Sign Up
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
