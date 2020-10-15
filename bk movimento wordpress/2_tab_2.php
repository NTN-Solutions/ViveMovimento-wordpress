<?php
function fnTab_22_cargar(){
  global $wpdb,$intEjercicio;
  $strUsuario = fnViveMovimento_usuario();
  try {
    $buscar = $wpdb->get_results("SELECT * FROM wp_vivemov_users_ejercicio WHERE strUsuario = '$strUsuario' ORDER BY decId DESC LIMIT 1;");
    if (count($buscar) > 0) {
      $buscar = $buscar[0];
      $intEjercicio = $buscar->intEjercicio;
    }else{
      $intEjercicio = 1;
    }
  } catch (Exception $e) {
    echo $e;
  }
}
function fnTab_22_save($strUsuario,$intEjercicio){
    $registro = array(
        'strUsuario'    =>   $strUsuario,
        'intEjercicio'  =>   $intEjercicio
    );
    global $wpdb;
    $response = $wpdb->insert("wp_vivemov_users_ejercicio", $registro);
    if($response) {
      $_POST = array();
      echo fnMensaje(1,'Listo, guardado!');
    } else {
      echo fnMensaje(2,'Inconvenientes, datos no guardados!');
    }
}
function fnTab_22(){
  global $strUsuario,$intEjercicio;
  $strUsuario = fnViveMovimento_usuario();
  if (isset($_GET['action']) && $_GET['action'] == 'tab_Paso_22' && isset($_POST['txtForm_22']) && $_POST['txtForm_22'] != null && $_POST['txtForm_22'] != '') {
      $intEjercicio = intval($_POST['intEjercicio']);
      fnTab_22_save($strUsuario,$intEjercicio);
  }
  fnTab_22_cargar();
  // fnMiInformacion_cargar($strUsuario);
   fnTab_22_form($strUsuario,$intEjercicio);
}

function fnTab_22_form($strUsuario,$intEjercicio){ ?>
   <div class="row" style="padding: 0px;">
     <div class="col-md-12 col-xs-12 col-sm-12" style="padding: 0px;">
        <div class="vc_message_box vc_message_box-solid-icon vc_message_box-square vc_color-success">
          <div class="vc_message_box-icon"><i class="fa fa-info" aria-hidden="true"></i></div>
            <p><?php echo $strUsuario; ?> elige tu tipo de ejercicio. </p>
        </div>
    </div>
  </div>

<form id="frm_tab_22" action="<?php echo strtok($_SERVER["REQUEST_URI"],'?')?>?action=tab_Paso_22" method="post" class="row">
<input type="hidden" value="listo" name="txtForm_22" />
  <div class="table-responsive">  
    <table class="table table-striped table-condensed">
      <thead>
        <tr>
          <th>Ejercicio</th>
          <th>Descripción</th>
        </tr>
      </thead>
      <tbody>
        <tr class="info">
          <th scope="row">
            <label class="rbContainer"><i class="fas fa-battery-empty"></i> PESAS
              <input type="radio" name="intEjercicio" value="1" <?php echo ($intEjercicio == 1 ? 'checked="checked"': ''); ?> >
              <span class="rbCheckmark"></span>
            </label>
          </th>
          <th>Ejercicio de resistencia muscular con peso adicional, maquinas, mancuernas, barras, etc.</th>
        </tr>
        <tr class="active">
          <th scope="row">
            <label class="rbContainer"><i class="fas fa-battery-quarter"></i> CARDIO
              <input type="radio" name="intEjercicio" value="2" <?php echo ($intEjercicio == 2 ? 'checked="checked"': ''); ?>>
              <span class="rbCheckmark"></span>
            </label>
          </th>
          <th>Cardio HIIT (intervalos de alta intensidad), videos de ejercicios en aplicaciones móviles, máquinas de cardio (caminadora, elíptica, bicicleta), caminatas, deporte. Etc.</th>
        </tr>
        <tr class="success">
          <th scope="row">
            <label class="rbContainer"><i class="fas fa-battery-three-quarters"></i> AMBOS
              <input type="radio" name="intEjercicio" value="3" <?php echo ($intEjercicio == 3 ? 'checked="checked"': ''); ?>>
              <span class="rbCheckmark"></span>
            </label>
          </th>
          <th>Crossfit, pesas y sesiones de cardio.</th>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="col-md-12 col-xs-12 col-sm-12">
    <div class="btn-group btn-group-justified" role="group" aria-label="...">
      <div class="btn-group" role="group">
        <button type="button" class="btn" onclick="fnTabNavRedirect(21);" style="color: white;">
          <i class="fas fa-angle-left"></i> Anterior
        </button>
      </div>
      <div class="btn-group" role="group" style="display: none;">
        <button type="submit" class="btn" style="color: white;">
          <i class="fas fa-save"></i> Guardar
        </button>
      </div>
      <div class="btn-group" role="group">
        <button type="submit" class="btn" style="color: white;">
          Siguiente <i class="fas fa-angle-right"></i>
        </button>
      </div>
    </div>
  </div>

</form>

<script>
    $( document ).ready(function() {
        setTimeout(function () {
          $("#frm_tab_22").submit(function(e) {
              e.preventDefault();
              var form = $(this);
              var url = form.attr('action');
              $.ajax({
                     type: "POST",
                     url: url,
                     data: form.serialize(), // serializes the form's elements.
                     success: function(data)
                     {
                      fnTabNavRedirect(3);
                     }
                   });
          });
        }, 1000);
    });
</script>

<?php } ?>



