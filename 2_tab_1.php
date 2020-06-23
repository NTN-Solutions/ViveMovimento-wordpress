<?php
function fnTab_21_cargar(){
  global $wpdb,$intExperiencia;
  $strUsuario = wp_get_current_user()->user_login;
  try {
    $buscar = $wpdb->get_results("SELECT * FROM wp_vivemov_users_experiencia WHERE strUsuario = '$strUsuario' ORDER BY decId DESC LIMIT 1;");
    if (count($buscar) > 0) {
      $buscar = $buscar[0];
      $intExperiencia = $buscar->intExperiencia;
    }else{
      $intExperiencia = 1;
    }
  } catch (Exception $e) {
    echo $e;
  }
}
function fnTab_21_save($strUsuario,$intExperiencia){
    $registro = array(
        'strUsuario'    =>   $strUsuario,
        'intExperiencia'  =>   $intExperiencia
    );
    global $wpdb;
    $response = $wpdb->insert("wp_vivemov_users_experiencia", $registro);
    if($response) {
      $_POST = array();
      echo fnMensaje(1,'Listo, guardado!');
    } else {
      echo fnMensaje(2,'Inconvenientes, datos no guardados!');
    }
}
function fnTab_21(){
  global $strUsuario,$intExperiencia;
  $strUsuario = wp_get_current_user()->user_login;
  if (isset($_GET['action']) && $_GET['action'] == 'tab_Paso_21' && isset($_POST['txtForm_21']) && $_POST['txtForm_21'] != null && $_POST['txtForm_21'] != '') {
      $intExperiencia = intval($_POST['intExperiencia']);
      fnTab_21_save($strUsuario,$intExperiencia);
  }
  fnTab_21_cargar();
  // fnMiInformacion_cargar($strUsuario);
   fnTab_21_form($strUsuario,$intExperiencia);
}

function fnTab_21_form($strUsuario,$intExperiencia){ ?>
   <div class="row" style="padding: 0px;">
     <div class="col-md-12 col-xs-12 col-sm-12" style="padding: 0px;">
        <div class="vc_message_box vc_message_box-solid-icon vc_message_box-square vc_color-success">
          <div class="vc_message_box-icon"><i class="fa fa-info" aria-hidden="true"></i></div>
            <p><?php echo $strUsuario; ?> elige tu experiencia. </p>
        </div>
    </div>
  </div>

<form id="frm_tab_21" action="<?php echo strtok($_SERVER["REQUEST_URI"],'?')?>?action=tab_Paso_21" method="post" class="row">
<input type="hidden" value="listo" name="txtForm_21" />
  <div class="table-responsive">  
    <table class="table table-striped table-condensed">
      <thead>
        <tr>
          <th></th>
          <th>Experiencia</th>
          <th>Descripción</th>
        </tr>
      </thead>
      <tbody>
        <tr class="info">
          <th scope="row">
            <label class="rbContainer"><i class="fas fa-battery-empty"></i> PRINCIPIANTE
              <input type="radio" name="intExperiencia" value="1" <?php echo ($intExperiencia == 1 ? 'checked="checked"': ''); ?> >
              <span class="rbCheckmark"></span>
            </label>
          </th>
          <td>Nunca he hecho ejercicio y dieta de manera consistente </td>
        </tr>
        <tr class="active">
          <th scope="row">
            <label class="rbContainer"><i class="fas fa-battery-quarter"></i> INTERMEDIO
              <input type="radio" name="intExperiencia" value="2" <?php echo ($intExperiencia == 2 ? 'checked="checked"': ''); ?>>
              <span class="rbCheckmark"></span>
            </label>
          </th>
          <td>He estado intermitente con el ejercicio y la dieta</td>
        </tr>
        <tr class="success">
          <th scope="row">
            <label class="rbContainer"><i class="fas fa-battery-three-quarters"></i> AVANZADO
              <input type="radio" name="intExperiencia" value="3" <?php echo ($intExperiencia == 3 ? 'checked="checked"': ''); ?>>
              <span class="rbCheckmark"></span>
            </label>
          </th>
          <td>He estado disciplinada en cuanto a la dieta y el ejercicio de manera consistente por mas de 4 meses.</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="col-md-12 col-xs-12 col-sm-12">
    <div class="btn-group btn-group-justified" role="group" aria-label="...">
      <div class="btn-group" role="group">
        <button type="button" class="btn" onclick="fnTabNavRedirect(2);" style="color: white;">
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
          $("#frm_tab_21").submit(function(e) {
              e.preventDefault();
              var form = $(this);
              var url = form.attr('action');
              $.ajax({
                     type: "POST",
                     url: url,
                     data: form.serialize(), // serializes the form's elements.
                     success: function(data)
                     {
                      fnTabNavRedirect(22);
                     }
                   });
          });
        }, 1000);
    });
</script>

<?php } ?>



