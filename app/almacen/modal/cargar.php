<?php
    require_once __DIR__ . '/../controller/ClassOps.php';
    
    $modalcargar = new Ops();

	function ListDivision(){
        global $modalcargar;
		$filas = $modalcargar->ListDivisiones();

		foreach ($filas as $row) {
			echo "<option value='".$row['idDivision']."'>".strtoupper($row['Division'])."</option>";
		}
	}
	
    function UltimaSalida(){
        global $modalcargar;
        $folio = $modalcargar->FolioProvisionalSalida();
        $folio = $folio !== false ? $folio : '';
        echo '<input type="number" name="folio" id="Folio_Salida" class="form-control" value="'.$folio.'" readonly="">';
    }

    function UltimaTraspaso(){
        global $modalcargar;
        $ultimo = $modalcargar->UltimaTraspaso();

        $folio = 0;
        if ($ultimo) {
            foreach ($ultimo as $key) {
                $folio = $key['Folio_Traspaso_T'] + 1;

                echo '<input type="number" name="folio" class="form-control" value="'.$folio.'" readonly="">';
            }
        }else{
            $folio = 1;
            echo '<input type="number" name="folio" class="form-control" value="'.$folio.'" readonly="">';
        }
    }

    function Tecnicos($idContratistas){
        global $modalcargar;
		$filas = $modalcargar->Tecnicos($idContratistas);

		foreach ($filas as $row) {
			echo "<option value='".$row['idTecnico']."'>".$row['Nombre_T']." ".$row['Apellidos_T']."</option>";
		}
    }

	function Materiales($idContratistas){
        global $modalcargar;
		$filas = $modalcargar->Materiales($idContratistas);

		foreach ($filas as $row) {
			echo "<option value='".$row['idProducto']."'>".$row['Producto']." | ".$row['Modelo']."</option>";
		}
	}
?>
