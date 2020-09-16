<?php
function fnListadoDiario($decDiario){
    global $wpdb, $strUsuario;
    $strUsuario = fnViveMovimento_usuario();
    if ($decDiario == null || $decDiario == 0) {
        $list = $wpdb->get_results("SELECT intId,datFecha, SUM(intProteinas) intProteinas,SUM(intCarbohidratos) intCarbohidratos,SUM(intGrasas) intGrasas,SUM(intVegetales) intVegetales,SUM(intLibres) intLibres,strNota
            FROM wp_vivemov_users_diario
            WHERE strUsuario = '$strUsuario' GROUP BY intId,datFecha,strNota ORDER BY datFecha DESC");
    }else{
        $list = $wpdb->get_results("SELECT intId,datFecha, SUM(intProteinas) intProteinas,SUM(intCarbohidratos) intCarbohidratos,SUM(intGrasas) intGrasas,SUM(intVegetales) intVegetales,SUM(intLibres) intLibres,strNota
        FROM wp_vivemov_users_diario
        WHERE strUsuario = '$strUsuario' AND intId = $decDiario GROUP BY intId,datFecha,strNota ORDER BY datFecha DESC");
    }
    return $list;
}
function fnTiempoSiguiente(){
    global $wpdb, $strUsuario;
    $strUsuario = fnViveMovimento_usuario();
//        SELECT CASE WHEN MAX(T.intId) + 1 <= 3 THEN (MAX(T.intId) + 1) ELSE MAX(T.intId) END intTiempoSiguiente
    $list = $wpdb->get_results("
        SELECT MAX(T.intId) intTiempoSiguiente
        FROM wp_vivemov_users_diario_detalle D
        INNER JOIN wp_vivemov_alimentos_tiempo T ON T.intId = D.intTiempo
        WHERE 
            strUsuario = '$strUsuario' AND CAST(D.datModificado AS DATE) = CAST(NOW() AS DATE)
    ");
    if (count($list) > 0 ) {
        return ($list[0]->intTiempoSiguiente != null && $list[0]->intTiempoSiguiente > 0 ? $list[0]->intTiempoSiguiente : 1);
    }else{

    }
    return 1;
}
function fnDiarioSiguiente(){
    global $wpdb, $strUsuario;
    $strUsuario = fnViveMovimento_usuario();
    $list = $wpdb->get_results("        
        SELECT MAX(D.intId) intDiario
        FROM wp_vivemov_users_diario D
        WHERE 
            strUsuario = '$strUsuario' AND CAST(D.datFecha AS DATE) <= CAST(NOW() AS DATE)
    ");
    if (count($list) > 0 ) {
        return $list[0]->intDiario;        
    }
    return 0;
}
function fnDiario_Tiempos(){
    global $wpdb;
    $listado = $wpdb->get_results("SELECT * FROM wp_vivemov_alimentos_tiempo T WHERE T.bitActivo = 1 ORDER BY decOrden ASC");
    return $listado;
}
function fnDiario_Agregar($txtFechaDiario){
    global $wpdb, $strUsuario;
    $datSiguiente = null;

    $listArreglo = explode('/', $txtFechaDiario);
    $fechaFormato = $listArreglo[2].'-'.$listArreglo[1].'-'.$listArreglo[0]; 

    $datPorFecha = $wpdb->get_results("SELECT datFecha FROM wp_vivemov_users_diario WHERE strUsuario = '$strUsuario' AND datFecha = '".$fechaFormato."';");
    $datMaximo = $wpdb->get_results("SELECT DATE_ADD(MAX(datFecha), INTERVAL 1 DAY) datFecha FROM wp_vivemov_users_diario WHERE strUsuario = '$strUsuario';");
    if ($datPorFecha == null) {
        $datSiguiente = $fechaFormato;            
        $strUsuario = fnViveMovimento_usuario();
        $itemRow = array(
            'strUsuario'        => $strUsuario,
            // 'datFecha'          => date('Y-m-d'),
            'datFecha'          => $datSiguiente,
            'datCreado'         => date('Y-m-d H:i:s'),
            'datModificado'     => date('Y-m-d H:i:s'),
            'intProteinas'      => 0,
            'intCarbohidratos'  => 0,
            'intGrasas'         => 0,
            'intVegetales'      => 0,
            'intLibres'         => 0
        );
        if($_SESSION["intFormulario"] == 1){
            $_SESSION["intFormulario"] = 0;
            // $buscar = $wpdb->get_results("SELECT * FROM wp_vivemov_users_diario WHERE strUsuario = '$strUsuario' AND datFecha = '".$datSiguiente."' LIMIT 1;");
            // if(count($buscar) == 0){
                $responseDiario = $wpdb->insert("wp_vivemov_users_diario", $itemRow);
                if($responseDiario) {
                    echo fnMensaje(1,'Listo, dia('.$datSiguiente.') agregado!');
                    $myDateTime = DateTime::createFromFormat('Y-m-d', $datSiguiente);
                    echo '<script> setTimeout(function(){ $("#txtFechaDiario").datepicker("setDate", "'.$myDateTime->format('d-m-Y').'");}, 900);</script>';
                    echo '<script> setTimeout(function(){ fnCargarDiario(); }, 1100);</script>';
                    echo '<script> setTimeout(function(){ $("#txtFechaDiario").datepicker("setDate", "'.$myDateTime->format('d-m-Y').'"); }, 2000);</script>';
                    echo '<script> setTimeout(function(){ $("#txtFechaDiario").datepicker("setDate", "'.$myDateTime->format('d-m-Y').'"); }, 3000);</script>';
                } else {
                    echo fnMensaje(2,'Inconvenientes, no guardado!');
                }
            // }
        }else{
            $_SESSION["intFormulario"] = 1;
        }
    }else{
        echo fnMensaje(2,'Ya hay un diario agregado con esa fecha ('.$fechaFormato.')');
    }
    // }else if ($datMaximo == null) {
    //     $datSiguiente = date('Y-m-d');
    // }else if($datMaximo[0]->datFecha > date('Y-m-d') == 1){ //si la ultima fecha mas 1 dia, es mayor igual a hoy, continuamos para delante
    //     $datSiguiente = $datMaximo[0]->datFecha;
    // }else{ //si la ultima fecha + 1 dia no es mayor a hoy, entonces empezamos de nuevo desde hoy
    //     $datSiguiente = date('Y-m-d');
    // }
}
function fnDiario_clonar(){
    global $wpdb, $strUsuario;
    global $reg_errors;
    global $intDiarioDet_Cantidad, $detEncabezado, $detAlimento, $detTiempo, $txtClonar;

    $datSiguiente = null;

    $datMaximo = $wpdb->get_results("SELECT DATE_ADD(MAX(datFecha), INTERVAL 1 DAY) datFecha FROM wp_vivemov_users_diario WHERE strUsuario = '$strUsuario';");
    if ($datMaximo == null) {
        $datSiguiente = date('Y-m-d');
    }else if($datMaximo[0]->datFecha > date('Y-m-d') == 1){ //si la ultima fecha mas 1 dia, es mayor igual a hoy, continuamos para delante
        $datSiguiente = $datMaximo[0]->datFecha;
    }else{ //si la ultima fecha + 1 dia no es mayor a hoy, entonces empezamos de nuevo desde hoy
        $datSiguiente = date('Y-m-d');
    }

    $strUsuario = fnViveMovimento_usuario();
    $itemRow = array(
        'strUsuario'        => $strUsuario,
        // 'datFecha'          => date('Y-m-d'),
        'datFecha'          => $datSiguiente,
        'datCreado'         => date('Y-m-d H:i:s'),
        'datModificado'     => date('Y-m-d H:i:s'),
        'intProteinas'      => 0,
        'intCarbohidratos'  => 0,
        'intGrasas'         => 0,
        'intVegetales'      => 0,
        'intLibres'         => 0
    );
    if($_SESSION["intFormulario"] == 1){
        $_SESSION["intFormulario"] = 0;
        // $buscar = $wpdb->get_results("SELECT * FROM wp_vivemov_users_diario WHERE strUsuario = '$strUsuario' AND datFecha = '".$datSiguiente."' LIMIT 1;");
        // if(count($buscar) == 0){
            $responseDiario = $wpdb->insert("wp_vivemov_users_diario", $itemRow);
            $nuedoDiarioClonado = $wpdb->insert_id;
            if($responseDiario) {
                $clonar = $wpdb->get_results("
                    INSERT INTO wp_vivemov_users_diario_detalle(intDiario,strUsuario,intTiempo,intAlimentoPorcion,devCantidad,strDescripcion,intProteinas,intCarbohidratos,intGrasas,intVegetales,intLibres,datModificado)
                    SELECT $nuedoDiarioClonado, strUsuario,intTiempo,intAlimentoPorcion,devCantidad,strDescripcion,intProteinas,intCarbohidratos,intGrasas,intVegetales,intLibres,NOW()
                    FROM wp_vivemov_users_diario_detalle D
                    WHERE D.intDiario = $txtClonar;");
                echo fnMensaje(1,'Listo, diario clonado en dia '.$datSiguiente.'');
            } else {
                echo fnMensaje(2,'Inconvenientes, no guardado!');
            }
        // }
    }else{
        $_SESSION["intFormulario"] = 1;
    }
}
function fnDiario_Detalle($decDiario,$intTiempo){
    global $wpdb;
    $listado = $wpdb->get_results("
        SELECT T.strTiempo,T.bitPrincipal,DD.*, AP.strAlimento
            ,um.strUnidadMedida
            ,((AP.decPorcion /
              (
            CASE
                WHEN AP.decProteina > 0 THEN AP.decProteina
                WHEN AP.decCarbohidratos > 0 THEN AP.decCarbohidratos
                WHEN AP.decGrasa > 0 THEN AP.decGrasa
                WHEN AP.decVegetales > 0 THEN AP.decVegetales
                WHEN AP.decLibre > 0 THEN AP.decLibre
              END
                  /
             CASE
                WHEN DD.intProteinas > 0 THEN DD.intProteinas
                WHEN DD.intCarbohidratos > 0 THEN DD.intCarbohidratos
                WHEN DD.intGrasas > 0 THEN DD.intGrasas
                WHEN DD.intVegetales > 0 THEN DD.intVegetales
                WHEN DD.intLibres > 0 THEN DD.intLibres
              END
             )
            ) * 1) intCantidadTomado
        FROM wp_vivemov_alimentos_tiempo T
        INNER JOIN wp_vivemov_users_diario_detalle DD ON DD.intDiario = $decDiario AND DD.intTiempo = T.intId  
        INNER JOIN wp_vivemov_alimentos_porciones AP ON AP.intId = DD.intAlimentoPorcion
        INNER JOIN wp_vivemov_alimentos_unidad_medida um on um.intId = AP.intUnidadMedida
        WHERE T.bitActivo = 1 AND T.intId = $intTiempo ORDER BY AP.strAlimento ASC");
    return $listado;
}
function fnDiario_eliminar($intDetalle,$detEncabezado){
    global $wpdb,$strUsuario;
    $wpdb->delete( 'wp_vivemov_users_diario_detalle', array( 'intId' => $intDetalle ) );
    fnDiarioDetalleCalcular_P_CH_G_V($wpdb,$strUsuario,$detEncabezado);
    $result['type'] = 'success';
    $result['mnj'] = 'Listo, eliminado!';
    $result = json_encode($result);
    echo $result;
}
function fnDiario_AgregarDetalle(){
    global $reg_errors, $strUsuario;
    global $intDiarioDet_Cantidad, $detEncabezado, $detAlimento, $detTiempo, $txtClonar;

    if ($txtClonar != null && $txtClonar != '' && $txtClonar > 0) {
        fnDiario_clonar();
        return;
    }

    $strUsuario = fnViveMovimento_usuario();
    $itemRow = array(
        'intDiario'             => $detEncabezado,
        'strUsuario'            => $strUsuario,
        'intTiempo'             => $detTiempo,
        'intAlimentoPorcion'    => $detAlimento,
        'devCantidad'           => $intDiarioDet_Cantidad,
        'strDescripcion'        => '...',
        'intProteinas'          => 0,
        'intCarbohidratos'      => 0,
        'intGrasas'             => 0,
        'intVegetales'          => 0,
        'intLibres'             => 0,
        'datModificado'         => date('Y-m-d H:i:s'),
    );
    $result;
    // if($_SESSION["intFormulario"] == 1){
    //     $_SESSION["intFormulario"] = 0;
        if ($reg_errors == null || ($reg_errors != null && 1 > count( $reg_errors->get_error_messages() ) )) {
            global $wpdb;
            $responseDiario = $wpdb->insert("wp_vivemov_users_diario_detalle", $itemRow);
            if($responseDiario) {
                // echo fnMensaje(1,'Listo, agregado!');
                fnDiarioDetalleCalcular_P_CH_G_V($wpdb,$strUsuario,$detEncabezado);

                $result['type'] = 'success';
                $result['mnj'] = 'Listo, agregado!';
            } else {
                // echo fnMensaje(2,'Inconvenientes, no guardado!');
                $result['type'] = 'error';
                $result['mnj'] = 'Inconvenientes, no guardado!';
            }
        }
    // }else{
    //     $_SESSION["intFormulario"] = 1;
    // }
    $result = json_encode($result);
    echo $result;
}
function fnDiarioDetalleCalcular_P_CH_G_V($wpdb,$strUsuario,$detEncabezado){
    $wpdb->get_results("
        UPDATE wp_vivemov_users_diario_detalle as D
        INNER JOIN wp_vivemov_alimentos_porciones AP ON AP.intId = D.intAlimentoPorcion
        /* SET D.intProteinas = (D.devCantidad/AP.decPorcion) * AP.decProteina
        ,D.intCarbohidratos = (D.devCantidad/AP.decPorcion) * AP.decCarbohidratos
        ,D.intGrasas = (D.devCantidad/AP.decPorcion) * AP.decGrasa
        ,D.intVegetales = (D.devCantidad/AP.decPorcion) * AP.decVegetales
        ,D.intLibres = (D.devCantidad/AP.decPorcion) * AP.decLibre */
        SET D.intProteinas = D.devCantidad * AP.decProteina
        ,D.intCarbohidratos = D.devCantidad * AP.decCarbohidratos
        ,D.intGrasas = D.devCantidad * AP.decGrasa
        ,D.intVegetales = D.devCantidad * AP.decVegetales
        ,D.intLibres = D.devCantidad * AP.decLibre
        WHERE D.intDiario = $detEncabezado AND D.strUsuario = '$strUsuario';
        ");
    $wpdb->get_results("
        UPDATE wp_vivemov_users_diario as E
        SET intProteinas = (SELECT SUM(D.intProteinas) FROM wp_vivemov_users_diario_detalle D WHERE D.intDiario = $detEncabezado GROUP BY D.intDiario)
        ,intCarbohidratos = (SELECT SUM(D.intCarbohidratos) FROM wp_vivemov_users_diario_detalle D WHERE D.intDiario = $detEncabezado GROUP BY D.intDiario)
        ,intGrasas = (SELECT SUM(D.intGrasas) FROM wp_vivemov_users_diario_detalle D WHERE D.intDiario = $detEncabezado GROUP BY D.intDiario)
        ,intVegetales = (SELECT SUM(D.intVegetales) FROM wp_vivemov_users_diario_detalle D WHERE D.intDiario = $detEncabezado GROUP BY D.intDiario)
        ,intLibres = (SELECT SUM(D.intLibres) FROM wp_vivemov_users_diario_detalle D WHERE D.intDiario = $detEncabezado GROUP BY D.intDiario)
        WHERE intId = $detEncabezado  AND strUsuario = '$strUsuario';
        ");
}
function fnDiario_ActualizarDetalle(){
    global $wpdb, $intDiarioDet_Cantidad, $detAlimento, $detTiempo, $intIDDETALLE, $strUsuario,$detEncabezado;
    echo fnMensaje(1,'Listo, editado!');
    $wpdb->get_results('
        UPDATE wp_vivemov_users_diario_detalle as D
        SET
            D.intTiempo = '.$detTiempo.'
            ,D.intAlimentoPorcion = '.$detAlimento.'
            ,D.devCantidad = '.$intDiarioDet_Cantidad.'
        WHERE D.intId = '.$intIDDETALLE);
    fnDiarioDetalleCalcular_P_CH_G_V($wpdb,$strUsuario,$detEncabezado);
}
function fnDiario_Detalle_Validar($decCantidad,$decAlimento,$decTiempo){
    global $reg_errors;
    $reg_errors = new WP_Error;
    if ( empty( $decCantidad ) ) {
        $reg_errors->add('field', 'Ingresar cantidad!');
    }
    if ( empty( $decTiempo ) ) {
        $reg_errors->add('field', 'Seleccionar tiempo!');
    }
    if ( empty( $decAlimento ) || $decAlimento == 0 ) {
        $reg_errors->add('field', 'Seleccionar alimento!');
    }
    if ( is_wp_error( $reg_errors ) ) { 
        $strMensaje = '';
        foreach ( $reg_errors->get_error_messages() as $error ) {        
            $strMensaje = $strMensaje.'<br/>'.$error;
        }
        echo fnMensaje(2,$strMensaje);
    }
}
function fnTab_5(){
    $intDiasSuscripcion = fnVerificarCombraDeSuscripcion();
    if($intDiasSuscripcion < 30){
        fnFoodJournalCore();
    }else{
        fnTab_4_alerta_Suscripcion();
    }
}
//Ajax
function fnViveMovimentoDiarioAgregar(){
    global $wpdb, $strUsuario;
    global $decProteinas,$decCarbo,$decGrasas;
    global $decPorcionDia;
    global $reg_errors;
    global $itemEditar;
    $itemEditar = null;
    $decPorcionDia = array(0,0,0,0);
    $decPorcionDia[0] = 0;
    $decPorcionDia[1] = 0;
    $decPorcionDia[2] = 0;
    $strUsuario = fnViveMovimento_usuario();

    global $intDiarioDet_Cantidad, $detEncabezado, $detAlimento, $detTiempo, $txtClonar, $intIDDETALLE;
    $intDiarioDet_Cantidad = $_GET['intDiarioDet_Cantidad'];
    $detEncabezado = $_GET['intDiarioDet_Enc'];
    $detAlimento = (isset($_GET['intDiarioDet_Alimento'])?$_GET['intDiarioDet_Alimento'] : 0);
    $detTiempo = $_GET['intDiarioDet_Tiempo'];
    $txtClonar = $_GET['txtClonar'];
    $intIDDETALLE = $_GET['intIDDETALLE'];
    if ($intIDDETALLE == null || $intIDDETALLE == 0) {
        fnDiario_AgregarDetalle();
    }
    exit();
}
//ajax
function fnViveMovimentoDiarioDetalleTabla(){
    $listTiempos = fnDiario_Tiempos();
    $listadoDiario = fnListadoDiario(intval($_GET['intDiario']));
    $diario = $listadoDiario[0];
    $strUsuario = fnViveMovimento_usuario();
    $strURL = 'https://vivemovimento.com/user/'.$strUsuario.'/';
    fnViveMovimentoDiarioDetalleTablaCore($strURL,$diario,$listTiempos);
    exit();
}
function fnViveMovimentoDiarioDetalleTablaCore($strURL,$diario,$listTiempos){
    echo '<div id="div_vivemovimento_tabla_diario_'.$diario->intId.'"><table id="tblDiario_'.$diario->intId.'" class="tblDiario">';
        foreach ($listTiempos as $tiempo) {
            $listDetalle = fnDiario_Detalle($diario->intId,$tiempo->intId);
            if($tiempo->bitPrincipal == 0 && count($listDetalle) == 0){continue;}
            echo '<tr>
                <td class="blanco" style="font-weight: bolder;" colspan=2>'.$tiempo->strTiempo.'</td>
                <td style="font-weight: bolder;width: 10%;">Cantidad</td>
                <td class="amarillo" style="font-weight: bolder;width: 10%;">Proteina</td>
                <td class="naranja" style="font-weight: bolder;width: 10%;">Carbohidrato</td>
                <td class="celeste" style="font-weight: bolder;width: 10%;">Grasa</td>
                <td class="verde" style="font-weight: bolder;width: 10%;">Vegetales</td>
                <td class="morado" style="font-weight: bolder;width: 10%;">Libre</td>
              </tr>';
              foreach ($listDetalle as $det) {
                echo '<tr>
                        <td style="width: 90px;">
                            <form action="'.$strURL.'/?action=tab_Paso_5&tab_Diario_'.$diario->intId.'" method="post" style="display: inline-block;">
                                <input type="hidden" name="intOp" value="3" />
                                <input type="hidden" name="intEncabezado" value="'.$diario->intId.'" />
                                <input type="hidden" name="intEliminar" value="'.$det->intId.'" />
                                <button type="button" class="btn btn-link badge" role="button" href="#" style="color: white;" onClick="fnDiarioEliminarAjax('.$diario->intId.','.$det->intId.')"><i class="fas fa-trash-alt"></i></button>
                            </form>
                            <form action="'.$strURL.'/?action=tab_Paso_5&tab_Diario_'.$diario->intId.'" method="post" style="display: inline-block;">
                                <input type="hidden" name="intOp" value="4" />
                                <input type="hidden" name="intEditar" value="'.$det->intId.'" />
                                <button type="submit" class="btn btn-link badge" role="button" href="#"><i class="fa fa-pencil"></i></button>
                            </form>                            
                        </td>
                        <td>'.$det->strAlimento.'</td>
                        <td>'.fnRedondearCUSTOMUP_1($det->intCantidadTomado).' '.$det->strUnidadMedida.'</td>
                        <td class="amarillo">'.$det->intProteinas.'</td>
                        <td class="naranja">'.$det->intCarbohidratos.'</td>
                        <td class="celeste">'.$det->intGrasas.'</td>
                        <td class="verde">'.$det->intVegetales.'</td>
                        <td class="morado">'.$det->intLibres.'</td>
                      </tr>';
              }
        }
    echo '<tr>
        <td class="rojo" rowspan="3" style="font-weight: bolder;" colspan=3>Total</td>
        <td class="rojo" style="font-weight: bolder;">Proteinas</td>
        <td class="rojo" style="font-weight: bolder;">Carbohidrato</td>
        <td class="rojo" style="font-weight: bolder;">Grasa</td>
        <td class="rojo" style="font-weight: bolder;">Vegetales</td>
        <td class="rojo" style="font-weight: bolder;">Libre</td>
      </tr>
      <tr>
        <td class="rojo" style="font-weight: bolder;">'.$diario->intProteinas.'</td>
        <td class="rojo" style="font-weight: bolder;">'.$diario->intCarbohidratos.'</td>
        <td class="rojo" style="font-weight: bolder;">'.$diario->intGrasas.'</td>
        <td class="rojo" style="font-weight: bolder;">'.$diario->intVegetales.'</td>
        <td class="rojo" style="font-weight: bolder;">'.$diario->intLibres.'</td>
      </tr>';
    echo '</table></div>';
}
function fnViveMovimentoDiarioEliminar(){
    global $strUsuario;
    $strUsuario = fnViveMovimento_usuario();
    fnDiario_eliminar(intval($_GET['intEliminar']), intval($_GET['intEncabezado']));
    exit();
}
function fnViveMovimentoDiarioDetalleReceta(){
    fnViveMovimentoDiarioDetalleReceta_Core(intval($_GET['intEncabezado']));
    exit();
}
function fnViveMovimentoDiarioDetalleReceta_Core($decDiario){
    global $strUsuario;
    $strUsuario = fnViveMovimento_usuario();

    $intCursor = 1;
    $intCursorSinReceta = 1;
    $intCursorRow = 1;

    $listadoDiario = fnListadoDiario($decDiario);
    $diario = $listadoDiario[0];
    $listTiempos = fnDiario_Tiempos();
    $listadoRECETAS = fnViveMovimentoRecetaListado();
    foreach ($listadoRECETAS as $item) {
        if($item->bitActivo == 0){ $intCursor += 1; continue; } ?>
        <div class="col-md-12 col-xs-12 col-sm-12" style="padding-left: 2px;padding-right: 2px;">
            <button type="button" class="btn-info btn-xs btn-block" onclick="fnViveMovimentoDiarioDetalleReceta(<?= $decDiario ?>);">
                <i class="fa fa-refresh" aria-hidden="true"></i> Refrescar recetas
            </button>
        </div>

        <div class="col-md-4 col-xs-12 col-sm-12" style="padding-left: 2px;padding-right: 2px;">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list-ul" aria-hidden="true"></i> Receta No. <?= $intCursor ?>
                        <button type="button" class="btn btn-success btn-xs" onclick="fnViveMovimento_Receta_Journal_agregar(<?= $diario->intId ?>, <?= $item->intId ?>);" style="float: right;">
                          <i class="fa fa-plus" aria-hidden="true"></i> Journal
                        </button>
                        <select class="intDiarioDet_Tiempo" id="intDiarioDet_Receta_Tiempo_<?= $diario->intId ?>">';
                        <?php
                        foreach ($listTiempos as $tiempo) {
                            echo '<option value="'.$tiempo->intId.'">'.$tiempo->strTiempo.'</option>';
                        } ?>
                        </select>
                    </h3>
                </div>
                <div class="panel-body" style="padding-left: 2px;padding-right: 2px;">
                    <div class="table-responsive">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 100px;">Porción</th>
                                    <th>Alimento</th>
                                </tr>
                            </thead>
                        <?php foreach ($item->detalle as $itemDetalle) { ?>
                            <tr>
                                <td style="padding-left: 0px;padding-right: 0px;"><?= $itemDetalle->decCantidad ?> <?= $itemDetalle->strUnidadMedida ?></td>
                                <td><?= $itemDetalle->strAlimento ?></td>
                            </tr>
                        <?php } ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php
        if ($intCursorRow % 3 == 0) {
          echo '<div class="col-md-12 col-xs-12 col-sm-12">.</div>';
        }

        $intCursor += 1;
        $intCursorSinReceta += 1;
        $intCursorRow += 1;
    }
    if ($intCursorSinReceta == 1) {
        echo '<div class="alert alert-info" role="alert"><strong>Sin Recetas!</strong> Debes agregar tus propias recetas para luego ingresarlas desde el journal. <a href="#" onClick="$('."'#tab_Paso_9_Ref'".').click();"; class="alert-link">Ver Mis Recetas</a></div>';
    }
}
function fnFoodJournalCore(){
    global $wpdb, $strUsuario;
    global $decProteinas,$decCarbo,$decGrasas;
    global $decPorcionDia;
    global $reg_errors;
    global $itemEditar;
    $itemEditar = null;
    $decPorcionDia = array(0,0,0,0);
    $decPorcionDia[0] = 0;
    $decPorcionDia[1] = 0;
    $decPorcionDia[2] = 0;
    $strUsuario = fnViveMovimento_usuario();

    if (isset($_GET['action']) && $_GET['action'] == 'tab_Paso_5' && isset($_POST['intOp']) && $_POST['intOp'] != null && $_POST['intOp'] == '1') {    
        fnDiario_Agregar($_POST['txtFechaDiario']);
    } else if (isset($_GET['action']) && $_GET['action'] == 'tab_Paso_5' && isset($_POST['intDiarioDet_Enc']) && $_POST['intDiarioDet_Enc'] != null  && isset($_POST['intOp']) && $_POST['intOp'] != null && $_POST['intOp'] == '2') {
        if (!isset($_POST['txtClonar']) && ($_POST['txtClonar'] == null || $_POST['txtClonar'] == '')) {
            fnDiario_Detalle_Validar($_POST['intDiarioDet_Cantidad'],(isset($_POST['intDiarioDet_Alimento'])?$_POST['intDiarioDet_Alimento'] : 0),$_POST['intDiarioDet_Tiempo']);
        }
        global $intDiarioDet_Cantidad, $detEncabezado, $detAlimento, $detTiempo, $txtClonar, $intIDDETALLE;
        $intDiarioDet_Cantidad = $_POST['intDiarioDet_Cantidad'];
        $detEncabezado = $_POST['intDiarioDet_Enc'];
        $detAlimento = (isset($_POST['intDiarioDet_Alimento'])?$_POST['intDiarioDet_Alimento'] : 0);
        $detTiempo = $_POST['intDiarioDet_Tiempo'];
        $txtClonar = $_POST['txtClonar'];
        $intIDDETALLE = $_POST['intIDDETALLE'];
        if ($intIDDETALLE == null || $intIDDETALLE == 0) {
            fnDiario_AgregarDetalle();
        }else{
            fnDiario_ActualizarDetalle();            
        }
    }else if (isset($_GET['action']) && $_GET['action'] == 'tab_Paso_5' && isset($_POST['intOp']) && $_POST['intOp'] != null && $_POST['intOp'] == '3') {
        fnDiario_eliminar(intval($_POST['intEliminar']), intval($_POST['intEncabezado']));
    }else if (isset($_GET['action']) && $_GET['action'] == 'tab_Paso_5' && isset($_POST['intOp']) && $_POST['intOp'] != null && $_POST['intOp'] == '4') {
        $intEditar = intval($_POST['intEditar']);
        $itemEditar = $wpdb->get_results("SELECT * FROM wp_vivemov_users_diario_detalle WHERE intId = ".$intEditar);        
        // fnDiario_eliminar(intval($_POST['intEditar']));
    }else if (isset($_GET['action']) && $_GET['action'] == 'tab_Paso_5' && isset($_POST['intOp']) && $_POST['intOp'] != null && $_POST['intOp'] == '5') {
        $intDiario = intval($_POST['intDiario']);
        $txtNota = $_POST['txtNota'];
        $wpdb->get_results("UPDATE wp_vivemov_users_diario as D SET D.strNota = '$txtNota' WHERE D.intId = $intDiario");
        echo fnMensaje(1,'Listo, nota guardada!');
    
    }

    $listadoDiario = fnListadoDiario(null);
    $intDia = 0;
    // if($listadoDiario != null && $listadoDiario[0] != null && $listadoDiario[0]->datFecha != date('Y-m-d')){
    if($listadoDiario != null && $listadoDiario[0] != null){
        $intDia = count($listadoDiario) + 1;
    }else if ($listadoDiario == null){
        $intDia = 1;
    }

    if($listadoDiario != null){        
        $decPorcionDia[0] = $listadoDiario[0]->intProteinas;
        $decPorcionDia[1] = $listadoDiario[0]->intCarbohidratos;
        $decPorcionDia[2] = $listadoDiario[0]->intGrasas;
    }

    $listTiempos = fnDiario_Tiempos();
    $listAlimentos = $wpdb->get_results("SELECT ap.*, um.strUnidadMedida FROM wp_vivemov_alimentos_porciones ap INNER JOIN wp_vivemov_alimentos_unidad_medida um ON um.intId = ap.intUnidadMedida WHERE ap.bitActivo=1");

    $intTiempoSiguiente = fnTiempoSiguiente();
    $intDiarioSiguiente = fnDiarioSiguiente();

    $listadoRECETAS = fnViveMovimentoRecetaListado();

    $misPorciones = null;
    $misPorciones = $wpdb->get_results("SELECT * FROM wp_vivemov_users_porciones WHERE strUsuario = '$strUsuario' ORDER BY decId DESC LIMIT 1;");
    if (count($misPorciones) > 0) {
      $misPorciones = $misPorciones[0];
    }else{
      $misPorciones = null;
    }

?>

  <div class="col-md-5 col-xs-5 col-sm-5 sinPadding">
    <center><h2 style="margin: 0px;"><small>Porciones recomendades del Día</small></h2></center>
    <div class="table-responsive">
      <table class="table table-striped table-condensed" style="margin: 0px;">
        <thead>
          <tr>
            <th class="amarillo">PROTEINAS</th>
            <th class="naranja">CARBOHIDRATOS</th>
            <th class="celeste">GRASAS</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th class="amarillo"><?php echo fnRedondearCUSTOMUP($decProteinas[3]); ?></th>
            <th class="naranja"><?php echo fnRedondearCUSTOMUP($decCarbo[3]); ?></th>
            <th class="celeste"><?php echo fnRedondearCUSTOMUP($decGrasas[3]); ?></th>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-md-2 col-xs-2 col-sm-2">.
  </div>
<?php if ($misPorciones != null) { ?>
  <div class="col-md-5 col-xs-5 col-sm-5 sinPadding">
    <center><h2 style="margin: 0px;"><small>Tus propias porciones</small></h2></center>
    <div class="table-responsive">
      <table class="table table-striped table-condensed" style="margin: 0px;">
        <thead>
          <tr>
            <th class="amarillo">PROTEINAS</th>
            <th class="naranja">CARBOHIDRATOS</th>
            <th class="celeste">GRASAS</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th class="amarillo"><?php echo str_replace(',','.',fnRedondearCUSTOMUP_1($misPorciones->intProteina)); ?></th>
            <th class="naranja"><?php echo str_replace(',','.',fnRedondearCUSTOMUP_1($misPorciones->intCarbohidrato)); ?></th>
            <th class="celeste"><?php echo str_replace(',','.',fnRedondearCUSTOMUP_1($misPorciones->intGrasa)); ?></th>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
<?php } ?>

<br/>

<div id="divFechaNoEncontrado" style="display: none;" class="col-md-12 col-xs-12 col-sm-12">
    <div class="alert alert-warning alert-dismissible" role="alert">
      <strong id="span_fecha_no_encontrado"></strong> diario no encontrado.
    </div>
</div>
<script>
    var arrayFechas = [];
</script>

<div class="col-md-12 col-xs-12 col-sm-12 sinPadding">
<div class="panel panel-default">
  <div class="panel-heading">
    <div class="row">
        <form action="<?php echo strtok($_SERVER["REQUEST_URI"],'?') ?>?action=tab_Paso_5" method="post">
            <div class="col-md-3 col-xs-6 col-sm-6 sinPadding">
                <h3 class="panel-title" style="margin-top: 15px;"><i class="fas fa-calendar-day"></i> Food Journal</h3>
            </div>
            <div class="col-md-3 col-xs-6 col-sm-6 sinPadding">
                <label><i class="fas fa-calendar-day"></i> Calendario:</label>
                <div class="input-group date">
                  <input id="txtFechaDiario" name="txtFechaDiario" type="text" class="form-control datepicker"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                </div>
            </div>
            <div class="col-md-6 col-xs-12 col-sm-12 sinPadding" style="padding: 0px;">
              <div class="col-md-12 col-xs-12 col-sm-12 sinPadding" style="padding: 0px;">
                <div class="btn-group btn-group-justified" role="group" aria-label="...">
                  <div class="btn-group" role="group">
                    <button type="button" class="btn" onclick="fnTabNav(4);" style="color: white;display: none;">
                      <i class="fas fa-angle-left"></i> Anterior
                    </button>

                    <button type="button" class="btn" onclick="fnCargarDiario();" style="color: white !important;margin-top: 15px;">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                  </div>
                  <div class="btn-group" role="group">
                        <input type="hidden" name="intOp" value="1">
                        <button type="submit" style="color: white;margin-top: 15px;" class="btn" <?php  echo ($intDia == 0 ? 'disabled="disabled"': ''); ?> >
                          <!-- <i class="fas fa-plus"></i> <?php echo ($intDia > 0 ? ' Agregar Dia '.$intDia : 'Dia actual ya fue agregado'); ?> -->
                          <i class="fas fa-plus"></i> Agregar Dia
                        </button>
                  </div>
                  <div class="btn-group" role="group" style="display: none;">
                    <button type="button" class="btn" onclick="fnTabNav(6);" style="color: white;">
                      Siguiente <i class="fas fa-angle-right"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
        </form>
    </div>
  </div>
  <div class="panel-body">
<?php
    $intDiarioDelDia = 0;

    $intContadorFolder = 0;$bitFolderAbierto = false;
    $intDiaContador = count($listadoDiario);
    echo '<div class="row">';    
    foreach ($listadoDiario as $diario) {
        $datFechaDiario = new DateTime($diario->datFecha);
        if ($intContadorFolder >= (4) && $bitFolderAbierto == false) {
            $bitFolderAbierto = true;
            //se habilita nuevamente los badges de fechas, steven 22/jun/2020 09:40 pm
            // echo '<div class="col-md-12 col-xs-12 col-sm-12 sinPadding" style="display: none;"><br/>';
            echo '<div class="col-md-12 col-xs-12 col-sm-12 sinPadding"><br/>';
            echo '  <a class="btn btn-link badge" role="button" data-toggle="collapse" href="#collapseFOLDER" aria-expanded="false" aria-controls="collapseFOLDER">';
            echo '      <i class="fas fa-calendar-day"></i> Folder de dias anteriores';
            echo '  </a>';
            echo '</div>';
            echo '<div class="collapse col-md-12 col-xs-12 col-sm-12 sinPadding" id="collapseFOLDER">';
        }
        //se habilita nuevamente los badges de fechas, steven 22/jun/2020 09:40 pm
        // echo '<div class="col-md-3 col-xs-12 col-sm-12 sinPadding" style="display: none;">';
        echo '<div class="col-md-3 col-xs-12 col-sm-12 sinPadding">';
        echo '  <a class="btn btn-link badge" role="button" data-toggle="collapse" href="#collapseDiario_'.$diario->intId.'" aria-expanded="false" aria-controls="collapseDiario_'.$diario->intId.'">';
        echo '      <i class="fas fa-calendar-day"></i> Dia '.$intDiaContador.' ('.$datFechaDiario->format('D, d-M-Y').')';
        echo '  </a>';
        echo '</div>';
        $intDiaContador -= 1;
        $intContadorFolder += 1;
    }
    if ($bitFolderAbierto == true) {
        echo '</div>';
    }

    echo '  </div>';
    $intDiaContador = count($listadoDiario);

    date_default_timezone_set('America/Costa_Rica');

    foreach ($listadoDiario as $diario) {
        $datFechaDiario = new DateTime($diario->datFecha);
        if ((new DateTime())->format('d/m/Y') == $datFechaDiario->format('d/m/Y')) {
            $intDiarioDelDia = $diario->intId;
        }
        echo '<script>arrayFechas.push(["'.$datFechaDiario->format('d/m/Y').'",'.$diario->intId.']);</script>';
        echo '
            <div class="collapse col-md-12 col-xs-12 col-sm-12 sinPadding" id="collapseDiario_'.$diario->intId.'">
                <div class="row">
                    <div class="col-md-6 col-xs-12 col-sm-12 sinPadding">
                        <center><h2 style="margin: 0px;"><small><i class="fas fa-calendar-day"></i> Dia '.$intDiaContador.' - '.$datFechaDiario->format('D, d-M-Y').'</small></h2></center>
                    </div>
                    <div class="col-md-4 col-xs-6 col-sm-6 sinPadding">
                        <input onClick="$('."'#txtClonar_".$diario->intId."'".').val('.$diario->intId.'); setTimeout(function(){ $('."'#btnForm_diario_".$diario->intId."'".').click(); }, 500);" type="submit" name="submit" value="Clonar diario en siguiente día" class="btn btn-block btn-xs" style="padding-bottom: 0px;padding-top: 0px;"/>
                    </div>
                    <div class="col-md-2 col-xs-6 col-sm-6 sinPadding">
                        <button class="btn btn-block btn-xs" onClick="$('."'".'#collapseDiario_'.$diario->intId."'".').collapse('."'".'hide'."'".')" style="color:white !important;">
                            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Cerrar
                        </button>
                    </div>

                    <form action="/user/?action=tab_Paso_5&tab_Diario_'.$diario->intId.'" method="post">
                        <input type="hidden" name="intOp" value="5" />
                        <input type="hidden" name="intDiario" value="'.$diario->intId.'" />
                        <div class="col-xs-10 col-sm-10 col-md-10" style="padding-left: 0px;padding-right: 0px;">
                            <textarea name="txtNota" class="form-control" rows="2" placeholder="Nota del día...">'.$diario->strNota.'</textarea>
                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2" style="padding-left: 0px;padding-right: 0px;">
                            <button type="submit" name="submit" value="Guardar Nota" class="btn btn-block btn-xs" style="padding-bottom: 0px;padding-top: 0px;color: white;"><i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar Nota</button>
                        </div>
                    </form>


                    <div class="col-xs-12 col-sm-12 col-md-12">
                    <hr/>
                    </div>

                    <form action="/user/?action=tab_Paso_5&tab_Diario_'.$diario->intId.'" id="frm_diario_'.$diario->intId.'" method="post" class="" style="margin-bottom: 0px;">
                        <input type="hidden" name="intIDDETALLE" id="intIDDETALLE_'.$diario->intId.'" value="0" />
                        <input type="hidden" name="intOp" value="2" />
                        
                          <ul class="nav nav-tabs nav-justified" role="tablist">
                            <li role="presentation" class="active"><a href="#tab_food_journal_'.$diario->intId.'" aria-controls="tab_food_journal_'.$diario->intId.'" role="tab" data-toggle="tab"><i class="fas fa-utensils"></i><i class="fas fa-book"></i> Por Alimento</a></li>
                            <li role="presentation"><a href="#tab_food_journal_receta_'.$diario->intId.'" aria-controls="tab_food_journal_receta_'.$diario->intId.'" role="tab" data-toggle="tab"><i class="fas fa-list-ul"></i> Por Receta</a></li>
                          </ul>
                          <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="tab_food_journal_'.$diario->intId.'">

                                <div class="col-md-3 col-xs-6 col-sm-6 sinPadding" style="display: grid;">
                                    <label for="intDiarioDet_Tiempo"><i class="fa fa-clock-o" aria-hidden="true"></i> Tiempo <strong>*</strong></label>
                                    <select name="intDiarioDet_Tiempo" class="intDiarioDet_Tiempo" id="intDiarioDet_Tiempo_'.$diario->intId.'">';
                                    $bitPrimero = false;
                                    foreach ($listTiempos as $tiempo) { echo '<option value="'.$tiempo->intId.'"'.(!$bitPrimero?' selected ':'').'>'.$tiempo->strTiempo.'</option>';$bitPrimero = true; }
                                echo '</select>
                                </div>
                                <div class="col-md-3 col-xs-6 col-sm-6 sinPadding" style="display: grid;">
                                    <label for="intDiarioDet_Cantidad_'.$diario->intId.'"><span id="span_porcion_'.$diario->intId.'"><i class="fa fa-list-ol" aria-hidden="true"></i> Porcion</span> <strong>*</strong></label>
                                    <input type="number" id="intDiarioDet_Cantidad_'.$diario->intId.'" value="1" min="0" max="999" step="0.01" onChange="fnAlimentoSeleccionado('.$diario->intId.');" onkeyup="fnAlimentoSeleccionado('.$diario->intId.');" >
                                    <input type="hidden"id="intDiarioDet_Cantidad_hidden_'.$diario->intId.'" name="intDiarioDet_Cantidad" value="1" min="0" max="999" step="0.01">
                                </div>
                                <div class="col-md-4 col-xs-12 col-sm-12 sinPadding" style="display: grid;">
                                    <label for="intDiarioDet_Alimento"><i class="fa fa-cutlery" aria-hidden="true"></i> Alimento <strong>*</strong></label>
                                    <select name="intDiarioDet_Alimento" id="intDiarioDet_Alimento_'.$diario->intId.'" onChange="fnAlimentoSeleccionado('.$diario->intId.');">
                                        <option selected="true" disabled="disabled">Seleccionar el alimento</option>
                                    ';
                                    foreach ($listAlimentos as $alimento) {
                                        $strPCGVL = '';
                                        if($alimento->decProteina>0){
                                            $strPCGVL = ($alimento->decProteina + 0).'P';
                                        }
                                        if($alimento->decCarbohidratos>0){
                                            $strPCGVL = $strPCGVL.($strPCGVL!=''?', ':'').($alimento->decCarbohidratos + 0).'C';
                                        }
                                        if($alimento->decGrasa>0){
                                            $strPCGVL = $strPCGVL.($strPCGVL!=''?', ':'').($alimento->decGrasa + 0).'G';
                                        }
                                        if($alimento->decVegetales>0){
                                            $strPCGVL = $strPCGVL.($strPCGVL!=''?', ':'').($alimento->decVegetales + 0).'V';
                                        }
                                        if($alimento->decLibre>0){
                                            $strPCGVL = $strPCGVL.($strPCGVL!=''?', ':'').($alimento->decLibre + 0).'L';
                                        }
                                        echo '<option value="'.$alimento->intId.'">'.($alimento->decPorcion + 0).' '.$alimento->strUnidadMedida.' de '.$alimento->strAlimento.' ('.$strPCGVL.')</option>'; }
                                echo '</select>
                                </div>
                                <div class="col-md-2 col-xs-12 col-sm-12 sinPadding" style="display: grid;padding-left: 0px;">
                                    <input type="hidden" name="intDiarioDet_Enc" value="'.$diario->intId.'"/>
                                    <input type="hidden" name="intDiarioDet_Descripcion" value="..."/>';
                                    if ($itemEditar == null) {
                                        echo '<button onClick="fnDiarioGuardarAjax('.$diario->intId.');" type="button" value="'.($itemEditar == null? 'Agregar': 'Editar').'" class="btn btn-block btn-xs"id="btnForm_diario_'.$diario->intId.'" style="margin-top: 20px;color: white;height: 40px;"><i class="fa fa-plus" aria-hidden="true"></i>'.($itemEditar == null? 'Agregar': 'Editar').'</button>';
                                    }else{
                                        echo '<button type="submit" name="submit" value="'.($itemEditar == null? 'Agregar': 'Editar').'" class="btn btn-block btn-xs"id="btnForm_diario_'.$diario->intId.'" style="margin-top: 20px;color: white;height: 40px;"><i class="fa fa-plus" aria-hidden="true"></i>'.($itemEditar == null? 'Agregar': 'Editar').'</button>';
                                    }
                                echo '
                                </div>
                                <div style="display: none;">
                                    <input type="hidden" name="txtClonar" id="txtClonar_'.$diario->intId.'" value="0"/>                    
                                </div>
                            </div>
                        
                        <div role="tabpanel" class="tab-pane" id="tab_food_journal_receta_'.$diario->intId.'">';
                        fnViveMovimentoDiarioDetalleReceta_Core($diario->intId);
                        echo'</div>

                        </div>


                    </form>
                </div>
                <hr/>
            <div class="table-responsive">
        ';
        fnViveMovimentoDiarioDetalleTablaCore(strtok($_SERVER["REQUEST_URI"],'?'),$diario,$listTiempos);
        echo '</div></div>';
        $intDiaContador -= 1;
    }
?>
</div>
</div>
</div>

<script>
    function fnAlimentoSeleccionado(decDiario) {
        var cbAlimento = $('#intDiarioDet_Alimento_' + decDiario + ' option:selected').text();        
        $('#span_porcion_' + decDiario).html('Cantidad de ' + cbAlimento.split(' ')[1]);

        var decIngresado = ($('#intDiarioDet_Cantidad_' + decDiario).val() == '' ? 0 : $('#intDiarioDet_Cantidad_' + decDiario).val());
        var decBD = cbAlimento.split(' ')[0];
        decBD = decBD.replace(',','.');
        decIngresado = parseFloat(decIngresado);
        decBD = parseFloat(decBD);
        var decCalculo = parseFloat(decIngresado / decBD);
        $('#intDiarioDet_Cantidad_hidden_' + decDiario).val(decCalculo);
    }
    function fnCargarDiario(){
        var bitExiste = false;
        if ($('#txtFechaDiario').datepicker('getFormattedDate') == '')return;
        var strFechaSeleccionada = $('#txtFechaDiario').datepicker('getFormattedDate');
        for (var i = arrayFechas.length - 1; i >= 0; i--) {
            if (arrayFechas[i][0] == strFechaSeleccionada) {
                $('#collapseDiario_'+arrayFechas[i][1]).collapse('show');
                bitExiste = true;
                break;
            }
        }
        if (!bitExiste) {
            $('#span_fecha_no_encontrado').html(strFechaSeleccionada);
            $('#divFechaNoEncontrado').show();
        }else{            
            $('#divFechaNoEncontrado').hide();
        }
    }
    function fnDiarioGuardarAjax(decDiario) {
        if($('#intDiarioDet_Tiempo_' + decDiario).val() == null || $('#intDiarioDet_Cantidad_hidden_' + decDiario).val() == null || $('#intDiarioDet_Alimento_' + decDiario).val() == null || $('#intDiarioDet_Cantidad_' + decDiario).val() == 0 || $('#intDiarioDet_Cantidad_hidden_' + decDiario).val() == null){
            alert('Completar campos!');
            return;
        }
        jQuery.ajax({
            type : "get",
            // dataType : "json",
            url : '<?= admin_url( 'admin-ajax.php' ) ?>',
            data : {
                action: "fnViveMovimentoDiarioAgregar"
                ,intDiarioDet_Cantidad : $('#intDiarioDet_Cantidad_hidden_' + decDiario).val()
                ,intDiarioDet_Enc: decDiario
                ,intDiarioDet_Alimento: $('#intDiarioDet_Alimento_' + decDiario).val()
                ,intDiarioDet_Tiempo: $('#intDiarioDet_Tiempo_' + decDiario).val()
                ,txtClonar: 0
                ,intIDDETALLE: null
            },
            success: function(response) {
                var response = jQuery.parseJSON(response);
                if(response.type == "success") {
                    $('#intDiarioDet_Cantidad_hidden_' + decDiario).val(null);
                    $('#intDiarioDet_Cantidad_' + decDiario).val(null);
                    setTimeout(function () {
                        fnViveMovimentoDiarioDetalleTabla(decDiario);
                    }, 150);
                }else {
                   alert(response.mnj);
                }
            }
        });
    }
    function fnDiarioEliminarAjax(decDiario, intEliminar) {
        jQuery.ajax({
            type : "get",
            // dataType : "json",
            url : '<?= admin_url( 'admin-ajax.php' ) ?>',
            data : {
                action: "fnViveMovimentoDiarioEliminar"
                ,intEliminar : intEliminar
                ,intEncabezado: decDiario
            },
            success: function(response) {
                var response = jQuery.parseJSON(response);
                if(response.type == "success") {
                    setTimeout(function () {
                        fnViveMovimentoDiarioDetalleTabla(decDiario);
                    }, 150);
                }else {
                   alert(response.mnj);
                }
            }
        });
    }
    function fnViveMovimentoDiarioDetalleTabla(decDiario) {
        jQuery.ajax({
            type : "get",
            // dataType : "json",
            url : '<?= admin_url( 'admin-ajax.php' ) ?>',
            data : {
                action: "fnViveMovimentoDiarioDetalleTabla"
                ,intDiario: decDiario                
            },
            success: function(response) {
                $('#div_vivemovimento_tabla_diario_'+ decDiario).html(response);
            }
        });
    }
    function fnViveMovimentoDiarioDetalleReceta(decDiario) {
        $('#tab_food_journal_receta_'+decDiario).html('Cargando...');
        jQuery.ajax({
            type : "get",
            // dataType : "json",
            url : '<?= admin_url( 'admin-ajax.php' ) ?>',
            data : {
                action: "fnViveMovimentoDiarioDetalleReceta"
                ,intEncabezado: decDiario
            },
            success: function(response) {
                $('#tab_food_journal_receta_' + decDiario).html(response);
                setTimeout(function(){ $('select').select2(); }, 500);
            }
        });
    }
    $( document ).ready(function() {
        setTimeout(function(){
            $('.datepicker').datepicker({
                startDate: 0,
                clearBtn: true,
                language: "es",
                autoclose: true,
                todayHighlight: true,
                format: 'dd/mm/yyyy'
                //,startDate: '-1d'
            });
        }, 300);
        setTimeout(function(){
            $('#txtFechaDiario').datepicker('setDate', new Date());
            <?php
                if ($intDiarioDelDia > 0 ) {
                    echo "setTimeout(function(){ $('#collapseDiario_".$intDiarioDelDia."').collapse('show'); }, 1000);";
                }
            ?>
        }, 600);
    });
</script>


<?php
    if ($itemEditar != null) {
        $itemEditar = $itemEditar[0];
        echo '<script>';
        echo 'setTimeout(function(){
                $("#intIDDETALLE_'.$itemEditar->intDiario.'").val('.$itemEditar->intId.');
                $("#intDiarioDet_Tiempo_'.$itemEditar->intDiario.'").val("'.$itemEditar->intTiempo.'").trigger("change");                
                $("#intDiarioDet_Cantidad_'.$itemEditar->intDiario.'").val('.$itemEditar->devCantidad.');
                $("#intDiarioDet_Alimento_'.$itemEditar->intDiario.'").val("'.$itemEditar->intAlimentoPorcion.'").trigger("change");
        }, 2000);';
        echo '</script>';
    }else if($intDiarioSiguiente != null && $intDiarioSiguiente > 0 ){
        echo '<script>$("#intDiarioDet_Tiempo_'.$intDiarioSiguiente.'").val("'.$intTiempoSiguiente.'").trigger("change");</script>';
    }
}
?>

