<?php require('partials/header.php') ?>
<?php require('partials/navbar.php') ?>

    <main class="min-h-screen bg-slate-50 py-16">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-900 to-blue-800 px-6 py-8 text-center">
                    <h1 class="text-5xl font-bold text-white mb-2"><?= $code ?></h1>
                    <p class="text-2xl font-bold text-white mb-2" >There is not such a page</p>
                </div>
                <div class="p-6 text-center">
                    <a href="/FolioFlow"
                       class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-emerald-600 hover:bg-emerald-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Return Home
                    </a>
                </div>
            </div>
        </div>
    </main>

<?php require('partials/footer.php') ?>