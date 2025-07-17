<?php
require_once 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="min-h-screen bg-gray-100">
    <nav class="gradient-bg text-white shadow-md">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <img src="https://placehold.co/40x40" alt="University logo" class="rounded-full">
                <span class="text-xl font-bold">StudentProfiles</span>
            </div>
            <div class="hidden md:flex space-x-6">
                <a href="index.php" class="hover:underline font-bold text-indigo-200">Home</a>
                <a href="login.php" class="hover:underline">Login</a>
                <a href="register.php" class="hover:underline">Register</a>
            </div>
            <button id="mobile-menu-btn" class="md:hidden text-white focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </nav>
    <div class="mobile-menu hidden gradient-bg text-white py-4">
        <div class="container mx-auto px-4 flex flex-col space-y-4">
            <a href="index.php" class="hover:underline font-bold text-indigo-200">Home</a>
            <a href="login.php" class="hover:underline">Login</a>
            <a href="register.php" class="hover:underline">Register</a>
        </div>
    </div>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Student Profile System</h1>
            <p class="text-xl text-gray-600">
                Manage student profiles efficiently with our comprehensive platform.
            </p>
        </div>
        <div class="grid md:grid-cols-2 gap-8 mb-12">
            <div class="bg-white rounded-xl p-8 card-shadow">
                <div class="flex items-center mb-6">
                    <div class="bg-indigo-100 p-3 rounded-full mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Student Profiles</h2>
                </div>
                <p class="text-gray-600 mb-6">
                    Access detailed student profiles with academic records and contact information.
                </p>
                <a href="login.php" class="inline-block bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                    Login to Access
                </a>
            </div>
            <div class="bg-white rounded-xl p-8 card-shadow">
                <div class="flex items-center mb-6">
                    <div class="bg-purple-100 p-3 rounded-full mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Registration</h2>
                </div>
                <p class="text-gray-600 mb-6">
                    New students can register to create their profile and gain system access.
                </p>
                <a href="register.php" class="inline-block bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                    Register Now
                </a>
            </div>
        </div>
    </div>
    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto px-4 text-center text-gray-400 text-sm">
            <p>© 2023 StudentProfiles. All rights reserved.</p>
        </div>
    </footer>
    <script src="script.js"></script>
    <script>
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            document.querySelector('.mobile-menu').classList.toggle('hidden');
        });
    </script>
</body>
</html>