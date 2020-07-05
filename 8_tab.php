<?php
function fnTab_8_cargar(){
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

function fnTab_8(){
  $intDiasSuscripcion = fnVerificarCombraDeSuscripcion();
  if($intDiasSuscripcion < 30){
    fnTab_8_form();
  }else{
    fnTab_8_alerta_Suscripcion();
  }

}
function fnTab_8_alerta_Suscripcion(){
  ?>
<style>
.pdfobject-container { height: 800px !important; border: 1rem solid rgba(0,0,0,.1); }
</style>

<script src="/wp-content/plugins/vivemovimento/js/pdfobject.min.js"></script>


<div class="alert alert-warning alert-dismissible" role="alert">
    <strong><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Suscripción Mensual</strong>
    <br/>Para observar los planes de ejercicios debes realizar la compra de la Suscripción!
    <br/><br/><a href="/product/suscripcion-de-nutricion-2/">Click aquí para comprar Suscripción Mensual</a>
  </div>
  <?php
}

function fnTab_8_form(){ ?>
  <!-- Nav tabs -->
  <ul class="nav nav-tabs nav-tabs nav-justified" role="tablist">
    <li role="presentation" class="active"><a href="#tab_ejercicio_1" aria-controls="tab_ejercicio_1" role="tab" data-toggle="tab">Plan Ejercicio 1</a></li>
    <li role="presentation"><a href="#tab_ejercicio_2" aria-controls="tab_ejercicio_2" role="tab" data-toggle="tab">Plan Ejercicio 2</a></li>
    <li role="presentation"><a href="#tab_ejercicio_3" aria-controls="tab_ejercicio_3" role="tab" data-toggle="tab">Plan Ejercicio 3</a></li>
    <li role="presentation"><a href="#tab_ejercicio_4" aria-controls="tab_ejercicio_4" role="tab" data-toggle="tab">Plan Ejercicio 4</a></li>
  </ul>
  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="tab_ejercicio_1">
      <div id="example1"></div>
    </div>
    <div role="tabpanel" class="tab-pane" id="tab_ejercicio_2">
      <div id="example2"></div>
    </div>
    <div role="tabpanel" class="tab-pane" id="tab_ejercicio_3">
      <div id="example3"></div>
    </div>
    <div role="tabpanel" class="tab-pane" id="tab_ejercicio_4">
      <div id="example4"></div>
    </div>
  </div>

  <script>
    PDFObject.embed("http://www.ahuce.org/Portals/0/Publicaciones/Boletines_OI/Ejercicios_para_Trabajar_en_Casa.pdf", "#example1");
    PDFObject.embed("https://seom.org/seomcms/images/stories/recursos/Guias_Nutricion_Ejercicio_Cancer_Mama.pdf", "#example2");
    PDFObject.embed("http://www.sld.cu/galerias/pdf/sitios/rehabilitacion-ejer/estiramientos_1.pdf", "#example3");
    PDFObject.embed("http://media.specialolympics.org/soi/files/resources/SPANISH/StriveTrain/Train_HomeExercise_Spanish.pdf", "#example4");
  </script>
  <?php } ?>


