<?php

// Define variables for the home page
$pageTitle = "Welcome to FolioFlow";
$features = [
    [
        'icon' => '📈',
        'title' => 'Track Performance',
        'description' => 'Monitor your investments in real-time with detailed analytics'
    ],
    [
        'icon' => '📊',
        'title' => 'Portfolio Overview',
        'description' => 'Get a clear view of your entire investment portfolio'
    ],
    [
        'icon' => '📝',
        'title' => 'Investment Notes',
        'description' => 'Keep detailed notes and track your investment decisions'
    ]
];

// Load the view
require 'views/home.view.php';