<?php
include "../koneksi.php";

$user_id = 1; // sementara untuk testing

$cek = mysqli_query($conn,
"SELECT share_bonus,poin
FROM users
WHERE id='$user_id'");

$user = mysqli_fetch_assoc($cek);

if($user['share_bonus'] == 1){

    echo json_encode([
        "status"=>"error",
        "message"=>"Bonus sudah pernah diklaim"
    ]);

    exit();
}

mysqli_query($conn,
"UPDATE users
SET poin = poin + 10,
share_bonus = 1
WHERE id='$user_id'");

echo json_encode([
    "status"=>"success",
    "message"=>"+10 poin berhasil ditambahkan"
]);
?>