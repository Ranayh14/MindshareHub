<?php
include("conn.php");

$sql_user = "CREATE OR REPLACE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    pass VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

try {   
    if(mysqli_query($koneksi, $sql_user)) {
        echo "tabel users berhasil dibuat <br>";
    }
}
catch (mysqli_sql_exception) {
    echo "Tabel users gagal dibuat";
}
?>