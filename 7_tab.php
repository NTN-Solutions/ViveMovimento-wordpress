<?php		
	function fnTab_7() {
	    global $wpdb, $strUsuario,$reg_errors,$intEdad,$decAltura,$decPeso,$intSexo,$decGrasa;
	    $strUsuario = wp_get_current_user()->user_login;
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
?>
	<div class="row">
		<div class="col-md-12 col-xs-12 col-sm-12">
			<form class="form-inline" action="<?php echo strtok($_SERVER["REQUEST_URI"],'?') ?>?action=tab_Paso_7" method="post">
			  <div class="form-group">
			    <label class="sr-only" for="txtActualizarPeso">Actualizar peso:</label>
			    <div class="input-group">
			      <div class="input-group-addon">Libras</div>
			      <input type="hidden" name="intOp" value="1">
			      <input type="number" class="form-control" name="txtActualizarPeso" id="txtActualizarPeso" placeholder="Peso en libras..." required="required" step="0.01">
			    </div>
			  </div>
			  <button type="submit" class="btn btn-primary">
			  	<span class="glyphicon glyphicon-refresh" aria-hidden="true"></span> 
			  	Actualizar</button>
			</form>
		</div>
	</div>
	<div class="table-responsive">
	  <table class="table">
	  	<caption>Detalle por dia tu cambio en peso, grasa y metabolismo</caption>
		  	<thead>
			    <tr>
			      <th>Fecha</th>
			      <th>Peso</th>
			      <th>Grasa</th>
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

	<?php
		}
	?>

