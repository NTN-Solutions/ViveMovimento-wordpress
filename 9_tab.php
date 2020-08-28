<?php //MIS RECETAS
function fnViveMovimentoRecetaAgregar(){  
  global $wpdb;
  $strUsuario = fnViveMovimento_usuario();
  $itemRow = array(
    'strUsuario'  => $strUsuario,
    'datCreacion' => date('Y-m-d H:i:s'),
    'bitActivo'   => 1
  );
  $responseDiario = $wpdb->insert("wp_vivemov_recetas", $itemRow);  
  $result['type'] = 'success';
  $result['mnj'] = 'Listo, agregado!';
  echo json_encode($result);
}
function fnViveMovimentoRecetaEliminar(){  
  global $wpdb;
  $intReceta = intval($_GET['intReceta']);
  $wpdb->get_results("UPDATE wp_vivemov_recetas as D SET D.bitActivo = 0 WHERE D.intId = $intReceta; ");
  $result['type'] = 'success';
  $result['mnj'] = 'Listo, eliminado!';
  echo json_encode($result);
}
function fnViveMovimentoRecetaListado(){  
  global $wpdb;
  $strUsuario = fnViveMovimento_usuario();
  $list = $wpdb->get_results("SELECT * FROM wp_vivemov_recetas WHERE strUsuario = '$strUsuario' ORDER BY intId ASC;");    
  foreach ($list as $item) {
    if($item->bitActivo == 1){
      $item->detalle = fnViveMovimentoRecetaListadoDetalle($item->intId);      
    }
  }
  return $list;
}

function fnViveMovimentoRecetaListadoDetalle($intReceta){
  global $wpdb;
  $strUsuario = fnViveMovimento_usuario();
  $list = $wpdb->get_results("
    SELECT D.*, A.strAlimento, um.strUnidadMedida
    FROM wp_vivemov_recetas_detalle D
    INNER JOIN wp_vivemov_alimentos_porciones A ON A.intId = D.decAlimento
    INNER JOIN wp_vivemov_alimentos_unidad_medida um ON um.intId = A.intUnidadMedida
    WHERE D.intReceta = $intReceta AND D.bitActivo = 1
    ORDER BY A.strAlimento ASC;");
  return $list;
}
function fnViveMovimentoRecetaDetalleAgregar(){  
  global $wpdb;
  $intReceta = intval($_GET['intReceta']);
  $decCantidad = intval($_GET['decCantidad']);
  $decAlimento = intval($_GET['decAlimento']);
  $itemRow = array(
    'intReceta'  => $intReceta,
    'decCantidad'  => $decCantidad,
    'decAlimento'  => $decAlimento,
    'bitActivo'   => 1
  );
  $responseDiario = $wpdb->insert("wp_vivemov_recetas_detalle", $itemRow);  
  $result['type'] = 'success';
  $result['mnj'] = 'Listo, agregado!';
  echo json_encode($result);
}
function fnViveMovimentoRecetaDetalleEditar(){  
  global $wpdb;
  $intReceta = intval($_GET['intReceta']);
  $decCantidad = intval($_GET['decCantidad']);
  $wpdb->get_results("UPDATE wp_vivemov_recetas_detalle as D SET D.decCantidad = $decCantidad WHERE D.intId = $intReceta; ");
  $result['type'] = 'success';
  $result['mnj'] = 'Listo, actualizado!';
  echo json_encode($result);
}
function fnViveMovimentoRecetaDetalleEliminar(){
  global $wpdb;
  $intReceta = intval($_GET['intReceta']);
  $wpdb->get_results("UPDATE wp_vivemov_recetas_detalle as D SET D.bitActivo = 0 WHERE D.intId = $intReceta; ");
  $result['type'] = 'success';
  $result['mnj'] = 'Listo, eliminado!';
  echo json_encode($result);
}
function fnViveMovimentoRecetaJournalAgregar(){
  global $wpdb;
  $strUsuario = fnViveMovimento_usuario();
  $decDiario = intval($_GET['decDiario']);
  $intReceta = intval($_GET['intReceta']);
  $intTiempo = intval($_GET['intTiempo']);
  $wpdb->get_results("
    INSERT INTO wp_vivemov_users_diario_detalle(intDiario,strUsuario,intTiempo,intAlimentoPorcion,devCantidad,strDescripcion,intProteinas,intCarbohidratos,intGrasas,intVegetales,intLibres,datModificado)
    SELECT $decDiario
    ,'$strUsuario'
    ,$intTiempo
    ,RD.decAlimento
    ,(RD.decCantidad / ap.decPorcion)
    ,'...'
    ,(RD.decCantidad / ap.decPorcion) * AP.decProteina
    ,(RD.decCantidad / ap.decPorcion) * AP.decCarbohidratos
    ,(RD.decCantidad / ap.decPorcion) * AP.decGrasa
    ,(RD.decCantidad / ap.decPorcion) * AP.decVegetales
    ,(RD.decCantidad / ap.decPorcion) * AP.decLibre
    ,now()
    FROM wp_vivemov_recetas_detalle AS RD
    INNER JOIN wp_vivemov_alimentos_porciones AS AP on AP.intId = RD.decAlimento
    WHERE RD.intReceta = $intReceta AND RD.bitActivo = 1 AND AP.bitActivo = 1
  ");

  fnDiarioDetalleCalcular_P_CH_G_V($wpdb,$strUsuario,$decDiario);
  $result['type'] = 'success';
  $result['mnj'] = 'Listo, agregado!';
  echo json_encode($result);
}

function fnTab_9(){
  global $wpdb;
  $listado = fnViveMovimentoRecetaListado();
  $listAlimentos = $wpdb->get_results("SELECT ap.*, um.strUnidadMedida FROM wp_vivemov_alimentos_porciones ap INNER JOIN wp_vivemov_alimentos_unidad_medida um ON um.intId = ap.intUnidadMedida WHERE ap.bitActivo=1 ORDER BY ap.strAlimento ASC ");

  ?>
</br>
<div class="col-md-4 col-xs-12 col-sm-12" style="padding-left: 2px;padding-right: 2px;">
  <div class="panel panel-primary">
    <div class="panel-heading">
      <h3 class="panel-title">Nueva Receta</h3>
    </div>
    <div class="panel-body">
      <button class="btn btn-primary btn-xs btn-block" onclick="fnViveMovimento_Receta_agregar();">
        <i class="fa fa-plus" aria-hidden="true"></i> Agregar
      </button>
    </div>
  </div>
</div>
<?php
$intCursor = 1;
$intCursorRow = 1;
foreach ($listado as $item) {
  if($item->bitActivo == 0){ $intCursor += 1; continue; }
  ?>
  <div class="col-md-4 col-xs-12 col-sm-12" style="padding-left: 2px;padding-right: 2px;">
    <div class="panel panel-info">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list-ul" aria-hidden="true"></i> Receta No. <?= $intCursor ?> 
        <button class="btn btn-success btn-xs" onclick="fnViveMovimento_Receta_Detalle_agregar(<?= $item->intId ?>);" style="float: right;">
          <i class="fa fa-plus" aria-hidden="true"></i> Alimento
        </button>   
        <button class="btn btn-warning btn-xs" onclick="fnViveMovimento_Receta_eliminar(<?= $item->intId ?>);" style="float: right;">
          <i class="fa fa-trash" aria-hidden="true"></i> Receta
        </button>          
      </h3>
    </div>
    <div class="panel-body" style="padding-left: 2px;padding-right: 2px;">
        <div class="row">
          <div class="form-group col-md-4 col-xs-4 col-sm-4">
            <label for="txtRecetaCantidad_<?= $item->intId ?>">Porción:</label>
            <input type="number" class="form-control" id="txtRecetaCantidad_<?= $item->intId ?>" placeholder="1" value="1" min="0" max="999" step="0.01">
          </div>
          <div class="form-group col-md-8 col-xs-8 col-sm-8" style="padding-left: 0px;">
            <label for="cbRecetaAlimento_<?= $item->intId ?>">Alimento:</label>
              <select id="cbRecetaAlimento_<?= $item->intId ?>">
                <option selected="true" disabled="disabled">Seleccionar alimento</option>
                <?php foreach ($listAlimentos as $alimento) {
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
                  $strAlimento = (($alimento->decPorcion+0).' '.$alimento->strUnidadMedida.' de '.$alimento->strAlimento.' ('.$strPCGVL.')');
                  ?>
                  <option value="<?= $alimento->intId ?>"><?= $strAlimento ?></option>
                <?php } ?>
              </select>
          </div>
        </div>

<div class="table-responsive">
  <table class="table table-condensed table-striped">
    <thead>
        <tr>
          <th style="width: 15px;"></th>
          <th style="width: 80px;">Porción</th>
          <th style="width: 30px;"></th>
          <th>Alimento</th>
          <th style="width: 15px;"></th>
        </tr>
      </thead>
<?php foreach ($item->detalle as $itemDetalle) { ?>
  <tr>
    <td>
        <button class="btn btn-info btn-xs" onclick="fnViveMovimento_Receta_Detalle_editar(<?= $itemDetalle->intId ?>);">
          <i class="fa fa-refresh" aria-hidden="true"></i>
        </button>   
    </td>
    <td>
      <input type="number" class="form-control input-sm" id="txtRecetaCantidad_editar_<?= $itemDetalle->intId ?>" placeholder="1" value="<?= $itemDetalle->decCantidad ?>" min="0" max="999" step="0.01" style="padding: 1px;">
    </td>
    <td style="padding-left: 0px;padding-right: 0px;"><?= $itemDetalle->strUnidadMedida ?></td>
    <td><?= $itemDetalle->strAlimento ?></td>
    <td>
       <button class="btn btn-warning btn-xs" onclick="fnViveMovimento_Receta_Detalle_eliminar(<?= $itemDetalle->intId ?>);">
          <i class="fa fa-trash" aria-hidden="true"></i>
        </button>
    </td>
  </tr>
<?php } ?>
  </table>
</div>


    </div>
  </div>
</div>
<?php
$intCursor += 1;
$intCursorRow += 1;
if ($intCursorRow % 3 == 0) {
  echo '<div class="col-md-12 col-xs-12 col-sm-12">.</div>';
}
} ?>
<script>
  function fnViveMovimento_Receta_agregar(){
    jQuery.ajax({
      type : "get",
      url : '<?= admin_url( 'admin-ajax.php' ) ?>',
      data : {
        action: "fnViveMovimentoRecetaAgregar"
      },
      success: function(response) {
        response = response.replace('"}0','"}');
        var response = jQuery.parseJSON(response);
        if(response.type == "success") {
          location = '/user/?action=tab_Paso_9';
        }else {
         alert(response.mnj);
       }
     }
   });
  }
  function fnViveMovimento_Receta_eliminar(intReceta){
    jQuery.ajax({
      type : "get",
      url : '<?= admin_url( 'admin-ajax.php' ) ?>',
      data : {
        action: "fnViveMovimentoRecetaEliminar"
        ,intReceta: intReceta
      },
      success: function(response) {
        response = response.replace('"}0','"}');
        var response = jQuery.parseJSON(response);
        if(response.type == "success") {
          location = '/user/?action=tab_Paso_9';
        }else {
         alert(response.mnj);
       }
     }
   });
  }
  function fnViveMovimento_Receta_Detalle_agregar(intReceta){
    if($('#txtRecetaCantidad_' + intReceta).val() == null || $('#txtRecetaCantidad_' + intReceta).val() == ''){
      return;
    }else if($('#cbRecetaAlimento_' + intReceta).val() == null || $('#cbRecetaAlimento_' + intReceta).val() == ''){
      return;
    }
    var intCantidad = parseFloat($('#txtRecetaCantidad_' + intReceta).val());
    var decAlimento = parseFloat($('#cbRecetaAlimento_' + intReceta).val());
    jQuery.ajax({
      type : "get",
      url : '<?= admin_url( 'admin-ajax.php' ) ?>',
      data : {
        action: "fnViveMovimentoRecetaDetalleAgregar"
        ,intReceta: intReceta
        ,decCantidad: intCantidad
        ,decAlimento: decAlimento
      },
      success: function(response) {
        response = response.replace('"}0','"}');
        var response = jQuery.parseJSON(response);
        if(response.type == "success") {
          location = '/user/?action=tab_Paso_9';
        }else {
         alert(response.mnj);
       }
     }
   });
  }
  function fnViveMovimento_Receta_Detalle_editar(intReceta){
    if($('#txtRecetaCantidad_editar_' + intReceta).val() == null || $('#txtRecetaCantidad_editar_' + intReceta).val() == ''){
      return;
    }
    var intCantidad = parseFloat($('#txtRecetaCantidad_editar_' + intReceta).val());
    jQuery.ajax({
      type : "get",
      url : '<?= admin_url( 'admin-ajax.php' ) ?>',
      data : {
        action: "fnViveMovimentoRecetaDetalleEditar"
        ,intReceta: intReceta
        ,decCantidad: intCantidad
      },
      success: function(response) {
        response = response.replace('"}0','"}');
        var response = jQuery.parseJSON(response);
        if(response.type == "success") {
          // location = '/user/?action=tab_Paso_9';
        }else {
         alert(response.mnj);
       }
     }
   });
  }
  function fnViveMovimento_Receta_Detalle_eliminar(intReceta){
    jQuery.ajax({
      type : "get",
      url : '<?= admin_url( 'admin-ajax.php' ) ?>',
      data : {
        action: "fnViveMovimentoRecetaDetalleEliminar"
        ,intReceta: intReceta
      },
      success: function(response) {
        response = response.replace('"}0','"}');
        var response = jQuery.parseJSON(response);
        if(response.type == "success") {
          $('#txtRecetaCantidad_editar_' + intReceta).parent().parent().remove();
        }else {
         alert(response.mnj);
       }
     }
   });
  }
  function fnViveMovimento_Receta_Journal_agregar(decDiario,intReceta){
    jQuery.ajax({
      type : "get",
      url : '<?= admin_url( 'admin-ajax.php' ) ?>',
      data : {
        action: "fnViveMovimentoRecetaJournalAgregar"
        ,decDiario: decDiario
        ,intReceta: intReceta
        ,intTiempo: $('#intDiarioDet_Receta_Tiempo_' + decDiario).val()
      },
      success: function(response) {
        response = response.replace('"}0','"}');
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
</script>
<?php } ?>