<?php
// ===================================
// BAGIAN 1: LOGIKA PEMROSESAN PHP
// ===================================

$shareable_link = '';
$message = '';
$target_dir = "uploads/";

if(isset($_POST["submit"])) {
    // Pastikan folder 'uploads/' ada dan memiliki izin tulis!
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Pembuatan nama file unik agar tidak menimpa file yang sudah ada
    $original_name = basename($_FILES["fileToUpload"]["name"]);
    $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
    $new_file_name = uniqid() . "." . $file_extension;
    $target_file = $target_dir . $new_file_name;
    
    $uploadOk = 1;

    // Cek jika file gagal diunggah karena alasan lain
    if ($_FILES["fileToUpload"]["error"] !== UPLOAD_ERR_OK) {
        $message = "Terjadi kesalahan saat mengunggah. Kode error: " . $_FILES["fileToUpload"]["error"];
        $uploadOk = 0;
    }
    
    // Jika semua OK, coba unggah file
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            
            // Dapatkan URL dasar situs Anda
            $host = $_SERVER['HTTP_HOST'];
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
            
            // Buat link yang dapat diakses publik
            // Catatan: Jika Anda menjalankan di localhost/uploader/uploader.php, link akan seperti: http://localhost/uploader/uploads/namafile.ext
            $directory_path = dirname($_SERVER['PHP_SELF']);
            $shareable_link = $protocol . "://" . $host . $directory_path . "/" . $target_file;
            
            $message = "File <b>" . htmlspecialchars($original_name) . "</b> berhasil diunggah.";

        } else {
            $message = "Maaf, terjadi kesalahan saat menyimpan file Anda di server.";
        }
    }
}

// ===================================
// BAGIAN 2: TAMPILAN HTML
// ===================================
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Uploader File Gabungan</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .link-box { margin-top: 20px; border: 1px solid #ccc; padding: 15px; background-color: #f9f9f9; }
        input[type="text"] { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd; }
    </style>
</head>
<body>

    <h2>Uploader File Sederhana</h2>

    <?php 
    // Tampilkan pesan status
    if ($message) {
        $class = strpos($message, 'berhasil') !== false ? 'success' : 'error';
        echo "<p class='{$class}'>{$message}</p>";
    }
    ?>

    <form action="uploader.php" method="post" enctype="multipart/form-data">
        Pilih file untuk diunggah:
        <input type="file" name="fileToUpload" id="fileToUpload" required>
        <br><br>
        <input type="submit" value="Unggah dan Dapatkan Link" name="submit">
    </form>

    <?php 
    // Tampilkan link yang dapat dibagikan JIKA SUKSES
    if ($shareable_link) {
        echo '<div class="link-box">';
        echo '<h3>Link File Anda:</h3>';
        echo '<input type="text" value="' . htmlspecialchars($shareable_link) . '" id="shareLink" readonly>';
        echo '<button onclick="copyLink()">Salin Link</button>';
        echo '</div>';
    }
    ?>

    <script>
    function copyLink() {
        var copyText = document.getElementById("shareLink");
        copyText.select();
        copyText.setSelectionRange(0, 99999); // Untuk perangkat mobile
        document.execCommand("copy");
        alert("Link berhasil disalin: " + copyText.value);
    }
    </script>

</body>
</html>
