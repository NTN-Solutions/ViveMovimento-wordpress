<?php
function fnMiInformacion_validar($strUsuario,$intEdad,$decAltura,$decPeso){
    global $reg_errors;
    $reg_errors = new WP_Error;
    if ( empty( $strUsuario ) || empty($intEdad) || empty($decAltura) || empty($decPeso) ) {
        $reg_errors->add('field', 'Todos los campos son requeridos');
    }
    if ( empty($intEdad) ) {
        $reg_errors->add('field', 'Ingresar Edad!');
    }
    if ( empty($decAltura) ) {
        $reg_errors->add('field', 'Ingresar Altura (cm)');
    }
    if ( empty($decPeso) ) {
        $reg_errors->add('field', 'Ingresar Peso (lbs)');
    }
    if ( is_wp_error( $reg_errors ) ) { 
        $strMensaje = '';
        foreach ( $reg_errors->get_error_messages() as $error ) {        
            $strMensaje = $strMensaje.'<br/>'.$error;
        }
        echo fnMensaje(2,$strMensaje);
    }
}
function fnTab_1() {
    global $strUsuario,$intEdad,$decAltura,$decPeso,$intSexo,$decGrasa,$decMetabolismo;
    $strUsuario = wp_get_current_user()->user_login;
    if (isset($_GET['action']) && $_GET['action'] == 'tab_Paso_1' && isset($_POST['txtForm_1']) && $_POST['txtForm_1'] != null && $_POST['txtForm_1'] != '') {
        fnMiInformacion_validar($strUsuario,$_POST['intEdad'],$_POST['decAltura'],$_POST['decPeso']);
        $intEdad = floatval($_POST['intEdad']);
        $decAltura = floatval($_POST['decAltura']);
        $decPeso = floatval($_POST['decPeso']);
        $intSexo = floatval($_POST['intSexo']);
        $decGrasa = floatval($_POST['decGrasa']);
        fnGuardarMiInformacion_save();
    }
    fnMiInformacion_cargar($strUsuario);
    fnMiInformacion_form($strUsuario,$intEdad,$decAltura,$decPeso,$intSexo,$decGrasa,$decMetabolismo);
}

function fnGuardarMiInformacion_save() {
    global $reg_errors, $strUsuario,$intEdad,$decAltura,$decPeso,$intSexo,$decGrasa;
    $decMetabolismo = 0;
    $decGrasa = ($decGrasa == null || $decGrasa == '' ? 0 : $decGrasa);
    $decGrasaPctj = ($decGrasa >= 1 ? $decGrasa / 100 : $decGrasa);
    if ($intSexo == 1) {
      $Harris_Benedict_Original = (13.7516*($decPeso/2.2))+(5.0033*$decAltura)-(6.755*$intEdad)+66.473;
      $Harris_Benedict_Revised = (13.397*($decPeso/2.2))+(4.799*$decAltura)-(5.677*$intEdad)+88.362;
      $Mifflin_St_Jeor = (10*($decPeso/2.2))+(6.25*$decAltura)-(5*$intEdad)+5;
      $Katch_McArdle = 370+( 21.6*(($decPeso/2.2)*(1-$decGrasaPctj)));
      $Katch_McArdle_Hybrid =(370 * ( 1 -$decGrasaPctj  )) + (21.6 * (($decPeso/2.2) * (1 - $decGrasaPctj))) + (6.17 * (($decPeso/2.2) * $decGrasaPctj));
      $Cunningham = 500 + ( 22 * (($decPeso/2.2)  * ( 1 -$decGrasaPctj  ) ) );
  }else{
      $Harris_Benedict_Original = (9.5634*($decPeso/2.2))+(1.8496*$decAltura)-(4.6756*$intEdad)+655.0955;
      $Harris_Benedict_Revised = (9.247*($decPeso/2.2))+(3.098*$decAltura)-(4.33*$intEdad)+447.593;
      $Mifflin_St_Jeor = (10*($decPeso/2.2))+(6.25*$decAltura)-(5*$intEdad)-161;
      $Katch_McArdle = 370+( 21.6*(($decPeso/2.2)*(1-$decGrasaPctj)));
      $Katch_McArdle_Hybrid = (370 * ( 1 -$decGrasaPctj  )) + (21.6 * (($decPeso/2.2) * (1 - $decGrasaPctj))) + (6.17 * (($decPeso/2.2) * $decGrasaPctj));
      $Cunningham = 500 + ( 22 * (($decPeso/2.2)  * ( 1 -$decGrasaPctj  ) ) );
  }
  if ($decGrasa > 0) {
      $decMetabolismo = ($Harris_Benedict_Original+$Harris_Benedict_Revised+$Mifflin_St_Jeor+$Katch_McArdle+$Katch_McArdle_Hybrid+$Cunningham) / 6.0;
  }else{
      $decMetabolismo = ($Harris_Benedict_Original+$Harris_Benedict_Revised+$Mifflin_St_Jeor) / 3.0;
  }

  $registro = array(
    'strUsuario'    =>   $strUsuario,
    'intEdad'       =>   $intEdad,
    'decAltura'     =>   $decAltura,
    'decPeso'       =>   $decPeso,
    'decGrasa'      =>   $decGrasa,
    'intSexo'       =>   $intSexo,
    'decMetabolismo'=>   $decMetabolismo
);
  if (count($reg_errors->get_error_messages()) == 0) {
    global $wpdb;
    $response = $wpdb->insert("wp_vivemov_users_informacion", $registro);
    if($response) {
       $_POST = array();
       echo fnMensaje(1,'Listo, guardado!');
   } else {
    echo fnMensaje(2,'Inconvenientes, datos no guardados!');
}
}
}
function fnMiInformacion_cargar($strUsuario){
    global $wpdb,$intEdad,$decAltura,$decPeso,$intSexo,$decGrasa,$decMetabolismo,$decIMC;
    try {
       $buscar = $wpdb->get_results("SELECT * FROM wp_vivemov_users_informacion WHERE strUsuario = '$strUsuario' ORDER BY intId DESC LIMIT 1;");
       if (count($buscar) > 0) {
          $buscar = $buscar[0];
          $intEdad = $buscar->intEdad;
          $decAltura = $buscar->decAltura;
          $decPeso = $buscar->decPeso;
          $intSexo = $buscar->intSexo;
          $decGrasa = $buscar->decGrasa;
          $decMetabolismo = $buscar->decMetabolismo;    
          $decIMC = (($decPeso / 2.2) / (($decAltura/100) * ($decAltura/100)));

      }
  } catch (Exception $e) {
  }
}
// $decPeso = 172.0;
// $decAltura = 182.0;
// $intEdad = 24.0;
//   //=(13.7516*C8)+(5.0033*C9)-(6.755*C10)+66.473
//   $Harris_Benedict_Original = (13.7516*($decPeso/2.2))+(5.0033*$decAltura)-(6.755*$intEdad)+66.473;
//   $Harris_Benedict_Revised = (13.397*($decPeso/2.2))+(4.799*$decAltura)-(5.677*$intEdad)+88.362;
//   $Mifflin_St_Jeor = (10*($decPeso/2.2))+(6.25*$decAltura)-(5*$intEdad)+5;
//   $decMetabolismo = ($Harris_Benedict_Original+$Harris_Benedict_Revised+$Mifflin_St_Jeor) / 3.0;


// echo '<br/>============> $decPeso/2.2)'.($decPeso/2.2).'<============';
// echo '<br/>============> Harris_Benedict_Original'.$Harris_Benedict_Original.'<============';
// echo '<br/>============> Harris_Benedict_Revised'.$Harris_Benedict_Revised.'<============';
// echo '<br/>============> Mifflin_St_Jeor'.$Mifflin_St_Jeor.'<============';
// echo '<br/>============> decMetabolismo'.$decMetabolismo.'<============';

function fnMiInformacion_form($strUsuario,$intEdad,$decAltura,$decPeso,$intSexo,$decGrasa,$decMetabolismo){ ?>    
    <form id="frm_tab_1" action="<?php echo strtok($_SERVER["REQUEST_URI"],'?')?>?action=tab_Paso_1" method="post" class="row">
    <input type="hidden" value="listo" name="txtForm_1" />

        <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
          <ol class="carousel-indicators">
            <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
            <li data-target="#carousel-example-generic" data-slide-to="1"></li>
            <li data-target="#carousel-example-generic" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner" role="listbox">
            <div class="item active customCarousel">
                <div class="carousel-caption">
                    <h3>Hola <?php echo $strUsuario; ?>! Bienvenido a Vive Movimento.
                      <br/>Podras obtener tu plan personalizado a tan solo unos clicks!
                      <br/>Para iniciar, necesitamos saber los siguientes datos
                    </h3>
                </div>
            </div>
            <div class="item customCarousel">
                <div class="carousel-caption">
                    <h3><i class="far fa-address-card"></i> Edad:</h3>
                    <p>Ingresa tu edad:</p>
                    <input type="number" name="intEdad" value="<?php echo ( isset( $_POST['intEdad'] )? $_POST['intEdad'] : $intEdad ) ?>">
                </div>
            </div>
            <div class="item customCarousel">
                <div class="carousel-caption">
                    <h3><i class="fas fa-ruler-vertical"></i> Altura:</h3>
                    <p>Ingresa en centimetros:</p>
                    <input type="number" step="0.01" name="decAltura" value="<?php echo ( isset( $_POST['decAltura']) ? $_POST['decAltura'] : $decAltura ) ?>">
                </div>
            </div>
            <div class="item customCarousel">
                <div class="carousel-caption">
                    <h3><i class="fas fa-balance-scale"></i> Peso:</h3>
                    <p>Ingresa en libras:</p>
                    <input type="number" step="0.01" name="decPeso" value="<?php echo ( isset( $_POST['decPeso']) ? $_POST['decPeso'] : $decPeso ) ?>">
                </div>
            </div>
            <div class="item customCarousel">
                <div class="carousel-caption">
                    <h3><i class="fas fa-balance-scale-right"></i> Grasa (%):</h3>
                    <p>Es opcional:</p>
                    <input type="number" step="0.01" name="decGrasa" value="<?php echo ( isset( $_POST['decGrasa']) ? $_POST['decGrasa'] : $decGrasa ) ?>">
                </div>
            </div>
            <div class="item customCarousel">
                <div class="carousel-caption">
                    <h3>Selecciona tu sexo:</h3>
                    <div class="col-md-12 col-xs-12 col-sm-12" style="padding-top: 30px;">
                      <label class="rbContainer"><i class="fas fa-male"></i> Hombre
                            <input type="radio" name="intSexo" value="1">
                            <span class="rbCheckmark"></span>
                        </label>
                        <label class="rbContainer"><i class="fas fa-female"></i> Mujer
                            <input type="radio" name="intSexo" value="0">
                            <span class="rbCheckmark"></span>
                        </label>
                    </div>
                    <div class="col-md-12 col-xs-12 col-sm-12">
                        <hr/>
                    </div>
                    <div class="col-md-12 col-xs-12 col-sm-12">
                       <div class="btn-group btn-group-justified" role="group" aria-label="...">
                            <div class="btn-group" role="group" style="display: none;">
                                <button type="submit" id="btnTab1_submit" class="btn" style="color: white;">
                                     <i class="fas fa-save"></i> Guardar
                                </button>
                             </div>
                             <div class="btn-group" role="group">
                                  <button type="submit" class="btn" onclick="fnTab_1_finish();" style="color: white;">
                                     Siguiente <i class="fas fa-angle-right"></i>
                                 </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span><span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
           <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span><span class="sr-only">Next</span>
        </a>
        </div>
<script>
  var bitTab1Finish = false;
  function fnTab_1_finish() {
    bitTab1Finish = true;
  }
    $( document ).ready(function() {
      $('input:radio[name="intSexo"]').filter('[value="<?php echo ($intSexo==1?'1':'0'); ?>"]').click();
        $('.carousel').carousel();
        setTimeout(function () { $('.carousel').carousel('pause'); }, 500);
        setTimeout(function () { $('.carousel').carousel('pause'); }, 1000);
        setTimeout(function () { $('.carousel').carousel('pause'); }, 1500);
        setTimeout(function () {
          $("#frm_tab_1").submit(function(e) {
              e.preventDefault(); // avoid to execute the actual submit of the form.
              var form = $(this);
              var url = form.attr('action');
              $.ajax({
                     type: "POST",
                     url: url,
                     data: form.serialize(), // serializes the form's elements.
                     success: function(data)
                     {
                      if (bitTab1Finish)fnTabNavRedirect(2);
                        console.log('data'); // show response from the php script.
                        // console.log(data); // show response from the php script.
                     }
                   });
          });
        }, 2000);        
        $('#carousel-example-generic').on('slide.bs.carousel', function () {
          $('#btnTab1_submit').click();
          $('#carousel-example-generic').carousel('pause');
        })
        $('#carousel-example-generic').on('slid', '', checkitem);  // on caroussel move
        $('#carousel-example-generic').on('slid.bs.carousel', '', checkitem); // on carousel move

      checkitem();
    });

    function checkitem()                        // check function
    {
        var $this = $('#carousel-example-generic');
        if($('.carousel-inner .item:first').hasClass('active')) {
            $this.children('.left.carousel-control').hide();
            $this.children('.right.carousel-control').show();
        } else if($('.carousel-inner .item:last').hasClass('active')) {
            $this.children('.left.carousel-control').show();
            $this.children('.right.carousel-control').hide();
        } else {
            $this.children('.carousel-control').show();
        } 
    }

</script>

<div class="col-md-12 col-xs-12 col-sm-12" style="display: none;">
  <div class="vc_message_box vc_message_box-solid-icon vc_message_box-square vc_color-success"><div class="vc_message_box-icon"><i class="fa fa-info-circle" aria-hidden="true"></i></div><p>TU METABOLISMO BASAL ES (<?php echo ($decMetabolismo==null || $decMetabolismo==0?'Primero ingresa los datos anteriores.':$decMetabolismo)?>)</p>
</div>
</div>    


</form>
<?php
}

?>