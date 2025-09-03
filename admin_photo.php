<?php
require_once 'config.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create') {
        $title = $_POST['title'] ?? 'Untitled';
        $alt_text = $_POST['alt_text'] ?? '';
        $photo_file = $_FILES['photo_file'];

        // --- File Validation ---
        if ($photo_file['error'] !== UPLOAD_ERR_OK) {
            die("File upload error. Code: " . $photo_file['error']);
        }

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($photo_file['type'], $allowed_types)) {
            die("Invalid file type. Only JPG, PNG, and GIF are allowed.");
        }

        $max_size = 5 * 1024 * 1024; // 5 MB
        if ($photo_file['size'] > $max_size) {
            die("File is too large. Maximum size is 5 MB.");
        }

        // --- File Upload ---
        $upload_dir = 'assets/photos/';
        // Generate a unique filename to prevent overwrites
        $file_extension = pathinfo($photo_file['name'], PATHINFO_EXTENSION);
        $unique_filename = uniqid('photo_', true) . '.' . $file_extension;
        $target_path = $upload_dir . $unique_filename;

        if (move_uploaded_file($photo_file['tmp_name'], $target_path)) {
            // --- Database Insertion ---
            try {
                $stmt = $conn->prepare("INSERT INTO photos (title, alt_text, file_path) VALUES (?, ?, ?)");
                $stmt->execute([$title, $alt_text, $target_path]);

                // Redirect to avoid form resubmission
                header("Location: admin_photo.php");
                exit;
            } catch (PDOException $e) {
                // If insertion fails, delete the uploaded file
                unlink($target_path);
                die("Database error on insert: " . $e->getMessage());
            }
        } else {
            die("Failed to move uploaded file.");
        }
    } elseif ($_POST['action'] === 'delete') {
        $photo_id = $_POST['photo_id'] ?? 0;
        if ($photo_id) {
            try {
                // First, get the file path to delete the file from the server
                $stmt = $conn->prepare("SELECT file_path FROM photos WHERE id = ?");
                $stmt->execute([$photo_id]);
                $photo = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($photo) {
                    // Delete from database
                    $deleteStmt = $conn->prepare("DELETE FROM photos WHERE id = ?");
                    $deleteStmt->execute([$photo_id]);

                    // Delete the file from server
                    if (file_exists($photo['file_path'])) {
                        unlink($photo['file_path']);
                    }
                }

                header("Location: admin_photo.php");
                exit;
            } catch (PDOException $e) {
                die("Database error on delete: " . $e->getMessage());
            }
        }
    }
}

// Fetch all photos from the database
try {
    $stmt = $conn->query("SELECT * FROM photos ORDER BY uploaded_at DESC");
    $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // If the table doesn't exist, this will fail.
    // We'll create an empty array and an error message to guide the user.
    $photos = [];
    $db_error = "Database error: " . $e->getMessage() . ". Please ensure the 'photos' table exists and has the correct structure.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo Gallery Management</title>
    <link rel="stylesheet" href="css/admin.css" />
    <style>
        /* Scoped styles for photo gallery */
        .photo-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
        }
        .photo-card {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 1rem;
            text-align: center;
            background: #fff;
        }
        .photo-card img {
            max-width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 0.5rem;
        }
        .photo-card .card-actions {
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <?php require_once 'includes/sidebar.php'; ?>

    <div class="main-content">
        <header>
            <div class="header-content">
                <h1>Photo Gallery Management</h1>
            </div>
        </header>

        <section class="form-section">
            <h2>Upload New Photo</h2>
            <form action="admin_photo.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="create">
                <div class="form-group">
                    <label for="title">Photo Title</label>
                    <input type="text" id="title" name="title" placeholder="e.g., Company Gathering 2024">
                </div>
                <div class="form-group">
                    <label for="alt_text">Alt Text (for accessibility)</label>
                    <input type="text" id="alt_text" name="alt_text" placeholder="e.g., Team members smiling">
                </div>
                <div class="form-group">
                    <label for="photo_file">Photo File *</label>
                    <input type="file" id="photo_file" name="photo_file" accept="image/*" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn">Upload Photo</button>
                </div>
            </form>
        </section>

        <section class="photo-gallery-section">
            <h2>Existing Photos</h2>
            <?php if (isset($db_error)): ?>
                <div class="alert alert-error"><?= htmlspecialchars($db_error) ?></div>
            <?php endif; ?>
            <div class="photo-gallery">
                <?php if (empty($photos)): ?>
                    <p>No photos uploaded yet.</p>
                <?php else: ?>
                    <?php foreach ($photos as $photo): ?>
                        <div class="photo-card">
                            <img src="<?= htmlspecialchars($photo['file_path']) ?>" alt="<?= htmlspecialchars($photo['alt_text']) ?>">
                            <p><?= htmlspecialchars($photo['title']) ?></p>
                            <div class="card-actions">
                                <form action="admin_photo.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="photo_id" value="<?= $photo['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-small" onclick="return confirm('Are you sure you want to delete this photo?');">Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>
</body>
</html>
