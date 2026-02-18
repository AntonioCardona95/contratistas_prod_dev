<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once($_SERVER['DOCUMENT_ROOT']."/ordenes/controller/ClassOps.php");
session_start();

$modal = new Ops();
$idContratistas = $_SESSION['idContratistas'] ?? null;
$semana = $_POST['semana'] ?? null;

if (!$idContratistas || !$semana) {
    echo 0;
    exit;
}

$total = $modal->ConteoPrenomina($idContratistas, $semana);
echo $total;