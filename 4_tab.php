<?php
function fnTab_4_cargar(){
  global $wpdb,$intMeta;
  $strUsuario = wp_get_current_user()->user_login;
  try {
    $buscar = $wpdb->get_results("SELECT * FROM wp_vivemov_users_meta WHERE strUsuario = '$strUsuario' ORDER BY decId DESC LIMIT 1;");
    if (count($buscar) > 0) {
      $buscar = $buscar[0];
      $intMeta = $buscar->intMeta;
    }else{
      $intMeta = 1;
    }
  } catch (Exception $e) {
    echo $e;
  }
}
function fnTab_4_save($strUsuario,$intMeta){
  $registro = array(
    'strUsuario'    =>   $strUsuario,
    'intMeta'  =>   $intMeta
  );
  global $wpdb;
  $response = $wpdb->insert("wp_vivemov_users_meta", $registro);
  if($response) {
    $_POST = array();
    echo fnMensaje(1,'Listo, guardado!');
  } else {
    echo fnMensaje(2,'Inconvenientes, datos no guardados!');
  }
}
function fnTab_4(){
  global $strUsuario,$intMeta,$decMetabolismo,$decActivityFactor,$intActividadTipo,$decTDEE,$decEjercicio,$intMetaValor,$intMeta,$decCalorias,$decPeso;
  global $decProteinas,$decCarbo,$decGrasas,$decIMC,$intSexo,$intExperiencia;
  $intMetaValor = array(0,-500,0,350);

  $strUsuario = wp_get_current_user()->user_login;
  if (isset($_GET['action']) && $_GET['action'] == 'tab_Paso_4' && isset($_POST['txtForm_4']) && $_POST['txtForm_4'] != null && $_POST['txtForm_4'] != '') {
    $intMeta = intval($_POST['intMeta']);
    fnTab_4_save($strUsuario,$intMeta);
  }
  fnTab_4_cargar();
  $decTDEE = ($decMetabolismo * $decActivityFactor[$intActividadTipo]);
  $decEjercicio = 500 - ($decTDEE-$decMetabolismo);
  $decCalorias = $decTDEE + $intMetaValor[$intMeta];

  $decProteinas = array(0,0,0,0);
  $decCarbo = array(0,0,0,0);
  $decGrasas = array(0,0,0,0);

  $decIndicador = 0.8;
  if ($intSexo == 1) { //hombres
    if ($intMeta == 1) { //bajar
      if ($intExperiencia == 3 && $decIMC < 26) { //avanzado + no obeso
        $decIndicador = 0.9;
      }else if ($decIMC >= 26) { //hombre + bajar + obeso
        $decIndicador = 0.6;
      }else{
        $decIndicador = 0.7;          
      }
    }else{ //para manter o subir es igual
      $decIndicador = 0.7;
    }
  }else{ //mujeres
    if ($intMeta == 1) { //bajar
      if ($intExperiencia == 3 && $decIMC < 26) { //avanzada + no obesa
        $decIndicador = 0.9;
      }else if ($decIMC >= 26) { //mujer + bajar + obesa
        $decIndicador = 0.6;
      }else{
        $decIndicador = 0.7;          
      }
    }else{ //para mantener o subir es igual
      $decIndicador = 0.7;
    }
  }
  // echo '=========> $intSexo '.$intSexo.'<=========';
  // echo '=========> $intExperiencia '.$intExperiencia.'<=========';
  // echo '=========> $decIMC '.$decIMC.'<=========';
  // echo '=========> $decIndicador '.$decIndicador.'<=========';
  // $decProteinas[2]=$decPeso * 0.8;
  $decProteinas[2]=$decPeso * $decIndicador;
  $decProteinas[1]=$decProteinas[2]*4;
  if ($decCalorias != 0) {
    $decProteinas[0]=$decProteinas[1]/$decCalorias;
    // $decProteinas[3]=($decProteinas[2]/30)-1;
    $decProteinas[3]=($decProteinas[2]/25.0);
  }
 
  $decGrasas[0] = 0.30;
  $decGrasas[1] = $decGrasas[0] * $decCalorias;
  $decGrasas[2] = $decGrasas[1] / 9;
  $decGrasas[3] = ($decGrasas[2] / 14)-1;

  $decCarbo[0] = 1 - ($decProteinas[0]+$decGrasas[0]);
  $decCarbo[0] = 1 - ($decProteinas[0]+$decGrasas[0]);
  $decCarbo[1] = $decCalorias * $decCarbo[0];
  $decCarbo[2] = $decCarbo[1] / 4;
  $decCarbo[3] = $decCarbo[2] / 25;

  fnTab_4_form($strUsuario,$intMeta,$decMetabolismo,$decActivityFactor,$decTDEE,$intActividadTipo,$decEjercicio,$decCalorias,$decProteinas,$decCarbo,$decGrasas,$decIMC);
}

function fnTab_4_form($strUsuario,$intMeta,$decMetabolismo,$decActivityFactor,$decTDEE,$intActividadTipo,$decEjercicio,$decCalorias,$decProteinas,$decCarbo,$decGrasas,$decIMC){ ?>

 <div class="row" style="padding: 0px;">
     <div class="col-md-12 col-xs-12 col-sm-12" style="padding: 0px;">
        <div class="vc_message_box vc_message_box-solid-icon vc_message_box-square vc_color-success">
          <div class="vc_message_box-icon"><i class="fa fa-info" aria-hidden="true"></i></div>
            <p><?php echo $strUsuario; ?>, hemos calculado tus porciones segun tu peso, altura y gasto energetico.</p>
        </div>
    </div>
  </div>

  <form action="<?php echo strtok($_SERVER["REQUEST_URI"],'?');?>?action=tab_Paso_4" method="post" class="row">
  <input type="hidden" value="listo" name="txtForm_4" />
  <div class="col-md-12 col-xs-12 col-sm-12" style="padding: 0px;">
      <div class="vc_message_box vc_message_box-solid-icon vc_message_box-square vc_color-success"><div class="vc_message_box-icon"><i class="fa fa-info" aria-hidden="true"></i></div><p>Para alcanzar tu meta de <strong><?php 
        if ($intMeta == 1) {
          echo 'Bajar';
        }else if ($intMeta == 2) {        
          echo 'Mantener';          
        }else if ($intMeta == 3) {
          echo 'Subir';
        }
      ?></strong> estas son las porciones que tienes que seguir <strong> <?php echo fnRedondearCUSTOMUP($decProteinas[3]); ?> Proteinas </strong>
        , <strong><?php echo fnRedondearCUSTOMUP($decCarbo[3]); ?> Carbohidratos </strong>
        y <strong><?php echo fnRedondearCUSTOMUP($decGrasas[3]); ?> Grasas</strong>
      </p>
      </div>
  </div>

  <div class="col-md-5 col-xs-12 col-sm-12 noMostrar">
    <br/>
    <center><h2><small>Calorias</small></h2></center>
    <div class="table-responsive">
      <table class="table table-striped table-condensed">
        <thead>
          <tr>
            <th>Meta</th>
            <th>Descripcion</th>
          </tr>
        </thead>
        <tbody>
          <tr class="info">
            <th scope="row">BMI</th>
            <td>
              <?php echo fnRedondear($decIMC); ?>
            </td>
          </tr>
          <tr class="info">
            <th scope="row">RMR</th>
            <td>        
              <?php echo fnRedondear($decMetabolismo); ?>
            </td>
          </tr>
          <tr class="info">
            <th scope="row">TDEE</th>
            <td>
              <?php echo fnRedondear($decTDEE); ?>
            </td>
          </tr>
          <tr class="info">
            <th scope="row">AF</th>
            <td>
              <?php echo fnRedondear($decActivityFactor[($intActividadTipo-1)]); ?>
            </td>
          </tr>
          <tr class="info">
            <th scope="row">EJERCICIO</th>
            <td>
              <?php echo fnRedondear($decEjercicio); ?>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-md-7 col-xs-12 col-sm-12 noMostrar">
    <br/>
    <center><h2><small>Porciones</small></h2></center>
    <div class="table-responsive">
      <table class="table table-striped table-condensed">
        <thead>
          <tr>
            <th class="rojo texto_blanco">Calorias</th>
            <th class="rojo texto_blanco" colspan="3"><?php echo fnRedondear($decCalorias); ?></th>
            <th class="rojo texto_blanco"></th>
          </tr>
          <tr>
            <th></th>
            <th class="amarillo">PROTEINAS</th>
            <th class="naranja">CARBOHIDRATOS</th>
            <th class="celeste">GRASAS</th>
            <th>TOTAL</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th>%</th>
            <th class="amarillo"><?php echo fnRedondear($decProteinas[0]*100); ?>%</th>
            <th class="naranja"><?php echo fnRedondear($decCarbo[0]*100); ?>%</th>
            <th class="celeste"><?php echo fnRedondear($decGrasas[0]*100); ?>%</th>
            <th>100%</th>
          </tr>
          <tr>
            <th>CALORIAS/MACRO</th>
            <th class="amarillo"><?php echo fnRedondear($decProteinas[1]); ?></th>
            <th class="naranja"><?php echo fnRedondear($decCarbo[1]); ?></th>
            <th class="celeste"><?php echo fnRedondear($decGrasas[1]); ?></th>
            <th><?php echo fnRedondear($decCalorias); ?></th>
          </tr>
          <tr>
            <th>MACROS</th>
            <th class="amarillo"><?php echo fnRedondear($decProteinas[2]); ?></th>
            <th class="naranja"><?php echo fnRedondear($decCarbo[2]); ?></th>
            <th class="celeste"><?php echo fnRedondear($decGrasas[2]); ?></th>
            <th></th>
          </tr>
          <tr>
            <th>PORCIONES</th>
            <th class="amarillo"><?php echo fnRedondear($decProteinas[3]); ?></th>
            <th class="naranja"><?php echo fnRedondear($decCarbo[3]); ?></th>
            <th class="celeste"><?php echo fnRedondear($decGrasas[3]); ?></th>
            <th></th>
          </tr>
          <tr>
            <th>PORCIONES <i class="fas fa-chevron-right"></i></th>
            <th class="amarillo"><?php echo (' <i class="fas fa-chevron-right"></i> '.fnRedondearCUSTOMUP($decProteinas[3])); ?></th>
            <th class="naranja"><?php echo (' <i class="fas fa-chevron-right"></i> '.fnRedondearCUSTOMUP($decCarbo[3])); ?></th>
            <th class="celeste"><?php echo (' <i class="fas fa-chevron-right"></i> '.fnRedondearCUSTOMUP($decGrasas[3])); ?></th>
            <th></th>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="col-md-12 col-xs-12 col-sm-12">
    <div class="btn-group btn-group-justified" role="group" aria-label="...">
      <div class="btn-group" role="group">
        <button type="button" class="btn" onclick="fnTabNavRedirect(3);" style="color: white;">
          <i class="fas fa-angle-left"></i> Anterior
        </button>
      </div>
      <div class="btn-group" role="group" style="display: none;" style="color: white;">
        <button type="submit" class="btn">
          <i class="fas fa-save"></i> Guardar
        </button>
      </div>
      <div class="btn-group" role="group">
        <button type="button" class="btn" onclick="fnTabNavRedirect(10);" style="color: white;">
          Perfil <i class="fas fa-angle-right"></i>
        </button>
      </div>
    </div>
  </div>

  <?php echo '</form>'; } ?>

