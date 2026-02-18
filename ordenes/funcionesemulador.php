<?php
// filtrosEmulador.php

function obtenerFiltrosPorEmulador(?string $emulador, ?string $tecnologia = null): array {

    $emulador   = strtoupper(trim($emulador ?? ''));
    $tecnologia = strtoupper(trim($tecnologia ?? ''));

    $filtrosFibra = [
        "ALTAS" => ['A'],
        "CAMBIOS DE DOMICILIO" => ['D'],
        "MIGRACIONES" => ['FC', 'TI', 'TS', 'TV'],
        "A4" => ['A4'],
        "QUEJAS" => ['Q', 'R', 'C', 'EB', 'EI', 'EU', 'GD', 'MG', 'F597', 'F841'],

        // 👉 NUEVOS EMULADORES
        "FIBRA" => ['A', 'D'],
        //"COBRE" => ['A', 'CD'],
    ];

    $excluirFibra = [
        "ALTAS" => ['A4'],
    ];

    $filtrosCobre = [
        "ALTAS" => ['A'],
        "CAMBIOS DE DOMICILIO" => ['D'],
        "A4" => ['A4'],
        "QUEJAS" => ['Q', 'R', 'C', 'EB', 'EI', 'EU', 'GD', 'MG', 'F597', 'F841'],

        // 👉 NUEVOS EMULADORES
        //"FIBRA" => ['A', 'D'],
        "COBRE" => ['A', 'CD'],
    ];

    $excluirCobre = [
        "ALTAS" => ['A4'],
    ];

    if ($tecnologia === 'FIBRA') {
        $prefijos = $filtrosFibra[$emulador] ?? [];
        $excluir_prefijos = $excluirFibra[$emulador] ?? [];
    } elseif ($tecnologia === 'COBRE') {
        $prefijos = $filtrosCobre[$emulador] ?? [];
        $excluir_prefijos = $excluirCobre[$emulador] ?? [];
    } else {
        $prefijos = [];
        $excluir_prefijos = [];
    }

/*echo "<pre>";
print_r($prefijos);
echo "</pre>"; */

/*echo "<pre>";
print_r($excluir_prefijos);
echo "</pre>"; */

    return [
        'prefijos' => $prefijos,
        'excluir_prefijos' => $excluir_prefijos
    ];
}