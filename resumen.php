<?php
  function fnViveMovimento_resumen_data(){
    setlocale(LC_ALL, 'es_ES').': ';
    global $wpdb, $strUsuario;
    global $gDataPaso_1,$gDataPaso_2,$gDataPaso_3;
    $strUsuario = wp_get_current_user()->user_login;
    $gDataPaso_1 = $wpdb->get_results("SELECT * FROM wp_vivemov_users_informacion WHERE strUsuario = '$strUsuario' ORDER BY intId DESC LIMIT 1;");
    $gDataPaso_2 = $wpdb->get_results("SELECT * FROM wp_vivemov_users_actividad_gasto_energetico WHERE strUsuario = '$strUsuario' ORDER BY decId DESC LIMIT 1;");
    $gDataPaso_3 = $wpdb->get_results("SELECT * FROM wp_vivemov_users_meta WHERE strUsuario = '$strUsuario' ORDER BY decId DESC LIMIT 1;");
  }
	function fnViveMovimento_resumen_init(){
  		global $wpdb, $strUsuario;
      global $gDataPaso_1,$gDataPaso_2,$gDataPaso_3;
    	$strUsuario = wp_get_current_user()->user_login;
      fnViveMovimento_resumen_data();
        //$datFechaDiario = new DateTime();
?>

<div class="row">
  <div class="col-md-10 col-xs-12 col-sm-12  col-md-offset-1">
    <h2>Bienvenid@ <?php echo $strUsuario; ?></h2>
    <!-- <h4><?php//echo $datFechaDiario->format('D, d-M-Y'); ?></h4> -->
    <h4><?php echo iconv('ISO-8859-1', 'UTF-8', strftime('%A, %d/%B/%Y', time())); ?></h4>

    <div class="alert alert-<?php echo ($gDataPaso_1 == null ? 'warning' : 'success') ?>" role="alert">
      <strong><a href="/user/?action=tab_Paso_1"> Paso 1 - </a></strong> Información Básica (<?php echo ($gDataPaso_1 == null ? 'PENDIENTE' : 'Completada') ?>)
    </div>

    <div class="alert alert-<?php echo ($gDataPaso_2 == null ? 'warning' : 'success') ?>" role="alert">
      <strong><a href="/user/?action=tab_Paso_2"> Paso 2 - </a></strong> Gasto Energético (<?php echo ($gDataPaso_2 == null ? 'PENDIENTE' : 'Completada') ?>)
    </div>

    <div class="alert alert-<?php echo ($gDataPaso_3 == null ? 'warning' : 'success') ?>" role="alert">
      <strong><a href="/user/?action=tab_Paso_3"> Paso 3 - </a></strong> Metas (<?php echo ($gDataPaso_3 == null ? 'PENDIENTE' : 'Completada') ?>)
    </div>

    <div class="alert alert-info" role="alert">
      <strong><a href="/user/?action=tab_Paso_4"> Paso 4 - </a></strong> Conoce tu Plan Nutricional
    </div>
    <div class="alert alert-info" role="alert">
      <strong><a href="/plan-de-ejercicios">Paso 5 - </a></strong> Elige tu Plan de Ejercicios
    </div>
  </div>
</div>

<?php 
  if ($gDataPaso_1 == null) {
    echo '<script>window.location.href = "/user/?action=tab_Paso_1";</script>';
  }else if ($gDataPaso_2 == null) {
    echo '<script>window.location.href = "/user/?action=tab_Paso_2";</script>';
  }else if ($gDataPaso_3 == null) {
    echo '<script>window.location.href = "/user/?action=tab_Paso_3";</script>';
  }
}
?>
