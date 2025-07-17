<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['student_id'] = $user['id'];
        $_SESSION['student_name'] = $user['first_name'] . ' ' . $user['last_name'];
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Student Profile System</title>
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
                <a href="index.php" class="hover:underline">Home</a>
                <a href="login.php" class="font-bold text-indigo-200">Login</a>
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
            <a href="index.php" class="hover:underline">Home</a>
            <a href="login.php" class="font-bold text-indigo-200">Login</a>
            <a href="register.php" class="hover:underline">Register</a>
        </div>
    </div>
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden md:max-w-2xl card-shadow">
            <div class="md:flex">
                <div class="md:w-1/2 gradient-bg p-8 flex items-center justify-center">
                    <img src="icon.png" alt="Students studying together" class="rounded-lg">
                </div>
                <div class="p-8 md:w-1/2">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Student Login</h2>
                    <?php if (isset($error)): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    <form id="login-form" method="POST">
                        <div class="mb-4">
                            <label for="login-email" class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                            <input type="email" id="login-email" name="email" class="w-full px-4 py-2 border rounded-lg input-field" placeholder="your@email.com" required>
                        </div>
                        <div class="mb-6">
                            <label for="login-password" class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                            <input type="password" id="login-password" name="password" class="w-full px-4 py-2 border rounded-lg input-field" placeholder="●●●●●●●●" required>
                        </div>
                        <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 transition">
                            Login
                        </button>
                        <p class="text-center mt-4 text-gray-600">
                            Don't have an account? <a href="register.php" class="text-indigo-600 hover:underline">Register here</a>
                        </p>
                    </form>
                </div>
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