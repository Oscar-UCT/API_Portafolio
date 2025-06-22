<?php
session_start();
if (!isset($_SESSION['user'])) {
  header("Location: ../login.php");
  exit;
}

$id = intval($_GET['id']);

session_write_close();

$ch = curl_init("https://teclab.uct.cl/~oscar.cariaga/portafolio-admin/api/proyectos.php?id=$id");

curl_setopt_array($ch, [
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Cookie: PHPSESSID=' . $_COOKIE["PHPSESSID"]],
    CURLOPT_POSTFIELDS => json_encode(['_method' => 'DELETE']),
]);

curl_exec($ch);
curl_close($ch);

header("Location: ../index.php");
exit;
?>
