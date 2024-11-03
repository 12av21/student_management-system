<?php
session_start();
include '../config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Get the course ID from URL
if (!isset($_GET['course_id'])) {
    die("Course ID not provided.");
}

$course_id = $_GET['course_id'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject_name = $_POST['subject_name'];
    $semester_id = $_POST['semester'];
    $subject_type = $_POST['subject_type']; // Get the subject type

    // Insert new subject
    $stmt = $conn->prepare("INSERT INTO subjects (course_id, subject_name, semester_id, subject_type) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $course_id, $subject_name, $semester_id, $subject_type);

    if ($stmt->execute()) {
        $success = "Subject added successfully.";
    } else {
        $error = "Error adding subject: " . $stmt->error;
    }
}

// Fetch all semesters from the database
$semesters_result = $conn->query("SELECT semester_id, semester_name FROM semesters");
if (!$semesters_result) {
    die("Error fetching semesters: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Subject</title>
    <link rel="stylesheet" href="manage_courses.css">
    <style>
        /* Container styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        
        .container {
            width: 90%;
            max-width: 500px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        /* Heading styling */
        h1 {
            font-size: 1.8rem;
            color: #4a90e2;
            margin-bottom: 10px;
        }

        /* Form and input styling */
        form {
            margin-top: 20px;
        }
        
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            text-align: left;
        }

        input[type="text"], select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 15px;
            box-sizing: border-box;
        }

        /* Button styling */
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            margin: 5px;
        }

        .btn-primary {
            background-color: #4a90e2;
        }

        .btn-secondary {
            background-color: #888;
        }

        .btn-primary:hover {
            background-color: #3a6db5;
        }

        .btn-secondary:hover {
            background-color: #666;
        }

        /* Success and error messages */
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            font-size: 0.9rem;
            width: 100%;
            text-align: center;
        }

        .alert-success {
            background-color: #e6ffed;
            color: #2d572c;
        }

        .alert-danger {
            background-color: #ffe6e6;
            color: #9f3a38;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add New Subject</h1>

        <!-- Display success or error message -->
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Form for adding new subject -->
        <form action="" method="post">
            <label for="subject_name">Subject Name</label>
            <input type="text" id="subject_name" name="subject_name" required>

            <label for="semester">Semester</label>
            <select id="semester" name="semester" required>
                <option value="">Select Semester</option>
                <?php while ($semester = $semesters_result->fetch_assoc()): ?>
                    <option value="<?php echo $semester['semester_id']; ?>">
                        <?php echo htmlspecialchars($semester['semester_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="subject_type">Subject Type</label>
            <select id="subject_type" name="subject_type" required>
                <option value="">Select Subject Type</option>
                <option value="Theory">Theory</option>
                <option value="Lab">Lab</option>
                <option value="Practical">Practical</option>
            </select>

            <button type="submit" class="btn btn-primary">Add Subject</button>
            <a href="manage_subjects.php?course_id=<?php echo $course_id; ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>