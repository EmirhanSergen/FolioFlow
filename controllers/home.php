<?php

// Define variables for the home page
$pageTitle = "Welcome to FolioFlow"; // Page title for the home page

// Dynamic features for the home page
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

// Call-to-action data
$cta = [
    'title' => 'Ready to start tracking your investments?',
    'button_text' => 'Create Free Account',
    'button_link' => '/FolioFlow/register'
];

// Load the view
require 'views/home.view.php';
