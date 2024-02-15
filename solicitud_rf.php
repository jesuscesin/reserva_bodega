<?php
//error_reporting(E_ALL);
//session_start();
require_once "conexion.php";
require_once "config.php";
require_once "generar_insert.php";
//require_once "page.ext";

function pagina_solicitud(){
	$formulario = "
	<form>
		<div class='container-fluid'>
			<div class='row'>
				<div class='col-md-6'>
					<div class='form-group'>
						<label>Codigo Usuario Solicitante</label>
						<input autofocus type='text' id='cod_solicitante' class='form-control' required/>
					</div>
				</div>
				<div class='col-md-6'>
					<div class='form-group'><label>Codigo QR Radio Frecuencia</label>
					<input type='text' id='cod_rf_solicitud' class='form-control' required />
					</div>
				</div>
			</div>
			<div class='row'>
				<div class='col-md-12'>
					<div class='form-group'>
						<label>Comentario</label>
						<input type='text' id='coment_solicitud' class='form-control' required/>
					</div>
						<button type='button' class='btn btn-primary' id='enviar_solicitud'> Enviar Solicitud</button>
						<button type='button' class='btn btn-secondary ml-auto' id='atras'>Atras 🚶‍♂️</button>
				</div>
			</div>
		</div>
	</form>";
	return $formulario;
}

function pagina_devolucion(){
	$formulario = '
	<form>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label>Codigo Usuario que lo está devolviendo </label>
						<input autofocus type="text" id="cod_devolucion" class="form-control"/>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label>Codigo QR Radio Frecuencia</label>
						<input type="text" id="cod_frec_devolucion" class="form-control" />
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<label>Comentario</label>
						<input type="text" id="coment_devolucion" class="form-control"/>
					</div>
					<button type="button" class="btn btn-primary" id="enviar_devolucion">Devolver Radio</button>
					<button type="button" class="btn btn-secondary" id="atras">Atras 🚶‍♂️</button>
				</div>
			</div>
		</div>
	</form>';
	return $formulario;
}

function pagina_inicio_solicitud(){
	$formulario = '
	<div class="card-body">
        <div class="row" id="divbutton">
            <div class="col">
                <div class="col text-center">
                    <label class="">Solicitar Radio Frecuencia</label>
                </div>
                <div class="col text-center">
                	<button type="button" class="btn btn-primary btn-block" id="solicitar_rf">Solicitar</button>
                </div>
            </div>
          	<div class="col">
				<div class="col text-center">
                	<label class="" placeholder="Last name">Devolver Radio Frecuencia</label>
             	</div>
				<div class="col text-center mg">
                	<button type="button" class="btn btn-warning btn-block mx-auto"
                    	id="devolver_rf">Devolver
					</button>
            	</div>
        	</div>
    	</div>
        <div class="row" id="pantalla_solicitar"></div>
    	<div class="row" id="pantalla_devolver"></div>
    </div>';
	return $formulario;
}

function ver_estados_radios(){
	global $tipobd_portal,$conexion_portal;
	$querysel = "SELECT RF_ID,
	ESTADO_RF,
	USU_ACTUAL,
	SUBSTR(FEC_HOR_MD,0,4) || '-' || SUBSTR(FEC_HOR_MD,5,2) || '-' || SUBSTR(FEC_HOR_MD,7,2) || ' ' || SUBSTR(FEC_HOR_MD,9,2) || ':' || SUBSTR(FEC_HOR_MD,11,2) AS FECHA
	FROM RADIO_FRECUENCIA
	ORDER BY 1";
    //echo $querysel;
    $rss = querys($querysel,$tipobd_portal,$conexion_portal);
    while($fila = ver_result($rss, $tipobd_portal)){
        $ordenes_compra[]=array(
            "CODIGO_QR"			=>$fila["RF_ID"],
            "ESTADO"			=>$fila["ESTADO_RF"],
			"USU_ACTUAL"		=>$fila["USU_ACTUAL"],
            "FEC_HOR_MD"		=>$fila["FECHA"]
        );
    }
    echo json_encode($ordenes_compra);
}

function ver_solicitudes_filtro($ini, $fin){
	global $tipobd_portal,$conexion_portal;
	//--mejoras--
	//hacer la consulta de las fechas con un solo valor ingresado, inicio o fin

	$querysel = "SELECT ID_SOLICIT, RF_ID, COD_US_SOL, FEC_HOR_SO, EST_SOLICI,COD_US_DEV,FEC_HOR_DE
				FROM SOLICITUDES_RF
				WHERE SUBSTR(FEC_HOR_SO, '1', '8')
				BETWEEN '$ini' AND '$fin'
				ORDER BY TO_NUMBER(ID_SOLICIT)";

	//echo $querysel;
	$rss = querys($querysel,$tipobd_portal,$conexion_portal);
	while($fila = ver_result($rss, $tipobd_portal)){
        $lista_solicitudes[]=array(
            "ID"				=>$fila["ID_SOLICIT"],
            "CODIGO_QR"			=>$fila["RF_ID"],
            "ESTADO"			=>$fila["EST_SOLICI"],
			"USU_ACTUAL"		=>$fila["COD_US_SOL"],
            "FEC_HOR_MD"		=>formatDate($fila["FEC_HOR_SO"]),
			"COD_US_DEV"		=>$fila["COD_US_DEV"],
            "FEC_HOR_DE"		=>$fila["FEC_HOR_DE"],
        );
    }
    echo json_encode($lista_solicitudes);
	//$rss = querys($querysel,$tipobd_portal,$conexion_portal);
}

function obtener_correlativo(){
	global $tipobd_portal,$conexion_portal;

    $querysel = "SELECT NVL(MAX(TO_NUMBER(ID_SOLICIT)),0) AS ID_SOLICIT FROM SOLICITUDES_RF";
    $rss = querys($querysel,$tipobd_portal,$conexion_portal);
    $fila = ver_result($rss, $tipobd_portal);    

    if($fila["ID_SOLICIT"] == null or $fila["ID_SOLICIT"] == 0){
        return $fila["ID_SOLICIT"];
    }else{
        return $fila["ID_SOLICIT"] + 1; 
    }
}

function existe_radio($v){
	global $tipobd_portal,$conexion_portal;

	$querysel = "SELECT
				NVL((SELECT USU_ACTUAL FROM RADIO_FRECUENCIA WHERE RF_ID = '$v'), 
	  			'No existe') AS USUARIO
  				FROM dual";
	$rss = querys($querysel,$tipobd_portal,$conexion_portal);
	$fila = ver_result($rss, $tipobd_portal);

	return $fila["USUARIO"];
}

function crear_solicitud() {
    global $tipobd_portal,$conexion_portal;

	//datos para ingresarlos en la tabla "SOLICITUDES"
	$v_id_correlativo = obtener_correlativo(); //id_solicitud
	$v_cod_solicitante = $_POST['cod_solicitante']; //codigo usuario, posteriormente se vera este ingreso con la pistola
	$v_qr_radio = $_POST['cod_rf_solicitud']; //el RF_ID que es el codigo qr vinculado a la radio
	$v_com_solici = $_POST['coment_solicitud']; //comentario ingresado en el formulario
	$fecha_hora_actual = date("YmdHis"); //fecha de cuando se solicito la RF

	$v_existe_radio = existe_radio($v_qr_radio);

	//Validamos que el "$v_existe_radio" sea distinto a 'No Existe' 
	if ($v_existe_radio != 'No existe') {
		//Validamos que el "$v_existe_radio" sea igual a 'Disponible' 
		if ($v_existe_radio == 'Disponible') {
			//Insertamos los datos en la talba "Solicitudes_rf" en el caso que el "$v_existe_radio" sea "Disponible"
			$queryinsert = "INSERT INTO SOLICITUDES_RF(ID_SOLICIT, RF_ID, COD_US_SOL, COD_US_DEV,
						FEC_HOR_SO, FEC_HOR_DE, COM_ENTREG, COM_DEVOLU, EST_SOLICI)
						VALUES ('$v_id_correlativo', '$v_qr_radio', '$v_cod_solicitante', 'Pendiente',
						'$fecha_hora_actual', 'Pendiente','$v_com_solici', ' ', 'En Proceso')";
			$rss1 = querys($queryinsert,$tipobd_portal, $conexion_portal);

			//Actualizamos los datos en la tabla "RADIO_FRECUENCIA":
			$queryupdate = "UPDATE RADIO_FRECUENCIA SET ESTADO_RF = 'En Uso',
							USU_ACTUAL = '$v_cod_solicitante',
							FEC_HOR_MD = '$fecha_hora_actual'
							WHERE RF_ID = '$v_qr_radio'";
			$rss2 = querys($queryupdate,$tipobd_portal, $conexion_portal);
			echo $v_existe_radio;
		} else {
			//En el caso que el "$v_existe_radio" no sea igual a "Disponible" significa que la radio la esta utilizando un usuario 
			echo $v_existe_radio;
		}
	}else {
		//En caso que "$v_existe_radio" sea igual a 'No Existe' indica que la radio no se encuentra ingresada en el sistema
		echo $v_existe_radio;
	}
}

function actualizar_solicitud() {
    global $tipobd_portal,$conexion_portal;

	//datos para ingresarlos en la tabla "SOLICITUDES"
	$v_id_correlativo = obtener_correlativo(); //id_solicitud
	$v_codigo_usuario = $_POST['cod_devolucion']; //codigo usuario, posteriormente se vera este ingreso con la pistola
	$v_qr_radio = $_POST['cod_rf_devolucion']; //el RF_ID que es el codigo qr vinculado a la radio
	$v_comentario_devolver = $_POST['coment_devolucion']; //comentario ingresado en el formulario
	$fecha_hora_actual = date("YmdHis"); //fecha de cuando se solicito la RF

	$v_existe_radio = existe_radio($v_qr_radio);

	//Validamos que el "$v_existe_radio" sea distinto a 'No Existe' 
	if ($v_existe_radio != 'No existe') {
		//Validamos que el "$v_existe_radio" sea distinto a 'Disponible' 
		if ($v_existe_radio != 'Disponible') {
			//Actualizamos la tabla "SOLICITUDES_RF":
		$queryupdate1 = "UPDATE SOLICITUDES_RF SET COD_US_DEV = '$v_codigo_usuario',
						FEC_HOR_DE = '$fecha_hora_actual',
						COM_DEVOLU = '$v_comentario_devolver',
						EST_SOLICI = 'Terminada'
						where RF_ID = '$v_qr_radio' and EST_SOLICI = 'En Proceso'";
		$rss1 = querys($queryupdate1,$tipobd_portal, $conexion_portal);
		//----------------------------------------------
		//Actualizando tabla "RADIO_FRECUENCIA":
		$queryupdate2 = "UPDATE RADIO_FRECUENCIA SET ESTADO_RF = 'Disponible',
				USU_ACTUAL = 'Disponible',
				FEC_HOR_MD = '$fecha_hora_actual'
				WHERE RF_ID = '$v_qr_radio'";
		$rss2 = querys($queryupdate2,$tipobd_portal, $conexion_portal);
		echo $v_existe_radio;
		} else {
			//En el caso que el "$v_existe_radio" no sea igual a "Disponible" significa que la radio la esta utilizando un usuario 
			echo $v_existe_radio;
		}
	}else {
		//En caso que "$v_existe_radio" sea igual a 'No Existe' indica que la radio no se encuentra ingresada en el sistema
		echo $v_existe_radio;
	}
}

//Validar los campos del formulario esten con informacion y que estos sean enviados por un metodo "POST"
if(isset($_POST['cod_solicitante']) && isset($_POST['cod_rf_solicitud']) && isset($_POST['coment_solicitud']) && !empty($_POST['cod_solicitante']) && !empty($_POST['cod_rf_solicitud']) && !empty($_POST['coment_solicitud'])) {
    crear_solicitud();
}

//Validar los campos del formulario esten con informacion y que estos sean enviados por un metodo "POST"
if(isset($_POST['cod_devolucion']) && isset($_POST['cod_rf_devolucion']) && isset($_POST['coment_devolucion']) && !empty($_POST['cod_devolucion']) && !empty($_POST['cod_rf_devolucion']) && !empty($_POST['coment_devolucion'])) {
    actualizar_solicitud();
}

if(isset($_GET['enviar_solicitudes'])){
    enviar_solicitudes();
}

if(isset($_POST['ver_estados_radios'])){
    ver_estados_radios();
}

if (isset($_GET['solicitud']) && $_GET['solicitud'] === 'obtener_formulario') {
    $contenido = pagina_solicitud();
    echo $contenido;
}

if (isset($_GET['devolucion']) && $_GET['devolucion'] === 'obtener_formulario') {
    $contenido = pagina_devolucion();
    echo $contenido;
}

if(isset($_POST["ver_tabla_filtros"])){

	$ini			 = $_POST['fecha_ini'];
	$fin 	 		 = $_POST['fecha_fin'];
	///////formateo de fecha
	$ini = str_replace(array('/'),'',$ini);
		$parte1 = substr($ini,4,8); //12
		$parte2 = substr($ini,2,2); //345
		$parte3 = substr($ini,0,2); //456
		$ini = $parte1.$parte2.$parte3;
	$fin = str_replace(array('/'),'',$fin);
		$parte1 = substr($fin,4,8); //12
		$parte2 = substr($fin,2,2); //345
		$parte3 = substr($fin,0,2); //456
		$fin = $parte1.$parte2.$parte3;
    
    ver_solicitudes_filtro($ini,$fin);
}

?>