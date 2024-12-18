<?php
session_start();
include('../conn.php');

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo "User tidak terautentikasi.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['postContent'];
    $user_id = $_SESSION['user_id'];
    $imagePath = null;

    // Validasi user_id
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        echo "User tidak valid.";
        exit;
    }
    $stmt->close();

    // Proses upload gambar
    if (isset($_FILES['postImage']) && $_FILES['postImage']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        $fileName = basename($_FILES['postImage']['name']);
        $targetFilePath = $uploadDir . uniqid() . '_' . $fileName;

        $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $validExtensions)) {
            if (move_uploaded_file($_FILES['postImage']['tmp_name'], $targetFilePath)) {
                $imagePath = $targetFilePath;
            } else {
                echo "Gagal mengunggah gambar.";
                exit;
            }
        } else {
            echo "Format file tidak valid.";
            exit;
        }
    }

    $stmt = $conn->prepare("INSERT INTO posts (user_id, content, image_path, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $user_id, $content, $imagePath);

    if ($stmt->execute()) {
        echo "Posting berhasil disimpan.";
        header("Location: dashboard.php");
    } else {
        echo "Gagal menyimpan postingan: " . $stmt->error;
    }

    $stmt->close();
}
?>


