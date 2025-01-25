<?php require('partials/header.php') ?>
<?php require('partials/navbar.php') ?>

<main class="bg-slate-50">
    <!-- Hero Section -->
    <div class="relative isolate overflow-hidden">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(45rem_50rem_at_top,theme(colors.blue.900),theme(colors.slate.50))] opacity-20"></div>
        <div class="mx-auto max-w-7xl px-6 py-24 sm:py-32">
            <div class="text-center">
                <h1 class="text-4xl font-bold tracking-tight text-blue-900 sm:text-6xl">
                    FolioFlow Documentation
                </h1>
                <p class="mt-6 text-lg leading-8 text-gray-600 max-w-2xl mx-auto">
                    Built as an MVP, this documentation details the architecture, current features, and areas for improvement to guide developers in understanding and extending FolioFlow.
                </p>
            </div>
        </div>
    </div>

    <!-- Documentation Content -->
    <div class="py-24 bg-white">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">

            <!-- Introduction -->
            <div class="mx-auto max-w-2xl lg:text-center">
                <h2 class="text-base font-semibold leading-7 text-emerald-600">Introduction</h2>
                <p class="mt-2 text-3xl font-bold tracking-tight text-blue-900 sm:text-4xl">
                    For Developers, By Developers
                </p>
                <p class="mt-6 text-lg leading-8 text-gray-600">
                    FolioFlow is an MVP designed to showcase core portfolio tracking functionality. This documentation provides
                    insights into the rationale behind design choices, explains the current implementation, and outlines opportunities
                    for future development. Whether you're contributing or building similar systems, this is your guide.
                </p>
            </div>

            <!-- File Structure Overview -->
            <div class="mt-16">
                <h2 class="text-xl font-bold text-blue-900">File Structure Overview</h2>
                <pre class="mt-4 bg-slate-100 p-4 rounded-lg text-sm text-gray-800 overflow-auto">
FolioFlow/
|
|-- api/                # API routes and logic
|-- classes/            # Core application classes
|-- config/             # Configuration files
|-- controllers/        # Controllers for handling requests
|-- logs/               # Log files
|-- middleware/         # Middleware for request handling
|-- views/              # View templates
|-- functions.php       # Helper functions
|-- index.php           # Entry point of the application
|-- router.php          # Routing logic
                </pre>
            </div>

            <!-- Usage Instructions -->
            <div class="mt-16">
                <h2 class="text-xl font-bold text-blue-900">Usage Instructions</h2>
                <ol class="mt-4 space-y-4 text-gray-600 list-decimal list-inside">
                    <li>Register or log in to access your portfolio.</li>
                    <li>Add investments using the dropdown menu and relevant details.</li>
                    <li>View a summary of active investments on the dashboard.</li>
                    <li>Analyze performance using the advanced analytics section.</li>
                    <li>Keep prices updated in real-time by running the "Update Prices" feature.</li>
                    <li>Close positions to book profits or mitigate losses as needed.</li>
                </ol>
            </div>

            <!-- Features Section with Card Layout -->
            <div class="mt-16">
                <h2 class="text-xl font-bold text-blue-900">Features</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
                    <div class="p-6 bg-white shadow rounded-lg text-center">
                        <h3 class="text-lg font-semibold text-blue-900">Secure Authentication</h3>
                        <p class="mt-2 text-gray-600">Robust login ensures data safety and restricted access.</p>
                    </div>
                    <div class="p-6 bg-white shadow rounded-lg text-center">
                        <h3 class="text-lg font-semibold text-blue-900">Smart Investment Management</h3>
                        <p class="mt-2 text-gray-600">Manage investments with a dropdown featuring 150 cryptocurrencies.</p>
                    </div>
                    <div class="p-6 bg-white shadow rounded-lg text-center">
                        <h3 class="text-lg font-semibold text-blue-900">Detailed Analytics</h3>
                        <p class="mt-2 text-gray-600">Gain insights with performance reports and historical trends.</p>
                    </div>
                    <div class="p-6 bg-white shadow rounded-lg text-center">
                        <h3 class="text-lg font-semibold text-blue-900">Real-Time Tracking</h3>
                        <p class="mt-2 text-gray-600">Monitor prices and portfolio value dynamically.</p>
                    </div>
                    <div class="p-6 bg-white shadow rounded-lg text-center">
                        <h3 class="text-lg font-semibold text-blue-900">Streamlined Dashboard</h3>
                        <p class="mt-2 text-gray-600">Centralized view for quick metrics access.</p>
                    </div>
                    <div class="p-6 bg-white shadow rounded-lg text-center">
                        <h3 class="text-lg font-semibold text-blue-900">Closing Positions</h3>
                        <p class="mt-2 text-gray-600">Close investments to realize profits or minimize losses.</p>
                    </div>
                </div>
            </div>

            <!-- Known Issues and Improvements Section with Two-Column Layout -->
            <div class="mt-16">
                <h2 class="text-xl font-bold text-blue-900">Known Issues and Improvements</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
                    <div>
                        <h3 class="text-lg font-semibold text-red-600">Known Issues</h3>
                        <ul class="mt-4 list-disc pl-5 space-y-4">
                            <li><strong>Float Handling:</strong> Adjust dynamic float precision for better price visualization.</li>
                            <li><strong>Performance:</strong> Optimize for high traffic during peak usage.</li>
                            <li><strong>Limited Analytics Customization</strong>Users currently cannot customize reports or set specific parameters for analysis.</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-emerald-600">Improvements</h3>
                        <ul class="mt-4 list-disc pl-5 space-y-4">
                            <li>Introduce custom analytics filters and exportable formats.</li>
                            <li>Enable multi-currency management with automatic conversions.</li>
                            <li>Add alerts for significant price changes, portfolio milestones, or other user-defined triggers.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require('partials/footer.php') ?>
