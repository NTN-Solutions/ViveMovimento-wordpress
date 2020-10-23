<?php
/**
* Plugin Name: Vive Movimento - Nicaragua
* Plugin URI: https://vivemovimento.com/
* Description: Plugin realizado desde cero para Vive Movimento
* Version: 1.0.0
* Author: steven vilchez castillo
* Author URI: https://vivemovimento.com/
**/
function registration_form( $username, $password, $email, $website, $first_name, $last_name, $nickname, $bio ) {
    echo '
    <style>
    div {
        margin-bottom:2px;
    }
     
    input{
        margin-bottom:4px;
    }
    </style>
    ';
 
    echo '
    <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
    <div>
    <label for="username">Username <strong>*</strong></label>
    <input type="text" name="username" value="' . ( isset( $_POST['username'] ) ? $username : null ) . '">
    </div>
     
    <div>
    <label for="password">Password <strong>*</strong></label>
    <input type="password" name="password" value="' . ( isset( $_POST['password'] ) ? $password : null ) . '">
    </div>
     
    <div>
    <label for="email">Email <strong>*</strong></label>
    <input type="text" name="email" value="' . ( isset( $_POST['email']) ? $email : null ) . '">
    </div>
     
    <div>
    <label for="website">Website</label>
    <input type="text" name="website" value="' . ( isset( $_POST['website']) ? $website : null ) . '">
    </div>
     
    <div>
    <label for="firstname">First Name</label>
    <input type="text" name="fname" value="' . ( isset( $_POST['fname']) ? $first_name : null ) . '">
    </div>
     
    <div>
    <label for="website">Last Name</label>
    <input type="text" name="lname" value="' . ( isset( $_POST['lname']) ? $last_name : null ) . '">
    </div>
     
    <div>
    <label for="nickname">Nickname</label>
    <input type="text" name="nickname" value="' . ( isset( $_POST['nickname']) ? $nickname : null ) . '">
    </div>
     
    <div>
    <label for="bio">About / Bio</label>
    <textarea name="bio">' . ( isset( $_POST['bio']) ? $bio : null ) . '</textarea>
    </div>
    <input type="submit" name="submit" value="Register"/>
    </form>
    ';
}

function registration_validation( $username, $password, $email, $website, $first_name, $last_name, $nickname, $bio )  {
    global $reg_errors;
    $reg_errors = new WP_Error;
    if ( empty( $username ) || empty( $password ) || empty( $email ) ) {
        $reg_errors->add('field', 'Required form field is missing');
    }
    if ( 4 > strlen( $username ) ) {
        $reg_errors->add( 'username_length', 'Username too short. At least 4 characters is required' );
    }
    if ( username_exists( $username ) )
        $reg_errors->add('user_name', 'Sorry, that username already exists!');
    if ( 5 > strlen( $password ) ) {
        $reg_errors->add( 'password', 'Password length must be greater than 5' );
    }
    if ( !is_email( $email ) ) {
        $reg_errors->add( 'email_invalid', 'Email is not valid' );
    }
    if ( email_exists( $email ) ) {
        $reg_errors->add( 'email', 'Email Already in use' );
    }
    // if ( ! empty( $website ) ) {
    //     if ( ! filter_var( $website, FILTER_VALIDATE_URL ) ) {
    //         $reg_errors->add( 'website', 'Website is not a valid URL' );
    //     }
    // }
    if ( is_wp_error( $reg_errors ) ) { 
        foreach ( $reg_errors->get_error_messages() as $error ) {        
            echo '<div>';
            echo '<strong>ERROR</strong>:';
            echo $error . '<br/>';
            echo '</div>';           
        }    
    }
}
function complete_registration() {
    global $reg_errors, $username, $password, $email, $website, $first_name, $last_name, $nickname, $bio;
    if ( 1 > count( $reg_errors->get_error_messages() ) ) {
        $userdata = array(
        'user_login'    =>   $username,
        'user_email'    =>   $email,
        'user_pass'     =>   $password,
        'user_url'      =>   $website,
        'first_name'    =>   $first_name,
        'last_name'     =>   $last_name,
        'nickname'      =>   $nickname,
        'description'   =>   $bio,
        );
        $user = wp_insert_user( $userdata );
        echo 'Registration complete. Goto <a href="' . get_site_url() . '/wp-login.php">login page</a>.';   
    }
}

function custom_registration_function() {
    if ( isset($_POST['submit'] ) ) {
        registration_validation(
        $_POST['username'],
        $_POST['password'],
        $_POST['email'],
        $_POST['website'],
        $_POST['fname'],
        $_POST['lname'],
        $_POST['nickname'],
        $_POST['bio']
        );
         
        // sanitize user form input
        global $username, $password, $email, $website, $first_name, $last_name, $nickname, $bio;
        $username   =   sanitize_user( $_POST['username'] );
        $password   =   esc_attr( $_POST['password'] );
        $email      =   sanitize_email( $_POST['email'] );
        $website    =   esc_url( $_POST['website'] );
        $first_name =   sanitize_text_field( $_POST['fname'] );
        $last_name  =   sanitize_text_field( $_POST['lname'] );
        $nickname   =   sanitize_text_field( $_POST['nickname'] );
        $bio        =   esc_textarea( $_POST['bio'] );
 
        // call @function complete_registration to create the user
        // only when no WP_error is found
        complete_registration(
        $username,
        $password,
        $email,
        $website,
        $first_name,
        $last_name,
        $nickname,
        $bio
        );
    }
 
    registration_form(
        $username,
        $password,
        $email,
        $website,
        $first_name,
        $last_name,
        $nickname,
        $bio
        );
}

function fnMensaje($intTipo, $strMensaje){
    if($strMensaje=='')return;
    if ($intTipo == 1) { //success
        return '<div class="vc_message_box vc_message_box-solid-icon vc_message_box-square vc_color-success"><div class="vc_message_box-icon"><i class="fa fa-check"></i></div><p>'.$strMensaje.'</p></div>';
    }else if ($intTipo == 2) { //error
        return '<div class="vc_message_box vc_message_box-solid vc_message_box-square vc_color-danger"><div class="vc_message_box-icon"><i class="fa fa-times"></i></div><p>'.$strMensaje.'</p></div>';
    }
}

function fnRedondear($decValor){
    return round($decValor, 2);
}
function fnRedondearUP($decValor) { 
    $pow = pow ( 10, 0 ); 
    return ( ceil ( $pow * $decValor ) + ceil ( $pow * $decValor - ceil ( $pow * $decValor ) ) ) / $pow; 
}
function fnRedondearCUSTOMUP($decValor)
{   /*habia pedido un tipo de redondeo y ahora el normal, por eso se comenta */
    return round($decValor);
    // $whole = floor($decValor);
    // $fraction = $decValor - $whole;
    // if ($fraction <= 0.14) {
    //     return $whole;
    // }else if ($fraction >= 0.14 && $fraction <= 0.44) {
    //     return ($whole + 0.5);
    // }else if ($fraction >= 0.45) {
    //     return ($whole + 1);
    // }
}
function fnRedondearCUSTOMUP_1($decValor){
    return round($decValor, 1);
}
function fnUtils_core(){
    echo '
    <script>
        $(".tm-titlebar-wrapper").hide();
        $("#main").css({"padding":"0px"});
        $(".wpb_text_column.wpb_content_element").hide();
    </script>';
}
include('resumen.php');
include('0_estilos.php');
include('1_tab.php');
include('2_tab.php');
include('2_tab_1.php');
include('2_tab_2.php');
include('3_tab.php');
include('4_tab.php');
include('5_tab.php');
include('bd_tab.php');
include('7_tab.php');
include('8_tab.php');
include('9_tab.php');
function fnUsuarioLogeadoViveMovimento() {
    $user = wp_get_current_user(); 
    return $user->exists();
}

function fnViveMovimento_resumen(){
    ob_start();
    fnViveMovimento_resumen_init();
    return ob_get_clean();
} 
function fnViveMovimento_topbar(){
    ob_start();    
    if (fnUsuarioLogeadoViveMovimento() == false) {
        echo '<li class="dropCustom dropdown" style="z-index: 99999 !important;">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-user"></i>Cuenta <span class="caret"></span>
          </a>
          <ul class="dropCustomDown dropdown-menu">
            <li class="sinPaddinIzquierdaVivemovMenu">
                <a href="/Login/" class="a_top" style="color: white;"><i class="fa fa-sign-in" aria-hidden="true"></i> Iniciar Sesión
                </a>
            </li>
          </ul>
        </li>';

    }else{
        // echo '<a href="/mi-cuenta" class="a_top" style="color: white;"><i class="fa fa-user" aria-hidden="true"></i> Mi Cuenta</a>';
        echo '<li class="dropCustom dropdown" style="z-index: 99999 !important;">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-user" aria-hidden="true"></i> Cuenta <span class="caret"></span>
          </a>
          <ul class="dropCustomDown dropdown-menu">
            <li class="sinPaddinIzquierdaVivemovMenu"><a href="/mi-cuenta"><i class="fa fa-user" aria-hidden="true"></i> Mi Cuenta</a></li>
            <li><a href="/user/?action=tab_Paso_5"><i class="fa fa-cutlery" aria-hidden="true"></i> Food Journal</a></li>
            <li><a href="/user/?action=tab_Paso_6"><i class="fa fa-book"></i> Base de Datos</a></li>
            <li><a href="/user/?action=tab_Paso_7"><i class="fa fa-check"></i> Check In</a></li>
            <li><a href="'.wp_logout_url( home_url()).'"><i class="fa fa-sign-out"></i> Salir</a></li>
          </ul>
        </li>';
    }
    return ob_get_clean();
} 
// The callback function that will replace [book]
function fnFontawesome() {
        echo '<style>
            .sinPaddinIzquierdaVivemovMenu{
                padding: 0px !important;
            }
            .dropCustom{
                z-index: 99999 !important;
            }
            .dropCustom:hover .dropCustomDown { display: block; }
            .a_top:hover {
              color: white !important;
            }
            .dropdown-menu>li>a>i {
                color: #333 !important;
            }
          </style>';

    if ( is_page('Perfil') || is_page('User') || is_page('Settings') || is_page('mi-cuenta') ): 
        echo '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">';
        echo '<script src="https://kit.fontawesome.com/1ea98a15ec.js" crossorigin="anonymous"></script>';
        // echo '<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30="crossorigin="anonymous"></script>';
        echo '<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>';
        echo '<link rel="stylesheet" href="/wp-content/plugins/vivemovimento/datepicker/css/bootstrap-datepicker3.min.css">';
        // echo '<script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous"></script>';
    endif;
}
function fnFontawesome2() {
    if ( is_page('Perfil') || is_page('User') || is_page('Settings')  || is_page('mi-cuenta') ): 
        echo '<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>';
        echo '<script src="/wp-content/plugins/vivemovimento/datepicker/js/bootstrap-datepicker.min.js"></script>';
    endif;
}

function fnViveMovimentoRedireccionLuegoCompra( $order_id ){
    $order = wc_get_order( $order_id );
    $url = '/shop';
    if ( ! $order->has_status( 'failed' ) ) {
        $order = wc_get_order( $order_id );
        $items = $order->get_items();
        $bitSuscripcionComprada = false;
        foreach ( $items as $item ) {
            $strProducto = strtolower($item->get_name());
            if (strpos($strProducto, 'suscripción') !== false) {
                $bitSuscripcionComprada = true;
            }
            // $product_name = $item->get_name();
            // $product_id = $item->get_product_id();
            // $product_variation_id = $item->get_variation_id();
        }
        if ($bitSuscripcionComprada == true) {
            echo '<a type="button"  href="/user/?action=tab_Paso_4" class="btn btn-success btn-sm btn-block"><i class="fa fa-check" aria-hidden="true"></i> Ver Mi Plan Nutricional</a>';
        }
        // wp_safe_redirect( $url );
        // exit;
    }else{
        wp_safe_redirect( $url );
    }
}


function redirect_admin( $redirect_to, $request, $user ){
    if ( isset( $user->roles ) && is_array( $user->roles ) ) {
        // if ( in_array( 'administrator', $user->roles ) == false ) {
            $redirect_to = '/mi-cuenta/';
        // }
    }
    return $redirect_to;
}

function fnViveMovimento() {
    ob_start();
    fnViveMovimento_Init();
    return ob_get_clean();
}
function wp_get_current_user(){
    // session_start();
    $current_user_info = array();
    $current_user_info = array(
        'user_login' => (isset($_SESSION['strUsuario']) ? $_SESSION['strUsuario'] : ""),
        'user_mail' => (isset($_SESSION['strCorreo']) ? $_SESSION['strCorreo'] : ""),
        'user_admin' => (isset($_SESSION['bitAdmin']) ? ($_SESSION['bitAdmin'] == 1 ? true : false) : ""),
        'teste' => 'asdf'
    );
    // echo '===========>';
    // print_r($current_user_info);
    // echo '<===========';
    return $current_user_info;
}
function fnViveMovimento_INIT_SISTEMA(){
    ob_start();
    session_start();
    if (isset($_GET['login_api_usr']) && $_GET['login_api_usr'] != null && $_GET['login_api_usr'] != '') {
        $_SESSION['strUsuario'] = $_GET['login_api_usr'];
        $_SESSION['strCorreo'] = $_GET['login_api_email'];
        $_SESSION['bitAdmin'] = $_GET['login_api_admin'];
        // fnViveMovimento_Init();
        $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        if (strpos($url,'tab_Paso_') !== false) {
            fnViveMovimento_Init();
        } else if (strpos($url,'cuenta') !== false){
            fnViveMovimento_resumen_init();
        }
    }else if (isset($_GET['login_api_out']) && $_GET['login_api_out'] != null && $_GET['login_api_out'] != '') {
       session_start();
       unset($_SESSION["strUsuario"]);
       unset($_SESSION["strCorreo"]);
       unset($_SESSION["bitAdmin"]);
    }else{
        // fnViveMovimento_Init();
        $url = 'http://' . $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
        if (strpos($url,'tab_Paso_') !== false) {
            fnViveMovimento_Init();
        } else if (strpos($url,'cuenta') !== false){
            fnViveMovimento_resumen_init();
        }
    }
}
function fn_insert($strTabla, $registro){
    //wp_vivemov_users_informacion
    $servername = "localhost";
    $username = "vivemovimento_own";
    $password = "vivemovimento123";
    $dbname = "vivemovimento";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
       die("Connection failed: " . $conn->connect_error);
    } 
    // echo '======>';
    // print_r($registro);
    // echo '<======';
    
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
        echo "New record created successfully";
    } else {
        $bitSuccess = false;
        echo "Error: " . $sql . "" . mysqli_error($conn);
    }
    $conn->close();
    return $bitSuccess;
}
function get_results($strQuery){
    $servername = "localhost";
    $username = "vivemovimento_own";
    $password = "vivemovimento123";
    $dbname = "vivemovimento";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = $strQuery;
    $result = $conn->query($sql);

    $datos = array();
    $intFila = 0;
    if ($result->num_rows > 0) {
        // echo "<table><tr><th>ID</th><th>Name</th></tr>";
        // // output data of each row
        while($row = $result->fetch_assoc()) {
            $datos[$intFila] = $row;
            $intFila += 1;
            // echo "<tr><td>".$row["id"]."</td><td>".$row["firstname"]." ".$row["lastname"]."</td></tr>";
        }
        // echo "</table>";
        // $datos = $result->fetch_assoc();
        // print_r($datos);
    } else {
        $datos = array();
    }
    $conn->close();
    return $datos;
}
function admin_url($strFuncion){
    // $strURL = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] .'/'. $strFuncion;
    $strURL = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    return $strURL;
}

$bitNormal = true;
$url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
if (strpos($url,'&action=fnViveMovimento') === false) {
    $bitNormal = true;
} else {
    $bitNormal = false;
}

if ($bitNormal == true) {
?>
    <!doctype html>
    <html lang="es">
      <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>Vivemovimento</title>

        <!-- Bootstrap -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>

        <script src="/js/pdfobject.min.js"></script>

        <script src="/datepicker/js/bootstrap-datepicker.min.js"></script>

      </head>
      <body>
    <?php
        //INICIAR SISTEMA
        fnViveMovimento_INIT_SISTEMA();
    ?>
      </body>
    </html>
<?php 
}else{
    if (strpos($url,'fnViveMovimentoDiarioAgregar') !== false){
        session_start();
        fnViveMovimentoDiarioAgregar();
    }else if (strpos($url,'fnViveMovimentoDiarioAgregar') !== false){
        session_start();
        fnViveMovimentoDiarioAgregar();
    }else if (strpos($url,'fnViveMovimentoDiarioEliminar') !== false){
        session_start();
        fnViveMovimentoDiarioEliminar();
    }else if (strpos($url,'fnViveMovimentoDiarioDetalleTabla') !== false){
        session_start();
        fnViveMovimentoDiarioDetalleTabla();
    }else if (strpos($url,'fnViveMovimentoRecetaClonar') !== false){
        session_start();
        fnViveMovimentoRecetaClonar();
    }else if (strpos($url,'fnViveMovimentoRecetaAgregar') !== false){
        session_start();
        fnViveMovimentoRecetaAgregar();
    }else if (strpos($url,'fnViveMovimentoRecetaEditar') !== false){
        session_start();
        fnViveMovimentoRecetaEditar();
    }else if (strpos($url,'fnViveMovimentoRecetaEliminar') !== false){
        session_start();
        fnViveMovimentoRecetaEliminar();
    }else if (strpos($url,'fnViveMovimentoRecetaListado') !== false){
        session_start();
        fnViveMovimentoRecetaListado();
    }else if (strpos($url,'fnViveMovimentoRecetaListadoCore') !== false){
        session_start();
        fnViveMovimentoRecetaListadoCore();
    }else if (strpos($url,'fnViveMovimentoRecetaDetalleAgregar') !== false){
        session_start();
        fnViveMovimentoRecetaDetalleAgregar();
    }else if (strpos($url,'fnViveMovimentoRecetaDetalleEditar') !== false){
        session_start();
        fnViveMovimentoRecetaDetalleEditar();
    }else if (strpos($url,'fnViveMovimentoRecetaDetalleEliminar') !== false){
        session_start();
        fnViveMovimentoRecetaDetalleEliminar();
    }else if (strpos($url,'fnViveMovimentoRecetaJournalAgregar') !== false){
        session_start();
        fnViveMovimentoRecetaJournalAgregar();
    }else if (strpos($url,'fnViveMovimentoPorcionesPropias') !== false){
        session_start();
        fnViveMovimentoPorcionesPropias();
    }else if (strpos($url,'fnViveMovimentoOneToOnetabla') !== false){
        session_start();
        fnViveMovimentoOneToOnetabla();
    }else if (strpos($url,'fnViveMovimentoOneToOneAgregar') !== false){
        session_start();
        fnViveMovimentoOneToOneAgregar();
    }else if (strpos($url,'fnViveMovimentoOneToOneEliminar') !== false){
        session_start();
        fnViveMovimentoOneToOneEliminar();
    }else if (strpos($url,'fnViveMovimentoDiarioDetalleReceta') !== false){
        session_start();
        fnViveMovimentoDiarioDetalleReceta();
    }
}
?>