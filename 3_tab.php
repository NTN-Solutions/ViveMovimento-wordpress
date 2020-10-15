<?php
function fnTab_3_cargar(){
  global $wpdb,$intMeta;
  $strUsuario = fnViveMovimento_usuario();
  try {
    $buscar = get_results("SELECT * FROM wp_vivemov_users_meta WHERE strUsuario = '$strUsuario' ORDER BY decId DESC LIMIT 1;");
    if (count($buscar) > 0) {
      $buscar = $buscar[0];
      $intMeta = $buscar['intMeta'];
    }else{
      $intMeta = 1;
    }
  } catch (Exception $e) {
    echo $e;
  }
}
function fnTab_3_save($strUsuario,$intMeta){
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
function fnTab_3(){
  global $strUsuario, $intMeta;
  $strUsuario = fnViveMovimento_usuario();
  if (isset($_GET['action']) && $_GET['action'] == 'tab_Paso_3' && isset($_POST['txtForm_3']) && $_POST['txtForm_3'] != null && $_POST['txtForm_3'] != '') {
      $intMeta = intval($_POST['intMeta']);
      fnTab_3_save($strUsuario,$intMeta);
  }
  fnTab_3_cargar();
  // fnMiInformacion_cargar($strUsuario);
   fnTab_3_form($strUsuario,$intMeta);
}

function fnTab_3_form($strUsuario,$intMeta){ ?>
 <div class="row" style="padding: 0px;">
     <div class="col-md-12 col-xs-12 col-sm-12" style="padding: 0px;">
        <div class="vc_message_box vc_message_box-solid-icon vc_message_box-square vc_color-success">
          <div class="vc_message_box-icon"><i class="fa fa-info" aria-hidden="true"></i></div>
            <p>Ahora <?php echo $strUsuario; ?>, selecciona la meta que deseas alcanzar</p>
        </div>
    </div>
  </div>

<form id="frm_tab_3" action="<?php echo strtok($_SERVER["REQUEST_URI"],'?'); ?>?action=tab_Paso_3" method="post" class="row">
<input type="hidden" value="listo" name="txtForm_3" />
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
          <th scope="row">
            <label class="rbContainer"><i class="fas fa-battery-half"></i> BAJAR
              <input type="radio" name="intMeta" value="1" <?php echo ($intMeta == 1 ? 'checked="checked"': ''); ?>>
              <span class="rbCheckmark"></span>
            </label>            
          </th>
          <td>Quiero bajar grasa corporal y peso.</td>
        </tr>
        <tr class="success">
          <th scope="row">
            <label class="rbContainer"><i class="fas fa-battery-three-quarters"></i> MANTENER
              <input type="radio" name="intMeta" value="2" <?php echo ($intMeta == 2 ? 'checked="checked"': ''); ?>>
              <span class="rbCheckmark"></span>
            </label>
          </th>
          <td>Ya llegue a mi meta y quiero mantener mi peso y mi progreso.</td>
        </tr>
        <tr class="warning">
          <th scope="row">
            <label class="rbContainer"><i class="fas fa-battery-full"></i> SUBIR
              <input type="radio" name="intMeta" value="3" <?php echo ($intMeta == 3 ? 'checked="checked"': ''); ?>>
              <span class="rbCheckmark"></span>
            </label>            
          </th>
          <td>Quiero subir masa muscular y peso.</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="col-md-12 col-xs-12 col-sm-12">
    <div class="btn-group btn-group-justified" role="group" aria-label="...">
      <div class="btn-group" role="group">
        <button type="button" class="btn" onclick="fnTabNavRedirect(22);" style="color: white;">
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
          $("#frm_tab_3").submit(function(e) {
              e.preventDefault();
              var form = $(this);
              var url = form.attr('action');
              $.ajax({
                     type: "POST",
                     url: url,
                     data: form.serialize(), // serializes the form's elements.
                     success: function(data)
                     {
                      fnTabNavRedirect(4);
                     }
                   });
          });
        }, 1000);
    });
</script>

<?php } ?>



