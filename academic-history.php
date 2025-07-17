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

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_course'])) {
        // Add new course
        $code = $_POST['code'];
        $title = $_POST['title'];
        $credits = $_POST['credits'];
        $grade = $_POST['grade'];
        
        // Debug information
        error_log("Adding course: Code=$code, Title=$title, Credits=$credits, Grade=$grade, StudentID=" . $_SESSION['student_id']);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO courses (student_id, code, title, credits, grade) VALUES (?, ?, ?, ?, ?)");
            $result = $stmt->execute([$student['id'], $code, $title, $credits, $grade]);
            
            if ($result) {
                header('Location: academic-history.php');
                exit();
            } else {
                $error = "Failed to add course. Please try again.";
                error_log("Course insertion failed: " . print_r($stmt->errorInfo(), true));
            }
        } catch (PDOException $e) {
            $error = "Error adding course: " . $e->getMessage();
            error_log("Database error: " . $e->getMessage());
        }
    } elseif (isset($_POST['update_course'])) {
        // Update existing course
        $course_id = $_POST['course_id'];
        $code = $_POST['code'];
        $title = $_POST['title'];
        $credits = $_POST['credits'];
        $grade = $_POST['grade'];
        
        try {
            $stmt = $pdo->prepare("UPDATE courses SET code = ?, title = ?, credits = ?, grade = ? WHERE id = ? AND student_id = ?");
            $result = $stmt->execute([$code, $title, $credits, $grade, $course_id, $student['id']]);
            
            if ($result) {
                header('Location: academic-history.php');
                exit();
            } else {
                $error = "Failed to update course. Please try again.";
                error_log("Course update failed: " . print_r($stmt->errorInfo(), true));
            }
        } catch (PDOException $e) {
            $error = "Error updating course: " . $e->getMessage();
            error_log("Database error: " . $e->getMessage());
        }
    } elseif (isset($_POST['delete_course'])) {
        // Delete course
        $course_id = $_POST['course_id'];
        
        try {
            $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ? AND student_id = ?");
            $result = $stmt->execute([$course_id, $student['id']]);
            
            if ($result) {
                header('Location: academic-history.php');
                exit();
            } else {
                $error = "Failed to delete course. Please try again.";
                error_log("Course deletion failed: " . print_r($stmt->errorInfo(), true));
            }
        } catch (PDOException $e) {
            $error = "Error deleting course: " . $e->getMessage();
            error_log("Database error: " . $e->getMessage());
        }
    }
}

// Get courses for the student
try {
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE student_id = ?");
    $stmt->execute([$student['id']]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching courses: " . $e->getMessage());
}

// Check if courses table exists
try {
    $result = $pdo->query("SELECT 1 FROM courses LIMIT 1");
} catch (PDOException $e) {
    die("Courses table does not exist. Please create it first.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic History - Student Profile System</title>
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
                <a href="academic-history.php" class="font-bold text-indigo-200">Academic History</a>
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
        <div class="max-w-6xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Academic History</h1>
                <button onclick="showAddCourseModal()" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                    Add New Course
                </button>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <div class="bg-white rounded-xl shadow-md p-6 mb-8 card-shadow">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course Code</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Credits</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (count($courses) > 0): ?>
                                <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"><?php echo htmlspecialchars($course['code']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"><?php echo htmlspecialchars($course['title']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"><?php echo htmlspecialchars($course['credits']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="showEditCourseModal(<?php echo $course['id']; ?>)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                        <button onclick="confirmDeleteCourse(<?php echo $course['id']; ?>)" class="text-red-600 hover:text-red-900">Delete</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No courses found. Add your first course below.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Academic Summary -->
            <div class="grid md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-md p-6 card-shadow">
                    <div class="flex items-center mb-4">
                        <div class="bg-indigo-100 p-2 rounded-full mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-800">Total Credits</h3>
                    </div>
                    <p class="text-3xl font-bold text-indigo-600"><?php 
                        $totalCredits = array_sum(array_column($courses, 'credits'));
                        echo $totalCredits;
                    ?></p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6 card-shadow">
                    <div class="flex items-center mb-4">
                        <div class="bg-green-100 p-2 rounded-full mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-800">GPA</h3>
                    </div>
                    <p class="text-3xl font-bold text-green-600"><?php 
                        if (count($courses) > 0) {
                            $gradePoints = [
                                'A' => 4.0, 'B' => 3.0, 'C' => 2.0, 'D' => 1.0, 'F' => 0.0, 'W' => 0.0
                            ];
                            $totalPoints = 0;
                            foreach ($courses as $course) {
                                if (isset($gradePoints[$course['grade']])) {
                                    $totalPoints += $gradePoints[$course['grade']] * $course['credits'];
                                }
                            }
                            $gpa = $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0;
                            echo $gpa;
                    } else {
                        echo "0.00";
                    }
                    ?></p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6 card-shadow">
                    <div class="flex items-center mb-4">
                        <div class="bg-blue-100 p-2 rounded-full mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-800">Completed Courses</h3>
                    </div>
                    <p class="text-3xl font-bold text-blue-600"><?php 
                        $completedCourses = array_filter($courses, function($course) {
                            return $course['grade'] !== 'IP' && $course['grade'] !== 'W';
                        });
                        echo count($completedCourses);
                    ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add Course Modal -->
    <div id="addCourseModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-xl p-8 w-full max-w-md">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">Add New Course</h3>
                <button onclick="hideAddCourseModal()" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="addCourseForm" method="POST">
                <div class="mb-4">
                    <label for="course-code" class="block text-gray-700 text-sm font-medium mb-2">Course Code</label>
                    <input type="text" id="course-code" name="code" class="w-full px-4 py-2 border rounded-lg input-field" placeholder="CS101" required>
                </div>
                <div class="mb-4">
                    <label for="course-title" class="block text-gray-700 text-sm font-medium mb-2">Course Title</label>
                    <input type="text" id="course-title" name="title" class="w-full px-4 py-2 border rounded-lg input-field" placeholder="Introduction to Computer Science" required>
                </div>
                <div class="mb-4">
                    <label for="course-credits" class="block text-gray-700 text-sm font-medium mb-2">Credits</label>
                    <input type="number" id="course-credits" name="credits" min="1" max="6" class="w-full px-4 py-2 border rounded-lg input-field" placeholder="3" required>
                </div>
                <div class="mb-6">
                    <label for="course-grade" class="block text-gray-700 text-sm font-medium mb-2">Grade</label>
                    <select id="course-grade" name="grade" class="w-full px-4 py-2 border rounded-lg input-field" required>
                        <option value="IP">In Progress (IP)</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="F">F</option>
                        <option value="W">Withdrawn (W)</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideAddCourseModal()" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Add Course
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Edit Course Modal -->
    <div id="editCourseModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-xl p-8 w-full max-w-md">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">Edit Course</h3>
                <button onclick="hideEditCourseModal()" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="editCourseForm" method="POST">
                <input type="hidden" id="edit-course-id" name="course_id">
                <div class="mb-4">
                    <label for="edit-course-code" class="block text-gray-700 text-sm font-medium mb-2">Course Code</label>
                    <input type="text" id="edit-course-code" name="code" class="w-full px-4 py-2 border rounded-lg input-field" required>
                </div>
                <div class="mb-4">
                    <label for="edit-course-title" class="block text-gray-700 text-sm font-medium mb-2">Course Title</label>
                    <input type="text" id="edit-course-title" name="title" class="w-full px-4 py-2 border rounded-lg input-field" required>
                </div>
                <div class="mb-4">
                    <label for="edit-course-credits" class="block text-gray-700 text-sm font-medium mb-2">Credits</label>
                    <input type="number" id="edit-course-credits" name="credits" min="1" max="6" class="w-full px-4 py-2 border rounded-lg input-field" required>
                </div>
                <div class="mb-6">
                    <label for="edit-course-grade" class="block text-gray-700 text-sm font-medium mb-2">Grade</label>
                    <select id="edit-course-grade" name="grade" class="w-full px-4 py-2 border rounded-lg input-field" required>
                        <option value="IP">In Progress (IP)</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="F">F</option>
                        <option value="W">Withdrawn (W)</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideEditCourseModal()" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div id="deleteCourseModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-xl p-8 w-full max-w-md">
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-800">Confirm Delete</h3>
                <p class="text-gray-600 mt-2">Are you sure you want to delete this course from your academic history?</p>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="hideDeleteCourseModal()" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">
                    Cancel
                </button>
                <button type="button" onclick="deleteCourse()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Delete
                </button>
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
        
        // Modal functions
        function showAddCourseModal() {
            document.getElementById('addCourseModal').classList.remove('hidden');
        }
        
        function hideAddCourseModal() {
            document.getElementById('addCourseModal').classList.add('hidden');
            document.getElementById('addCourseForm').reset();
        }
        
        function showEditCourseModal(courseId) {
            // Fetch course data
            const course = <?php echo json_encode($courses); ?>.find(c => c.id == courseId);
            if (course) {
                document.getElementById('edit-course-id').value = course.id;
                document.getElementById('edit-course-code').value = course.code;
                document.getElementById('edit-course-title').value = course.title;
                document.getElementById('edit-course-credits').value = course.credits;
                document.getElementById('edit-course-grade').value = course.grade;
                document.getElementById('editCourseModal').classList.remove('hidden');
            }
        }
        
        function hideEditCourseModal() {
            document.getElementById('editCourseModal').classList.add('hidden');
            document.getElementById('editCourseForm').reset();
        }
        
        function confirmDeleteCourse(courseId) {
            document.getElementById('deleteCourseModal').classList.remove('hidden');
            document.getElementById('deleteCourseModal').dataset.courseId = courseId;
        }
        
        function hideDeleteCourseModal() {
            document.getElementById('deleteCourseModal').classList.add('hidden');
        }
        
        function deleteCourse() {
            const courseId = document.getElementById('deleteCourseModal').dataset.courseId;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'academic-history.php';
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'delete_course';
            input.value = courseId;
            
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
        
        // Form submission handlers
        document.getElementById('addCourseForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            
            fetch('academic-history.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    return response.text();
                }
            })
            .then(data => {
                console.log('Response:', data);
                if (data.includes('Error')) {
                    alert('Error: ' + data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while adding the course.');
            });
        });
        
        document.getElementById('editCourseForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            
            fetch('academic-history.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    return response.text();
                }
            })
            .then(data => {
                console.log('Response:', data);
                if (data.includes('Error')) {
                    alert('Error: ' + data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the course.');
            });
        });
    </script>
</body>
</html>