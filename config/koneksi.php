<?php
$host = "lamp-db";
$user = "root";
$pass = "root";
$db = "ngolab_loyalty";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

function uploadAndConvertToWebP($fileInfo, $targetDir, $filenamePrefix) {
    if ($fileInfo['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    if (!is_dir($targetDir)) {
        @mkdir($targetDir, 0777, true);
    }
    
    $source = $fileInfo['tmp_name'];
    $info = getimagesize($source);
    if (!$info) return false;
    
    $mime = $info['mime'];
    
    // Fallback if GD is not installed
    if (!function_exists('imagecreatefromjpeg')) {
        $extension = pathinfo($fileInfo['name'], PATHINFO_EXTENSION);
        if (empty($extension)) {
            if ($mime === 'image/jpeg') $extension = 'jpg';
            elseif ($mime === 'image/png') $extension = 'png';
            elseif ($mime === 'image/gif') $extension = 'gif';
            elseif ($mime === 'image/webp') $extension = 'webp';
            else $extension = 'img';
        }
        $filename = $filenamePrefix . '_' . time() . '.' . $extension;
        if (move_uploaded_file($source, rtrim($targetDir, '/') . '/' . $filename)) {
            return $filename;
        }
        return false;
    }
    
    $image = null;
    if ($mime === 'image/jpeg') {
        $image = @imagecreatefromjpeg($source);
    } elseif ($mime === 'image/png') {
        $image = @imagecreatefrompng($source);
        if ($image) {
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
        }
    } elseif ($mime === 'image/gif') {
        $image = @imagecreatefromgif($source);
    } elseif ($mime === 'image/webp') {
        $filename = $filenamePrefix . '_' . time() . '.webp';
        if (move_uploaded_file($source, rtrim($targetDir, '/') . '/' . $filename)) {
            return $filename;
        }
        return false;
    } else {
        return false;
    }
    
    if (!$image) return false;
    
    $filename = $filenamePrefix . '_' . time() . '.webp';
    $targetPath = rtrim($targetDir, '/') . '/' . $filename;
    
    if (imagewebp($image, $targetPath, 80)) {
        imagedestroy($image);
        return $filename;
    }
    
    imagedestroy($image);
    return false;
}
?>
