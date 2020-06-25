<?php		
	function fnTab_7() {
	    global $wpdb, $strUsuario,$reg_errors,$intEdad,$decAltura,$decPeso,$intSexo,$decGrasa;
		global $strRutaImagenMadre;
		global $arrayGraficaPesoLabels, $arrayGraficaPesoValues;
		$arrayGraficaPesoLabels = "";
		$arrayGraficaPesoValues = "";
	    $strUsuario = wp_get_current_user()->user_login;
		$strRutaImagenMadre = '/home/jhx94zix8g9i/public_html/wp-content/plugins/vivemovimento/fotos/'.$strUsuario;
		$whitelist = array(
		    '127.0.0.1',
		    '::1'
		);
		if(in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
			$strRutaImagenMadre = '/Applications/MAMP/htdocs/wp-content/plugins/vivemovimento/fotos/'.$strUsuario;
		}

		if (isset($_GET['action']) && $_GET['action'] == 'tab_Paso_7' && isset($_POST['intOp']) && $_POST['intOp'] != null && $_POST['intOp'] == '1') { 
    		$reg_errors = new WP_Error;
	        $decPeso = floatval($_POST['txtActualizarPeso']);
			$lastUserINFO = $wpdb->get_results("
			    	SELECT
						(datRegistro) datRegistro
						,(intEdad) intEdad
						,(decAltura) decAltura
						,(decPeso) decPeso
						,(decGrasa) decGrasa
						,(decMetabolismo) decMetabolismo
						,(intSexo) intSexo
					FROM wp_vivemov_users_informacion
					WHERE strUsuario = '$strUsuario'
					ORDER BY datRegistro DESC
					LIMIT 2");
			$lastUserINFO = $lastUserINFO[0];
			$intEdad = $lastUserINFO->intEdad;
			$decAltura = $lastUserINFO->decAltura;
			$intSexo = $lastUserINFO->intSexo;
			$decGrasa = $lastUserINFO->decGrasa;
			fnGuardarMiInformacion_save();
       		echo fnMensaje(1,'Listo, peso guardado!');
    	}
    	else if (isset($_GET['action']) && $_GET['action'] == 'tab_Paso_7' && isset($_POST['intOp']) && $_POST['intOp'] != null && $_POST['intOp'] == '2') { 

    		//metodo 1
   //  		$target_dir = "fotos/";
			// $target_file = basename($_FILES["fileToUpload"]["name"]);
			// // $target_file = "fotos/";
			// $uploadOk = 1;
			// $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));


			// // Check if image file is a actual image or fake image
			// if(isset($_POST["submit"])) {
			//   $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
			//   if($check !== false) {
			//     echo "File is an image - " . $check["mime"] . ".";
			//     $uploadOk = 1;
			//   } else {
			//     echo "File is not an image.";
			//     $uploadOk = 0;
			//   }
			// }

			// // Check if file already exists
			// if (file_exists($target_file)) {
			//   echo "Sorry, file already exists.";
			//   $uploadOk = 0;
			// }

			// // Check file size
			// if ($_FILES["fileToUpload"]["size"] > 5000000) {
			//   echo "Sorry, your file is too large.";
			//   $uploadOk = 0;
			// }

			// // Allow certain file formats
			// if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
			//   echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
			//   $uploadOk = 0;
			// }

			// // Check if $uploadOk is set to 0 by an error
			// if ($uploadOk == 0) {
			//   echo "Sorry, your file was not uploaded.";
			// // if everything is ok, try to upload file
			// } else {
			//   if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], '/Applications/MAMP/htdocs/wp-content/plugins/vivemovimento/fotos/'.$target_file)) {
			//     echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
			//   } else {
			//     echo "Sorry, there was an error uploading your file.";
			//   }
			// }


			///metodo 2
			
			$errors= array();
		    $file_name = $_FILES['image']['name'];
		    $file_size =$_FILES['image']['size'];
		    $file_tmp =$_FILES['image']['tmp_name'];
		    $file_type=$_FILES['image']['type'];
		    $tmp = explode('.',$_FILES['image']['name']);
			$file_extension = end($tmp);

		    $file_ext=strtolower($file_extension);
		      
		    $extensions= array("jpeg","jpg","png");
		      
		    if(in_array($file_ext,$extensions)=== false){
		       $errors[]="formato de imagen no permitido, por favor verifique que sea JPEG o PNG";
		    }
		      
		    if($file_size > 5242880){
		       $errors[]='Imagen excede los 5 MB!';
		    }

			// if (file_exists("/Applications/MAMP/htdocs/wp-content/plugins/vivemovimento/fotos/".$file_name)) {
		 //       $errors[]= 'Imagen ya existe con el mismo nombre!';
			// }

		    $strRutaImagen = $strRutaImagenMadre;
		    $strDiaActual = (new DateTime())->format('yy-m-d');
		    $strRutaImagen = $strRutaImagen.'/'.$strDiaActual;

		    if (!file_exists($strRutaImagen)) {
			    mkdir($strRutaImagen, 0777, true);
			}

		    $file_name = $strUsuario.'_'.$file_name;
		    if(empty($errors)==true){
		      	move_uploaded_file($file_tmp,$strRutaImagen.'/'.$file_name);
    			echo fnMensaje(1,'Listo, imagen subida!');
		    }else{
    			echo fnMensaje(2, $errors[0]);
		    }
    	}

    $list = $wpdb->get_results("
    	SELECT
			MAX(datRegistro) datRegistro
			,MAX(decAltura) decAltura
			,MAX(decPeso) decPeso
			,MAX(decGrasa) decGrasa
			,MAX(decMetabolismo) decMetabolismo
		FROM wp_vivemov_users_informacion
		WHERE strUsuario = '$strUsuario'
		GROUP BY CAST(datRegistro AS DATE)
		ORDER BY datRegistro DESC
    	");

    $listGrafica = $wpdb->get_results("
    	SELECT
			MAX(datRegistro) datRegistro
			,MAX(decPeso) decPeso
		FROM wp_vivemov_users_informacion
		WHERE strUsuario = '$strUsuario'
			AND CAST(datRegistro AS DATE) >= CAST(DATE_ADD(CURDATE(), INTERVAL -2 MONTH) AS DATE)
		GROUP BY CAST(datRegistro AS DATE)
		ORDER BY datRegistro ASC
		LIMIT 30
    	");
   	foreach ($listGrafica as $item) {
   		if ($arrayGraficaPesoLabels == ''){
   			$arrayGraficaPesoLabels = "'".(new DateTime($item->datRegistro))->format('d-M')."'";
   			$arrayGraficaPesoValues = $item->decPeso;
   		}else{
   			$arrayGraficaPesoLabels .= ", '".(new DateTime($item->datRegistro))->format('d-M')."'";
   			$arrayGraficaPesoValues .= ', '.$item->decPeso;
   		}
   	}
?>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.js"></script>

	<div class="row">
		<div class="col-md-6 col-xs-12 col-sm-12">
			<form class="form-inline" action="<?php echo strtok($_SERVER["REQUEST_URI"],'?') ?>?action=tab_Paso_7" method="post">
			  <div class="form-group">
			    <label class="sr-only" for="txtActualizarPeso">Actualizar peso:</label>
			    <div class="input-group">
			      <div class="input-group-addon">Peso</div>
			      <input type="hidden" name="intOp" value="1">
			      <input type="number" class="form-control" name="txtActualizarPeso" id="txtActualizarPeso" placeholder="100 libras..." required="required" step="0.01">
			    </div>
			  </div>
			  <button type="submit" class="btn btn-primary">
			  	<span class="glyphicon glyphicon-refresh" aria-hidden="true"></span> 
			  	Actualizar Peso(lbs)
			  </button>
			</form>
		</div>
		<div class="col-md-6 col-xs-12 col-sm-12">
			<form class="form-inline" action="<?= strtok($_SERVER["REQUEST_URI"],'?') ?>?action=tab_Paso_7" method="post" enctype="multipart/form-data">

				<div class="form-group">
				    <label for="image">Subir foto</label>
				    <input type="file" class="form-control" id="image" name="image" placeholder="Imagen" required="required">
				    <p class="help-block">Tus fotos se cargarán al día actual y debes hacer Check In</p>
				  </div>
			  <input type="hidden" name="intOp" value="2">
			  <button type="submit" class="btn btn-primary">
			  	<span class="glyphicon glyphicon-floppy-saved" aria-hidden="true"></span> 
			  	Subir Foto
			  </button>
			</form>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12 col-xs-12 col-md-12">
			<div class="collapse" id="collapse_grafica_peso">
			  <div class="well">
			  	<!-- <canvas id="graficaPesoCanvas" width="400" height="600" style="max-width: 1200px;max-height: 800px;"></canvas> -->
			  	<center>
			  		<canvas id="graficaPesoCanvas" width="1100px" height="600px" style="max-width: 1100px;max-height: 600px;"></canvas>
			  	</center>
			  </div>
			</div>
		</div>
	</div>
	<div class="table-responsive">
	  <table class="table">
	  	<caption>
	  		Detalle por dia de tu cambio del peso.
		  <button type="button" class="btn btn-primary btn-xs" data-toggle="collapse" data-target="#collapse_grafica_peso" aria-expanded="false" aria-controls="collapse_grafica_peso" onclick="fnGraficaPesoInit();">
		  	<span class="glyphicon glyphicon-stats" aria-hidden="true"></span> 
		  	Gráfica de peso
		  </button>
	  	</caption>
		  	<thead>
			    <tr>
			      <th>Fecha</th>
			      <th>Peso</th>
			      <th>Grasa</th>
			      <th style="width: 40% !important;">Fotos</th>
			      <!--<th>Metabolismo</th>-->
			    </tr>
		  </thead>
<?php
	for ($i=0; $i < count($list); $i++) { 
		$strTipo_1 = 'info';
		$strTipo_2 = 'info';
		$strTipo_3 = 'info';
		$item = $list[$i];
		if (($i + 1) < count($list)) {
			if ($item->decPeso <= $list[(($i + 1))]->decPeso) {
				$strTipo_1 = 'success';
			}else{
				$strTipo_1 = 'warning';				
			}
			if ($item->decGrasa <= $list[(($i + 1))]->decGrasa) {
				$strTipo_2 = 'success';
			}else{
				$strTipo_2 = 'warning';				
			}
			if ($item->decMetabolismo <= $list[(($i + 1))]->decMetabolismo) {
				$strTipo_3 = 'success';
			}else{
				$strTipo_3 = 'warning';				
			}
		}
	  	echo '<tr>
		  <td class="info">'.(new DateTime($item->datRegistro))->format('D, d-M-Y h:m a').'</td>
		  <td class="'.$strTipo_1.'">'.$item->decPeso.'</td>
		  <td class="'.$strTipo_2.'">'.$item->decGrasa.'</td>
		  <td>';
		    $strRutaImagen = $strRutaImagenMadre.'/'.(new DateTime($item->datRegistro))->format('yy-m-d');
		    if (!file_exists($strRutaImagen)) {
		    	echo '-';
			}else{
		    	$fileList = glob($strRutaImagen.'/*');
		    	$intContadorImagen = 0;
				foreach($fileList as $filename){
				  	echo '<img id="img_foto_'.(new DateTime($item->datRegistro))->format('yy-m-d').$intContadorImagen.'" width=50 src="'.str_replace($_SERVER['DOCUMENT_ROOT'],'',$filename).'" alt="Foto" class="img_foto img-responsive img-thumbnail" style="cursor: pointer;" onclick="fnMostrarFoto(\'img_foto_'.(new DateTime($item->datRegistro))->format('yy-m-d').$intContadorImagen.'\',\''.str_replace($_SERVER['DOCUMENT_ROOT'],'',$filename).'\');">';
				  	$intContadorImagen += 1;
				}
			}
		echo'
		  </td>
		  <!-- <td class="'.$strTipo_3.'">'.$item->decMetabolismo.'</td>-->
		</tr>';
	}
?>
	  	<!-- <tr>
		  <td class="active">...</td>
		  <td class="success">...</td>
		  <td class="warning">...</td>
		  <td class="info">...</td>
		</tr> -->
	  </table>


	</div>

	<script>
		function fnGraficaPesoInit() {
			setTimeout(function(){
				var ctx = document.getElementById('graficaPesoCanvas').getContext('2d');
				var graficaPesoCanvas = new Chart(ctx, {
				    type: 'line',
				    data: {
				        labels: [<?= $arrayGraficaPesoLabels ?>],
				        datasets: [{
				            label: 'Gráfica de Peso de últimos 2 meses',
				            data: [<?= $arrayGraficaPesoValues ?>],
				            backgroundColor: [
				                'rgba(54, 162, 235, 0.2)',
				                'rgba(255, 206, 86, 0.2)',
				                'rgba(75, 192, 192, 0.2)',
				                'rgba(255, 159, 64, 0.2)'
				            ],
				            borderColor: [
				                'rgba(54, 162, 235, 1)',
				                'rgba(255, 206, 86, 1)',
				                'rgba(75, 192, 192, 1)',
				                'rgba(255, 159, 64, 1)'
				            ],
				            borderWidth: 1
				        }]
				    },
				    options: {
    					responsive:false,
	                    scales: {
				            yAxes: [{
				                ticks: {
				                    beginAtZero: false
				                }
				            }]
				        }
				    }
				});				
			}, 1000);
		}
		function fnMostrarFoto(strID, strFotoURL) {
			if($('#'+strID).attr('width') == 50){
				$('#'+strID).attr('width',400);
			} else{
				$('.img_foto').attr('width',50);
			}
			// $('#modalFoto').modal('show');
		}
	</script>

	<?php
		}
	?>


