<?php
//1- se obtendran parametros por http get
//2- se retornara siempre respuesta en json
//3- nombreclarutra de datos: strign str, int numerico, dec decimal, list listado;
//4 - sexo, 0 mujer, 1 hombre

//5- para realizar los llamados es,ejemplo
//http://localhost:8888/vivemovimento_api.php?strAccion=NOMBRE_DE_TAL_ACCION&strUsuario=USUARIO_TAL&OTROS_PARAMETROS......

function fnRedondear($decValor){
    return round($decValor, 2);
}
function fnRedondearCUSTOMUP_1($decValor){
    return round($decValor, 1);
}
function fnDBCon(){
	//metodo para obtener la conexion a BD
	$servername = "localhost";
	$username = "vivemovimento_own";
	$password = "vivemovimento123";
	$dbname = "vivemovimento";
	$conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}else{
		return $conn;
	}
}
function fnDBResult($strQuery){
	//metodo para obtener resultados de algun query
	$conn = fnDBCon();
	$sql = $strQuery;
	$result = $conn->query($sql);
	$sql = strtolower($sql);    
	$datos = array();
	$intFila = 0;
	if (strpos($sql, 'update') !== false || strpos($sql, 'delete') !== false|| strpos($sql, 'insert') !== false){
		$datos = array();
	}else if (strpos($sql, 'select') !== false && $result != null ) {
		while($row = $result->fetch_assoc()) {
			$datos[$intFila] = $row;
			$intFila += 1;
		}
		$result->close();
	} else {
		$datos = array();
	}
	$conn->close();
	return $datos;
}
function fnDBInsert($strTabla, $registro){
	$conn = fnDBCon();
	$strColumnas = '';
	$strValores = '';
	foreach($registro as $key=>$data) {
		if ($strColumnas == '') {
			$strColumnas = $key;
		}else{
			$strColumnas = $strColumnas.','.$key;
		}
		$data = str_replace(",", ".", $data);
		if ($strValores == '') {
			$strValores = "'".$data."'";
		}else{
			$strValores = $strValores.",'".$data."'";
		}
	}
	$sql = "INSERT INTO $strTabla($strColumnas)VALUES ($strValores)";
	$bitSuccess = false;
	if (mysqli_query($conn, $sql)) {
		$bitSuccess = true;
	} else {
		$bitSuccess = false;
	}
	$conn->close();
	return $bitSuccess;
}
function fnDBDelete($strTabla, $strColumna1, $strValor1){
	$conn = fnDBCon();
    $sql = 'DELETE FROM '.$strTabla.' WHERE '.$strColumna1." = '".$strValor1."'";
    $conn->query($sql);    
    $conn->close();
    return true;
}
function fnEndCallback($result){
	//metodo lo ejecutaran todas los demas para responder
	echo json_encode($result);
	exit();
}
function fnBeginCallback($result){
	//metodo lo ejecutaran todas los demas para obtener los datos a variables globales
	global $strUsuario;
	$strUsuario = $_GET['strUsuario'];	
}

/*** PASO 1 INICIO - Informacion Basica del Usuario */
function fnUsuarioInformacionBasicaObtener(){
	//primer pantalla datos basicos del usuario
	$result;
	try {
		$strUsuario = $_GET['strUsuario']; //obligatorio
		$buscar = fnDBResult("SELECT * FROM wp_vivemov_users_informacion WHERE strUsuario = '$strUsuario' ORDER BY intId DESC LIMIT 1;");
		if (count($buscar) > 0) {
			$buscar = $buscar[0];
			$decAltura = $buscar['decAltura'];
			$decPeso = $buscar['decPeso'];
			$buscar['decIMC'] = (($decPeso / 2.2) / (($decAltura/100) * ($decAltura/100)));
			
			$result['strTipo'] = 'success';
			$result['listDatosBasicos'] = $buscar;			
		}else{
			$result['strTipo'] = 'warning';
			$result['strMensaje'] = 'Sin datos Basicos!';
		}
	} catch (Exception $e) {
		$result['strTipo'] = 'error';
		$result['strMensaje'] = $e->getMessage();
	}
	fnEndCallback($result);
}
function fnUsuarioInformacionBasicaGuardar() {
	$result;

	$strUsuario = $_GET['strUsuario']; //obligatorio
	$intSexo = $_GET['intSexo']; //obligatorio
	$intEdad = $_GET['intEdad']; //obligatorio
	$decAltura = $_GET['decAltura']; //obligatorio
	$decPeso = $_GET['decPeso']; //obligatorio
	$decGrasa = $_GET['decGrasa']; //opcional, con valor o cero

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
	if(fnDBInsert("wp_vivemov_users_informacion", $registro) == true) {
		$result['strTipo'] = 'success';
		$result['strMensaje'] = 'Guardado!';
	} else {
		$result['strTipo'] = 'error';
		$result['strMensaje'] = 'Inconveniente al guardar!';
	}
	fnEndCallback($result);
}
/*** PASO 1 FIN - Informacion Basica del Usuario */


/*** PASO 2 INICIO - Gasto Energetico */
function fnUsuarioGastoEnergeticoObtener(){
	$result;

	$listGastoEnergetico = array();
	$gastoEnergetico = array();
	$gastoEnergetico['intTipo'] = 1;
	$gastoEnergetico['decOrden'] = 1;
	$gastoEnergetico['strTipo'] = 'SEDENTARIO';
	$gastoEnergetico['strActividad'] = 'Trabajo de oficina, minima actividad fisica *actividad fisica incluye caminar, subir y bajar escaleras, estar en constante movimiento';
	$gastoEnergetico['strEjercicio'] = '0 ejercicio *ejercicio refiere a disciplinas constantes como hacer pesas, cardio, yoga, ejercicio funcional, Crossfit, etc.';
	$gastoEnergetico['strEjemplo'] = 'Oficina 8hrs al dia, netflix, dormir, manejar, el resto del dia.';
	$listGastoEnergetico[0] = $gastoEnergetico;

	$gastoEnergetico['intTipo'] = 2;
	$gastoEnergetico['decOrden'] = 2;
	$gastoEnergetico['strTipo'] = 'SEMI-SEDENTARIO';
	$gastoEnergetico['strActividad'] = 'Trabajo de oficina,minima actividad fisica ademas del ejercicio';
	$gastoEnergetico['strEjercicio'] = 'ejercicio 1hr hasta 3 veces por semana';
	$gastoEnergetico['strEjemplo'] = 'Oficina 8hrs al dia, al salir de la oficina atiende clases de ejercicios funcionales de 5:30 a 6:30pm 3 veces por semana';
	$listGastoEnergetico[1] = $gastoEnergetico;

	$gastoEnergetico['intTipo'] = 3;
	$gastoEnergetico['decOrden'] = 3;
	$gastoEnergetico['strTipo'] = 'SEMI-ACTIVO (1)';
	$gastoEnergetico['strActividad'] = 'Trabajo de oficina,minima actividad fisica ademas del ejercicio';
	$gastoEnergetico['strEjercicio'] = 'ejercicio 1hr 4 veces o mas por semana';
	$gastoEnergetico['strEjemplo'] = 'Oficina 8hrs al dia, al salir de la oficina atiende clases de ejercicios funcionales de 5:30 a 6:30pm 5-6 veces por semana';
	$listGastoEnergetico[2] = $gastoEnergetico;

	$gastoEnergetico['intTipo'] = 4;
	$gastoEnergetico['decOrden'] = 4;
	$gastoEnergetico['strTipo'] = ' SEMI-ACTIVO (2)';
	$gastoEnergetico['strActividad'] = 'Trabajo que requiere constante movimiento';
	$gastoEnergetico['strEjercicio'] = 'ejercicio 1hr hasta 3 veces por semana';
	$gastoEnergetico['strEjemplo'] = 'Profesor, supervisor de proyecto, constante movimiento durante el dia, adicional ejercicio 1hr 4 veces o mas por semana';
	$listGastoEnergetico[3] = $gastoEnergetico;

	$gastoEnergetico['intTipo'] = 5;
	$gastoEnergetico['decOrden'] = 5;
	$gastoEnergetico['strTipo'] = 'MODERADAMENTE ACTIVO';
	$gastoEnergetico['strActividad'] = 'Trabajo que requiere constante movimiento';
	$gastoEnergetico['strEjercicio'] = 'ejercicio 1hr 4 veces o mas por semana';
	$gastoEnergetico['strEjemplo'] = 'profesor, supervisor de proyecto, constante movimiento durante el dia, adicional ejercicio 1hr 4 veces o mas por semana';
	$listGastoEnergetico[4] = $gastoEnergetico;

	$gastoEnergetico['intTipo'] = 6;
	$gastoEnergetico['decOrden'] = 6;
	$gastoEnergetico['strTipo'] = 'MUY ACTIVO';
	$gastoEnergetico['strActividad'] = 'Trabajo de oficina';
	$gastoEnergetico['strEjercicio'] = 'ejercicio mas de 1hr o dos sesiones al dia 5x o mas por semana';
	$gastoEnergetico['strEjemplo'] = 'trabajo de oficina, crossfit o gym + cardio consistente';
	$listGastoEnergetico[5] = $gastoEnergetico;

	$gastoEnergetico['intTipo'] = 7;
	$gastoEnergetico['decOrden'] = 7;
	$gastoEnergetico['strTipo'] = 'EXTREMADAMENTE ACTIVO';
	$gastoEnergetico['strActividad'] = 'Trabajo que requiere constante movimiento';
	$gastoEnergetico['strEjercicio'] = 'ejercicio mas de 1hr o dos veces por dia 5x o mas por semana';
	$gastoEnergetico['strEjemplo'] = 'entrenador, atleta, deportista';
	$listGastoEnergetico[6] = $gastoEnergetico;

	$result['listGastoEnergetico'] = $listGastoEnergetico;	

	try {
		$strUsuario = $_GET['strUsuario']; //obligatorio
		$buscar = fnDBResult("SELECT * FROM wp_vivemov_users_actividad_gasto_energetico WHERE strUsuario = '$strUsuario' ORDER BY decId DESC LIMIT 1;");
		if (count($buscar) > 0) {
			$buscar = $buscar[0];		
			$result['listGastoEnergeticoUsuario'] = $buscar;			
		}else{
			$result['strTipo'] = 'warning';
			$result['strMensaje'] = 'Sin Gasto Energetico!';
		}
	} catch (Exception $e) {
		$result['strTipo'] = 'error';
		$result['strMensaje'] = $e->getMessage();
	}
	fnEndCallback($result);
}
function fnUsuarioGastoEnergeticoGuardar() {
	$result;

	$strUsuario = $_GET['strUsuario']; //obligatorio
	$intActividadTipo = $_GET['intActividadTipo']; //obligatorio

	$registro = array(
		'strUsuario'    =>   $strUsuario,
		'intActividad'  =>   $intActividadTipo
	);
	if(fnDBInsert("wp_vivemov_users_actividad_gasto_energetico", $registro) == true) {
		$result['strTipo'] = 'success';
		$result['strMensaje'] = 'Guardado!';
	} else {
		$result['strTipo'] = 'error';
		$result['strMensaje'] = 'Inconveniente al guardar!';
	}
	fnEndCallback($result);
}
/*** PASO 2 FIN - Gasto Energetico */


/*** PASO 3 INICIO - Experiencia */
function fnUsuarioExperienciaObtener(){
	$result;
	$listExperiencia = array();
	$experiencia = array();

	$experiencia['intTipo'] = 1;
	$experiencia['decOrden'] = 1;
	$experiencia['strExperiencia'] = 'PRINCIPIANTE';
	$experiencia['strDescripcion'] = 'Nunca he hecho ejercicio y dieta de manera consistente';
	$listExperiencia[0] = $experiencia;

	$experiencia['intTipo'] = 2;
	$experiencia['decOrden'] = 2;
	$experiencia['strExperiencia'] = 'INTERMEDIO';
	$experiencia['strDescripcion'] = 'He estado intermitente con el ejercicio y la dieta';
	$listExperiencia[1] = $experiencia;

	$experiencia['intTipo'] = 3;
	$experiencia['decOrden'] = 3;
	$experiencia['strExperiencia'] = 'AVANZADO';
	$experiencia['strDescripcion'] = 'He estado disciplinado en cuanto a la dieta y el ejercicio de manera consistente por mas de 4 meses.';
	$listExperiencia[2] = $experiencia;

	$result['listExperiencia'] = $listExperiencia;			

	try {
		$strUsuario = $_GET['strUsuario']; //obligatorio
		$buscar = fnDBResult("SELECT * FROM wp_vivemov_users_experiencia WHERE strUsuario = '$strUsuario' ORDER BY decId DESC LIMIT 1;");
		if (count($buscar) > 0) {
			$buscar = $buscar[0];
			$result['listExperienciaUsuario'] = $buscar;			
		}else{
			$result['strTipo'] = 'warning';
			$result['strMensaje'] = 'Sin Experiencia!';
		}
	} catch (Exception $e) {
		$result['strTipo'] = 'error';
		$result['strMensaje'] = $e->getMessage();
	}
	fnEndCallback($result);
}
function fnUsuarioExperienciaGuardar() {
	$result;

	$strUsuario = $_GET['strUsuario']; //obligatorio
	$intExperiencia = $_GET['intExperiencia']; //obligatorio

	$registro = array(
		'strUsuario' 		=>	$strUsuario,
		'intExperiencia'	=>	$intExperiencia
	);
	if(fnDBInsert("wp_vivemov_users_experiencia", $registro) == true) {
		$result['strTipo'] = 'success';
		$result['strMensaje'] = 'Guardado!';
	} else {
		$result['strTipo'] = 'error';
		$result['strMensaje'] = 'Inconveniente al guardar!';
	}
	fnEndCallback($result);
}
/*** PASO 3 FIN - Experiencia */


/*** PASO 4 INICIO - Tipo de Ejercicio */
function fnUsuarioTipoEjercicioObtener(){
	$result;
	$listTiposEjercicio = array();
	$tipoEjercicio = array();

	$tipoEjercicio['intTipo'] = 1;
	$tipoEjercicio['decOrden'] = 1;
	$tipoEjercicio['strTipo'] = 'PESAS';
	$tipoEjercicio['strDescripcion'] = 'Ejercicio de resistencia muscular con peso adicional, maquinas, mancuernas, barras, etc.';
	$listTiposEjercicio[0] = $tipoEjercicio;

	$tipoEjercicio['intTipo'] = 2;
	$tipoEjercicio['decOrden'] = 2;
	$tipoEjercicio['strTipo'] = 'CARDIO';
	$tipoEjercicio['strDescripcion'] = 'Cardio HIIT (intervalos de alta intensidad), videos de ejercicios en aplicaciones móviles, máquinas de cardio (caminadora, elíptica, bicicleta), caminatas, deporte. Etc.';
	$listTiposEjercicio[1] = $tipoEjercicio;

	$tipoEjercicio['intTipo'] = 3;
	$tipoEjercicio['decOrden'] = 3;
	$tipoEjercicio['strTipo'] = 'AMBOS';
	$tipoEjercicio['strDescripcion'] = 'Crossfit, pesas y sesiones de cardio.';
	$listTiposEjercicio[2] = $tipoEjercicio;

	$result['listTiposEjercicio'] = $listTiposEjercicio;			

	try {
		$strUsuario = $_GET['strUsuario']; //obligatorio
		$buscar = fnDBResult("SELECT * FROM wp_vivemov_users_ejercicio WHERE strUsuario = '$strUsuario' ORDER BY decId DESC LIMIT 1;");
		if (count($buscar) > 0) {
			$buscar = $buscar[0];
			$result['listTipoEjercicioUsuario'] = $buscar;			
		}else{
			$result['strTipo'] = 'warning';
			$result['strMensaje'] = 'Sin Experiencia!';
		}
	} catch (Exception $e) {
		$result['strTipo'] = 'error';
		$result['strMensaje'] = $e->getMessage();
	}
	fnEndCallback($result);
}
function fnUsuarioTipoEjercicioGuardar() {
	$result;

	$strUsuario = $_GET['strUsuario']; //obligatorio
	$intEjercicio = $_GET['intEjercicio']; //obligatorio

	$registro = array(
		'strUsuario' 	=>	$strUsuario,
		'intEjercicio'	=>	$intEjercicio
	);
	if(fnDBInsert("wp_vivemov_users_ejercicio", $registro) == true) {
		$result['strTipo'] = 'success';
		$result['strMensaje'] = 'Guardado!';
	} else {
		$result['strTipo'] = 'error';
		$result['strMensaje'] = 'Inconveniente al guardar!';
	}
	fnEndCallback($result);
}
/*** PASO 4 FIN - Tipo de Ejercicio */


/*** PASO 5 INICIO - META */
function fnUsuarioMetaObtener(){
	$result;
	$listMetas = array();
	$meta = array();

	$meta['intTipo'] = 1;
	$meta['decOrden'] = 1;
	$meta['strTipo'] = 'BAJAR';
	$meta['strDescripcion'] = 'Quiero bajar grasa corporal y peso.';
	$listMetas[0] = $meta;

	$meta['intTipo'] = 2;
	$meta['decOrden'] = 2;
	$meta['strTipo'] = 'MANTENER';
	$meta['strDescripcion'] = 'Ya llegue a mi meta y quiero mantener mi peso y mi progreso.';
	$listMetas[1] = $meta;

	$meta['intTipo'] = 3;
	$meta['decOrden'] = 3;
	$meta['strTipo'] = 'SUBIR';
	$meta['strDescripcion'] = 'Quiero subir masa muscular y peso.';
	$listMetas[2] = $meta;

	$result['listMetas'] = $listMetas;			

	try {
		$strUsuario = $_GET['strUsuario']; //obligatorio
		$buscar = fnDBResult("SELECT * FROM wp_vivemov_users_meta WHERE strUsuario = '$strUsuario' ORDER BY decId DESC LIMIT 1;");
		if (count($buscar) > 0) {
			$buscar = $buscar[0];
			$result['listMetaUsuario'] = $buscar;			
		}else{
			$result['strTipo'] = 'warning';
			$result['strMensaje'] = 'Sin Experiencia!';
		}
	} catch (Exception $e) {
		$result['strTipo'] = 'error';
		$result['strMensaje'] = $e->getMessage();
	}
	fnEndCallback($result);
}
function fnUsuarioMetaGuardar() {
	$result;

	$strUsuario = $_GET['strUsuario']; //obligatorio
	$intMeta = $_GET['intMeta']; //obligatorio

	$registro = array(
		'strUsuario' 	=>	$strUsuario,
		'intMeta'	=>	$intMeta
	);
	if(fnDBInsert("wp_vivemov_users_meta", $registro) == true) {
		$result['strTipo'] = 'success';
		$result['strMensaje'] = 'Guardado!';
	} else {
		$result['strTipo'] = 'error';
		$result['strMensaje'] = 'Inconveniente al guardar!';
	}
	fnEndCallback($result);
}
/*** PASO 5 FIN - META */


/*** PASO 6 INICIO - CALCULADORA */
function fnUsuarioCalculadoraObtener(){
	$result;
	try {
		$strUsuario = $_GET['strUsuario']; //obligatorio
		$decMetabolismo = 0;
		$decActivityFactor = array(0,1.12,1.375,1.425,1.425,1.55,1.725,1.9);

		$itemPeso = fnDBResult("SELECT * FROM wp_vivemov_users_informacion WHERE strUsuario = '$strUsuario' ORDER BY intId DESC LIMIT 1;");
		$itemMeta = fnDBResult("SELECT * FROM wp_vivemov_users_meta WHERE strUsuario = '$strUsuario' ORDER BY decId DESC LIMIT 1;");
		$itemActividad = fnDBResult("SELECT * FROM wp_vivemov_users_actividad_gasto_energetico WHERE strUsuario = '$strUsuario' ORDER BY decId DESC LIMIT 1;");
		$misPorciones = fnDBResult("SELECT * FROM wp_vivemov_users_porciones WHERE strUsuario = '$strUsuario' ORDER BY decId DESC LIMIT 1;");

		if (count($itemPeso) > 0 && count($itemMeta) > 0 && count($itemActividad) > 0) {
			$itemPeso = $itemPeso[0];
			$decAltura = $itemPeso['decAltura'];
			$decPeso = $itemPeso['decPeso'];
			$decGrasa = $itemPeso['decGrasa'];
			$intSexo = $itemPeso['intSexo'];
			$intEdad = $itemPeso['intEdad'];
			$intActividadTipo = $itemActividad[0]['intActividad'];

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

			$intMeta = $itemMeta[0]['intMeta'];

			$intMetaValor = array(0,-500,0,350);

			$decTDEE = ($decMetabolismo * $decActivityFactor[$intActividadTipo]);
			$decEjercicio = 500 - ($decTDEE-$decMetabolismo);
			$decCalorias = $decTDEE + $intMetaValor[$intMeta];

			$decProteinas = array(0,0,0,0);
			$decCarbo = array(0,0,0,0);
			$decGrasas = array(0,0,0,0);

			$decIndicador = 0.8;
			if ($intSexo == 1) { //hombres
    			if ($intMeta == 1) { //bajar
      				if ($intExperiencia == 3 && $decIMC < 26) { //avanzado + no obeso
      					$decIndicador = 0.9;
      				}else if ($decIMC >= 26) { //hombre + bajar + obeso
      					$decIndicador = 0.6;
      				}else{
      					$decIndicador = 0.7;          
      				}
			    }else{ //para manter o subir es igual
			    	$decIndicador = 0.7;
			    }
			}else{ //mujeres
			    if ($intMeta == 1) { //bajar
			    	if ($intExperiencia == 3 && $decIMC < 26) { //avanzada + no obesa
			      		$decIndicador = 0.9;
			     	}else if ($decIMC >= 26) { //mujer + bajar + obesa
			      		$decIndicador = 0.6;
			      	}else{
			      		$decIndicador = 0.7;          
			     	}
			    }else{ //para mantener o subir es igual
			    	$decIndicador = 0.7;
			    }
			}

			$decProteinas[2]=$decPeso * $decIndicador;
			$decProteinas[1]=$decProteinas[2]*4;
			if ($decCalorias != 0) {
				$decProteinas[0]=$decProteinas[1]/$decCalorias;
			    // $decProteinas[3]=($decProteinas[2]/30)-1;
				$decProteinas[3]=($decProteinas[2]/25.0);
			}

			$decGrasas[0] = 0.30;
			$decGrasas[1] = $decGrasas[0] * $decCalorias;
			$decGrasas[2] = $decGrasas[1] / 9;
			$decGrasas[3] = ($decGrasas[2] / 14)-1;

			$decCarbo[0] = 1 - ($decProteinas[0]+$decGrasas[0]);
			$decCarbo[0] = 1 - ($decProteinas[0]+$decGrasas[0]);
			$decCarbo[1] = $decCalorias * $decCarbo[0];
			$decCarbo[2] = $decCarbo[1] / 4;
			$decCarbo[3] = $decCarbo[2] / 25;

			$listCalculadora = array();
			$calculadora = array();

			$calculadora['strTipo'] = '%';
			$calculadora['decProteinas'] = fnRedondear($decProteinas[0]);
			$calculadora['decCarbohidratos'] = fnRedondear($decCarbo[0]);
			$calculadora['decGrasas'] = fnRedondear($decGrasas[0]);
			$calculadora['decTotal'] = 100;
			$listCalculadora[0] = $calculadora;

			$calculadora['strTipo'] = 'CALORIAS/MACRO';
			$calculadora['decProteinas'] = fnRedondear($decProteinas[1]);
			$calculadora['decCarbohidratos'] = fnRedondear($decCarbo[1]);
			$calculadora['decGrasas'] = fnRedondear($decGrasas[1]);
			$calculadora['decTotal'] = fnRedondear($decCalorias);
			$listCalculadora[1] = $calculadora;

			$calculadora['strTipo'] = 'MACROS';
			$calculadora['decProteinas'] = fnRedondear($decProteinas[2]);
			$calculadora['decCarbohidratos'] = fnRedondear($decCarbo[2]);
			$calculadora['decGrasas'] = fnRedondear($decGrasas[2]);
			$calculadora['decTotal'] = 0;
			$listCalculadora[2] = $calculadora;

			$calculadora['strTipo'] = 'PORCIONES Recomendadas';
			$calculadora['decProteinas'] = fnRedondear($decProteinas[3]);
			$calculadora['decCarbohidratos'] = fnRedondear($decCarbo[3]);
			$calculadora['decGrasas'] = fnRedondear($decGrasas[3]);
			$calculadora['decTotal'] = 0;
			$listCalculadora[3] = $calculadora;
			
		    if (count($misPorciones) > 0) {
		    	$misPorciones = $misPorciones[0];
		    	
		    	$calculadora['strTipo'] = 'Tus Propias Porciones';
				$calculadora['decProteinas'] = ($misPorciones != null ? fnRedondearCUSTOMUP_1($misPorciones['intProteina']) : '0');
				$calculadora['decCarbohidratos'] = ($misPorciones != null ? fnRedondearCUSTOMUP_1($misPorciones['intCarbohidrato']) : '0');
				$calculadora['decGrasas'] = ($misPorciones != null ? fnRedondearCUSTOMUP_1($misPorciones['intGrasa']) : '0');
				$calculadora['decTotal'] = 0;
				$listCalculadora[4] = $calculadora;
		    }

			$result['listCalculadora'] = $listCalculadora;	
		}else{
			$result['strTipo'] = 'warning';
			$result['strMensaje'] = 'Sin Datos!';
		}
	} catch (Exception $e) {
		$result['strTipo'] = 'error';
		$result['strMensaje'] = $e->getMessage();
	}
	fnEndCallback($result);
}
function fnUsuarioCalculadoraGuardar() {
	$result;

	$strUsuario = $_GET['strUsuario']; //obligatorio
	$intProteina = $_GET['intProteina']; //obligatorio
	$intCarbohidrato = $_GET['intCarbohidrato']; //obligatorio
	$intGrasa = $_GET['intGrasa']; //obligatorio, valor o cero

  	fnDBResult("UPDATE wp_vivemov_users_porciones as D
              SET D.bitActivo = 0
              WHERE D.strUsuario = '$strUsuario' AND D.bitActivo = 1;");

	$registro = array(
		'strUsuario'=>$strUsuario,
		'intProteina'=> $intProteina,
		'intCarbohidrato'=> $intCarbohidrato,
		'intGrasa'=> $intGrasa,
		'bitActivo'=>1,
		'datCreacion'=>date('Y-m-d H:i:s')
	);
	if(fnDBInsert("wp_vivemov_users_porciones", $registro) == true) {
		$result['strTipo'] = 'success';
		$result['strMensaje'] = 'Guardado!';
	} else {
		$result['strTipo'] = 'error';
		$result['strMensaje'] = 'Inconveniente al guardar!';
	}
	fnEndCallback($result);
}
/*** PASO 6 FIN - CALCULADORA */


/*** FOOD JOURNAL - INICIO */
function fnFoodJournalObtener(){
	//listado de todos los diarios encabezados que existen
	$strUsuario = $_GET['strUsuario']; //obligatorio
	$decDiario = $_GET['decDiario']; //obligatorio, valor o cero, si es cero es devolver todos los diarios
	$result;
    if ($decDiario == null || $decDiario == 0) {
        $result['listDiarios'] = fnDBResult("SELECT intId,datFecha, SUM(intProteinas) intProteinas,SUM(intCarbohidratos) intCarbohidratos,SUM(intGrasas) intGrasas,SUM(intVegetales) intVegetales,SUM(intLibres) intLibres,strNota
            FROM wp_vivemov_users_diario
            WHERE strUsuario = '$strUsuario' GROUP BY intId,datFecha,strNota ORDER BY datFecha DESC");
    }else{
        $result['listDiarios'] = fnDBResult("SELECT intId,datFecha, SUM(intProteinas) intProteinas,SUM(intCarbohidratos) intCarbohidratos,SUM(intGrasas) intGrasas,SUM(intVegetales) intVegetales,SUM(intLibres) intLibres,strNota
        FROM wp_vivemov_users_diario
        WHERE strUsuario = '$strUsuario' AND intId = $decDiario GROUP BY intId,datFecha,strNota ORDER BY datFecha DESC");
    }
    $result['strTipo'] = 'success';
	fnEndCallback($result);
}
function fnFoodJournalTablaDetalleObtener(){
	//listado del detalle del diario tal por tiempo(desayuno,almuerzo,cena) pueden remover el parametor del tiempo y el where del $intTiempo, para que devuleva toda la info del diario por el $decDiario
	$intTiempo = $_GET['intTiempo']; //obligatorio
	$decDiario = $_GET['decDiario']; //obligatorio, valor o cero, si es cero es devolver todos los diarios
	$result;
    $result['listDiarios'] = fnDBResult("
    	SELECT T.strTiempo,T.bitPrincipal,DD.*, AP.strAlimento
            ,um.strUnidadMedida
            ,((AP.decPorcion /
              (
            CASE
                WHEN AP.decProteina > 0 THEN AP.decProteina
                WHEN AP.decCarbohidratos > 0 THEN AP.decCarbohidratos
                WHEN AP.decGrasa > 0 THEN AP.decGrasa
                WHEN AP.decVegetales > 0 THEN AP.decVegetales
                WHEN AP.decLibre > 0 THEN AP.decLibre
              END
                  /
             CASE
                WHEN DD.intProteinas > 0 THEN DD.intProteinas
                WHEN DD.intCarbohidratos > 0 THEN DD.intCarbohidratos
                WHEN DD.intGrasas > 0 THEN DD.intGrasas
                WHEN DD.intVegetales > 0 THEN DD.intVegetales
                WHEN DD.intLibres > 0 THEN DD.intLibres
              END
             )
            ) * 1) intCantidadTomado
        FROM wp_vivemov_alimentos_tiempo T
        INNER JOIN wp_vivemov_users_diario_detalle DD ON DD.intDiario = $decDiario AND DD.intTiempo = T.intId  
        INNER JOIN wp_vivemov_alimentos_porciones AP ON AP.intId = DD.intAlimentoPorcion
        INNER JOIN wp_vivemov_alimentos_unidad_medida um on um.intId = AP.intUnidadMedida
        WHERE T.bitActivo = 1
        	AND T.intId = $intTiempo
        ORDER BY AP.strAlimento ASC
    ");
    $result['strTipo'] = 'success';
	fnEndCallback($result);
}
function fnFoodJournalActualizar_P_CH_G_V($strUsuario,$decDiario){
	//para actualizar los totales en el encabezado del dario para saber cuanto lleva de P,CH,G,V en cada diario
	//P= proteirna
	//CH= carbohidratos
	//G = grasas
	//V=vegetales
    fnDBResult("
        UPDATE wp_vivemov_users_diario_detalle as D
        INNER JOIN wp_vivemov_alimentos_porciones AP ON AP.intId = D.intAlimentoPorcion
        SET D.intProteinas = D.devCantidad * AP.decProteina
        ,D.intCarbohidratos = D.devCantidad * AP.decCarbohidratos
        ,D.intGrasas = D.devCantidad * AP.decGrasa
        ,D.intVegetales = D.devCantidad * AP.decVegetales
        ,D.intLibres = D.devCantidad * AP.decLibre
        WHERE D.intDiario = $decDiario AND D.strUsuario = '$strUsuario';
    ");
    fnDBResult("
	    UPDATE wp_vivemov_users_diario as E
	    SET intProteinas = (SELECT SUM(D.intProteinas) FROM wp_vivemov_users_diario_detalle D WHERE D.intDiario = $decDiario GROUP BY D.intDiario)
	    ,intCarbohidratos = (SELECT SUM(D.intCarbohidratos) FROM wp_vivemov_users_diario_detalle D WHERE D.intDiario = $decDiario GROUP BY D.intDiario)
	    ,intGrasas = (SELECT SUM(D.intGrasas) FROM wp_vivemov_users_diario_detalle D WHERE D.intDiario = $decDiario GROUP BY D.intDiario)
	    ,intVegetales = (SELECT SUM(D.intVegetales) FROM wp_vivemov_users_diario_detalle D WHERE D.intDiario = $decDiario GROUP BY D.intDiario)
	    ,intLibres = (SELECT SUM(D.intLibres) FROM wp_vivemov_users_diario_detalle D WHERE D.intDiario = $decDiario GROUP BY D.intDiario)
	    WHERE intId = $decDiario  AND strUsuario = '$strUsuario';
    ");
}
function fnFoodJournalTiemposObtener(){
	$result;
    $result['listTiempos'] = fnDBResult("SELECT * FROM wp_vivemov_alimentos_tiempo T WHERE T.bitActivo = 1 ORDER BY decOrden ASC");
	$result['strTipo'] = 'success';
	fnEndCallback($result);
}
function fnFoodJournalAlimentosObtener(){
	$result;
    $result['listAlimentos'] = fnDBResult("SELECT ap.*, um.strUnidadMedida FROM wp_vivemov_alimentos_porciones ap INNER JOIN wp_vivemov_alimentos_unidad_medida um ON um.intId = ap.intUnidadMedida WHERE ap.bitActivo=1");
	$result['strTipo'] = 'success';
	fnEndCallback($result);
}
function fnFoodJournalUnidadMedidaObtener(){
	$result;
    $result['listUM'] = fnDBResult("SELECT * FROM wp_vivemov_alimentos_unidad_medida WHERE bitActivo = 1 ORDER BY strUnidadMedida ASC;");
	$result['strTipo'] = 'success';
	fnEndCallback($result);
}
function fnFoodJournalTablaDetalleAgregar(){
	$result;

	$strUsuario = $_GET['strUsuario']; //obligatorio
	$decDiario = $_GET['decDiario']; //obligatorio
	$detTiempo = $_GET['detTiempo']; //obligatorio
	$decAlimento = $_GET['decAlimento']; //obligatorio
	$decCantidadTomada = $_GET['decCantidadTomada']; //obligatorio

    $registro = array(
        'intDiario'             => $decDiario,
        'strUsuario'            => $strUsuario,
        'intTiempo'             => $detTiempo,
        'intAlimentoPorcion'    => $decAlimento,
        'devCantidad'           => $decCantidadTomada,
        'strDescripcion'        => '...',
        'intProteinas'          => 0,
        'intCarbohidratos'      => 0,
        'intGrasas'             => 0,
        'intVegetales'          => 0,
        'intLibres'             => 0,
        'datModificado'         => date('Y-m-d H:i:s'),
    );
	if(fnDBInsert("wp_vivemov_users_diario_detalle", $registro) == true) {
		fnFoodJournalActualizar_P_CH_G_V($strUsuario,$decDiario);
		$result['strTipo'] = 'success';
		$result['strMensaje'] = 'Guardado!';
	} else {
		$result['strTipo'] = 'error';
		$result['strMensaje'] = 'Inconveniente al guardar!';
	}
 	fnEndCallback($result);
}
function fnFoodJournalTablaDetalleActualizar(){
	//para editar el alimento, tiempo, o cantidad comida del journal detalle
	$result;

	$strUsuario = $_GET['strUsuario']; //obligatorio
	$decDiario = $_GET['decDiario']; //obligatorio
	$decDetalle = $_GET['decDetalle']; //obligatorio
	$detTiempo = $_GET['detTiempo']; //obligatorio
	$decAlimento = $_GET['decAlimento']; //obligatorio
	$decCantidadTomada = $_GET['decCantidadTomada']; //obligatorio

    fnDBResult('
        UPDATE wp_vivemov_users_diario_detalle as D
        SET
            D.intTiempo = '.$detTiempo.'
            ,D.intAlimentoPorcion = '.$decAlimento.'
            ,D.devCantidad = '.$decCantidadTomada.'
        WHERE D.intId = '.$decDetalle);
    fnFoodJournalActualizar_P_CH_G_V($strUsuario,$decDiario);

	$result['strTipo'] = 'success';
	$result['strMensaje'] = 'Editado!';

 	fnEndCallback($result);
}
function fnFoodJournalTablaDetalleEliminar(){
	//para eliminar row del detalle del alimento
	$result;

	$strUsuario = $_GET['strUsuario']; //obligatorio
	$decDiario = $_GET['decDiario']; //obligatorio
	$decDetalle = $_GET['decDetalle']; //obligatorio

    fnDBDelete('wp_vivemov_users_diario_detalle','intId',$decDetalle);

    fnFoodJournalActualizar_P_CH_G_V($strUsuario,$decDiario);

	$result['strTipo'] = 'success';
	$result['strMensaje'] = 'Eliminado!';

 	fnEndCallback($result);
}
/*** FOOD JOURNAL - FIN */


/*** BD ALIMENTOS - INICIO */
//base de datos de todos los alimentos ya sean del admin o de cada usuario personal
function fnBDAlimentosObtener(){
	$result;
    $result['listAlimentos'] = fnDBResult("
    	SELECT DISTINCT *
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
	      INNER JOIN wp_vivemov_alimentos_unidad_medida um ON um.intId = ap.intUnidadMedida WHERE ap.bitActivo=1 and ap.strUsuario IN('anamoralescpt','amms24')
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
	  ORDER BY intOrdenTipo ASC, ap.strAlimento ASC
    ");
	$result['strTipo'] = 'success';
	fnEndCallback($result);
}
function fnBDAlimentosAgregar(){
	$result;

	$strUsuario = $_GET['strUsuario']; //obligatorio
	$intA_Cantidad = $_GET['intA_Cantidad']; //obligatorio
	$intA_UM = $_GET['intA_UM']; //obligatorio
	$strA_Alimento = $_GET['strA_Alimento']; //obligatorio
	$decA_Proteina = $_GET['decA_Proteina']; //obligatorio
	$decA_Carbs = $_GET['decA_Carbs']; //obligatorio
	$decA_Grasa = $_GET['decA_Grasa']; //obligatorio
	$decA_Libre = $_GET['decA_Libre']; //obligatorio

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
	if(fnDBInsert("wp_vivemov_alimentos_porciones", $registro) == true) {
		$result['strTipo'] = 'success';
		$result['strMensaje'] = 'Guardado!';
	} else {
		$result['strTipo'] = 'error';
		$result['strMensaje'] = 'Inconveniente al guardar!';
	}
 	fnEndCallback($result);
}
function fnBDAlimentosActualizar(){
	//para editar el alimento, tiempo, o cantidad comida del journal detalle
	$result;

	$strUsuario = $_GET['strUsuario']; //obligatorio
	$intA_Id = $_GET['intA_Id']; //obligatorio
	$intA_Cantidad = $_GET['intA_Cantidad']; //obligatorio
	$intA_UM = $_GET['intA_UM']; //obligatorio
	$strA_Alimento = $_GET['strA_Alimento']; //obligatorio
	$decA_Proteina = $_GET['decA_Proteina']; //obligatorio
	$decA_Carbs = $_GET['decA_Carbs']; //obligatorio
	$decA_Grasa = $_GET['decA_Grasa']; //obligatorio
	$decA_Libre = $_GET['decA_Libre']; //obligatorio

    fnDBResult('
        UPDATE wp_vivemov_alimentos_porciones as D
        SET
            D.decPorcion = '.$intA_Cantidad.'
            ,D.intUnidadMedida = '.$intA_UM.'
            ,D.strAlimento = '."'".$strA_Alimento."'".'
            ,D.decProteina = '.$decA_Proteina.'
            ,D.decCarbohidratos = '.$decA_Carbs.'
            ,D.decGrasa = '.$decA_Grasa.'
            ,D.decLibre = '.$decA_Libre.'
        WHERE D.intId = '.$intA_Id);

	$result['strTipo'] = 'success';
	$result['strMensaje'] = 'Editado!';

 	fnEndCallback($result);
}
/*** BD ALIMENTOS - FIN */


/*** RECETAS - INICIO */
function fnRecetasObtener(){
	$result;
	$strUsuario = $_GET['strUsuario']; //obligatorio
    $result['listRecetas'] = fnDBResult("SELECT * FROM wp_vivemov_recetas WHERE strUsuario = '$strUsuario' AND bitActivo = 1 ORDER BY strNombre ASC;");
	$result['strTipo'] = 'success';
	fnEndCallback($result);
}
function fnRecetasAgregar(){
	$result;

	$strUsuario = $_GET['strUsuario']; //obligatorio
	$strNombre = $_GET['strNombre']; //obligatorio

	$registro = array(
		'strUsuario'  => $strUsuario,
	   	'strNombre'  => $strNombre,
	    'datCreacion' => date('Y-m-d H:i:s'),
	    'bitActivo'   => 1
	 );
	if(fnDBInsert("wp_vivemov_recetas", $registro) == true) {
		$result['strTipo'] = 'success';
		$result['strMensaje'] = 'Guardado!';
	} else {
		$result['strTipo'] = 'error';
		$result['strMensaje'] = 'Inconveniente al guardar!';
	}
 	fnEndCallback($result);
}
function fnRecetasActualizar(){
	$result;

	$strUsuario = $_GET['strUsuario']; //obligatorio
	$intReceta = $_GET['intReceta']; //obligatorio
	$strNombre = $_GET['strNombre']; //obligatorio

    fnDBResult("UPDATE wp_vivemov_recetas as D SET D.strNombre = '$strNombre' WHERE D.intId = $intReceta; ");

	$result['strTipo'] = 'success';
	$result['strMensaje'] = 'Editado!';

 	fnEndCallback($result);
}
function fnRecetasEliminar(){
	$result;

	$intReceta = $_GET['intReceta']; //obligatorio

    fnDBResult('UPDATE wp_vivemov_recetas as D SET D.bitActivo = 0 WHERE D.intId = $intReceta;');

	$result['strTipo'] = 'success';
	$result['strMensaje'] = 'Eliminado!';

 	fnEndCallback($result);
}

function fnRecetaDetalleObtener(){
	//para obtener el detalle de los alimentos de cada receta
	$result;

	$intReceta = $_GET['intReceta']; //obligatorio

    $result['listRecetas'] = fnDBResult("
    	SELECT D.*, A.strAlimento, um.strUnidadMedida
	    FROM wp_vivemov_recetas_detalle D
	    INNER JOIN wp_vivemov_alimentos_porciones A ON A.intId = D.decAlimento
	    INNER JOIN wp_vivemov_alimentos_unidad_medida um ON um.intId = A.intUnidadMedida
	    WHERE D.intReceta = $intReceta AND D.bitActivo = 1
	    ORDER BY A.strAlimento ASC;
    ");
	$result['strTipo'] = 'success';
	fnEndCallback($result);
}
function fnRecetaDetalleAgregar(){
	$result;

	$strUsuario = $_GET['strUsuario']; //obligatorio
	$intReceta = $_GET['intReceta']; //obligatorio
	$decCantidad = $_GET['decCantidad']; //obligatorio
	$decAlimento = $_GET['decAlimento']; //obligatorio

	$registro = array(
	    'intReceta'  => $intReceta,
	    'decCantidad'  => $decCantidad,
	    'decAlimento'  => $decAlimento,
	    'bitActivo'   => 1
	);
	if(fnDBInsert("wp_vivemov_recetas_detalle", $registro) == true) {
		$result['strTipo'] = 'success';
		$result['strMensaje'] = 'Guardado!';
	} else {
		$result['strTipo'] = 'error';
		$result['strMensaje'] = 'Inconveniente al guardar!';
	}
 	fnEndCallback($result);
}
function fnRecetaDetalleActualizar(){
	$result;

	$strUsuario = $_GET['strUsuario']; //obligatorio
	$intReceta = $_GET['intReceta']; //obligatorio
	$decCantidad = $_GET['decCantidad']; //obligatorio

    fnDBResult('UPDATE wp_vivemov_recetas_detalle as D SET D.decCantidad = $decCantidad WHERE D.intId = $intReceta;');

	$result['strTipo'] = 'success';
	$result['strMensaje'] = 'Editado!';

 	fnEndCallback($result);
}
function fnRecetaDetalleEliminar(){
	$result;

	$intReceta = $_GET['intReceta']; //obligatorio

    fnDBResult('UPDATE wp_vivemov_recetas_detalle as D SET D.bitActivo = 0 WHERE D.intId = $intReceta;');

	$result['strTipo'] = 'success';
	$result['strMensaje'] = 'Eliminado!';

 	fnEndCallback($result);
}

function fnRecetaFoodJournalAgregar(){
	//para agregar el detalle de la receta el food journal que usuario desee cargar
	//es decir, si tengo mi receta de 3 alimentos, y quiero agregarla al journal del desayuno de hoy, se me van agregar esos 3 alimentos a ese journal para el tiempo del desayuno
	$result;

	$strUsuario = $_GET['strUsuario']; //obligatorio
	$decDiario = $_GET['decDiario']; //obligatorio
	$intTiempo = $_GET['intTiempo']; //obligatorio
	$intReceta = $_GET['intReceta']; //obligatorio

	fnDBResult("
	    INSERT INTO wp_vivemov_users_diario_detalle(intDiario,strUsuario,intTiempo,intAlimentoPorcion,devCantidad,strDescripcion,intProteinas,intCarbohidratos,intGrasas,intVegetales,intLibres,datModificado)
	    SELECT $decDiario
	    ,'$strUsuario'
	    ,$intTiempo
	    ,RD.decAlimento
	    ,(RD.decCantidad / AP.decPorcion)
	    ,'...'
	    ,(RD.decCantidad / AP.decPorcion) * AP.decProteina
	    ,(RD.decCantidad / AP.decPorcion) * AP.decCarbohidratos
	    ,(RD.decCantidad / AP.decPorcion) * AP.decGrasa
	    ,(RD.decCantidad / AP.decPorcion) * AP.decVegetales
	    ,(RD.decCantidad / AP.decPorcion) * AP.decLibre
	    ,now()
	    FROM wp_vivemov_recetas_detalle AS RD
	    INNER JOIN wp_vivemov_alimentos_porciones AS AP on AP.intId = RD.decAlimento
	    WHERE RD.intReceta = $intReceta AND RD.bitActivo = 1 AND AP.bitActivo = 1
	");

	fnFoodJournalActualizar_P_CH_G_V($strUsuario,$decDiario);
	$result['strTipo'] = 'success';
	$result['strMensaje'] = 'Guardado!';

 	fnEndCallback($result);
}
/*** RECETAS - FIN */


if (isset($_GET['strAccion'])) {
	$execute = $_GET['strAccion'];
	$execute();
}else{
	$result['strTipo'] = 'error';
	$result['strMensaje'] = 'Accion no encontrada!';
	fnEndCallback($result);
}
?>