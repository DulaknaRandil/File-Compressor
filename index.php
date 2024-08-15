<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $source = $_FILES['image']['tmp_name'];
    $quality = intval($_POST['quality']);
    
    // Determine the output format and destination path
    $info = getimagesize($source);
    $output_extension = '';
    $image = null;

    switch ($info['mime']) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source);
            $output_extension = '.jpg';
            break;
        case 'image/png':
            $image = imagecreatefrompng($source);
            $output_extension = '.png';
            break;
        case 'image/gif':
            $image = imagecreatefromgif($source);
            $output_extension = '.gif';
            break;
    }

    if ($image != null) {
        $destination = 'uploads/' . pathinfo($_FILES['image']['name'], PATHINFO_FILENAME) . '-compressed' . $output_extension;
        
        // Ensure the uploads directory exists
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }
        
        // Compress and save the image
        if ($output_extension === '.jpg') {
            imagejpeg($image, $destination, $quality);
        } elseif ($output_extension === '.png') {
            // Scale quality for PNG (0-9)
            $png_quality = 9 - floor($quality / 10);
            imagepng($image, $destination, $png_quality);
        } elseif ($output_extension === '.gif') {
            imagegif($image, $destination);
        }

        imagedestroy($image);
        echo "<script>showToast('Image compression completed. <a href=\"$destination\" class=\"underline\">Download here</a>.', 'success');</script>";
    } else {
        echo "<script>showToast('Unsupported image format.', 'error');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Compression</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .toast {
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            min-width: 250px;
            background-color: white;
            color: black;
            border-radius: 0.5rem;
            padding: 1rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s, transform 0.5s;
        }
        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }
        .toast.success {
            border-left: 5px solid #39ff14; /* Neon Green */
        }
        .toast.error {
            border-left: 5px solid #ff3e3e; /* Red */
        }
        .slider-thumb {
            background-color: #39ff14; /* Neon Green */
        }
    </style>
</head>
<body class="bg-white flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md bg-gray-100 shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4">
            <h2 class="text-2xl font-semibold text-center text-black">Image Compression Tool</h2>
            <p class="mt-1 text-center text-gray-600">Compress your images easily and quickly.</p>
            <form action="" method="post" enctype="multipart/form-data" class="mt-6">
                <div class="mb-4">
                    <label for="image" class="block text-black font-bold mb-2">Upload Image</label>
                    <input type="file" class="form-control w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-neon-green" id="image" name="image" accept="image/*" required>
                </div>
                <div class="mb-4">
                    <label for="quality" class="block text-black font-bold mb-2">Compression Quality: <span id="qualityValue">50</span></label>
                    <input type="range" class="w-full" id="quality" name="quality" min="1" max="100" value="50" oninput="document.getElementById('qualityValue').innerText = this.value;">
                </div>
                <button type="submit" class="w-full bg-neon-green text-black px-4 py-2 rounded-lg hover:bg-green-500 transition duration-300">Compress Image</button>
            </form>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-100">
            <p class="text-xs text-gray-600 text-center">Supported formats: JPEG, PNG, GIF</p>
        </div>
    </div>
    
    <div id="toast" class="toast"></div>
    
    <script>
        function showToast(message, type) {
            const toast = document.getElementById('toast');
            toast.innerHTML = message;
            toast.className = 'toast show ' + type;
            setTimeout(() => {
                toast.className = 'toast';
            }, 3000);
        }
    </script>
</body>
</html>
