<?php

function fnTabla_1(){
    $strUsuario = wp_get_current_user()->user_login;
    global $intEdad,$decAltura,$decPeso,$decIMC,$decRMR,$decTDEE,$decAF,$decEjercicio;
    global $intSexo,$intExperiencia,$intEjercicio,$intMeta;
    fnMiInformacion_cargar($strUsuario);    
    
    $decCalorias = $decRMR;

    $decIndicador = 0.8;
    if ($intSexo == 1) { //hombres
      if ($intMeta == 1) { //bajar
        if ($intExperiencia == 1 && $decIMC < 26) { //avanzado + no obeso
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
        if ($intExperiencia == 1 && $decIMC < 26) { //avanzada + no obesa
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

    // $decProteinas_MACRO = ($decPeso * 0.8);
    $decProteinas_MACRO = ($decPeso * $decIndicador);
    $decProteinas_CALORIAS_MACRO = ($decProteinas_MACRO * 4);
    $decPorcentaje_Proteinas = $decProteinas_CALORIAS_MACRO / $decCalorias;
    $decPorcentaje_Grasa = 0.30;
    $decPorcentaje_Carbohidratos = 1 - ($decPorcentaje_Proteinas + $decPorcentaje_Grasa);

    $decProteinas_PORCIONES = ($decProteinas_MACRO / 30);

    $decCarbohidratos_CARBOHIDRATOS = ($decCalorias * $decPorcentaje_Carbohidratos);
    $decCarbohidratos_MACROS = ($decCarbohidratos_CARBOHIDRATOS / 4);
    $decCarbohidratos_PORCIONES = ($decCarbohidratos_MACROS / 25);

    $decGrasas_CARBOHIDRATOS = ($decCalorias * $decPorcentaje_Grasa);
    $decGrasas_MACROS = ($decGrasas_CARBOHIDRATOS / 9);
    $decGrasas_PORCIONES = ($decGrasas_MACROS / 14);

    $decTotal_1 = ($decPorcentaje_Grasa + $decPorcentaje_Carbohidratos + $decPorcentaje_Proteinas) * 100;
    $decTotal_2 = $decProteinas_CALORIAS_MACRO + $decCarbohidratos_CARBOHIDRATOS + $decGrasas_CARBOHIDRATOS;
    $decTotal_3 = 110 * (2.5 + 5 + 2.5);

    $strResponse = '<style>  </style>';
    $strResponse = $strResponse.'<table id="customers">
      <tr>
        <th colspan=3>Calorias</th>
        <th>'.$decCalorias.'</th>
        <th class="blanco"></th>
      </tr>
      <tr>
        <td></td>
        <td class="amarillo">PROTEINAS</td>
        <td class="naranja">CARBOHIDRATOS</td>
        <td class="celeste">GRASAS</td>
        <td>Total</td>
      </tr>
      <tr>
        <td>%</td>
        <td class="amarillo">'.fnRedondearUP($decPorcentaje_Proteinas*100).'%</td>
        <td class="naranja">'.fnRedondearUP($decPorcentaje_Carbohidratos*100).'%</td>
        <td class="celeste">'.fnRedondearUP($decPorcentaje_Grasa*100).'%</td>
        <td>'.$decTotal_1.'%</td>
      </tr>
      <tr>
        <td>CALORIAS/MACRO</td>
        <td class="amarillo">'.fnRedondear($decProteinas_CALORIAS_MACRO).'</td>
        <td class="naranja">'.fnRedondear($decCarbohidratos_CARBOHIDRATOS).'</td>
        <td class="celeste">'.fnRedondear($decGrasas_CARBOHIDRATOS).'</td>
        <td>'.$decTotal_2.'</td>
      </tr>
      <tr>
        <td>MACROS</td>
        <td class="amarillo">'.fnRedondear($decProteinas_MACRO).'</td>
        <td class="naranja">'.fnRedondear($decCarbohidratos_MACROS).'</td>
        <td class="celeste">'.fnRedondear($decGrasas_MACROS).'</td>
        <td></td>
      </tr>
      <tr>
        <td>PORCIONES</td>
        <td class="amarillo">'.fnRedondear($decProteinas_PORCIONES).'</td>
        <td class="naranja">'.fnRedondear($decCarbohidratos_PORCIONES).'</td>
        <td class="celeste">'.fnRedondear($decGrasas_PORCIONES).'</td>
        <td></td>
      </tr>
      <tr>
        <td colspan=5></td>
      </tr>
      <tr class="verde">
        <td>DEFICIT</td>
        <td>2.5</td>
        <td>5</td>
        <td>2.5</td>
        <td class="blanco"></td>
      </tr>
      <tr class="naranja">
        <td>MANTENIMIENTO</td>
        <td></td>
        <td></td>
        <td></td>
        <td class="blanco">Calorias</td>
      </tr>
      <tr class="rojo">
        <td>EXCESO</td>
        <td></td>
        <td></td>
        <td></td>
        <td class="blanco">'.$decTotal_3.'</td>
      </tr>
      <tr class="morado">
        <td>REFEED</td>
        <td></td>
        <td></td>
        <td></td>
        <td class="blanco"></td>
      </tr>
    </table>';

    echo $strResponse;
}
?>