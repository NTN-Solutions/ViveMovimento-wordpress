<?php //MIS RECETAS
function fnViveMovimentoRecetaAgregar(){  
  global $wpdb;
  $strNombre = $_GET['strNombre'];
  $strUsuario = fnViveMovimento_usuario();
  $itemRow = array(
    'strUsuario'  => $strUsuario,
    'strNombre'  => $strNombre,
    'datCreacion' => date('Y-m-d H:i:s'),
    'bitActivo'   => 1
  );
  $responseDiario = fn_insert("wp_vivemov_recetas", $itemRow);  
  $result['type'] = 'success';
  $result['mnj'] = 'Listo, agregado!';
  echo json_encode($result);
  exit();
}
function fnViveMovimentoRecetaEditar(){  
  global $wpdb;
  $intReceta = intval($_GET['intReceta']);
  $strNombre = $_GET['strNombre'];
  get_results("UPDATE wp_vivemov_recetas as D SET D.strNombre = '$strNombre' WHERE D.intId = $intReceta; ");
  $result['type'] = 'success';
  $result['mnj'] = 'Listo, actualizado!';
  echo json_encode($result);
  exit();
}
function fnViveMovimentoRecetaClonar(){  
  global $wpdb;
  $strUsuario = fnViveMovimento_usuario();
  $intClonar = intval($_GET['intClonar']);
  $insertPadre = get_results("
      INSERT INTO wp_vivemov_recetas(strUsuario,datCreacion,bitActivo,strNombre)
      SELECT '$strUsuario',now(),1,strNombre
      FROM wp_vivemov_recetas
      WHERE intId = $intClonar;
      ");
  $ULTIMO = get_results("SELECT intId, strUsuario FROM wp_vivemov_recetas WHERE strUsuario = '$strUsuario' ORDER BY intId DESC LIMIT 1");
  $decReceta = $ULTIMO[0]['intId'];
  get_results("
      INSERT INTO wp_vivemov_recetas_detalle(intReceta,decCantidad,decAlimento,bitActivo)
      SELECT $decReceta,decCantidad,decAlimento,bitActivo
      FROM wp_vivemov_recetas_detalle
      WHERE intReceta = $intClonar AND bitActivo = 1;
  ");
  $result['type'] = 'success';
  $result['mnj'] = 'Listo, agregado!';
  echo json_encode($result);
  exit();
}
function fnViveMovimentoRecetaEliminar(){  
  global $wpdb;
  $intReceta = intval($_GET['intReceta']);
  get_results("UPDATE wp_vivemov_recetas as D SET D.bitActivo = 0 WHERE D.intId = $intReceta; ");
  $result['type'] = 'success';
  $result['mnj'] = 'Listo, eliminado!';
  echo json_encode($result);
  exit();
}
function fnViveMovimentoRecetaListado(){  
  global $wpdb,$intReceta;
  $strUsuario = fnViveMovimento_usuario();
  if ($intReceta == 0) {
    $list = get_results("SELECT * FROM wp_vivemov_recetas WHERE strUsuario = '$strUsuario' ORDER BY strNombre ASC;");    
  }else{
    $list = get_results("SELECT * FROM wp_vivemov_recetas WHERE strUsuario = '$strUsuario' AND intId = $intReceta ORDER BY strNombre ASC;");
  }
  foreach ($list as $item) {

  // echo '-------------=';
  // print_r($item);
  // echo '=-------------';
  
    if($item['bitActivo'] == 1){
      $item['detalle'] = fnViveMovimentoRecetaListadoDetalle($item['intId']);      
      // echo '<br/>==========>';
      // echo '<br/>';
      // print_r($item['detalle']);
      // echo '<br/>==========>';
    }
  }
  return $list;
}
function fnViveMovimentoRecetaListadoAdmin(){  
  global $wpdb;
  $list = get_results("SELECT * FROM wp_vivemov_recetas WHERE strUsuario IN('anamoralescpt','amms24') ORDER BY strNombre ASC;");
  return $list;
}

function fnViveMovimentoRecetaListadoDetalle($intReceta){
  global $wpdb;
  $strUsuario = fnViveMovimento_usuario();
  $list = get_results("
    SELECT D.*, A.strAlimento, um.strUnidadMedida
    FROM wp_vivemov_recetas_detalle D
    INNER JOIN wp_vivemov_alimentos_porciones A ON A.intId = D.decAlimento
    INNER JOIN wp_vivemov_alimentos_unidad_medida um ON um.intId = A.intUnidadMedida
    WHERE D.intReceta = $intReceta AND D.bitActivo = 1
    ORDER BY A.strAlimento ASC;");
  
  // echo '-------=';
  // print_r($list);
  // echo '=-------';
  
  return $list;
}
function fnViveMovimentoRecetaDetalleAgregar(){  
  global $wpdb;
  $intReceta = intval($_GET['intReceta']);
  $decCantidad = floatval($_GET['decCantidad']);
  $decAlimento = intval($_GET['decAlimento']);
  $itemRow = array(
    'intReceta'  => $intReceta,
    'decCantidad'  => $decCantidad,
    'decAlimento'  => $decAlimento,
    'bitActivo'   => 1
  );
  $responseDiario = fn_insert("wp_vivemov_recetas_detalle", $itemRow);  
  $result['type'] = 'success';
  $result['mnj'] = 'Listo, agregado!';
  echo '================> fnViveMovimentoRecetaDetalleAgregar 111';
  echo json_encode($result);
  exit();
}
function fnViveMovimentoRecetaDetalleEditar(){  
  global $wpdb;
  $intReceta = intval($_GET['intReceta']);
  $decCantidad = floatval($_GET['decCantidad']);
  get_results("UPDATE wp_vivemov_recetas_detalle as D SET D.decCantidad = $decCantidad WHERE D.intId = $intReceta; ");
  $result['type'] = 'success';
  $result['mnj'] = 'Listo, actualizado!';
  echo json_encode($result);
  exit();
}
function fnViveMovimentoRecetaDetalleEliminar(){
  global $wpdb;
  $intReceta = intval($_GET['intReceta']);
  get_results("UPDATE wp_vivemov_recetas_detalle as D SET D.bitActivo = 0 WHERE D.intId = $intReceta; ");
  $result['type'] = 'success';
  $result['mnj'] = 'Listo, eliminado!';
  echo json_encode($result);
  exit();
}
function fnViveMovimentoRecetaJournalAgregar(){
  global $wpdb;
  $strUsuario = fnViveMovimento_usuario();
  $decDiario = intval($_GET['decDiario']);
  $intReceta = intval($_GET['intReceta']);
  $intTiempo = intval($_GET['intTiempo']);
  get_results("
    INSERT INTO wp_vivemov_users_diario_detalle(intDiario,strUsuario,intTiempo,intAlimentoPorcion,devCantidad,strDescripcion,intProteinas,intCarbohidratos,intGrasas,intVegetales,intLibres,datModificado)
    SELECT $decDiario
    ,'$strUsuario'
    ,$intTiempo
    ,RD.decAlimento
    ,(RD.decCantidad / AP.decPorcion)
    ,'...'
    ,(RD.decCantidad / AP.decPorcion) * AP.decProteina
    ,(RD.decCantidad / AP.decPorcion) * AP.decCarbohidratos
    ,(RD.decCantidad / AP.decPorcion) * AP.decGrasa
    ,(RD.decCantidad / AP.decPorcion) * AP.decVegetales
    ,(RD.decCantidad / AP.decPorcion) * AP.decLibre
    ,now()
    FROM wp_vivemov_recetas_detalle AS RD
    INNER JOIN wp_vivemov_alimentos_porciones AS AP on AP.intId = RD.decAlimento
    WHERE RD.intReceta = $intReceta AND RD.bitActivo = 1 AND AP.bitActivo = 1
  ");

  fnDiarioDetalleCalcular_P_CH_G_V($wpdb,$strUsuario,$decDiario);
  $result['type'] = 'success';
  $result['mnj'] = 'Listo, agregado!';
  echo json_encode($result);
  exit();
}
function fnViveMovimentoRecetaListadoCore(){
  $intReceta = intval($_GET['intReceta']);
  fnTab_9_core($intReceta);
  exit();
}
function fnTab_9(){
  echo '<div id="divRecetasCore_0">';
  fnTab_9_core(0);
  echo '</div>';
}
function fnTab_9_core($intR){
  // echo 'fnTab_9_coreasdfasdf asdfasdfasdf';
  global $wpdb;
  global $intReceta;
  $intReceta = $intR;
  $listado = fnViveMovimentoRecetaListado($intReceta);
  $listadoAdmin = fnViveMovimentoRecetaListadoAdmin();
  $listAlimentos = get_results("SELECT ap.*, um.strUnidadMedida FROM wp_vivemov_alimentos_porciones ap INNER JOIN wp_vivemov_alimentos_unidad_medida um ON um.intId = ap.intUnidadMedida WHERE ap.bitActivo=1 ORDER BY ap.strAlimento ASC ");
  if ($intReceta == 0) { ?>
</br>
<div class="col-md-4 col-xs-12 col-sm-12" style="padding-left: 2px;padding-right: 2px;">
  <div class="panel panel-primary">
    <div class="panel-heading">
      <h3 class="panel-title">Nueva Receta</h3>
    </div>
    <div class="panel-body">
        <label for="cbRecetaAlimento_configurada">Receta Personalizada:</label>
      <input type="text" class="form-control input-sm" id="txtRecetaStrNombre_0" placeholder="Nombre..." maxlength="100" style="padding: 1px;">
      <button class="btn btn-primary btn-xs btn-block" onclick="fnViveMovimento_Receta_agregar(0);">
        <i class="fa fa-plus" aria-hidden="true"></i> Agregar Personalizada
      </button>
      <hr>

      <div class="form-group col-md-12 col-xs-12 col-sm-12" style="padding-left: 0px;margin: 0px;">
        <label for="cbRecetaAlimento_configurada">Receta Movimento:</label>
          <select id="cbRecetaAlimento_configurada">
            <option selected="true" disabled="disabled">Seleccionar</option>
            <?php foreach ($listadoAdmin as $receta) { ?>
              <option value="<?= $receta['intId'] ?>"><?= $receta['strNombre'] ?></option>
            <?php } ?>
          </select>
      </div>
      <button class="btn btn-primary btn-xs btn-block" onclick="fnViveMovimento_Receta_clonar();">
        <i class="fa fa-plus" aria-hidden="true"></i> Agregar Receta Movimento
      </button>
    </div>
  </div>
</div>
<?php
  }


$intCursor = 1;
$intCursorRow = 1;
foreach ($listado as $item) {
  if($item['bitActivo'] == 0){ $intCursor += 1; continue; }
  
  if ($intReceta == 0) { ?>
  <div class="col-md-4 col-xs-12 col-sm-12" style="padding-left: 2px;padding-right: 2px;" id="divRecetasCore_<?= $item['intId'] ?>">
<?php 
  }
?>
    <div class="panel panel-info">
      <div class="panel-heading" style="padding: 0px;">
        <h3 class="panel-title">
        <input type="text" class="form-control input-sm" id="txtRecetaStrNombre_<?= $item['intId'] ?>" value="<?= $item['strNombre'] ?>" placeholder="Nombre..." maxlength="100" style="padding: 1px;width: 50%;display: inline-block;color: black;">        
        <button class="btn btn-warning btn-xs" onclick="fnViveMovimento_Receta_eliminar(<?= $item['intId'] ?>);" style="float: right;">
          <i class="fa fa-trash" aria-hidden="true"></i> Receta
        </button>          
        <button class="btn btn-info btn-xs" onclick="fnViveMovimento_Receta_editar(<?= $item['intId'] ?>);" style="float: right;">
          <i class="fa fa-refresh" aria-hidden="true"></i> Nombre
        </button>          
      </h3>
    </div>
    <div class="panel-body" style="padding-left: 2px;padding-right: 2px;">
        <div class="row">
          <div class="form-group col-md-4 col-xs-4 col-sm-4" style="margin: 0px;">
            <label for="txtRecetaCantidad_<?= $item['intId'] ?>">Porción:</label>
            <input type="number" class="form-control" id="txtRecetaCantidad_<?= $item['intId'] ?>" placeholder="1" value="1" min="0" max="999" step="0.01">
          </div>
          <div class="form-group col-md-8 col-xs-8 col-sm-8" style="padding-left: 0px;margin: 0px;">
            <label for="cbRecetaAlimento_<?= $item['intId'] ?>">Alimento:</label>
              <select id="cbRecetaAlimento_<?= $item['intId'] ?>">
                <option selected="true" disabled="disabled">Seleccionar alimento</option>
                <?php
                  foreach ($listAlimentos as $alimento) {
                    if(!isset($alimento['decProteina'])){
                      continue;
                    }
                  $strPCGVL = '';
                  if($alimento['decProteina']>0){
                      $strPCGVL = ($alimento['decProteina'] + 0).'P';
                  }
                  if($alimento['decCarbohidratos']>0){
                      $strPCGVL = $strPCGVL.($strPCGVL!=''?', ':'').($alimento['decCarbohidratos'] + 0).'C';
                  }
                  if($alimento['decGrasa']>0){
                      $strPCGVL = $strPCGVL.($strPCGVL!=''?', ':'').($alimento['decGrasa'] + 0).'G';
                  }
                  if($alimento['decVegetales']>0){
                      $strPCGVL = $strPCGVL.($strPCGVL!=''?', ':'').($alimento['decVegetales'] + 0).'V';
                  }
                  if($alimento['decLibre']>0){
                      $strPCGVL = $strPCGVL.($strPCGVL!=''?', ':'').($alimento['decLibre'] + 0).'L';
                  }
                  $strAlimento = (($alimento['decPorcion']+0).' '.$alimento['strUnidadMedida'].' de '.$alimento['strAlimento'].' ('.$strPCGVL.')');
                  ?>
                  <option value="<?= $alimento['intId'] ?>"><?= $strAlimento ?></option>
                <?php } ?>
              </select>
          </div>
          <div class="col-md-12 col-xs-12 col-sm-12">
            <button class="btn btn-success btn-block btn-xs" onclick="fnViveMovimento_Receta_Detalle_agregar(<?= $item['intId'] ?>);" style="float: right;">
              <i class="fa fa-plus" aria-hidden="true"></i> Alimento
            </button>   
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
<?php
$item['detalle'] = fnViveMovimentoRecetaListadoDetalle($item['intId']);

 if (isset($item['detalle'])){ foreach ($item['detalle'] as $itemDetalle) { ?>
  <tr>
    <td>
        <button class="btn btn-info btn-xs" onclick="fnViveMovimento_Receta_Detalle_editar(<?= $itemDetalle['intId'] ?>);">
          <i class="fa fa-refresh" aria-hidden="true"></i>
        </button>   
    </td>
    <td>
      <input type="number" class="form-control input-sm" id="txtRecetaCantidad_editar_<?= $itemDetalle['intId'] ?>" placeholder="1" value="<?= $itemDetalle['decCantidad'] ?>" min="0" max="999" step="0.01" style="padding: 1px;">
    </td>
    <td style="padding-left: 0px;padding-right: 0px;"><?= $itemDetalle['strUnidadMedida'] ?></td>
    <td><?= $itemDetalle['strAlimento'] ?></td>
    <td>
       <button class="btn btn-warning btn-xs" onclick="fnViveMovimento_Receta_Detalle_eliminar(<?= $itemDetalle['intId'] ?>);">
          <i class="fa fa-trash" aria-hidden="true"></i>
        </button>
    </td>
  </tr>
<?php } } ?>
  </table>
</div>


    </div>
  </div>

<?php if ($intReceta == 0) { ?> </div> <?php } ?>

<?php
$intCursor += 1;
$intCursorRow += 1;
if ($intCursorRow % 3 == 0) {
  echo '<div class="col-md-12 col-xs-12 col-sm-12">.</div>';
}
} ?>

<script>
  function fnViveMovimentoRecetaListadoCore(intReceta){
    jQuery.ajax({
      type : "get",
      url : '<?= admin_url( 'admin-ajax.php' ) ?>',
      data : {
        action: "fnViveMovimentoRecetaListadoCore",
        intReceta: intReceta,
      },
      success: function(response) {
        $('#divRecetasCore_'+intReceta).html(response);
        setTimeout(function(){ try{$('select').select2();}catch(e){} }, 500);
     }
   });
  }
  function fnViveMovimento_Receta_agregar(intReceta){
    if($('#txtRecetaStrNombre_' + intReceta).val() == null || $('#txtRecetaStrNombre_' + intReceta).val() == ''){
      return;
    }
    var strNombre = $('#txtRecetaStrNombre_' + intReceta).val();
    jQuery.ajax({
      type : "get",
      url : '<?= admin_url( 'admin-ajax.php' ) ?>',
      data : {
        action: "fnViveMovimentoRecetaAgregar",
        strNombre: strNombre
      },
      success: function(response) {
        // var response = jQuery.parseJSON(response);
        if(response.toLocaleLowerCase().indexOf('listo') !== -1 || response.toLocaleLowerCase().indexOf('exit') !== -1) {
          fnViveMovimentoRecetaListadoCore(0);
        }else {
         alert(response.mnj);
       }
     }
   });
  }
  function fnViveMovimento_Receta_clonar(){
    if($('#cbRecetaAlimento_configurada').val() == null || $('#cbRecetaAlimento_configurada').val() == '' || $('#cbRecetaAlimento_configurada').val() == 0){
      return;
    }
    var intClonar = $('#cbRecetaAlimento_configurada').val();
    jQuery.ajax({
      type : "get",
      url : '<?= admin_url( 'admin-ajax.php' ) ?>',
      data : {
        action: "fnViveMovimentoRecetaClonar",
        intClonar: intClonar
      },
      success: function(response) {
        // var response = jQuery.parseJSON(response);
        if(response.toLocaleLowerCase().indexOf('listo') !== -1 || response.toLocaleLowerCase().indexOf('exit') !== -1) {
          fnViveMovimentoRecetaListadoCore(0);
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
        // var response = jQuery.parseJSON(response);
        if(response.toLocaleLowerCase().indexOf('listo') !== -1 || response.toLocaleLowerCase().indexOf('exit') !== -1) {
          fnViveMovimentoRecetaListadoCore(0);
        }else {
         alert(response.mnj);
       }
     }
   });
  }
  function fnViveMovimento_Receta_editar(intReceta){
    if($('#txtRecetaStrNombre_' + intReceta).val() == null || $('#txtRecetaStrNombre_' + intReceta).val() == ''){
      return;
    }
    var strNombre = $('#txtRecetaStrNombre_' + intReceta).val();
    jQuery.ajax({
      type : "get",
      url : '<?= admin_url( 'admin-ajax.php' ) ?>',
      data : {
        action: "fnViveMovimentoRecetaEditar"
        ,intReceta: intReceta
        ,strNombre: strNombre
      },
      success: function(response) {
        // var response = jQuery.parseJSON(response);
        if(response.toLocaleLowerCase().indexOf('listo') !== -1 || response.toLocaleLowerCase().indexOf('exit') !== -1) {
          fnViveMovimentoRecetaListadoCore(intReceta);
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
        // var response = jQuery.parseJSON(response);
        if(response.toLocaleLowerCase().indexOf('listo') !== -1 || response.toLocaleLowerCase().indexOf('exit') !== -1) {
          fnViveMovimentoRecetaListadoCore(intReceta);
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
        // var response = jQuery.parseJSON(response);
        if(response.toLocaleLowerCase().indexOf('listo') !== -1 || response.toLocaleLowerCase().indexOf('exit') !== -1) {
          fnViveMovimentoRecetaListadoCore(intReceta);
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
        // var response = jQuery.parseJSON(response);
        if(response.toLocaleLowerCase().indexOf('listo') !== -1 || response.toLocaleLowerCase().indexOf('exit') !== -1) {
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
        // var response = jQuery.parseJSON(response);
        if(response.toLocaleLowerCase().indexOf('listo') !== -1 || response.toLocaleLowerCase().indexOf('exit') !== -1) {
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