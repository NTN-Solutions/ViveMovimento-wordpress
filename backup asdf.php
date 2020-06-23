<?php

function fnListadoDiario(){
    global $wpdb, $strUsuario;
    $strUsuario = wp_get_current_user()->user_login;
    $list = $wpdb->get_results("SELECT intId,datFecha, SUM(intProteinas) intProteinas,SUM(intCarbohidratos) intCarbohidratos,SUM(intGrasas) intGrasas,SUM(intVegetales) intVegetales,SUM(intLibres) intLibres FROM wp_vivemov_users_diario WHERE strUsuario = '$strUsuario' GROUP BY intId,datFecha ORDER BY datFecha DESC");
    return $list;
}
function fnDiario_Tiempos(){
    global $wpdb;
    $listado = $wpdb->get_results("SELECT * FROM wp_vivemov_alimentos_tiempo T WHERE T.bitActivo = 1 ORDER BY decOrden ASC");
    return $listado;
}

function fnDiario_Agregar(){
    global $wpdb, $strUsuario;
    $datSiguiente = null;

    $datMaximo = $wpdb->get_results("SELECT DATE_ADD(MAX(datFecha), INTERVAL 1 DAY) datFecha FROM wp_vivemov_users_diario WHERE strUsuario = '$strUsuario';");
    if ($datMaximo == null) {
        $datSiguiente = date('Y-m-d');
    }else if($datMaximo[0]->datFecha > date('Y-m-d') == 1){ //si la ultima fecha mas 1 dia, es mayor igual a hoy, continuamos para delante
        $datSiguiente = $datMaximo[0]->datFecha;
    }else{ //si la ultima fecha + 1 dia no es mayor a hoy, entonces empezamos de nuevo desde hoy
        $datSiguiente = date('Y-m-d');
    }

    $strUsuario = wp_get_current_user()->user_login;
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
            } else {
                echo fnMensaje(2,'Inconvenientes, no guardado!');
            }
        // }
    }else{
        $_SESSION["intFormulario"] = 1;
    }
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

    $strUsuario = wp_get_current_user()->user_login;
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
                echo fnMensaje(1,'Listo, dia('.$datSiguiente.') agregado!');
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
        FROM wp_vivemov_alimentos_tiempo T
        INNER JOIN wp_vivemov_users_diario_detalle DD ON DD.intDiario = $decDiario AND DD.intTiempo = T.intId  
        INNER JOIN wp_vivemov_alimentos_porciones AP ON AP.intId = DD.intAlimentoPorcion
        WHERE T.bitActivo = 1 AND T.intId = $intTiempo ORDER BY AP.strAlimento ASC");
    return $listado;
}
function fnDiario_eliminar($intDetalle){
    global $wpdb;
    $wpdb->delete( 'wp_vivemov_users_diario_detalle', array( 'intId' => $intDetalle ) );
}
function fnDiario_AgregarDetalle(){
    global $reg_errors, $strUsuario;
    global $intDiarioDet_Cantidad, $detEncabezado, $detAlimento, $detTiempo, $txtClonar;

    if ($txtClonar != null && $txtClonar != '' && $txtClonar > 0) {
        fnDiario_clonar();
        return;
    }

    $strUsuario = wp_get_current_user()->user_login;
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
    if($_SESSION["intFormulario"] == 1){
        $_SESSION["intFormulario"] = 0;
        if ( 1 > count( $reg_errors->get_error_messages() ) ) {
            global $wpdb;
            $responseDiario = $wpdb->insert("wp_vivemov_users_diario_detalle", $itemRow);
            if($responseDiario) {
                echo fnMensaje(1,'Listo, agregado!');
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
                    WHERE D.strUsuario = '$strUsuario';
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
            } else {
                echo fnMensaje(2,'Inconvenientes, no guardado!');
            }
        }
    }else{
        $_SESSION["intFormulario"] = 1;
    }
}
function fnDiario_Detalle_Validar($decCantidad,$decAlimento,$decTiempo){
    global $reg_errors;
    $reg_errors = new WP_Error;
    if ( empty( $decCantidad ) ) {
        $reg_errors->add('field', 'Ingresar cantidad');
    }
    if ( empty( $decTiempo ) ) {
        $reg_errors->add('field', 'Ingresar tiempo');
    }
    if ( empty( $decAlimento ) ) {
        $reg_errors->add('field', 'Ingresar alimento');
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
    global $wpdb, $strUsuario;
    global $decProteinas,$decCarbo,$decGrasas;
    global $decPorcionDia;
    $decPorcionDia = array(0,0,0,0);
    $decPorcionDia[0] = 0;
    $decPorcionDia[1] = 0;
    $decPorcionDia[2] = 0;
    $strUsuario = wp_get_current_user()->user_login;

    if (isset($_GET['action']) && $_GET['action'] == 'tab_Paso_5' && isset($_POST['intOp']) && $_POST['intOp'] != null && $_POST['intOp'] == '1') {    
        fnDiario_Agregar();
    } else if (isset($_GET['action']) && $_GET['action'] == 'tab_Paso_5' && isset($_POST['intDiarioDet_Enc']) && $_POST['intDiarioDet_Enc'] != null  && isset($_POST['intOp']) && $_POST['intOp'] != null && $_POST['intOp'] == '2') {
        fnDiario_Detalle_Validar($_POST['intDiarioDet_Cantidad'],$_POST['intDiarioDet_Alimento'],$_POST['intDiarioDet_Tiempo']);
        global $intDiarioDet_Cantidad, $detEncabezado, $detAlimento, $detTiempo, $txtClonar;
        $intDiarioDet_Cantidad = $_POST['intDiarioDet_Cantidad'];
        $detEncabezado = $_POST['intDiarioDet_Enc'];
        $detAlimento = $_POST['intDiarioDet_Alimento'];
        $detTiempo = $_POST['intDiarioDet_Tiempo'];
        $txtClonar = $_POST['txtClonar'];
        fnDiario_AgregarDetalle();
    }else if (isset($_GET['action']) && $_GET['action'] == 'tab_Paso_5' && isset($_POST['intOp']) && $_POST['intOp'] != null && $_POST['intOp'] == '3') {
        fnDiario_eliminar(intval($_POST['intEliminar']));
    }

    $listadoDiario = fnListadoDiario();
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
?>

  <div class="col-md-12 col-xs-12 col-sm-12">
    <center><h2><small>Porciones del Dia</small></h2></center>
    <div class="table-responsive">
      <table class="table table-striped table-condensed">
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

<br/>

<script>
    var arrayFechas = [];
</script>

<div class="col-md-12 col-xs-12 col-sm-12">
<div class="panel panel-default">
  <div class="panel-heading">
    <div class="row">
        <div class="col-md-3 col-xs-3 col-sm-3">
            <h3 class="panel-title" style="margin-top: 15px;"><i class="fas fa-calendar-day"></i> Food Journal</h3>
        </div>
        <div class="col-md-3 col-xs-3 col-sm-3">
            <label><i class="fas fa-calendar-day"></i> Calendario:</label>
            <div class="input-group date">
              <input id="txtFechaDiario" type="text" class="form-control datepicker"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
            </div>
        </div>
        <div class="col-md-6 col-xs-6 col-sm-6" style="padding: 0px;">
            <form action="<?php echo strtok($_SERVER["REQUEST_URI"],'?') ?>?action=tab_Paso_5" method="post">
              <div class="col-md-12 col-xs-12 col-sm-12" style="padding: 0px;">
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
                          <i class="fas fa-plus"></i> <?php echo ($intDia > 0 ? ' Agregar Dia '.$intDia : 'Dia actual ya fue agregado'); ?>
                        </button>
                  </div>
                  <div class="btn-group" role="group" style="display: none;">
                    <button type="button" class="btn" onclick="fnTabNav(6);" style="color: white;">
                      Siguiente <i class="fas fa-angle-right"></i>
                    </button>
                  </div>
                </div>
              </div>
            </form>
        </div>
    </div>
  </div>
  <div class="panel-body">
<?php
    $intContadorFolder = 1;$bitFolderAbierto = false;
    $intDiaContador = count($listadoDiario);
    echo '<div class="row">';    
    foreach ($listadoDiario as $diario) {
        $datFechaDiario = new DateTime($diario->datFecha);
        if ($intContadorFolder >= (7*4) && $bitFolderAbierto == false) {
            $bitFolderAbierto = true;
            echo '<div class="col-md-12 col-xs-12 col-sm-12" style="display: none;"><br/>';
            echo '  <a class="btn btn-link badge" role="button" data-toggle="collapse" href="#collapseFOLDER" aria-expanded="false" aria-controls="collapseFOLDER">';
            echo '      <i class="fas fa-calendar-day"></i> Folder de dias anteriores';
            echo '  </a>';
            echo '</div>';
            echo '<div class="collapse col-md-12 col-xs-12 col-sm-12" id="collapseFOLDER">';
        }
        echo '<div class="col-md-3 col-xs-12 col-sm-12" style="display: none;">';
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
    foreach ($listadoDiario as $diario) {
        $datFechaDiario = new DateTime($diario->datFecha);
        echo '<script>arrayFechas.push(["'.$datFechaDiario->format('d/m/Y').'",'.$diario->intId.']);</script>';
        echo '
            <div class="collapse col-md-12 col-xs-12 col-sm-12" id="collapseDiario_'.$diario->intId.'">
            <div class="col-md-6 col-xs-6 col-sm-6">
                <center><h2 style="margin: 0px;"><small><i class="fas fa-calendar-day"></i> Dia '.$intDiaContador.' - '.$datFechaDiario->format('D, d-M-Y').'</small></h2></center>
            </div>
            <div class="col-md-4 col-xs-4 col-sm-4">
                <input onClick="$('."'#txtClonar_".$diario->intId."'".').val('.$diario->intId.');" type="submit" name="submit" value="Clonar diario en siguiente día" class="btn btn-block btn-xs" style="padding-bottom: 0px;padding-top: 0px;"/>
            </div>
            <div class="col-md-2 col-xs-2 col-sm-2">
                <button class="btn btn-block btn-xs" onClick="$('."'".'#collapseDiario_'.$diario->intId."'".').collapse('."'".'hide'."'".')" style="color:white !important;">
                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Cerrar
                </button>
            </div>

            <form action="'.strtok($_SERVER["REQUEST_URI"],'?').'?action=tab_Paso_5&tab_Diario_'.$diario->intId.'" method="post" class="row" style="margin-bottom: 0px;">
                <input type="hidden" name="intOp" value="2" />
                <div class="col-md-3 col-xs-6 col-sm-6" style="display: grid;">
                    <label for="intDiarioDet_Tiempo">Tiempo <strong>*</strong></label>
                    <select name="intDiarioDet_Tiempo" id="intDiarioDet_Tiempo">';
                    foreach ($listTiempos as $tiempo) { echo '<option value="'.$tiempo->intId.'">'.$tiempo->strTiempo.'</option>'; }
                echo '</select>
                </div>
                <div class="col-md-3 col-xs-6 col-sm-6" style="display: grid;">
                    <label for="intDiarioDet_Cantidad">Porcion <strong>*</strong></label>
                    <input type="number" name="intDiarioDet_Cantidad" value="1" min="0" max="99" step="0.01">
                </div>
                <div class="col-md-4 col-xs-6 col-sm-6" style="display: grid;">
                    <label for="intDiarioDet_Alimento">Alimento <strong>*</strong></label>
                    <select name="intDiarioDet_Alimento" id="intDiarioDet_Alimento">';
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
                <div class="col-md-2 col-xs-6 col-sm-6" style="display: grid;padding-left: 0px;">
                    <input type="hidden" name="intDiarioDet_Enc" value="'.$diario->intId.'"/>
                    <input type="hidden" name="intDiarioDet_Descripcion" value="..."/>
                    <input type="submit" name="submit" value="Agregar" class="btn btn-block btn-xs" style="margin-top: 20px;"/>
                </div>
                <div style="display: none;">
                    <input type="hidden" name="txtClonar" name="txtClonar" id="txtClonar_'.$diario->intId.'" value="0"/>                    
                </div>
            </form>
        <table id="tblDiario_'.$diario->intId.'" class="tblDiario">';
        foreach ($listTiempos as $tiempo) {
            $listDetalle = fnDiario_Detalle($diario->intId,$tiempo->intId);
            if($tiempo->bitPrincipal == 0 && count($listDetalle) == 0){continue;}
            echo '<tr>
                <td class="blanco" style="font-weight: bolder;">'.$tiempo->strTiempo.'</td>
                <td class="amarillo" style="font-weight: bolder;">Proteina</td>
                <td class="naranja" style="font-weight: bolder;">Carbohidrato</td>
                <td class="celeste" style="font-weight: bolder;">Grasa</td>
                <td class="verde" style="font-weight: bolder;">Vegetales</td>
                <td class="morado" style="font-weight: bolder;">Libre</td>
              </tr>';
              foreach ($listDetalle as $det) {
                echo '<tr>
                        <td>
                            <form action="'.strtok($_SERVER["REQUEST_URI"],'?').'?action=tab_Paso_5&tab_Diario_'.$diario->intId.'" method="post" style="display: inline-block;">
                                <input type="hidden" name="intOp" value="3" />
                                <input type="hidden" name="intEliminar" value="'.$det->intId.'" />
                                <button type="submit" class="btn btn-link badge" role="button" href="#"><i class="fas fa-trash-alt"></i></button>
                            </form>
                            '.$det->strAlimento.'
                        </td>
                        <td class="amarillo">'.$det->intProteinas.'</td>
                        <td class="naranja">'.$det->intCarbohidratos.'</td>
                        <td class="celeste">'.$det->intGrasas.'</td>
                        <td class="verde">'.$det->intVegetales.'</td>
                        <td class="morado">'.$det->intLibres.'</td>
                      </tr>';
              }
        }
        echo '<tr>
            <td class="rojo" rowspan="2" style="font-weight: bolder;">Total</td>
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
        $intDiaContador -= 1;
    }
?>
</div>
</div>
</div>

<script>
    function fnCargarDiario(){
        if ($('#txtFechaDiario').datepicker('getFormattedDate') == '')return;
        var strFechaSeleccionada = $('#txtFechaDiario').datepicker('getFormattedDate');
        for (var i = arrayFechas.length - 1; i >= 0; i--) {
            if (arrayFechas[i][0] == strFechaSeleccionada) {
                $('#collapseDiario_'+arrayFechas[i][1]).collapse('show');
            }
        }
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
        }, 500);
        setTimeout(function(){
            $('#txtFechaDiario').datepicker('setDate', new Date());
        }, 1000);
    });
</script>


<?php
}
?>

