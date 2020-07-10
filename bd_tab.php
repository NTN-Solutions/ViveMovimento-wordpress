<?php
function fnTab_bd_cargar(){
  global $wpdb,$listAlimentos,$listUM,$strUsuario;
  // $listAlimentos = $wpdb->get_results("SELECT DISTINCT ap.*, um.strUnidadMedida FROM wp_vivemov_alimentos_porciones ap INNER JOIN wp_vivemov_alimentos_unidad_medida um ON um.intId = ap.intUnidadMedida WHERE ap.bitActivo=1 ORDER BY ap.strAlimento asc");

  //   proteina  amarillo
  // carbs naranja
  // grasas  azul
  // proteina + grasa  rojo
  // proteina + carbs  morado
  // carbs + grasa verde
  // Proteina + carbs + grasa  gris
  // libre rosado
  $listAlimentos = $wpdb->get_results("SELECT DISTINCT *
    FROM (
      SELECT DISTINCT ap.*, um.strUnidadMedida
      ,CASE
          WHEN IFNULL(decProteina,0) > 0 AND IFNULL(decCarbohidratos,0) = 0 AND IFNULL(decGrasa,0) = 0 AND IFNULL(decVegetales,0) = 0 AND IFNULL(decLibre,0) = 0 THEN 1
          WHEN IFNULL(decProteina,0) > 0 AND IFNULL(decCarbohidratos,0) = 0 AND IFNULL(decGrasa,0) > 0 AND IFNULL(decVegetales,0) = 0 AND IFNULL(decLibre,0) = 0 THEN 6
          WHEN IFNULL(decProteina,0) > 0 AND IFNULL(decCarbohidratos,0) > 0 AND IFNULL(decGrasa,0) = 0 AND IFNULL(decVegetales,0) = 0 AND IFNULL(decLibre,0) = 0 THEN 7
          WHEN IFNULL(decProteina,0) > 0 AND IFNULL(decCarbohidratos,0) > 0 AND IFNULL(decGrasa,0) > 0 AND IFNULL(decVegetales,0) = 0 AND IFNULL(decLibre,0) = 0 THEN 9
          WHEN IFNULL(decCarbohidratos,0) > 0 AND IFNULL(decGrasa,0) = 0 AND IFNULL(decVegetales,0) = 0 AND IFNULL(decLibre,0) = 0 THEN 2
          WHEN IFNULL(decCarbohidratos,0) > 0 AND IFNULL(decGrasa,0) > 0 AND IFNULL(decVegetales,0) = 0 AND IFNULL(decLibre,0) = 0 THEN 8
          WHEN IFNULL(decGrasa,0) > 0 AND IFNULL(decVegetales,0) = 0 AND IFNULL(decLibre,0) = 0 THEN 3
          WHEN IFNULL(decVegetales,0) > 0 AND IFNULL(decLibre,0) = 0 THEN 4
          WHEN IFNULL(decLibre,0) > 0 THEN 5
          ELSE 1
      END intOrdenTipo
      FROM wp_vivemov_alimentos_porciones ap
      INNER JOIN wp_vivemov_alimentos_unidad_medida um ON um.intId = ap.intUnidadMedida WHERE ap.bitActivo=1 and ap.strUsuario IN('svc9304','anamoralescpt','amms24')
      UNION ALL
      SELECT DISTINCT ap.*, um.strUnidadMedida 
      ,CASE
          WHEN IFNULL(decProteina,0) > 0 AND IFNULL(decCarbohidratos,0) = 0 AND IFNULL(decGrasa,0) = 0 AND IFNULL(decVegetales,0) = 0 AND IFNULL(decLibre,0) = 0 THEN 1
          WHEN IFNULL(decProteina,0) > 0 AND IFNULL(decCarbohidratos,0) = 0 AND IFNULL(decGrasa,0) > 0 AND IFNULL(decVegetales,0) = 0 AND IFNULL(decLibre,0) = 0 THEN 6
          WHEN IFNULL(decProteina,0) > 0 AND IFNULL(decCarbohidratos,0) > 0 AND IFNULL(decGrasa,0) = 0 AND IFNULL(decVegetales,0) = 0 AND IFNULL(decLibre,0) = 0 THEN 7
          WHEN IFNULL(decProteina,0) > 0 AND IFNULL(decCarbohidratos,0) > 0 AND IFNULL(decGrasa,0) > 0 AND IFNULL(decVegetales,0) = 0 AND IFNULL(decLibre,0) = 0 THEN 9
          WHEN IFNULL(decCarbohidratos,0) > 0 AND IFNULL(decGrasa,0) = 0 AND IFNULL(decVegetales,0) = 0 AND IFNULL(decLibre,0) = 0 THEN 2
          WHEN IFNULL(decCarbohidratos,0) > 0 AND IFNULL(decGrasa,0) > 0 AND IFNULL(decVegetales,0) = 0 AND IFNULL(decLibre,0) = 0 THEN 8
          WHEN IFNULL(decGrasa,0) > 0 AND IFNULL(decVegetales,0) = 0 AND IFNULL(decLibre,0) = 0 THEN 3
          WHEN IFNULL(decVegetales,0) > 0 AND IFNULL(decLibre,0) = 0 THEN 4
          WHEN IFNULL(decLibre,0) > 0 THEN 5
          ELSE 1
      END intOrdenTipo
      FROM wp_vivemov_alimentos_porciones ap
      INNER JOIN wp_vivemov_alimentos_unidad_medida um ON um.intId = ap.intUnidadMedida WHERE ap.bitActivo=1 and ap.strUsuario IN('".$strUsuario."')
  )ap
  ORDER BY intOrdenTipo ASC, ap.strAlimento ASC");
  $listUM = $wpdb->get_results("SELECT * FROM wp_vivemov_alimentos_unidad_medida WHERE bitActivo = 1 ORDER BY strUnidadMedida ASC;");
}
function fnTab_bd_save(){
  global $wpdb,$intA_Id,$intA_Cantidad,$intA_UM,$strA_Alimento,$decA_Proteina,$decA_Carbs,$decA_Grasa,$decA_Libre,$strUsuario;
  $registro = array(
    'decPorcion'        => $intA_Cantidad,
    'intUnidadMedida'   => $intA_UM,
    'strAlimento'       => $strA_Alimento,
    'decProteina'       => $decA_Proteina,
    'decCarbohidratos'  => $decA_Carbs,
    'decGrasa'          => $decA_Grasa,
    'decLibre'          => $decA_Libre,
    'bitActivo'         => true,
    'strUsuario'         => $strUsuario,
  );
  if($intA_Id == 0){
      $response = $wpdb->insert("wp_vivemov_alimentos_porciones", $registro);
      if($response) {
        $intA_Id = null;
        $intA_Cantidad = null;
        $intA_UM = null;
        $strA_Alimento = null;
        $decA_Proteina = null;
        $decA_Carbs = null;
        $decA_Grasa = null;
        $decA_Libre = null;
        $_POST = array();
      }
  }else{
    $buscar = $wpdb->get_results("SELECT * FROM wp_vivemov_alimentos_porciones WHERE intId = ".$intA_Id);
    $buscar = $buscar[0];
    $where = array(
      'intId' => $intA_Id
    );
    $wpdb->update( 'wp_vivemov_alimentos_porciones', $registro, $where, null, null );
  }
}

function fnTab_BD(){
  global $wpdb,$strUsuario,$bitPermiso,$listAlimentos,$bdItem;
  global $intA_Id,$intA_Cantidad,$intA_UM,$strA_Alimento,$decA_Proteina,$decA_Carbs,$decA_Grasa,$decA_Libre,$listUM;
  $intA_Id = 0;
  $bitPermiso = true;//ana pide que usuarios puedan agregar su base datos
  $strUsuario = wp_get_current_user()->user_login;
  if (isset($_GET['action']) && $_GET['action'] == 'tab_Paso_6' && isset($_POST['intOp']) && $_POST['intOp'] != null && $_POST['intOp'] == '1') {    
    $intA_Id = number_format($_POST['intA_Id'], 2);
    $intA_Cantidad = number_format($_POST['intA_Cantidad'], 2);
    $intA_UM = number_format($_POST['intA_UM'], 2);
    $strA_Alimento = $_POST['strA_Alimento'];

    $decA_Proteina = (isset($_POST['decA_Proteina']) && $_POST['decA_Proteina'] != null ? number_format($_POST['decA_Proteina'], 2) : null);
    $decA_Carbs = (isset($_POST['decA_Carbs']) && $_POST['decA_Carbs'] != null ? number_format($_POST['decA_Carbs'], 2) : null);
    $decA_Grasa = (isset($_POST['decA_Grasa']) && $_POST['decA_Grasa'] != null ? number_format($_POST['decA_Grasa'], 2) : null);
    $decA_Libre = (isset($_POST['decA_Libre']) && $_POST['decA_Libre'] != null ? number_format($_POST['decA_Libre'], 2) : null);
    fnTab_bd_save();
    echo fnMensaje(1,'Guardado!');

    $intA_Id = null;
    $intA_Cantidad = null;
    $intA_UM = null;
    $strA_Alimento = null;
    $decA_Proteina = null;
    $decA_Carbs = null;
    $decA_Grasa = null;
    $decA_Libre = null;

  }else if (isset($_GET['action']) && $_GET['action'] == 'tab_Paso_6' && isset($_POST['intOp']) && $_POST['intOp'] != null && $_POST['intOp'] == '2') {
    $bdItem = $wpdb->get_results("SELECT * FROM wp_vivemov_alimentos_porciones WHERE intId = ".$_POST['intId']);
    $bdItem = $bdItem[0];

    $intA_Id = $bdItem->intId;
    $intA_Cantidad = $bdItem->decPorcion;
    $intA_UM = $bdItem->intUnidadMedida;
    $strA_Alimento = $bdItem->strAlimento;
    $decA_Proteina = $bdItem->decProteina;
    $decA_Carbs = $bdItem->decCarbohidratos;
    $decA_Grasa = $bdItem->decGrasa;
    $decA_Libre = $bdItem->decLibre;
  }else if (isset($_GET['action']) && $_GET['action'] == 'tab_Paso_6' && isset($_POST['intOp']) && $_POST['intOp'] != null && $_POST['intOp'] == '3') {
    $decId = intval($_POST['intId']);
    $registro = array('bitActivo' => false);
    $where = array('intId' => $decId);
    $wpdb->update( 'wp_vivemov_alimentos_porciones', $registro, $where, null, null);
  }
  fnTab_bd_cargar();
  // if ($bitPermiso == true) {
    fnTab_bd_form_nuevo($listUM);
  // }

  fnTab_bd_tabla($listAlimentos, $bitPermiso, $strUsuario);
}

function fnTab_bd_tabla($listAlimentos, $bitPermiso, $strUsuario){ ?>
  <div class="table-responsive">
    <table id="tblAlimentos" class="table table-striped table-condensed">
      <tr>
        <?php if($bitPermiso == true){ echo '<td class="col-md-1 col-xs-1 col-sm-1">Accion</td>'; } ?>
        <th class="blanco">Porcion</th>
        <th class="blanco">Alimento</th>
        <th class="amarillo">Proteina</th>
        <th class="naranja">Carbohidrato</th>
        <th class="celeste">Grasa</th>
        <th class="rosado">Libre</th>
      </tr>
      <?php
      foreach ($listAlimentos as $item) {
        $strColor = '';        
        if($item->decProteina > 0 && $item->decCarbohidratos > 0 && $item->decGrasa > 0){
          $strColor = 'gris';
        }else if($item->decProteina > 0 && $item->decGrasa > 0){
          $strColor = 'rojo';
        }else if($item->decProteina > 0 && $item->decCarbohidratos > 0){
          $strColor = 'morado';
        }else if($item->decCarbohidratos > 0 && $item->decGrasa > 0){
          $strColor = 'verde';
        }else if($item->decProteina > 0){
          $strColor = 'amarillo';
        }else if($item->decCarbohidratos > 0){
          $strColor = 'naranja';
        }else if($item->decGrasa > 0){
          $strColor = 'celeste';
        }else if($item->decLibre > 0){
          $strColor = 'rosado';
        }
        $strEditar = '';
        $bitPermiso = false;

        if ($strUsuario != $item->strUsuario && ($item->strUsuario == 'svc9304' || $item->strUsuario == 'anamoralescpt' || $item->strUsuario == 'amms24')) {
            $bitPermiso = false;
        }else if ($item->strUsuario == $item->strUsuario) {
            $bitPermiso = true;
        }

        if($bitPermiso == true){
          $strEditar = '
          <td style="display: flex;">
          <form action="'.strtok($_SERVER["REQUEST_URI"],'?').'?action=tab_Paso_6" method="post" style="margin: 0px;">
          <input type="hidden" name="intOp" value="2">
          <input type="hidden" name="intId" value="'.$item->intId.'">
          <button type="submit" name="submit" class="btn" style="color: white;">
          <i class="fas fa-pen"></i>
          </button>
          </form>

          <form action="'.strtok($_SERVER["REQUEST_URI"],'?').'?action=tab_Paso_6" method="post" style="margin: 0px;">
          <input type="hidden" name="intOp" value="3">
          <input type="hidden" name="intId" value="'.$item->intId.'">
          <button type="submit" name="submit" class="btn" style="color: white;">
          <i class="fas fa-trash"></i>
          </button>
          </form>
          </td>';
        }else{
          $strEditar = '<td style="display: flex;"></td>';
        }

        echo '<tr>'.$strEditar.'
        <td class="'.$strColor.'">'.$item->decPorcion.' '.$item->strUnidadMedida.'</td>
        <td class="'.$strColor.'">'.$item->strAlimento.'</td>
        <td class="amarillo">'.($item->decProteina > 0 ? $item->decProteina : '').'</td>
        <td class="naranja">'.($item->decCarbohidratos > 0 ? $item->decCarbohidratos : '').'</td>
        <td class="celeste">'.($item->decGrasa > 0 ? $item->decGrasa : '').'</td>
        <td class="rosado">'.($item->decLibre > 0 ? $item->decLibre : '').'</td>
        </tr>
        ';
      }
      ?>
    </table>
  </div>
    <?php
  } 

  function fnTab_bd_form_nuevo($listUM){
    global $intA_Id,$intA_Cantidad,$intA_UM,$strA_Alimento,$decA_Proteina,$decA_Carbs,$decA_Grasa,$decA_Libre;
    ?>
    <form action="<?php echo strtok($_SERVER["REQUEST_URI"],'?'); ?>?action=tab_Paso_6" method="post" class="row">
      <input type="hidden" name="intOp" value="1">
      <div class="col-md-3 col-xs-6 col-sm-6">
        <label for="intA_Cantidad">Cantidad <strong>*</strong></label>
        <input type="number" step="0.01" name="intA_Cantidad" value='<?php echo $intA_Cantidad; ?>'>
      </div>

      <div class="col-md-3 col-xs-6 col-sm-6">
        <label for="intA_UM" style="display: block;">Unidad Medida <strong>*</strong></label>
        <select name="intA_UM" style="width: 100%">
          <?php
          foreach ($listUM as $um) {
            echo '<option value="'.$um->intId.'" '.($um->intId == $intA_UM? 'selected' : '').'>'.$um->strUnidadMedida.'</option>';
          }
          ?>
        </select>
      </div>
      <input type="hidden" value = "<?php echo $intA_Id ?>" name="intA_Id"/>
      <div class="col-md-3 col-xs-12 col-sm-12">
        <label for="strA_Alimento">Alimento <strong>*</strong></label>
        <input type="text" name="strA_Alimento" value='<?php echo $strA_Alimento;?> '>
      </div>

      <div class="col-md-3 col-xs-6 col-sm-6">
        <label for="decA_Proteina">Proteina </label>
        <input type="number" step="0.01" name="decA_Proteina" value='<?php echo ($decA_Proteina > 0 ? $decA_Proteina : ''); ?>'>
      </div>

      <div class="col-md-3 col-xs-6 col-sm-6">
        <label for="decA_Carbs">Carbohidratos</label>
        <input type="number" step="0.01" name="decA_Carbs" value='<?php echo ($decA_Carbs > 0 ? $decA_Carbs : ''); ?>'>
      </div>

      <div class="col-md-3 col-xs-6 col-sm-6">
        <label for="decA_Grasa">Grasa</label>
        <input type="number" step="0.01" name="decA_Grasa" value='<?php echo ($decA_Grasa > 0 ? $decA_Grasa : ''); ?>'>
      </div>

      <div class="col-md-3 col-xs-6 col-sm-6">
        <label for="decA_Libre">Libre</label>
        <input type="number" step="0.01" name="decA_Libre" value='<?php echo ($decA_Libre > 0 ? $decA_Libre : ''); ?>'>
      </div>

      <input type="submit" name="submit" value='<?php echo ($intA_Id == 0 ? 'Nuevo' : 'Guardar'); ?>' class="col-md-3 col-xs-12 col-sm-12" style="margin-top: 25px;"/>
    </form>

  <?php } ?>
