<?php

    require_once(__DIR__ . '/../../cnx/cnx.php');

    class Aceptacion extends Conexion
    {
        // ====== FUNCIONES GENERICAS DE CONSULTA Y EJECUCION ====== //

        protected function runQuery($sql, $params = array(), $useAnalytics = false)
        {
            $rows = array();
            $conexion = $this->get_conexion();

            try {
                $stmt = $conexion->prepare($sql);

                foreach ($params as $key => $val) {
                    $param = ':' . ltrim($key, ':');
                    if (is_int($val)) {
                        $stmt->bindValue($param, $val, PDO::PARAM_INT);
                    } else {
                        $stmt->bindValue($param, $val, PDO::PARAM_STR);
                    }
                }

                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (method_exists($stmt, 'closeCursor')) {
                    $stmt->closeCursor();
                }
            } catch (PDOException $e) {
                error_log("[Aceptacion::runQuery] Error: " . $e->getMessage() . " SQL: " . $sql);
                return array();
            }

            return is_array($rows) ? $rows : array();
        }

        protected function runExecute($sql, $params = array())
        {
            $conexion = $this->get_conexion();
            try {
                $stmt = $conexion->prepare($sql);
                foreach ($params as $key => $val) {
                    $param = ':' . ltrim($key, ':');
                    $stmt->bindValue($param, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
                }
                return $stmt->execute();
            } catch (PDOException $e) {
                error_log("[Aceptacion::runExecute] Error: " . $e->getMessage() . " SQL: " . $sql);
                return false;
            }
        }

        public function ObtenerPendientes($idContratistas, $Fecha1, $Fecha2)
        {
            $sql = "SELECT t.idTransferencias_T, t.Folio_Salida_ST, t.Folio_Telmex_ST, t.Fecha_Salida_ST,
                    t.Cantidad_ST, t.Cantidad_Metros_T, t.Solicitante_Salida_T, t.DescripcionSalida_T,
                    a.Almacen, uNeg.UN AS Unidad_Negocio,
                    (SELECT CASE
                            WHEN EXISTS (SELECT 1 FROM temp_material_det_salida m WHERE m.Folio_Salida_Material_Det_S = t.Folio_Salida_ST AND m.Status_Item != 'cancelado')
                                THEN COALESCE((SELECT COUNT(*) FROM temp_material_det_salida m WHERE m.Folio_Salida_Material_Det_S = t.Folio_Salida_ST AND m.Status_Item = 'pendiente'), 0)
                                ELSE COALESCE(SUM(
                                    CASE
                                        WHEN tt.Status_Item IN ('pendiente','parcial') THEN GREATEST(tt.Total_Piezas - COALESCE(tt.Cantidad_Aceptada,0),0)
                                        ELSE 0
                                    END
                                ),0)
                        END
                    FROM temp_transferencias tt
                    WHERE tt.Folio_Salida = t.Folio_Salida_ST) AS Cantidad_Pendiente
                    FROM temp_transferencias_t t
                    INNER JOIN almacenes a ON t.FK_Almacen_ST = a.idAlmacenes
                    LEFT JOIN unegocio uNeg ON t.FK_UNS_T = uNeg.idUN
                    WHERE t.FK_Contra_ST = COALESCE(NULLIF(:idContratistas, 0), t.FK_Contra_ST)
                    AND t.Status_Temp IN ('pendiente','parcial')
                    AND (
                        EXISTS (SELECT 1 FROM temp_material_det_salida m
                                WHERE m.Folio_Salida_Material_Det_S = t.Folio_Salida_ST AND m.Status_Item = 'pendiente')
                        OR EXISTS (SELECT 1 FROM temp_transferencias tt
                                WHERE tt.Folio_Salida = t.Folio_Salida_ST AND tt.Status_Item = 'pendiente')
                    )
                    AND t.Fecha_Salida_ST BETWEEN :Fecha1 AND :Fecha2
                    ORDER BY t.Folio_Salida_ST DESC";

            return $this->runQuery($sql, [
                'idContratistas' => $idContratistas,
                'Fecha1' => $Fecha1,
                'Fecha2' => $Fecha2
            ]);
        }

        public function ObtenerDetalleMateriales($Folio_Salida)
        {
            $sql = "SELECT t.idTransferencias, p.Producto, p.Modelo, t.Tecnologia, p.UM, p.Presentacion,
                        t.Cantidad_S, t.Piezas_Sueltas, t.Total_Piezas, t.Total_Metros,
                        COALESCE(t.Cantidad_Aceptada, 0) AS Cantidad_Aceptada,
                        CASE WHEN EXISTS (
                                SELECT 1 FROM temp_material_det_salida m
                                WHERE m.Folio_Salida_Material_Det_S = :folio
                                  AND m.FK_Material_S = t.FK_Producto_S
                                  AND m.Status_Item != 'cancelado'
                            )
                            THEN COALESCE((
                                SELECT COUNT(*) FROM temp_material_det_salida m
                                WHERE m.Folio_Salida_Material_Det_S = :folio
                                  AND m.FK_Material_S = t.FK_Producto_S
                                  AND m.Status_Item = 'pendiente'
                            ), 0)
                            ELSE GREATEST(t.Total_Piezas - COALESCE(t.Cantidad_Aceptada, 0), 0)
                        END AS Cantidad_Pendiente,
                        t.FK_Producto_S,
                        (SELECT COUNT(*) FROM temp_material_det_salida m
                            WHERE m.Folio_Salida_Material_Det_S = :folio
                              AND m.FK_Material_S = t.FK_Producto_S
                              AND m.Status_Item != 'cancelado') AS series_total
                    FROM temp_transferencias t
                    INNER JOIN producto p ON t.FK_Producto_S = p.idProducto
                    WHERE t.Folio_Salida = :folio";

            return $this->runQuery($sql, ['folio' => $Folio_Salida]);
        }

        public function ObtenerDetalleSeries($Folio_Salida, $soloLectura = false)
        {
            $sql = "SELECT m.Num_Serie_S, m.Status_Item, m.Motivo_Rechazo, p.Producto, p.Modelo
                    FROM temp_material_det_salida m
                    INNER JOIN producto p ON m.FK_Material_S = p.idProducto
                    WHERE m.Folio_Salida_Material_Det_S = :folio
                      AND m.Status_Item != 'cancelado'";

            if (!$soloLectura) {
                $sql .= " AND m.Status_Item = 'pendiente'";
            }

            return $this->runQuery($sql, ['folio' => $Folio_Salida]);
        }

        public function ProcesarRespuestaParcial($Folio_Salida, $idContratistas, $seriesAceptadas, $seriesRechazadas, $ajustesCantidad, $rechazarTodo = false)
        {
            $conexion = $this->get_conexion();
            try {
                $conexion->beginTransaction();

                $stmtCheck = $conexion->prepare("SELECT FK_User_ST FROM temp_transferencias_t
                    WHERE Folio_Salida_ST = :folio AND FK_Contra_ST = :contratista
                    AND Status_Temp IN ('pendiente','parcial') FOR UPDATE");
                $stmtCheck->bindValue(':folio', $Folio_Salida, PDO::PARAM_INT);
                $stmtCheck->bindValue(':contratista', $idContratistas, PDO::PARAM_INT);
                $stmtCheck->execute();
                $fila = $stmtCheck->fetch(PDO::FETCH_ASSOC);
                if (!$fila) {
                    $conexion->rollBack();
                    return ['success' => false, 'message' => 'Folio no disponible para procesar.'];
                }
                $idUsuarioNass = $fila['FK_User_ST'];

                if ($rechazarTodo) {
                    $stmtRejectAllSeries = $conexion->prepare("UPDATE temp_material_det_salida
                        SET Status_Item = 'rechazado', Motivo_Rechazo = 'no_solicitado'
                        WHERE Folio_Salida_Material_Det_S = :folio
                          AND Status_Item NOT IN ('cancelado', 'aceptado')");
                    $stmtRejectAllSeries->execute(['folio' => $Folio_Salida]);

                    $stmtRejectAllLines = $conexion->prepare("UPDATE temp_transferencias
                        SET Status_Item = CASE WHEN Cantidad_Aceptada > 0 THEN 'parcial' ELSE 'rechazado' END,
                            Motivo_Rechazo = 'no_solicitado'
                        WHERE Folio_Salida = :folio");
                    $stmtRejectAllLines->execute(['folio' => $Folio_Salida]);

                    $stmtRejectAllHeader = $conexion->prepare("UPDATE temp_transferencias_t
                        SET Status_Temp = 'rechazado', Fecha_Respuesta = NOW()
                        WHERE Folio_Salida_ST = :folio");
                    $stmtRejectAllHeader->execute(['folio' => $Folio_Salida]);
                }

                if (!empty($seriesAceptadas) && !$rechazarTodo) {
                    $inPlaceholders = implode(',', array_fill(0, count($seriesAceptadas), '?'));

                    $stmtUpd = $conexion->prepare("UPDATE temp_material_det_salida
                        SET Status_Item = 'aceptado'
                        WHERE Folio_Salida_Material_Det_S = ? AND Num_Serie_S IN ($inPlaceholders)
                          AND Status_Item != 'cancelado'");
                    $stmtUpd->execute(array_merge([$Folio_Salida], $seriesAceptadas));

                    $conexion->exec("INSERT INTO material_det_salida
                        (Num_Serie_S, Alfanumerico_S, FK_Material_S, Localidad_Material_S, Folio_Salida_Material_Det_S,
                        Fecha_Salida_S, FK_Contratista_Salida, Ubicacion_Material, Estatus_Ubicacion,
                        Folio_Ultima_Transferencia, Folio_Ultima_Devolucion)
                        SELECT Num_Serie_S, Alfanumerico_S, FK_Material_S, Localidad_Material_S, Folio_Salida_Material_Det_S,
                            Fecha_Salida_S, FK_Contratista_Salida, Ubicacion_Material, Estatus_Ubicacion,
                            Folio_Ultima_Transferencia, Folio_Ultima_Devolucion
                        FROM temp_material_det_salida
                        WHERE Folio_Salida_Material_Det_S = " . (int)$Folio_Salida . "
                        AND Status_Item = 'aceptado'
                        AND Num_Serie_S NOT IN (
                            SELECT Num_Serie_S FROM material_det_salida
                            WHERE Folio_Salida_Material_Det_S = " . (int)$Folio_Salida . "
                        )");

                    $stmtEstatus = $conexion->prepare("UPDATE materiales_det SET Estatus_NS = 'ASSIGMENT'
                        WHERE Num_Serie IN ($inPlaceholders)");
                    $stmtEstatus->execute($seriesAceptadas);

                    $stmtAcceptedTotals = $conexion->prepare("SELECT FK_Material_S, COUNT(*) AS cantidad_aceptada
                        FROM temp_material_det_salida
                        WHERE Folio_Salida_Material_Det_S = ?
                          AND Status_Item = 'aceptado'
                        GROUP BY FK_Material_S");
                    $stmtAcceptedTotals->execute([$Folio_Salida]);
                    $acceptedSeriesByProduct = [];
                    while ($row = $stmtAcceptedTotals->fetch(PDO::FETCH_ASSOC)) {
                        $acceptedSeriesByProduct[(int)$row['FK_Material_S']] = (int)$row['cantidad_aceptada'];
                    }

                    $stmtSeriesByProduct = $conexion->prepare("SELECT DISTINCT FK_Material_S
                        FROM temp_material_det_salida
                        WHERE Folio_Salida_Material_Det_S = ?
                          AND Status_Item != 'cancelado'");
                    $stmtSeriesByProduct->execute([$Folio_Salida]);
                    $seriesProducts = $stmtSeriesByProduct->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($seriesProducts as $productSeries) {
                        $stmtLineaSerie = $conexion->prepare("SELECT * FROM temp_transferencias
                            WHERE Folio_Salida = :folio AND FK_Producto_S = :prod FOR UPDATE");
                        $stmtLineaSerie->execute(['folio' => $Folio_Salida, 'prod' => $productSeries['FK_Material_S']]);
                        $lineaSerie = $stmtLineaSerie->fetch(PDO::FETCH_ASSOC);

                        if (!$lineaSerie) {
                            continue;
                        }

                        $stmtSeriesStatus = $conexion->prepare("SELECT
                                SUM(CASE WHEN Status_Item = 'aceptado' THEN 1 ELSE 0 END) AS aceptadas,
                                SUM(CASE WHEN Status_Item = 'rechazado' THEN 1 ELSE 0 END) AS rechazadas,
                                SUM(CASE WHEN Status_Item = 'pendiente' THEN 1 ELSE 0 END) AS pendientes
                            FROM temp_material_det_salida
                            WHERE Folio_Salida_Material_Det_S = ?
                              AND FK_Material_S = ?
                              AND Status_Item != 'cancelado'");
                        $stmtSeriesStatus->execute([$Folio_Salida, $productSeries['FK_Material_S']]);
                        $seriesStatus = $stmtSeriesStatus->fetch(PDO::FETCH_ASSOC);

                        $aceptadas = (int)($seriesStatus['aceptadas'] ?? 0);
                        $rechazadas = (int)($seriesStatus['rechazadas'] ?? 0);
                        $pendientes = (int)($seriesStatus['pendientes'] ?? 0);

                        if ($pendientes > 0) {
                            $statusSerie = ($aceptadas > 0 || $rechazadas > 0) ? 'parcial' : 'pendiente';
                        } elseif ($aceptadas > 0 && $rechazadas > 0) {
                            $statusSerie = 'parcial';
                        } elseif ($rechazadas > 0) {
                            $statusSerie = 'rechazado';
                        } else {
                            $statusSerie = 'aceptado';
                        }

                        $stmtUpdLineaSerie = $conexion->prepare("UPDATE temp_transferencias
                            SET Status_Item = :status, Cantidad_Aceptada = :cant, Motivo_Rechazo = :motivo
                            WHERE idTransferencias = :id");
                        $stmtUpdLineaSerie->execute([
                            'status' => $statusSerie,
                            'cant' => $aceptadas,
                            'motivo' => $statusSerie !== 'aceptado' ? 'numero_serie_incorrecto' : null,
                            'id' => $lineaSerie['idTransferencias']
                        ]);

                        if ($aceptadas > 0) {
                            $existeSerie = $conexion->prepare("SELECT idTransferencias FROM transferencias
                                WHERE Folio_Salida = :folio AND FK_Producto_S = :prod");
                            $existeSerie->execute(['folio' => $Folio_Salida, 'prod' => $productSeries['FK_Material_S']]);

                            if ($existeSerie->fetch()) {
                                $stmtUpdTransferenciaSerie = $conexion->prepare("UPDATE transferencias
                                    SET Cantidad_S = :cantidad, Total_Piezas = :totalpiezas
                                    WHERE Folio_Salida = :folio AND FK_Producto_S = :prod");
                                $stmtUpdTransferenciaSerie->execute([
                                    'cantidad' => $aceptadas,
                                    'totalpiezas' => $aceptadas,
                                    'folio' => $lineaSerie['Folio_Salida'],
                                    'prod' => $lineaSerie['FK_Producto_S']
                                ]);
                            } else {
                                $stmtInsLineaSerie = $conexion->prepare("INSERT INTO transferencias
                                    (Folio_Salida, FK_Almacen_S, FK_Contratista_S, FK_Producto_S, Fecha_S, Cantidad_S,
                                    FK_User_S, FK_Tecnico_S, Folio_Telmex_S, FK_UNS, recibe_contratista, Tecnologia,
                                    Piezas_Sueltas, Total_Piezas, Total_Metros)
                                    VALUES (:folio, :almacen, :contratista, :producto, :fecha, :cantidad,
                                            :user, :tecnico, :telmex, :uns, :recibe, :tecnologia,
                                            :piezas, :totalpiezas, :totalmetros)");
                                $stmtInsLineaSerie->execute([
                                    'folio' => $lineaSerie['Folio_Salida'],
                                    'almacen' => $lineaSerie['FK_Almacen_S'],
                                    'contratista' => $lineaSerie['FK_Contratista_S'],
                                    'producto' => $lineaSerie['FK_Producto_S'],
                                    'fecha' => $lineaSerie['Fecha_S'],
                                    'cantidad' => $aceptadas,
                                    'user' => $lineaSerie['FK_User_S'],
                                    'tecnico' => $lineaSerie['FK_Tecnico_S'],
                                    'telmex' => $lineaSerie['Folio_Telmex_S'],
                                    'uns' => $lineaSerie['FK_UNS'],
                                    'recibe' => $lineaSerie['recibe_contratista'],
                                    'tecnologia' => $lineaSerie['Tecnologia'],
                                    'piezas' => $lineaSerie['Piezas_Sueltas'],
                                    'totalpiezas' => $aceptadas,
                                    'totalmetros' => $lineaSerie['Total_Metros']
                                ]);
                            }
                        }
                    }
                }

                foreach ($seriesRechazadas as $rechazo) {
                    $stmtRechazo = $conexion->prepare("UPDATE temp_material_det_salida
                        SET Status_Item = 'rechazado', Motivo_Rechazo = :motivo
                        WHERE Folio_Salida_Material_Det_S = :folio AND Num_Serie_S = :serie");
                    $stmtRechazo->execute([
                        'motivo' => $rechazo['motivo'],
                        'folio' => $Folio_Salida,
                        'serie' => $rechazo['serie']
                    ]);

                    $stmtLiberar = $conexion->prepare("UPDATE materiales_det SET Estatus_NS = NULL WHERE Num_Serie = :serie");
                    $stmtLiberar->execute(['serie' => $rechazo['serie']]);
                }

                foreach ($ajustesCantidad as $ajuste) {
                    $stmtLinea = $conexion->prepare("SELECT * FROM temp_transferencias WHERE idTransferencias = :id FOR UPDATE");
                    $stmtLinea->execute(['id' => $ajuste['id']]);
                    $linea = $stmtLinea->fetch(PDO::FETCH_ASSOC);
                    if (!$linea) {
                        continue;
                    }

                    if ($ajuste['cantidad_aceptada'] >= $linea['Total_Piezas']) {
                        $status = 'aceptado';
                    } elseif ($ajuste['cantidad_aceptada'] <= 0) {
                        $status = 'rechazado';
                    } else {
                        $status = 'parcial';
                    }

                    $unidadPresentacion = 1;
                    $stmtProducto = $conexion->prepare("SELECT Presentacion FROM producto WHERE idProducto = ?");
                    $stmtProducto->execute([$linea['FK_Producto_S']]);
                    $presentacionTexto = $stmtProducto->fetchColumn();
                    if (preg_match('/(\d+)/', $presentacionTexto, $matches)) {
                        $unidadPresentacion = max(1, (int)$matches[1]);
                    }

                    $cantidadAceptada = max(0, (int)$ajuste['cantidad_aceptada']);
                    $cantidadS = intdiv($cantidadAceptada, $unidadPresentacion);
                    $piezasSueltas = $cantidadAceptada % $unidadPresentacion;
                    $totalPiezas = $cantidadS * $unidadPresentacion + $piezasSueltas;

                    $stmtUpdLinea = $conexion->prepare("UPDATE temp_transferencias
                        SET Status_Item = :status, Cantidad_Aceptada = :cant, Motivo_Rechazo = :motivo
                        WHERE idTransferencias = :id");
                    $stmtUpdLinea->execute([
                        'status' => $status,
                        'cant' => $cantidadAceptada,
                        'motivo' => $status !== 'aceptado' ? $ajuste['motivo'] : null,
                        'id' => $ajuste['id']
                    ]);

                    if ($cantidadAceptada > 0) {
                        $stmtExistente = $conexion->prepare("SELECT idTransferencias FROM transferencias
                            WHERE Folio_Salida = :folio AND FK_Producto_S = :prod");
                        $stmtExistente->execute(['folio' => $Folio_Salida, 'prod' => $linea['FK_Producto_S']]);
                        $transferenciaExistente = $stmtExistente->fetch(PDO::FETCH_ASSOC);

                        if ($transferenciaExistente) {
                            $stmtActualizar = $conexion->prepare("UPDATE transferencias
                                SET Cantidad_S = :cantidad, Piezas_Sueltas = :piezas, Total_Piezas = :totalpiezas, Total_Metros = :totalmetros
                                WHERE idTransferencias = :id");
                            $stmtActualizar->execute([
                                'cantidad' => $cantidadS,
                                'piezas' => $piezasSueltas,
                                'totalpiezas' => $totalPiezas,
                                'totalmetros' => $linea['Total_Metros'],
                                'id' => $transferenciaExistente['idTransferencias']
                            ]);
                        } else {
                            $stmtInsertar = $conexion->prepare("INSERT INTO transferencias
                                (Folio_Salida, FK_Almacen_S, FK_Contratista_S, FK_Producto_S, Fecha_S, Cantidad_S,
                                FK_User_S, FK_Tecnico_S, Folio_Telmex_S, FK_UNS, recibe_contratista, Tecnologia,
                                Piezas_Sueltas, Total_Piezas, Total_Metros)
                                VALUES (:folio, :almacen, :contratista, :producto, :fecha, :cantidad,
                                        :user, :tecnico, :telmex, :uns, :recibe, :tecnologia,
                                        :piezas, :totalpiezas, :totalmetros)");
                            $stmtInsertar->execute([
                                'folio' => $linea['Folio_Salida'],
                                'almacen' => $linea['FK_Almacen_S'],
                                'contratista' => $linea['FK_Contratista_S'],
                                'producto' => $linea['FK_Producto_S'],
                                'fecha' => $linea['Fecha_S'],
                                'cantidad' => $cantidadS,
                                'user' => $linea['FK_User_S'],
                                'tecnico' => $linea['FK_Tecnico_S'],
                                'telmex' => $linea['Folio_Telmex_S'],
                                'uns' => $linea['FK_UNS'],
                                'recibe' => $linea['recibe_contratista'],
                                'tecnologia' => $linea['Tecnologia'],
                                'piezas' => $piezasSueltas,
                                'totalpiezas' => $totalPiezas,
                                'totalmetros' => $linea['Total_Metros']
                            ]);
                        }
                    }
                }

                $this->RecalcularStatusCabecera($conexion, $Folio_Salida);

                $stmtTotalAceptado = $conexion->prepare("SELECT COALESCE(SUM(Total_Piezas), 0) AS cantidad
                    FROM transferencias WHERE Folio_Salida = :folio");
                $stmtTotalAceptado->execute(['folio' => $Folio_Salida]);
                $cantidadTotalAceptada = (int)$stmtTotalAceptado->fetchColumn();

                if ($cantidadTotalAceptada > 0) {
                    $stmtActualizarTemporal = $conexion->prepare("UPDATE temp_transferencias_t
                        SET Cantidad_ST = :cantidad WHERE Folio_Salida_ST = :folio");
                    $stmtActualizarTemporal->execute([
                        'cantidad' => $cantidadTotalAceptada,
                        'folio' => $Folio_Salida
                    ]);

                    $stmtActualizarCabecera = $conexion->prepare("UPDATE transferencias_t
                        SET Cantidad_ST = :cantidad WHERE Folio_Salida_ST = :folio");
                    $stmtActualizarCabecera->execute([
                        'cantidad' => $cantidadTotalAceptada,
                        'folio' => $Folio_Salida
                    ]);

                    if ($stmtActualizarCabecera->rowCount() === 0) {
                        $conexion->exec("INSERT INTO transferencias_t
                            (FK_Almacen_ST, FK_Contra_ST, Fecha_Salida_ST, Cantidad_ST, FK_User_ST, Folio_Salida_ST,
                            Solicitante_Salida_T, Solicitante_SalidaA_T, DescripcionSalida_T, Tipo_MovS_T, Folio_Telmex_ST,
                            FK_UNS_T, Comprobante_Salida_T, Hash_Archivo_Salidas, Estatus_ComprobanteS_T,
                            Ubicacion_Material_ST, recibe_contratista_t, Cantidad_Metros_T)
                            SELECT FK_Almacen_ST, FK_Contra_ST, Fecha_Salida_ST, " . $cantidadTotalAceptada . ", FK_User_ST, Folio_Salida_ST,
                                Solicitante_Salida_T, Solicitante_SalidaA_T, DescripcionSalida_T, Tipo_MovS_T, Folio_Telmex_ST,
                                FK_UNS_T, Comprobante_Salida_T, Hash_Archivo_Salidas, Estatus_ComprobanteS_T,
                                Ubicacion_Material_ST, recibe_contratista_t, Cantidad_Metros_T
                            FROM temp_transferencias_t WHERE Folio_Salida_ST = " . (int)$Folio_Salida);
                    }
                }

                $conexion->commit();
                return ['success' => true, 'idUsuarioNass' => $idUsuarioNass];
            } catch (PDOException $e) {
                if ($conexion->inTransaction()) {
                    $conexion->rollBack();
                }
                error_log('[Aceptacion::ProcesarRespuestaParcial] Error: ' . $e->getMessage());
                return ['success' => false, 'message' => 'Error al procesar la respuesta.'];
            }
        }

        private function RecalcularStatusCabecera($conexion, $Folio_Salida)
        {
            $stmt = $conexion->prepare("SELECT
                    SUM(CASE WHEN Status_Item = 'pendiente' THEN 1 ELSE 0 END) AS pendientes,
                    SUM(CASE WHEN Status_Item = 'parcial' THEN 1 ELSE 0 END) AS parciales,
                    SUM(CASE WHEN Status_Item = 'rechazado' THEN 1 ELSE 0 END) AS rechazados,
                    SUM(CASE WHEN Status_Item = 'aceptado' THEN 1 ELSE 0 END) AS aceptados
                FROM (
                    SELECT Status_Item FROM temp_material_det_salida
                    WHERE Folio_Salida_Material_Det_S = :folio AND Status_Item != 'cancelado'
                    UNION ALL
                    SELECT Status_Item FROM temp_transferencias WHERE Folio_Salida = :folio
                ) AS todos");
            $stmt->execute(['folio' => $Folio_Salida]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($resultado['pendientes'] > 0 || $resultado['parciales'] > 0) {
                $nuevoStatus = 'parcial';
            } elseif ($resultado['rechazados'] > 0) {
                $nuevoStatus = $resultado['aceptados'] > 0 ? 'parcial' : 'rechazado';
            } else {
                $nuevoStatus = 'aceptado';
            }

            $stmtActualizar = $conexion->prepare("UPDATE temp_transferencias_t
                SET Status_Temp = :status WHERE Folio_Salida_ST = :folio");
            $stmtActualizar->execute([
                'status' => $nuevoStatus,
                'folio' => $Folio_Salida
            ]);
        }

    }

?>