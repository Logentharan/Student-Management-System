<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $studentId = $_POST['student_id'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Validate passwords match
    if ($password !== $confirmPassword) {
        $error = "Passwords do not match";
    } else {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO students (first_name, last_name, email, student_id, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$firstName, $lastName, $email, $studentId, $hashedPassword]);
            
            // Set session variables
            $_SESSION['student_id'] = $pdo->lastInsertId();
            $_SESSION['student_name'] = $firstName . ' ' . $lastName;
            
            header('Location: login.php');
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Email or Student ID already exists";
            } else {
                $error = "Registration failed: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Student Profile System</title>
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
                <a href="login.php" class="hover:underline">Login</a>
                <a href="register.php" class="font-bold text-indigo-200">Register</a>
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
            <a href="login.php" class="hover:underline">Login</a>
            <a href="register.php" class="font-bold text-indigo-200">Register</a>
        </div>
    </div>
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden md:max-w-2xl card-shadow">
            <div class="md:flex">
                <div class="md:w-1/2 gradient-bg p-8 flex items-center justify-center">
                    <img src="icon.png" alt="University campus" class="rounded-lg">
                </div>
                <div class="p-8 md:w-1/2">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Student Registration</h2>
                    <?php if (isset($error)): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    <form id="register-form" method="POST">
                        <div class="grid md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="register-first-name" class="block text-gray-700 text-sm font-medium mb-2">First Name</label>
                                <input type="text" id="register-first-name" name="first_name" class="w-full px-4 py-2 border rounded-lg input-field" placeholder="John" required>
                            </div>
                            <div>
                                <label for="register-last-name" class="block text-gray-700 text-sm font-medium mb-2">Last Name</label>
                                <input type="text" id="register-last-name" name="last_name" class="w-full px-4 py-2 border rounded-lg input-field" placeholder="Doe" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="register-email" class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                            <input type="email" id="register-email" name="email" class="w-full px-4 py-2 border rounded-lg input-field" placeholder="your@email.com" required>
                        </div>
                        <div class="mb-4">
                            <label for="register-student-id" class="block text-gray-700 text-sm font-medium mb-2">Student ID</label>
                            <input type="text" id="register-student-id" name="student_id" class="w-full px-4 py-2 border rounded-lg input-field" placeholder="S12345678" required>
                        </div>
                        <div class="grid md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="register-password" class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                                <input type="password" id="register-password" name="password" class="w-full px-4 py-2 border rounded-lg input-field" placeholder="●●●●●●●●" required>
                            </div>
                            <div>
                                <label for="register-confirm-password" class="block text-gray-700 text-sm font-medium mb-2">Confirm Password</label>
                                <input type="password" id="register-confirm-password" name="confirm_password" class="w-full px-4 py-2 border rounded-lg input-field" placeholder="●●●●●●●●" required>
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition">
                            Register
                        </button>
                        <p class="text-center mt-4 text-gray-600">
                            Already have an account? <a href="login.php" class="text-purple-600 hover:underline">Login here</a>
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