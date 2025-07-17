<?php
require_once 'db.php';

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit();
}

// Get student data
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$_SESSION['student_id']]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $year = $_POST['year'];
    $gpa = $_POST['gpa'];
    $credits = $_POST['credits'];
    $status = $_POST['status'];
    
    try {
        // Update student data
        $updateStmt = $pdo->prepare("UPDATE students SET year = ?, gpa = ?, credits = ?, status = ? WHERE id = ?");
        $updateStmt->execute([$year, $gpa, $credits, $status, $_SESSION['student_id']]);
        
        // Update session
        $_SESSION['student_name'] = $student['first_name'] . ' ' . $student['last_name'];
        
        header('Location: dashboard.php');
        exit();
    } catch (PDOException $e) {
        $error = "Update failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Academic Information - Student Profile System</title>
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
                <a href="dashboard.php" class="hover:underline">Dashboard</a>
                <a href="edit-academic.php" class="font-bold text-indigo-200">Update Academic Info</a>
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
            <a href="edit-academic.php" class="hover:underline">Update Academic Info</a>
            <button onclick="logout()" class="flex items-center text-white hover:text-red-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" />
                </svg>
                Logout
            </button>
        </div>
    </div>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="flex items-center mb-6">
                <a href="dashboard.php" class="text-indigo-600 hover:underline flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Back to Dashboard
                </a>
            </div>
            <div class="bg-white rounded-xl shadow-md p-8 card-shadow">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Academic Information</h2>
                <?php if (isset($error)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                <form id="edit-academic-form" method="POST">
                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="edit-year" class="block text-gray-700 text-sm font-medium mb-2">Academic Year</label>
                            <select id="edit-year" name="year" class="w-full px-4 py-2 border rounded-lg input-field" required>
                                <option value="Freshman" <?php echo $student['year'] === 'Freshman' ? 'selected' : ''; ?>>Freshman</option>
                                <option value="Sophomore" <?php echo $student['year'] === 'Sophomore' ? 'selected' : ''; ?>>Sophomore</option>
                                <option value="Junior" <?php echo $student['year'] === 'Junior' ? 'selected' : ''; ?>>Junior</option>
                                <option value="Senior" <?php echo $student['year'] === 'Senior' ? 'selected' : ''; ?>>Senior</option>
                                <option value="Graduate" <?php echo $student['year'] === 'Graduate' ? 'selected' : ''; ?>>Graduate</option>
                            </select>
                        </div>
                        <div>
                            <label for="edit-gpa" class="block text-gray-700 text-sm font-medium mb-2">GPA</label>
                            <input type="number" id="edit-gpa" name="gpa" step="0.01" min="0" max="4.0" class="w-full px-4 py-2 border rounded-lg input-field" value="<?php echo htmlspecialchars($student['gpa']); ?>" required>
                        </div>
                        <div>
                            <label for="edit-credits" class="block text-gray-700 text-sm font-medium mb-2">Credits Earned</label>
                            <input type="number" id="edit-credits" name="credits" min="0" class="w-full px-4 py-2 border rounded-lg input-field" value="<?php echo htmlspecialchars($student['credits']); ?>" required>
                        </div>
                        <div>
                            <label for="edit-status" class="block text-gray-700 text-sm font-medium mb-2">Academic Status</label>
                            <select id="edit-status" name="status" class="w-full px-4 py-2 border rounded-lg input-field" required>
                                <option value="Good Standing" <?php echo $student['status'] === 'Good Standing' ? 'selected' : ''; ?>>Good Standing</option>
                                <option value="Academic Probation" <?php echo $student['status'] === 'Academic Probation' ? 'selected' : ''; ?>>Academic Probation</option>
                                <option value="Satisfactory Progress" <?php echo $student['status'] === 'Satisfactory Progress' ? 'selected' : ''; ?>>Satisfactory Progress</option>
                                <option value="Honors" <?php echo $student['status'] === 'Honors' ? 'selected' : ''; ?>>Honors</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="window.location.href='dashboard.php'" class="px-6 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">
                            Cancel
                        </button>
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Save Changes
                        </button>
                    </div>
                </form>
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