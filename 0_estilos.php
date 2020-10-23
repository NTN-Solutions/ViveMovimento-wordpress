<?php
	function fnViveMovimento_usuario($bitMensaje = null){
		// echo '===============>';
		// echo wp_get_current_user()['user_login'];
		// echo '<===============';
		if (isset($_GET['infoUsuario']) && $_GET['infoUsuario'] != null && $_GET['infoUsuario'] != '') {
			if ( wp_get_current_user()['user_admin'] == true) {
				if($bitMensaje != null && $bitMensaje == true){
					echo '<div class="vc_message_box vc_message_box-solid vc_message_box-rounded vc_color-warning"><div class="vc_message_box-icon"><i class="fa fa-exclamation-triangle"></i></div><p>Se encuentra como "'.$_GET['infoUsuario'].'" viendo información del USUARIO! Precaución! NO realizar guardados!</p><a href="#" onClick="fnUsuarioInfoEnd();">Finalizar</a></div>';					
				}
				return $_GET['infoUsuario'];
			}else{
				return wp_get_current_user()['user_login'];
			}
		}else{
			return wp_get_current_user()['user_login'];
		}
	}
	function fnViveMovimento_admin(){
		if (isset($_GET['infoUsuario']) && $_GET['infoUsuario'] != null && $_GET['infoUsuario'] != '') {
			return false;
		}else{
			return wp_get_current_user()['user_admin'];
		}
	}
	function fnViveMovimento_Init()
		{
	fnViveMovimento_usuario(true);
?>
<style>
	label {
	    margin-bottom: 0px !important;
	}
	a.badge:hover, a.badge:focus {
	    color: black !important;
	}
	/* The rbContainer */
	.rbContainer {
		display: inline-block;
		position: relative;
		padding-left: 35px;
		margin-bottom: 12px;
		cursor: pointer;
		font-size: 15px;
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
	}
	/* Hide the browser's default radio button */
	.rbContainer input {
		position: absolute;
		opacity: 0;
		cursor: pointer;
	}
	/* Create a custom radio button */
	.rbCheckmark {
		position: absolute;
		top: 0;
		left: 0;
		height: 25px;
		width: 25px;
		background-color: #eee;
		border-radius: 50%;
	}
	/* On mouse-over, add a grey background color */
	.rbContainer:hover input ~ .rbCheckmark {
		background-color: #ccc;
	}
	/* When the radio button is checked, add a blue background */
	.rbContainer input:checked ~ .rbCheckmark {
		background-color: #2196F3;
	}
	/* Create the indicator (the dot/circle - hidden when not checked) */
	.rbCheckmark:after {
		content: "";
		position: absolute;
		display: none;
	}
	/* Show the indicator (dot/circle) when checked */
	.rbContainer input:checked ~ .rbCheckmark:after {
		display: block;
	}
	/* Style the indicator (dot/circle) */
	.rbContainer .rbCheckmark:after {
		top: 9px;
		left: 9px;
		width: 8px;
		height: 8px;
		border-radius: 50%;
		background: white;
	}
	#customers {
		font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
		border-collapse: collapse;
		width: 100%;
	}
	#customers td, #customers th {
		border: 1px solid #ddd;
		padding: 1px;
	}
	#customers tr:nth-child(even){
		background-color: #f2f2f2;
	}
	#customers tr:hover {
		background-color: #ddd;
	}
	#customers th {
		padding-top: 12px;
		padding-bottom: 12px;
		text-align: center;
		background-color: #c9302c;
		color: white;
	}
	.amarillo{
		background-color: #fff176;
		text-align: center;
	}
	.texto_blanco{
		color: white;
	}
	.naranja, naranja > td{
		background-color: #ffb74d;
		text-align: center;
	}
	.celeste{
		background-color: #4fc3f7;
		text-align: center;
	}
	.verde > td{
		background-color: #81c784;
		text-align: center;
	}
	.rojo, .rojo > td{
		background-color: #ef5350;
		text-align: center;
		color: white;
	}
	.morado, .morado > td{
		background-color: #9575cd;
		text-align: center;
	}
	.blanco, .blanco > td{
		background-color: white !important;
		text-align: center;
	}
	.rosado, .rosado > td{
		background-color: #f06292 !important;
		text-align: center;
	}
	.gris, .gris > td{
		background-color: #e0e0e0 !important;
		text-align: center;
	}
	#tblAlimentos {		
		font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
		border-collapse: collapse;
		width: 100%;
	}
	#tblAlimentos td, #customers th {
		border: 1px solid #ddd;
		padding: 6px;
	}
	#tblAlimentos tr:nth-child(even){
		background-color: #f2f2f2;
	}
	#tblAlimentos tr:hover {
		background-color: #ddd;
	}
	#tblAlimentos th {
		padding-top: 2px;
		padding-bottom: 2px;
		text-align: center;
		color: black;
	}
	#tblAlimentos td {
		text-align: center;
	}
	.tblDiario {
		font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
		border-collapse: collapse;
		width: 100%;
	}
	.tblDiario td, #customers th {
		border: 1px solid #ddd;
		padding: 6px;
	}
	.tblDiario tr:nth-child(even){
		background-color: #f2f2f2;
	}		
	.tblDiario tr:hover {
		background-color: #ddd;
	}	
	.tblDiario th {
		padding-top: 12px;
		padding-bottom: 12px;
		text-align: center;
		color: black;
	}
	.tblDiario td {
		text-align: center;
	}
	.verde {
		background-color: #81c784;
	}
	.noMostrar{
		display: none;
	}
	.customCarousel{
		line-height: 1 !important;
	    max-width: 100% !important;
	    height: 400px !important;
	    color: black !important;
	}
	.customCarousel > div{
		color: black !important;
	}
	.carousel-caption {
	    /*padding-bottom: 10% !important;*/
	    padding-bottom: 110px !important;
	}
	.sinPadding{
		padding: 0px !important;
	}
	input[type="submit"]{
    	padding: 0px !important; 
	}
	.btn{
    	color: white;
	    background-color: black;
	}
	/*.carousel .item {
	  height: 300px;
	}*/
</style>
<?php
		if(fnViveMovimento_usuario() == null || fnViveMovimento_usuario() == '')return;
		global $wpdb,$bitPermiso;
		$strUsuario = fnViveMovimento_usuario();
		$bitPermiso = wp_get_current_user()['user_admin'];
		$bitPermisoLocal = wp_get_current_user()['user_admin'];
		// $bitPermisoLocal = false;
	    global $gDataPaso_1,$gDataPaso_2,$gDataPaso_3;
	    fnViveMovimento_resumen_data();
		?>
		<div class="" style="display: none;">
			<div class="col-md-12- col-xs-12 col-sm-12 sinPadding">
				<i class="fas fa-info"></i>
				Cuando uno entra a la pagina, lo primero que sale es esto:
				Quiero que sea mas interactivo, informativo.
				
				Que salga un step by step. 
				Podras obtener tu plan personalizado a tan solo unos clicks!
				Para iniciar, necesitamos saber tu edad.
				Ahora indícanos tu altura.
				blabla.....
				Estas son las porciones que tienes que seguir! .....
			</div>
		</div>
		<div class="">
			<div class="col-md-12- col-xs-12 col-sm-12">
				<ul class="nav nav-tabs nav-justified" role="tablist" id="tabsViveMovimento">
					<li role="presentation" class="tab_core_no_diario">
						<a href="#tab_Paso_1" id="tab_Paso_1_Ref" aria-controls="tab_Paso_1" role="tab" data-toggle="tab">
							<i class="fas fa-weight"></i></i><?php if($gDataPaso_1 == null || $gDataPaso_2 == null || $gDataPaso_3 == null) { echo ' Paso 1 <br/>';}?> Metabolismo Basal
						</a>
					</li>
					<li role="presentation" class="tab_core_no_diario">
						<a href="#tab_Paso_2" id="tab_Paso_2_Ref" aria-controls="tab_Paso_2" role="tab" data-toggle="tab">
							<i class="fas fa-running"></i><?php if($gDataPaso_1 == null || $gDataPaso_2 == null || $gDataPaso_3 == null) { echo ' Paso 2 <br/>';}?>Gasto Energético
						</a>
					</li>
					<li role="presentation" class="tab_core_no_diario">
						<a href="#tab_Paso_21" id="tab_Paso_21_Ref" aria-controls="tab_Paso_21" role="tab" data-toggle="tab">
							<i class="fas fa-running"></i><?php if($gDataPaso_1 == null || $gDataPaso_2 == null || $gDataPaso_3 == null) { echo ' Paso 3 <br/>';}?>Experiencia
						</a>
					</li>
					<li role="presentation" class="tab_core_no_diario">
						<a href="#tab_Paso_22" id="tab_Paso_22_Ref" aria-controls="tab_Paso_22" role="tab" data-toggle="tab">
							<i class="fas fa-running"></i><?php if($gDataPaso_1 == null || $gDataPaso_2 == null || $gDataPaso_3 == null) { echo ' Paso 3 <br/>';}?>Tipo de Ejercicio
						</a>
					</li>
					<li role="presentation" class="tab_core_no_diario">
						<a href="#tab_Paso_3" id="tab_Paso_3_Ref" aria-controls="tab_Paso_3" role="tab" data-toggle="tab">
							<i class="fas fa-chart-line"></i><?php if($gDataPaso_1 == null || $gDataPaso_2 == null || $gDataPaso_3 == null) { echo ' Paso 4 <br/>';}?>Meta
						</a>
					</li>
					<li role="presentation" class="tab_core_no_diario">
						<a href="#tab_Paso_4" id="tab_Paso_4_Ref" aria-controls="tab_Paso_4" role="tab" data-toggle="tab">
							<i class="fas fa-calculator"></i><?php if($gDataPaso_1 == null || $gDataPaso_2 == null || $gDataPaso_3 == null) { echo ' Paso 5 <br/>';}?>Calculadora
						</a>
					</li>
					<li role="presentation" class="tab_core_diario" style="display: none;">
						<a href="#" onclick="fnTabNav(10);" role="tab" data-toggle="tab">
							<i class="fas fa-user"></i>Dashboard
						</a>
					</li>
					<li role="presentation" id="tab_core_5" class="tab_core_diario" style="display: none;">
						<a href="#tab_Paso_5" id="tab_Paso_5_Ref" aria-controls="tab_Paso_5" role="tab" data-toggle="tab">
							<i class="fas fa-utensils"></i><i class="fas fa-book"></i><?php if($gDataPaso_1 == null || $gDataPaso_2 == null || $gDataPaso_3 == null) { echo '<br/>';} ?> Food Journal
						</a>
					</li>
					<li role="presentation" id="tab_core_6" class="tab_core_diario" style="display: none;">
						<a href="#tab_Paso_9" id="tab_Paso_9_Ref" aria-controls="tab_Paso_9" role="tab" data-toggle="tab">
							<i class="fas fa-list-ul"></i><?php if($gDataPaso_1 == null || $gDataPaso_2 == null || $gDataPaso_3 == null) { echo '<br/>';} ?>Mis Recetas
						</a>
					</li>
					<li role="presentation" id="tab_core_6" class="tab_core_diario" style="display: none;">
						<a href="#tab_Paso_6" id="tab_Paso_6_Ref" aria-controls="tab_Paso_6" role="tab" data-toggle="tab">
							<i class="fas fa-hamburger"></i><i class="fas fa-pizza-slice"></i><?php if($gDataPaso_1 == null || $gDataPaso_2 == null || $gDataPaso_3 == null) { echo '<br/>';} ?> Base Datos
						</a>
					</li>
					<li role="presentation" id="tab_core_7" class="tab_check_in" style="display: none;">
						<a href="#tab_Paso_7" id="tab_Paso_7_Ref" aria-controls="tab_Paso_7" role="tab" data-toggle="tab">
							<i class="fas fa-check"></i><?php if($gDataPaso_1 == null || $gDataPaso_2 == null || $gDataPaso_3 == null) { echo '<br/>';} ?> Check In
						</a>
					</li>
					<li role="presentation" id="tab_core_8" class="tab_planes_ejercicio" style="display: none;">
						<a href="#tab_Paso_8" id="tab_Paso_8_Ref" aria-controls="tab_Paso_8" role="tab" data-toggle="tab">
							<i class="fas fa-check"></i> Plan de Ejercicios
						</a>
					</li>
				</ul>
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane" id="tab_Paso_1">
						<?php fnTab_1(); ?>
					</div>
					<div role="tabpanel" class="tab-pane" id="tab_Paso_2">
						<?php fnTab_2(); ?>
					</div>
					<div role="tabpanel" class="tab-pane" id="tab_Paso_21">
						<?php fnTab_21(); ?>
					</div>
					<div role="tabpanel" class="tab-pane" id="tab_Paso_22">
						<?php fnTab_22(); ?>
					</div>
					<div role="tabpanel" class="tab-pane" id="tab_Paso_3">
						<?php fnTab_3(); ?>
					</div>
					<div role="tabpanel" class="tab-pane" id="tab_Paso_4">
						<?php fnTab_4(); ?>					
					</div>
					<div role="tabpanel" class="tab-pane" id="tab_Paso_5">
						<?php fnTab_5(); ?>
					</div>
					<div role="tabpanel" class="tab-pane" id="tab_Paso_6">
						<?php fnTab_BD(); ?>
					</div>
					<div role="tabpanel" class="tab-pane" id="tab_Paso_7">
						<?php fnTab_7(); ?>
					</div>
					<div role="tabpanel" class="tab-pane" id="tab_Paso_8">
						<?php fnTab_8(); ?>
					</div>
					<div role="tabpanel" class="tab-pane" id="tab_Paso_9">
						<?php fnTab_9(); ?>
					</div>
				</div>
			</div>
		</div>
	<script>		
		function fnTabNavRedirect(intTab) {
			//se hace esta funcion porque solicitaron remover botones de guardar, y la info se cargaba solo 1 vez al cargar la pagina
			//pero ahora como los form submit se hacen por ajax no se refresca la data del siguiente paso
			window.location = '/user/?action=tab_Paso_'+intTab;
		}
		function fnTabNav(intTab) {
			$('.nav-tabs a[href="#tab_Paso_' + intTab + '"]').tab('show');
			if (intTab == 10) {
				window.location.href = '/mi-cuenta/';
			}
		// $('#tabsViveMovimento a[href="#tab_Paso_'+intTab+'"]').tab('show');
		}
		$( document ).ready(function() {
			<?php if ($bitPermisoLocal != null && strlen($bitPermisoLocal)>0 && $bitPermisoLocal === true) { echo "$('.noMostrar').show();"; }else{ echo "$('#divTablaMisPorciones').attr('class','col-md-8 col-xs-12 col-sm-12 col-md-offset-2'); "; } ?>
			if(window.location.href.toLowerCase().indexOf('tab_paso_') !== -1){
				if(window.location.href.toLowerCase().split('tab_paso_')[1].indexOf('&') !== -1){
					var strTab = window.location.href.toLowerCase().split('tab_paso_')[1].split('&')[0];
					if (strTab == 5 || strTab == 6) {
						$('.tab_core_no_diario').hide();
						$('.tab_core_diario').show();						
					}else if(strTab == 7){
						$('.tab_core_no_diario').hide();
						$('.tab_core_diario').hide();	
						$('.tab_check_in').show();
					}
					fnTabNav(strTab);			
				}else{
					fnTabNav(window.location.href.toLowerCase().split('tab_paso_')[1]);
					if (window.location.href.toLowerCase().split('tab_paso_')[1] == 5 || window.location.href.toLowerCase().split('tab_paso_')[1] == 6 || window.location.href.toLowerCase().split('tab_paso_')[1] == 9) {
						$('.tab_core_no_diario').hide();
						$('.tab_core_diario').show();
					}else if(window.location.href.toLowerCase().split('tab_paso_')[1] == 7){
						$('.tab_core_no_diario').hide();
						$('.tab_core_diario').hide();	
						$('.tab_check_in').show();
					}else if(window.location.href.toLowerCase().split('tab_paso_')[1] == 8){
						$('.tab_core_no_diario').hide();
						$('.tab_core_diario').hide();	
						$('.tab_check_in').hide();
						$('.tab_planes_ejercicio').show();
					}
				}
			}else{
				fnTabNav(1);			
			}
			if(window.location.href.toLowerCase().indexOf('tab_diario') !== -1){
				if(window.location.href.toLowerCase().split('tab_diario_')[1].indexOf('&') !== -1){
					var strTab = window.location.href.toLowerCase().split('tab_diario_')[1].split('&')[0];
					$('#collapseDiario_' + strTab).collapse('show');
				}else{
					$('#collapseDiario_' + window.location.href.toLowerCase().split('tab_diario_')[1]).collapse('show');
				}
			}
		});
	$(".tm-titlebar-wrapper").hide();
	$("#main").css({"padding-top":"90px"});
	$(".wpb_text_column.wpb_content_element").hide();
</script>
<?php include('user_info.php'); ?>
	<div id="modalFoto" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
	  <div class="modal-dialog modal-lg" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h4 class="modal-title" id="myModalLabel">Foto</h4>
	      </div>
	    	<div class="row">
	    		<div class="col-md-12 col-xs-12 col-sm-12">
	    			<img id="imgFotoDia" src="#" alt="Foto" class="img-responsive img-thumbnail">
	    		</div>
	    	</div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default btn-sm btn-block" data-dismiss="modal">Cerrar</button>
		      </div>
	    </div>
	  </div>
	</div>
<?php } ?>