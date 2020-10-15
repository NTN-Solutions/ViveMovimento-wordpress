<?php

function fnBDAlimentos_save(){
    global $reg_errors,$intA_Id,$intA_Cantidad,$intA_UM,$strA_Alimento,$decA_Proteina,$decA_Carbs,$decA_Grasa,$decA_Libre;

    $registro = array(
        'decPorcion'        => $intA_Cantidad,
        'intUnidadMedida'   => $intA_UM,
        'strAlimento'       => $strA_Alimento,
        'decProteina'       => $decA_Proteina,
        'decCarbohidratos'  => $decA_Carbs,
        'decGrasa'          => $decA_Grasa,
        'decLibre'          => $decA_Libre,
        'bitActivo'         => true
    );
    if ( 1 > count( $reg_errors->get_error_messages() ) ) {
        global $wpdb;
        if($intA_Id == 0){
            if($_SESSION["intFormulario"] == 1){
                $_SESSION["intFormulario"] = 0;
                $response = $wpdb->insert("wp_vivemov_alimentos_porciones", $registro);
                if($response) {
                    echo fnMensaje(1,'Listo, nuevo alimento guardado!');
                } else {
                    echo fnMensaje(2,'Inconvenientes, alimento no guardado!');
                }
            }else{
                $_SESSION["intFormulario"] = 1;
            }            
        }else{
            $buscar = get_results("SELECT * FROM wp_vivemov_alimentos_porciones WHERE intId = ".$intA_Id);
            $buscar = $buscar[0];
            $where = array(
                'intId' => $intA_Id
            );
            $wpdb->update( 'wp_vivemov_alimentos_porciones', $registro, $where, null, null );
            echo fnMensaje(1,'Listo, alimento actualizado!');
        }
        // $user = wp_insert_user( $registro );
        // echo 'Registration complete. Goto <a href="' . get_site_url() . '/wp-login.php">login page</a>.';   
    }
}

function fnBDAlimentos_validar($intA_Cantidad,$intA_UM,$strA_Alimento,$decA_Proteina,$decA_Carbs,$decA_Grasa,$decA_Libre){
    global $reg_errors;
    $reg_errors = new WP_Error;
    if ( empty( $intA_Cantidad ) || empty($intA_UM) || empty($strA_Alimento) ) {
        $reg_errors->add('field', 'Todos los campos(*) son requeridos');
    }
    if ( empty($intA_Cantidad) ) {
        $reg_errors->add('field', 'Ingresar Cantidad!');
    }
    if ( empty($intA_UM) ) {
        $reg_errors->add('field', 'Ingresar Unidad de Medida');
    }
    if ( empty($strA_Alimento) ) {
        $reg_errors->add('field', 'Ingresar Alimento');
    }
    if ( empty($decA_Proteina) && empty($decA_Carbs) && empty($decA_Grasa) && empty($decA_Libre) ) {
        $reg_errors->add('field', 'Ingresar Proteina, Carbohidratos, Grasa o Libre!');
    }
    if ( is_wp_error( $reg_errors ) ) {        
        $strMensaje = '';
        foreach ( $reg_errors->get_error_messages() as $error ) {        
            $strMensaje = $strMensaje.'<br/>'.$error;
        }
        echo fnMensaje(2,$strMensaje);
    }
}

function fnBDAlimentos_nuevo($listUM){
    global $intA_Id,$intA_Cantidad,$intA_UM,$strA_Alimento,$decA_Proteina,$decA_Carbs,$decA_Grasa,$decA_Libre;

    $strReponseBDAlimentos = '<form action="'.strtok($_SERVER["REQUEST_URI"],'?').'?action=nuevoalimento" method="post" class="row">     
        <div class="col-md-3 col-xs-6 col-sm-6">
        <label for="intA_Cantidad">Cantidad <strong>*</strong></label>
        <input type="number" name="intA_Cantidad" value='.$intA_Cantidad.'>
        </div>

        <div class="col-md-3 col-xs-6 col-sm-6">
            <label for="intA_UM">Unidad Medida <strong>*</strong></label>
            <select name="intA_UM">';
foreach ($listUM as $um) {
$strReponseBDAlimentos = $strReponseBDAlimentos.'<option value="'.$um->intId.'" '.($um->intId == $intA_UM? 'selected' : '').'>'.$um->strUnidadMedida.'</option>';
}
    $strReponseBDAlimentos = $strReponseBDAlimentos.'</select>
        </div>
        <input type="hidden" value = "'.$intA_Id.'" name="intA_Id"/>
        <div class="col-md-3 col-xs-6 col-sm-6">
        <label for="strA_Alimento">Alimento <strong>*</strong></label>
        <input type="text" name="strA_Alimento" value='.$strA_Alimento.'>
        </div>

        <div class="col-md-3 col-xs-6 col-sm-6">
        <label for="decA_Proteina">Proteina </label>
        <input type="number" name="decA_Proteina" value='.($decA_Proteina > 0 ? $decA_Proteina : '').'>
        </div>

        <div class="col-md-3 col-xs-6 col-sm-6">
        <label for="decA_Carbs">Carbohidratos</label>
        <input type="number" name="decA_Carbs" value='.($decA_Carbs > 0 ? $decA_Carbs : '').'>
        </div>

        <div class="col-md-3 col-xs-6 col-sm-6">
        <label for="decA_Grasa">Grasa</label>
        <input type="number" name="decA_Grasa" value='.($decA_Grasa > 0 ? $decA_Grasa : '').'>
        </div>

        <div class="col-md-3 col-xs-6 col-sm-6">
        <label for="decA_Libre">Libre</label>
        <input type="number" name="decA_Libre" value='.($decA_Libre > 0 ? $decA_Libre : '').'>
        </div>

        <input type="submit" name="submit" value="'.($intA_Id == 0 ? 'Nuevo' : 'Guardar').'" class="col-md-3 col-xs-12 col-sm-12" style="margin-top: 25px;"/>
        </form>
        <br/>
    ';
    echo $strReponseBDAlimentos;
}

function fnBDAlimentos_1(){
    global $wpdb, $strUsuario;
    $strUsuario = fnViveMovimento_usuario();
    $bitPermiso = wp_get_current_user()['user_admin'];

    global $intA_Id,$intA_Cantidad,$intA_UM,$strA_Alimento,$decA_Proteina,$decA_Carbs,$decA_Grasa,$decA_Libre;
    $intA_Id = 0;
    
    if (isset($_GET['action']) && $_GET['action'] == 'nuevoalimento' && isset($_POST['submit'] ) ) {
        fnBDAlimentos_validar($_POST['intA_Cantidad'],$_POST['intA_UM'],$_POST['strA_Alimento'],$_POST['decA_Proteina'],$_POST['decA_Carbs'],$_POST['decA_Grasa'],$_POST['decA_Libre']);

        $intA_Id = $_POST['intA_Id'];
        $intA_Cantidad = $_POST['intA_Cantidad'];
        $intA_UM = $_POST['intA_UM'];
        $strA_Alimento = $_POST['strA_Alimento'];
        $decA_Proteina = $_POST['decA_Proteina'];
        $decA_Carbs = $_POST['decA_Carbs'];
        $decA_Grasa = $_POST['decA_Grasa'];
        $decA_Libre = $_POST['decA_Libre'];
        fnBDAlimentos_save();
    }else if (isset($_GET['action']) && $_GET['action'] == 'editarAlimento' && isset($_POST['submit'] ) ) {
        $buscar = get_results("SELECT * FROM wp_vivemov_alimentos_porciones WHERE intId = ".$_POST['intId']);
        $buscar = $buscar[0];

        $intA_Id = $buscar->intId;
        $intA_Cantidad = $buscar->decPorcion;
        $intA_UM = $buscar->intUnidadMedida;
        $strA_Alimento = $buscar->strAlimento;
        $decA_Proteina = $buscar->decProteina;
        $decA_Carbs = $buscar->decCarbohidratos;
        $decA_Grasa = $buscar->decGrasa;
        $decA_Libre = $buscar->decLibre;
    }else if (isset($_GET['action']) && $_GET['action'] == 'eliminarAlimento' && isset($_POST['submit'] ) ) {
        $registro = array(
            'bitActivo'         => false
        );
        $where = array(
            'intId' => $_POST['intId']
        );
        $wpdb->update( 'wp_vivemov_alimentos_porciones', $registro, $where, null, null );
            echo fnMensaje(1,'Alimento, eliminado!');
    }
    if($bitPermiso == true){
        $listUM = get_results("SELECT * FROM wp_vivemov_alimentos_unidad_medida WHERE bitActivo = 1 ORDER BY strUnidadMedida ASC;");
        fnBDAlimentos_nuevo($listUM);
        if (isset($_GET['action']) && ($_GET['action'] == 'nuevoalimento' || $_GET['action'] == 'editarAlimento' || $_GET['action'] == 'eliminarAlimento')) {
            echo '<script>setTimeout(function(){ $("#tablist1-tab3").click();  }, 1000);</script>';
        }
    }
    $listAlimentos = get_results("SELECT ap.*, um.strUnidadMedida FROM wp_vivemov_alimentos_porciones ap INNER JOIN wp_vivemov_alimentos_unidad_medida um ON um.intId = ap.intUnidadMedida WHERE ap.bitActivo=1");
    fnBDAlimentos_tabla($listAlimentos, $bitPermiso);
}

function fnBDAlimentos_tabla($listAlimentos, $bitPermiso){
    echo '<style> #tblAlimentos {font-family: "Trebuchet MS", Arial, Helvetica, sans-serif; border-collapse: collapse; width: 100%; } #tblAlimentos td, #customers th {border: 1px solid #ddd; padding: 8px; } #tblAlimentos tr:nth-child(even){background-color: #f2f2f2;} #tblAlimentos tr:hover {background-color: #ddd;} #tblAlimentos th {padding-top: 12px; padding-bottom: 12px; text-align: center; color: black; } #tblAlimentos td {text-align: center; } </style>';
    echo '<table id="tblAlimentos">';

    $strEditar = '';
    if($bitPermiso == true){
        $strEditar = '<td class="col-md-2 col-xs-2 col-sm-2">Accion</td>';
    }
    echo '<tr>'.$strEditar.'
        <th class="blanco">Porcion</th>
        <th class="blanco">Alimento</th>
        <th class="amarillo">Proteina</th>
        <th class="naranja">Carbohidrato</th>
        <th class="celeste">Grasa</th>
        <th class="rosado">Libre</th>
      </tr>
    ';
    foreach ($listAlimentos as $item) {
        $strColor = '';
        if($item->decProteina > 0){
            $strColor = 'amarillo';
        }else if($item->decCarbohidratos > 0){
            $strColor = 'naranja';
        }else if($item->decGrasa > 0){
            $strColor = 'celeste';
        }else if($item->decLibre > 0){
            $strColor = 'rosado';
        }
        if($item->decProteina > 0 && $item->decCarbohidratos > 0 && $item->decGrasa > 0){
            $strColor = 'gris';
        }else if($item->decProteina > 0 && $item->decGrasa > 0){
            $strColor = 'rojo';
        }else if($item->decProteina > 0 && $item->decCarbohidratos > 0){
            $strColor = 'morado';
        }else if($item->decCarbohidratos > 0 && $item->decGrasa > 0){
            $strColor = 'verde';
        }
        $strEditar = '';
        if($bitPermiso == true){
            $strEditar = '<td>'.'<form action="'.strtok($_SERVER["REQUEST_URI"],'?').'?action=editarAlimento" method="post"><input  type="hidden" name="intId" value="'.$item->intId.'"><input type="submit" name="submit" value="Editar" class="btn btn-block" /></form>
                <form action="'.strtok($_SERVER["REQUEST_URI"],'?').'?action=eliminarAlimento" method="post"><input type="hidden" name="intId" value="'.$item->intId.'"><input type="submit" name="submit" value="Eliminar" class="btn btn-block"/></form>'.'</td>';
        }

        echo '<tr>'.$strEditar.'
                <td class="'.$strColor.'">'.$item->decPorcion.' '.$item->strUnidadMedida.'</td>
                <td class="'.$strColor.'">'.$item->strAlimento.'</td>
                <td class="amarillo">'.($item->decProteina > 0 ? $item->decProteina : '').'</td>
                <td class="naranja">'.($item->decCarbohidratos > 0 ? $item->decCarbohidratos : '').'</td>
                <td class="celeste">'.($item->decGrasa > 0 ? $item->decGrasa : '').'</td>
                <td class="rosado">'.($item->decLibre > 0 ? $item->decLibre : '').'</td>
              </tr>
        ';
    }
    echo '</table>';
}

?>