<?php

// $user='engovclientxp_eneo_dashboard';
// $pass='n8)SY^4*x#f0eneo_dashboard';
// $database='engovclientxp_dashboard_eneo';

//             $bdd = new PDO("mysql:host=localhost;dbname=$database", $user, $pass, array(
//              PDO::ATTR_PERSISTENT => true,
//         PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
//             ));
//              $bdd->setAttribute(PDO::ATTR_ERRMODE, 
//              PDO::ERRMODE_EXCEPTION);
            //  echo "connect to db";


// $user='qcpi7360_eneo_admin';
// $pass='aaQBrKuxOT?4eneo_admin';
// $database='qcpi7360_eneo_dashbord';
// $bdd = new PDO("mysql:host=109.234.162.244;PORT:2083;dbname=$database", $user, $pass, array(
//     PDO::ATTR_PERSISTENT => true
// ));
// $bdd->setAttribute(PDO::ATTR_ERRMODE, 
// PDO::ERRMODE_EXCEPTION);
 



$user='admin_eneo_user';
$pass='877Daa2g_eneo';
$database='admin_eneo_dashboard';
$host='37.187.28.103';


try{
    $bdd = new PDO("mysql:host=$host;dbname=$database", $user, $pass,array(
        PDO::ATTR_PERSISTENT => true,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
    ));
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	// echo "Connection succesfull ";
}
catch(PDOException $e){
    echo "Connection error ".$e->getMessage(); 
    exit;
}
?>