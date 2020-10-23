<?php

  function fnViveMovimentoOneToOnetabla(){
    global $wpdb;
    $list = get_results("
        SELECT *
        FROM wp_vivemov_users_one_to_one D
        WHERE bitActivo =1
    ");
    ?>
        <table class="table table-striped">
          <thead>
              <tr>
                <th>No.</th>
                <th>Usuario One to One</th>
                <th>Eliminar</th>
              </tr>
            </thead>
          <tbody>
            <?php
              $intContadoronetoone = 1;
              foreach ($list as $item) {
                echo '<tr>';
                echo '<td>';
                echo $intContadoronetoone;
                echo '</td>';
                echo '<td>';
                echo $item->strUsuario;
                echo '</td>';
                echo '<td>';
                echo '<button type="button" class="btn btn-warning btn-block btn-xs" aria-label="Eliminar" onclick="fnUsuarioonetoone_Eliminar('."'".$item->strUsuario."'".');">
              <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
            </button>';
                echo '</td>';
                echo '</tr>';
                $intContadoronetoone += 1;
              }
            ?>
          </tbody>
        </table>
    <?php
      exit();
  }
  function fnViveMovimentoOneToOneAgregar(){
      global $wpdb;
      $itemRow = array(
          'strUsuario'            => $_GET['strUsuario'],
          'bitActivo'             => 1,
      );
      insert("wp_vivemov_users_one_to_one", $itemRow);
      $result['type'] = 'success';
      $result['mnj'] = 'Listo, eliminado!';
      $result = json_encode($result);
      echo $result;
      exit();
  }
  function fnViveMovimentoOneToOneEliminar(){
      global $wpdb;
      delete( 'wp_vivemov_users_one_to_one', array( 'strUsuario' => $_GET['strUsuario'] ) );
      $result['type'] = 'success';
      $result['mnj'] = 'Listo, eliminado!';
      $result = json_encode($result);
      echo $result;
      exit();
  }


  function fnViveMovimento_resumen_data(){
    setlocale(LC_ALL, 'es_ES').': ';
    global $wpdb, $strUsuario;
    global $gDataPaso_1,$gDataPaso_2,$gDataPaso_3;
    $strUsuario = fnViveMovimento_usuario();
    $gDataPaso_1 = get_results("SELECT * FROM wp_vivemov_users_informacion WHERE strUsuario = '$strUsuario' ORDER BY intId DESC LIMIT 1;");
    $gDataPaso_2 = get_results("SELECT * FROM wp_vivemov_users_actividad_gasto_energetico WHERE strUsuario = '$strUsuario' ORDER BY decId DESC LIMIT 1;");
    $gDataPaso_3 = get_results("SELECT * FROM wp_vivemov_users_meta WHERE strUsuario = '$strUsuario' ORDER BY decId DESC LIMIT 1;");
  }
	function fnViveMovimento_resumen_init() {
    fnViveMovimento_usuario(true);
  		global $wpdb, $strUsuario;
      global $gDataPaso_1,$gDataPaso_2,$gDataPaso_3;
      $strUsuario = fnViveMovimento_usuario();
      fnViveMovimento_resumen_data();
        //$datFechaDiario = new DateTime();
      if (isset($_GET['action']) && $_GET['action'] == 'dashboard' && isset($_POST['intOpcion']) && $_POST['intOpcion'] != null && $_POST['intOpcion'] == '1') {
        $strURL_paso_1 = $_POST['decId_1'];
        $strURL_paso_2 = $_POST['decId_2'];
        $strURL_paso_3 = $_POST['decId_3'];
        $strURL_paso_4 = $_POST['decId_4'];
        $strURL_paso_5 = $_POST['decId_5'];
        $strURL_paso_6 = $_POST['decId_6'];
        $strURL_paso_7 = $_POST['decId_7'];

        $strAyuda_1 = $_POST['strAyuda_1'];
        $strAyuda_2 = $_POST['strAyuda_2'];
        $strAyuda_3 = $_POST['strAyuda_3'];
        $strAyuda_4 = $_POST['strAyuda_4'];
        $strAyuda_5 = $_POST['strAyuda_5'];
        $strAyuda_6 = $_POST['strAyuda_6'];
        $strAyuda_7 = $_POST['strAyuda_7'];

        get_results("UPDATE wp_vivemov_pasos_videos as D SET D.strURL = '$strURL_paso_1', D.strAyudaLink = '$strAyuda_1' WHERE D.decId = 1");
        get_results("UPDATE wp_vivemov_pasos_videos as D SET D.strURL = '$strURL_paso_2', D.strAyudaLink = '$strAyuda_2' WHERE D.decId = 2");
        get_results("UPDATE wp_vivemov_pasos_videos as D SET D.strURL = '$strURL_paso_3', D.strAyudaLink = '$strAyuda_3' WHERE D.decId = 3");
        get_results("UPDATE wp_vivemov_pasos_videos as D SET D.strURL = '$strURL_paso_4', D.strAyudaLink = '$strAyuda_4' WHERE D.decId = 4");
        get_results("UPDATE wp_vivemov_pasos_videos as D SET D.strURL = '$strURL_paso_5', D.strAyudaLink = '$strAyuda_5' WHERE D.decId = 5");
        get_results("UPDATE wp_vivemov_pasos_videos as D SET D.strURL = '$strURL_paso_6', D.strAyudaLink = '$strAyuda_6' WHERE D.decId = 6");
        get_results("UPDATE wp_vivemov_pasos_videos as D SET D.strURL = '$strURL_paso_7', D.strAyudaLink = '$strAyuda_7' WHERE D.decId = 7");
      }

      $bitPermisoAdmin = wp_get_current_user()['user_admin'];
      $listPasosVideos = get_results("SELECT * FROM wp_vivemov_pasos_videos ORDER BY decId ASC;");

?>

<div class="row">
  <div class="col-md-10 col-xs-12 col-sm-12  col-md-offset-1">
    <h2>Bienvenid@ <?php echo $strUsuario; ?></h2>
    <!-- <h4><?php//echo $datFechaDiario->format('D, d-M-Y'); ?></h4> -->
    <h4><?php echo iconv('ISO-8859-1', 'UTF-8', strftime('%A, %d/%B/%Y', time())); ?></h4>

    <div class="alert alert-<?php echo ($gDataPaso_1 == null ? 'warning' : 'success') ?>" role="alert">
      <strong>
        <a href="/user/?action=tab_Paso_1"> Paso 1 - </strong> Información Básica (<?php echo ($gDataPaso_1 == null ? 'PENDIENTE' : 'Completada') ?>)
        </a>
        <?php if ($listPasosVideos != null && $listPasosVideos[0]->strURL != null && $listPasosVideos[0]->strURL != '') {
          echo '<span style="float: right;cursor: pointer;" onClick="fnPasosMostrarVideo('."'".$listPasosVideos[0]->strURL."'".');"><i class="fa fa-video-camera" aria-hidden="true"></i></span>';
        } ?>
        <?php if ($listPasosVideos != null && $listPasosVideos[0]->strAyudaLink != null && $listPasosVideos[0]->strAyudaLink != '') {
          echo '<a style="float: right;cursor: pointer;" href="'.$listPasosVideos[0]->strAyudaLink.'"><i class="fa fa-question-circle" aria-hidden="true"></i></a>';
        } ?>
    </div>

    <div class="alert alert-<?php echo ($gDataPaso_2 == null ? 'warning' : 'success') ?>" role="alert">
      <strong>
        <a href="/user/?action=tab_Paso_2"> Paso 2 - </strong> Gasto Energético (<?php echo ($gDataPaso_2 == null ? 'PENDIENTE' : 'Completada') ?>)
        </a>
        <?php if ($listPasosVideos != null && $listPasosVideos[1]->strURL != null && $listPasosVideos[1]->strURL != '') {
          echo '<span style="float: right;cursor: pointer;" onClick="fnPasosMostrarVideo('."'".$listPasosVideos[1]->strURL."'".');"><i class="fa fa-video-camera" aria-hidden="true"></i></span>';
        } ?>
        <?php if ($listPasosVideos != null && $listPasosVideos[1]->strAyudaLink != null && $listPasosVideos[1]->strAyudaLink != '') {
          echo '<a style="float: right;cursor: pointer;" href="'.$listPasosVideos[1]->strAyudaLink.'"><i class="fa fa-question-circle" aria-hidden="true"></i></a>';
        } ?>
    </div>

    <div class="alert alert-<?php echo ($gDataPaso_3 == null ? 'warning' : 'success') ?>" role="alert">
      <strong>
        <a href="/user/?action=tab_Paso_3"> Paso 3 - </strong> Metas (<?php echo ($gDataPaso_3 == null ? 'PENDIENTE' : 'Completada') ?>)
        </a>
        <?php if ($listPasosVideos != null && $listPasosVideos[2]->strURL != null && $listPasosVideos[2]->strURL != '') {
          echo '<span style="float: right;cursor: pointer;" onClick="fnPasosMostrarVideo('."'".$listPasosVideos[2]->strURL."'".');"><i class="fa fa-video-camera" aria-hidden="true"></i></span>';
        } ?>
        <?php if ($listPasosVideos != null && $listPasosVideos[2]->strAyudaLink != null && $listPasosVideos[2]->strAyudaLink != '') {
          echo '<a style="float: right;cursor: pointer;" href="'.$listPasosVideos[2]->strAyudaLink.'"><i class="fa fa-question-circle" aria-hidden="true"></i></a>';
        } ?>
    </div>

    <div class="alert alert-info" role="alert">
      <strong>
        <a href="/user/?action=tab_Paso_4"> Paso 4 - </strong> Conoce tu Plan Nutricional
        </a>
        <?php if ($listPasosVideos != null && $listPasosVideos[3]->strURL != null && $listPasosVideos[3]->strURL != '') {
          echo '<span style="float: right;cursor: pointer;" onClick="fnPasosMostrarVideo('."'".$listPasosVideos[3]->strURL."'".');"><i class="fa fa-video-camera" aria-hidden="true"></i></span>';
        } ?>
        <?php if ($listPasosVideos != null && $listPasosVideos[3]->strAyudaLink != null && $listPasosVideos[3]->strAyudaLink != '') {
          echo '<a style="float: right;cursor: pointer;" href="'.$listPasosVideos[3]->strAyudaLink.'"><i class="fa fa-question-circle" aria-hidden="true"></i></a>';
        } ?>
    </div>
    <div class="alert alert-info" role="alert">
      <strong>
        <a href="/user/?action=tab_Paso_8"> Paso 5 - </strong> Elige tu Plan de Ejercicios
        </a>
        <?php if ($listPasosVideos != null && $listPasosVideos[4]->strURL != null && $listPasosVideos[4]->strURL != '') {
          echo '<span style="float: right;cursor: pointer;" onClick="fnPasosMostrarVideo('."'".$listPasosVideos[4]->strURL."'".');"><i class="fa fa-video-camera" aria-hidden="true"></i></span>';
        } ?>
        <?php if ($listPasosVideos != null && $listPasosVideos[4]->strAyudaLink != null && $listPasosVideos[4]->strAyudaLink != '') {
          echo '<a style="float: right;cursor: pointer;" href="'.$listPasosVideos[4]->strAyudaLink.'"><i class="fa fa-question-circle" aria-hidden="true"></i></a>';
        } ?>
    </div>
    <div class="alert alert-info" role="alert">
      <strong>
        <a href="/user/?action=tab_Paso_5"> Paso 6 - </strong> Ingresa tus comidas en tu Food Journal
        </a>
        <?php if ($listPasosVideos != null && $listPasosVideos[5]->strURL != null && $listPasosVideos[5]->strURL != '') {
          echo '<span style="float: right;cursor: pointer;" onClick="fnPasosMostrarVideo('."'".$listPasosVideos[5]->strURL."'".');"><i class="fa fa-video-camera" aria-hidden="true"></i></span>';
        } ?>
        <?php if ($listPasosVideos != null && $listPasosVideos[5]->strAyudaLink != null && $listPasosVideos[5]->strAyudaLink != '') {
          echo '<a style="float: right;cursor: pointer;" href="'.$listPasosVideos[5]->strAyudaLink.'"><i class="fa fa-question-circle" aria-hidden="true"></i></a>';
        } ?>
    </div>
    <div class="alert alert-info" role="alert">
      <strong>
        <a href="/user/?action=tab_Paso_7"> Paso 7 - </strong> Lleva control de tu progreso
        </a>
        <?php if ($listPasosVideos != null && $listPasosVideos[6]->strURL != null && $listPasosVideos[6]->strURL != '') {
          echo '<span style="float: right;cursor: pointer;" onClick="fnPasosMostrarVideo('."'".$listPasosVideos[6]->strURL."'".');"><i class="fa fa-video-camera" aria-hidden="true"></i></span>';
        } ?>
        <?php if ($listPasosVideos != null && $listPasosVideos[6]->strAyudaLink != null && $listPasosVideos[6]->strAyudaLink != '') {
          echo '<a style="float: right;cursor: pointer;" href="'.$listPasosVideos[6]->strAyudaLink.'"><i class="fa fa-question-circle" aria-hidden="true"></i></a>';
        } ?>
    </div>
  </div>
</div>

<?php 
  // if (!isset($_GET['infoUsuario']) || $_GET['infoUsuario'] == null || $_GET['infoUsuario'] == '') {
  //   if ($gDataPaso_1 == null) {
  //     echo '<script>window.location.href = "/user/?action=tab_Paso_1";</script>';
  //   }else if ($gDataPaso_2 == null) {
  //     echo '<script>window.location.href = "/user/?action=tab_Paso_2";</script>';
  //   }else if ($gDataPaso_3 == null) {
  //     echo '<script>window.location.href = "/user/?action=tab_Paso_3";</script>';
  //   }
  // }
  if ($bitPermisoAdmin == true) {
?>
<div class="row">
  <div class="col-md-10 col-xs-12 col-sm-12  col-md-offset-1">
    <form action="<?php echo strtok($_SERVER["REQUEST_URI"],'?');?>?action=dashboard" method="post" class="row">
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
              <tr>
                <th>Paso</th>
                <th>URL Video</th>
                <th>URL Ayuda</th>
              </tr>
            </thead>
          <tbody>
              <input type="hidden" name="intOpcion" value="1">
            <?php
              $intContadorPasos = 1;
              foreach ($listPasosVideos as $key) {
                echo '<tr class="info">
                        <td>'.$intContadorPasos.'</td>
                        <td><input type="text" class="form-control" name="decId_'.$intContadorPasos.'" placeholder="Url youtube" value="'.$key->strURL.'"></td>
                        <td><input type="text" class="form-control" name="strAyuda_'.$intContadorPasos.'" placeholder="Link pagina ayuda" value="'.$key->strAyudaLink.'"></td>
                      </tr>';
                $intContadorPasos += 1;
              }
            ?>
          </tbody>
        </table>
      </div>
    <button type="submit" class="btn btn-primary btn-sm btn-block">Guardar</button>
    </form>
  </div>
</div>

<br>
<hr>
<br>
<?php
  if (!isset($_GET['infoUsuario']) || $_GET['infoUsuario'] == null || $_GET['infoUsuario'] == '') {
?>
<div class="row">
  <div class="col-md-8 col-xs-10 col-sm-10 col-md-offset-1">
    <select id="cbUsuarioInfo">
      <option selected="true" disabled="disabled">Ver informacion. Usuario - nombres - correo</option>
<?php
    $users = get_users(['meta_key' => 'first_name','orderby' => 'meta_value','order' => 'ASC' ]);
    foreach($users as $user){
            $info = get_user_meta ( $user->ID);
            echo '<option value="'.$info['nickname'][0].'">'.$info['nickname'][0].' - '.$info['first_name'][0].' '.$info['last_name'][0].' - '.$info['billing_email'][0].'</option>';
        }
?>
    </select>
  </div>
  <div class="col-md-2 col-xs-2 col-sm-2">
      <button type="button" class="btn btn-primary btn-block" onclick="fnUsuarioInfo();">
        <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Ver
      </button>
  </div>
</div>
<script>
  function fnUsuarioInfo(){
    if($('#cbUsuarioInfo').val() == null || $('#cbUsuarioInfo').val() == '')return;
    localStorage['infoUsuario'] = $('#cbUsuarioInfo').val();
    fnUsuarioInfoSet();
    window.location = '/mi-cuenta?infoUsuario='+localStorage['infoUsuario'];
  }
</script>
<?php
  }
?>


<?php include('user_info.php'); ?>

<br>
<hr>
<br>

<div class="row">
  <div class="col-md-8 col-xs-10 col-sm-10 col-md-offset-1">
    <select id="cbUsuarioonetoone">
      <option selected="true" disabled="disabled">Usuario One to One - nombres - correo</option>
<?php
    $users = get_users(['meta_key' => 'first_name','orderby' => 'meta_value','order' => 'ASC' ]);
    foreach($users as $user){
            $info = get_user_meta ( $user->ID);
            echo '<option value="'.$info['nickname'][0].'">'.$info['nickname'][0].' - '.$info['first_name'][0].' '.$info['last_name'][0].' - '.$info['billing_email'][0].'</option>';
        }
?>
    </select>
  </div>
  <div class="col-md-2 col-xs-2 col-sm-2">
      <button type="button" class="btn btn-primary btn-block btn-xs" onclick="fnUsuarioonetoone_Agregar();">
        <span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span> Agregar
      </button>
  </div>
</div>

<div class="row">
  <div class="col-md-10 col-xs-12 col-sm-12  col-md-offset-1">
      <div class="table-responsive" id="divonetooneTabla">
      </div>
  </div>
</div>


<script>
  function fnUsuarioonetoone_Tabla(){
    jQuery.ajax({
        type : "GET",
        // dataType : "json",
        url : '<?= admin_url( 'admin-ajax.php' ) ?>',
        data : {
            action: "fnViveMovimentoOneToOnetabla"
        },
        success: function(response) {
            $('#divonetooneTabla').html(response);
        }
    });
  }
  function fnUsuarioonetoone_Agregar(){
    if($('#cbUsuarioonetoone').val() ==  null || $('#cbUsuarioonetoone').val() == '')return;
      jQuery.ajax({
          type : "GET",
          // dataType : "json",
          url : '<?= admin_url( 'admin-ajax.php' ) ?>',
          data : {
              action: "fnViveMovimentoOneToOneAgregar"
              ,strUsuario: $('#cbUsuarioonetoone').val()
          },
          success: function(response) {
              var response = jQuery.parseJSON(response);
              if(response.type == "success") {
                  setTimeout(function () {
                      fnUsuarioonetoone_Tabla();
                  }, 150);
              }else {
                 alert(response.mnj);
              }
          }
      });
  }
  function fnUsuarioonetoone_Eliminar(strUsuario){
    jQuery.ajax({
            type : "GET",
            // dataType : "json",
            url : '<?= admin_url( 'admin-ajax.php' ) ?>',
            data : {
                action: "fnViveMovimentoOneToOneEliminar"
                ,strUsuario: strUsuario
            },
            success: function(response) {
                var response = jQuery.parseJSON(response);
                if(response.type == "success") {
                    setTimeout(function () {
                        fnUsuarioonetoone_Tabla();
                    }, 150);
                }else {
                   alert(response.mnj);
                }
            }
        });
  }
  setTimeout(function(){
    fnUsuarioonetoone_Tabla();
  }, 2000);
</script>




<?php  } ?>


<div class="modal fade" id="modalPasosVideo" tabindex="-1" role="dialog" aria-labelledby="modalPasosVideoLabel" data-backdrop="false">
  <div class="modal-dialog" role="document" style="margin-top: 7%;width: 99%">
    <div class="modal-content">
      <div class="modal-header" style="padding: 0px;text-align: center;">
        <h4 class="modal-title" id="modalPasosVideoLabel">Ayuda</h4>
      </div>
      <div class="modal-body" style="padding: 0px;text-align: center;">
        <iframe id="iframePasosVideos" width="100%" height="500" src="about:blank"></iframe>
      </div>
      <div class="modal-footer" style="padding: 0px;text-align: center;">
        <button type="button" class="btn btn-default btn-sm btn-block" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>
  function fnPasosMostrarVideo(strUrl) {
    $('#iframePasosVideos').attr('src', strUrl);
    $('#modalPasosVideo').modal('show');
  }
</script>

<?php

}
?>
