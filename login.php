<?php

session_start();

//include_once "conexion.php";

function verificar_login($user,$password) {

    //$sql = "SELECT count(*) as filas FROM usuarios WHERE usuario = '$user' and password = '$password'";
    //$rec = mysql_query($sql);
    

    //$row = mysql_fetch_array($rec);
    
    //$userr = "alberto";
    //$pass = "albes";
    //if($userr==$user and $pass==$password){
    //    return true;
    //}else{
    //    return false;
    //}
    //if($row["FILAS"]>0){
    //    return true;
    //}else{
    //    return false;
    //}
}


//if(!isset($_SESSION['userid'])){
//
//    if(isset($_POST['login'])){
//
//        if(verificar_login($_POST['user'],$_POST['pass'])){
//
//            $_SESSION['userid'] = $_POST['user'];
//            header("location:inicio.php");
//        }else{
//
//            echo"<script>alert('Usuario y/o Contraseña Ingresados Incorrectamente, Intente Nuevamente');window.location='index.php';</script>";
//
//        }
//    }
//}



?>

<!DOCTYPE html>

<html>

<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
        <meta name="viewport" content ="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="css/bootstrap-responsive.css">
        <link rel="stylesheet" type="text/css" href="js/bootstrap.min.js">
        <meta charset="utf-8" /> 
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        <style type="text/css">
.centrar {
height: 150px;
width: 300px;
margin-right: auto;
margin-left: auto;

}
</style>
</head>
<body>

    <div class="container">
        <div class="centar">
            <h4 class="text-center">Ingrese Usuario y contraseña</h4>


        </div>  
        <div class="row-fluid">
            <div class="span12">&nbsp;</div>
        </div>
        <div class="rowfluid">
            <div class="centrar">
                <form action="login.php" method="POST" class ="login" >
                    <label>Usuario:</label>
                    <input type="text" name="user" size="15">
                    <br>
                    <br>
                    <label>Contraseña: </label>
                    <input type="password" name="pass" size="15">
                    <br>
                    <input type="submit" value="Login" name="login">
                </form>
            </div>
        </div>

    </div>
    
</body>
</html>

