<?php require('partials/header.php') ?>
<?php require('partials/navbar.php') ?>

    <main class="bg-slate-50">
        <!-- Hero Section -->
        <div class="relative isolate overflow-hidden">
            <div class="absolute inset-0 -z-10 bg-[radial-gradient(45rem_50rem_at_top,theme(colors.blue.900),theme(colors.slate.50))] opacity-20"></div>
            <div class="mx-auto max-w-7xl px-6 py-24 sm:py-32">
                <!-- Main Hero Content -->
                <div class="text-center">
                    <h1 class="text-4xl font-bold tracking-tight text-blue-900 sm:text-6xl">
                        Track Your Investments Like a Pro
                    </h1>
                    <p class="mt-6 text-lg leading-8 text-gray-600 max-w-2xl mx-auto">
                        FolioFlow helps you monitor your cryptocurrency investments in real-time, track performance, and make informed decisions with professional-grade tools.
                    </p>
                    <div class="mt-10 flex items-center justify-center gap-x-6">
                        <a href="/FolioFlow/register"
                           class="rounded-md bg-emerald-600 px-6 py-3 text-lg font-semibold text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600">
                            Get Started Free
                        </a>
                        <a href="#features" class="text-lg font-semibold leading-6 text-blue-900 hover:text-blue-700">
                            Learn more <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="mt-20 py-8">
                    <div class="grid grid-cols-1 gap-8 sm:grid-cols-3">
                        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                            <div class="text-4xl font-bold text-blue-900">100+</div>
                            <div class="mt-2 text-gray-600">Supported Cryptocurrencies</div>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                            <div class="text-4xl font-bold text-blue-900">Real-time</div>
                            <div class="mt-2 text-gray-600">Price Updates</div>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                            <div class="text-4xl font-bold text-blue-900">Free</div>
                            <div class="mt-2 text-gray-600">Portfolio Tracking</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div id="features" class="py-24 bg-white sm:py-32">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl lg:text-center">
                    <h2 class="text-base font-semibold leading-7 text-emerald-600">Features</h2>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-blue-900 sm:text-4xl">
                        Everything you need to manage your portfolio
                    </p>
                </div>
                <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
                    <div class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-3">
                        <!-- Feature 1 -->
                        <div class="flex flex-col">
                            <div class="bg-blue-900 rounded-lg p-2 w-10 h-10 flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                            <div class="mt-4">
                                <h3 class="text-lg font-semibold text-blue-900">Real-time Tracking</h3>
                                <p class="mt-2 text-gray-600">Monitor your investments with real-time price updates and portfolio valuation.</p>
                            </div>
                        </div>

                        <!-- Feature 2 -->
                        <div class="flex flex-col">
                            <div class="bg-blue-900 rounded-lg p-2 w-10 h-10 flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                                </svg>
                            </div>
                            <div class="mt-4">
                                <h3 class="text-lg font-semibold text-blue-900">Performance Analytics</h3>
                                <p class="mt-2 text-gray-600">Detailed performance metrics and analytics to track your investment success.</p>
                            </div>
                        </div>

                        <!-- Feature 3 -->
                        <div class="flex flex-col">
                            <div class="bg-blue-900 rounded-lg p-2 w-10 h-10 flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <div class="mt-4">
                                <h3 class="text-lg font-semibold text-blue-900">Secure Platform</h3>
                                <p class="mt-2 text-gray-600">Bank-grade security to keep your investment data safe and private.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="bg-blue-900">
            <div class="mx-auto max-w-7xl py-16 px-6 sm:py-24 lg:px-8">
                <div class="text-center">
                    <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                        Ready to start tracking your investments?
                    </h2>
                    <div class="mt-10 flex justify-center">
                        <a href="/FolioFlow/register"
                           class="rounded-md bg-emerald-600 px-8 py-4 text-lg font-semibold text-white shadow-sm hover:bg-emerald-500">
                            Create Free Account
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php require('partials/footer.php') ?>