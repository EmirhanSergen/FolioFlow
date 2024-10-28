<?php require('partials/header.php') ?>
<?php require('partials/navbar.php') ?>

    <main class="max-w-6xl mx-auto px-4 py-16">
        <div class="text-center">
            <h1 class="text-6xl font-bold text-indigo-600 mb-4"><?= $code ?></h1>
            <p class="text-2xl font-semibold text-gray-900 mb-4"><?= $error['title'] ?></p>
            <p class="text-gray-600 mb-8"><?= $error['message'] ?></p>
            <a href="/" class="inline-block px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Go Back Home
            </a>
        </div>
    </main>

<?php require('partials/footer.php') ?>