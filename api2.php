<?php
	include("connect_bd.php");

	header('Content-type:application/json;charset=utf-8');
	// Autoriser l'accès depuis n'importe quelle origine
header('Access-Control-Allow-Origin: *');

// Autoriser les méthodes de requête spécifiées
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

// Autoriser les en-têtes personnalisés et les en-têtes par défaut
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

// Spécifier la durée de validité des résultats préchargés (en secondes)
header('Access-Control-Max-Age: 3600');

	$key = "f3c6f9640ce74c2fb73e27b955064425";

	function getIVFromUser($identifier) {
		include("connect_bd.php");
	
		try {
			// Connexion à la base de données
			
	
			// Préparation de la requête de sélection
			$stmt = $bdd->prepare("SELECT IV FROM utilisateur WHERE EMAIL = :identifier OR PHONE = :identifier");
	
			// Exécution de la requête avec la valeur du paramètre
			$stmt->bindParam(':identifier', $identifier);
			$stmt->execute();
	
			// Récupération du résultat de la requête
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
	
			if ($row) {
				return $row['IV'];
			} else {
				return null;
			}
		} catch (PDOException $e) {
			echo "Erreur lors de la récupération de l'IV de l'utilisateur depuis la base de données : " . $e->getMessage();
			return null;
		}
	}


	function getPDWFromUser($identifier) {
		include("connect_bd.php");
	
		try {
			// Connexion à la base de données
			
	
			// Préparation de la requête de sélection
			$stmt = $bdd->prepare("SELECT `PASSWORD` FROM utilisateur WHERE EMAIL = :identifier OR PHONE = :identifier");
	
			// Exécution de la requête avec la valeur du paramètre
			$stmt->bindParam(':identifier', $identifier);
			$stmt->execute();
	
			// Récupération du résultat de la requête
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
	
			if ($row) {
				return $row['PASSWORD'];
			} else {
				return null;
			}
		} catch (PDOException $e) {
			echo "Erreur lors de la récupération du PASSWORD de l'utilisateur depuis la base de données : " . $e->getMessage();
			return null;
		}
	}
	


	function encrypt($data, $key, $iv) {
		$iv='f3c6f9640ce74c2a';
		$cipher = "AES-256-CBC";
		$options = OPENSSL_RAW_DATA;
		$encrypted = openssl_encrypt($data, $cipher, $key, $options, $iv);
		return base64_encode($encrypted);
	}
	
	function decrypt($data, $key, $iv) {
		$iv='f3c6f9640ce74c2a';
		$cipher = "AES-256-CBC";
		$options = OPENSSL_RAW_DATA;
		$decrypted = openssl_decrypt(base64_decode($data), $cipher, $key, $options, $iv);
		return $decrypted;
	}

// Génération d'un vecteur d'initialisation aléatoire



 	if(isset($_GET["sendOTP"]))
		{
           $tr=(isset($_GET["EMAIL"]) || isset($_GET["PHONE"])) ;
    
         if( $tr)
         {
         $val=$_GET["EMAIL"]??$_GET["PHONE"];

		 $query="SELECT ID_UTILISATEUR  FROM utilisateur WHERE EMAIL=$val or PHONE=$val";
		 
         $email=isset($_GET["EMAIL"])?$_GET["EMAIL"]:NULL;
         $phone=isset($_GET["PHONE"])?$_GET["PHONE"]:NULL;
		 $nom=isset($_GET["NOM"])?$_GET["NOM"]:NULL;
         $prenom=isset($_GET["PRENOM"])?$_GET["PRENOM"]:NULL;
		 $idParent=isset($_GET["ID_PARENT_UTILISATEUR"])?$_GET["ID_PARENT_UTILISATEUR"]:NULL;

         $query="INSERT INTO `utilisateur` (`ID_UTILISATEUR`, `ID_TAGS`, `EMAIL`, `PHONE`, `NOM`, `PRENOM`, `ID_PARENT_UTILISATEUR`)
		         VALUES (NULL, '".$_GET["ID_TAGS"]."', '".$email."', '".$phone."', '".$nom."', '".$prenom."', '".$idParent."')"; 
          $req=$bdd->query($query, PDO::FETCH_ASSOC);
                 if($req)
                 {
                   print(true);   
                 }
                 else
                 {
                    print(0); 
                 }
            }
		}


		if(isset($_GET["userLogin"]))
		{
           $tr=(isset($_GET["LOGIN"]) and isset($_GET["PASSWORD"]));
      
         if( $tr)
         {
             	    $result = array();
           $val=$_GET["LOGIN"];
		   $pass=$_GET["PASSWORD"];

		   $pass = encrypt( $pass, $key, $key);
		  
		  // echo $pass;
           $query="SELECT `ID_UTILISATEUR`,`ID_TAGS`,`EMAIL`,`PHONE`,`NOM`,`PRENOM`,`ID_PARENT_UTILISATEUR`,`STATUS` FROM `utilisateur` WHERE (`EMAIL`='$val' or `PHONE`='$val') AND password='$pass'"; 
        	// var_dump($query);
			//echo $query;
		    $req=$bdd->query($query, PDO::FETCH_ASSOC);
    		$resultats=$req->fetch();
    	
    	    // foreach($resultats as $resultat )
    		// {
    		//     array_push($result,$resultat);
    		// }

          print(json_encode($resultats, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
         }
     
		}





















   if (isset($_GET["getGlobalInformationsPerMonth"])) 
	{
        $result=array();
        $result2=array();
	    $result3=array();
	    $query="SELECT MONTH as MONTHS ,`SOMME` as NBRES From TabGlobalInformationsInstitutionsByMONTH WHERE YEARS=2021";
        $query2="SELECT MONTH as MONTHS ,`SOMME` as NBRES From TabGlobalInformationsInstitutionsByMONTH WHERE YEARS=2022";
	    $query3="SELECT MONTH as MONTHS ,`SOMMES` as NBRES FROM `tabglobalinformationsinstitutionsbymonth2023` WHERE `MONTH`!=0;";

		$req=$bdd->query($query, PDO::FETCH_ASSOC);
		$req2=$bdd->query($query2, PDO::FETCH_ASSOC);
	    $req3=$bdd->query($query3, PDO::FETCH_ASSOC);
        $resultats=$req->fetchAll();
        $resultats2=$req2->fetchAll();
		$resultats3=$req3->fetchAll();
	   
		print(json_encode(array("2021"=>$resultats,"2022"=>$resultats2,"2023"=>$resultats3), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
		$req->closeCursor();

	}



	

	if(isset($_GET['getGlobalInformationsInstitutions']))
	{
			$res=array();
			if(isset($_GET["DISPATCH_DATE"]))
			{
				
				if($_GET["DISPATCH_DATE"]=='2023')
				{
					$MONTHS=array();
					$month='';
					
					if(isset($_GET["MONTH"]))
					{
					   $month=format_month($_GET["MONTH"]);
					   //echo $month;
					}
				   // echo $month;
					
					$query="SELECT DISTINCT b.SERVICE_NO FROM `final_referentiele` b WHERE  YEAR(SAVE_DATE)=2023;";
				  $AllContrat= implode_key(',',getData($query,[]),'SERVICE_NO');
				 
				  $queryLV="SELECT DISTINCT b.SERVICE_NO FROM `final_referentiele` b WHERE  b.`VOLTAGE`='LV' AND YEAR(SAVE_DATE)=2023;";
				  $ContratLV= implode_key(',',getData($queryLV,[]),'SERVICE_NO');
				 
				  $queryMV="SELECT DISTINCT b.SERVICE_NO FROM `final_referentiele` b WHERE  b.`VOLTAGE`='MV' AND YEAR(SAVE_DATE)=2023;";
				  $ContratMV= implode_key(',',getData($queryMV,[]),'SERVICE_NO');
				  
			   // echo $ContratMV; 
			 
				$All =json_decode(postApiInfos('https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php',array('contrat_bills_group' =>$AllContrat ,'annees' => '2023','mois' => $month)));
				$LV  =json_decode(postApiInfos('https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php',array('contrat_bills_group' =>$ContratLV ,'annees' => '2023','mois' => $month)));
				$MV  =json_decode(postApiInfos('https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php',array('contrat_bills_group' =>$ContratMV ,'annees' => '2023','mois' => $month)));
				   //   var_dump($All);
			  /*      print(json_encode(array(
					'contrat_bills_group' => $AllContrat,
					   'annees' => '2023',
					 'month' =>$month  
			
				 ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));*/
		   
			   print(json_encode(array(
							'SOMMES' => $All->total_conso??0,
							'SOMMESLV' =>$LV->total_conso??0,
							'SOMMESMV' =>$MV->total_conso??0,
							'NOMBRE' =>(int) count(getData($query,[])) ?? $All->nbr_contrat_facture??0,
							'NOMBRELV' =>(int) count(getData($queryLV,[])) ?? $LV->nbr_contrat_facture??0,
							'NOMBREML' =>(int) count(getData($queryMV,[])) ??$MV->nbr_contrat_facture??0,
							'PRICE' => (int)$All->total_facture??0,
							'PRICEMV' =>(int) $MV->total_facture??0,
							'ACTIF' => (int)$All->contrats_actif??0,
							'PRICELV' =>(int) $LV->total_facture??0,
						), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
	
				}
				else
				{
				 if(isset($_GET["MONTH"]) && !empty($_GET["MONTH"]))
				{		   
					   $queryT="SELECT 
					   SUM(IFNULL(bc.CONSUMPTION,0)) as SOMME,
					   SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICE,
					   COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBRE 
					   From 
						   bill bc
					   WHERE 
						   YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
							AND MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y')) IN(".$_GET["MONTH"].")";
						   $reqT=$bdd->query($queryT, PDO::FETCH_ASSOC);
							$resultaT=$reqT->fetch();
					   $queryMV="SELECT 
					   SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMEMV,
					   SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICEMV,
					   COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBREMV 
					   From 
						   bill bc
					   WHERE 
						   YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
						   AND bc.VOLT_TP_ID='MV' AND MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y')) IN(".$_GET["MONTH"].")";
						   $reqMV=$bdd->query($queryMV, PDO::FETCH_ASSOC);
							$resultaMV=$reqMV->fetch();
	
						   $queryLV="SELECT 
						   SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMELV,
						  SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICELV,
						  COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBRELV
							From 
								bill bc
							WHERE 
						   YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
							 AND bc.VOLT_TP_ID='LV' AND MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y')) IN(".$_GET["MONTH"].")";
							   $reqLV=$bdd->query($queryLV, PDO::FETCH_ASSOC);
								$resultaLV=$reqLV->fetch();
	
							   $queryACTIF="SELECT COUNT(DISTINCT IFNULL(b.`SERVICE_NO`,0)) as ACTIF FROM bill b WHERE b.SERVICESTATUS='ACTIVE' and YEAR(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."  AND MONTH(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y')) IN(".$_GET["MONTH"].")";
							   $reqACTIF=$bdd->query($queryACTIF, PDO::FETCH_ASSOC);
								$resultaACTIF=$reqACTIF->fetch();
	
	
									 print(json_encode(array(
										'SOMMES' => $resultaT["SOMME"]??0,
										'SOMMESLV' =>  $resultaLV["SOMMELV"]??0,
										'SOMMESMV' =>  $resultaMV["SOMMEMV"]??0,
										'NOMBRE' =>$resultaT["NOMBRE"]??0,
										'NOMBRELV' =>  $resultaLV["NOMBRELV"]??0,
										'NOMBREML' => $resultaMV["NOMBREMV"]??0,
										'PRICE' => $resultaT["PRICE"]??0,
										'PRICEMV' => $resultaMV["PRICEMV"]??0,
										'ACTIF' =>$resultaACTIF["ACTIF"]??0,
										'PRICELV' => $resultaLV["PRICELV"]??0
									), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
				
				
				}
				else{
					$query="SELECT * FROM GlobalInformationsInstitutions WHERE YEARS=".$_GET['DISPATCH_DATE'];
				  var_dump($query);
					   $req=$bdd->query($query);
					   $resultat=$req->fetch();
					   print(json_encode(array(
							   'SOMMES' => $resultat["SOMME"]??0,
							   'SOMMESLV' => $resultat["SOMMELV"]??0,
							   'SOMMESMV' => $resultat["SOMMEMV"]??0,
							   'NOMBRE' => $resultat["NOMBRE"]??0,
							   'NOMBRELV' => $resultat["NOMBRELV"]??0,
							   'NOMBREML' => $resultat["NOMBREMV"]??0,
							   'PRICE' => $resultat["PRICE"]??0,
							   'PRICEMV' => $resultat["PRICEMV"]??0,
							   'ACTIF' => $resultat["ACTIF"]??0,
							   'PRICELV' => $resultat["PRICELV"]??0
						   ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
					   $req->closeCursor();
			   
				
				}   
				}
	
				
			}
	}








	if (isset($_GET["getREviewRemoveContractInMonth"]))
	{
		$result = array();
		if(isset($_GET["DISPATCH_DATE"]) && !empty($_GET["DISPATCH_DATE"]) )
		{
			$date=$_GET["DISPATCH_DATE"];
				if (isset($_GET["MONTH"]) && !empty($_GET["MONTH"]))
				{
						$MONTHS=explode(",", $_GET["MONTH"]);
						foreach($MONTHS  as $key => $month)
						{
							if ($key==0)
							{
								 
								   if($date==2021 && $month==1)  $query='SELECT "" AS `TUTELLE`,"" AS `SERVICE_NO`,"" AS `NAMES`,"" AS `AGENCE`,'.$month.' AS `MONTHS`,'.$date.' as `YEAR`';	
                                   else 
                                   {
                                    if($month==12)
                                     {
                                        $query="SELECT `TUTELLE`, `SERVICE_NO`, `NAMES`, `AGENCE`, $month AS `MONTHS`, $date AS `YEAR` FROM final_referentiele WHERE MONTH(SAVE_DATE)=$month and YEAR(SAVE_DATE)=$date AND SERVICE_NO NOT IN (SELECT `SERVICE_NO` FROM final_referentiele WHERE MONTH(SAVE_DATE)=1 and YEAR(SAVE_DATE)=".($date+1).")";
                                  }
                                    else{
                                        $query="SELECT `TUTELLE`, `SERVICE_NO`, `NAMES`, `AGENCE`, $month AS `MONTHS`, $date AS `YEAR` FROM final_referentiele WHERE MONTH(SAVE_DATE)=$month and YEAR(SAVE_DATE)=$date AND SERVICE_NO NOT IN (SELECT `SERVICE_NO` FROM final_referentiele WHERE MONTH(SAVE_DATE)=".($month+1)." and YEAR(SAVE_DATE)=$date)";
                                    }
                                    
                                   }
                                   	
							}
							else
							{	
								
					
                                if($date==2021 && $month==1)  $query.=' UNION SELECT "" AS `TUTELLE`,"" AS `SERVICE_NO`,"" AS `NAMES`,"" AS `AGENCE`,'.$month.' AS `MONTHS`,'.$date.' as `YEAR`';	
                                else 
                                {
                                    if($month==12)
                                    {
                                        $query.=" UNION SELECT `TUTELLE`, `SERVICE_NO`, `NAMES`, `AGENCE`, $month AS `MONTHS`, $date AS `YEAR` FROM final_referentiele WHERE MONTH(SAVE_DATE)=$month and YEAR(SAVE_DATE)=$date AND SERVICE_NO NOT IN (SELECT `SERVICE_NO` FROM final_referentiele WHERE MONTH(SAVE_DATE)=1 and YEAR(SAVE_DATE)=".($date+1).")";
                                    }
                                    else
                                    {
                                        $query.=" UNION SELECT `TUTELLE`, `SERVICE_NO`, `NAMES`, `AGENCE`, $month AS `MONTHS`, $date AS `YEAR` FROM final_referentiele WHERE MONTH(SAVE_DATE)=$month and YEAR(SAVE_DATE)=$date AND SERVICE_NO NOT IN (SELECT `SERVICE_NO` FROM final_referentiele WHERE MONTH(SAVE_DATE)=".($month+1)." and YEAR(SAVE_DATE)=$date)";
                                    }
                                 
                                }
									
							}
						}
					
							$req=$bdd->query($query, PDO::FETCH_ASSOC);
							$resultats=$req->fetchAll();
							foreach($resultats as $resultat )
							{
			
								array_push($result,(object)$resultat);
							}
						
					print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
							$req->closeCursor();
			  }	
		   	
		}
	}



	if (isset($_GET["getSumContractByTutelleAllMonth"]))
	{

       $result = array();
       if(isset($_GET["DISPATCH_DATE"]) && !empty($_GET["DISPATCH_DATE"]) )
       {
			$date=$_GET["DISPATCH_DATE"];
			$query="SELECT 
    r.TUTELLE,
    IF(SUM(MONTH(SAVE_DATE) = 1) = 0, '-', SUM(MONTH(SAVE_DATE) = 1)) AS JAN,
    IF(SUM(MONTH(SAVE_DATE) = 2) = 0, '-', SUM(MONTH(SAVE_DATE) = 2)) AS FEV,
    IF(SUM(MONTH(SAVE_DATE) = 3) = 0, '-', SUM(MONTH(SAVE_DATE) = 3)) AS MAR,
    IF(SUM(MONTH(SAVE_DATE) = 4) = 0, '-', SUM(MONTH(SAVE_DATE) = 4)) AS AVR,
    IF(SUM(MONTH(SAVE_DATE) = 5) = 0, '-', SUM(MONTH(SAVE_DATE) = 5)) AS MAI,
    IF(SUM(MONTH(SAVE_DATE) = 6) = 0, '-', SUM(MONTH(SAVE_DATE) = 6)) AS JUIN,
    IF(SUM(MONTH(SAVE_DATE) = 7) = 0, '-', SUM(MONTH(SAVE_DATE) = 7)) AS JUIL,
    IF(SUM(MONTH(SAVE_DATE) = 8) = 0, '-', SUM(MONTH(SAVE_DATE) = 8)) AS AOUT,
    IF(SUM(MONTH(SAVE_DATE) = 9) = 0, '-', SUM(MONTH(SAVE_DATE) = 9)) AS SEP,
    IF(SUM(MONTH(SAVE_DATE) = 10) = 0, '-', SUM(MONTH(SAVE_DATE) = 10)) AS OCT,
    IF(SUM(MONTH(SAVE_DATE) = 11) = 0, '-', SUM(MONTH(SAVE_DATE) = 11)) AS NOV,
    IF(SUM(MONTH(SAVE_DATE) = 12) = 0, '-', SUM(MONTH(SAVE_DATE) = 12)) AS DECE
FROM 
    final_referentiele r
WHERE 
    YEAR(SAVE_DATE) = $date
GROUP BY 
    r.TUTELLE DESC
		";	
			$req=$bdd->query($query, PDO::FETCH_ASSOC);
			$resultats=$req->fetchAll();
			foreach($resultats as $resultat )
			{
				array_push($result,(object)$resultat);
			}


			print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
			$req->closeCursor();
		}	
	}












if (isset($_GET["getCountContractInMonth"]))
{
    $result_all = array();
	if(isset($_GET["DISPATCH_DATE"]) && !empty($_GET["DISPATCH_DATE"]) )
	{
		$date=$_GET["DISPATCH_DATE"];
		if(isset($_GET["ID_TAGS"]) and !empty($_GET["ID_TAGS"]) and $_GET["ID_TAGS"]!=="GLOBAL")
		{
		  if (isset($_GET["MONTH"]) && !empty($_GET["MONTH"]))
			{
					$MONTHS=explode(",", $_GET["MONTH"]);
					foreach($MONTHS  as $key => $month)
					{
						if ($key==0)
						{
									
								if($date==2021 && $month==1) $query_all="SELECT $month as MONTHS,0 as NBRES";
							else $query_all="SELECT $month as MONTHS,  count(*) as NBRES  FROM `final_referentiele` WHERE `TUTELLE`='".$_GET["ID_TAGS"]."' AND MONTH(SAVE_DATE)=$month AND YEAR(SAVE_DATE)=$date";
						}
						else
						{
							
							
							if($date==2021 && $month==1) $query_all.="SELECT $month as MONTHS,0 as NBRES";
							else $query_all.=" UNION SELECT $month as MONTHS,  count(*) as NBRES  FROM `final_referentiele` WHERE `TUTELLE`='".$_GET["ID_TAGS"]."' AND MONTH(SAVE_DATE)=$month AND YEAR(SAVE_DATE)=$date";
						}
					}
				//	echo $query;
						$req_all=$bdd->query($query_all, PDO::FETCH_ASSOC);
						$resultats_all=$req_all->fetchAll();
						
						foreach($resultats_all as $resultat_all )
						{
							array_push($result_all,(object)$resultat_all);
						}
			}	
		}
		else
		{
			if (isset($_GET["MONTH"]) && !empty($_GET["MONTH"]))
			{
					$MONTHS=explode(",", $_GET["MONTH"]);
					foreach($MONTHS  as $key => $month)
					{
						if ($key==0)
						{
							 
							   if($date==2021 && $month==1) $query_all="SELECT $month as MONTHS,0 as NBRES";	
							else  $query_all="SELECT $month as MONTHS,  count(*) as NBRES  FROM final_referentiele WHERE MONTH(SAVE_DATE)=$month AND YEAR(SAVE_DATE)=$date";
						}
						else
						{	
			
							  if($date==2021 && $month==1) $query_all.="SELECT $month as MONTHS,0 as NBRES";
							else 				$query_all.=" UNION SELECT $month as MONTHS,  count(*) as NBRES FROM final_referentiele WHERE MONTH(SAVE_DATE)=$month AND YEAR(SAVE_DATE)=$date";
						}
					}
					 // echo $query;
						$req_all=$bdd->query($query_all, PDO::FETCH_ASSOC);
						$resultats_all=$req_all->fetchAll();
						foreach($resultats_all as $resultat_all )
						{
		
							array_push($result_all,(object)$resultat_all);
						}
		  }	
	   }	
	}

















	$result = array();
	if(isset($_GET["DISPATCH_DATE"]) && !empty($_GET["DISPATCH_DATE"]) )
	{
		$date=$_GET["DISPATCH_DATE"];
		if(isset($_GET["ID_TAGS"]) and !empty($_GET["ID_TAGS"]) and $_GET["ID_TAGS"]!=="GLOBAL")
		{
		  if (isset($_GET["MONTH"]) && !empty($_GET["MONTH"]))
			{
					$MONTHS=explode(",", $_GET["MONTH"]);
					foreach($MONTHS  as $key => $month)
					{
						if ($key==0)
						{
									
								if($date==2021 && $month==1) $query="SELECT $month as MONTHS,0 as NBRES";
							else $query="SELECT $month as MONTHS,  count(*) as NBRES  FROM `final_referentiel_retirer` WHERE `TUTELLE`='".$_GET["ID_TAGS"]."' AND `MONTHS`=$month AND `YEAR`=$date";
						}
						else
						{
							
							
							if($date==2021 && $month==1) $query.="SELECT $month as MONTHS,0 as NBRES";
							else $query.=" UNION SELECT $month as MONTHS,  count(*) as NBRES  FROM `final_referentiel_retirer` WHERE `TUTELLE`='".$_GET["ID_TAGS"]."' AND `MONTHS`=$month AND `YEAR`=$date";
						}
					}
				//	echo $query;
						$req=$bdd->query($query, PDO::FETCH_ASSOC);
						$resultats=$req->fetchAll();
						$i=0;
						foreach($resultats as $resultat )
						{
						 if($result_all[$i]->NBRES==0)
						   array_push($result,(object)array("MONTHS"=>$resultat["MONTHS"],"NBRES"=>0));
							 else
							array_push($result,(object)$resultat);
							$i=$i+1;
						}
			}	
		}
		else
		{
			if (isset($_GET["MONTH"]) && !empty($_GET["MONTH"]))
			{
					$MONTHS=explode(",", $_GET["MONTH"]);
					foreach($MONTHS  as $key => $month)
					{
						if ($key==0)
						{
							 
							   if($date==2021 && $month==1) $query="SELECT $month as MONTHS,0 as NBRES";	
							else  $query="SELECT $month as MONTHS,  count(*) as NBRES  FROM final_referentiel_retirer WHERE MONTHS=$month AND YEAR=$date";
						}
						else
						{	
			
							  if($date==2021 && $month==1) $query.="SELECT $month as MONTHS,0 as NBRES";
							else 				$query.=" UNION SELECT $month as MONTHS,  count(*) as NBRES FROM final_referentiel_retirer WHERE MONTHS=$month AND YEAR=$date";
						}
					}
					 // echo $query;
						$req=$bdd->query($query, PDO::FETCH_ASSOC);
						$resultats=$req->fetchAll();
				        $i=0;
						foreach($resultats as $resultat )
						{
		                   if($result_all[$i]->NBRES==0)
						   array_push($result,(object)array("MONTHS"=>$resultat["MONTHS"],"NBRES"=>0));
							 else
							array_push($result,(object)$resultat);
							$i=$i+1;
						}
		  }	
	   }	
	}










		$result1 = array();
		if(isset($_GET["DISPATCH_DATE"]) && !empty($_GET["DISPATCH_DATE"]) )
		{
			$date=$_GET["DISPATCH_DATE"];
			if(isset($_GET["ID_TAGS"]) and !empty($_GET["ID_TAGS"]) and $_GET["ID_TAGS"]!=="GLOBAL")
			{
			  if (isset($_GET["MONTH"]) && !empty($_GET["MONTH"]))
				{
						$MONTHS=explode(",", $_GET["MONTH"]);
						foreach($MONTHS  as $key => $month)
						{
							if ($key==0)
							{
										
									if($date==2021 && $month==1) $query="SELECT $month as MONTHS,0 as NBRES";
								else $query="SELECT $month as MONTHS,  count(*) as NBRES  FROM `final_referentiel_ajouter` WHERE `TUTELLE`='".$_GET["ID_TAGS"]."' AND `MONTHS`=$month AND `YEAR`=$date";
							}
							else
							{
								
								
								if($date==2021 && $month==1) $query.="SELECT $month as MONTHS,0 as NBRES";
								else $query.=" UNION SELECT $month as MONTHS,  count(*) as NBRES  FROM `final_referentiel_ajouter` WHERE `TUTELLE`='".$_GET["ID_TAGS"]."' AND `MONTHS`=$month AND `YEAR`=$date";
							}
						}
					//	echo $query;
							$req=$bdd->query($query, PDO::FETCH_ASSOC);
							$result1ats=$req->fetchAll();
							$i=0;
							foreach($result1ats as $result1at )
							{
								
							array_push($result1,(object)$result1at);
								
								
							}
				}	
			}
			else
			{
				if (isset($_GET["MONTH"]) && !empty($_GET["MONTH"]))
				{
						$MONTHS=explode(",", $_GET["MONTH"]);
						foreach($MONTHS  as $key => $month)
						{
							if ($key==0)
							{
								 
								   if($date==2021 && $month==1) $query="SELECT $month as MONTHS,0 as NBRES";	
								else  $query="SELECT $month as MONTHS,  count(*) as NBRES  FROM final_referentiel_ajouter WHERE MONTHS=$month AND YEAR=$date";
							}
							else
							{	
				
								  if($date==2021 && $month==1) $query.="SELECT $month as MONTHS,0 as NBRES";
								else 				$query.=" UNION SELECT $month as MONTHS,  count(*) as NBRES FROM final_referentiel_ajouter WHERE MONTHS=$month AND YEAR=$date";
							}
						}
					     // echo $query;
							$req=$bdd->query($query, PDO::FETCH_ASSOC);
							$result1ats=$req->fetchAll();
					        $i=0;
							foreach($result1ats as $result1at )
							{
			
								
							array_push($result1,(object)$result1at);
								
								
							}
						   
							print(json_encode(array("total_facturer"=>$result_all,"total_ajouter"=>$result1,"total_retirer"=>$result), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
							$req->closeCursor();
							
			  }	
		   }	
		}
	}

















if (isset($_GET["getSumContractByMonth"]))
						{
	
	$result = array();		
	$result_remove = array();	
	if(isset($_GET["DISPATCH_DATE"]) && !empty($_GET["DISPATCH_DATE"]) )
		{
			$date=$_GET["DISPATCH_DATE"];
			//$query="SELECT count(*) as NBRES, `MONTHS`FROM `final_referentiele` WHERE `YEAR`=$date GROUP BY `MONTHS`;";	
			$query="SELECT count(*) as NBRES,MONTH(`SAVE_DATE`) as `MONTHS`FROM `final_referentiele` WHERE YEAR(`SAVE_DATE`)=$date GROUP BY `MONTHS`;";	
			
			                $req=$bdd->query($query, PDO::FETCH_ASSOC);
							$resultats=$req->fetchAll();
							foreach($resultats as $resultat )
							{
								array_push($result,(object)$resultat);
							}

                 /*  $query_remove="SELECT count(*) as NBRES, `MONTHS`FROM `final_referentiel_retirer` WHERE `YEAR`=$date GROUP BY `MONTHS`;";	 
							$req_remove=$bdd->query($query_remove, PDO::FETCH_ASSOC);
							$resultats_remove=$req_remove->fetchAll();
							foreach($resultats_remove as $resultat_remove )
							{
								array_push($result_remove,(object)$resultat_remove);
							}array("sum_ajouter"=>$result,"sum_retirer"=>$result_remove)
							*/
					print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
							$req->closeCursor();
	    }
}




  
if (isset($_GET["getSumNewContractByTutelle"]))
{

        $result = array();	
        $result_remove = array();	
        $result_all = array();	
        if(isset($_GET["DISPATCH_DATE"]) && !empty($_GET["DISPATCH_DATE"]) )
        {
            $date=$_GET["DISPATCH_DATE"];
            if($date<2023)
            {
                if(isset($_GET["MONTH"]) && !empty($_GET["MONTH"]) )
                { 
    
                    
                    $month=$_GET["MONTH"];
                    $query_all="SELECT `TUTELLE` ,count( `SERVICE_NO`) as NBRES FROM `final_referentiele` WHERE YEAR(`SAVE_DATE`)=$date and MONTH(`SAVE_DATE`)=$month GROUP BY `TUTELLE` ORDER BY NBRES DESC;";
                    $query="SELECT `TUTELLE` ,count( `SERVICE_NO`) as NBRES FROM `final_referentiel_ajouter` WHERE `YEAR`=$date and MONTHS=$month  GROUP BY `TUTELLE` ORDER BY NBRES DESC";	
                    $query_remove="SELECT `TUTELLE` ,count( `SERVICE_NO`) as NBRES FROM `final_referentiel_retirer` WHERE `YEAR`=$date and MONTHS=$month  GROUP BY `TUTELLE` ORDER BY NBRES DESC";
                    
                
                            $req=$bdd->query($query, PDO::FETCH_ASSOC);
                            $resultats=$req->fetchAll();
                            foreach($resultats as $resultat )
                            {
                                array_push($result,(object)$resultat);
                            }
                        
                            $req_all=$bdd->query($query_all, PDO::FETCH_ASSOC);
                            $resultats_all=$req_all->fetchAll();
                            foreach($resultats_all as $resultat_all )
                            {
    
                                array_push($result_all,(object)$resultat_all);
                            }
    
                            $req_remove=$bdd->query($query_remove, PDO::FETCH_ASSOC);
                            $resultats_remove=$req_remove->fetchAll();
                            foreach($resultats_remove as $resultat_remove )
                            {
                                array_push($result_remove,(object)$resultat_remove);
                            }
                            print(json_encode(array("total_facturer"=>$result_all,"total_ajouter"=>$result,"total_retirer"=>$result_remove), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
                            $req->closeCursor();
                            $req_all->closeCursor();
                            $req_remove->closeCursor();
                 }
    
                 else
                 {
                   
                  
                    $query_all="SELECT `TUTELLE` ,count( `SERVICE_NO`) as NBRES FROM `final_referentiele` WHERE YEAR(`SAVE_DATE`)=$date  GROUP BY `TUTELLE` ORDER BY NBRES DESC;";
                    $query="SELECT `TUTELLE` ,count( `SERVICE_NO`) as NBRES FROM `final_referentiel_ajouter` WHERE `YEAR`=$date  GROUP BY `TUTELLE` ORDER BY NBRES DESC";	
                    $query_remove="SELECT `TUTELLE` ,count( `SERVICE_NO`) as NBRES FROM `final_referentiel_retirer` WHERE `YEAR`=$date  GROUP BY `TUTELLE` ORDER BY NBRES DESC";
                    
                
                            $req=$bdd->query($query, PDO::FETCH_ASSOC);
                            $resultats=$req->fetchAll();
                            foreach($resultats as $resultat )
                            {
                                array_push($result,(object)$resultat);
                            }
                        
                            $req_all=$bdd->query($query_all, PDO::FETCH_ASSOC);
                            $resultats_all=$req_all->fetchAll();
                            foreach($resultats_all as $resultat_all )
                            {
    
                                array_push($result_all,(object)$resultat_all);
                            }
    
                            $req_remove=$bdd->query($query_remove, PDO::FETCH_ASSOC);
                            $resultats_remove=$req_remove->fetchAll();
                            foreach($resultats_remove as $resultat_remove )
                            {
                                array_push($result_remove,(object)$resultat_remove);
                            }
                            print(json_encode(array("total_facturer"=>$result_all,"total_ajouter"=>$result,"total_retirer"=>$result_remove), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
                            $req->closeCursor();
                            $req_all->closeCursor();
                            $req_remove->closeCursor();
                 }


            }
            else
            {


                if(isset($_GET["MONTH"]) && !empty($_GET["MONTH"]) )
                { 
    
                    
                    $month=$_GET["MONTH"];
                    $query_all="SELECT `TUTELLE` ,count(DISTINCT `SERVICE_NO`) as NBRES FROM `final_referentiele` WHERE YEAR(`SAVE_DATE`)=$date and MONTH(`SAVE_DATE`)=$month GROUP BY `TUTELLE` ORDER BY NBRES DESC;";
                    $query="SELECT `TUTELLE` ,count(DISTINCT `SERVICE_NO`) as NBRES FROM `final_referentiel_ajouter` WHERE `YEAR`=$date and MONTHS=$month  GROUP BY `TUTELLE` ORDER BY NBRES DESC";	
                    $query_remove="SELECT `TUTELLE` ,count(DISTINCT `SERVICE_NO`) as NBRES FROM `final_referentiel_retirer` WHERE `YEAR`=$date and MONTHS=$month  GROUP BY `TUTELLE` ORDER BY NBRES DESC";
                    
                
                            $req=$bdd->query($query, PDO::FETCH_ASSOC);
                            $resultats=$req->fetchAll();
                            foreach($resultats as $resultat )
                            {
                                array_push($result,(object)$resultat);
                            }
                        
                            $req_all=$bdd->query($query_all, PDO::FETCH_ASSOC);
                            $resultats_all=$req_all->fetchAll();
                            foreach($resultats_all as $resultat_all )
                            {
    
                                array_push($result_all,(object)$resultat_all);
                            }
    
                            $req_remove=$bdd->query($query_remove, PDO::FETCH_ASSOC);
                            $resultats_remove=$req_remove->fetchAll();
                            foreach($resultats_remove as $resultat_remove )
                            {
                                array_push($result_remove,(object)$resultat_remove);
                            }
                            print(json_encode(array("total_facturer"=>$result_all,"total_ajouter"=>$result,"total_retirer"=>$result_remove), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
                            $req->closeCursor();
                            $req_all->closeCursor();
                            $req_remove->closeCursor();
                 }
    
                 else
                 {
                   
                  
                    $query_all="SELECT `TUTELLE` ,count(DISTINCT `SERVICE_NO`) as NBRES FROM `final_referentiele` WHERE YEAR(`SAVE_DATE`)=$date  GROUP BY `TUTELLE` ORDER BY NBRES DESC;";
                    $query="SELECT `TUTELLE` ,count(DISTINCT `SERVICE_NO`) as NBRES FROM `final_referentiel_ajouter` WHERE `YEAR`=$date  GROUP BY `TUTELLE` ORDER BY NBRES DESC";	
                    $query_remove="SELECT `TUTELLE` ,count(DISTINCT `SERVICE_NO`) as NBRES FROM `final_referentiel_retirer` WHERE `YEAR`=$date  GROUP BY `TUTELLE` ORDER BY NBRES DESC";
                    
                
                            $req=$bdd->query($query, PDO::FETCH_ASSOC);
                            $resultats=$req->fetchAll();
                            foreach($resultats as $resultat )
                            {
                                array_push($result,(object)$resultat);
                            }
                        
                            $req_all=$bdd->query($query_all, PDO::FETCH_ASSOC);
                            $resultats_all=$req_all->fetchAll();
                            foreach($resultats_all as $resultat_all )
                            {
    
                                array_push($result_all,(object)$resultat_all);
                            }
    
                            $req_remove=$bdd->query($query_remove, PDO::FETCH_ASSOC);
                            $resultats_remove=$req_remove->fetchAll();
                            foreach($resultats_remove as $resultat_remove )
                            {
                                array_push($result_remove,(object)$resultat_remove);
                            }
                            print(json_encode(array("total_facturer"=>$result_all,"total_ajouter"=>$result,"total_retirer"=>$result_remove), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
                            $req->closeCursor();
                            $req_all->closeCursor();
                            $req_remove->closeCursor();
                 }
            }
           
                

              
               
    }
}






if (isset($_GET["getCountNewContractInMonth"]))
	{
		$result = array();
		if(isset($_GET["DISPATCH_DATE"]) && !empty($_GET["DISPATCH_DATE"]) )
		{
			$date=$_GET["DISPATCH_DATE"];
			if(isset($_GET["ID_TAGS"]) and !empty($_GET["ID_TAGS"]) and $_GET["ID_TAGS"]!=="GLOBAL")
			{
			  if (isset($_GET["MONTH"]) && !empty($_GET["MONTH"]))
				{
						$MONTHS=explode(",", $_GET["MONTH"]);
						foreach($MONTHS  as $key => $month)
						{
							if ($key==0)
							{
										
									if($date==2021 && $month==1) $query="SELECT $month as MONTHS,0 as NBRES";
								else $query="SELECT $month as MONTHS,  count(*) as NBRES  FROM `final_referentiel_ajouter` WHERE `TUTELLE`='".$_GET["ID_TAGS"]."' AND `MONTHS`=$month AND `YEAR`=$date";
							}
							else
							{
								
								
								if($date==2021 && $month==1) $query.="SELECT $month as MONTHS,0 as NBRES";
								else $query.=" UNION SELECT $month as MONTHS,  count(*) as NBRES  FROM `final_referentiel_ajouter` WHERE `TUTELLE`='".$_GET["ID_TAGS"]."' AND `MONTHS`=$month AND `YEAR`=$date";
							}
						}
					//	echo $query;
							$req=$bdd->query($query, PDO::FETCH_ASSOC);
							$resultats=$req->fetchAll();
						
							foreach($resultats as $resultat )
							{
								array_push($result,(object)$resultat);
							}
						
						print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
							$req->closeCursor();
				}	
			}
			else
			{
				if (isset($_GET["MONTH"]) && !empty($_GET["MONTH"]))
				{
						$MONTHS=explode(",", $_GET["MONTH"]);
						foreach($MONTHS  as $key => $month)
						{
							if ($key==0)
							{
								 
								   if($date==2021 && $month==1) $query="SELECT $month as MONTHS,0 as NBRES";	
								else  $query="SELECT $month as MONTHS,  count(*) as NBRES  FROM final_referentiel_ajouter WHERE MONTHS=$month AND YEAR=$date";
							}
							else
							{	
				
								  if($date==2021 && $month==1) $query.="SELECT $month as MONTHS,0 as NBRES";
								else 				$query.=" UNION SELECT $month as MONTHS,  count(*) as NBRES FROM final_referentiel_ajouter WHERE MONTHS=$month AND YEAR=$date";
							}
						}
					     // echo $query;
							$req=$bdd->query($query, PDO::FETCH_ASSOC);
							$resultats=$req->fetchAll();
							foreach($resultats as $resultat )
							{
			
								array_push($result,(object)$resultat);
							}
						
					print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
							$req->closeCursor();
			  }	
		   }	
		}
	}







	if (isset($_GET["getNewContractInMonth"]))
	{
		$result = array();
		if(isset($_GET["DISPATCH_DATE"]) && !empty($_GET["DISPATCH_DATE"]) )
		{
			$date=$_GET["DISPATCH_DATE"];
			if(isset($_GET["ID_TAGS"]) and !empty($_GET["ID_TAGS"]) and $_GET["ID_TAGS"]!=="GLOBAL")
			{
			  if (isset($_GET["MONTH"]) && !empty($_GET["MONTH"]))
				{
						$MONTHS=explode(",", $_GET["MONTH"]);
						foreach($MONTHS  as $key => $month)
						{
							if ($key==0)
							{
									
									if($date==2021 && $month==1) $query=' SELECT "" AS `TUTELLE`,"" AS `SERVICE_NO`,"" AS `NAMES`,"" AS `AGENCE`,'.$month.' AS `MONTHS`,'.$date.' as `YEAR`';	
								else $query="SELECT * FROM `final_referentiel_ajouter` WHERE `TUTELLE`='".$_GET["ID_TAGS"]."' AND `MONTHS`=$month AND `YEAR`=$date";	
							}
							else
							{
								
								
								if($date==2021 && $month==1)  $query.=' UNION SELECT "" AS `TUTELLE`,"" AS `SERVICE_NO`,"" AS `NAMES`,"" AS `AGENCE`,'.$month.' AS `MONTHS`,'.$date.' as `YEAR`';	
								else $query.=" UNION SELECT * FROM `final_referentiel_ajouter` WHERE `TUTELLE`='".$_GET["ID_TAGS"]."' AND `MONTHS`=$month AND `YEAR`=$date";
							}
						}
						
							$req=$bdd->query($query, PDO::FETCH_ASSOC);
							$resultats=$req->fetchAll();
							
							foreach($resultats as $resultat )
							{
								array_push($result,(object)$resultat);
							}
						
						print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
							$req->closeCursor();
				}	
			}
			else
			{
				if (isset($_GET["MONTH"]) && !empty($_GET["MONTH"]))
				{
						$MONTHS=explode(",", $_GET["MONTH"]);
						foreach($MONTHS  as $key => $month)
						{
							if ($key==0)
							{
								  $query="SELECT * FROM final_referentiel_ajouter WHERE MONTHS=$month AND YEAR=$date";
								   if($date==2021 && $month==1)  $query='SELECT "" AS `TUTELLE`,"" AS `SERVICE_NO`,"" AS `NAMES`,"" AS `AGENCE`,'.$month.' AS `MONTHS`,'.$date.' as `YEAR`';	
							}
							else
							{	
								
								  if($date==2021 && $month==1) $query.='UNION SELECT "" AS `TUTELLE`,"" AS `SERVICE_NO`,"" AS `NAMES`,"" AS `AGENCE`,'.$month.' AS `MONTHS`,'.$date.' as `YEAR`';	
								else $query.=" UNION SELECT * FROM final_referentiel_ajouter WHERE MONTHS=$month AND YEAR=$date";
									
							}
						}
					
							$req=$bdd->query($query, PDO::FETCH_ASSOC);
							$resultats=$req->fetchAll();
							foreach($resultats as $resultat )
							{
			
								array_push($result,(object)$resultat);
							}
						
					print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
							$req->closeCursor();
			  }	
		   }	
		}
	}






if (isset($_GET["getRemoveContractInMonth"]))
{
	$result = array();
	if(isset($_GET["DISPATCH_DATE"]) && !empty($_GET["DISPATCH_DATE"]) )
	{
		$date=$_GET["DISPATCH_DATE"];
		if(isset($_GET["ID_TAGS"]) and !empty($_GET["ID_TAGS"]) and $_GET["ID_TAGS"]!=="GLOBAL")
		{
		  if (isset($_GET["MONTH"]) && !empty($_GET["MONTH"]))
			{
					$MONTHS=explode(",", $_GET["MONTH"]);
					foreach($MONTHS  as $key => $month)
					{
						if ($key==0)
						{
								
								if($date==2021 && $month==1) $query=' SELECT "" AS `TUTELLE`,"" AS `SERVICE_NO`,"" AS `NAMES`,"" AS `AGENCE`,'.$month.' AS `MONTHS`,'.$date.' as `YEAR`';	
							else $query="SELECT * FROM `final_referentiel_retirer` WHERE `TUTELLE`='".$_GET["ID_TAGS"]."' AND `MONTHS`=$month AND `YEAR`=$date";	
						}
						else
						{
							
							
							if($date==2021 && $month==1)  $query.=' UNION SELECT "" AS `TUTELLE`,"" AS `SERVICE_NO`,"" AS `NAMES`,"" AS `AGENCE`,'.$month.' AS `MONTHS`,'.$date.' as `YEAR`';	
							else $query.=" UNION SELECT * FROM `final_referentiel_retirer` WHERE `TUTELLE`='".$_GET["ID_TAGS"]."' AND `MONTHS`=$month AND `YEAR`=$date";
						}
					}
					
						$req=$bdd->query($query, PDO::FETCH_ASSOC);
						$resultats=$req->fetchAll();
						
						foreach($resultats as $resultat )
						{
							array_push($result,(object)$resultat);
						}
					
					print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
						$req->closeCursor();
			}	
		}
		else
		{
			if (isset($_GET["MONTH"]) && !empty($_GET["MONTH"]))
			{
					$MONTHS=explode(",", $_GET["MONTH"]);
					foreach($MONTHS  as $key => $month)
					{
						if ($key==0)
						{
							  $query="SELECT * FROM final_referentiel_retirer WHERE MONTHS=$month AND YEAR=$date";
							   if($date==2021 && $month==1)  $query='SELECT "" AS `TUTELLE`,"" AS `SERVICE_NO`,"" AS `NAMES`,"" AS `AGENCE`,'.$month.' AS `MONTHS`,'.$date.' as `YEAR`';	
						}
						else
						{	
							
							  if($date==2021 && $month==1) $query.='UNION SELECT "" AS `TUTELLE`,"" AS `SERVICE_NO`,"" AS `NAMES`,"" AS `AGENCE`,'.$month.' AS `MONTHS`,'.$date.' as `YEAR`';	
							else $query.=" UNION SELECT * FROM final_referentiel_retirer WHERE MONTHS=$month AND YEAR=$date";
								
						}
					}
				
						$req=$bdd->query($query, PDO::FETCH_ASSOC);
						$resultats=$req->fetchAll();
						foreach($resultats as $resultat )
						{
		
							array_push($result,(object)$resultat);
						}
					
				print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
						$req->closeCursor();
		  }	
	   }	
	}
}












































if (isset($_GET["getCountRemoveContractInMonth"]))
	{
		$result = array();
		if(isset($_GET["DISPATCH_DATE"]) && !empty($_GET["DISPATCH_DATE"]) )
		{
			$date=$_GET["DISPATCH_DATE"];
			if(isset($_GET["ID_TAGS"]) and !empty($_GET["ID_TAGS"]) and $_GET["ID_TAGS"]!=="GLOBAL")
			{
			  if (isset($_GET["MONTH"]) && !empty($_GET["MONTH"]))
				{
						$MONTHS=explode(",", $_GET["MONTH"]);
						foreach($MONTHS  as $key => $month)
						{
							if ($key==0)
							{
										
									if($date==2021 && $month==1) $query="SELECT $month as MONTHS,0 as NBRES";
								else $query="SELECT $month as MONTHS,  count(*) as NBRES  FROM `final_referentiel_retirer` WHERE `TUTELLE`='".$_GET["ID_TAGS"]."' AND `MONTHS`=$month AND `YEAR`=$date";
							}
							else
							{
								
								
								if($date==2021 && $month==1) $query.="SELECT $month as MONTHS,0 as NBRES";
								else $query.=" UNION SELECT $month as MONTHS,  count(*) as NBRES  FROM `final_referentiel_retirer` WHERE `TUTELLE`='".$_GET["ID_TAGS"]."' AND `MONTHS`=$month AND `YEAR`=$date";
							}
						}
					//	echo $query;
							$req=$bdd->query($query, PDO::FETCH_ASSOC);
							$resultats=$req->fetchAll();
							
							foreach($resultats as $resultat )
							{
								array_push($result,(object)$resultat);
							}
						
						print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
							$req->closeCursor();
				}	
			}
			else
			{
				if (isset($_GET["MONTH"]) && !empty($_GET["MONTH"]))
				{
						$MONTHS=explode(",", $_GET["MONTH"]);
						foreach($MONTHS  as $key => $month)
						{
							if ($key==0)
							{
								 
								   if($date==2021 && $month==1) $query="SELECT $month as MONTHS,0 as NBRES";	
								else  $query="SELECT $month as MONTHS,  count(*) as NBRES  FROM final_referentiel_retirer WHERE MONTHS=$month AND YEAR=$date";
							}
							else
							{	
				
								  if($date==2021 && $month==1) $query.="SELECT $month as MONTHS,0 as NBRES";
								else 				$query.=" UNION SELECT $month as MONTHS,  count(*) as NBRES FROM final_referentiel_retirer WHERE MONTHS=$month AND YEAR=$date";
							}
						}
					     // echo $query;
							$req=$bdd->query($query, PDO::FETCH_ASSOC);
							$resultats=$req->fetchAll();
							foreach($resultats as $resultat )
							{
			
								array_push($result,(object)$resultat);
							}
						
					print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
							$req->closeCursor();
			  }	
		   }	
		}
	}



















	if (isset($_GET["getTotalImpayers"])) {
		$result = array();
	
		if ( isset($_GET["DISPATCH_DATE"])) 
		{ 
		  if(isset($_GET["ID_TAGS"]) and !empty($_GET["ID_TAGS"]) and $_GET["ID_TAGS"]!=="GLOBAL")
		  {
			if(isset($_GET["MONTH"]))
			{	
			 $req=$bdd->query("SELECT SUM(`bill_amount`) AS PRICE ,null AS SOMME FROM `unpaid_bills` b,contrat_possede_tags cpt WHERE b.`SERVICE_NO`=cpt.`SERVICE_NO`  and cpt.ID_TAGS='".$_GET["ID_TAGS"]."' AND YEAR(STR_TO_DATE(b.`billing_date`,'%Y-%m-%d'))=".$_GET["DISPATCH_DATE"]." AND MONTH(STR_TO_DATE(b.`billing_date`,'%Y-%m-%d')) IN (".$_GET["MONTH"].") ", PDO::FETCH_ASSOC);
			 $reqMV=$bdd->query("SELECT SUM(`bill_amount`) AS PRICE ,null AS SOMME FROM `unpaid_bills` b,contrat_possede_tags cpt WHERE b.`SERVICE_NO`=cpt.`SERVICE_NO`  and cpt.ID_TAGS='".$_GET["ID_TAGS"]."' AND  b.type='MV' AND YEAR(STR_TO_DATE(b.`billing_date`,'%Y-%m-%d'))=".$_GET["DISPATCH_DATE"]." AND MONTH(STR_TO_DATE(b.`billing_date`,'%Y-%m-%d')) IN (".$_GET["MONTH"].") ", PDO::FETCH_ASSOC);
			 $reqLV=$bdd->query("SELECT SUM(`bill_amount`) AS PRICE ,null AS SOMME FROM `unpaid_bills` b,contrat_possede_tags cpt WHERE b.`SERVICE_NO`=cpt.`SERVICE_NO`  and cpt.ID_TAGS='".$_GET["ID_TAGS"]."' AND  b.type='LV' AND YEAR(STR_TO_DATE(b.`billing_date`,'%Y-%m-%d'))=".$_GET["DISPATCH_DATE"]." AND MONTH(STR_TO_DATE(b.`billing_date`,'%Y-%m-%d')) IN (".$_GET["MONTH"].") ", PDO::FETCH_ASSOC);
			}
			else
			{
				$req=$bdd->query("SELECT SUM(`bill_amount`) AS PRICE ,null AS SOMME FROM `unpaid_bills` b,contrat_possede_tags cpt WHERE b.`SERVICE_NO`=cpt.`SERVICE_NO`  and cpt.ID_TAGS='".$_GET["ID_TAGS"]."' AND YEAR(STR_TO_DATE(b.`billing_date`,'%Y-%m-%d'))=".$_GET["DISPATCH_DATE"], PDO::FETCH_ASSOC);
				$reqMV=$bdd->query("SELECT SUM(`bill_amount`) AS PRICE ,null AS SOMME FROM `unpaid_bills` b,contrat_possede_tags cpt WHERE b.`SERVICE_NO`=cpt.`SERVICE_NO`  and cpt.ID_TAGS='".$_GET["ID_TAGS"]."' AND  b.type='MV' AND   YEAR(STR_TO_DATE(b.`billing_date`,'%Y-%m-%d'))=".$_GET["DISPATCH_DATE"], PDO::FETCH_ASSOC);
				$reqLV=$bdd->query("SELECT SUM(`bill_amount`) AS PRICE ,null AS SOMME FROM `unpaid_bills` b,contrat_possede_tags cpt WHERE b.`SERVICE_NO`=cpt.`SERVICE_NO`  and cpt.ID_TAGS='".$_GET["ID_TAGS"]."'   AND  b.type='LV' AND  YEAR(STR_TO_DATE(b.`billing_date`,'%Y-%m-%d'))=".$_GET["DISPATCH_DATE"], PDO::FETCH_ASSOC);
			 

			}
			  
		   print(json_encode(array(
					
				//	'SOMMES' =>$req->fetchAll()[0]['SOMME']??0,
				//	'SOMMESLV' =>$req->fetchAll()[0]['SOMME']??0,
				//	'SOMMESMV' =>$req->fetchAll()[0]['SOMME']??0,
					'PRICE' => $req->fetchAll()[0]['PRICE']??0,
					'PRICEMV' =>$reqMV->fetchAll()[0]['PRICE']??0,
					'PRICELV' => $reqLV->fetchAll()[0]['PRICE']??0,
				), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
			$req->closeCursor();
			$reqLV->closeCursor();
			$reqMV->closeCursor();
		  }
		  else
		  {
			if(isset($_GET["MONTH"]))
			{	
			 $req=$bdd->query("SELECT SUM(`bill_amount`) AS PRICE ,0 AS SOMME FROM `unpaid_bills` b WHERE  YEAR(STR_TO_DATE(b.`billing_date`,'%Y-%m-%d'))=".$_GET["DISPATCH_DATE"]." AND MONTH(STR_TO_DATE(b.`billing_date`,'%Y-%m-%d')) IN (".$_GET["MONTH"].") ", PDO::FETCH_ASSOC);
			 $reqMV=$bdd->query("SELECT SUM(`bill_amount`) AS PRICE ,0 AS SOMME FROM `unpaid_bills` b WHERE b.type='MV' AND YEAR(STR_TO_DATE(b.`billing_date`,'%Y-%m-%d'))=".$_GET["DISPATCH_DATE"]." AND MONTH(STR_TO_DATE(b.`billing_date`,'%Y-%m-%d')) IN (".$_GET["MONTH"].") ", PDO::FETCH_ASSOC);
			 $reqLV=$bdd->query("SELECT SUM(`bill_amount`) AS PRICE ,0 AS SOMME FROM `unpaid_bills` b WHERE b.type='LV' AND YEAR(STR_TO_DATE(b.`billing_date`,'%Y-%m-%d'))=".$_GET["DISPATCH_DATE"]." AND MONTH(STR_TO_DATE(b.`billing_date`,'%Y-%m-%d')) IN (".$_GET["MONTH"].") ", PDO::FETCH_ASSOC);
			}
			else
			{
			 $req=$bdd->query("SELECT SUM(`bill_amount`) AS PRICE ,0 AS SOMME FROM `unpaid_bills` b WHERE YEAR(STR_TO_DATE(b.`billing_date`,'%Y-%m-%d'))=".$_GET["DISPATCH_DATE"], PDO::FETCH_ASSOC);
			 $reqMV=$bdd->query("SELECT SUM(`bill_amount`) AS PRICE ,0 AS SOMME FROM `unpaid_bills` b WHERE b.type='MV' AND YEAR(STR_TO_DATE(b.`billing_date`,'%Y-%m-%d'))=".$_GET["DISPATCH_DATE"], PDO::FETCH_ASSOC);
			 $reqLV=$bdd->query("SELECT SUM(`bill_amount`) AS PRICE ,0 AS SOMME FROM `unpaid_bills` b WHERE b.type='LV' AND YEAR(STR_TO_DATE(b.`billing_date`,'%Y-%m-%d'))=".$_GET["DISPATCH_DATE"], PDO::FETCH_ASSOC);
			}
 
				print(json_encode(array(
					'PRICE' => $req->fetchAll()[0]['PRICE']??0,
					'PRICEMV' =>$reqMV->fetchAll()[0]['PRICE']??0,
					'PRICELV' => $reqLV->fetchAll()[0]['PRICE']??0,
				), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
			$req->closeCursor();
			$reqLV->closeCursor();
			$reqMV->closeCursor();
		   }
				
		}
		
	}
	

    	if(isset($_GET["UpdateUserStatus"]))
		{
		    
		    $tr=(isset($_GET["EMAIL"]) || isset($_GET["ID_UTILISATEUR"])) ;
      
         if( $tr)
         {
             	    $result = array();
         $val=$_GET["EMAIL"]??$_GET["ID_UTILISATEUR"];
         $query="UPDATE `utilisateur` SET `STATUS`= !`STATUS` WHERE `EMAIL`='".$val."' or `ID_UTILISATEUR`='".$val."'"; 
         $req=$bdd->query($query, PDO::FETCH_ASSOC);
			if($req)
			{
			  print(true);   
			}
			else
			{
			   print(0); 
			}
         }
		 
		}
	
			function getStructureContratByTag($ID_TAGS)
		{
		      
		    include("connect_bd.php");
		    $result = array();
		  
		    $query= "SELECT * FROM `contrat_possede_tags` ct WHERE ct.`ID_TAGS`='".$ID_TAGS."'";
		    $req=$bdd->query($query, PDO::FETCH_ASSOC);
    		$resultats=$req->fetchAll();
    	    
    	    foreach($resultats as $resultat )
    		{
   
    		    array_push($result,array('SERVICE_NO'=>$resultat["SERVICE_NO"]));
    
    		}
    			$req->closeCursor();

    		  //$jsonString = json_encode($result);
  
    		    return $result;
    
    	
		
		}
		
	
	
	
	
	
		if (isset($_GET["getListInformationsContratsByTag"])) 
		{
		  if (isset($_GET["ID_TAGS"]) && !empty($_GET["ID_TAGS"]))
			{

				if($_GET["ID_TAGS"]==='GLOBAL')
				{
			$result = array();
			$query="SELECT 
			b.SERVICE_NO,
			b.VOLT_TP_ID as TL ,
			'' AS STATUT, c.TUTELLE, 
			'' AS ANNOTATION,
			b.AGENCE, 
			0 AS AVOIR, 
			b.CUST_NAME, 
			SUM(IFNULL(b.CONSUMPTION,0)) as SOMME, 
			SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE , 
			COUNT(IFNULL(b.SERVICE_NO,0)) AS NOMBRE 
			FROM bill b, 
			   contract c
		   WHERE c.SERVICE_NO=b.SERVICE_NO  GROUP BY c.SERVICE_NO ORDER BY `b`.`VOLT_TP_ID` DESC";
			  
			  $req=$bdd->query($query, PDO::FETCH_ASSOC);
	         
			  $resultats=$req->fetchAll();
    	    
			  foreach($resultats as $resultat )
			  {
				array_push($result,(object)$resultat);
			  }
			print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
			$req->closeCursor();
				}
				else{
					$result = array();
		  $query="SELECT b.SERVICE_NO, b.VOLT_TP_ID as TL , '' AS STATUT, c.TUTELLE, '' AS ANNOTATION, b.AGENCE, 0 AS AVOIR, b.CUST_NAME, SUM(IFNULL(b.CONSUMPTION,0)) as SOMME, SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE , COUNT(IFNULL(b.SERVICE_NO,0)) AS NOMBRE FROM bill b, contract c WHERE c.SERVICE_NO=b.SERVICE_NO And b.SERVICE_NO IN (SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS='".$_GET['ID_TAGS']."') GROUP BY c.SERVICE_NO ORDER BY `b`.`VOLT_TP_ID` DESC";
			  
			  $req=$bdd->query($query,  PDO::FETCH_ASSOC);
	
			  $resultats=$req->fetchAll();
    	    
			  foreach($resultats as $resultat )
			  {
				array_push($result,(object)$resultat);
			  }

			print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
			$req->closeCursor(); 
				}
		
			}

		  
		}
	
	
		if(isset($_GET["getAbreStructureFromTags2"]))
		{
          $path = 'abre.json';
          $jsonString = file_get_contents($path);
          $jsonData = json_decode($jsonString, true);
         if(isset($_GET["ID_TAGS"]) && !empty($_GET["ID_TAGS"]))
         {
              	  foreach ($jsonData as $object) {
                     
                   }
         }
         
    
// var_dump($jsonData);
print($jsonString);
     
		}
			$value="";	

// 		if(isset($_GET["getAbreStructureFromTags"]))
// 		{
     
//         //  if(isset($_GET["ID_TAGS"]) && !empty($_GET["ID_TAGS"]))
//         //  {
//         //       $tab=getStructureChildContractStructure($_GET["ID_TAGS"]);
    
//         //     $jsonString = json_encode($tab[0]);
       
//         // 	  
//         //  }
         
//          $path = 'abre.json';
// $jsonString = file_get_contents($path);
// $jsonData = json_decode($jsonString, true);
// // var_dump($jsonData);
//         //   print(json_encode($jsonData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
//           echo $jsonString;
     
// 		}
		
	   	if(isset($_GET["getAbreStructureFromTags"]))
		{
			if(isset($_GET["referentiel"]) && !empty($_GET["referentiel"]) &&  $_GET["referentiel"]==true)
			{
        if(isset($_GET["ID_TAGS"]) && !empty($_GET["ID_TAGS"]))
        {
             $tab=getStructureChildContractStructure($_GET["ID_TAGS"],true,1);

             $jsonString = json_encode($tab[0]);
  
           print($jsonString); 
        } 
			}
   			 else
			{
				  if(isset($_GET["ID_TAGS"]) && !empty($_GET["ID_TAGS"]))
        		 {
                  $tab=getStructureChildContractStructure($_GET["ID_TAGS"],false,0);
    
                  $jsonString = json_encode($tab[0]);
       
        	      print($jsonString); 
        		 } 
			 }
       
     
		}
			
	   		
	   	if(isset($_GET["postUser"]))
		{
           $tr=(isset($_GET["EMAIL"]) || isset($_GET["PHONE"])) ;
    
         if( $tr && isset($_GET["ID_TAGS"]))
         {
         $val=$_GET["EMAIL"]??$_GET["PHONE"];
         $email=isset($_GET["EMAIL"])?$_GET["EMAIL"]:NULL;
         $phone=isset($_GET["PHONE"])?$_GET["PHONE"]:NULL;
		 $nom=isset($_GET["NOM"])?$_GET["NOM"]:NULL;
         $prenom=isset($_GET["PRENOM"])?$_GET["PRENOM"]:NULL;
		 $idParent=isset($_GET["ID_PARENT_UTILISATEUR"])?$_GET["ID_PARENT_UTILISATEUR"]:NULL;
		 $pass=$_GET["PASSWORD"]??'123456';

		 $pass = encrypt($pass, $key, $key);

         $query="INSERT INTO `utilisateur` (`ID_UTILISATEUR`, `ID_TAGS`, `EMAIL`, `PHONE`, `PASSWORD`, `NOM`, `PRENOM`, `ID_PARENT_UTILISATEUR`)
		         VALUES (NULL, '".$_GET["ID_TAGS"]."', '".$email."', '".$phone."',  '".$pass."', '".$nom."', '".$prenom."', '".$idParent."')"; 

          $req=$bdd->query($query, PDO::FETCH_ASSOC);
                 if($req)
                 {
                   print($query);   
                 }
                 else
                 {
                    print(0); 
                 }

		
         }
     
		}

		if (isset($_GET["updateUser"])) {
			$tr = (isset($_GET["EMAIL"]) || isset($_GET["PHONE"]));
			    $iv_update = openssl_random_pseudo_bytes(16); 
				$val = $_GET["EMAIL"] ?? $_GET["PHONE"];
				$email = isset($_GET["EMAIL"]) ? $_GET["EMAIL"] : NULL;
				$phone = isset($_GET["PHONE"]) ? $_GET["PHONE"] : NULL;
				$nom = isset($_GET["NOM"]) ? $_GET["NOM"] : NULL ;
				$prenom = isset($_GET["PRENOM"]) ? $_GET["PRENOM"] : NULL;
				
				$pass = $_GET["PASSWORD"] ?? '123456';
				$pass = encrypt($pass, $key, $key);
				
				$query = "UPDATE utilisateur SET EMAIL = :email, PHONE = :phone, `PASSWORD` = :pass, 
						  NOM = :nom, PRENOM = :prenom  WHERE EMAIL = :email or PHONE = :phone";
				
				try {
	
					// Préparation de la requête de mise à jour
					$stmt = $bdd->prepare($query);
					
					// Exécution de la requête avec les valeurs des paramètres
					$stmt->bindParam(':email', $email);
					$stmt->bindParam(':phone', $phone);
					$stmt->bindParam(':pass', $pass);
					$stmt->bindParam(':nom', $nom);
					$stmt->bindParam(':prenom', $prenom);
					$stmt->execute();
					
					$rowCount = $stmt->rowCount();
					
					if ($rowCount > 0) {
						echo "Mise à jour réussie pour l'utilisateur avec ID_TAGS : " . $_GET["ID_TAGS"];
					} else {
						echo "Aucune mise à jour effectuée. L'utilisateur avec ID_TAGS : " . $_GET["ID_TAGS"] . " n'a pas été trouvé.";
					}
				} catch (PDOException $e) {
					echo "Erreur lors de la mise à jour de l'utilisateur dans la base de données : " . $e->getMessage();
				}
			
		}

		if(isset($_GET["deleteUser"]))
		{
		  if(isset($_GET["ID_UTILISATEUR"]) && !empty($_GET["ID_UTILISATEUR"]))
          {
			$idParent=$_GET["ID_UTILISATEUR"];

			$query="DELETE FROM utilisateur WHERE `utilisateur`.`ID_UTILISATEUR`  =".$idParent; 
	        $req=$bdd->query($query, PDO::FETCH_ASSOC);
			if($req)
			{
			  print(true);   
			}
			else
			{
			   print(0); 
			}
		  }
		}


        if(isset($_GET["gettUsersbyMailOrPhone"]))
		{
           $tr=(isset($_GET["EMAIL"]) || isset($_GET["PHONE"])) ;
      
         if( $tr)
         {
             	    $result = array();
         $val=$_GET["EMAIL"]??$_GET["PHONE"];
         $query="SELECT * FROM `utilisateur` WHERE `EMAIL`='".$val."' or `PHONE`='".$val."'"; 
        	// var_dump($query);
		    $req=$bdd->query($query, PDO::FETCH_ASSOC);
    		$resultats=$req->fetch();
    	
    	    // foreach($resultats as $resultat )
    		// {
    		//     array_push($result,$resultat);
    		// }

          print(json_encode($resultats, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
         }
     
		}
		
		if(isset($_GET["getListUsersChildID"]))
		{
         
      
         if( isset($_GET["ID_UTILISATEUR"]))
         {
             	    $result = array();
         $val=$_GET["ID_UTILISATEUR"];
         $query="SELECT * FROM `utilisateur` WHERE `ID_PARENT_UTILISATEUR`='".$val."'"; 
        	
		    $req=$bdd->query($query, PDO::FETCH_ASSOC);
    		$resultats=$req->fetchAll();
    	
    	    foreach($resultats as $resultat )
    		{
    		    array_push($result,$resultat);
    		}

          print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
         }
     
		}
	
		function getStructureChildContractStructure($id,$referentiel,$count)
		{
		      
		    include("connect_bd.php");
		    $result = array();
		      $res = array();
		    $query= "SELECT * FROM structure_contient_tags st, tags t,`structure` s WHERE s.ID_STRUCTURE=st.ID_STRUCTURE and t.ID_TAGS=st.ID_TAGS and (s.ID_STRUCTURE_PARENT='".$id."' or t.ID_TAGS='".$id."')";
		      //var_dump($query);
		   $childrens= array();;
		    $req=$bdd->query($query, PDO::FETCH_ASSOC);
    		$resultats=$req->fetchAll();
    	
    	    foreach($resultats as $resultat )
    		{
                    array_push($result,array(
                    "ID_STRUCTURE"=> $resultat["ID_STRUCTURE"],
                    "ID_STRUCTURE_PARENT"=> $resultat["ID_STRUCTURE_PARENT"],
                    "TITRE_FR"=>$resultat["TITRE_FR"],
                    "TITRE_EN"=> $resultat["TITRE_EN"],
                    "TITRE_COURT_FR"=> $resultat["TITRE_COURT_FR"],
                    "TITRE_COURT_EN"=> $resultat["TITRE_COURT_EN"],
                    "TUTELLE"=> $resultat["TUTELLE"],
                    "ID_TAGS"=> $resultat["ID_TAGS"],
                    "CHILDREN"=> [],
                    // "CONTRACT"=>getStructureContratByTag($resultat["ID_TAGS"]),
                    
                    ));
    		}
        if($referentiel==true) $count=$count+1;
    		if(!empty($result))
    		{
    		    
    		    
    		         foreach($result as $key => $elt )
    		        {

    		            if(!empty($elt["ID_STRUCTURE"]))
    		            {
    		                $children=getStructureChildContractStructure($result[$key]["ID_STRUCTURE"],$referentiel,$count);
    		               
    		                if(!empty($children) && $children!=[])
    		                {
                              if($count<=2)
                              {
                                array_push($result[$key]["CHILDREN"],$children) ;
                              }
                              
                             
    	                 
    		                }
    		            }
    	        	} 

    		}

    				$req->closeCursor();
    		  return $result;
    
		
		}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
		$idbase=0;
	
		function getAllStructureChild($id,$idbqse)
		{
		    $idbase=$idbqse;
		   echo $idbqse;
		    include("connect_bd.php");
		    $result = array();
		    $tab = array();
		      $res = array();
		    $query= "SELECT * FROM `structure` WHERE `ID_STRUCTURE_PARENT`=".$id;
		    
		    
		    $req=$bdd->query($query, PDO::FETCH_ASSOC);
    		$resultats=$req->fetchAll();
    	
    	    foreach($resultats as $resultat)
    		{
    		    array_push($result,array($id=>$resultat));
    		}
    		if(!empty($result))
    		{
    		   
    		        foreach($result as $elt)
    		        {
    
    		            if(!empty($tab))
    		            {

                            if($idbqse!=[$id])
    		                 array_push($tab[$idbqse][$id],array($id=>$elt));
    		                 else
    		                array_push($tab[$id],getAllStructureChild($elt[$id]["ID_STRUCTURE"],$idbase));
    		                
    		          $jsonString = json_encode($tab);
                      echo $jsonString ."<br><br><br>";
    		  
    		            }
    		            else
    		            {
    		                array_push($tab,getAllStructureChild($elt[$id]["ID_STRUCTURE"],$idbase));
    		            }
    	        	} 

    		}
    		else
    		{
		                		        		                    		              $jsonString = json_encode($tab);
echo $jsonString ."<br><br><br>";
    		    return $tab;
    		}
    		$req->closeCursor();
		
		}
		
			if(isset($_GET["getGlobaltest"]))
		{
         $url="https://ppsr.eneoapps.com/apiContrat/serverApi.php?get_facture=200237921";
        //  	echo getImpayer('203673817');
         	 $result = array();
         
      $tab=getStructureChild(7,$result);
        global $val;
        $res=substr($val, 0, -1);
        // echo "[".$val."]";
        print("[".$res."]"); 
//             		  $jsonString = json_encode($tab);
// echo $jsonString;
        // 	print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK)); 
		}
		
	   
	$val="";
			function getStructureChild($id,$tab)
		{
		      
		    include("connect_bd.php");
		    $result = array();
		      $res = array();
		    $query= "SELECT * FROM `structure` WHERE `ID_STRUCTURE_PARENT`=".$id;
		    $req=$bdd->query($query, PDO::FETCH_ASSOC);
    		$resultats=$req->fetchAll();
    	
    	    foreach($resultats as $resultat )
    		{
    		    array_push($result,array(
    		        "ID_STRUCTURE"=> $resultat["ID_STRUCTURE"],
    "ID_STRUCTURE_PARENT"=> $resultat["ID_STRUCTURE_PARENT"],
    "TITRE_FR"=>$resultat["TITRE_FR"],
    "TITRE_EN"=> $resultat["TITRE_EN"],
    "TITRE_COURT_FR"=> $resultat["TITRE_COURT_FR"],
    "TITRE_COURT_EN"=> $resultat["TITRE_COURT_EN"],
    "TUTELLE"=> $resultat["TUTELLE"],
    "CHILDREN"=>[]));
    		}
    		if(!empty($result))
    		{
    		        		              $jsonString = json_encode($result);
          $jsonString .=",";
        global $val;
        $val.=$jsonString;
        // echo $val;
    		         foreach($result as $elt )
    		        {
//     		              $jsonString = json_encode($elt);
// echo $jsonString ."<br>";
    		            if(!empty($elt["ID_STRUCTURE"]))
    		            {
    		                 $tab=$result;
    		                 array_push($elt["CHILDREN"],getStructureChild($elt["ID_STRUCTURE"],$tab));
    		            }
    		             
    		          //  else
    		          //  {
    		          //      array_push($result["CHILDREN"],array());
    		          //  }
    	        	} 

    		}
    		else
    		{
    		    return $tab;
    		}
    		$req->closeCursor();
		
		}
		
		function getStructureChilds($id)
		{
		      
		    include("connect_bd.php");
		    $result = array();
		  
		    $query= "SELECT * FROM `structure` WHERE `ID_STRUCTURE_PARENT`=".$id;
		    $req=$bdd->query($query, PDO::FETCH_ASSOC);
    		$resultats=$req->fetchAll();
    	
    	    foreach($resultats as $resultat )
    		{
    		    array_push($result,$resultat);
    		}

    		  //$jsonString = json_encode($result);
        //     echo $jsonString;
    		   
    
    		$req->closeCursor();
			return $result;
		
		}
		
		
// 			if(isset($_GET["getLastTwelveFacture"]))
// 		{
		    
// 		    if(isset($_GET["SERVICE_NO"]) && !empty($_GET["SERVICE_NO"]))
// 		    {
		        
// 		         $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_facture=".$_GET["SERVICE_NO"];  
// 		     $jsonobj=getApiInfos($url);
// 		     $tab=json_decode($jsonobj);
// 		     $taille = count($tab);
// 		     $res=$tab[$taille-1];
		     
		 
// 		       	 $result=array(
// 		       	     'SERVICE_NO'=>$res->contract_number,
// 			    'Date' =>$res->Bill_Details->payment_deadline,
// 			    'SOMME' => str_replace(" Kwh", "", $res->Bill_Details->consumption),
// 			    'PRICE' => $res->amount_with_tax
// 			    );
		
// 		print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK)); 
// 		    }
	
// 		}
	
		if(isset($_GET["getLastFacture"]))
		{
		    
		    if(isset($_GET["SERVICE_NO"]) && !empty($_GET["SERVICE_NO"]))
		    {
		        
		         $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_facture=".$_GET["SERVICE_NO"];  
		     $jsonobj=getApiInfos($url);
		     $tab=json_decode($jsonobj);
		     $taille = count($tab);
		     $res=$tab[$taille-1];
		     
		 
		       	 $result=array(
		       	     'SERVICE_NO'=>$res->contract_number,
			    'Date' =>$res->Bill_Details->payment_deadline,
			    'SOMME' => str_replace(" Kwh", "", $res->Bill_Details->consumption),
			    'PRICE' => $res->amount_with_tax
			    );
		
		print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK)); 
		    }
	
		}
	
	
	if(isset($_GET["getLastTwelveFacture"]))
		{
		    
		    if(isset($_GET["SERVICE_NO"]) && !empty($_GET["SERVICE_NO"]))
		    {
		     $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_facture=".$_GET["SERVICE_NO"];  
		     print(getApiInfos($url));
		    }
		    else
		     {
		      $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_facture";  
		      print(getApiInfos($url));
		     }
         
        //  	print(getApiInfos($url, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
         
		}
		if(isset($_GET["getFacturesByContract"]))
		{
		    
		    if(isset($_GET["SERVICE_NO"]) && !empty($_GET["SERVICE_NO"]))
		    {
		     $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_facture=".$_GET["SERVICE_NO"];  
		     print(getApiInfos($url));
		    }
		    else
		     {
		      $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_facture";  
		      print(getApiInfos($url));
		     }
         
        //  	print(getApiInfos($url, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
         
		}
		function getImpayer($SERVICE_NO)
		{
		  $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_facturesImpayes=".$SERVICE_NO; 
		  $impayer=0;
		  foreach(json_decode(getApiInfos($url), true) as $row)
		  {
		      $impayer= $impayer + (int) str_replace(" FCFA", "",$row["amount_with_tax"]);
		  }

		  return $impayer; 
		}
		
		function getApiInfos($url)
		{
		    		    	$ch = curl_init();
	try {
	    
  
            // Initialisez une session CURL.
              
              
            // Récupérer le contenu de la page
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
              
            //Saisir l'URL et la transmettre à la variable.
            curl_setopt($ch, CURLOPT_URL, $url); 
            //Désactiver la vérification du certificat puisque waytolearnx utilise HTTPS
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            //Exécutez la requête 
            $result = curl_exec($ch); 
            curl_close($ch);
            
            			return $result;
          
        	} catch (\Throwable $th) {
        		throw $th;
        	} finally {
        		curl_close($ch);
        	}
		}
		
	
				
		function postApiInfos($url,$datas)
		{
		    		    	$ch = curl_init();
	try {
	    
  
            // Initialisez une session CURL.
              
              
            // Récupérer le contenu de la page
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
              
            //Saisir l'URL et la transmettre à la variable.
            curl_setopt($ch, CURLOPT_URL, $url); 
            curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
            //Désactiver la vérification du certificat puisque waytolearnx utilise HTTPS
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            //Exécutez la requête 
            $result = curl_exec($ch); 
            curl_close($ch);
            
            			return $result;
          
        	} catch (\Throwable $th) {
        		throw $th;
        	} finally {
        		curl_close($ch);
        	}
		}
		
		
		if(isset($_GET["getFacturesImpayerByContract"]))
		{
		    
		    if(isset($_GET["SERVICE_NO"]) && !empty($_GET["SERVICE_NO"]))
		    {
		     $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_facturesImpayes=".$_GET["SERVICE_NO"];  
		     print(getApiInfos($url));
		    }
		    else
		     {
		      $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_facturesImpayes";  
		      print(getApiInfos($url));
		     }
         
        //  	print(getApiInfos($url, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
         
		}
		
		
		if(isset($_GET["getFacturesReclamationByContract"]))
		{
		    
		    if(isset($_GET["SERVICE_NO"]) && !empty($_GET["SERVICE_NO"]))
		    {
		     $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_reclamations=".$_GET["SERVICE_NO"];  
		     print(getApiInfos($url, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
		    }
		    else
		     {
		      $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_reclamations";  
		      print(getApiInfos($url, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
		     }
         
        //  	print(getApiInfos($url, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
         
		}
		
		
		if(isset($_GET["getFacturesListPayementByContract"]))
		{
		    
		    if(isset($_GET["SERVICE_NO"]) && !empty($_GET["SERVICE_NO"]))
		    {
		     $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_payement=".$_GET["SERVICE_NO"];  
		     print(getApiInfos($url, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
		    }
		    else
		     {
		      $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_payement";  
		      print(getApiInfos($url, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
		     }
         
        //  	print(getApiInfos($url, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
         
		}
		
		if(isset($_GET["getDetailsContract"]))
		{ 
		    if(isset($_GET["SERVICE_NO"]) && !empty($_GET["SERVICE_NO"]))
		    {
		     $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_infoContrat=".$_GET["SERVICE_NO"];  
		     print(getApiInfos($url, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
		    }
		    else
		     {
		    //   $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_infoContrat";  
		      print(getApiInfos($url, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
		     } 
		}
		
		if(isset($_GET["getBalanceContract"]))
		{ 
		    if(isset($_GET["SERVICE_NO"]) && !empty($_GET["SERVICE_NO"]))
		    {
		     $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_balance=".$_GET["SERVICE_NO"];  
		     print(getApiInfos($url, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
		    }
		    else
		     {
		      $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_balance";  
		      print(getApiInfos($url, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
		     } 
		}
		
		
	if (isset($_GET["getGlobalInformationsContractsWithRegions"])) {
        $result = array();
        $query="";
        if(isset($_GET["DISPATCH_DATE"]) && !empty($_GET["DISPATCH_DATE"]))
        {
         $query=" SELECT 
     f.REGION,
     f.SOMME,
     f.PRICE,
     f.NOMBRE,
     mv.SOMMEMV,
     mv.PRICEMV,
     mv.NOMBREMV,
     lv.SOMMELV,
     lv.PRICELV,
     lv.NOMBRELV
     FROM 
     (SELECT 
           bc.REGION,
     	   SUM(IFNULL(bc.CONSUMPTION,0)) as SOMME,
           SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICE,
           COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBRE
     From 
     	bill bc
     WHERE 
        YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
         GROUP BY bc.REGION) f,
    (SELECT 
           bc.REGION,
     	   SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMELV,
           SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICELV,
           COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBRELV
     From 
     	bill bc
     WHERE 
        YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
          AND bc.VOLT_TP_ID='LV' GROUP BY bc.REGION) lv,
          
    (SELECT 
           bc.REGION,
     	   SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMEMV,
           SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICEMV,
           COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBREMV
     From 
     	bill bc
     WHERE 
        YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
          AND bc.VOLT_TP_ID='MV' GROUP BY bc.REGION) mv
          WHERE lv.REGION=mv.REGION and f.REGION=mv.REGION";
        }
        else
        {
        $query="SELECT 
     f.REGION,
     f.SOMME,
     f.PRICE,
     f.NOMBRE,
     mv.SOMMEMV,
     mv.PRICEMV,
     mv.NOMBREMV,
     lv.SOMMELV,
     lv.PRICELV,
     lv.NOMBRELV
     FROM 
     (SELECT 
           bc.REGION,
     	   SUM(IFNULL(bc.CONSUMPTION,0)) as SOMME,
           SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICE,
           COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBRE
     From 
     	bill bc
    GROUP BY bc.REGION) f,
    (SELECT 
           bc.REGION,
     	   SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMELV,
           SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICELV,
           COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBRELV
     From 
     	bill bc
     WHERE 
        bc.VOLT_TP_ID='LV' GROUP BY bc.REGION) lv,
          
    (SELECT 
           bc.REGION,
     	   SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMEMV,
           SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICEMV,
           COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBREMV
     From 
     	bill bc
     WHERE  bc.VOLT_TP_ID='MV' GROUP BY bc.REGION) mv
          WHERE lv.REGION=mv.REGION and f.REGION=mv.REGION";
   

        }
	   
    		$req=$bdd->query($query, PDO::FETCH_ASSOC);
		$resultats=$req->fetchAll();
		
	    foreach($resultats as $resultat )
		{
		    		array_push($result,(object)$resultat);
		}
		print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
		$req->closeCursor();
	}
	
	
// 	if (isset($_GET["getInformationsContractPassOneYear"])) {
//         $result = array();
// 	    //$query="SELECT CONCAT(MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y')),'/',YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))) AS MONTHS,
// 	   // $query="SELECT STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y') AS MONTHS,
// 	   // SUM(IFNULL(bc.CONSUMPTION,0)) as SOMME, SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICE, COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBRE 
// 	   // From bill bc WHERE DATE_FORMAT(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'),'%Y-%m-%d') BETWEEN STR_TO_DATE(CONCAT(YEAR(NOW())-1,'-',MONTH(NOW()),'-',DAY(NOW())),'%Y-%m-%d')
// 	   // AND STR_TO_DATE(CONCAT(YEAR(NOW()),'-',MONTH(NOW()),'-',DAY(NOW())),'%Y-%m-%d') 
// 	   // GROUP BY CONCAT(MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y')),'/',YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y')))";
   
//       if (isset($_GET["SERVICE_NO"]) && !empty($_GET["SERVICE_NO"])) 
// 	    {
// 	    $query="SELECT STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y') AS MONTHS,
// 	    SUM(IFNULL(bc.CONSUMPTION,0)) as SOMME, SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICE
// 	    From bill bc WHERE bc.SERVICE_NO= '".$_GET["SERVICE_NO"]."' and  DATE_FORMAT(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'),'%Y-%m-%d') BETWEEN STR_TO_DATE(CONCAT(YEAR(NOW())-1,'-',MONTH(NOW()),'-',DAY(NOW())),'%Y-%m-%d')
// 	    AND STR_TO_DATE(CONCAT(YEAR(NOW()),'-',MONTH(NOW()),'-',DAY(NOW())),'%Y-%m-%d') 
// 	    GROUP BY CONCAT(MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y')),'/',YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y')))";
// 		$req=$bdd->query($query, PDO::FETCH_ASSOC);
// 		$resultats=$req->fetchAll();
		
// 	    foreach($resultats as $resultat )
// 		{
// 		    		array_push($result,(object)$resultat);
// 		}
// 		print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
// 		$req->closeCursor();
//     	}    
// 	}
	
	
		if (isset($_GET["getInformationsContractPassOneYear"])) {
      
        if (isset($_GET["SERVICE_NO"]) && !empty($_GET["SERVICE_NO"])) 
	    {
	        
	    $result = array();
	    $query="SELECT STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y') AS MONTHS,
	    SUM(IFNULL(bc.CONSUMPTION,0)) as SOMME, SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICE
	    From bill bc WHERE bc.SERVICE_NO= '".$_GET["SERVICE_NO"]."' and  DATE_FORMAT(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'),'%Y-%m-%d') BETWEEN STR_TO_DATE(CONCAT(YEAR(NOW())-1,'-',MONTH(NOW()),'-',DAY(NOW())),'%Y-%m-%d')
	    AND STR_TO_DATE(CONCAT(YEAR(NOW()),'-',MONTH(NOW()),'-',DAY(NOW())),'%Y-%m-%d') 
	    GROUP BY CONCAT(MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y')),'/',YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y')))";
		$req=$bdd->query($query, PDO::FETCH_ASSOC);
		$resultats=$req->fetchAll();
		
	    foreach($resultats as $resultat )
		{
		    		array_push($result,(object)$resultat);
		}
		
		     $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_facture=".$_GET["SERVICE_NO"];  
		     $jsonobj=getApiInfos($url);
		     $tab=json_decode($jsonobj);
		     $taille = count($tab);
		     $res=$tab[$taille-1];
		     $lt=(int)date("m");
		     $i;
		     $debut=$taille-$lt+1;
		     $fin=$taille-1;
		    
		     for($i=$debut;$i<=$fin;$i++)
		     {
		         $date = new DateTime($tab[$i]->Bill_Details->payment_deadline);
		       array_push($result,array(
		       	 
			    'MONTHS' =>$date->format('Y-m-d'),
			    'SOMME' => str_replace(" Kwh", "", $tab[$i]->Bill_Details->consumption),
			    'PRICE' => $tab[$i]->amount_with_tax
			    ));
		     }
		print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
		$req->closeCursor();
    	}
       
    
	}
		if (isset($_GET["getListContractWithRegroupCode"])) {
        $result = array();
	    $query="SELECT b.`SERVICE_NO` , c.`REGROUP_ID` FROM `contract` c, bill b WHERE c.SERVICE_NO=b.SERVICE_NO GROUP BY c.`REGROUP_ID`";
   
		$req=$bdd->query($query, PDO::FETCH_ASSOC);
		$resultats=$req->fetchAll();
		
	    foreach($resultats as $resultat )
		{
		    		array_push($result,(object)$resultat);
		}
		print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
		$req->closeCursor();
    
	}
		
	if (isset($_GET["getInformationsContract"])) {
     	if (isset($_GET["SERVICE_NO"]) && !empty($_GET["SERVICE_NO"])) 
	    {
	    $query="SELECT * FROM `contract` WHERE `SERVICE_NO`='".$_GET["SERVICE_NO"]."'";
   
		$req=$bdd->query($query, PDO::FETCH_ASSOC);
		$resultat=$req->fetchAll();
		print(json_encode((object)$resultat[0], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
		$req->closeCursor();
    	}
	}	
	if (isset($_GET["getInformationsReleve"])) {
// 		$result = array();
	   // $query="SELECT * FROM `releve`";
       	if (isset($_GET["SERVICE_NO"]) && !empty($_GET["SERVICE_NO"])) 
	    {
	    $query="SELECT * FROM `releve` WHERE `SERVICE_NO`='".$_GET["SERVICE_NO"]."'";
	      $req=$bdd->query($query, PDO::FETCH_ASSOC);

// 	   while($resultat=$req->fetch()){
// 		   array_push($result,(object)$resultat);
// 		 }
       $resultat=$req->fetch();
	   print(json_encode($resultat?(object)$resultat:null, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
	   $req->closeCursor();
    	}
	 

   }
		
		
    if (isset($_GET["getListInformationsContratsByTutelle"])) 
	{
	  if (isset($_GET["TUTELLE"]) && !empty($_GET["TUTELLE"])) 
	    {
	    $result = array();
	    $query="SELECT 
	            b.SERVICE_NO,
	            b.VOLT_TP_ID as TL ,
	            '' AS STATUT, c.TUTELLE, 
	            '' AS ANNOTATION,
	            b.AGENCE, 
	            0 AS AVOIR, 
	            b.CUST_NAME, 
	            SUM(IFNULL(b.CONSUMPTION,0)) as SOMME, 
	            SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE , 
	            COUNT(IFNULL(b.SERVICE_NO,0)) AS NOMBRE 
	            FROM bill b, 
	               contract c 
	           WHERE c.SERVICE_NO=b.SERVICE_NO    
	           AND 
             c.TUTELLE='".$_GET['TUTELLE']."' 
             GROUP BY c.SERVICE_NO ORDER BY `b`.`VOLT_TP_ID` DESC";
          
		  $req=$bdd->query($query, PDO::FETCH_ASSOC);

		while($resultat=$req->fetch()){
			array_push($result,(object)$resultat);
      	}

		print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
		$req->closeCursor();
	    }
	    else
	    {
	        $result = array();
	  $query="SELECT 
	            b.SERVICE_NO,
	            b.VOLT_TP_ID as TL ,
	            '' AS STATUT, c.TUTELLE, 
	            '' AS ANNOTATION,
	            b.AGENCE, 
	            0 AS AVOIR, 
	            b.CUST_NAME, 
	            SUM(IFNULL(b.CONSUMPTION,0)) as SOMME, 
	            SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE , 
	            COUNT(IFNULL(b.SERVICE_NO,0)) AS NOMBRE 
	            FROM bill b, 
	               contract c
	           WHERE c.SERVICE_NO=b.SERVICE_NO  GROUP BY c.SERVICE_NO ORDER BY `b`.`VOLT_TP_ID` DESC";
          
		  $req=$bdd->query($query);

		while($resultat=$req->fetch()){
                array_push($result,(object)$resultat);
		}
		print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
		$req->closeCursor(); 
	    }
	  
	}
		if (isset($_GET["getListInformationsContrats"])) 
	{
	 
	    $result = array();
	  $query="SELECT 
	            b.SERVICE_NO,
	            b.VOLT_TP_ID ,
	            '' AS Statut, c.TUTELLE, 
	            '' AS Annotation,
	            b.AGENCE, 
	            0 AS Avoir, 
	            b.CUST_NAME, 
	            SUM(IFNULL(b.CONSUMPTION,0)) as SOMME, 
	            SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE , 
	            COUNT(IFNULL(b.SERVICE_NO,0)) AS NOMBRE 
	            FROM bill b, 
	               contract c 
	           WHERE c.SERVICE_NO=b.SERVICE_NO  GROUP BY c.SERVICE_NO ORDER BY `b`.`VOLT_TP_ID` DESC";
          
		  $req=$bdd->query($query);
		
		while($resultat=$req->fetch()){
			array_push($result,array(
		        'SERVICE_NO' => $resultat["SERVICE_NO"],
			    'TL' => $resultat["VOLT_TP_ID"],
			    'STATUT' => $resultat["Statut"],
			    'TUTELLE' => $resultat["TUTELLE"],
			    'ANNOTATION' => $resultat["Annotation"],
			    'AGENCE' => $resultat["AGENCE"],
			    'AVOIR' => $resultat["Avoir"],
			    'CUST_NAME' => $resultat["CUST_NAME"]
			    
			));
		}
// 		echo "<script>console.log( json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK))</script>";
		print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
		$req->closeCursor();
	  
	}
	
if (isset($_GET["getGlobalInformationsInstitutionsPerMonth"])) 
	{
	    if (isset($_GET["DISPATCH_DATE"])) 
	    {
	    $result = array();
	    $query="SELECT * From GlobalInformationsInstitutionsPerMonth WHERE YEARS=".$_GET["DISPATCH_DATE"];
		$req=$bdd->query($query);
		
		while($resultat=$req->fetch()){
			array_push($result,array(
			    'MONTHS' => $resultat["MONTHS"],
			    'SOMME' => $resultat["SOMME"],
			    'PRICE' => $resultat["PRICE"],
			    'NOMBRE' => $resultat["NOMBRE"],
			    'SOMMELV' => $resultat["SOMMELV"],
			    'PRICELV' => $resultat["PRICELV"],
			    'NOMBRELV' => $resultat["NOMBRELV"],
			    'SOMMEMV' => $resultat["SOMMEMV"],
			    'PRICEMV' => $resultat["PRICEMV"],
			    'NOMBREMV' => $resultat["NOMBREMV"],
			    
			));
		}
// 		echo "<script>console.log( json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK))</script>";
		print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
		$req->closeCursor();
	    }
	}
   function	format_month($Tab)
	{
	    $month='';
	         $MONTHS=explode(",", $Tab);
	               foreach($MONTHS  as $key => $elt)
	               {
	                   if(strlen($elt)>1)
	                   $month.=$key>0?",".$elt:$elt;
	                   else
	                   $month.=$key>0?",0".$elt:"0".$elt;
	               } 
	               return $month;
	}
	
	

	
	if (isset($_GET["getInformationsInstitutionsContractByTutelleAndDateAndTag"])) {
	    if (isset($_GET["TUTELLE"]) && isset($_GET["DISPATCH_DATE"])) 
	    {
	    $result = array();
	    $query="SELECT 
	                 b.SERVICE_NO,
	                  b.`AGENCE`,
	                  b.`VOLT_TP_ID`,
	                 b.CUST_NAME, 
	                 SUM(IFNULL(b.CONSUMPTION,0)) as SOMME,
	                 SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE , 
	                 COUNT(IFNULL(b.SERVICE_NO,0)) AS NOMBRE
	            FROM 
	               bill b, 
	               contract c 
	            WHERE 
	               c.SERVICE_NO=b.SERVICE_NO 
	                 and 
	               c.TUTELLE='".$_GET['TUTELLE']."' 
	                 AND 
	               YEAR(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']." 
	            GROUP BY c.SERVICE_NO";
		  //$req=$bdd->query($query);
			  $req=$bdd->query($query, PDO::FETCH_ASSOC);

		while($resultat=$req->fetch()){
			array_push($result,(object)$resultat);
      	}
// 		echo "<script>console.log( json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK))</script>";
		print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
		$req->closeCursor();
	    }
	}


    if (isset($_GET["getInformationsInstitutionsContractByTutelleAndDate"])) {

        if(isset($_GET["DISPATCH_DATE"]) && !empty($_GET["DISPATCH_DATE"]) && $_GET["DISPATCH_DATE"]=='2023')
        {
            if ( isset($_GET["ID_TAGS"]) && !empty($_GET["ID_TAGS"])) 
        {
            if( $_GET["ID_TAGS"]==='GLOBAL')
            {
              if (isset($_GET["TUTELLE"]) && isset($_GET["DISPATCH_DATE"])) 
               {
                if(isset($_GET["MONTH"]) && !empty($_GET["MONTH"]))
                {
                        $result = array();
                    $query="SELECT 
                     b.SERVICE_NO,
                      b.`AGENCE`,
                      b.`VOLT_TP_ID`,
                     b.CUST_NAME, 
                     SUM(IFNULL(b.CONSUMPTION,0)) as SOMME,
                     SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE , 
                     COUNT(IFNULL(b.SERVICE_NO,0)) AS NOMBRE
                        FROM 
                   bill b, 
                   contract c 
                    WHERE 
                   c.SERVICE_NO=b.SERVICE_NO 
                     and 
                   c.TUTELLE='".$_GET['TUTELLE']."' 
                     AND 
                   YEAR(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y'))=2021
                    AND MONTH(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y')) IN(".$_GET["MONTH"].")
                    GROUP BY c.SERVICE_NO";
                  //$req=$bdd->query($query);
                 
                  $req=$bdd->query($query, PDO::FETCH_ASSOC);
    
                while($resultat=$req->fetch()){
                array_push($result,(object)$resultat);
                  }
            // 		echo "<script>console.log( json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK))</script>";
                    print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
                    $req->closeCursor();
                }
                else
                {
                        $result = array();
                    $query="SELECT 
                     b.SERVICE_NO,
                      b.`AGENCE`,
                      b.`VOLT_TP_ID`,
                     b.CUST_NAME, 
                     SUM(IFNULL(b.CONSUMPTION,0)) as SOMME,
                     SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE , 
                     COUNT(IFNULL(b.SERVICE_NO,0)) AS NOMBRE
                        FROM 
                   bill b, 
                   contract c 
                    WHERE 
                   c.SERVICE_NO=b.SERVICE_NO 
                     and 
                   c.TUTELLE='".$_GET['TUTELLE']."' 
                     AND 
                   YEAR(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y'))=2021 
                    GROUP BY c.SERVICE_NO";
                  //$req=$bdd->query($query);
                 
                  $req=$bdd->query($query, PDO::FETCH_ASSOC);
    
                while($resultat=$req->fetch()){
                array_push($result,(object)$resultat);
                  }
            // 		echo "<script>console.log( json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK))</script>";
                    print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
                    $req->closeCursor();
                }
                }
            }
           else if( isset($_GET["DISPATCH_DATE"]))
            {
    
    
               if(isset($_GET["MONTH"]) && !empty($_GET["MONTH"]))
               {
                      $result = array();
                   $query="SELECT 
                     b.SERVICE_NO,
                      b.`AGENCE`,
                      b.`VOLT_TP_ID`,
                     b.CUST_NAME, 
                     SUM(IFNULL(b.CONSUMPTION,0)) as SOMME,
                     SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE , 
                     COUNT(IFNULL(b.SERVICE_NO,0)) AS NOMBRE
                FROM 
                   bill b, 
                   contract c ,
        contrat_possede_tags cpt
                WHERE 
                   c.SERVICE_NO=b.SERVICE_NO 
                     and 
                   b.SERVICE_NO IN(
                    SELECT
                        SERVICE_NO
                    FROM
                        contrat_possede_tags
                    WHERE
                        ID_TAGS = '".$_GET['ID_TAGS']."'
                ) AND
                      
                   YEAR(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y'))=2021 
                    AND MONTH(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y')) IN(".$_GET["MONTH"].") AND 
                  c.SERVICE_NO=cpt.SERVICE_NO 
                GROUP BY c.SERVICE_NO";
        //   $req=$bdd->query($query);
        // echo $query;
              $req=$bdd->query($query, PDO::FETCH_ASSOC);
    
        while($resultat=$req->fetch()){
            array_push($result,(object)$resultat);
          }
        // echo "<script>console.log( json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK))</script>";
        print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
        $req->closeCursor();  
               }
               else
               {
                      $result = array();
                   $query="SELECT 
                     b.SERVICE_NO,
                      b.`AGENCE`,
                      b.`VOLT_TP_ID`,
                     b.CUST_NAME, 
                     SUM(IFNULL(b.CONSUMPTION,0)) as SOMME,
                     SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE , 
                     COUNT(IFNULL(b.SERVICE_NO,0)) AS NOMBRE
                FROM 
                   bill b, 
                   contract c ,
        contrat_possede_tags cpt
                WHERE 
                   c.SERVICE_NO=b.SERVICE_NO 
                     and 
                   b.SERVICE_NO IN(
                    SELECT
                        SERVICE_NO
                    FROM
                        contrat_possede_tags
                    WHERE
                        ID_TAGS = '".$_GET['ID_TAGS']."'
                ) AND
                      
                   YEAR(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y'))=2021 
                    AND 
                  c.SERVICE_NO=cpt.SERVICE_NO 
                GROUP BY c.SERVICE_NO";
        //   $req=$bdd->query($query);
        // echo $query;
              $req=$bdd->query($query, PDO::FETCH_ASSOC);
    
        while($resultat=$req->fetch()){
            array_push($result,(object)$resultat);
          }
        // echo "<script>console.log( json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK))</script>";
        print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
        $req->closeCursor();  
               }
            }
        }
        }
        else
        {
    
        
        if ( isset($_GET["ID_TAGS"]) && !empty($_GET["ID_TAGS"])) 
        {
            if( $_GET["ID_TAGS"]==='GLOBAL')
            {
              if (isset($_GET["TUTELLE"]) && isset($_GET["DISPATCH_DATE"])) 
               {
                if(isset($_GET["MONTH"]) && !empty($_GET["MONTH"]))
                {
                        $result = array();
                    $query="SELECT 
                     b.SERVICE_NO,
                      b.`AGENCE`,
                      b.`VOLT_TP_ID`,
                     b.CUST_NAME, 
                     SUM(IFNULL(b.CONSUMPTION,0)) as SOMME,
                     SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE , 
                     COUNT(IFNULL(b.SERVICE_NO,0)) AS NOMBRE
                        FROM 
                   bill b, 
                   contract c 
                    WHERE 
                   c.SERVICE_NO=b.SERVICE_NO 
                     and 
                   c.TUTELLE='".$_GET['TUTELLE']."' 
                     AND 
                   YEAR(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
                    AND MONTH(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y')) IN(".$_GET["MONTH"].")
                    GROUP BY c.SERVICE_NO";
                  //$req=$bdd->query($query);
                 
                  $req=$bdd->query($query, PDO::FETCH_ASSOC);
    
                while($resultat=$req->fetch()){
                array_push($result,(object)$resultat);
                  }
            // 		echo "<script>console.log( json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK))</script>";
                    print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
                    $req->closeCursor();
                }
                else
                {
                        $result = array();
                    $query="SELECT 
                     b.SERVICE_NO,
                      b.`AGENCE`,
                      b.`VOLT_TP_ID`,
                     b.CUST_NAME, 
                     SUM(IFNULL(b.CONSUMPTION,0)) as SOMME,
                     SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE , 
                     COUNT(IFNULL(b.SERVICE_NO,0)) AS NOMBRE
                        FROM 
                   bill b, 
                   contract c 
                    WHERE 
                   c.SERVICE_NO=b.SERVICE_NO 
                     and 
                   c.TUTELLE='".$_GET['TUTELLE']."' 
                     AND 
                   YEAR(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']." 
                    GROUP BY c.SERVICE_NO";
                  //$req=$bdd->query($query);
                 
                  $req=$bdd->query($query, PDO::FETCH_ASSOC);
    
                while($resultat=$req->fetch()){
                array_push($result,(object)$resultat);
                  }
            // 		echo "<script>console.log( json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK))</script>";
                    print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
                    $req->closeCursor();
                }
                }
            }
           else if( isset($_GET["DISPATCH_DATE"]))
            {
    
    
               if(isset($_GET["MONTH"]) && !empty($_GET["MONTH"]))
               {
                      $result = array();
                   $query="SELECT 
                     b.SERVICE_NO,
                      b.`AGENCE`,
                      b.`VOLT_TP_ID`,
                     b.CUST_NAME, 
                     SUM(IFNULL(b.CONSUMPTION,0)) as SOMME,
                     SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE , 
                     COUNT(IFNULL(b.SERVICE_NO,0)) AS NOMBRE
                FROM 
                   bill b, 
                   contract c ,
        contrat_possede_tags cpt
                WHERE 
                   c.SERVICE_NO=b.SERVICE_NO 
                     and 
                   b.SERVICE_NO IN(
                    SELECT
                        SERVICE_NO
                    FROM
                        contrat_possede_tags
                    WHERE
                        ID_TAGS = '".$_GET['ID_TAGS']."'
                ) AND
                      
                   YEAR(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']." 
                    AND MONTH(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y')) IN(".$_GET["MONTH"].") AND 
                  c.SERVICE_NO=cpt.SERVICE_NO 
                GROUP BY c.SERVICE_NO";
        //   $req=$bdd->query($query);
        // echo $query;
              $req=$bdd->query($query, PDO::FETCH_ASSOC);
    
        while($resultat=$req->fetch()){
            array_push($result,(object)$resultat);
          }
        // echo "<script>console.log( json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK))</script>";
        print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
        $req->closeCursor();  
               }
               else
               {
                      $result = array();
                   $query="SELECT 
                     b.SERVICE_NO,
                      b.`AGENCE`,
                      b.`VOLT_TP_ID`,
                     b.CUST_NAME, 
                     SUM(IFNULL(b.CONSUMPTION,0)) as SOMME,
                     SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE , 
                     COUNT(IFNULL(b.SERVICE_NO,0)) AS NOMBRE
                FROM 
                   bill b, 
                   contract c ,
        contrat_possede_tags cpt
                WHERE 
                   c.SERVICE_NO=b.SERVICE_NO 
                     and 
                   b.SERVICE_NO IN(
                    SELECT
                        SERVICE_NO
                    FROM
                        contrat_possede_tags
                    WHERE
                        ID_TAGS = '".$_GET['ID_TAGS']."'
                ) AND
                      
                   YEAR(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']." 
                    AND 
                  c.SERVICE_NO=cpt.SERVICE_NO 
                GROUP BY c.SERVICE_NO";
        //   $req=$bdd->query($query);
        // echo $query;
              $req=$bdd->query($query, PDO::FETCH_ASSOC);
    
        while($resultat=$req->fetch()){
            array_push($result,(object)$resultat);
          }
        // echo "<script>console.log( json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK))</script>";
        print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
        $req->closeCursor();  
               }
            }
        }
    }
    }
	   
// if (isset($_GET["getInformationsInstitutionsContractByTutelleAndDate"])) {
//     if ( isset($_GET["ID_TAGS"]) && !empty($_GET["ID_TAGS"])) 
//     {
//         if( $_GET["ID_TAGS"]==='GLOBAL')
//         {
//           if (isset($_GET["TUTELLE"]) && isset($_GET["DISPATCH_DATE"])) 
//            {
//             if(isset($_GET["MONTH"]) && !empty($_GET["MONTH"]))
//             {
//                     $result = array();
//                 $query="SELECT 
//                  b.SERVICE_NO,
//                   b.`AGENCE`,
//                   b.`VOLT_TP_ID`,
//                  b.CUST_NAME, 
//                  SUM(IFNULL(b.CONSUMPTION,0)) as SOMME,
//                  SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE , 
//                  COUNT(IFNULL(b.SERVICE_NO,0)) AS NOMBRE
//                     FROM 
//                bill b, 
//                contract c 
//                 WHERE 
//                c.SERVICE_NO=b.SERVICE_NO 
//                  and 
//                c.TUTELLE='".$_GET['TUTELLE']."' 
//                  AND 
//                YEAR(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
//                 AND MONTH(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y')) IN(".$_GET["MONTH"].")
//                 GROUP BY c.SERVICE_NO";
//               //$req=$bdd->query($query);
             
//               $req=$bdd->query($query, PDO::FETCH_ASSOC);

//             while($resultat=$req->fetch()){
//             array_push($result,(object)$resultat);
//               }
//         // 		echo "<script>console.log( json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK))</script>";
//                 print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
//                 $req->closeCursor();
//             }
//             else
//             {
//                     $result = array();
//                 $query="SELECT 
//                  b.SERVICE_NO,
//                   b.`AGENCE`,
//                   b.`VOLT_TP_ID`,
//                  b.CUST_NAME, 
//                  SUM(IFNULL(b.CONSUMPTION,0)) as SOMME,
//                  SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE , 
//                  COUNT(IFNULL(b.SERVICE_NO,0)) AS NOMBRE
//                     FROM 
//                bill b, 
//                contract c 
//                 WHERE 
//                c.SERVICE_NO=b.SERVICE_NO 
//                  and 
//                c.TUTELLE='".$_GET['TUTELLE']."' 
//                  AND 
//                YEAR(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']." 
//                 GROUP BY c.SERVICE_NO";
//               //$req=$bdd->query($query);
             
//               $req=$bdd->query($query, PDO::FETCH_ASSOC);

//             while($resultat=$req->fetch()){
//             array_push($result,(object)$resultat);
//               }
//         // 		echo "<script>console.log( json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK))</script>";
//                 print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
//                 $req->closeCursor();
//             }
//             }
//         }
//        else if( isset($_GET["DISPATCH_DATE"]))
//         {


//            if(isset($_GET["MONTH"]) && !empty($_GET["MONTH"]))
//            {
//                   $result = array();
//                $query="SELECT 
//                  b.SERVICE_NO,
//                   b.`AGENCE`,
//                   b.`VOLT_TP_ID`,
//                  b.CUST_NAME, 
//                  SUM(IFNULL(b.CONSUMPTION,0)) as SOMME,
//                  SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE , 
//                  COUNT(IFNULL(b.SERVICE_NO,0)) AS NOMBRE
//             FROM 
//                bill b, 
//                contract c ,
//     contrat_possede_tags cpt
//             WHERE 
//                c.SERVICE_NO=b.SERVICE_NO 
//                  and 
//                b.SERVICE_NO IN(
//                 SELECT
//                     SERVICE_NO
//                 FROM
//                     contrat_possede_tags
//                 WHERE
//                     ID_TAGS = '".$_GET['ID_TAGS']."'
//             ) AND
                  
//                YEAR(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']." 
//                 AND MONTH(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y')) IN(".$_GET["MONTH"].") AND 
//               c.SERVICE_NO=cpt.SERVICE_NO 
//             GROUP BY c.SERVICE_NO";
//     //   $req=$bdd->query($query);
//     // echo $query;
//           $req=$bdd->query($query, PDO::FETCH_ASSOC);

//     while($resultat=$req->fetch()){
//         array_push($result,(object)$resultat);
//       }
//     // echo "<script>console.log( json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK))</script>";
//     print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
//     $req->closeCursor();  
//            }
//            else
//            {
//                   $result = array();
//                $query="SELECT 
//                  b.SERVICE_NO,
//                   b.`AGENCE`,
//                   b.`VOLT_TP_ID`,
//                  b.CUST_NAME, 
//                  SUM(IFNULL(b.CONSUMPTION,0)) as SOMME,
//                  SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE , 
//                  COUNT(IFNULL(b.SERVICE_NO,0)) AS NOMBRE
//             FROM 
//                bill b, 
//                contract c ,
//     contrat_possede_tags cpt
//             WHERE 
//                c.SERVICE_NO=b.SERVICE_NO 
//                  and 
//                b.SERVICE_NO IN(
//                 SELECT
//                     SERVICE_NO
//                 FROM
//                     contrat_possede_tags
//                 WHERE
//                     ID_TAGS = '".$_GET['ID_TAGS']."'
//             ) AND
                  
//                YEAR(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']." 
//                 AND 
//               c.SERVICE_NO=cpt.SERVICE_NO 
//             GROUP BY c.SERVICE_NO";
//     //   $req=$bdd->query($query);
//     // echo $query;
//           $req=$bdd->query($query, PDO::FETCH_ASSOC);

//     while($resultat=$req->fetch()){
//         array_push($result,(object)$resultat);
//       }
//     // echo "<script>console.log( json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK))</script>";
//     print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
//     $req->closeCursor();  
//            }
//         }
//     }
   
// }





	function getData($query,$parrams)
	{
		// echo ($query);
		include("connect_bd.php");
		$result=array();
		if(empty($parrams))
		{
		  $req=$bdd->query($query, PDO::FETCH_ASSOC);

			while($resultat=$req->fetch()){
				array_push($result,$resultat);
			  }
		}
		else
		{
		
			$req=$bdd->prepare($query );
            $req->execute($parrams);
			while($resultat=$req->fetch(PDO::FETCH_ASSOC)){
				array_push($result,$resultat);
			  }
		}
		return $result;
	}
	function implode_key($glue, $arr, $key){
		$arr2=array();
		foreach($arr as $f){
			if(!isset($f[$key])) continue;
			$arr2[]=$f[$key];
		}
		return implode($glue, $arr2);
	}
	
	if(isset($_GET['DEMO']))
	{
	  $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://ppsr.eneoapps.com/apiContrat/serverApi.php',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => array('contrat_bills_group' => '200051132,200051374,200051380,200052513,200052514,200052520,200052523,200052849,200053031,200053521,200054584,200054848,200055322,200055323,200057232,200057558,200057566,200057645,200057658,200057664,200057874,200057886,200059997,200061035,200061850,200061930,200061937,200062533,200062632,200062809,200062914,200062989,200063195,200063281,200063286,200063369,200063618,200063710,200063986,200064028,200064083,200064089,200064171,200064287,200064296,200064304,200064352','annees' => '2023','mois' => '01,02'),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;  
	    
	    
	    
// 	    $result=array();
// 	    $query="SELECT DISTINCT `SERVICE_NO` FROM `contract`;";
	 
	   
// 	     $req=$bdd->query($query, PDO::FETCH_ASSOC);
//           $arrayString;
// 			while($resultat=$req->fetch()){
// 				array_push($result,$resultat["SERVICE_NO"]);
// 			  }
// // 		var_dump($req->fetchAll());
//   echo implode(',', $result);
			  
	}

	
	
	
	
	if (isset($_GET["getAllInformationsInstitutions"])) {
	    $result = array();
	    
	    if ( isset($_GET["DISPATCH_DATE"])) 
	    {
	        
	        if($_GET["DISPATCH_DATE"]==2023)
	        {
 
	            if(isset($_GET["MONTH"]))
	            {
        $req=$bdd->query("SELECT SUM(`SOMMES`) as SOMMES,SUM(`PRICE`) as PRICE, NOMBRE,`TUTELLE` FROM `AllInformationsInstitutions_2023` WHERE YEARS=".$_GET['DISPATCH_DATE']." and `MONTH` IN (".$_GET["MONTH"].")  AND PRICE!=0 GROUP BY TUTELLE ORDER BY `PRICE` DESC");
  
	            }
	            else
	            {
	        $req=$bdd->query("SELECT SUM(`SOMMES`) as SOMMES,SUM(`PRICE`) as PRICE, NOMBRE,`TUTELLE` FROM `AllInformationsInstitutions_2023` WHERE YEARS=".$_GET['DISPATCH_DATE']."  AND PRICE!=0 GROUP BY TUTELLE ORDER BY `PRICE` DESC");
		
		     
	            }
	             while($resultat=$req->fetch()){
			array_push($result,array(
			    'TUTELLE' => $resultat["TUTELLE"],
			    'SOMMES' => $resultat["SOMMES"],
			    'NOMBRE' => $resultat["NOMBRE"],
			    'PRICE' => $resultat["PRICE"]
			) );
	     	}
	     	$req->closeCursor();
	        }
	        else
	        {
	            if(isset($_GET["MONTH"]))
	            {
        $req=$bdd->query("SELECT SUM(`SOMMES`) as SOMMES,SUM(`PRICE`) as PRICE, NOMBRE,`TUTELLE` FROM `AllInformationsInstitutions` WHERE YEARS=".$_GET['DISPATCH_DATE']." and `MONTH` IN (".$_GET["MONTH"].") GROUP BY TUTELLE ORDER BY `PRICE` DESC");
  
	            }
	            else
	            {
	       $req=$bdd->query("SELECT SUM(`SOMMES`) as SOMMES,SUM(`PRICE`) as PRICE, NOMBRE,`TUTELLE` FROM `AllInformationsInstitutionsYear` WHERE YEARS=".$_GET['DISPATCH_DATE']." GROUP BY TUTELLE ORDER BY `PRICE` DESC");
		
		     
	            }
	             while($resultat=$req->fetch()){
			array_push($result,array(
			    'TUTELLE' => $resultat["TUTELLE"],
			    'SOMMES' => $resultat["SOMMES"],
			    'NOMBRE' => $resultat["NOMBRE"],
			    'PRICE' => $resultat["PRICE"]
			) );
	     	}
	     	$req->closeCursor();
	         
	        }
	
	    }
	    else
	    {
	        	$req=$bdd->query("SELECT SUM(`SOMMES`) as SOMMES,SUM(`PRICE`) as PRICE, NOMBRE, `TUTELLE` FROM `AllInformationsInstitutions` GROUP BY TUTELLE ORDER BY `PRICE` DESC;");
		
		while($resultat=$req->fetch()){
		    
		   
			array_push($result,array(
			    'TUTELLE' => $resultat["TUTELLE"],
			    'SOMMES' => $resultat["SOMMES"],
			    'NOMBRE' => $resultat["NOMBRE"],
			    'PRICE' => $resultat["PRICE"]
			) );
	     	}
	     	$req->closeCursor();
	    }
	    
		
// 		echo "<script>console.log( json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK))</script>";
		print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
// 		$req->closeCursor();
	}
	

	
	if (isset($_GET["getInformationInstitution"])) {
		$req=$bdd->query("SELECT SERVICE_NO,SUM(CONSUMPTION) AS sommes, COUNT(BILL_NO) AS nombre FROM bill WHERE SERVICE_NO = '".$_GET["getInformationInstitution"]."' GROUP BY SERVICE_NO");
		$resultat=$req->fetch();
		print('('.$resultat['SERVICE_NO'].')'.$_GET['nomInstitution'].'*'.$resultat['nombre'].'@'.$resultat['sommes'].'|');
		$req->closeCursor();
	}
	
	if (isset($_GET["getInstitution"])) {
	    $result = array();
		$req=$bdd->query('SELECT DISTINCT TUTELLE FROM contract');
		$resultat=$req->fetch();
		while($resultat=$req->fetch()) {
		    array_push($result,array(
			    'TUTELLE' => $resultat["TUTELLE"],
			));
		}
		print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
		$req->closeCursor();
	}
	
	if (isset($_GET["getNumeroContratFtInstitution"])) {
	    $result = array();
		$req=$bdd->query('SELECT SERVICE_NO,TUTELLE FROM contract');
		$resultat=$req->fetch();
		while($resultat=$req->fetch()) {
		    array_push($result,array(
			    'TUTELLE' => $resultat["TUTELLE"],
			    'SERVICE_NO' => $resultat["SERVICE_NO"]
			) );
		}
		print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
		$req->closeCursor();
	}
	
	if (isset($_GET["getConsoTotalWitchDate"])) {
		$req=$bdd->query("SELECT DISPATCH_DATE, SUM(CONSUMPTION) AS sommes FROM bill GROUP BY DISPATCH_DATE");
		while($resultat=$req->fetch()) {
			print($resultat['DISPATCH_DATE'].' = '.$resultat['sommes'].';');
		}
		$req->closeCursor();
	}
	
	if (isset($_GET["getConsoTotalWitchYearAndProfil"]) && isset($_GET["profil"])) {
		$req=$bdd->query("SELECT SUM(CONSUMPTION) AS sommes FROM bill WHERE DISPATCH_DATE LIKE '%".$_GET["getConsoTotalWitchYearAndProfil"]."' AND VOLT_TP_ID = '".$_GET["profil"]."'");
		$resultat=$req->fetch();
		print($resultat['sommes']);
		$req->closeCursor();
	}
	
	if (isset($_GET["getMontantWitchYearAndProfil"]) && isset($_GET["profil"])) {
		$req=$bdd->query("SELECT SUM(AMOUNT_WITH_TAX) AS sommes FROM bill WHERE DISPATCH_DATE LIKE '%".$_GET["getMontantWitchYearAndProfil"]."' AND VOLT_TP_ID = '".$_GET["profil"]."'");
		$resultat=$req->fetch();
		print($resultat['sommes']);
		$req->closeCursor();
	}
	
	
	if (isset($_GET["getContractWitchYearAndProfil"]) && isset($_GET["profil"])) {
		$req=$bdd->query("SELECT COUNT(DISTINCT SERVICE_NO) AS nombre FROM bill WHERE DISPATCH_DATE LIKE '%".$_GET["getContractWitchYearAndProfil"]."' AND VOLT_TP_ID = '".$_GET["profil"]."'");
		$resultat=$req->fetch();
		print($resultat['nombre']);
		$req->closeCursor();
	}
	
	if (isset($_GET["nombreFactureActive"])) {
		$req=$bdd->query("SELECT COUNT(DISTINCT SERVICE_NO) AS nombreFactureActive FROM bill WHERE DISPATCH_DATE LIKE '%".$_GET["nombreFactureActive"]."' AND SERVICESTATUS = 'ACTIVE'");
		$resultat=$req->fetch();
		print($resultat['nombreFactureActive']);
		$req->closeCursor();
	}
	
	if (isset($_GET["getConsoTotal"])) {
		$req=$bdd->query("SELECT SUM(CONSUMPTION) AS sommes FROM bill WHERE DISPATCH_DATE LIKE '%".$_GET["getConsoTotal"]."'");
		$resultat=$req->fetch();
		print($resultat['sommes']);
		$req->closeCursor();
	}
	
	if (isset($_GET["getMontantTotal"])) {
		$req=$bdd->query("SELECT SUM(AMOUNT_WITH_TAX) AS sommes FROM bill WHERE DISPATCH_DATE LIKE '%".$_GET["getMontantTotal"]."'");
		$resultat=$req->fetch();
		print($resultat['sommes']);
		$req->closeCursor();
	}
	
	if (isset($_GET["getNombreInstitution"])) {
		$req=$bdd->query('SELECT COUNT(DISTINCT TUTELLE) AS nombre FROM contract');
		$resultat=$req->fetch();
		print($resultat['nombre']);
		$req->closeCursor();
	}
	
	if (isset($_GET["getNombreContrat"])) {
		$req=$bdd->query("SELECT COUNT(DISTINCT SERVICE_NO) AS nombre FROM bill WHERE DISPATCH_DATE LIKE '%".$_GET["getNombreContrat"]."'");
		$resultat=$req->fetch();
		print($resultat['nombre']);
		$req->closeCursor();
	}
	
	if (isset($_GET["getAllContrats"])) {
		$result = array(); $myObj = null;
		$req=$bdd->query('SELECT ID_CONTRAT,UNITE,PROFIL,NEW_REGION,AGENCE,NAMES,CUSTOMER,SERVICE_NO,TYPE_CLIENT,NEW_GESTION,REGROUP_ID,REGROUP_NAME,TOT_KWH,METER_NUMBER,PREMISE_REF,STATUS,RECORD_FLAG,BILLING_ANOM,BILLING_ANOM_STATUS,PHONE_NUMBERS,NATURE_CLIENT,MMS_NUMBER,CREATED_AT FROM contrat');
		while($resultat=$req->fetch()){
			$result[] = array(
			    'ID_CONTRAT' => $resultat["ID_CONTRAT"],
			    'UNITE' => $resultat["UNITE"],
			    'PROFIL' => $resultat["PROFIL"],
			    'NEW_REGION' => $resultat["NEW_REGION"],
			    'AGENCE' => $resultat["AGENCE"],
			    'NAMES' => $resultat["NAMES"],
			    'CUSTOMER' => $resultat["CUSTOMER"],
			    'SERVICE_NO' => $resultat["SERVICE_NO"],
			    'TYPE_CLIENT' => $resultat["TYPE_CLIENT"],
			    'NEW_GESTION' => $resultat["NEW_GESTION"],
			    'REGROUP_ID' => $resultat["REGROUP_ID"],
			    'REGROUP_NAME' => $resultat["REGROUP_NAME"],
			    'TOT_KWH' => $resultat["TOT_KWH"],
			    'METER_NUMBER' => $resultat["METER_NUMBER"],
			    'PREMISE_REF' => $resultat["PREMISE_REF"],
			    'STATUS' => $resultat["STATUS"],
			    'RECORD_FLAG' => $resultat["RECORD_FLAG"],
			    'BILLING_ANOM' => $resultat["BILLING_ANOM"],
			    'BILLING_ANOM_STATUS' => $resultat["BILLING_ANOM_STATUS"],
			    'PHONE_NUMBERS' => $resultat["PHONE_NUMBERS"],
			    'NATURE_CLIENT' => $resultat["NATURE_CLIENT"],
			    'MMS_NUMBER' => $resultat["MMS_NUMBER"],
			    'CREATED_AT' => $resultat["CREATED_AT"]
			);
		}
		print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
		$req->closeCursor();
	}
	
	if (isset($_GET["getAllFactures"])) {
		$result = array(); $myObj = null;
		$req=$bdd->query('SELECT REGION,DIVISION,AGENCE,SERVICE_NO,SERVICESTATUS,CUST_NAME,METER_NUMBER,BILL_NO,DISPATCH_DATE,DUE_DATE,PREV_ACTUAL_READ,HHT_CURRENT_INDEX,CONSUMPTION,FK_CUST_CAT_ID,AMOUNT_WITHOUT_TAX,AMOUNT_WITH_TAX,DUE_AMT,VOLT_TP_ID,BILL_STATUS,PK_CUST_ID FROM facture');
		while($resultat=$req->fetch()){
			$result[] = array(
			    'REGION' => $resultat["REGION"],
			    'DIVISION' => $resultat["DIVISION"],
			    'AGENCE' => $resultat["AGENCE"],
			    'SERVICE_NO' => $resultat["SERVICE_NO"],
			    'SERVICESTATUS' => $resultat["SERVICESTATUS"],
			    'CUST_NAME' => $resultat["CUST_NAME"],
			    'METER_NUMBER' => $resultat["METER_NUMBER"],
			    'BILL_NO' => $resultat["BILL_NO"],
			    'DISPATCH_DATE' => $resultat["DISPATCH_DATE"],
			    'DUE_DATE' => $resultat["DUE_DATE"],
			    'PREV_ACTUAL_READ' => $resultat["PREV_ACTUAL_READ"],
			    'HHT_CURRENT_INDEX' => $resultat["HHT_CURRENT_INDEX"],
			    'CONSUMPTION' => $resultat["CONSUMPTION"],
			    'FK_CUST_CAT_ID' => $resultat["FK_CUST_CAT_ID"],
			    'AMOUNT_WITHOUT_TAX' => $resultat["AMOUNT_WITHOUT_TAX"],
			    'AMOUNT_WITH_TAX' => $resultat["AMOUNT_WITH_TAX"],
			    'DUE_AMT' => $resultat["DUE_AMT"],
			    'VOLT_TP_ID' => $resultat["VOLT_TP_ID"],
			    'BILL_STATUS' => $resultat["BILL_STATUS"],
			    'PK_CUST_ID' => $resultat["PK_CUST_ID"]
			);
		}
		print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
		$req->closeCursor();
	}
		
		if (isset($_GET["getArchive"])) 
		{
    		if(isset($_GET["SERVICE_NO"]))
    		{
    		    	$query="SELECT YEAR(STR_TO_DATE(DISPATCH_DATE, '%m/%d/%Y')) as YEAR,MONTH(STR_TO_DATE(DISPATCH_DATE, '%m/%d/%Y')) as MONTH, `SERVICE_NO`,`CUST_NAME`,`METER_NUMBER`,`BILL_NO`,STR_TO_DATE(DISPATCH_DATE, '%m/%d/%Y') as `DISPATCH_DATE`,STR_TO_DATE(DUE_DATE, '%m/%d/%Y') as `DUE_DATE`,`CONSUMPTION`,`AMOUNT_WITHOUT_TAX`,`VOLT_TP_ID` FROM `bill` WHERE `SERVICE_NO`='".$_GET["SERVICE_NO"]."' ORDER BY `DISPATCH_DATE` ASC";
		print(json_encode(getData($query,[]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
	
    		}
	
     	}

























































































































		 $mois_en_cours = date("m");
		 if(isset($_GET['getTabGlobalInformationsInstitutionsByMONTH']))
		 {
					  $res=array();
					  if(isset($_GET["DISPATCH_DATE"]))
					  {
						  if($_GET["DISPATCH_DATE"]=='2023')
						  {
				  
									  
										  if(isset($_GET["MONTH"]) && !empty($_GET["MONTH"]))
										  {		   
												 if(isset($_GET["ID_TAGS"]) && !empty($_GET["ID_TAGS"]) && $_GET["ID_TAGS"]!="GLOBAL") 
											  {
												$query="SELECT DISTINCT  b.`SERVICE_NO` FROM `final_referentiele` b WHERE  b.SERVICE_NO IN( SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS = '".$_GET["ID_TAGS"]."' ) and YEAR(b.SAVE_DATE)=2023";
												   $queryLV="SELECT DISTINCT  b.`SERVICE_NO` FROM `final_referentiele` b WHERE  b.SERVICE_NO IN( SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS = '".$_GET["ID_TAGS"]."' ) AND b.VOLTAGE = 'LV' and YEAR(b.SAVE_DATE)=2023"; 
												   $queryMV="SELECT DISTINCT  b.`SERVICE_NO` FROM `final_referentiele` b WHERE  b.SERVICE_NO IN( SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS = '".$_GET["ID_TAGS"]."' ) AND b.VOLTAGE = 'MV' and YEAR(b.SAVE_DATE)=2023;";
											  
												  
												   //   var_dump($queryLV);
															 
												  $AllContrat= implode_key(',',getData($query,[]),'SERVICE_NO');
											  
											  
												  $ContratLV= implode_key(',',getData($queryLV,[]),'SERVICE_NO');
											  
											  
												  $ContratMV= implode_key(',',getData($queryMV,[]),'SERVICE_NO');
											  
													//   echo $AllContrat; 
									
									
												  $All =json_decode(postApiInfos('https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php',array('contrat_bills_group' =>$AllContrat ,'annees' => '2023','mois' => format_month($_GET["MONTH"]))));
												  $LV  =json_decode(postApiInfos('https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php',array('contrat_bills_group' =>$ContratLV ,'annees' => '2023','mois' => format_month($_GET["MONTH"]))));
												  $MV  =json_decode(postApiInfos('https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php',array('contrat_bills_group' =>$ContratMV ,'annees' => '2023','mois' => format_month($_GET["MONTH"]))));
						  
								  
												  array_push($res,array(
													  'SOMMES' => $All->total_conso??0,
													  'SOMMESLV' =>$LV->total_conso??0,
													  'SOMMESMV' =>$MV->total_conso??0,
													  'NOMBRE' => (int) count(getData($query,[])) ?? $All->nbr_contrat_facture??0,
													  'NOMBRELV' => (int) count(getData($queryLV,[])) ?? $LV->nbr_contrat_facture??0,
													  'NOMBREML' =>(int) count(getData($queryMV,[])) ??$MV->nbr_contrat_facture??0,
													  'PRICE' => $All->total_facture??0,
													  'PRICEMV' => $MV->total_facture??0,
													  'ACTIF' => $All->contrats_actif??0,
													  'MONTH' => 0,
													  'PRICELV' => $LV->total_facture??0,
												  ));
					  
					                               
												 
												  foreach (explode(",",$_GET["MONTH"]) as $key => $i) 
												  {
					  
													 $query="SELECT DISTINCT  b.`SERVICE_NO` FROM `final_referentiele` b WHERE  b.SERVICE_NO IN( SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS = '".$_GET["ID_TAGS"]."' ) and YEAR(b.SAVE_DATE)=2023 and MONTH(b.SAVE_DATE)=$i";
													 $queryLV="SELECT DISTINCT  b.`SERVICE_NO` FROM `final_referentiele` b WHERE  b.SERVICE_NO IN( SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS = '".$_GET["ID_TAGS"]."' ) AND b.VOLTAGE = 'LV' and YEAR(b.SAVE_DATE)=2023 and MONTH(b.SAVE_DATE)=$i"; 
													 $queryMV="SELECT DISTINCT  b.`SERVICE_NO` FROM `final_referentiele` b WHERE  b.SERVICE_NO IN( SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS = '".$_GET["ID_TAGS"]."' ) AND b.VOLTAGE = 'MV' and YEAR(b.SAVE_DATE)=2023 and MONTH(b.SAVE_DATE)=$i;";
												
													
													 //   var_dump($queryLV);
															   
													$AllContrat= implode_key(',',getData($query,[]),'SERVICE_NO');
												
												
													$ContratLV= implode_key(',',getData($queryLV,[]),'SERVICE_NO');
												
												
													$ContratMV= implode_key(',',getData($queryMV,[]),'SERVICE_NO');
													 
												
													   if($i<=9)	
														  {
															 
															  $All =json_decode(postApiInfos('https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php',array('contrat_bills_group' =>$AllContrat ,'annees' => '2023','mois' => "0".$i)));
														  $LV  =json_decode(postApiInfos('https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php',array('contrat_bills_group' =>$ContratLV ,'annees' => '2023','mois' => "0".$i)));
														  $MV  =json_decode(postApiInfos('https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php',array('contrat_bills_group' =>$ContratMV ,'annees' => '2023','mois' =>  "0".$i)));
											  
														  }
													  else
														  {
																  
														 $All =json_decode(postApiInfos('https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php',array('contrat_bills_group' =>$AllContrat ,'annees' => '2023','mois' => "'.$i.'")));
														  $LV  =json_decode(postApiInfos('https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php',array('contrat_bills_group' =>$ContratLV ,'annees' => '2023','mois' =>  "'.$i.'")));
														  $MV  =json_decode(postApiInfos('https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php',array('contrat_bills_group' =>$ContratMV ,'annees' => '2023','mois' =>  "'.$i.'")));
											  
														  
														  }
			  
																  //var_dump($All);
													   if( intval($mois_en_cours)>=$i)
													  array_push($res,array(
															  'SOMMES' => $All->total_conso??0,
															  'SOMMESLV' =>$LV->total_conso??0,
															  'SOMMESMV' =>$MV->total_conso??0,
															  'NOMBRE' =>(int) $All->nbr_contrat_facture??0,
															  'NOMBRELV' =>(int) $LV->nbr_contrat_facture??0,
															  'NOMBREML' =>(int)$MV->nbr_contrat_facture??0,
															  'PRICE' => (int)$All->total_facture??0,
															  'PRICEMV' =>(int) $MV->total_facture??0,
															  'ACTIF' => (int)$All->contrats_actif??0,
															  'MONTH' =>$i,
															  'PRICELV' =>(int) $LV->total_facture??0,
														  ));
												  } 
					  
													  print(json_encode($res, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
											  }
											  else if((isset($_GET["ID_TAGS"]) && !empty($_GET["ID_TAGS"]) && $_GET["ID_TAGS"]==="GLOBAL") || (!isset($_GET["ID_TAGS"]) && empty($_GET["ID_TAGS"])))
											  {
												   
												  $month=$_GET["MONTH"];
												$query="SELECT * FROM `tabglobalinformationsinstitutionsbymonth2023` WHERE MONTH IN (0,$month)";							
													
										
												
							
															print(json_encode(getData($query,[]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
													
											  }
										  
								  
								  
										  }
										  else
										  {
											  if(isset($_GET["ID_TAGS"]) && !empty($_GET["ID_TAGS"]) && $_GET["ID_TAGS"]!="GLOBAL") 
											  {
			  
												  $query="SELECT DISTINCT  b.`SERVICE_NO` FROM `final_referentiele` b WHERE  b.SERVICE_NO IN( SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS = '".$_GET["ID_TAGS"]."' ) and YEAR(b.SAVE_DATE)=2023";
												  $queryLV="SELECT DISTINCT  b.`SERVICE_NO` FROM `final_referentiele` b WHERE  b.SERVICE_NO IN( SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS = '".$_GET["ID_TAGS"]."' ) AND b.VOLTAGE = 'LV' and YEAR(b.SAVE_DATE)=2023"; 
												  $queryMV="SELECT DISTINCT  b.`SERVICE_NO` FROM `final_referentiele` b WHERE  b.SERVICE_NO IN( SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS = '".$_GET["ID_TAGS"]."' ) AND b.VOLTAGE = 'MV' and YEAR(b.SAVE_DATE)=2023;";
												  
												   //   var_dump($queryLV);
															 
												  $AllContrat= implode_key(',',getData($query,[]),'SERVICE_NO');
											  
											  
												  $ContratLV= implode_key(',',getData($queryLV,[]),'SERVICE_NO');
											  
											  
												  $ContratMV= implode_key(',',getData($queryMV,[]),'SERVICE_NO');
											  
												 //   echo $AllContrat; 
									
									
												  $All =json_decode(postApiInfos('https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php',array('contrat_bills_group' =>$AllContrat ,'annees' => '2023','mois' => '')));
												  $LV  =json_decode(postApiInfos('https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php',array('contrat_bills_group' =>$ContratLV ,'annees' => '2023','mois' => '')));
												  $MV  =json_decode(postApiInfos('https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php',array('contrat_bills_group' =>$ContratMV ,'annees' => '2023','mois' => '')));
						  
								  
												 array_push($res,array(
													  'SOMMES' => $All->total_conso??0,
													  'SOMMESLV' =>$LV->total_conso??0,
													  'SOMMESMV' =>$MV->total_conso??0,
													  'NOMBRE' => $All->nbr_contrat_facture??0,
													  'NOMBRELV' => $LV->nbr_contrat_facture??0,
													  'NOMBREML' =>$MV->nbr_contrat_facture??0,
													  'PRICE' => $All->total_facture??0,
													  'PRICEMV' => $MV->total_facture??0,
													  'ACTIF' => $All->contrats_actif??0,
													  'MONTH' => 0,
													  'PRICELV' => $LV->total_facture??0,
												  ));
					  
					  
												  for($i=1;$i<=12;$i++)
												  {
					  
													 $query="SELECT DISTINCT  b.`SERVICE_NO` FROM `final_referentiele` b WHERE  b.SERVICE_NO IN( SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS = '".$_GET["ID_TAGS"]."' ) and YEAR(b.SAVE_DATE)=2023 and MONTH(b.SAVE_DATE)=$i";
													 $queryLV="SELECT DISTINCT  b.`SERVICE_NO` FROM `final_referentiele` b WHERE  b.SERVICE_NO IN( SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS = '".$_GET["ID_TAGS"]."' ) AND b.VOLTAGE = 'LV' and YEAR(b.SAVE_DATE)=2023 and MONTH(b.SAVE_DATE)=$i"; 
													 $queryMV="SELECT DISTINCT  b.`SERVICE_NO` FROM `final_referentiele` b WHERE  b.SERVICE_NO IN( SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS = '".$_GET["ID_TAGS"]."' ) AND b.VOLTAGE = 'MV' and YEAR(b.SAVE_DATE)=2023 and MONTH(b.SAVE_DATE)=$i;";
													 
													  //   var_dump($queryLV);
																
													 $AllContrat= implode_key(',',getData($query,[]),'SERVICE_NO');
												 
												 
													 $ContratLV= implode_key(',',getData($queryLV,[]),'SERVICE_NO');
												 
												 
													 $ContratMV= implode_key(',',getData($queryMV,[]),'SERVICE_NO');
												 
													//   echo $AllContrat; 
													   if($i<=9)	
														  {
															  $All =json_decode(postApiInfos('https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php',array('contrat_bills_group' =>$AllContrat ,'annees' => '2023','mois' => "0".$i)));
														  $LV  =json_decode(postApiInfos('https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php',array('contrat_bills_group' =>$ContratLV ,'annees' => '2023','mois' => "0".$i)));
														  $MV  =json_decode(postApiInfos('https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php',array('contrat_bills_group' =>$ContratMV ,'annees' => '2023','mois' =>  "0".$i)));
											  
														  }
													  else
														  {
																  
															  $All =json_decode(postApiInfos('https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php',array('contrat_bills_group' =>$AllContrat ,'annees' => '2023','mois' => "'.$i.'")));
														  $LV  =json_decode(postApiInfos('https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php',array('contrat_bills_group' =>$ContratLV ,'annees' => '2023','mois' =>  "'.$i.'")));
														  $MV  =json_decode(postApiInfos('https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php',array('contrat_bills_group' =>$ContratMV ,'annees' => '2023','mois' =>  "'.$i.'")));
											  
														  
														  }
			  
													   // 			var_dump($LV);
														if( intval($mois_en_cours)>=$i)
													  array_push($res,array(
															  'SOMMES' => $All->total_conso??0,
															  'SOMMESLV' =>$LV->total_conso??0,
															  'SOMMESMV' =>$MV->total_conso??0,
															  'NOMBRE' =>(int) $All->nbr_contrat_facture??0,
															  'NOMBRELV' =>(int) $LV->nbr_contrat_facture??0,
															  'NOMBREML' =>(int)$MV->nbr_contrat_facture??0,
															  'PRICE' => (int)$All->total_facture??0,
															  'PRICEMV' =>(int) $MV->total_facture??0,
															  'ACTIF' => (int)$All->contrats_actif??0,
															  'MONTH' =>$i,
															  'PRICELV' =>(int) $LV->total_facture??0,
														  ));
												  } 
					  
													  print(json_encode($res, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
											  }
											  else if((isset($_GET["ID_TAGS"]) && !empty($_GET["ID_TAGS"]) && $_GET["ID_TAGS"]==="GLOBAL") || (!isset($_GET["ID_TAGS"]) && empty($_GET["ID_TAGS"])))
											  {
												  $query="SELECT * FROM `tabglobalinformationsinstitutionsbymonth2023`";

													  print(json_encode(getData($query,[]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
											  }
											   
										  }
						  }
						  else
						  {
							if(isset($_GET["MONTH"]) && !empty($_GET["MONTH"]))
							{	
							  
								if(isset($_GET["ID_TAGS"]) && !empty($_GET["ID_TAGS"]) && $_GET["ID_TAGS"]!="GLOBAL") 
								  {
									  
											  $query="SELECT 
									  SUM(IFNULL(b.CONSUMPTION,0)) as SOMME,
									  SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE, 
									  COUNT(DISTINCT IFNULL(b.`SERVICE_NO`,0)) AS NOMBRE, 
									  lv.SOMMELV,
									  mv.SOMMEMV,
									  lv.PRICELV,
									  mv.PRICEMV,
									  lv.NOMBRELV,
									  mv.NOMBREMV,
									  act.ACTIF,
									  act.MONTHS
								  FROM 
								  bill b,
						  contrat_possede_tags cpt,
								  (SELECT 
										  SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMELV,
										  SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICELV,
										  COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBRELV
									  From 
										  bill bc,
						  contrat_possede_tags cpt
									  WHERE 
									  bc.SERVICE_NO = cpt.SERVICE_NO AND cpt.`ID_TAGS` = '".$_GET["ID_TAGS"]."' AND
									  YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
										  AND bc.VOLT_TP_ID='LV') lv, 
								  (SELECT 
										  SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMEMV,
										  SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICEMV,
										  COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBREMV 
									  From 
										  bill bc,
						  contrat_possede_tags cpt
									  WHERE 
									  bc.SERVICE_NO = cpt.SERVICE_NO AND cpt.`ID_TAGS` = '".$_GET["ID_TAGS"]."' AND
									  YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
										  AND bc.VOLT_TP_ID='MV') mv,
										  (SELECT COUNT(IFNULL(b.`SERVICESTATUS`,0)) as ACTIF,0 as MONTHS  FROM bill b,
						  contrat_possede_tags cpt WHERE  b.SERVICE_NO = cpt.SERVICE_NO AND cpt.`ID_TAGS` = '".$_GET["ID_TAGS"]."' AND b.SERVICESTATUS='ACTIVE' and YEAR(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE'].") act 
									  WHERE
									  b.SERVICE_NO = cpt.SERVICE_NO AND cpt.`ID_TAGS` = '".$_GET["ID_TAGS"]."' AND
								  YEAR(STR_TO_DATE(`DISPATCH_DATE`,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE'];
								  
							  // 	echo $query;
											  $queryT="SELECT 
								 SUM(IFNULL(bc.CONSUMPTION,0)) as SOMME,
								 SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICE,
								 COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBRE 
								 From 
									 bill bc,
				  contrat_possede_tags cpt
								 WHERE 
								  bc.SERVICE_NO = cpt.SERVICE_NO AND cpt.`ID_TAGS` = '".$_GET["ID_TAGS"]."' AND
									 YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET["DISPATCH_DATE"]."
									  GROUP BY MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))"; 
									  
									   $queryMV="SELECT 
								 SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMEMV,
								 SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICEMV,
								 COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBREMV 
								 From 
									 bill bc,
				  contrat_possede_tags cpt
								 WHERE 
								  bc.SERVICE_NO = cpt.SERVICE_NO AND cpt.`ID_TAGS` = '".$_GET["ID_TAGS"]."' AND
									 YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET["DISPATCH_DATE"]."
									 AND bc.VOLT_TP_ID='MV' GROUP BY MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))";
									 
									 
									 $queryLV="SELECT 
									 SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMELV,
									SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICELV,
									COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBRELV
							  From 
								  bill bc,
				  contrat_possede_tags cpt
							  WHERE 
							   bc.SERVICE_NO = cpt.SERVICE_NO AND cpt.`ID_TAGS` = '".$_GET["ID_TAGS"]."' AND
								 YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET["DISPATCH_DATE"]."
								   AND bc.VOLT_TP_ID='LV' GROUP BY MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))";
								   
								   
										 $queryACTIF="SELECT COUNT(IFNULL(b.`SERVICESTATUS`,0)) as ACTIF,MONTH(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y')) as MONTHS FROM bill b , contrat_possede_tags cpt  WHERE  b.SERVICE_NO = cpt.SERVICE_NO AND cpt.`ID_TAGS` = '".$_GET["ID_TAGS"]."' AND b.SERVICESTATUS='ACTIVE' and YEAR(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET["DISPATCH_DATE"]." GROUP BY MONTH(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y'))";
								   
									 $req=$bdd->query($query);
								$resultat=$req->fetch();
						 
							  // var_dump($resultat);
								array_push($res,array(
									 'MONTH' =>(int) $resultat["MONTHS"],
									 'SOMMES' => $resultat["SOMME"],
									 'SOMMESLV' => $resultat["SOMMELV"],
									 'SOMMESMV' => $resultat["SOMMEMV"],
									 'NOMBRE' => $resultat["NOMBRE"],
									 'NOMBRELV' => $resultat["NOMBRELV"],
									 'NOMBREML' => $resultat["NOMBREMV"],
									 'PRICE' => $resultat["PRICE"],
									 'PRICEMV' => $resultat["PRICEMV"],
									 'ACTIF' => $resultat["ACTIF"],
									
									 'PRICELV' => $resultat["PRICELV"]
								));
			  
								$req->closeCursor();
						 
								 
									 $reqT=$bdd->query($queryT, PDO::FETCH_ASSOC);
									  $resultaT=$reqT->fetchAll();
						 
			  
								
									 $reqMV=$bdd->query($queryMV, PDO::FETCH_ASSOC);
									  $resultaMV=$reqMV->fetchAll();
			  
										 $reqLV=$bdd->query($queryLV, PDO::FETCH_ASSOC);
										  $resultaLV=$reqLV->fetchAll();
						 
										 
										 $reqACTIF=$bdd->query($queryACTIF, PDO::FETCH_ASSOC);
										  $resultaACTIF=$reqACTIF->fetchAll();
						// echo $queryLV;
							  // 		 var_dump($resultaLV);  
										  $end=count(explode(",",$_GET["MONTH"]));
										  foreach ($_GET["MONTH"] as $key => $i) 
												  {
											 array_push($res,array(
												 'MONTH' =>($i),
												 'SOMMES' => $resultaT[($i-1)]["SOMME"]??0,
												 'SOMMESLV' =>  $resultaLV[($i-1)]["SOMMELV"]??0,
												 'SOMMESMV' =>  $resultaMV[($i-1)]["SOMMEMV"]??0,
												 'NOMBRE' =>$resultaT[($i-1)]["NOMBRE"]??0,
												 'NOMBRELV' =>  $resultaLV[($i-1)]["NOMBRELV"]??0,
												 'NOMBREML' => $resultaMV[($i-1)]["NOMBREMV"]??0,
												 'PRICE' => $resultaT[($i-1)]["PRICE"]??0,
												 'PRICEMV' => $resultaMV[($i-1)]["PRICEMV"]??0,
												 'ACTIF' =>$resultaACTIF[($i-1)]["ACTIF"]??0,
												 
												 'PRICELV' => $resultaLV[($i-1)]["PRICELV"]??0
											 ));
										  } 
			  
											  print(json_encode($res, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));	
								  }
								  else if((isset($_GET["ID_TAGS"]) && !empty($_GET["ID_TAGS"]) && $_GET["ID_TAGS"]==="GLOBAL") || (!isset($_GET["ID_TAGS"]) && empty($_GET["ID_TAGS"])))
								  {
				
								 $query="SELECT * From GlobalInformationsInstitutions WHERE YEARS=".$_GET["DISPATCH_DATE"]." MONTH IN(".$_GET["MONTH"].")";
								  
									   $query2="SELECT * From TabGlobalInformationsInstitutionsByMONTH WHERE YEARS=".$_GET["DISPATCH_DATE"]." MONTH IN(".$_GET["MONTH"].")";
									   
																   $fullquery="SELECT
											  `YEARS`,
											  0 AS `MONTH`,
											  SUM(`SOMME`) AS SOMME,
											  SUM(`PRICE`) AS PRICE,
											  AVG(`NOMBRE`) AS NOMBRE,
											  SUM(`SOMMESMV`) AS SOMMESMV,
											  SUM(`PRICEMV`) AS PRICEMV,
											  AVG(`NOMBREML`) AS NOMBREML,
											  SUM(`SOMMESLV`) AS SOMMESLV,
											  SUM(`PRICELV`) AS PRICELV,
											  AVG(`NOMBRELV`) AS NOMBRELV,
											  SUM(`ACTIF`) AS ACTIF
										  FROM
											  TabGlobalInformationsInstitutionsByMONTH
										  WHERE
											  YEARS = ".$_GET["DISPATCH_DATE"]." AND MONTH IN(".$_GET["MONTH"].")
										  UNION
										  SELECT
											  *
										  FROM
											  TabGlobalInformationsInstitutionsByMONTH
										  WHERE
											  YEARS = ".$_GET["DISPATCH_DATE"]." AND MONTH IN(".$_GET["MONTH"].");";
									   $reqfull=$bdd->query($fullquery, PDO::FETCH_ASSOC);
										  $resultafull=$reqfull->fetchAll();
					  
										  print(json_encode($resultafull, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
								  }
								 
					  
								   
							}
							else
							{
							  
								  if(isset($_GET["ID_TAGS"]) && !empty($_GET["ID_TAGS"]) && $_GET["ID_TAGS"]!="GLOBAL") 
								  {
																  $query="SELECT 
									  SUM(IFNULL(b.CONSUMPTION,0)) as SOMME,
									  SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE, 
									  COUNT(DISTINCT IFNULL(b.`SERVICE_NO`,0)) AS NOMBRE, 
									  lv.SOMMELV,
									  mv.SOMMEMV,
									  lv.PRICELV,
									  mv.PRICEMV,
									  lv.NOMBRELV,
									  mv.NOMBREMV,
									  act.ACTIF,
									  act.MONTHS
								  FROM 
								  bill b,
						  contrat_possede_tags cpt,
								  (SELECT 
										  SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMELV,
										  SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICELV,
										  COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBRELV
									  From 
										  bill bc,
						  contrat_possede_tags cpt
									  WHERE 
									  bc.SERVICE_NO = cpt.SERVICE_NO AND cpt.`ID_TAGS` = '".$_GET["ID_TAGS"]."' AND
									  YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
										  AND bc.VOLT_TP_ID='LV') lv, 
								  (SELECT 
										  SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMEMV,
										  SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICEMV,
										  COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBREMV 
									  From 
										  bill bc,
						  contrat_possede_tags cpt
									  WHERE 
									  bc.SERVICE_NO = cpt.SERVICE_NO AND cpt.`ID_TAGS` = '".$_GET["ID_TAGS"]."' AND
									  YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
										  AND bc.VOLT_TP_ID='MV') mv,
										  (SELECT COUNT(IFNULL(b.`SERVICESTATUS`,0)) as ACTIF,0 as MONTHS  FROM bill b,
						  contrat_possede_tags cpt WHERE  b.SERVICE_NO = cpt.SERVICE_NO AND cpt.`ID_TAGS` = '".$_GET["ID_TAGS"]."' AND b.SERVICESTATUS='ACTIVE' and YEAR(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE'].") act 
									  WHERE
									  b.SERVICE_NO = cpt.SERVICE_NO AND cpt.`ID_TAGS` = '".$_GET["ID_TAGS"]."' AND
								  YEAR(STR_TO_DATE(`DISPATCH_DATE`,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE'];
								  
						  
								  
								  $queryT="SELECT 
									  SUM(IFNULL(bc.CONSUMPTION,0)) as SOMME,
									  SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICE,
									  COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBRE 
									  From 
										  bill bc,
						  contrat_possede_tags cpt
									  WHERE 
										  bc.SERVICE_NO = cpt.SERVICE_NO AND cpt.`ID_TAGS` = '".$_GET["ID_TAGS"]."' AND
										  YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
											  GROUP BY MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))";
											  
											  $queryMV="SELECT 
									  SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMEMV,
									  SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICEMV,
									  COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBREMV 
									  From 
										  bill bc,
						  contrat_possede_tags cpt
									  WHERE 
										  bc.SERVICE_NO = cpt.SERVICE_NO AND cpt.`ID_TAGS` = '".$_GET["ID_TAGS"]."' AND
										  YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
										  AND bc.VOLT_TP_ID='MV' GROUP BY MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))";
										  
												  $queryLV="SELECT 
										  SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMELV,
										  SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICELV,
										  COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBRELV
									  From 
										  bill bc,
						  contrat_possede_tags cpt
									  WHERE
									  bc.SERVICE_NO = cpt.SERVICE_NO AND cpt.`ID_TAGS` = '".$_GET["ID_TAGS"]."' AND
									  YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
										  AND bc.VOLT_TP_ID='LV' GROUP BY MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))";
										  
																  $queryACTIF="SELECT COUNT(IFNULL(b.`SERVICESTATUS`,0)) as ACTIF ,MONTH(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y')) as MONTHS FROM bill b,
						  contrat_possede_tags cpt WHERE   b.SERVICE_NO = cpt.SERVICE_NO AND cpt.`ID_TAGS` = '".$_GET["ID_TAGS"]."' AND b.SERVICESTATUS='ACTIVE' and YEAR(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']." GROUP BY MONTH(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y'))";
								$req=$bdd->query($query);
								$resultat=$req->fetch();
						 
							  //   var_dump($resultat);
								array_push($res,array(
									 'SOMMES' => $resultat["SOMME"],
									 'SOMMESLV' => $resultat["SOMMELV"],
									 'SOMMESMV' => $resultat["SOMMEMV"],
									 'NOMBRE' => $resultat["NOMBRE"],
									 'NOMBRELV' => $resultat["NOMBRELV"],
									 'NOMBREML' => $resultat["NOMBREMV"],
									 'PRICE' => $resultat["PRICE"],
									 'PRICEMV' => $resultat["PRICEMV"],
									 'ACTIF' => $resultat["ACTIF"],
									 'MONTH' =>(int) $resultat["MONTHS"],
									 'PRICELV' => $resultat["PRICELV"]
								));
			  
								$req->closeCursor();
						 
								 
									 $reqT=$bdd->query($queryT, PDO::FETCH_ASSOC);
									  $resultaT=$reqT->fetchAll();
						 
			  
								
									 $reqMV=$bdd->query($queryMV, PDO::FETCH_ASSOC);
									  $resultaMV=$reqMV->fetchAll();
			  
										 $reqLV=$bdd->query($queryLV, PDO::FETCH_ASSOC);
										  $resultaLV=$reqLV->fetchAll();
						 
										 
										 $reqACTIF=$bdd->query($queryACTIF, PDO::FETCH_ASSOC);
										  $resultaACTIF=$reqACTIF->fetchAll();
						// echo $queryLV;
							  // 		 var_dump($resultaLV);  
										  for($i=0;$i<12;$i++)
										  {
											 array_push($res,array(
												 'SOMMES' => $resultaT[$i]["SOMME"],
												 'SOMMESLV' =>  $resultaLV[$i]["SOMMELV"],
												 'SOMMESMV' =>  $resultaMV[$i]["SOMMEMV"],
												 'NOMBRE' =>$resultaT[$i]["NOMBRE"],
												 'NOMBRELV' =>  $resultaLV[$i]["NOMBRELV"],
												 'NOMBREML' => $resultaMV[$i]["NOMBREMV"],
												 'PRICE' => $resultaT[$i]["PRICE"],
												 'PRICEMV' => $resultaMV[$i]["PRICEMV"],
												 'ACTIF' =>$resultaACTIF[$i]["ACTIF"]??0,
												 'MONTH' =>$resultaACTIF[$i]["MONTHS"]??0,
												 'PRICELV' => $resultaLV[$i]["PRICELV"]
											 ));
										  } 
			  
											  print(json_encode($res, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));	
								  
								  }
								  
								  
								  else if((isset($_GET["ID_TAGS"]) && !empty($_GET["ID_TAGS"]) && $_GET["ID_TAGS"]==="GLOBAL") || (!isset($_GET["ID_TAGS"]) && empty($_GET["ID_TAGS"])))
								  {
									   $query="SELECT * From GlobalInformationsInstitutions WHERE YEARS=".$_GET["DISPATCH_DATE"];
								  
									   $query2="SELECT * From TabGlobalInformationsInstitutionsByMONTH WHERE YEARS=".$_GET["DISPATCH_DATE"];
									   
									   $fullquery="SELECT `YEARS` , 0 as MONTH, `SOMME`,`PRICE`, NOMBRE, SOMMEMV as SOMMESMV, PRICEMV, NOMBREMV as NOMBREML, SOMMELV as SOMMESLV, PRICELV, NOMBRELV, ACTIF FROM `GlobalInformationsInstitutions` WHERE YEARS=2021 UNION SELECT * FROM TabGlobalInformationsInstitutionsByMONTH WHERE YEARS=2021;";
									   $reqfull=$bdd->query($fullquery, PDO::FETCH_ASSOC);
										  $resultafull=$reqfull->fetchAll();
							  // 			echo "null";
										  print(json_encode($resultafull, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
								  }
									
							  }
						  }
					  
						  
					  }
		 }	 










	


		 if (isset($_GET["getInformationsInstitutionsByTutelleAndDate"])) 
			 {
			 
				 if(isset($_GET["DISPATCH_DATE"]) && !empty($_GET["DISPATCH_DATE"]) && $_GET["DISPATCH_DATE"]=='2023')
				 {
					 if((isset($_GET['ID_TAGS'])  && $_GET['ID_TAGS']!='GLOBAL'))
					 {
			 
						 if(isset($_GET["MONTH"]) && !empty($_GET["MONTH"]))
						 {
			 
							 $queryAll="SELECT DISTINCT b.`SERVICE_NO`,b.TUTELLE,(
								 SELECT
									 COUNT(*)
								 FROM
									 (
									 SELECT DISTINCT
										  b.`SERVICE_NO`,
										 b.`STATUS`
									 FROM
										 final_referentiele b
									 WHERE YEAR(b.SAVE_DATE)=2023 and b.SERVICE_NO IN( SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS = '".$_GET["ID_TAGS"]."' )
									 ) AS actif
									 WHERE
									 actif.STATUS = 'ACTIVE'
									 ) AS ACTIF FROM `final_referentiele` b WHERE YEAR(b.SAVE_DATE)=2023 and b.SERVICE_NO IN( SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS = '".$_GET["ID_TAGS"]."' )";
							 $queryLV="SELECT DISTINCT b.`SERVICE_NO` FROM `final_referentiele` b WHERE  b.VOLTAGE = 'LV' and YEAR(b.SAVE_DATE)=2023 and b.SERVICE_NO IN( SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS = '".$_GET["ID_TAGS"]."' )";
							 $queryMV="SELECT DISTINCT b.`SERVICE_NO` FROM `final_referentiele` b WHERE  b.VOLTAGE = 'MV' and YEAR(b.SAVE_DATE)=2023 and b.SERVICE_NO IN( SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS = '".$_GET["ID_TAGS"]."' )";
							 //$datas = array("contrats_group"=>"date","annees"=>2023,"mois"=> format_month($_GET["MONTH"]));
							 // var_dump($queryAll);
							 $params=[];
							 $ResultAll=getData($queryAll,$params);
							 $ResultMV=getData($queryMV,$params);
							 $ResultLV=getData($queryLV,$params);
							 // var_dump();
							 
							 $contrats_groupAll=implode_key(",", $ResultAll,"SERVICE_NO");
							 $datasAll = array("contrat_bills_group"=>$contrats_groupAll,"annees"=>2023,"mois"=> format_month($_GET["MONTH"]));
							 $contrats_groupMV=implode_key(",", $ResultMV,"SERVICE_NO");
							 $datasMV = array("contrat_bills_group"=>$contrats_groupMV,"annees"=>2023,"mois"=> format_month($_GET["MONTH"]));
							 $contrats_groupLV=implode_key(",", $ResultLV,"SERVICE_NO");
							 $datasLV = array("contrat_bills_group"=>$contrats_groupLV,"annees"=>2023,"mois"=> format_month($_GET["MONTH"]));
						   
							 $All=json_decode(postApiInfos("https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php",$datasAll));
							 $MV=json_decode(postApiInfos("https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php",$datasMV));
							 $LV=json_decode(postApiInfos("https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php",$datasLV));
							 print(json_encode(array(
								 'TUTELLE' => $ResultAll[0]["TUTELLE"]??'GLOBAL',
								 'SOMMES' => $All->total_conso??0,
								 'SOMMESLV' =>$LV->total_conso??0,
								 'SOMMESMV' =>$MV->total_conso??0,
								 'NOMBRE' => $All->total_contrat??count($ResultAll),
								 'NOMBRELV' => $LV->total_contrat??count($ResultLV),
								 'NOMBREML' =>$MV->total_contrat??count($ResultMV),
								 'PRICE' => $All->total_facture??0,
								 'PRICEMV' => $MV->total_facture??0,
								 'PRICELV' => $LV->total_facture??0,
								 'ACTIF' => $ResultAll[0]["ACTIF"]??1200
							 ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
						 
						 }
						 else
						 {
							 
							 $queryAll="SELECT DISTINCT b.`SERVICE_NO`,b.TUTELLE,(
								 SELECT
									 COUNT(*)
								 FROM
									 (
									 SELECT DISTINCT
										  b.`SERVICE_NO`,
										 b.`STATUS`
									 FROM
										 final_referentiele b
									 WHERE YEAR(b.SAVE_DATE)=2023 and b.SERVICE_NO IN( SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS = '".$_GET["ID_TAGS"]."' )
									 ) AS actif
									 WHERE
									 actif.STATUS = 'ACTIVE'
									 ) AS ACTIF FROM `final_referentiele` b WHERE YEAR(b.SAVE_DATE)=2023 and b.SERVICE_NO IN( SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS = '".$_GET["ID_TAGS"]."' )";
							 $queryLV="SELECT DISTINCT b.`SERVICE_NO` FROM `final_referentiele` b WHERE  b.VOLTAGE = 'LV' and YEAR(b.SAVE_DATE)=2023 and b.SERVICE_NO IN( SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS = '".$_GET["ID_TAGS"]."' )";
							 $queryMV="SELECT DISTINCT b.`SERVICE_NO` FROM `final_referentiele` b WHERE  b.VOLTAGE = 'MV' and YEAR(b.SAVE_DATE)=2023 and b.SERVICE_NO IN( SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS = '".$_GET["ID_TAGS"]."' )";
								 $datas = array("contrats_group"=>"date","annees"=>2023);
								 // var_dump($queryAll);
								 $params=[];
								 $ResultAll=getData($queryAll,$params);
								 $ResultMV=getData($queryMV,$params);
								 $ResultLV=getData($queryLV,$params);
								 // var_dump();
								 
								 $contrats_groupAll=implode_key(",", $ResultAll,"SERVICE_NO");
								 $datasAll = array("contrat_bills_group"=>$contrats_groupAll,"annees"=>2023,"mois"=> '');
								 $contrats_groupMV=implode_key(",", $ResultMV,"SERVICE_NO");
								 $datasMV = array("contrat_bills_group"=>$contrats_groupMV,"annees"=>2023,"mois"=> '');
								 $contrats_groupLV=implode_key(",", $ResultLV,"SERVICE_NO");
								 $datasLV = array("contrat_bills_group"=>$contrats_groupLV,"annees"=>2023,"mois"=> '');
							 
								 $All=json_decode(postApiInfos("https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php",$datasAll));
								 $MV=json_decode(postApiInfos("https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php",$datasMV));
								 $LV=json_decode(postApiInfos("https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php",$datasLV));
								 print(json_encode(array(
									 'TUTELLE' => $ResultAll[0]["TUTELLE"]??'GLOBAL',
									 'SOMMES' => $All->total_conso??0,
									 'SOMMESLV' =>$LV->total_conso??0,
									 'SOMMESMV' =>$MV->total_conso??0,
									 'NOMBRE' => $All->total_contrat??count($ResultAll),
									 'NOMBRELV' => $LV->total_contrat??count($ResultLV),
									 'NOMBREML' =>$MV->total_contrat??count($ResultMV),
									 'PRICE' => $All->total_facture??0,
									 'PRICEMV' => $MV->total_facture??0,
									 'PRICELV' => $LV->total_facture??0,
									 'ACTIF' => $ResultAll[0]["ACTIF"]??1200
								 ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
							 
						 }
					 }
					 else if((isset($_GET['ID_TAGS']) && !empty($_GET['ID_TAGS']) && $_GET['ID_TAGS']==='GLOBAL') || (!isset($_GET["MONTH"]) && empty($_GET["MONTH"])) )
					 {
						 if(isset($_GET["MONTH"]) && !empty($_GET["MONTH"]) && count(explode(",", $_GET["MONTH"]))<12)
						 {
							 $queryAll="SELECT DISTINCT  b.`SERVICE_NO`,b.TUTELLE,		(
								 SELECT
									 COUNT(*)
								 FROM
									 (
									 SELECT DISTINCT
										 b.`SERVICE_NO`,
										 b.`STATUS`
									 FROM
										 final_referentiele b
									 WHERE
									  YEAR(b.SAVE_DATE)=2023
									 ) AS actif
									 WHERE
									 actif.STATUS = 'ACTIVE'
									 ) AS ACTIF FROM `final_referentiele` b WHERE YEAR(b.SAVE_DATE)=2023 ";
							 $queryLV="SELECT DISTINCT  b.`SERVICE_NO`,b.TUTELLE FROM `final_referentiele` b WHERE b.VOLTAGE = 'LV' and YEAR(b.SAVE_DATE)=2023 ";
							 $queryMV="SELECT DISTINCT  b.`SERVICE_NO`,b.TUTELLE FROM `final_referentiele` b WHERE  b.VOLTAGE = 'MV' and YEAR(b.SAVE_DATE)=2023 ";
							 $params=[':ID_TAGS' => $_GET['ID_TAGS']];
							 // $datas = array("contrats_group"=>"date","annees"=>2023);
							 
							 $ResultAll=getData($queryAll,$params);
							 $ResultMV=getData($queryMV,$params);
							 $ResultLV=getData($queryLV,$params);
							 // var_dump();
							 $tab=explode(",", $_GET["MONTH"]);
							 $contrats_groupAll=implode_key(",", $ResultAll,"SERVICE_NO");
							 $datasAll = array("contrat_bills_group"=>$contrats_groupAll,"annees"=>2023,"mois"=>format_month($_GET["MONTH"]));
							 $contrats_groupMV=implode_key(",", $ResultMV,"SERVICE_NO");
							 $datasMV = array("contrat_bills_group"=>$contrats_groupMV,"annees"=>2023,"mois"=>format_month($_GET["MONTH"]));
							 $contrats_groupLV=implode_key(",", $ResultLV,"SERVICE_NO");
							 $datasLV = array("contrat_bills_group"=>$contrats_groupLV,"annees"=>2023,"mois"=>format_month($_GET["MONTH"]));
							  // 	var_dump( $datasLV);
							  $All=json_decode(postApiInfos("https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php",$datasAll));
							  $MV=json_decode(postApiInfos("https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php",$datasMV));
							 $LV=json_decode(postApiInfos("https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php",$datasLV));
							// 	// 
							 // 	echo "<script>console.log($contrats_groupAll)</script>";
							// 	print_r($All);
							 print(json_encode(array(
								 'TUTELLE' => $ResultAll[0]["TUTELLE"]??'',
								 'SOMMES' => $All->total_conso??0,
								 'SOMMESLV' =>$LV->total_conso??0,
								 'SOMMESMV' =>$MV->total_conso??0,
								 'NOMBRE' => $All->total_contrat??count($ResultAll),
								 'NOMBRELV' => $LV->total_contrat??count($ResultLV),
								 'NOMBREML' =>$MV->total_contrat??count($ResultMV),
								 'PRICE' => $All->total_facture??0,
								 'PRICEMV' => $MV->total_facture??0,
								 'PRICELV' => $LV->total_facture??0,
								 'ACTIF' => $ResultAll[0]["ACTIF"]??1200
							 ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
							// $req->closeCursor();
						 }
						 else
						 {
							 $queryAll="SELECT DISTINCT  b.`SERVICE_NO`,b.TUTELLE,		(
								 SELECT
									 COUNT(*)
								 FROM
									 (
									 SELECT DISTINCT
										 b.`SERVICE_NO`,
										 b.`STATUS`
									 FROM
										 final_referentiele b
									 WHERE
									  YEAR(b.SAVE_DATE)=2023 
									 ) AS actif
									 WHERE
									 actif.STATUS = 'ACTIVE'
									 ) AS ACTIF FROM `final_referentiele` b WHERE YEAR(b.SAVE_DATE)=2023 
									 ;";
							 $queryLV="SELECT DISTINCT  b.`SERVICE_NO`,b.TUTELLE FROM `final_referentiele` b WHERE b.VOLTAGE = 'LV' and YEAR(b.SAVE_DATE)=2023;";
							 $queryMV="SELECT DISTINCT  b.`SERVICE_NO`,b.TUTELLE FROM `final_referentiele` b WHERE b.VOLTAGE = 'MV' and YEAR(b.SAVE_DATE)=2023;";
							 $params=[':ID_TAGS' => $_GET['ID_TAGS']];
							 // $datas = array("contrats_group"=>"date","annees"=>2023);
							 
							 $ResultAll=getData($queryAll,$params);
							 $ResultMV=getData($queryMV,$params);
							 $ResultLV=getData($queryLV,$params);
							 // var_dump();
							 
							 $contrats_groupAll=implode_key(",", $ResultAll,"SERVICE_NO");
							 $datasAll = array("contrat_bills_group"=>$contrats_groupAll,"annees"=>2023);
							 $contrats_groupMV=implode_key(",", $ResultMV,"SERVICE_NO");
							 $datasMV = array("contrat_bills_group"=>$contrats_groupMV,"annees"=>2023);
							 $contrats_groupLV=implode_key(",", $ResultLV,"SERVICE_NO");
							 $datasLV = array("contrat_bills_group"=>$contrats_groupLV,"annees"=>2023);
							  // 	var_dump( $datasLV);
							  $All=json_decode(postApiInfos("https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php",$datasAll));
							  $MV=json_decode(postApiInfos("https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php",$datasMV));
							 $LV=json_decode(postApiInfos("https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php",$datasLV));
							// 	// 
							 // 	echo "<script>console.log($contrats_groupAll)</script>";
							// 	print_r($All);
							 print(json_encode(array(
								 'TUTELLE' => $ResultAll[0]["TUTELLE"]??'',
								 'SOMMES' => $All->total_conso??0,
								 'SOMMESLV' =>$LV->total_conso??0,
								 'SOMMESMV' =>$MV->total_conso??0,
								 'NOMBRE' => $All->total_contrat??count($ResultAll),
								 'NOMBRELV' => $LV->total_contrat??count($ResultLV),
								 'NOMBREML' =>$MV->total_contrat??count($ResultMV),
								 'PRICE' => $All->total_facture??0,
								 'PRICEMV' => $MV->total_facture??0,
								 'PRICELV' => $LV->total_facture??0,
								 'ACTIF' => $ResultAll[0]["ACTIF"]??1200
							 ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
							// $req->closeCursor();
						 }
						 
					 
					 }
					 else if ((isset($_GET["TUTELLE"]) and !empty($_GET["TUTELLE"])))
					 {
						 if(isset($_GET["MONTH"]) && !empty($_GET["MONTH"]))
						 {
							 $queryAll="SELECT DISTINCT  b.`SERVICE_NO`,b.TUTELLE,(
								 SELECT
									 COUNT(*)
								 FROM
									 (
									 SELECT DISTINCT
										 b.`SERVICE_NO`,
										 b.`STATUS`
									 FROM
										 
									 final_referentiele b
									 WHERE
									 b.TUTELLE='".$_GET['TUTELLE']."' and YEAR(b.SAVE_DATE)=2023
										 ) AS actif
									 WHERE
										 actif.STATUS = 'ACTIVE'
									 ) AS ACTIF
									 
									 FROM `final_referentiele` b WHERE  b.TUTELLE=:TUTELLE and YEAR(b.SAVE_DATE)=2023;";
							 $queryLV="SELECT DISTINCT  b.`SERVICE_NO`,b.TUTELLE FROM `final_referentiele` b WHERE  b.TUTELLE=:TUTELLE AND b.VOLTAGE  = 'LV' and YEAR(b.SAVE_DATE)=2023;";
							 $queryMV="SELECT DISTINCT  b.`SERVICE_NO`,b.TUTELLE FROM `final_referentiele` b WHERE  b.TUTELLE=:TUTELLE AND b.VOLTAGE  = 'MV' and YEAR(b.SAVE_DATE)=2023;";
							 $params=[':TUTELLE' => $_GET['TUTELLE']];
							 // $datas = array("contrats_group"=>"date","annees"=>2023);
							 
							 $ResultAll=getData($queryAll,$params);
							 $ResultMV=getData($queryMV,$params);
							 $ResultLV=getData($queryLV,$params);
							 // var_dump();
							 
							 $contrats_groupAll=implode_key(",", $ResultAll,"SERVICE_NO");
							 $datasAll = array("contrat_bills_group"=>$contrats_groupAll,"annees"=>2023,"mois"=>format_month($_GET["MONTH"]));
							 $contrats_groupMV=implode_key(",", $ResultMV,"SERVICE_NO");
							 $datasMV = array("contrat_bills_group"=>$contrats_groupMV,"annees"=>2023,"mois"=>format_month($_GET["MONTH"]));
							 $contrats_groupLV=implode_key(",", $ResultLV,"SERVICE_NO");
							 $datasLV = array("contrat_bills_group"=>$contrats_groupLV,"annees"=>2023,"mois"=>format_month($_GET["MONTH"]));
							 // var_dump( $datasLV);
							 $All=json_decode(postApiInfos("https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php",$datasAll));
							 $MV=json_decode(postApiInfos("https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php",$datasMV));
							 $LV=json_decode(postApiInfos("https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php",$datasLV));
							 // 
							 // echo "<script>console.log($contrats_groupAll)</script>";
							 // print_r($contrats_groupAll);
							 print(json_encode(array(
								 'TUTELLE' => $ResultAll[0]["TUTELLE"]??'',
								 'SOMMES' => $All->total_conso??0,
								 'SOMMESLV' =>$LV->total_conso??0,
								 'SOMMESMV' =>$MV->total_conso??0,
								 'NOMBRE' => $All->total_contrat??count($ResultAll),
								 'NOMBRELV' => $LV->total_contrat??count($ResultLV),
								 'NOMBREML' =>$MV->total_contrat??count($ResultMV),
								 'PRICE' => $All->total_facture??0,
								 'PRICEMV' => $MV->total_facture??0,
								 'PRICELV' => $LV->total_facture??0,
								 'ACTIF' => $ResultAll[0]["ACTIF"]??1200
							 ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
						 }
						 else
						 {
							 $queryAll="SELECT DISTINCT  b.`SERVICE_NO`,b.TUTELLE,		 (
								 SELECT
									 COUNT(*)
								 FROM
									 (
									 SELECT DISTINCT
										 b.`SERVICE_NO`,
										 b.`STATUS`
									 FROM
										 
									 final_referentiele b
									 WHERE
									 b.TUTELLE='".$_GET['TUTELLE']."' and YEAR(b.SAVE_DATE)=2023
										 ) AS actif
									 WHERE
										 actif.STATUS = 'ACTIVE'
									 ) AS ACTIF
									 
									 FROM `final_referentiele` b WHERE  b.TUTELLE=:TUTELLE and YEAR(b.SAVE_DATE)=2023;";
							 $queryLV="SELECT DISTINCT  b.`SERVICE_NO`,b.TUTELLE FROM `final_referentiele` b WHERE  b.TUTELLE=:TUTELLE AND b.VOLTAGE  = 'LV' and YEAR(b.SAVE_DATE)=2023;";
							 $queryMV="SELECT DISTINCT  b.`SERVICE_NO`,b.TUTELLE FROM `final_referentiele` b WHERE  b.TUTELLE=:TUTELLE AND b.VOLTAGE  = 'MV' and YEAR(b.SAVE_DATE)=2023;";
							 $params=[':TUTELLE' => $_GET['TUTELLE']];
							 // $datas = array("contrats_group"=>"date","annees"=>2023);
							 
							 $ResultAll=getData($queryAll,$params);
							 $ResultMV=getData($queryMV,$params);
							 $ResultLV=getData($queryLV,$params);
							 // var_dump();
							 
							 $contrats_groupAll=implode_key(",", $ResultAll,"SERVICE_NO");
							 $datasAll = array("contrat_bills_group"=>$contrats_groupAll,"annees"=>2023);
							 $contrats_groupMV=implode_key(",", $ResultMV,"SERVICE_NO");
							 $datasMV = array("contrat_bills_group"=>$contrats_groupMV,"annees"=>2023);
							 $contrats_groupLV=implode_key(",", $ResultLV,"SERVICE_NO");
							 $datasLV = array("contrat_bills_group"=>$contrats_groupLV,"annees"=>2023);
							 // var_dump( $datasLV);
							 $All=json_decode(postApiInfos("https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php",$datasAll));
							 $MV=json_decode(postApiInfos("https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php",$datasMV));
							 $LV=json_decode(postApiInfos("https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php",$datasLV));
							 // 
							 // echo "<script>console.log($contrats_groupAll)</script>";
							 // print_r($contrats_groupAll);
							 print(json_encode(array(
								 'TUTELLE' => $ResultAll[0]["TUTELLE"]??'',
								 'SOMMES' => $All->total_conso??0,
								 'SOMMESLV' =>$LV->total_conso??0,
								 'SOMMESMV' =>$MV->total_conso??0,
								 'NOMBRE' => $All->total_contrat??count($ResultAll),
								 'NOMBRELV' => $LV->total_contrat??count($ResultLV),
								 'NOMBREML' =>$MV->total_contrat??count($ResultMV),
								 'PRICE' => $All->total_facture??0,
								 'PRICEMV' => $MV->total_facture??0,
								 'PRICELV' => $LV->total_facture??0,
								 'ACTIF' => $ResultAll[0]["ACTIF"]??1200
							 ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
						 }
					 
					 }
					 
				 }
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
				 else if((isset($_GET['ID_TAGS'])  && $_GET['ID_TAGS']!='GLOBAL') && isset($_GET["DISPATCH_DATE"]) )
				 {
					 if(isset($_GET["MONTH"]) && !empty($_GET["MONTH"]))
					 {
						 
											 $query="SELECT
											 SUM(IFNULL(b.CONSUMPTION, 0)) AS SOMME,
											 SUM(IFNULL(b.AMOUNT_WITH_TAX, 0)) AS PRICE,
											 COUNT(DISTINCT b.`SERVICE_NO`) AS NOMBRE,
											 lv.SOMMELV,
											 mv.SOMMEMV,
											 lv.PRICELV,
											 mv.PRICEMV,
											 lv.NOMBRELV,
											 mv.NOMBREMV,
											 c.TUTELLE,
											 (
									 SELECT
										 COUNT(*)
									 FROM
										 (
										 SELECT DISTINCT
											 c.`SERVICE_NO`,
											 c.`STATUS`
										 FROM
											 `contract` c,
											 bill b
										 WHERE
											 c.SERVICE_NO = b.SERVICE_NO AND b.SERVICE_NO IN(
											 SELECT
												 SERVICE_NO
											 FROM
												 contrat_possede_tags
											 WHERE
												 ID_TAGS = '".$_GET['ID_TAGS']."'
										 ) AND YEAR(
											 STR_TO_DATE(b.DISPATCH_DATE, '%m/%d/%Y')
										 ) = ".$_GET['DISPATCH_DATE']."  AND 
										 MONTH(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y')) IN(".$_GET['MONTH'].")
										 ) AS actif
										 WHERE
										 actif.STATUS = 'ACTIVE'
										 ) AS ACTIF	
											 FROM
											 bill b,
											 contract c,
											 (
											 SELECT
												 SUM(IFNULL(bc.CONSUMPTION, 0)) AS SOMMELV,
												 SUM(IFNULL(bc.AMOUNT_WITH_TAX, 0)) AS PRICELV,
												 COUNT(DISTINCT bc.`SERVICE_NO`) AS NOMBRELV
											 FROM
												 bill bc
											 WHERE
												 bc.SERVICE_NO IN(
												 SELECT
													 SERVICE_NO
												 FROM
													 contrat_possede_tags
												 WHERE
													 ID_TAGS = '".$_GET['ID_TAGS']."'
											 ) AND bc.VOLT_TP_ID = 'LV' AND YEAR(
												 STR_TO_DATE(bc.DISPATCH_DATE, '%m/%d/%Y')
											 ) = '".$_GET['DISPATCH_DATE']."'  AND 
											 MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y')) IN(".$_GET['MONTH'].")
										 ) lv,
										 (
											 SELECT
												 SUM(IFNULL(bc.CONSUMPTION, 0)) AS SOMMEMV,
												 SUM(IFNULL(bc.AMOUNT_WITH_TAX, 0)) AS PRICEMV,
												 COUNT(DISTINCT bc.`SERVICE_NO`) AS NOMBREMV
											 FROM
												 bill bc
											 WHERE
												 bc.SERVICE_NO IN(
												 SELECT
													 SERVICE_NO
												 FROM
													 contrat_possede_tags
												 WHERE
													 ID_TAGS = '".$_GET['ID_TAGS']."'
											 ) AND bc.VOLT_TP_ID = 'MV' AND YEAR(
												 STR_TO_DATE(bc.DISPATCH_DATE, '%m/%d/%Y')
											 ) = '".$_GET['DISPATCH_DATE']."'  AND 
											 MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y')) IN(".$_GET['MONTH'].")
										 ) mv
										 WHERE
										 c.SERVICE_NO = b.SERVICE_NO AND 
											 b.SERVICE_NO IN(
											 SELECT
												 SERVICE_NO
											 FROM
												 contrat_possede_tags
											 WHERE
												 ID_TAGS = '".$_GET['ID_TAGS']."'
										 ) AND YEAR(
											 STR_TO_DATE(b.DISPATCH_DATE, '%m/%d/%Y')
										 ) = '".$_GET['DISPATCH_DATE']."'  AND 
										 MONTH(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y')) IN(".$_GET['MONTH'].")";
			 
					 
				   
						 
								 $req=$bdd->query($query);
								 $resultat=$req->fetch();
								 print(json_encode(array(
										 'TUTELLE' => $resultat["TUTELLE"],
										 'SOMMES' => $resultat["SOMME"],
										 'SOMMESLV' => $resultat["SOMMELV"],
										 'SOMMESMV' => $resultat["SOMMEMV"],
										 'NOMBRE' => $resultat["NOMBRE"],
										 'NOMBRELV' => $resultat["NOMBRELV"],
										 'NOMBREML' => $resultat["NOMBREMV"],
										 'PRICE' => $resultat["PRICE"],
										 'ACTIF' => $resultat["ACTIF"],
										 'PRICEMV' => $resultat["PRICEMV"],
										 'PRICELV' => $resultat["PRICELV"]
									 ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
								 $req->closeCursor();
								 
					 }
					 else
					 {
						 $query="SELECT
						 SUM(IFNULL(b.CONSUMPTION, 0)) AS SOMME,
						 SUM(IFNULL(b.AMOUNT_WITH_TAX, 0)) AS PRICE,
						 COUNT(DISTINCT b.`SERVICE_NO`) AS NOMBRE,
						 lv.SOMMELV,
						 mv.SOMMEMV,
						 lv.PRICELV,
						 mv.PRICEMV,
						 lv.NOMBRELV,
						 mv.NOMBREMV,
						 c.TUTELLE,
						 (
							 SELECT
								 COUNT(*)
							 FROM
								 (
								 SELECT DISTINCT
									 c.`SERVICE_NO`,
									 c.`STATUS`
								 FROM
									 `contract` c,
									 bill b
								 WHERE
									 c.SERVICE_NO = b.SERVICE_NO AND b.SERVICE_NO IN(
									 SELECT
										 SERVICE_NO
									 FROM
										 contrat_possede_tags
									 WHERE
										 ID_TAGS = '".$_GET['ID_TAGS']."'
								 ) AND YEAR(
									 STR_TO_DATE(b.DISPATCH_DATE, '%m/%d/%Y')
								 ) = ".$_GET['DISPATCH_DATE']."
								 ) AS actif
								 WHERE
								 actif.STATUS = 'ACTIVE'
								 ) AS ACTIF	
									 FROM
									 bill b,
									 contract c,
									 (
									 SELECT
										 SUM(IFNULL(bc.CONSUMPTION, 0)) AS SOMMELV,
										 SUM(IFNULL(bc.AMOUNT_WITH_TAX, 0)) AS PRICELV,
										 COUNT(DISTINCT bc.`SERVICE_NO`) AS NOMBRELV
									 FROM
										 bill bc
									 WHERE
										 bc.SERVICE_NO IN(
										 SELECT
											 SERVICE_NO
										 FROM
											 contrat_possede_tags
										 WHERE
											 ID_TAGS = '".$_GET['ID_TAGS']."'
									 ) AND bc.VOLT_TP_ID = 'LV' AND YEAR(
										 STR_TO_DATE(bc.DISPATCH_DATE, '%m/%d/%Y')
									 ) = '".$_GET['DISPATCH_DATE']."'
								 ) lv,
								 (
									 SELECT
										 SUM(IFNULL(bc.CONSUMPTION, 0)) AS SOMMEMV,
										 SUM(IFNULL(bc.AMOUNT_WITH_TAX, 0)) AS PRICEMV,
										 COUNT(DISTINCT bc.`SERVICE_NO`) AS NOMBREMV
									 FROM
										 bill bc
									 WHERE
										 bc.SERVICE_NO IN(
										 SELECT
											 SERVICE_NO
										 FROM
											 contrat_possede_tags
										 WHERE
											 ID_TAGS = '".$_GET['ID_TAGS']."'
									 ) AND bc.VOLT_TP_ID = 'MV' AND YEAR(
										 STR_TO_DATE(bc.DISPATCH_DATE, '%m/%d/%Y')
									 ) = '".$_GET['DISPATCH_DATE']."'
								 ) mv
								 WHERE
								 c.SERVICE_NO = b.SERVICE_NO AND 
									 b.SERVICE_NO IN(
									 SELECT
										 SERVICE_NO
									 FROM
										 contrat_possede_tags
									 WHERE
										 ID_TAGS = '".$_GET['ID_TAGS']."'
								 ) AND YEAR(
									 STR_TO_DATE(b.DISPATCH_DATE, '%m/%d/%Y')
								 ) = '".$_GET['DISPATCH_DATE']."'";
						 
					 
				   
						 
								 $req=$bdd->query($query);
								 $resultat=$req->fetch();
								 print(json_encode(array(
										 'TUTELLE' => $resultat["TUTELLE"],
										 'SOMMES' => $resultat["SOMME"],
										 'SOMMESLV' => $resultat["SOMMELV"],
										 'SOMMESMV' => $resultat["SOMMEMV"],
										 'NOMBRE' => $resultat["NOMBRE"],
										 'NOMBRELV' => $resultat["NOMBRELV"],
										 'NOMBREML' => $resultat["NOMBREMV"],
										 'PRICE' => $resultat["PRICE"],
										 'ACTIF' => $resultat["ACTIF"],
										 'PRICEMV' => $resultat["PRICEMV"],
										 'PRICELV' => $resultat["PRICELV"]
									 ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
								 $req->closeCursor();
					 }
				 }
				  else if ((isset($_GET["TUTELLE"]) and !empty($_GET["TUTELLE"])) && isset($_GET["DISPATCH_DATE"])) 
				 {
			 
					 if(isset($_GET["MONTH"]) && !empty($_GET["MONTH"]))
					 {
										 $query="SELECT 
										 SUM(IFNULL(b.CONSUMPTION,0)) as SOMME,
										 SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE, 
										 COUNT( DISTINCT b.`SERVICE_NO`) AS NOMBRE, 
										 lv.SOMMELV,
										 mv.SOMMEMV,
										 lv.PRICELV,
										 mv.PRICEMV,
										 lv.NOMBRELV,
										 mv.NOMBREMV,
										 c.TUTELLE,
										 
										 (
									 SELECT
										 COUNT(*)
									 FROM
										 (
										 SELECT DISTINCT
											 c.`SERVICE_NO`,
											 c.`STATUS`
										 FROM
											 `contract` c,
											 bill b
										 WHERE
											 c.SERVICE_NO = b.SERVICE_NO  AND 
										 c.TUTELLE='".$_GET['TUTELLE']."'
											 AND YEAR(
											 STR_TO_DATE(b.DISPATCH_DATE, '%m/%d/%Y')
										 ) = ".$_GET['DISPATCH_DATE']."
											 ) AS actif
										 WHERE
											 actif.STATUS = 'ACTIVE'
										 ) AS ACTIF
										 FROM 
									 bill b, 
									 contract c,
									 (SELECT 
											 SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMELV,
											 SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICELV,
											 COUNT(DISTINCT bc.`SERVICE_NO`) as NOMBRELV
										 From 
											 bill bc, 
										 contract ct 
										 WHERE 
										 ct.SERVICE_NO=bc.SERVICE_NO 
											 AND 
										 ct.TUTELLE='".$_GET['TUTELLE']."' 
											 AND 
										 YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']." 
										 AND 
										 MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y')) IN(".$_GET['MONTH'].")
											 AND bc.VOLT_TP_ID='LV') lv, 
									 (SELECT 
											 SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMEMV,
											 SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICEMV,
											 COUNT(DISTINCT bc.`SERVICE_NO`) as NOMBREMV 
										 From 
											 bill bc, 
										 contract ct 
										 WHERE 
										 ct.SERVICE_NO=bc.SERVICE_NO 
											 AND 
										 ct.TUTELLE='".$_GET['TUTELLE']."' 
											 AND 
										 YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
										 AND 
										 MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y')) IN(".$_GET['MONTH'].")
											 AND bc.VOLT_TP_ID='MV') mv
								 WHERE 
									 c.SERVICE_NO=b.SERVICE_NO 
										 AND 
									 c.TUTELLE='".$_GET['TUTELLE']."' 
										 AND 
									 YEAR(STR_TO_DATE(`DISPATCH_DATE`,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
									 AND 
										 MONTH(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y')) IN(".$_GET['MONTH'].")";
								 //    echo $query;
										 $req=$bdd->query($query);
										 $resultat=$req->fetch();
										 print(json_encode(array(
												 'TUTELLE' => $resultat["TUTELLE"],
												 'SOMMES' => $resultat["SOMME"],
												 'SOMMESLV' => $resultat["SOMMELV"],
												 'SOMMESMV' => $resultat["SOMMEMV"],
												 'NOMBRE' => $resultat["NOMBRE"],
												 'NOMBRELV' => $resultat["NOMBRELV"],
												 'NOMBREML' => $resultat["NOMBREMV"],
												 'PRICE' => $resultat["PRICE"],
													 'ACTIF' => $resultat["ACTIF"],
												 'PRICEMV' => $resultat["PRICEMV"],
												 'PRICELV' => $resultat["PRICELV"]
											 ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
										 $req->closeCursor();
					 }
					 else
					 {
						 $query="SELECT 
						 SUM(IFNULL(b.CONSUMPTION,0)) as SOMME,
						 SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE, 
						 COUNT( DISTINCT b.`SERVICE_NO`) AS NOMBRE, 
						 lv.SOMMELV,
						 mv.SOMMEMV,
						 lv.PRICELV,
						 mv.PRICEMV,
						 lv.NOMBRELV,
						 mv.NOMBREMV,
						 c.TUTELLE,
						 
						 (
						SELECT
							COUNT(*)
						FROM
							(
							SELECT DISTINCT
								c.`SERVICE_NO`,
								c.`STATUS`
							FROM
								`contract` c,
								bill b
							WHERE
								c.SERVICE_NO = b.SERVICE_NO  AND 
							c.TUTELLE='".$_GET['TUTELLE']."'
							 AND YEAR(
								STR_TO_DATE(b.DISPATCH_DATE, '%m/%d/%Y')
							) = ".$_GET['DISPATCH_DATE']."
								) AS actif
							WHERE
								actif.STATUS = 'ACTIVE'
							) AS ACTIF
							FROM 
					   bill b, 
					   contract c,
					   (SELECT 
								SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMELV,
							   SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICELV,
							   COUNT(DISTINCT bc.`SERVICE_NO`) as NOMBRELV
						 From 
							 bill bc, 
							contract ct 
						 WHERE 
							ct.SERVICE_NO=bc.SERVICE_NO 
							  AND 
							ct.TUTELLE='".$_GET['TUTELLE']."' 
							  AND 
							YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']." 
							  AND bc.VOLT_TP_ID='LV') lv, 
						(SELECT 
							   SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMEMV,
							   SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICEMV,
							   COUNT(DISTINCT bc.`SERVICE_NO`) as NOMBREMV 
						 From 
							 bill bc, 
							contract ct 
						 WHERE 
							ct.SERVICE_NO=bc.SERVICE_NO 
							  AND 
							ct.TUTELLE='".$_GET['TUTELLE']."' 
							  AND 
							YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
							  AND bc.VOLT_TP_ID='MV') mv
							WHERE 
					   c.SERVICE_NO=b.SERVICE_NO 
						 AND 
					   c.TUTELLE='".$_GET['TUTELLE']."' 
						 AND 
					   YEAR(STR_TO_DATE(`DISPATCH_DATE`,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE'];
					   //    echo $query;
							$req=$bdd->query($query);
							$resultat=$req->fetch();
							print(json_encode(array(
									'TUTELLE' => $resultat["TUTELLE"],
									'SOMMES' => $resultat["SOMME"],
									'SOMMESLV' => $resultat["SOMMELV"],
									'SOMMESMV' => $resultat["SOMMEMV"],
									'NOMBRE' => $resultat["NOMBRE"],
									'NOMBRELV' => $resultat["NOMBRELV"],
									'NOMBREML' => $resultat["NOMBREMV"],
									'PRICE' => $resultat["PRICE"],
									 'ACTIF' => $resultat["ACTIF"],
									'PRICEMV' => $resultat["PRICEMV"],
									'PRICELV' => $resultat["PRICELV"]
								), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
							$req->closeCursor();
					 }
			 
				 }
			 
			 
			 }