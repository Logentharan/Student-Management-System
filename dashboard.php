<?php
require_once 'db.php';

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit();
}

// Get student data
try {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$_SESSION['student_id']]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching student data: " . $e->getMessage());
}

// Get courses for the student
try {
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE student_id = ?");
    $stmt->execute([$student['id']]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching courses: " . $e->getMessage());
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Student Profile System</title>
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
                <a href="dashboard.php" class="font-bold text-indigo-200">Dashboard</a>
                <a href="academic-history.php" class="hover:underline">Academic History</a>
                <button onclick="logout()" class="flex items-center text-white hover:text-red-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" />
                    </svg>
                    Logout
                </button>
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
            <a href="dashboard.php" class="hover:underline">Dashboard</a>
            <a href="academic-history.php" class="hover:underline">Academic History</a>
            <button onclick="logout()" class="flex items-center text-white hover:text-red-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" />
                </svg>
                Logout
            </button>
        </div>
    </div>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto mb-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Student Dashboard</h1>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 mb-8 card-shadow">
                <div class="flex flex-col md:flex-row items-center">
                    <div class="mr-6 mb-4 md:mb-0">
                        <img src="https://placehold.co/150x150" id="profile-picture" alt="Student profile portrait" class="rounded-full w-32 h-32 object-cover border-4 border-indigo-100">
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-800 mb-2" id="student-name"><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></h2>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <h4 class="text-sm text-gray-500 font-medium">Student ID</h4>
                                <p class="text-gray-800"><?php echo htmlspecialchars($student['student_id']); ?></p>
                            </div>
                            <div>
                                <h4 class="text-sm text-gray-500 font-medium">Email</h4>
                                <p class="text-gray-800"><?php echo htmlspecialchars($student['email']); ?></p>
                            </div>
                            <div>
                                <h4 class="text-sm text-gray-500 font-medium">Major</h4>
                                <p class="text-gray-800"><?php echo htmlspecialchars($student['major']); ?></p>
                            </div>
                            <div>
                                <h4 class="text-sm text-gray-500 font-medium">Year</h4>
                                <p class="text-gray-800"><?php echo htmlspecialchars($student['year']); ?></p>
                            </div>
                        </div>
                        <button onclick="window.location.href='edit-profile.php'" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                            Edit Profile
                        </button>
                    </div>
                </div>
            </div>
            <div class="grid md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-md p-6 card-shadow">
                    <div class="flex items-center mb-4">
                        <div class="bg-green-100 p-2 rounded-full mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-800">Academic Status</h3>
                    </div>
                    <p class="text-gray-600 mb-2">GPA: <span class="font-medium text-gray-800"><?php echo htmlspecialchars($student['gpa']); ?></span></p>
                    <p class="text-gray-600 mb-2">Credits Earned: <span class="font-medium text-gray-800"><?php echo htmlspecialchars($student['credits']); ?></span></p>
                    <p class="text-gray-600">Status: <span class="font-medium text-green-600"><?php echo htmlspecialchars($student['status']); ?></span></p>
                    <a href="edit-academic.php" class="inline-block bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition mt-2">
                        Update Academic Info
                    </a>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6 card-shadow">
                    <div class="flex items-center mb-4">
                        <div class="bg-blue-100 p-2 rounded-full mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-800">Recent Courses</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Credits</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (count($courses) > 0): ?>
                                    <?php 
                                        // Show only the most recent 5 courses
                                        $recentCourses = array_slice($courses, -5, 5, true);
                                        foreach ($recentCourses as $course): ?>
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-800"><?php echo htmlspecialchars($course['code']); ?></td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-800"><?php echo htmlspecialchars($course['title']); ?></td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-800"><?php echo htmlspecialchars($course['credits']); ?></td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?php 
                                                    if ($course['grade'] == 'A') echo 'bg-green-100 text-green-800';
                                                    elseif ($course['grade'] == 'B') echo 'bg-blue-100 text-blue-800';
                                                    elseif ($course['grade'] == 'C') echo 'bg-yellow-100 text-yellow-800';
                                                    elseif ($course['grade'] == 'D') echo 'bg-orange-100 text-orange-800';
                                                    elseif ($course['grade'] == 'F') echo 'bg-red-100 text-red-800';
                                                    elseif ($course['grade'] == 'W') echo 'bg-gray-100 text-gray-800';
                                                    else echo 'bg-purple-100 text-purple-800';
                                                ?>">
                                                <?php echo htmlspecialchars($course['grade']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="px-4 py-3 text-center text-gray-500">No courses found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="academic-history.php" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition mt-3 text-sm">
                        View All Courses
                    </a>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6 card-shadow">
                    <div class="flex items-center mb-4">
                        <div class="bg-purple-100 p-2 rounded-full mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-800">Upcoming Events</h3>
                    </div>
                    <ul id="upcoming-events" class="space-y-2">
                        <li class="flex">
                            <div class="mr-3 text-purple-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-gray-800 font-medium">Registration Deadline</p>
                                <p class="text-sm text-gray-600">December 15, 2023 | 5:00 PM</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 card-shadow">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Academic History</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Credits</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                            </tr>
                        </thead>
                        <tbody id="academic-history" class="bg-white divide-y divide-gray-200">
                            <?php if (count($courses) > 0): ?>
                                <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"><?php echo htmlspecialchars($course['code']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo htmlspecialchars($course['title']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo htmlspecialchars($course['credits']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 font-medium"><?php echo htmlspecialchars($course['grade']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No academic history available</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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