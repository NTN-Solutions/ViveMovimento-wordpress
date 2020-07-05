<?php
function fnVerificarCombraDeSuscripcion() {
  //https://stackoverflow.com/questions/38157176/how-to-get-purchase-date-from-woocommerce-order
  // Get All order of current user
  
  // $strProducto = 'Suscripción Mensual';
  $strProducto = 'Suscripción de Nutrición';
  $product = get_page_by_title( $strProducto, OBJECT, 'product' );
  if($product == null)return 30; // devolvemos 30 dias simulando que ya paso el mes de no haber comprado la Suscripcion

  $id = $product->ID;
  $orders = get_posts( array(
      'numberposts' => -1,
      'meta_key'    => '_customer_user',
      'meta_value'  => get_current_user_id(),
      'post_type'   => wc_get_order_types( 'view-orders' ),
      'post_status' => array_keys( wc_get_order_statuses() )
  ) );

  if ( !$orders ) return 30; // devolvemos 30 dias simulando que ya paso el mes de no haber comprado la Suscripcion

  $all_ordered_product = array(); // store products ordered in an array

  foreach ( $orders as $order => $data ) { // Loop through each order
      $order_data = new WC_Order( $data->ID ); // create new object for each order
      foreach ( $order_data->get_items() as $key => $item ) {  // loop through each order item
          // store in array with product ID as key and order date a value
          $all_ordered_product[ $item['product_id'] ] = $data->post_date;
      }
  }
  if ( isset( $all_ordered_product[ $id ] ) ) { // check if defined ID is found in array
    // $datFechaCompra = date('Y-m-d', strtotime( $all_ordered_product[ $id ] ) );
    // $datFechaActual = date_format(date_create("2020-07-20"),"Y-m-d");

    $datFechaHoraActual = time();
    $datFechaHoraCompra = strtotime($all_ordered_product[ $id ]);
    $intDias = $datFechaHoraActual - $datFechaHoraCompra;
    $intDias = abs(round($intDias / (60 * 60 * 24)));
    // echo '$intDias....'.$intDias.'.....';
    return $intDias; //devolvemos la cantidad de dias transcurridos de la compra de susbcripcion
  } else {
    return 30;// devolvemos 30 dias simulando que ya paso el mes de no haber comprado la Suscripcion
  }
}

function strSuscripcionComprada($strUsuario){
  $decProducto = floatval(3731);
  $dataProducto = wc_get_product( $decProducto );
  //codigo de prueba comentado, se obtuvo de proyecto cafbook.org, misma logica se ocupara aca.

  // $strProductoNombre = strtolower($dataProducto->get_name());
  // $bitPermitirMostrarContenido = false;
  // if (strpos($strProductoNombre, '(demo)') !== false || strpos($strProductoNombre, '*') !== false || strpos($strProductoNombre, 'gratis') !== false) {
  //   //si nombre del producto lleva (Demo) le permitimos
  //   $bitPermitirMostrarContenido = true;
  // }else

  echo '=============>'.(_cmk_check_ordered_product(3731)).'<==============';

  if(!is_user_logged_in()){
    //si no esta logeado no permitimos mostrar tabla de porciones
    $bitPermitirMostrarContenido = false;
  }else{
    //si usuario esta logeado buscamos si ya lo compro
    $strUsuarioLogeado = wp_get_current_user();
    if ( wc_customer_bought_product( $strUsuarioLogeado->user_email, $strUsuarioLogeado->ID, $dataProducto->get_id() ) ){
      //si ya lo compro tiene permitido tabla de porciones
      echo '==========>'.(wc_customer_bought_product( $strUsuarioLogeado->user_email, $strUsuarioLogeado->ID, $dataProducto->get_id() )).'<===========';
      $bitPermitirMostrarContenido = true;
    }else{
      //sino lo compro no tiene permitido tabla de porciones
      $bitPermitirMostrarContenido = false;
    }
  } 
}

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
function fnTab_4_save_custom_porciones(){
  global $wpdb, $strUsuario;
  $registro = array(
    'strUsuario'=>$strUsuario,
    'intProteina'=> intval($_POST['intCustomP']),
    'intCarbohidrato'=> intval($_POST['intCustomC']),
    'intGrasa'=> intval($_POST['intCustomG']),
    'bitActivo'=>1,
    'datCreacion'=>date('Y-m-d H:i:s')
  );
  $wpdb->get_results("UPDATE wp_vivemov_users_porciones as D
                      SET D.bitActivo = 0
                      WHERE D.strUsuario = '$strUsuario' AND D.bitActivo = 1;");
  $response;
  if($_POST['txtCustomOpcion'] == 1){
    $response = $wpdb->insert("wp_vivemov_users_porciones", $registro);
  }
  if($response || $_POST['txtCustomOpcion'] == 2) {
    $_POST = array();
    echo fnMensaje(1,'Listo, '.($_POST['txtCustomOpcion'] == 1 ? 'guardado' : 'eliminado').'!');
  } else {
    echo fnMensaje(2,'Inconvenientes, datos no guardados!');
  }

}
function fnTab_4(){
  global $strUsuario,$intMeta,$decMetabolismo,$decActivityFactor,$intActividadTipo,$decTDEE,$decEjercicio,$intMetaValor,$intMeta,$decCalorias,$decPeso;
  global $decProteinas,$decCarbo,$decGrasas,$decIMC,$intSexo,$intExperiencia;
  $intMetaValor = array(0,-500,0,350);

  $strUsuario = wp_get_current_user()->user_login;
  if (isset($_GET['action']) && $_GET['action'] == 'tab_Paso_4' && isset($_POST['intOpcion']) && $_POST['intOpcion'] != null && $_POST['intOpcion'] == '1') {
    $intMeta = intval($_POST['intMeta']);
    fnTab_4_save($strUsuario,$intMeta);
  }else if (isset($_GET['action']) && $_GET['action'] == 'tab_Paso_4' && isset($_POST['intOpcion']) && $_POST['intOpcion'] != null && $_POST['intOpcion'] == '2') {
    fnTab_4_save_custom_porciones();
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

  $intDiasSuscripcion = fnVerificarCombraDeSuscripcion();
  if($intDiasSuscripcion < 30){
    fnTab_4_form($strUsuario,$intMeta,$decMetabolismo,$decActivityFactor,$decTDEE,$intActividadTipo,$decEjercicio,$decCalorias,$decProteinas,$decCarbo,$decGrasas,$decIMC,$intDiasSuscripcion);
  }else{
    fnTab_4_alerta_Suscripcion();
  }

}
function fnTab_4_alerta_Suscripcion(){
  ?>
  <div class="alert alert-warning alert-dismissible" role="alert">
    <strong><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Suscripción Mensual</strong>
    <br/>Para observar el calculo de tus porciones debes realizar la compra de la Suscripción!
    <br/><br/><a href="/product/suscripcion-de-nutricion-2/">Click aquí para comprar Suscripción Mensual</a>
  </div>
  <?php
}

function fnTab_4_form($strUsuario,$intMeta,$decMetabolismo,$decActivityFactor,$decTDEE,$intActividadTipo,$decEjercicio,$decCalorias,$decProteinas,$decCarbo,$decGrasas,$decIMC,$intDiasSuscripcion){
  global $intExperiencia;
  global $wpdb;
  $misPorciones = null;
  if ($intExperiencia == 3) {
    $misPorciones = $wpdb->get_results("SELECT * FROM wp_vivemov_users_porciones WHERE strUsuario = '$strUsuario' ORDER BY decId DESC LIMIT 1;");
    if (count($misPorciones) > 0) {
      $misPorciones = $misPorciones[0];
      // echo print_r($misPorciones);
    }else{
      $misPorciones = null;
    }
  }

  ?>

  <style type="text/css">
    .txtMisPorciones{
      color: black !important;
      font-weight: bold !important;
      text-align: center !important;
      font-size: x-large !important;
    }
  </style>

 <div class="row" style="padding: 0px;">
     <div class="col-md-12 col-xs-12 col-sm-12" style="padding: 0px;">
        <div class="vc_message_box vc_message_box-solid-icon vc_message_box-square vc_color-success">
          <div class="vc_message_box-icon"><i class="fa fa-info" aria-hidden="true"></i></div>
            <p><?php echo $strUsuario; ?>, hemos calculado tus porciones segun tu peso, altura y gasto energetico.</p>
            <p>Tu suscripción terminará en <?= abs(30 - $intDiasSuscripcion) ?> días</p>
        </div>
    </div>
  </div>

  <form action="<?php echo strtok($_SERVER["REQUEST_URI"],'?');?>?action=tab_Paso_4" method="post" class="row">
  <input type="hidden" value="1" name="intOpcion" />
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
          <?php
            if ($intExperiencia == 3) { //para avanzados mostramos opcion de cambiar porcion o elimiar si tenian agregada
          ?>
              <tr>
                <form action="<?php echo strtok($_SERVER["REQUEST_URI"],'?');?>?action=tab_Paso_4" method="post" class="row">
                  <input type="hidden" name="intOpcion" value="2">
                  <input type="hidden" name="txtCustomOpcion" id="txtCustomOpcion" value="1">
                  <th>MIS PORCIONES <i class="fas fa-chevron-right"></i></th>
                  <th class="amarillo"><input type="number" name="intCustomP" class="form-control txtMisPorciones" value="<?= ($misPorciones != null ? $misPorciones->intProteina : '0') ?>" min="1" max="99"></th>
                  <th class="naranja"><input type="number" name="intCustomC" class="form-control txtMisPorciones" value="<?= ($misPorciones != null ? $misPorciones->intCarbohidrato : '0') ?>" min="1" max="99"></th>
                  <th class="celeste"><input type="number" name="intCustomG" class="form-control txtMisPorciones" value="<?= ($misPorciones != null ? $misPorciones->intGrasa : '0') ?>" min="1" max="99"></th>
                  <th>
                    <div class="btn-group btn-group-sm" role="group" aria-label="...">
                      <button type="submit" class="btn btn-primary" onclick="$('#txtCustomOpcion').val(1);"><span class="glyphicon glyphicon-floppy-saved" aria-hidden="true"></span></button>
                      <button type="submit" class="btn btn-warning" onclick="$('#txtCustomOpcion').val(2);"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
                    </div>
                  </th>
                </form>
              </tr>
          <?php
            }
          ?>
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


