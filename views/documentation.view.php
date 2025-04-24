<?php
require('partials/header.php');
require('partials/navbar.php');
?>

<main class="bg-slate-50">
    <!-- Hero Section -->
    <div class="relative isolate overflow-hidden">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(45rem_50rem_at_top,theme(colors.blue.900),theme(colors.slate.50))] opacity-20"></div>
        <div class="mx-auto max-w-7xl px-6 py-24 sm:py-32 text-center">
            <h1 class="text-4xl font-extrabold tracking-tight text-blue-900 sm:text-6xl">
                FolioFlow Documentation
            </h1>
            <p class="mt-6 text-lg leading-8 text-gray-600 max-w-2xl mx-auto">
                Everything you need to understand, run and extend <span class="font-semibold">FolioFlow</span> — from folder structure to deployment best‑practices.
            </p>
            <a href="#toc" class="mt-10 inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-6 py-3 text-white font-semibold shadow hover:bg-emerald-700 transition">
                Jump to Docs
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6" /></svg>
            </a>
        </div>
    </div>

    <!-- Documentation Content -->
    <div id="toc" class="py-24 bg-white">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <!-- Table of Contents -->
            <div class="mb-20">
                <h2 class="text-base font-semibold leading-7 text-emerald-600">Quick Nav</h2>
                <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3 text-sm">
                    <a href="#intro" class="hover:underline text-blue-900">Introduction</a>
                    <a href="#structure" class="hover:underline text-blue-900">Project Structure</a>
                    <a href="#usage" class="hover:underline text-blue-900">Usage Instructions</a>
                    <a href="#features" class="hover:underline text-blue-900">Features</a>
                    <a href="#schema" class="hover:underline text-blue-900">Database Schema</a>
                    <a href="#api" class="hover:underline text-blue-900">API Endpoints</a>
                    <a href="#roadmap" class="hover:underline text-blue-900">Improvements & Roadmap</a>
                </div>
            </div>

            <!-- Introduction -->
            <section id="intro" class="mx-auto max-w-2xl lg:text-center">
                <h2 class="text-base font-semibold leading-7 text-emerald-600">Introduction</h2>
                <p class="mt-2 text-3xl font-bold tracking-tight text-blue-900 sm:text-4xl">
                    For Developers, By Developers
                </p>
                <p class="mt-6 text-lg leading-8 text-gray-600">
                    FolioFlow began as an MVP to demonstrate end‑to‑end crypto portfolio tracking. The stack is intentionally minimal — plain PHP 7.4+, MySQL and TailwindCSS — to keep contribution barrier low. This page explains <span class="font-medium">why</span> certain choices were made, <span class="font-medium">how</span> it currently works, and <span class="font-medium">where</span> we can take it next.
                </p>
            </section>

            <!-- Project Structure -->
            <section id="structure" class="mt-20">
                <h2 class="text-xl font-bold text-blue-900">File Structure Overview</h2>
                <pre class="mt-4 bg-slate-100 p-4 rounded-lg text-sm text-gray-800 overflow-auto">
FolioFlow/
|-- api/                  # (reserved) Future REST/GraphQL endpoints
|-- classes/              # Core OOP classes (Database, Investment, ...)
|-- config/               # Environment + runtime configuration
|-- controllers/          # Thin controllers for each page/action
|-- middleware/           # Reusable request middleware (auth, price‑check)
|-- views/                # Tailwind‑powered Blade‑like partials
|-- logs/                 # Error & custom app logs
|-- index.php             # Single front‑controller & bootstrap
|-- router.php            # Simple path → controller dispatcher
|-- helpers.php           # Global helper functions (dd, urlIs, ...)
                </pre>
            </section>

            <!-- Usage Instructions -->
            <section id="usage" class="mt-16">
                <h2 class="text-xl font-bold text-blue-900">Usage Instructions</h2>
                <ol class="mt-4 space-y-4 text-gray-600 list-decimal list-inside">
                    <li>Clone & set up your <code>.env</code> (see README).</li>
                    <li>Register or log in to access your portfolio.</li>
                    <li>Add investments via <em>Add Investment</em> form.</li>
                    <li>Review active positions on <em>Investments</em> & <em>Dashboard</em>.</li>
                    <li>Open <em>Analytics</em> for profit/loss graphs.</li>
                    <li>Close positions when ready — history is kept under <em>Closed Positions</em>.</li>
                </ol>
            </section>

            <!-- Features Grid -->
            <section id="features" class="mt-16">
                <h2 class="text-xl font-bold text-blue-900">Feature Highlights</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
                    <?php
                    $cards = [
                        ['Secure Authentication','Robust session & Argon2id hashing.'],
                        ['Smart Investment Management','150+ cryptos + USDT auto‑suffix.'],
                        ['Detailed Analytics','Monthly P/L, best & worst returns.'],
                        ['Real‑Time Tracking','Binance API with 15‑min smart cache.'],
                        ['Streamlined Dashboard','Key metrics at a glance.'],
                        ['Position History','Immutable logs for every update.']
                    ];
                    foreach($cards as [$title,$desc]): ?>
                        <div class="p-6 bg-white shadow rounded-lg text-center">
                            <h3 class="text-lg font-semibold text-blue-900"><?php echo $title; ?></h3>
                            <p class="mt-2 text-gray-600"><?php echo $desc; ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Database Schema -->
            <section id="schema" class="mt-16">
                <h2 class="text-xl font-bold text-blue-900">Database Schema (Core)</h2>
                <pre class="mt-4 bg-slate-100 p-4 rounded-lg text-sm text-gray-800 overflow-auto">
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE symbols (
    id INT AUTO_INCREMENT PRIMARY KEY,
    symbol VARCHAR(20) UNIQUE,
    name VARCHAR(50),
    type ENUM('crypto','stock') DEFAULT 'crypto',
    current_price DECIMAL(18,8),
    last_updated TIMESTAMP NULL
);

CREATE TABLE investments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(20),
    buy_price DECIMAL(18,8),
    amount DECIMAL(18,8),
    status ENUM('active','closed') DEFAULT 'active',
    sell_price DECIMAL(18,8) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    closed_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE investment_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    investment_id INT,
    previous_amount DECIMAL(18,8),
    added_amount DECIMAL(18,8),
    previous_buy_price DECIMAL(18,8),
    new_buy_price DECIMAL(18,8),
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (investment_id) REFERENCES investments(id)
);
                </pre>
            </section>

            <!-- API Endpoints -->
            <section id="api" class="mt-16">
                <h2 class="text-xl font-bold text-blue-900">Planned REST API (draft)</h2>
                <table class="mt-4 w-full text-left text-sm">
                    <thead class="bg-slate-100 font-medium text-gray-800">
                    <tr><th class="px-4 py-2">Method</th><th class="px-4 py-2">Endpoint</th><th class="px-4 py-2">Description</th></tr>
                    </thead>
                    <tbody class="divide-y">
                    <tr><td class="px-4 py-2">GET</td><td class="px-4 py-2">/api/investments</td><td class="px-4 py-2">List active investments</td></tr>
                    <tr><td class="px-4 py-2">POST</td><td class="px-4 py-2">/api/investments</td><td class="px-4 py-2">Create new investment</td></tr>
                    <tr><td class="px-4 py-2">PATCH</td><td class="px-4 py-2">/api/investments/{id}</td><td class="px-4 py-2">Update position</td></tr>
                    <tr><td class="px-4 py-2">DELETE</td><td class="px-4 py-2">/api/investments/{id}</td><td class="px-4 py-2">Close/Delete position</td></tr>
                    </tbody>
                </table>
            </section>

            <!-- Roadmap & Known Issues -->
            <section id="roadmap" class="mt-16">
                <h2 class="text-xl font-bold text-blue-900">Known Issues & Roadmap</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
                    <div>
                        <h3 class="text-lg font-semibold text-red-600">Known Issues</h3>
                        <ul class="mt-4 list-disc pl-5 space-y-3 text-gray-600">
                            <li><strong>Float Handling:</strong> Dynamic precision needed for very small‑cap coins.</li>
                            <li><strong>Performance:</strong> Price update bursts may hit API rate‑limits on large portfolios.</li>
                            <li><strong>Analytics Customization:</strong> User‑defined filters & export not yet available.</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-emerald-600">Planned Improvements</h3>
                        <ul class="mt-4 list-disc pl-5 space-y-3 text-gray-600">
                            <li>Custom analytics filters, CSV / PDF export.</li>
                            <li>Multi‑currency support with live FX rates.</li>
                            <li>Email / webhook alerts for price swings & milestones.</li>
                            <li>2FA & CSRF tokens for enhanced security.</li>
                        </ul>
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>

<?php require('partials/footer.php'); ?>
