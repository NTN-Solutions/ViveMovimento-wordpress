
        // sanitize user form input
        // global $username, $password, $email, $website, $first_name, $last_name, $nickname, $bio;
        // $username   =   sanitize_user( $_POST['username'] );
        // $password   =   esc_attr( $_POST['password'] );
        // $email      =   sanitize_email( $_POST['email'] );
        // $website    =   esc_url( $_POST['website'] );
        // $first_name =   sanitize_text_field( $_POST['fname'] );
        // $last_name  =   sanitize_text_field( $_POST['lname'] );
        // $nickname   =   sanitize_text_field( $_POST['nickname'] );
        // $nickname   =   sanitize_number_field( $_POST['nickname'] );
        // $bio        =   esc_textarea( $_POST['bio'] );

        $buscar = $wpdb->get_results("SELECT * FROM wp_vivemov_users_informacion WHERE strUsuario = '$strUsuario' LIMIT 1;");
        if(count($buscar) == 0){
            if($_SESSION["intFormulario"] == 1){
                $_SESSION["intFormulario"] = 0;
            }else{
                $_SESSION["intFormulario"] = 1;
            }
        }else{
            $where = array(
                'strUsuario' => $strUsuario
            );
            $wpdb->update( 'wp_vivemov_users_informacion', $registro, $where, null, null );
            echo fnMensaje(1,'Listo, datos guardados!');
        }
        // $user = wp_insert_user( $registro );
        // echo 'Registration complete. Goto <a href="' . get_site_url() . '/wp-login.php">login page</a>.';   






 <div class="col-md-3 col-xs-6 col-sm-6">
    <label for="decIMC"><i class="fas fa-calculator"></i> BMI (automatico)</label>
    <input type="number" name="decIMC" value="' . ( isset( $_POST['decIMC']) ? $_POST['decIMC'] : $decIMC ) . '" disabled>
    </div>
     
    <div class="col-md-3 col-xs-6 col-sm-6">
    <label for="decRMR">RMR</label>
    <input type="number" name="decRMR" value="' . ( isset( $_POST['decRMR']) ? $_POST['decRMR'] : $decRMR ) . '">
    </div>
     
    <div class="col-md-3 col-xs-6 col-sm-6">
    <label for="decTDEE">TDEE</label>
    <input type="number" name="decTDEE" value="' . ( isset( $_POST['decTDEE']) ? $_POST['decTDEE'] : $decTDEE ) . '">
    </div>     
     
    <div class="col-md-3 col-xs-6 col-sm-6">
    <label for="decAF">AF</label>
    <input type="number" name="decAF" value="' . ( isset( $_POST['decAF']) ? $_POST['decAF'] : $decAF ) . '">
    </div>
     
    <div class="col-md-3 col-xs-6 col-sm-6">
    <label for="decEjercicio">Ejercicio (automatico)</label>
    <input type="number" name="decEjercicio" value="' . ( isset( $_POST['decEjercicio']) ? $_POST['decEjercicio'] : $decEjercicio ) . '" disabled>
    </div>