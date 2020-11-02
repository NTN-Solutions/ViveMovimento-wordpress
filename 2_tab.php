<?php
function fnTab_2_cargar(){
  global $wpdb,$intActividadTipo;
  $strUsuario = fnViveMovimento_usuario();
  try {
    $buscar = get_results("SELECT * FROM wp_vivemov_users_actividad_gasto_energetico WHERE strUsuario = '$strUsuario' ORDER BY decId DESC LIMIT 1;");
    if (count($buscar) > 0) {
      $buscar = $buscar[0];
      $intActividadTipo = $buscar['intActividad'];
    }else{
      $intActividadTipo = 1;
    }
  } catch (Exception $e) {
    echo $e;
  }
}
function fnTab_2_save($strUsuario,$intActividadTipo){
    $registro = array(
        'strUsuario'    =>   $strUsuario,
        'intActividad'  =>   $intActividadTipo
    );
    global $wpdb;
    $response = fn_insert("wp_vivemov_users_actividad_gasto_energetico", $registro);
    if($response) {
      $_POST = array();
      echo fnMensaje(1,'Listo, guardado!');
    } else {
      echo fnMensaje(2,'Inconvenientes, datos no guardados!');
    }
}
function fnTab_2(){
  global $strUsuario, $decMetabolismo, $intActividadTipo, $decActivityFactor;
  // $decActivityFactor = array(0,1.12,1.375,1.425,1.55,1.725,1.9);
  // $decActivityFactor = array(0,1.12,1.375,1.425,1.55,1.725,1.9);
  $decActivityFactor = array(0,1.12,1.375,1.425,1.55,1.725,1.9,1.425);//la ultima es semi activo (2), la agrego a ultima hora

  $strUsuario = fnViveMovimento_usuario();
  if (isset($_GET['action']) && $_GET['action'] == 'tab_Paso_2' && isset($_POST['txtForm_2']) && $_POST['txtForm_2'] != null && $_POST['txtForm_2'] != '') {
      $intActividadTipo = intval($_POST['intActividadTipo']);
      fnTab_2_save($strUsuario,$intActividadTipo);
  }
  fnTab_2_cargar();
  // fnMiInformacion_cargar($strUsuario);
   fnTab_2_form($strUsuario,$intActividadTipo,$decMetabolismo,$decActivityFactor);
}

function fnTab_2_form($strUsuario,$intActividadTipo,$decMetabolismo,$decActivityFactor){ ?>
   <div class="row" style="padding: 0px;">
     <div class="col-md-12 col-xs-12 col-sm-12" style="padding: 0px;">
        <div class="vc_message_box vc_message_box-solid-icon vc_message_box-square vc_color-success">
          <div class="vc_message_box-icon"><i class="fa fa-info" aria-hidden="true"></i></div>
            <p><?php echo $strUsuario; ?> “elige tu actividad física”, que tan activo eres? Escoge la casilla que más se asimilan a tu día a día. </p>
        </div>
    </div>
  </div>

<form id="frm_tab_2" action="<?php echo strtok($_SERVER["REQUEST_URI"],'?')?>?action=tab_Paso_2" method="post" class="row">
<input type="hidden" value="listo" name="txtForm_2" />
  <div class="table-responsive">  
    <table class="table table-striped table-condensed">
      <thead>
        <tr>
          <th></th>
          <th>Actividad</th>
          <th>Ejercicio</th>
          <th>Ejemplo</th>
          <th class="noMostrar">Gasto Energético</th>
          <th class="noMostrar">Activity Factor</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row">
            <label class="rbContainer"><i class="fa fa-battery-empty"></i> SEDENTARIO
              <input type="radio" name="intActividadTipo" value="1" <?php echo ($intActividadTipo == 1 ? 'checked="checked"': ''); ?> >
              <span class="rbCheckmark"></span>
            </label>
          </th>
          <td>Trabajo de oficina, minima actividad fisica *actividad fisica incluye caminar, subir y bajar escaleras, estar en constante movimiento</td>
          <td>0 ejercicio *ejercicio refiere a disciplinas constantes como hacer pesas, cardio, yoga, ejercicio funcional, Crossfit, etc</td>
          <td>Oficina 8hrs al dia, netflix, dormir, manejar, el resto del dia</td>
          <td class="noMostrar"><?php echo ($decMetabolismo * $decActivityFactor[$intActividadTipo]) ?></td>
          <td class="noMostrar"><?php echo $decActivityFactor[0]; ?></td>
        </tr>
        <tr class="active">
          <th scope="row">
            <label class="rbContainer"><i class="fa fa-battery-quarter"></i> SEMI-SEDENTARIO
              <input type="radio" name="intActividadTipo" value="2" <?php echo ($intActividadTipo == 2 ? 'checked="checked"': ''); ?>>
              <span class="rbCheckmark"></span>
            </label>
          </th>
          <td>Trabajo de oficina,minima actividad fisica ademas del ejercicio </td>
          <td>ejercicio 1hr hasta 3 veces por semana</td>
          <td>Oficina 8hrs al dia, al salir de la oficina atiende clases de ejercicios funcionales de 5:30 a 6:30pm 3 veces por semana</td>
          <td class="noMostrar"><?php echo ($decMetabolismo * $decActivityFactor[$intActividadTipo]) ?></td>
          <td class="noMostrar"><?php echo $decActivityFactor[$intActividadTipo]; ?></td>
        </tr>
        <tr class="info">
          <th scope="row">
            <label class="rbContainer"><i class="fa fa-battery-half"></i> SEMI-ACTIVO (1)
              <input type="radio" name="intActividadTipo" value="3" <?php echo ($intActividadTipo == 3 ? 'checked="checked"': ''); ?>>
              <span class="rbCheckmark"></span>
            </label>            
          </th>
          <td>Trabajo de oficina,minima actividad fisica ademas del ejercicio </td>
          <td>ejercicio 1hr 4 veces o mas por semana</td>
          <td>Oficina 8hrs al dia, al salir de la oficina atiende clases de ejercicios funcionales de 5:30 a 6:30pm 5-6 veces por semana</td>
          <td class="noMostrar"><?php echo ($decMetabolismo * $decActivityFactor[$intActividadTipo]) ?></td>
          <td class="noMostrar"><?php echo $decActivityFactor[$intActividadTipo]; ?></td>
        </tr>
        <tr class="info">
          <th scope="row">
            <label class="rbContainer"><i class="fa fa-battery-half"></i> SEMI-ACTIVO (2)
              <input type="radio" name="intActividadTipo" value="7" <?php echo ($intActividadTipo == 7 ? 'checked="checked"': ''); ?>>
              <span class="rbCheckmark"></span>
            </label>            
          </th>
          <td>Trabajo que requiere constante movimiento  </td>
          <td>ejercicio 1hr hasta 3 veces por semana</td>
          <td>Profesor, supervisor de proyecto, constante movimiento durante el dia, adicional ejercicio 1hr 4 veces o mas por semana</td>
          <td class="noMostrar"><?php echo ($decMetabolismo * $decActivityFactor[$intActividadTipo]) ?></td>
          <td class="noMostrar"><?php echo $decActivityFactor[$intActividadTipo]; ?></td>
        </tr>
        <tr class="success">
          <th scope="row">
            <label class="rbContainer"><i class="fa fa-battery-three-quarters"></i> MODERADAMENTE ACTIVO
              <input type="radio" name="intActividadTipo" value="4" <?php echo ($intActividadTipo == 4 ? 'checked="checked"': ''); ?>>
              <span class="rbCheckmark"></span>
            </label>
          </th>
          <td>Trabajo que requiere constante movimiento</td>
          <td>ejercicio 1hr 4 veces o mas por semana</td>
          <td>profesor, supervisor de proyecto, constante movimiento durante el dia, adicional ejercicio 1hr 4 veces o mas por semana</td>
          <td class="noMostrar"><?php echo ($decMetabolismo * $decActivityFactor[$intActividadTipo]) ?></td>
          <td class="noMostrar"><?php echo $decActivityFactor[$intActividadTipo]; ?></td>
        </tr>
        <tr class="warning">
          <th scope="row">
            <label class="rbContainer"><i class="fa fa-battery-full"></i> MUY ACTIVO
              <input type="radio" name="intActividadTipo" value="5" <?php echo ($intActividadTipo == 5 ? 'checked="checked"': ''); ?>>
              <span class="rbCheckmark"></span>
            </label>            
          </th>
          <td>Trabajo de oficina</td>
          <td>ejercicio mas de 1hr o dos sesiones al dia 5x o mas por semana</td>
          <td>trabajo de oficina, crossfit o gym + cardio consistente</td>
          <td class="noMostrar"><?php echo ($decMetabolismo * $decActivityFactor[$intActividadTipo]) ?></td>
          <td class="noMostrar"><?php echo $decActivityFactor[$intActividadTipo]; ?></td>
        </tr>
        <tr class="danger">
          <th scope="row">            
            <label class="rbContainer"><i class="fa fa-charging-station"></i> EXTREMADAMENTE ACTIVO
              <input type="radio" name="intActividadTipo" value="6" <?php echo ($intActividadTipo == 6 ? 'checked="checked"': ''); ?>>
              <span class="rbCheckmark"></span>
            </label>  
          </th>
          <td>Trabajo que requiere constante movimiento  </td>
          <td>ejercicio mas de 1hr o dos veces por dia 5x o mas por semana</td>
          <td>entrenador, atleta, deportista</td>
          <td class="noMostrar"><?php echo ($decMetabolismo * $decActivityFactor[$intActividadTipo]) ?></td>
          <td class="noMostrar"><?php echo $decActivityFactor[$intActividadTipo]; ?></td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="col-md-12 col-xs-12 col-sm-12">
    <div class="btn-group btn-group-justified" role="group" aria-label="...">
      <div class="btn-group" role="group">
        <button type="button" class="btn" onclick="fnTabNavRedirect(1);" style="color: white;">
          <i class="fa fa-angle-left"></i> Anterior
        </button>
      </div>
      <div class="btn-group" role="group" style="display: none;">
        <button type="submit" class="btn" style="color: white;">
          <i class="fa fa-save"></i> Guardar
        </button>
      </div>
      <div class="btn-group" role="group">
        <button type="submit" class="btn" style="color: white;">
          Siguiente <i class="fa fa-angle-right"></i>
        </button>
      </div>
    </div>
  </div>

</form>

<script>
    $( document ).ready(function() {
        setTimeout(function () {
          $("#frm_tab_2").submit(function(e) {
              e.preventDefault();
              var form = $(this);
              var url = form.attr('action');
              $.ajax({
                     type: "POST",
                     url: url,
                     data: form.serialize(), // serializes the form's elements.
                     success: function(data)
                     {
                      fnTabNavRedirect(21);
                     }
                   });
          });
        }, 1000);
    });
</script>

<?php } ?>



