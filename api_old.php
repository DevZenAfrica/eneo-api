<?php
	include("connect_bd.php");
	
	
	
	
	
	
	
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
			 // echo $query;
			  $req=$bdd->query($query, PDO::FETCH_ASSOC);
	         
			  $resultats=$req->fetchAll();
    	    
			  foreach($resultats as $resultat )
			  {
				array_push($result,$resultat);
			  }
			print(json_encode($result,JSON_THROW_ON_ERROR));
			 //var_dump($result);
// 			$req->closeCursor();
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

			print(json_encode($result, JSON_PRETTY_PRINT));
			$req->closeCursor(); 
				}
		
			}

		  
		}
	
			$value="";	
		if(isset($_GET["getAbreStructureFromTags"]))
		{
     
         if(isset($_GET["ID_TAGS"]) && !empty($_GET["ID_TAGS"]))
         {
              $tab=getStructureChildContractStructure($_GET["ID_TAGS"]);
    
            $jsonString = json_encode($tab[0]);
       
        	 print($jsonString); 
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

          print(json_encode($resultats, JSON_PRETTY_PRINT));
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

          print(json_encode($result, JSON_PRETTY_PRINT));
         }
     
		}
	
		function getStructureChildContractStructure($id)
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
                    "CONTRACT"=>getStructureContratByTag($resultat["ID_TAGS"]),
                    
                    ));
    		}

    		if(!empty($result))
    		{
    		    
    		    
    		         foreach($result as $key => $elt )
    		        {

    		            if(!empty($elt["ID_STRUCTURE"]))
    		            {
    		                $children=getStructureChildContractStructure($result[$key]["ID_STRUCTURE"]);
    		               
    		                if(!empty($children) && $children!=[])
    		                {

                              array_push($result[$key]["CHILDREN"],$children) ;
    		              //  array_push($childrens,$children) ;
    		              //  $result[$key]["CHILDREN"]=$childrens;
//     		                    		                  // var_dump($children) ;
//     		                                		  $jsonString = json_encode($childrens);
// echo $jsonString."<br><br><br>";
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
        // 	print(json_encode($result, JSON_PRETTY_PRINT)); 
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
		
		print(json_encode($result, JSON_PRETTY_PRINT)); 
		    }
	
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
         
        //  	print(getApiInfos($url, JSON_PRETTY_PRINT));
         
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
         
        //  	print(getApiInfos($url, JSON_PRETTY_PRINT));
         
		}
		
		
		if(isset($_GET["getFacturesReclamationByContract"]))
		{
		    
		    if(isset($_GET["SERVICE_NO"]) && !empty($_GET["SERVICE_NO"]))
		    {
		     $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_reclamations=".$_GET["SERVICE_NO"];  
		     print(getApiInfos($url, JSON_PRETTY_PRINT));
		    }
		    else
		     {
		      $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_reclamations";  
		      print(getApiInfos($url, JSON_PRETTY_PRINT));
		     }
         
        //  	print(getApiInfos($url, JSON_PRETTY_PRINT));
         
		}
		
		
		if(isset($_GET["getFacturesListPayementByContract"]))
		{
		    
		    if(isset($_GET["SERVICE_NO"]) && !empty($_GET["SERVICE_NO"]))
		    {
		     $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_payement=".$_GET["SERVICE_NO"];  
		     print(getApiInfos($url, JSON_PRETTY_PRINT));
		    }
		    else
		     {
		      $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_payement";  
		      print(getApiInfos($url, JSON_PRETTY_PRINT));
		     }
         
        //  	print(getApiInfos($url, JSON_PRETTY_PRINT));
         
		}
		
		if(isset($_GET["getDetailsContract"]))
		{
		    
		    if(isset($_GET["SERVICE_NO"]) && !empty($_GET["SERVICE_NO"]))
		    {
		     $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_infoContrat=".$_GET["SERVICE_NO"];  
		     print(getApiInfos($url, JSON_PRETTY_PRINT));
		    }
		    else
		     {
		      $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_infoContrat";  
		      print(getApiInfos($url, JSON_PRETTY_PRINT));
		     }
         
		}
		
		if(isset($_GET["getBalanceContract"]))
		{
		    
		    if(isset($_GET["SERVICE_NO"]) && !empty($_GET["SERVICE_NO"]))
		    {
		     $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_balance=".$_GET["SERVICE_NO"];  
		     print(getApiInfos($url, JSON_PRETTY_PRINT));
		    }
		    else
		     {
		      $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_balance";  
		      print(getApiInfos($url, JSON_PRETTY_PRINT));
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
		print(json_encode($result, JSON_PRETTY_PRINT));
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
// 		print(json_encode($result, JSON_PRETTY_PRINT));
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
		print(json_encode($result, JSON_PRETTY_PRINT));
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
		print(json_encode($result, JSON_PRETTY_PRINT));
		$req->closeCursor();
    
	}
		
	if (isset($_GET["getInformationsContract"])) {
     	if (isset($_GET["SERVICE_NO"]) && !empty($_GET["SERVICE_NO"])) 
	    {
	    $query="SELECT * FROM `contract` WHERE `SERVICE_NO`='".$_GET["SERVICE_NO"]."'";
   
		$req=$bdd->query($query, PDO::FETCH_ASSOC);
		$resultat=$req->fetchAll();
		print(json_encode((object)$resultat[0], JSON_PRETTY_PRINT));
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

		print(json_encode($result, JSON_PRETTY_PRINT));
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
		print(json_encode($result, JSON_PRETTY_PRINT));
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
// 		echo "<script>console.log( json_encode($result, JSON_PRETTY_PRINT))</script>";
		print(json_encode($result, JSON_PRETTY_PRINT));
		$req->closeCursor();
	  
	}
	
	if (isset($_GET["getGlobalInformationsInstitutionsPerMonth"])) 
	{
	    if (isset($_GET["DISPATCH_DATE"])) 
	    {
	    $result = array();
	  $query="
	  SELECT 
     f.MONTHS,
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
           CONCAT(MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y')),'/',YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))) AS MONTHS,
     	   SUM(IFNULL(bc.CONSUMPTION,0)) as SOMME,
           SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICE,
           COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBRE
     From 
     	bill bc
     WHERE 
        YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
         GROUP BY MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))) f,
    (SELECT 
           CONCAT(MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y')),'/',YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))) AS MONTHS,
     	   SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMELV,
           SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICELV,
           COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBRELV
     From 
     	bill bc
     WHERE 
        YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
          AND bc.VOLT_TP_ID='LV' GROUP BY MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))) lv,
          
    (SELECT 
           CONCAT(MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y')),'/',YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))) AS MONTHS,
     	   SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMEMV,
           SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICEMV,
           COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBREMV
     From 
     	bill bc
     WHERE 
        YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
          AND bc.VOLT_TP_ID='MV' GROUP BY MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))) mv
          WHERE lv.MONTHS=mv.MONTHS and f.MONTHS=mv.MONTHS";
          
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
// 		echo "<script>console.log( json_encode($result, JSON_PRETTY_PRINT))</script>";
		print(json_encode($result, JSON_PRETTY_PRINT));
		$req->closeCursor();
	    }
	}
	
	
	
	
	
	if(isset($_GET['getGlobalInformationsInstitutions']))
	{
	    if(isset($_GET["DISPATCH_DATE"]))
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
     4200 as ACTIF
   FROM 
   bill b,
   (SELECT 
     	   SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMELV,
           SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICELV,
           COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBRELV
     From 
     	bill bc
     WHERE 
        YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
          AND bc.VOLT_TP_ID='LV') lv, 
    (SELECT 
           SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMEMV,
           SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICEMV,
           COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBREMV 
     From 
     	bill bc
     WHERE 
        YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
          AND bc.VOLT_TP_ID='MV') mv,
           (SELECT COUNT(IFNULL(b.`SERVICESTATUS`,0)) as ACTIF FROM bill b WHERE b.SERVICESTATUS='ACTIVE' and YEAR(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE'].") act 
WHERE 
   YEAR(STR_TO_DATE(`DISPATCH_DATE`,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE'];
   
        $req=$bdd->query($query);
		$resultat=$req->fetch();
		print(json_encode(array(
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
			), JSON_PRETTY_PRINT));
		$req->closeCursor();
   
	    }
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
// 		echo "<script>console.log( json_encode($result, JSON_PRETTY_PRINT))</script>";
		print(json_encode($result, JSON_PRETTY_PRINT));
		$req->closeCursor();
	    }
	}

	   
	if (isset($_GET["getInformationsInstitutionsContractByTutelleAndDate"])) {
	    if ( isset($_GET["ID_TAGS"]) && !empty($_GET["ID_TAGS"])) 
	    {
	        if( $_GET["ID_TAGS"]==='GLOBAL')
	        {
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
			// 		echo "<script>console.log( json_encode($result, JSON_PRETTY_PRINT))</script>";
					print(json_encode($result, JSON_PRETTY_PRINT));
					$req->closeCursor();
	  		  }
	        }
	       else if(isset($_GET["ID_TAGS"]) && !empty($_GET["ID_TAGS"]) && isset($_GET["DISPATCH_DATE"]))
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
		// echo "<script>console.log( json_encode($result, JSON_PRETTY_PRINT))</script>";
		print(json_encode($result, JSON_PRETTY_PRINT));
		$req->closeCursor();  
	        }
	    }
	   
	}

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
	
	if (isset($_GET["getInformationsInstitutionsByTutelleAndDate"])) 
	{

		if(isset($_GET["DISPATCH_DATE"]) && !empty($_GET["DISPATCH_DATE"]) && $_GET["DISPATCH_DATE"]=='2023')
		{
            if((isset($_GET['ID_TAGS'])  && $_GET['ID_TAGS']=='GLOBAL'))
			{
				$queryAll="SELECT DISTINCT b.`SERVICE_NO` FROM `bill` b, contract c WHERE c.SERVICE_NO=b.SERVICE_NO";
				$queryLV="SELECT DISTINCT b.`SERVICE_NO` FROM `bill` b, contract c WHERE c.SERVICE_NO=b.SERVICE_NO AND bc.VOLT_TP_ID = 'LV'";
				$queryMV="SELECT DISTINCT b.`SERVICE_NO` FROM `bill` b, contract c WHERE c.SERVICE_NO=b.SERVICE_NO AND bc.VOLT_TP_ID = 'MV'";
				$datas = array("contrats_group"=>"date","date"=>2023);
				var_dump($datas);
				

			}
			else if(isset($_GET['ID_TAGS']))
			{

				$queryAll="SELECT DISTINCT  b.`SERVICE_NO`,c.TUTELLE,		(
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
						)
						) AS actif
						WHERE
						actif.STATUS = 'ACTIVE'
						) AS ACTIF FROM `bill` b, contract c WHERE c.SERVICE_NO=b.SERVICE_NO and c.SERVICE_NO IN( SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS = :ID_TAGS);";
				$queryLV="SELECT DISTINCT  b.`SERVICE_NO`,c.TUTELLE FROM `bill` b, contract c WHERE c.SERVICE_NO=b.SERVICE_NO and c.SERVICE_NO IN( SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS = :ID_TAGS ) AND b.VOLT_TP_ID = 'LV';";
				$queryMV="SELECT DISTINCT  b.`SERVICE_NO`,c.TUTELLE FROM `bill` b, contract c WHERE c.SERVICE_NO=b.SERVICE_NO and c.SERVICE_NO IN( SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS = :ID_TAGS) AND b.VOLT_TP_ID = 'MV';";
				$params=[':ID_TAGS' => $_GET['ID_TAGS']];
				// $datas = array("contrats_group"=>"date","date"=>2023);
				
				$ResultAll=getData($queryAll,$params);
				$ResultMV=getData($queryMV,$params);
				$ResultLV=getData($queryLV,$params);
				// var_dump();
				
				$contrats_groupAll=implode_key(",", $ResultAll,"SERVICE_NO");
				$datasAll = array("contrat_bills_group"=>$contrats_groupAll,"date"=>2023);
				$contrats_groupMV=implode_key(",", $ResultMV,"SERVICE_NO");
				$datasMV = array("contrat_bills_group"=>$contrats_groupMV,"date"=>2023);
				$contrats_groupLV=implode_key(",", $ResultLV,"SERVICE_NO");
				$datasLV = array("contrat_bills_group"=>$contrats_groupLV,"date"=>2023);
                // var_dump( $datasLV);
				$All=json_decode(postApiInfos("https://ppsr.eneoapps.com/apiContrat/serverApi.php",$datasAll));
				$MV=json_decode(postApiInfos("https://ppsr.eneoapps.com/apiContrat/serverApi.php",$datasMV));
				$LV=json_decode(postApiInfos("https://ppsr.eneoapps.com/apiContrat/serverApi.php",$datasLV));
				// 
				// echo "<script>console.log($contrats_groupAll)</script>";
				// print_r($contrats_groupAll);
			    print(json_encode(array(
					'TUTELLE' => $ResultAll[0]["TUTELLE"]??'',
					'SOMMES' => $All[0]->total_conso??0,
					'SOMMESLV' =>$LV[0]->total_conso??0,
					'SOMMESMV' =>$MV[0]->total_conso??0,
					'NOMBRE' => $All[0]->total_contrat??count($ResultAll),
					'NOMBRELV' => $LV[0]->total_contrat??count($ResultLV),
					'NOMBREML' =>$MV[0]->total_contrat??count($ResultMV),
					'PRICE' => $All[0]->total_facture??0,
					'PRICEMV' => $MV[0]->total_facture??0,
					'PRICELV' => $LV[0]->total_facture??0,
					'ACTIF' => $ResultAll[0]["ACTIF"]??1200
				), JSON_PRETTY_PRINT));
			// $req->closeCursor();
			}
			else if ((isset($_GET["TUTELLE"]) and !empty($_GET["TUTELLE"])))
			{
				$queryAll="SELECT DISTINCT  b.`SERVICE_NO`,c.TUTELLE,		 (
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
							) AS actif
						WHERE
							actif.STATUS = 'ACTIVE'
						) AS ACTIF
						
						FROM `bill` b, contract c WHERE c.SERVICE_NO=b.SERVICE_NO  AND c.TUTELLE=:TUTELLE;";
				$queryLV="SELECT DISTINCT  b.`SERVICE_NO`,c.TUTELLE FROM `bill` b, contract c WHERE c.SERVICE_NO=b.SERVICE_NO  AND c.TUTELLE=:TUTELLE AND b.VOLT_TP_ID = 'LV';";
				$queryMV="SELECT DISTINCT  b.`SERVICE_NO`,c.TUTELLE FROM `bill` b, contract c WHERE c.SERVICE_NO=b.SERVICE_NO  AND c.TUTELLE=:TUTELLE AND b.VOLT_TP_ID = 'MV';";
				$params=[':TUTELLE' => $_GET['TUTELLE']];
				// $datas = array("contrats_group"=>"date","date"=>2023);
				
				$ResultAll=getData($queryAll,$params);
				$ResultMV=getData($queryMV,$params);
				$ResultLV=getData($queryLV,$params);
				// var_dump();
				
				$contrats_groupAll=implode_key(",", $ResultAll,"SERVICE_NO");
				$datasAll = array("contrat_bills_group"=>$contrats_groupAll,"date"=>2023);
				$contrats_groupMV=implode_key(",", $ResultMV,"SERVICE_NO");
				$datasMV = array("contrat_bills_group"=>$contrats_groupMV,"date"=>2023);
				$contrats_groupLV=implode_key(",", $ResultLV,"SERVICE_NO");
				$datasLV = array("contrat_bills_group"=>$contrats_groupLV,"date"=>2023);
                // var_dump( $datasLV);
				$All=json_decode(postApiInfos("https://ppsr.eneoapps.com/apiContrat/serverApi.php",$datasAll));
				$MV=json_decode(postApiInfos("https://ppsr.eneoapps.com/apiContrat/serverApi.php",$datasMV));
				$LV=json_decode(postApiInfos("https://ppsr.eneoapps.com/apiContrat/serverApi.php",$datasLV));
				// 
				// echo "<script>console.log($contrats_groupAll)</script>";
				// print_r($contrats_groupAll);
			    print(json_encode(array(
					'TUTELLE' => $ResultAll[0]["TUTELLE"]??'',
					'SOMMES' => $All[0]->total_conso??0,
					'SOMMESLV' =>$LV[0]->total_conso??0,
					'SOMMESMV' =>$MV[0]->total_conso??0,
					'NOMBRE' => $All[0]->total_contrat??count($ResultAll),
					'NOMBRELV' => $LV[0]->total_contrat??count($ResultLV),
					'NOMBREML' =>$MV[0]->total_contrat??count($ResultMV),
					'PRICE' => $All[0]->total_facture??0,
					'PRICEMV' => $MV[0]->total_facture??0,
					'PRICELV' => $LV[0]->total_facture??0,
					'ACTIF' => $ResultAll[0]["ACTIF"]??1200
				), JSON_PRETTY_PRINT));
			}
            
		}
		else if((isset($_GET['ID_TAGS'])  && $_GET['ID_TAGS']!='GLOBAL') && isset($_GET["DISPATCH_DATE"]) )
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
			), JSON_PRETTY_PRINT));
		$req->closeCursor();
		}
     	else if ((isset($_GET["TUTELLE"]) and !empty($_GET["TUTELLE"])) && isset($_GET["DISPATCH_DATE"])) 
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
			), JSON_PRETTY_PRINT));
		$req->closeCursor();
    	}


	}
	
	if (isset($_GET["getInformationsInstitutionsByTutelleAndDateAndTag"])) 
	{
     	if (isset($_GET["TUTELLE"]) && isset($_GET["DISPATCH_DATE"]) && isset($_GET["ID_TAGS"])) 
	    {
	    $query="SELECT 
                 SUM(IFNULL(b.CONSUMPTION,0)) as SOMME,
                 SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE, 
                 COUNT(IFNULL(b.`SERVICE_NO`,0)) AS NOMBRE, 
                 lv.SOMMELV,
                 mv.SOMMEMV,
                 lv.PRICELV,
                 mv.PRICEMV,
                 lv.NOMBRELV,
                 mv.NOMBREMV,
                 c.TUTELLE,
                 
				 (SELECT COUNT(*) FROM (SELECT DISTINCT c.`SERVICE_NO`, c.`STATUS` FROM `contract` c, bill b WHERE c.SERVICE_NO=b.SERVICE_NO and b.SERVICE_NO IN( SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS = 'MINSANTE' ) AND YEAR(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']." ) as actif WHERE actif.STATUS='ACTIVE') ACTIF
            FROM 
               bill b, 
               contract c,
                    contrat_possede_tags cpt,
               (SELECT 
                 	   SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMELV,
                       SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICELV,
                       COUNT(IFNULL(bc.`SERVICE_NO`,0)) as NOMBRELV
                 From 
                 	bill bc, 
                	contract ct ,
                    contrat_possede_tags cpt
                 WHERE 
                    ct.SERVICE_NO=bc.SERVICE_NO 
                      AND 
                    ct.TUTELLE='".$_GET['TUTELLE']."' 
                      AND 
                    YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
                      AND bc.VOLT_TP_ID='LV' AND 
                      ct.SERVICE_NO=cpt.SERVICE_NO 
                      And cpt.ID_TAGS='".$_GET['ID_TAGS']."' ) lv, 
                (SELECT 
                       SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMEMV,
                       SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICEMV,
                       COUNT(IFNULL(bc.`SERVICE_NO`,0)) as NOMBREMV 
                 From 
                 	bill bc, 
                	contract ct ,
                    contrat_possede_tags cpt
                 WHERE 
                    ct.SERVICE_NO=bc.SERVICE_NO 
                      AND 
                    ct.TUTELLE='".$_GET['TUTELLE']."' 
                      AND 
                    YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
                      AND bc.VOLT_TP_ID='MV' AND 
                      ct.SERVICE_NO=cpt.SERVICE_NO 
                      And cpt.ID_TAGS='".$_GET['ID_TAGS']."') mv
            WHERE 
               c.SERVICE_NO=b.SERVICE_NO 
                 AND 
               c.TUTELLE='".$_GET['TUTELLE']."' 
                 AND 
               YEAR(STR_TO_DATE(`DISPATCH_DATE`,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']." AND 
                      c.SERVICE_NO=cpt.SERVICE_NO 
                      And cpt.ID_TAGS='".$_GET['ID_TAGS']."'";



   
		
  
 
 
 
 
 
 
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
			), JSON_PRETTY_PRINT));
		$req->closeCursor();
    	}
	}
	
	if (isset($_GET["getAllInformationsInstitutions"])) {
	    $result = array();
	    
	    if ( isset($_GET["DISPATCH_DATE"])) 
	    {
		$req=$bdd->query("SELECT SUM(IFNULL(b.CONSUMPTION,0)) as SOMMES,SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE, COUNT(IFNULL(b.BILL_NO,0)) AS NOMBRE, c.TUTELLE FROM bill b, contract c WHERE c.SERVICE_NO=b.SERVICE_NO AND  YEAR(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']." GROUP BY c.TUTELLE");
		
		while($resultat=$req->fetch()){
		    
		   
			array_push($result,array(
			    'TUTELLE' => $resultat["TUTELLE"],
			    'SOMMES' => $resultat["SOMMES"],
			    'NOMBRE' => $resultat["NOMBRE"],
			    'PRICE' => $resultat["PRICE"]
			) );
	     	}
	    }
	    else
	    {
	        	$req=$bdd->query("SELECT SUM(IFNULL(b.CONSUMPTION,0)) as SOMMES,SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE, COUNT(IFNULL(b.BILL_NO,0)) AS NOMBRE, c.TUTELLE FROM bill b, contract c WHERE c.SERVICE_NO=b.SERVICE_NO GROUP BY c.TUTELLE");
		
		while($resultat=$req->fetch()){
		    
		   
			array_push($result,array(
			    'TUTELLE' => $resultat["TUTELLE"],
			    'SOMMES' => $resultat["SOMMES"],
			    'NOMBRE' => $resultat["NOMBRE"],
			    'PRICE' => $resultat["PRICE"]
			) );
	     	}
	    }
	    
		
// 		echo "<script>console.log( json_encode($result, JSON_PRETTY_PRINT))</script>";
		print(json_encode($result, JSON_PRETTY_PRINT));
		$req->closeCursor();
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
		print(json_encode($result, JSON_PRETTY_PRINT));
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
		print(json_encode($result, JSON_PRETTY_PRINT));
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
		print(json_encode($result, JSON_PRETTY_PRINT));
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
		print(json_encode($result, JSON_PRETTY_PRINT));
		$req->closeCursor();
	}
?>


























































































<?php
// 	include("connect_bd.php");
	
	
	
	
	
	
	
// 			function getStructureContratByTag($ID_TAGS)
// 		{
		      
// 		    include("connect_bd.php");
// 		    $result = array();
		  
// 		    $query= "SELECT * FROM `contrat_possede_tags` ct WHERE ct.`ID_TAGS`='".$ID_TAGS."'";
// 		    $req=$bdd->query($query, PDO::FETCH_ASSOC);
//     		$resultats=$req->fetchAll();
    	    
//     	    foreach($resultats as $resultat )
//     		{
   
//     		    array_push($result,array('SERVICE_NO'=>$resultat["SERVICE_NO"]));
    
//     		}
//     			$req->closeCursor();

//     		  //$jsonString = json_encode($result);
  
//     		    return $result;
    
    	
		
// 		}
		
	
	
	
	
	
// 		if (isset($_GET["getListInformationsContratsByTag"])) 
// 		{
// 		  if (isset($_GET["ID_TAGS"]) && !empty($_GET["ID_TAGS"]))
// 			{

// 				if($_GET["ID_TAGS"]==='GLOBAL')
// 				{
// 			$result = array();
// 			$query="SELECT 
// 			b.SERVICE_NO,
// 			b.VOLT_TP_ID as TL ,
// 			'' AS STATUT, c.TUTELLE, 
// 			'' AS ANNOTATION,
// 			b.AGENCE, 
// 			0 AS AVOIR, 
// 			b.CUST_NAME, 
// 			SUM(IFNULL(b.CONSUMPTION,0)) as SOMME, 
// 			SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE , 
// 			COUNT(IFNULL(b.SERVICE_NO,0)) AS NOMBRE 
// 			FROM bill b, 
// 			   contract c
// 		   WHERE c.SERVICE_NO=b.SERVICE_NO  GROUP BY c.SERVICE_NO ORDER BY `b`.`VOLT_TP_ID` DESC";
			  
// 			  $req=$bdd->query($query, PDO::FETCH_ASSOC);
	         
// 			  $resultats=$req->fetchAll();
    	    
// 			  foreach($resultats as $resultat )
// 			  {
// 				array_push($result,(object)$resultat);
// 			  }
// 			print(json_encode($result, JSON_PRETTY_PRINT));
// 			$req->closeCursor();
// 				}
// 				else{
// 					$result = array();
// 		  $query="SELECT b.SERVICE_NO, b.VOLT_TP_ID as TL , '' AS STATUT, c.TUTELLE, '' AS ANNOTATION, b.AGENCE, 0 AS AVOIR, b.CUST_NAME, SUM(IFNULL(b.CONSUMPTION,0)) as SOMME, SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE , COUNT(IFNULL(b.SERVICE_NO,0)) AS NOMBRE FROM bill b, contract c WHERE c.SERVICE_NO=b.SERVICE_NO And b.SERVICE_NO IN (SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS='".$_GET['ID_TAGS']."') GROUP BY c.SERVICE_NO ORDER BY `b`.`VOLT_TP_ID` DESC";
			  
// 			  $req=$bdd->query($query,  PDO::FETCH_ASSOC);
	
// 			  $resultats=$req->fetchAll();
    	    
// 			  foreach($resultats as $resultat )
// 			  {
// 				array_push($result,(object)$resultat);
// 			  }

// 			print(json_encode($result, JSON_PRETTY_PRINT));
// 			$req->closeCursor(); 
// 				}
		
// 			}

		  
// 		}
	
// 			$value="";	
// 		if(isset($_GET["getAbreStructureFromTags"]))
// 		{
     
//          if(isset($_GET["ID_TAGS"]) && !empty($_GET["ID_TAGS"]))
//          {
//               $tab=getStructureChildContractStructure($_GET["ID_TAGS"]);
    
//             $jsonString = json_encode($tab[0]);
       
//         	 print($jsonString); 
//          }
     
// 		}
		
	   		
	   		
// 	   	if(isset($_GET["postUser"]))
// 		{
//           $tr=(isset($_GET["EMAIL"]) || isset($_GET["PHONE"])) ;
    
//          if( $tr && isset($_GET["ID_TAGS"]))
//          {
//          $val=$_GET["EMAIL"]??$_GET["PHONE"];
//          $email=isset($_GET["EMAIL"])?$_GET["EMAIL"]:NULL;
//          $phone=isset($_GET["PHONE"])?$_GET["PHONE"]:NULL;
// 		 $nom=isset($_GET["NOM"])?$_GET["NOM"]:NULL;
//          $prenom=isset($_GET["PRENOM"])?$_GET["PRENOM"]:NULL;
// 		 $idParent=isset($_GET["ID_PARENT_UTILISATEUR"])?$_GET["ID_PARENT_UTILISATEUR"]:NULL;

//          $query="INSERT INTO `utilisateur` (`ID_UTILISATEUR`, `ID_TAGS`, `EMAIL`, `PHONE`, `NOM`, `PRENOM`, `ID_PARENT_UTILISATEUR`)
// 		         VALUES (NULL, '".$_GET["ID_TAGS"]."', '".$email."', '".$phone."', '".$nom."', '".$prenom."', '".$idParent."')"; 
//           $req=$bdd->query($query, PDO::FETCH_ASSOC);
//                  if($req)
//                  {
//                   print(true);   
//                  }
//                  else
//                  {
//                     print(0); 
//                  }

		
//          }
     
// 		}

// 		if(isset($_GET["deleteUser"]))
// 		{
// 		  if(isset($_GET["ID_UTILISATEUR"]) && !empty($_GET["ID_UTILISATEUR"]))
//           {
// 			$idParent=$_GET["ID_UTILISATEUR"];

// 			$query="DELETE FROM utilisateur WHERE `utilisateur`.`ID_UTILISATEUR`  =".$idParent; 
// 	        $req=$bdd->query($query, PDO::FETCH_ASSOC);
// 			if($req)
// 			{
// 			  print(true);   
// 			}
// 			else
// 			{
// 			   print(0); 
// 			}
// 		  }
// 		}


//         if(isset($_GET["gettUsersbyMailOrPhone"]))
// 		{
//           $tr=(isset($_GET["EMAIL"]) || isset($_GET["PHONE"])) ;
      
//          if( $tr)
//          {
//              	    $result = array();
//          $val=$_GET["EMAIL"]??$_GET["PHONE"];
//          $query="SELECT * FROM `utilisateur` WHERE `EMAIL`='".$val."' or `PHONE`='".$val."'"; 
//         	// var_dump($query);
// 		    $req=$bdd->query($query, PDO::FETCH_ASSOC);
//     		$resultats=$req->fetch();
    	
//     	    // foreach($resultats as $resultat )
//     		// {
//     		//     array_push($result,$resultat);
//     		// }

//           print(json_encode($resultats, JSON_PRETTY_PRINT));
//          }
     
// 		}
		
// 		if(isset($_GET["getListUsersChildID"]))
// 		{
         
      
//          if( isset($_GET["ID_UTILISATEUR"]))
//          {
//              	    $result = array();
//          $val=$_GET["ID_UTILISATEUR"];
//          $query="SELECT * FROM `utilisateur` WHERE `ID_PARENT_UTILISATEUR`='".$val."'"; 
        	
// 		    $req=$bdd->query($query, PDO::FETCH_ASSOC);
//     		$resultats=$req->fetchAll();
    	
//     	    foreach($resultats as $resultat )
//     		{
//     		    array_push($result,$resultat);
//     		}

//           print(json_encode($result, JSON_PRETTY_PRINT));
//          }
     
// 		}
	
// 		function getStructureChildContractStructure($id)
// 		{
		      
// 		    include("connect_bd.php");
// 		    $result = array();
// 		      $res = array();
// 		    $query= "SELECT * FROM structure_contient_tags st, tags t,`structure` s WHERE s.ID_STRUCTURE=st.ID_STRUCTURE and t.ID_TAGS=st.ID_TAGS and (s.ID_STRUCTURE_PARENT='".$id."' or t.ID_TAGS='".$id."')";
// 		      //var_dump($query);
// 		   $childrens= array();;
// 		    $req=$bdd->query($query, PDO::FETCH_ASSOC);
//     		$resultats=$req->fetchAll();
    	
//     	    foreach($resultats as $resultat )
//     		{
//                     array_push($result,array(
//                     "ID_STRUCTURE"=> $resultat["ID_STRUCTURE"],
//                     "ID_STRUCTURE_PARENT"=> $resultat["ID_STRUCTURE_PARENT"],
//                     "TITRE_FR"=>$resultat["TITRE_FR"],
//                     "TITRE_EN"=> $resultat["TITRE_EN"],
//                     "TITRE_COURT_FR"=> $resultat["TITRE_COURT_FR"],
//                     "TITRE_COURT_EN"=> $resultat["TITRE_COURT_EN"],
//                     "TUTELLE"=> $resultat["TUTELLE"],
//                     "ID_TAGS"=> $resultat["ID_TAGS"],
//                     "CHILDREN"=> [],
//                     "CONTRACT"=>getStructureContratByTag($resultat["ID_TAGS"]),
                    
//                     ));
//     		}

//     		if(!empty($result))
//     		{
    		    
    		    
//     		         foreach($result as $key => $elt )
//     		        {

//     		            if(!empty($elt["ID_STRUCTURE"]))
//     		            {
//     		                $children=getStructureChildContractStructure($result[$key]["ID_STRUCTURE"]);
    		               
//     		                if(!empty($children) && $children!=[])
//     		                {

//                               array_push($result[$key]["CHILDREN"],$children) ;
//     		              //  array_push($childrens,$children) ;
//     		              //  $result[$key]["CHILDREN"]=$childrens;
// //     		                    		                  // var_dump($children) ;
// //     		                                		  $jsonString = json_encode($childrens);
// // echo $jsonString."<br><br><br>";
//     		                }
//     		            }
//     	        	} 

//     		}

//     				$req->closeCursor();
//     		  return $result;
    
		
// 		}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
// 		$idbase=0;
	
// 		function getAllStructureChild($id,$idbqse)
// 		{
// 		    $idbase=$idbqse;
// 		   echo $idbqse;
// 		    include("connect_bd.php");
// 		    $result = array();
// 		    $tab = array();
// 		      $res = array();
// 		    $query= "SELECT * FROM `structure` WHERE `ID_STRUCTURE_PARENT`=".$id;
		    
		    
// 		    $req=$bdd->query($query, PDO::FETCH_ASSOC);
//     		$resultats=$req->fetchAll();
    	
//     	    foreach($resultats as $resultat)
//     		{
//     		    array_push($result,array($id=>$resultat));
//     		}
//     		if(!empty($result))
//     		{
    		   
//     		        foreach($result as $elt)
//     		        {
    
//     		            if(!empty($tab))
//     		            {

//                             if($idbqse!=[$id])
//     		                 array_push($tab[$idbqse][$id],array($id=>$elt));
//     		                 else
//     		                array_push($tab[$id],getAllStructureChild($elt[$id]["ID_STRUCTURE"],$idbase));
    		                
//     		          $jsonString = json_encode($tab);
//                       echo $jsonString ."<br><br><br>";
    		  
//     		            }
//     		            else
//     		            {
//     		                array_push($tab,getAllStructureChild($elt[$id]["ID_STRUCTURE"],$idbase));
//     		            }
//     	        	} 

//     		}
//     		else
//     		{
// 		                		        		                    		              $jsonString = json_encode($tab);
// echo $jsonString ."<br><br><br>";
//     		    return $tab;
//     		}
//     		$req->closeCursor();
		
// 		}
		
// 			if(isset($_GET["getGlobaltest"]))
// 		{
//          $url="https://ppsr.eneoapps.com/apiContrat/serverApi.php?get_facture=200237921";
//         //  	echo getImpayer('203673817');
//          	 $result = array();
         
//       $tab=getStructureChild(7,$result);
//         global $val;
//         $res=substr($val, 0, -1);
//         // echo "[".$val."]";
//         print("[".$res."]"); 
// //             		  $jsonString = json_encode($tab);
// // echo $jsonString;
//         // 	print(json_encode($result, JSON_PRETTY_PRINT)); 
// 		}
		
	   
// 	$val="";
// 			function getStructureChild($id,$tab)
// 		{
		      
// 		    include("connect_bd.php");
// 		    $result = array();
// 		      $res = array();
// 		    $query= "SELECT * FROM `structure` WHERE `ID_STRUCTURE_PARENT`=".$id;
// 		    $req=$bdd->query($query, PDO::FETCH_ASSOC);
//     		$resultats=$req->fetchAll();
    	
//     	    foreach($resultats as $resultat )
//     		{
//     		    array_push($result,array(
//     		        "ID_STRUCTURE"=> $resultat["ID_STRUCTURE"],
//     "ID_STRUCTURE_PARENT"=> $resultat["ID_STRUCTURE_PARENT"],
//     "TITRE_FR"=>$resultat["TITRE_FR"],
//     "TITRE_EN"=> $resultat["TITRE_EN"],
//     "TITRE_COURT_FR"=> $resultat["TITRE_COURT_FR"],
//     "TITRE_COURT_EN"=> $resultat["TITRE_COURT_EN"],
//     "TUTELLE"=> $resultat["TUTELLE"],
//     "CHILDREN"=>[]));
//     		}
//     		if(!empty($result))
//     		{
//     		        		              $jsonString = json_encode($result);
//           $jsonString .=",";
//         global $val;
//         $val.=$jsonString;
//         // echo $val;
//     		         foreach($result as $elt )
//     		        {
// //     		              $jsonString = json_encode($elt);
// // echo $jsonString ."<br>";
//     		            if(!empty($elt["ID_STRUCTURE"]))
//     		            {
//     		                 $tab=$result;
//     		                 array_push($elt["CHILDREN"],getStructureChild($elt["ID_STRUCTURE"],$tab));
//     		            }
    		             
//     		          //  else
//     		          //  {
//     		          //      array_push($result["CHILDREN"],array());
//     		          //  }
//     	        	} 

//     		}
//     		else
//     		{
//     		    return $tab;
//     		}
//     		$req->closeCursor();
		
// 		}
		
// 		function getStructureChilds($id)
// 		{
		      
// 		    include("connect_bd.php");
// 		    $result = array();
		  
// 		    $query= "SELECT * FROM `structure` WHERE `ID_STRUCTURE_PARENT`=".$id;
// 		    $req=$bdd->query($query, PDO::FETCH_ASSOC);
//     		$resultats=$req->fetchAll();
    	
//     	    foreach($resultats as $resultat )
//     		{
//     		    array_push($result,$resultat);
//     		}

//     		  //$jsonString = json_encode($result);
//         //     echo $jsonString;
    		   
    
//     		$req->closeCursor();
// 			return $result;
		
// 		}
		
		
// 		if(isset($_GET["getLastFacture"]))
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
		
// 		print(json_encode($result, JSON_PRETTY_PRINT)); 
// 		    }
	
// 		}
	
// 		if(isset($_GET["getFacturesByContract"]))
// 		{
		    
// 		    if(isset($_GET["SERVICE_NO"]) && !empty($_GET["SERVICE_NO"]))
// 		    {
// 		     $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_facture=".$_GET["SERVICE_NO"];  
// 		     print(getApiInfos($url));
// 		    }
// 		    else
// 		     {
// 		      $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_facture";  
// 		      print(getApiInfos($url));
// 		     }
         
//         //  	print(getApiInfos($url, JSON_PRETTY_PRINT));
         
// 		}
// 		function getImpayer($SERVICE_NO)
// 		{
// 		  $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_facturesImpayes=".$SERVICE_NO; 
// 		  $impayer=0;
// 		  foreach(json_decode(getApiInfos($url), true) as $row)
// 		  {
// 		      $impayer= $impayer + (int) str_replace(" FCFA", "",$row["amount_with_tax"]);
// 		  }

// 		  return $impayer; 
// 		}
		
// 		function getApiInfos($url)
// 		{
// 		    		    	$ch = curl_init();
// 	try {
	    
  
//             // Initialisez une session CURL.
              
              
//             // Récupérer le contenu de la page
//             curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
              
//             //Saisir l'URL et la transmettre à la variable.
//             curl_setopt($ch, CURLOPT_URL, $url); 
//             //Désactiver la vérification du certificat puisque waytolearnx utilise HTTPS
//             curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//             //Exécutez la requête 
//             $result = curl_exec($ch); 
//             curl_close($ch);
            
//             			return $result;
          
//         	} catch (\Throwable $th) {
//         		throw $th;
//         	} finally {
//         		curl_close($ch);
//         	}
// 		}
		
	
				
// 		function postApiInfos($url,$datas)
// 		{
// 		    		    	$ch = curl_init();
// 	try {
	    
  
//             // Initialisez une session CURL.
              
              
//             // Récupérer le contenu de la page
//             curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
              
//             //Saisir l'URL et la transmettre à la variable.
//             curl_setopt($ch, CURLOPT_URL, $url); 
//             curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
//             //Désactiver la vérification du certificat puisque waytolearnx utilise HTTPS
//             curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//             //Exécutez la requête 
//             $result = curl_exec($ch); 
//             curl_close($ch);
            
//             			return $result;
          
//         	} catch (\Throwable $th) {
//         		throw $th;
//         	} finally {
//         		curl_close($ch);
//         	}
// 		}
		
		
// 		if(isset($_GET["getFacturesImpayerByContract"]))
// 		{
		    
// 		    if(isset($_GET["SERVICE_NO"]) && !empty($_GET["SERVICE_NO"]))
// 		    {
// 		     $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_facturesImpayes=".$_GET["SERVICE_NO"];  
// 		     print(getApiInfos($url));
// 		    }
// 		    else
// 		     {
// 		      $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_facturesImpayes";  
// 		      print(getApiInfos($url));
// 		     }
         
//         //  	print(getApiInfos($url, JSON_PRETTY_PRINT));
         
// 		}
		
		
// 		if(isset($_GET["getFacturesReclamationByContract"]))
// 		{
		    
// 		    if(isset($_GET["SERVICE_NO"]) && !empty($_GET["SERVICE_NO"]))
// 		    {
// 		     $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_reclamations=".$_GET["SERVICE_NO"];  
// 		     print(getApiInfos($url, JSON_PRETTY_PRINT));
// 		    }
// 		    else
// 		     {
// 		      $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_reclamations";  
// 		      print(getApiInfos($url, JSON_PRETTY_PRINT));
// 		     }
         
//         //  	print(getApiInfos($url, JSON_PRETTY_PRINT));
         
// 		}
		
		
// 		if(isset($_GET["getFacturesListPayementByContract"]))
// 		{
		    
// 		    if(isset($_GET["SERVICE_NO"]) && !empty($_GET["SERVICE_NO"]))
// 		    {
// 		     $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_payement=".$_GET["SERVICE_NO"];  
// 		     print(getApiInfos($url, JSON_PRETTY_PRINT));
// 		    }
// 		    else
// 		     {
// 		      $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_payement";  
// 		      print(getApiInfos($url, JSON_PRETTY_PRINT));
// 		     }
         
//         //  	print(getApiInfos($url, JSON_PRETTY_PRINT));
         
// 		}
		
// 		if(isset($_GET["getDetailsContract"]))
// 		{
		    
// 		    if(isset($_GET["SERVICE_NO"]) && !empty($_GET["SERVICE_NO"]))
// 		    {
// 		     $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_infoContrat=".$_GET["SERVICE_NO"];  
// 		     print(getApiInfos($url, JSON_PRETTY_PRINT));
// 		    }
// 		    else
// 		     {
// 		      $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_infoContrat";  
// 		      print(getApiInfos($url, JSON_PRETTY_PRINT));
// 		     }
         
// 		}
		
// 		if(isset($_GET["getBalanceContract"]))
// 		{
		    
// 		    if(isset($_GET["SERVICE_NO"]) && !empty($_GET["SERVICE_NO"]))
// 		    {
// 		     $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_balance=".$_GET["SERVICE_NO"];  
// 		     print(getApiInfos($url, JSON_PRETTY_PRINT));
// 		    }
// 		    else
// 		     {
// 		      $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_balance";  
// 		      print(getApiInfos($url, JSON_PRETTY_PRINT));
// 		     }
         
// 		}
		
		
// 	if (isset($_GET["getGlobalInformationsContractsWithRegions"])) {
//         $result = array();
//         $query="";
//         if(isset($_GET["DISPATCH_DATE"]) && !empty($_GET["DISPATCH_DATE"]))
//         {
//          $query=" SELECT 
//      f.REGION,
//      f.SOMME,
//      f.PRICE,
//      f.NOMBRE,
//      mv.SOMMEMV,
//      mv.PRICEMV,
//      mv.NOMBREMV,
//      lv.SOMMELV,
//      lv.PRICELV,
//      lv.NOMBRELV
//      FROM 
//      (SELECT 
//           bc.REGION,
//      	   SUM(IFNULL(bc.CONSUMPTION,0)) as SOMME,
//           SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICE,
//           COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBRE
//      From 
//      	bill bc
//      WHERE 
//         YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
//          GROUP BY bc.REGION) f,
//     (SELECT 
//           bc.REGION,
//      	   SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMELV,
//           SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICELV,
//           COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBRELV
//      From 
//      	bill bc
//      WHERE 
//         YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
//           AND bc.VOLT_TP_ID='LV' GROUP BY bc.REGION) lv,
          
//     (SELECT 
//           bc.REGION,
//      	   SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMEMV,
//           SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICEMV,
//           COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBREMV
//      From 
//      	bill bc
//      WHERE 
//         YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
//           AND bc.VOLT_TP_ID='MV' GROUP BY bc.REGION) mv
//           WHERE lv.REGION=mv.REGION and f.REGION=mv.REGION";
//         }
//         else
//         {
//         $query="SELECT 
//      f.REGION,
//      f.SOMME,
//      f.PRICE,
//      f.NOMBRE,
//      mv.SOMMEMV,
//      mv.PRICEMV,
//      mv.NOMBREMV,
//      lv.SOMMELV,
//      lv.PRICELV,
//      lv.NOMBRELV
//      FROM 
//      (SELECT 
//           bc.REGION,
//      	   SUM(IFNULL(bc.CONSUMPTION,0)) as SOMME,
//           SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICE,
//           COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBRE
//      From 
//      	bill bc
//     GROUP BY bc.REGION) f,
//     (SELECT 
//           bc.REGION,
//      	   SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMELV,
//           SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICELV,
//           COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBRELV
//      From 
//      	bill bc
//      WHERE 
//         bc.VOLT_TP_ID='LV' GROUP BY bc.REGION) lv,
          
//     (SELECT 
//           bc.REGION,
//      	   SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMEMV,
//           SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICEMV,
//           COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBREMV
//      From 
//      	bill bc
//      WHERE  bc.VOLT_TP_ID='MV' GROUP BY bc.REGION) mv
//           WHERE lv.REGION=mv.REGION and f.REGION=mv.REGION";
   

//         }
	   
//     		$req=$bdd->query($query, PDO::FETCH_ASSOC);
// 		$resultats=$req->fetchAll();
		
// 	    foreach($resultats as $resultat )
// 		{
// 		    		array_push($result,(object)$resultat);
// 		}
// 		print(json_encode($result, JSON_PRETTY_PRINT));
// 		$req->closeCursor();
// 	}
	
	
// // 	if (isset($_GET["getInformationsContractPassOneYear"])) {
// //         $result = array();
// // 	    //$query="SELECT CONCAT(MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y')),'/',YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))) AS MONTHS,
// // 	   // $query="SELECT STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y') AS MONTHS,
// // 	   // SUM(IFNULL(bc.CONSUMPTION,0)) as SOMME, SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICE, COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBRE 
// // 	   // From bill bc WHERE DATE_FORMAT(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'),'%Y-%m-%d') BETWEEN STR_TO_DATE(CONCAT(YEAR(NOW())-1,'-',MONTH(NOW()),'-',DAY(NOW())),'%Y-%m-%d')
// // 	   // AND STR_TO_DATE(CONCAT(YEAR(NOW()),'-',MONTH(NOW()),'-',DAY(NOW())),'%Y-%m-%d') 
// // 	   // GROUP BY CONCAT(MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y')),'/',YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y')))";
   
// //       if (isset($_GET["SERVICE_NO"]) && !empty($_GET["SERVICE_NO"])) 
// // 	    {
// // 	    $query="SELECT STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y') AS MONTHS,
// // 	    SUM(IFNULL(bc.CONSUMPTION,0)) as SOMME, SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICE
// // 	    From bill bc WHERE bc.SERVICE_NO= '".$_GET["SERVICE_NO"]."' and  DATE_FORMAT(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'),'%Y-%m-%d') BETWEEN STR_TO_DATE(CONCAT(YEAR(NOW())-1,'-',MONTH(NOW()),'-',DAY(NOW())),'%Y-%m-%d')
// // 	    AND STR_TO_DATE(CONCAT(YEAR(NOW()),'-',MONTH(NOW()),'-',DAY(NOW())),'%Y-%m-%d') 
// // 	    GROUP BY CONCAT(MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y')),'/',YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y')))";
// // 		$req=$bdd->query($query, PDO::FETCH_ASSOC);
// // 		$resultats=$req->fetchAll();
		
// // 	    foreach($resultats as $resultat )
// // 		{
// // 		    		array_push($result,(object)$resultat);
// // 		}
// // 		print(json_encode($result, JSON_PRETTY_PRINT));
// // 		$req->closeCursor();
// //     	}
       
    
// // 	}
	
	
// 		if (isset($_GET["getInformationsContractPassOneYear"])) {
      
//         if (isset($_GET["SERVICE_NO"]) && !empty($_GET["SERVICE_NO"])) 
// 	    {
	        
// 	    $result = array();
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
		
// 		     $url="http://ppsr.eneoapps.com/apiContrat/serverApi.php?get_facture=".$_GET["SERVICE_NO"];  
// 		     $jsonobj=getApiInfos($url);
// 		     $tab=json_decode($jsonobj);
// 		     $taille = count($tab);
// 		     $res=$tab[$taille-1];
// 		     $lt=(int)date("m");
// 		     $i;
// 		     $debut=$taille-$lt+1;
// 		     $fin=$taille-1;
		    
// 		     for($i=$debut;$i<=$fin;$i++)
// 		     {
// 		         $date = new DateTime($tab[$i]->Bill_Details->payment_deadline);
// 		       array_push($result,array(
		       	 
// 			    'MONTHS' =>$date->format('Y-m-d'),
// 			    'SOMME' => str_replace(" Kwh", "", $tab[$i]->Bill_Details->consumption),
// 			    'PRICE' => $tab[$i]->amount_with_tax
// 			    ));
// 		     }
// 		print(json_encode($result, JSON_PRETTY_PRINT));
// 		$req->closeCursor();
//     	}
       
    
// 	}
// 		if (isset($_GET["getListContractWithRegroupCode"])) {
//         $result = array();
// 	    $query="SELECT b.`SERVICE_NO` , c.`REGROUP_ID` FROM `contract` c, bill b WHERE c.SERVICE_NO=b.SERVICE_NO GROUP BY c.`REGROUP_ID`";
   
// 		$req=$bdd->query($query, PDO::FETCH_ASSOC);
// 		$resultats=$req->fetchAll();
		
// 	    foreach($resultats as $resultat )
// 		{
// 		    		array_push($result,(object)$resultat);
// 		}
// 		print(json_encode($result, JSON_PRETTY_PRINT));
// 		$req->closeCursor();
    
// 	}
		
// 	if (isset($_GET["getInformationsContract"])) {
//      	if (isset($_GET["SERVICE_NO"]) && !empty($_GET["SERVICE_NO"])) 
// 	    {
// 	    $query="SELECT * FROM `contract` WHERE `SERVICE_NO`='".$_GET["SERVICE_NO"]."'";
   
// 		$req=$bdd->query($query, PDO::FETCH_ASSOC);
// 		$resultat=$req->fetchAll();
// 		print(json_encode((object)$resultat[0], JSON_PRETTY_PRINT));
// 		$req->closeCursor();
//     	}
// 	}	
		
		
//     if (isset($_GET["getListInformationsContratsByTutelle"])) 
// 	{
// 	  if (isset($_GET["TUTELLE"]) && !empty($_GET["TUTELLE"])) 
// 	    {
// 	    $result = array();
// 	    $query="SELECT 
// 	            b.SERVICE_NO,
// 	            b.VOLT_TP_ID as TL ,
// 	            '' AS STATUT, c.TUTELLE, 
// 	            '' AS ANNOTATION,
// 	            b.AGENCE, 
// 	            0 AS AVOIR, 
// 	            b.CUST_NAME, 
// 	            SUM(IFNULL(b.CONSUMPTION,0)) as SOMME, 
// 	            SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE , 
// 	            COUNT(IFNULL(b.SERVICE_NO,0)) AS NOMBRE 
// 	            FROM bill b, 
// 	               contract c 
// 	           WHERE c.SERVICE_NO=b.SERVICE_NO    
// 	           AND 
//              c.TUTELLE='".$_GET['TUTELLE']."' 
//              GROUP BY c.SERVICE_NO ORDER BY `b`.`VOLT_TP_ID` DESC";
          
// 		  $req=$bdd->query($query, PDO::FETCH_ASSOC);

// 		while($resultat=$req->fetch()){
// 			array_push($result,(object)$resultat);
//       	}

// 		print(json_encode($result, JSON_PRETTY_PRINT));
// 		$req->closeCursor();
// 	    }
// 	    else
// 	    {
// 	        $result = array();
// 	  $query="SELECT 
// 	            b.SERVICE_NO,
// 	            b.VOLT_TP_ID as TL ,
// 	            '' AS STATUT, c.TUTELLE, 
// 	            '' AS ANNOTATION,
// 	            b.AGENCE, 
// 	            0 AS AVOIR, 
// 	            b.CUST_NAME, 
// 	            SUM(IFNULL(b.CONSUMPTION,0)) as SOMME, 
// 	            SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE , 
// 	            COUNT(IFNULL(b.SERVICE_NO,0)) AS NOMBRE 
// 	            FROM bill b, 
// 	               contract c
// 	           WHERE c.SERVICE_NO=b.SERVICE_NO  GROUP BY c.SERVICE_NO ORDER BY `b`.`VOLT_TP_ID` DESC";
          
// 		  $req=$bdd->query($query);

// 		while($resultat=$req->fetch()){
//                 array_push($result,(object)$resultat);
// 		}
// 		print(json_encode($result, JSON_PRETTY_PRINT));
// 		$req->closeCursor(); 
// 	    }
	  
// 	}
// 		if (isset($_GET["getListInformationsContrats"])) 
// 	{
	 
// 	    $result = array();
// 	  $query="SELECT 
// 	            b.SERVICE_NO,
// 	            b.VOLT_TP_ID ,
// 	            '' AS Statut, c.TUTELLE, 
// 	            '' AS Annotation,
// 	            b.AGENCE, 
// 	            0 AS Avoir, 
// 	            b.CUST_NAME, 
// 	            SUM(IFNULL(b.CONSUMPTION,0)) as SOMME, 
// 	            SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE , 
// 	            COUNT(IFNULL(b.SERVICE_NO,0)) AS NOMBRE 
// 	            FROM bill b, 
// 	               contract c 
// 	           WHERE c.SERVICE_NO=b.SERVICE_NO  GROUP BY c.SERVICE_NO ORDER BY `b`.`VOLT_TP_ID` DESC";
          
// 		  $req=$bdd->query($query);
		
// 		while($resultat=$req->fetch()){
// 			array_push($result,array(
// 		        'SERVICE_NO' => $resultat["SERVICE_NO"],
// 			    'TL' => $resultat["VOLT_TP_ID"],
// 			    'STATUT' => $resultat["Statut"],
// 			    'TUTELLE' => $resultat["TUTELLE"],
// 			    'ANNOTATION' => $resultat["Annotation"],
// 			    'AGENCE' => $resultat["AGENCE"],
// 			    'AVOIR' => $resultat["Avoir"],
// 			    'CUST_NAME' => $resultat["CUST_NAME"]
			    
// 			));
// 		}
// // 		echo "<script>console.log( json_encode($result, JSON_PRETTY_PRINT))</script>";
// 		print(json_encode($result, JSON_PRETTY_PRINT));
// 		$req->closeCursor();
	  
// 	}
	
// 	if (isset($_GET["getGlobalInformationsInstitutionsPerMonth"])) 
// 	{
// 	    if (isset($_GET["DISPATCH_DATE"])) 
// 	    {
// 	    $result = array();
// 	  $query="
// 	  SELECT 
//      f.MONTHS,
//      f.SOMME,
//      f.PRICE,
//      f.NOMBRE,
//      mv.SOMMEMV,
//      mv.PRICEMV,
//      mv.NOMBREMV,
//      lv.SOMMELV,
//      lv.PRICELV,
//      lv.NOMBRELV
//      FROM 
//      (SELECT 
//           CONCAT(MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y')),'/',YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))) AS MONTHS,
//      	   SUM(IFNULL(bc.CONSUMPTION,0)) as SOMME,
//           SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICE,
//           COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBRE
//      From 
//      	bill bc
//      WHERE 
//         YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
//          GROUP BY MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))) f,
//     (SELECT 
//           CONCAT(MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y')),'/',YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))) AS MONTHS,
//      	   SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMELV,
//           SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICELV,
//           COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBRELV
//      From 
//      	bill bc
//      WHERE 
//         YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
//           AND bc.VOLT_TP_ID='LV' GROUP BY MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))) lv,
          
//     (SELECT 
//           CONCAT(MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y')),'/',YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))) AS MONTHS,
//      	   SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMEMV,
//           SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICEMV,
//           COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBREMV
//      From 
//      	bill bc
//      WHERE 
//         YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
//           AND bc.VOLT_TP_ID='MV' GROUP BY MONTH(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))) mv
//           WHERE lv.MONTHS=mv.MONTHS and f.MONTHS=mv.MONTHS";
          
// 		  $req=$bdd->query($query);
		
// 		while($resultat=$req->fetch()){
// 			array_push($result,array(
// 			    'MONTHS' => $resultat["MONTHS"],
// 			    'SOMME' => $resultat["SOMME"],
// 			    'PRICE' => $resultat["PRICE"],
// 			    'NOMBRE' => $resultat["NOMBRE"],
// 			    'SOMMELV' => $resultat["SOMMELV"],
// 			    'PRICELV' => $resultat["PRICELV"],
// 			    'NOMBRELV' => $resultat["NOMBRELV"],
// 			    'SOMMEMV' => $resultat["SOMMEMV"],
// 			    'PRICEMV' => $resultat["PRICEMV"],
// 			    'NOMBREMV' => $resultat["NOMBREMV"],
			    
// 			));
// 		}
// // 		echo "<script>console.log( json_encode($result, JSON_PRETTY_PRINT))</script>";
// 		print(json_encode($result, JSON_PRETTY_PRINT));
// 		$req->closeCursor();
// 	    }
// 	}
	
	
	
	
	
// 	if(isset($_GET['getGlobalInformationsInstitutions']))
// 	{
// 	    if(isset($_GET["DISPATCH_DATE"]))
// 	    {
// 	        $query="SELECT 
//      SUM(IFNULL(b.CONSUMPTION,0)) as SOMME,
//      SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE, 
//      COUNT(DISTINCT IFNULL(b.`SERVICE_NO`,0)) AS NOMBRE, 
//      lv.SOMMELV,
//      mv.SOMMEMV,
//      lv.PRICELV,
//      mv.PRICEMV,
//      lv.NOMBRELV,
//      mv.NOMBREMV,
//      4200 as ACTIF
//   FROM 
//   bill b,
//   (SELECT 
//      	   SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMELV,
//           SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICELV,
//           COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBRELV
//      From 
//      	bill bc
//      WHERE 
//         YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
//           AND bc.VOLT_TP_ID='LV') lv, 
//     (SELECT 
//           SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMEMV,
//           SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICEMV,
//           COUNT(DISTINCT IFNULL(bc.`SERVICE_NO`,0)) as NOMBREMV 
//      From 
//      	bill bc
//      WHERE 
//         YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
//           AND bc.VOLT_TP_ID='MV') mv,
//           (SELECT COUNT(IFNULL(b.`SERVICESTATUS`,0)) as ACTIF FROM bill b WHERE b.SERVICESTATUS='ACTIVE' and YEAR(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE'].") act 
// WHERE 
//   YEAR(STR_TO_DATE(`DISPATCH_DATE`,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE'];
   
//         $req=$bdd->query($query);
// 		$resultat=$req->fetch();
// 		print(json_encode(array(
// 			    'SOMMES' => $resultat["SOMME"],
// 			    'SOMMESLV' => $resultat["SOMMELV"],
// 			    'SOMMESMV' => $resultat["SOMMEMV"],
// 			    'NOMBRE' => $resultat["NOMBRE"],
// 			    'NOMBRELV' => $resultat["NOMBRELV"],
// 			    'NOMBREML' => $resultat["NOMBREMV"],
// 			    'PRICE' => $resultat["PRICE"],
// 			    'PRICEMV' => $resultat["PRICEMV"],
// 			    'ACTIF' => $resultat["ACTIF"],
// 			    'PRICELV' => $resultat["PRICELV"]
// 			), JSON_PRETTY_PRINT));
// 		$req->closeCursor();
   
// 	    }
// 	}
	
	
// 	if (isset($_GET["getInformationsInstitutionsContractByTutelleAndDateAndTag"])) {
// 	    if (isset($_GET["TUTELLE"]) && isset($_GET["DISPATCH_DATE"])) 
// 	    {
// 	    $result = array();
// 	    $query="SELECT 
// 	                 b.SERVICE_NO,
// 	                  b.`AGENCE`,
// 	                  b.`VOLT_TP_ID`,
// 	                 b.CUST_NAME, 
// 	                 SUM(IFNULL(b.CONSUMPTION,0)) as SOMME,
// 	                 SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE , 
// 	                 COUNT(IFNULL(b.SERVICE_NO,0)) AS NOMBRE
// 	            FROM 
// 	               bill b, 
// 	               contract c 
// 	            WHERE 
// 	               c.SERVICE_NO=b.SERVICE_NO 
// 	                 and 
// 	               c.TUTELLE='".$_GET['TUTELLE']."' 
// 	                 AND 
// 	               YEAR(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']." 
// 	            GROUP BY c.SERVICE_NO";
// 		  //$req=$bdd->query($query);
// 			  $req=$bdd->query($query, PDO::FETCH_ASSOC);

// 		while($resultat=$req->fetch()){
// 			array_push($result,(object)$resultat);
//       	}
// // 		echo "<script>console.log( json_encode($result, JSON_PRETTY_PRINT))</script>";
// 		print(json_encode($result, JSON_PRETTY_PRINT));
// 		$req->closeCursor();
// 	    }
// 	}

	   
// 	if (isset($_GET["getInformationsInstitutionsContractByTutelleAndDate"])) {
// 	    if ( isset($_GET["ID_TAGS"]) && !empty($_GET["ID_TAGS"])) 
// 	    {
// 	        if( $_GET["ID_TAGS"]==='GLOBAL')
// 	        {
// 	          if (isset($_GET["TUTELLE"]) && isset($_GET["DISPATCH_DATE"])) 
// 	           {
// 	    			$result = array();
// 	    			$query="SELECT 
// 	                 b.SERVICE_NO,
// 	                  b.`AGENCE`,
// 	                  b.`VOLT_TP_ID`,
// 	                 b.CUST_NAME, 
// 	                 SUM(IFNULL(b.CONSUMPTION,0)) as SOMME,
// 	                 SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE , 
// 	                 COUNT(IFNULL(b.SERVICE_NO,0)) AS NOMBRE
// 	           		 FROM 
// 	               bill b, 
// 	               contract c 
// 	            	WHERE 
// 	               c.SERVICE_NO=b.SERVICE_NO 
// 	                 and 
// 	               c.TUTELLE='".$_GET['TUTELLE']."' 
// 	                 AND 
// 	               YEAR(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']." 
// 	         	   GROUP BY c.SERVICE_NO";
// 				  //$req=$bdd->query($query);
// 				 echo $query;
// 				//   $req=$bdd->query($query, PDO::FETCH_ASSOC);

// 				// while($resultat=$req->fetch()){
// 				// array_push($result,(object)$resultat);
//     //   			}
		
// 				// 	print(json_encode($result, JSON_PRETTY_PRINT));
// 				// 	$req->closeCursor();
// 	  		  }
// 	        }
// 	       else if(isset($_GET["ID_TAGS"]) && !empty($_GET["ID_TAGS"]) && isset($_GET["DISPATCH_DATE"]))
// 	        {


// 	              $result = array();
// 	               $query="SELECT 
// 	                 b.SERVICE_NO,
// 	                  b.`AGENCE`,
// 	                  b.`VOLT_TP_ID`,
// 	                 b.CUST_NAME, 
// 	                 SUM(IFNULL(b.CONSUMPTION,0)) as SOMME,
// 	                 SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE , 
// 	                 COUNT(IFNULL(b.SERVICE_NO,0)) AS NOMBRE
// 	            FROM 
// 	               bill b, 
// 	               contract c ,
//         contrat_possede_tags cpt
// 	            WHERE 
// 	               c.SERVICE_NO=b.SERVICE_NO 
// 	                 and 
// 				   b.SERVICE_NO IN(
// 					SELECT
// 						SERVICE_NO
// 					FROM
// 						contrat_possede_tags
// 					WHERE
// 						ID_TAGS = '".$_GET['ID_TAGS']."'
// 				) AND
	                  
// 	               YEAR(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']." 
// 	                AND 
//                   c.SERVICE_NO=cpt.SERVICE_NO 
// 	            GROUP BY c.SERVICE_NO";
// 	echo $query;
// // 			  $req=$bdd->query($query, PDO::FETCH_ASSOC);

// // 		while($resultat=$req->fetch()){
// // 			array_push($result,(object)$resultat);
// //       	}

// // 		print(json_encode($result, JSON_PRETTY_PRINT));
// // 		$req->closeCursor();  
// 	        }
// 	    }
	   
// 	}

// 	function getData($query,$parrams)
// 	{
// 		// echo ($query);
// 		include("connect_bd.php");
// 		$result=array();
// 		if(empty($parrams))
// 		{
// 		  $req=$bdd->query($query, PDO::FETCH_ASSOC);

// 			while($resultat=$req->fetch()){
// 				array_push($result,$resultat);
// 			  }
// 		}
// 		else
// 		{
		
// 			$req=$bdd->prepare($query );
//             $req->execute($parrams);
// 			while($resultat=$req->fetch(PDO::FETCH_ASSOC)){
// 				array_push($result,$resultat);
// 			  }
// 		}
// 		return $result;
// 	}
// 	function implode_key($glue, $arr, $key){
// 		$arr2=array();
// 		foreach($arr as $f){
// 			if(!isset($f[$key])) continue;
// 			$arr2[]=$f[$key];
// 		}
// 		return implode($glue, $arr2);
// 	}
	
// 	if (isset($_GET["getInformationsInstitutionsByTutelleAndDate"])) 
// 	{

// 		if(isset($_GET["DISPATCH_DATE"]) && !empty($_GET["DISPATCH_DATE"]) && $_GET["DISPATCH_DATE"]=='2023')
// 		{
//             if((isset($_GET['ID_TAGS'])  && $_GET['ID_TAGS']=='GLOBAL'))
// 			{
// 				$queryAll="SELECT DISTINCT b.`SERVICE_NO` FROM `bill` b, contract c WHERE c.SERVICE_NO=b.SERVICE_NO";
// 				$queryLV="SELECT DISTINCT b.`SERVICE_NO` FROM `bill` b, contract c WHERE c.SERVICE_NO=b.SERVICE_NO AND bc.VOLT_TP_ID = 'LV'";
// 				$queryMV="SELECT DISTINCT b.`SERVICE_NO` FROM `bill` b, contract c WHERE c.SERVICE_NO=b.SERVICE_NO AND bc.VOLT_TP_ID = 'MV'";
// 				$datas = array("contrats_group"=>"date","date"=>2023);
// 				var_dump($datas);
				

// 			}
// 			else if(isset($_GET['ID_TAGS']))
// 			{

// 				$queryAll="SELECT DISTINCT  b.`SERVICE_NO`,c.TUTELLE,		(
// 					SELECT
// 						COUNT(*)
// 					FROM
// 						(
// 						SELECT DISTINCT
// 							c.`SERVICE_NO`,
// 							c.`STATUS`
// 						FROM
// 							`contract` c,
// 							bill b
// 						WHERE
// 							c.SERVICE_NO = b.SERVICE_NO AND b.SERVICE_NO IN(
// 							SELECT
// 								SERVICE_NO
// 							FROM
// 								contrat_possede_tags
// 							WHERE
// 								ID_TAGS = '".$_GET['ID_TAGS']."'
// 						)
// 						) AS actif
// 						WHERE
// 						actif.STATUS = 'ACTIVE'
// 						) AS ACTIF FROM `bill` b, contract c WHERE c.SERVICE_NO=b.SERVICE_NO and c.SERVICE_NO IN( SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS = :ID_TAGS);";
// 				$queryLV="SELECT DISTINCT  b.`SERVICE_NO`,c.TUTELLE FROM `bill` b, contract c WHERE c.SERVICE_NO=b.SERVICE_NO and c.SERVICE_NO IN( SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS = :ID_TAGS ) AND b.VOLT_TP_ID = 'LV';";
// 				$queryMV="SELECT DISTINCT  b.`SERVICE_NO`,c.TUTELLE FROM `bill` b, contract c WHERE c.SERVICE_NO=b.SERVICE_NO and c.SERVICE_NO IN( SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS = :ID_TAGS) AND b.VOLT_TP_ID = 'MV';";
// 				$params=[':ID_TAGS' => $_GET['ID_TAGS']];
// 				// $datas = array("contrats_group"=>"date","date"=>2023);
				
// 				$ResultAll=getData($queryAll,$params);
// 				$ResultMV=getData($queryMV,$params);
// 				$ResultLV=getData($queryLV,$params);
// 				// var_dump();
				
// 				$contrats_groupAll=implode_key(",", $ResultAll,"SERVICE_NO");
// 				$datasAll = array("contrat_bills_group"=>$contrats_groupAll,"date"=>2023);
// 				$contrats_groupMV=implode_key(",", $ResultMV,"SERVICE_NO");
// 				$datasMV = array("contrat_bills_group"=>$contrats_groupMV,"date"=>2023);
// 				$contrats_groupLV=implode_key(",", $ResultLV,"SERVICE_NO");
// 				$datasLV = array("contrat_bills_group"=>$contrats_groupLV,"date"=>2023);
//                 // var_dump( $datasLV);
// 				$All=json_decode(postApiInfos("https://ppsr.eneoapps.com/apiContrat/serverApi.php",$datasAll));
// 				$MV=json_decode(postApiInfos("https://ppsr.eneoapps.com/apiContrat/serverApi.php",$datasMV));
// 				$LV=json_decode(postApiInfos("https://ppsr.eneoapps.com/apiContrat/serverApi.php",$datasLV));
// 				// 
// 				// echo "<script>console.log($contrats_groupAll)</script>";
// 				// print_r($contrats_groupAll);
// 			    print(json_encode(array(
// 					'TUTELLE' => $ResultAll[0]["TUTELLE"]??'',
// 					'SOMMES' => $All[0]->total_conso??0,
// 					'SOMMESLV' =>$LV[0]->total_conso??0,
// 					'SOMMESMV' =>$MV[0]->total_conso??0,
// 					'NOMBRE' => $All[0]->total_contrat??count($ResultAll),
// 					'NOMBRELV' => $LV[0]->total_contrat??count($ResultLV),
// 					'NOMBREML' =>$MV[0]->total_contrat??count($ResultMV),
// 					'PRICE' => $All[0]->total_facture??0,
// 					'PRICEMV' => $MV[0]->total_facture??0,
// 					'PRICELV' => $LV[0]->total_facture??0,
// 					'ACTIF' => $ResultAll[0]["ACTIF"]??1200
// 				), JSON_PRETTY_PRINT));
// 			// $req->closeCursor();
// 			}
// 			else if ((isset($_GET["TUTELLE"]) and !empty($_GET["TUTELLE"])))
// 			{
// 				$queryAll="SELECT DISTINCT  b.`SERVICE_NO`,c.TUTELLE,		 (
// 					SELECT
// 						COUNT(*)
// 					FROM
// 						(
// 						SELECT DISTINCT
// 							c.`SERVICE_NO`,
// 							c.`STATUS`
// 						FROM
// 							`contract` c,
// 							bill b
// 						WHERE
// 							c.SERVICE_NO = b.SERVICE_NO  AND 
// 						c.TUTELLE='".$_GET['TUTELLE']."'
// 							) AS actif
// 						WHERE
// 							actif.STATUS = 'ACTIVE'
// 						) AS ACTIF
						
// 						FROM `bill` b, contract c WHERE c.SERVICE_NO=b.SERVICE_NO  AND c.TUTELLE=:TUTELLE;";
// 				$queryLV="SELECT DISTINCT  b.`SERVICE_NO`,c.TUTELLE FROM `bill` b, contract c WHERE c.SERVICE_NO=b.SERVICE_NO  AND c.TUTELLE=:TUTELLE AND b.VOLT_TP_ID = 'LV';";
// 				$queryMV="SELECT DISTINCT  b.`SERVICE_NO`,c.TUTELLE FROM `bill` b, contract c WHERE c.SERVICE_NO=b.SERVICE_NO  AND c.TUTELLE=:TUTELLE AND b.VOLT_TP_ID = 'MV';";
// 				$params=[':TUTELLE' => $_GET['TUTELLE']];
// 				// $datas = array("contrats_group"=>"date","date"=>2023);
				
// 				$ResultAll=getData($queryAll,$params);
// 				$ResultMV=getData($queryMV,$params);
// 				$ResultLV=getData($queryLV,$params);
// 				// var_dump();
				
// 				$contrats_groupAll=implode_key(",", $ResultAll,"SERVICE_NO");
// 				$datasAll = array("contrat_bills_group"=>$contrats_groupAll,"date"=>2023);
// 				$contrats_groupMV=implode_key(",", $ResultMV,"SERVICE_NO");
// 				$datasMV = array("contrat_bills_group"=>$contrats_groupMV,"date"=>2023);
// 				$contrats_groupLV=implode_key(",", $ResultLV,"SERVICE_NO");
// 				$datasLV = array("contrat_bills_group"=>$contrats_groupLV,"date"=>2023);
//                 // var_dump( $datasLV);
// 				$All=json_decode(postApiInfos("https://ppsr.eneoapps.com/apiContrat/serverApi.php",$datasAll));
// 				$MV=json_decode(postApiInfos("https://ppsr.eneoapps.com/apiContrat/serverApi.php",$datasMV));
// 				$LV=json_decode(postApiInfos("https://ppsr.eneoapps.com/apiContrat/serverApi.php",$datasLV));
// 				// 
// 				// echo "<script>console.log($contrats_groupAll)</script>";
// 				// print_r($contrats_groupAll);
// 			    print(json_encode(array(
// 					'TUTELLE' => $ResultAll[0]["TUTELLE"]??'',
// 					'SOMMES' => $All[0]->total_conso??0,
// 					'SOMMESLV' =>$LV[0]->total_conso??0,
// 					'SOMMESMV' =>$MV[0]->total_conso??0,
// 					'NOMBRE' => $All[0]->total_contrat??count($ResultAll),
// 					'NOMBRELV' => $LV[0]->total_contrat??count($ResultLV),
// 					'NOMBREML' =>$MV[0]->total_contrat??count($ResultMV),
// 					'PRICE' => $All[0]->total_facture??0,
// 					'PRICEMV' => $MV[0]->total_facture??0,
// 					'PRICELV' => $LV[0]->total_facture??0,
// 					'ACTIF' => $ResultAll[0]["ACTIF"]??1200
// 				), JSON_PRETTY_PRINT));
// 			}
            
// 		}
// 		else if((isset($_GET['ID_TAGS'])  && $_GET['ID_TAGS']!='GLOBAL') && isset($_GET["DISPATCH_DATE"]) )
// 		{
// 			$query="SELECT
// 			SUM(IFNULL(b.CONSUMPTION, 0)) AS SOMME,
// 			SUM(IFNULL(b.AMOUNT_WITH_TAX, 0)) AS PRICE,
// 			COUNT(DISTINCT b.`SERVICE_NO`) AS NOMBRE,
// 			lv.SOMMELV,
// 			mv.SOMMEMV,
// 			lv.PRICELV,
// 			mv.PRICEMV,
// 			lv.NOMBRELV,
// 			mv.NOMBREMV,
// 			c.TUTELLE,
// 			(
//     SELECT
//         COUNT(*)
//     FROM
//         (
//         SELECT DISTINCT
//             c.`SERVICE_NO`,
//             c.`STATUS`
//         FROM
//             `contract` c,
//             bill b
//         WHERE
//             c.SERVICE_NO = b.SERVICE_NO AND b.SERVICE_NO IN(
//             SELECT
//                 SERVICE_NO
//             FROM
//                 contrat_possede_tags
//             WHERE
//                 ID_TAGS = '".$_GET['ID_TAGS']."'
//         ) AND YEAR(
//             STR_TO_DATE(b.DISPATCH_DATE, '%m/%d/%Y')
//         ) = ".$_GET['DISPATCH_DATE']."
// 		) AS actif
// 		WHERE
// 		actif.STATUS = 'ACTIVE'
// 		) AS ACTIF	
// 			FROM
// 			bill b,
// 			contract c,
// 			(
// 			SELECT
// 				SUM(IFNULL(bc.CONSUMPTION, 0)) AS SOMMELV,
// 				SUM(IFNULL(bc.AMOUNT_WITH_TAX, 0)) AS PRICELV,
// 				COUNT(DISTINCT bc.`SERVICE_NO`) AS NOMBRELV
// 			FROM
// 				bill bc
// 			WHERE
// 				bc.SERVICE_NO IN(
// 				SELECT
// 					SERVICE_NO
// 				FROM
// 					contrat_possede_tags
// 				WHERE
// 					ID_TAGS = '".$_GET['ID_TAGS']."'
// 			) AND bc.VOLT_TP_ID = 'LV' AND YEAR(
// 				STR_TO_DATE(bc.DISPATCH_DATE, '%m/%d/%Y')
// 			) = '".$_GET['DISPATCH_DATE']."'
// 		) lv,
// 		(
// 			SELECT
// 				SUM(IFNULL(bc.CONSUMPTION, 0)) AS SOMMEMV,
// 				SUM(IFNULL(bc.AMOUNT_WITH_TAX, 0)) AS PRICEMV,
// 				COUNT(DISTINCT bc.`SERVICE_NO`) AS NOMBREMV
// 			FROM
// 				bill bc
// 			WHERE
// 				bc.SERVICE_NO IN(
// 				SELECT
// 					SERVICE_NO
// 				FROM
// 					contrat_possede_tags
// 				WHERE
// 					ID_TAGS = '".$_GET['ID_TAGS']."'
// 			) AND bc.VOLT_TP_ID = 'MV' AND YEAR(
// 				STR_TO_DATE(bc.DISPATCH_DATE, '%m/%d/%Y')
// 			) = '".$_GET['DISPATCH_DATE']."'
// 		) mv
// 		WHERE
// 		   c.SERVICE_NO = b.SERVICE_NO AND 
// 			b.SERVICE_NO IN(
// 			SELECT
// 				SERVICE_NO
// 			FROM
// 				contrat_possede_tags
// 			WHERE
// 				ID_TAGS = '".$_GET['ID_TAGS']."'
// 		) AND YEAR(
// 			STR_TO_DATE(b.DISPATCH_DATE, '%m/%d/%Y')
// 		) = '".$_GET['DISPATCH_DATE']."'";

		
      

// 		$req=$bdd->query($query);
// 		$resultat=$req->fetch();
// 		print(json_encode(array(
// 			    'TUTELLE' => $resultat["TUTELLE"],
// 			    'SOMMES' => $resultat["SOMME"],
// 			    'SOMMESLV' => $resultat["SOMMELV"],
// 			    'SOMMESMV' => $resultat["SOMMEMV"],
// 			    'NOMBRE' => $resultat["NOMBRE"],
// 			    'NOMBRELV' => $resultat["NOMBRELV"],
// 			    'NOMBREML' => $resultat["NOMBREMV"],
// 			    'PRICE' => $resultat["PRICE"],
// 			     'ACTIF' => $resultat["ACTIF"],
// 			    'PRICEMV' => $resultat["PRICEMV"],
// 			    'PRICELV' => $resultat["PRICELV"]
// 			), JSON_PRETTY_PRINT));
// 		$req->closeCursor();
// 		}
//      	else if ((isset($_GET["TUTELLE"]) and !empty($_GET["TUTELLE"])) && isset($_GET["DISPATCH_DATE"])) 
// 	    {
// 	    $query="SELECT 
//      SUM(IFNULL(b.CONSUMPTION,0)) as SOMME,
//      SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE, 
//      COUNT( DISTINCT b.`SERVICE_NO`) AS NOMBRE, 
//      lv.SOMMELV,
//      mv.SOMMEMV,
//      lv.PRICELV,
//      mv.PRICEMV,
//      lv.NOMBRELV,
//      mv.NOMBREMV,
//      c.TUTELLE,
     
// 	 (
//     SELECT
//         COUNT(*)
//     FROM
//         (
//         SELECT DISTINCT
//             c.`SERVICE_NO`,
//             c.`STATUS`
//         FROM
//             `contract` c,
//             bill b
//         WHERE
//             c.SERVICE_NO = b.SERVICE_NO  AND 
//         c.TUTELLE='".$_GET['TUTELLE']."'
// 		 AND YEAR(
//             STR_TO_DATE(b.DISPATCH_DATE, '%m/%d/%Y')
//         ) = ".$_GET['DISPATCH_DATE']."
// 			) AS actif
// 		WHERE
// 			actif.STATUS = 'ACTIVE'
// 		) AS ACTIF
// 		FROM 
//   bill b, 
//   contract c,
//   (SELECT 
//      	   SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMELV,
//           SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICELV,
//           COUNT(DISTINCT bc.`SERVICE_NO`) as NOMBRELV
//      From 
//      	bill bc, 
//     	contract ct 
//      WHERE 
//         ct.SERVICE_NO=bc.SERVICE_NO 
//           AND 
//         ct.TUTELLE='".$_GET['TUTELLE']."' 
//           AND 
//         YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']." 
//           AND bc.VOLT_TP_ID='LV') lv, 
//     (SELECT 
//           SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMEMV,
//           SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICEMV,
//           COUNT(DISTINCT bc.`SERVICE_NO`) as NOMBREMV 
//      From 
//      	bill bc, 
//     	contract ct 
//      WHERE 
//         ct.SERVICE_NO=bc.SERVICE_NO 
//           AND 
//         ct.TUTELLE='".$_GET['TUTELLE']."' 
//           AND 
//         YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
//           AND bc.VOLT_TP_ID='MV') mv
// WHERE 
//   c.SERVICE_NO=b.SERVICE_NO 
//      AND 
//   c.TUTELLE='".$_GET['TUTELLE']."' 
//      AND 
//   YEAR(STR_TO_DATE(`DISPATCH_DATE`,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE'];
// //    echo $query;
// 		$req=$bdd->query($query);
// 		$resultat=$req->fetch();
// 		print(json_encode(array(
// 			    'TUTELLE' => $resultat["TUTELLE"],
// 			    'SOMMES' => $resultat["SOMME"],
// 			    'SOMMESLV' => $resultat["SOMMELV"],
// 			    'SOMMESMV' => $resultat["SOMMEMV"],
// 			    'NOMBRE' => $resultat["NOMBRE"],
// 			    'NOMBRELV' => $resultat["NOMBRELV"],
// 			    'NOMBREML' => $resultat["NOMBREMV"],
// 			    'PRICE' => $resultat["PRICE"],
// 			     'ACTIF' => $resultat["ACTIF"],
// 			    'PRICEMV' => $resultat["PRICEMV"],
// 			    'PRICELV' => $resultat["PRICELV"]
// 			), JSON_PRETTY_PRINT));
// 		$req->closeCursor();
//     	}


// 	}
	
// 	if (isset($_GET["getInformationsInstitutionsByTutelleAndDateAndTag"])) 
// 	{
//      	if (isset($_GET["TUTELLE"]) && isset($_GET["DISPATCH_DATE"]) && isset($_GET["ID_TAGS"])) 
// 	    {
// 	    $query="SELECT 
//                  SUM(IFNULL(b.CONSUMPTION,0)) as SOMME,
//                  SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE, 
//                  COUNT(IFNULL(b.`SERVICE_NO`,0)) AS NOMBRE, 
//                  lv.SOMMELV,
//                  mv.SOMMEMV,
//                  lv.PRICELV,
//                  mv.PRICEMV,
//                  lv.NOMBRELV,
//                  mv.NOMBREMV,
//                  c.TUTELLE,
                 
// 				 (SELECT COUNT(*) FROM (SELECT DISTINCT c.`SERVICE_NO`, c.`STATUS` FROM `contract` c, bill b WHERE c.SERVICE_NO=b.SERVICE_NO and b.SERVICE_NO IN( SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS = 'MINSANTE' ) AND YEAR(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']." ) as actif WHERE actif.STATUS='ACTIVE') ACTIF
//             FROM 
//               bill b, 
//               contract c,
//                     contrat_possede_tags cpt,
//               (SELECT 
//                  	   SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMELV,
//                       SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICELV,
//                       COUNT(IFNULL(bc.`SERVICE_NO`,0)) as NOMBRELV
//                  From 
//                  	bill bc, 
//                 	contract ct ,
//                     contrat_possede_tags cpt
//                  WHERE 
//                     ct.SERVICE_NO=bc.SERVICE_NO 
//                       AND 
//                     ct.TUTELLE='".$_GET['TUTELLE']."' 
//                       AND 
//                     YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
//                       AND bc.VOLT_TP_ID='LV' AND 
//                       ct.SERVICE_NO=cpt.SERVICE_NO 
//                       And cpt.ID_TAGS='".$_GET['ID_TAGS']."' ) lv, 
//                 (SELECT 
//                       SUM(IFNULL(bc.CONSUMPTION,0)) as SOMMEMV,
//                       SUM(IFNULL(bc.AMOUNT_WITH_TAX,0)) AS PRICEMV,
//                       COUNT(IFNULL(bc.`SERVICE_NO`,0)) as NOMBREMV 
//                  From 
//                  	bill bc, 
//                 	contract ct ,
//                     contrat_possede_tags cpt
//                  WHERE 
//                     ct.SERVICE_NO=bc.SERVICE_NO 
//                       AND 
//                     ct.TUTELLE='".$_GET['TUTELLE']."' 
//                       AND 
//                     YEAR(STR_TO_DATE(bc.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']."
//                       AND bc.VOLT_TP_ID='MV' AND 
//                       ct.SERVICE_NO=cpt.SERVICE_NO 
//                       And cpt.ID_TAGS='".$_GET['ID_TAGS']."') mv
//             WHERE 
//               c.SERVICE_NO=b.SERVICE_NO 
//                  AND 
//               c.TUTELLE='".$_GET['TUTELLE']."' 
//                  AND 
//               YEAR(STR_TO_DATE(`DISPATCH_DATE`,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']." AND 
//                       c.SERVICE_NO=cpt.SERVICE_NO 
//                       And cpt.ID_TAGS='".$_GET['ID_TAGS']."'";



   
		
  
 
 
 
 
 
 
//   $req=$bdd->query($query);
// 		$resultat=$req->fetch();
// 		print(json_encode(array(
// 			    'TUTELLE' => $resultat["TUTELLE"],
// 			    'SOMMES' => $resultat["SOMME"],
// 			    'SOMMESLV' => $resultat["SOMMELV"],
// 			    'SOMMESMV' => $resultat["SOMMEMV"],
// 			    'NOMBRE' => $resultat["NOMBRE"],
// 			    'NOMBRELV' => $resultat["NOMBRELV"],
// 			    'NOMBREML' => $resultat["NOMBREMV"],
// 			    'PRICE' => $resultat["PRICE"],
// 			     'ACTIF' => $resultat["ACTIF"],
// 			    'PRICEMV' => $resultat["PRICEMV"],
// 			    'PRICELV' => $resultat["PRICELV"]
// 			), JSON_PRETTY_PRINT));
// 		$req->closeCursor();
//     	}
// 	}
	
// 	if (isset($_GET["getAllInformationsInstitutions"])) {
// 	    $result = array();
	    
// 	    if ( isset($_GET["DISPATCH_DATE"])) 
// 	    {
// 		$req=$bdd->query("SELECT SUM(IFNULL(b.CONSUMPTION,0)) as SOMMES,SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE, COUNT(IFNULL(b.BILL_NO,0)) AS NOMBRE, c.TUTELLE FROM bill b, contract c WHERE c.SERVICE_NO=b.SERVICE_NO AND  YEAR(STR_TO_DATE(b.DISPATCH_DATE,'%m/%d/%Y'))=".$_GET['DISPATCH_DATE']." GROUP BY c.TUTELLE");
		
// 		while($resultat=$req->fetch()){
		    
		   
// 			array_push($result,array(
// 			    'TUTELLE' => $resultat["TUTELLE"],
// 			    'SOMMES' => $resultat["SOMMES"],
// 			    'NOMBRE' => $resultat["NOMBRE"],
// 			    'PRICE' => $resultat["PRICE"]
// 			) );
// 	     	}
// 	    }
// 	    else
// 	    {
// 	        	$req=$bdd->query("SELECT SUM(IFNULL(b.CONSUMPTION,0)) as SOMMES,SUM(IFNULL(b.AMOUNT_WITH_TAX,0)) AS PRICE, COUNT(IFNULL(b.BILL_NO,0)) AS NOMBRE, c.TUTELLE FROM bill b, contract c WHERE c.SERVICE_NO=b.SERVICE_NO GROUP BY c.TUTELLE");
		
// 		while($resultat=$req->fetch()){
		    
		   
// 			array_push($result,array(
// 			    'TUTELLE' => $resultat["TUTELLE"],
// 			    'SOMMES' => $resultat["SOMMES"],
// 			    'NOMBRE' => $resultat["NOMBRE"],
// 			    'PRICE' => $resultat["PRICE"]
// 			) );
// 	     	}
// 	    }
	    
		
// // 		echo "<script>console.log( json_encode($result, JSON_PRETTY_PRINT))</script>";
// 		print(json_encode($result, JSON_PRETTY_PRINT));
// 		$req->closeCursor();
// 	}
	

	
// 	if (isset($_GET["getInformationInstitution"])) {
// 		$req=$bdd->query("SELECT SERVICE_NO,SUM(CONSUMPTION) AS sommes, COUNT(BILL_NO) AS nombre FROM bill WHERE SERVICE_NO = '".$_GET["getInformationInstitution"]."' GROUP BY SERVICE_NO");
// 		$resultat=$req->fetch();
// 		print('('.$resultat['SERVICE_NO'].')'.$_GET['nomInstitution'].'*'.$resultat['nombre'].'@'.$resultat['sommes'].'|');
// 		$req->closeCursor();
// 	}
	
// 	if (isset($_GET["getInstitution"])) {
// 	    $result = array();
// 		$req=$bdd->query('SELECT DISTINCT TUTELLE FROM contract');
// 		$resultat=$req->fetch();
// 		while($resultat=$req->fetch()) {
// 		    array_push($result,array(
// 			    'TUTELLE' => $resultat["TUTELLE"],
// 			));
// 		}
// 		print(json_encode($result, JSON_PRETTY_PRINT));
// 		$req->closeCursor();
// 	}
	
// 	if (isset($_GET["getNumeroContratFtInstitution"])) {
// 	    $result = array();
// 		$req=$bdd->query('SELECT SERVICE_NO,TUTELLE FROM contract');
// 		$resultat=$req->fetch();
// 		while($resultat=$req->fetch()) {
// 		    array_push($result,array(
// 			    'TUTELLE' => $resultat["TUTELLE"],
// 			    'SERVICE_NO' => $resultat["SERVICE_NO"]
// 			) );
// 		}
// 		print(json_encode($result, JSON_PRETTY_PRINT));
// 		$req->closeCursor();
// 	}
	
// 	if (isset($_GET["getConsoTotalWitchDate"])) {
// 		$req=$bdd->query("SELECT DISPATCH_DATE, SUM(CONSUMPTION) AS sommes FROM bill GROUP BY DISPATCH_DATE");
// 		while($resultat=$req->fetch()) {
// 			print($resultat['DISPATCH_DATE'].' = '.$resultat['sommes'].';');
// 		}
// 		$req->closeCursor();
// 	}
	
// 	if (isset($_GET["getConsoTotalWitchYearAndProfil"]) && isset($_GET["profil"])) {
// 		$req=$bdd->query("SELECT SUM(CONSUMPTION) AS sommes FROM bill WHERE DISPATCH_DATE LIKE '%".$_GET["getConsoTotalWitchYearAndProfil"]."' AND VOLT_TP_ID = '".$_GET["profil"]."'");
// 		$resultat=$req->fetch();
// 		print($resultat['sommes']);
// 		$req->closeCursor();
// 	}
	
// 	if (isset($_GET["getMontantWitchYearAndProfil"]) && isset($_GET["profil"])) {
// 		$req=$bdd->query("SELECT SUM(AMOUNT_WITH_TAX) AS sommes FROM bill WHERE DISPATCH_DATE LIKE '%".$_GET["getMontantWitchYearAndProfil"]."' AND VOLT_TP_ID = '".$_GET["profil"]."'");
// 		$resultat=$req->fetch();
// 		print($resultat['sommes']);
// 		$req->closeCursor();
// 	}
	
	
// 	if (isset($_GET["getContractWitchYearAndProfil"]) && isset($_GET["profil"])) {
// 		$req=$bdd->query("SELECT COUNT(DISTINCT SERVICE_NO) AS nombre FROM bill WHERE DISPATCH_DATE LIKE '%".$_GET["getContractWitchYearAndProfil"]."' AND VOLT_TP_ID = '".$_GET["profil"]."'");
// 		$resultat=$req->fetch();
// 		print($resultat['nombre']);
// 		$req->closeCursor();
// 	}
	
// 	if (isset($_GET["nombreFactureActive"])) {
// 		$req=$bdd->query("SELECT COUNT(DISTINCT SERVICE_NO) AS nombreFactureActive FROM bill WHERE DISPATCH_DATE LIKE '%".$_GET["nombreFactureActive"]."' AND SERVICESTATUS = 'ACTIVE'");
// 		$resultat=$req->fetch();
// 		print($resultat['nombreFactureActive']);
// 		$req->closeCursor();
// 	}
	
// 	if (isset($_GET["getConsoTotal"])) {
// 		$req=$bdd->query("SELECT SUM(CONSUMPTION) AS sommes FROM bill WHERE DISPATCH_DATE LIKE '%".$_GET["getConsoTotal"]."'");
// 		$resultat=$req->fetch();
// 		print($resultat['sommes']);
// 		$req->closeCursor();
// 	}
	
// 	if (isset($_GET["getMontantTotal"])) {
// 		$req=$bdd->query("SELECT SUM(AMOUNT_WITH_TAX) AS sommes FROM bill WHERE DISPATCH_DATE LIKE '%".$_GET["getMontantTotal"]."'");
// 		$resultat=$req->fetch();
// 		print($resultat['sommes']);
// 		$req->closeCursor();
// 	}
	
// 	if (isset($_GET["getNombreInstitution"])) {
// 		$req=$bdd->query('SELECT COUNT(DISTINCT TUTELLE) AS nombre FROM contract');
// 		$resultat=$req->fetch();
// 		print($resultat['nombre']);
// 		$req->closeCursor();
// 	}
	
// 	if (isset($_GET["getNombreContrat"])) {
// 		$req=$bdd->query("SELECT COUNT(DISTINCT SERVICE_NO) AS nombre FROM bill WHERE DISPATCH_DATE LIKE '%".$_GET["getNombreContrat"]."'");
// 		$resultat=$req->fetch();
// 		print($resultat['nombre']);
// 		$req->closeCursor();
// 	}
	
// 	if (isset($_GET["getAllContrats"])) {
// 		$result = array(); $myObj = null;
// 		$req=$bdd->query('SELECT ID_CONTRAT,UNITE,PROFIL,NEW_REGION,AGENCE,NAMES,CUSTOMER,SERVICE_NO,TYPE_CLIENT,NEW_GESTION,REGROUP_ID,REGROUP_NAME,TOT_KWH,METER_NUMBER,PREMISE_REF,STATUS,RECORD_FLAG,BILLING_ANOM,BILLING_ANOM_STATUS,PHONE_NUMBERS,NATURE_CLIENT,MMS_NUMBER,CREATED_AT FROM contrat');
// 		while($resultat=$req->fetch()){
// 			$result[] = array(
// 			    'ID_CONTRAT' => $resultat["ID_CONTRAT"],
// 			    'UNITE' => $resultat["UNITE"],
// 			    'PROFIL' => $resultat["PROFIL"],
// 			    'NEW_REGION' => $resultat["NEW_REGION"],
// 			    'AGENCE' => $resultat["AGENCE"],
// 			    'NAMES' => $resultat["NAMES"],
// 			    'CUSTOMER' => $resultat["CUSTOMER"],
// 			    'SERVICE_NO' => $resultat["SERVICE_NO"],
// 			    'TYPE_CLIENT' => $resultat["TYPE_CLIENT"],
// 			    'NEW_GESTION' => $resultat["NEW_GESTION"],
// 			    'REGROUP_ID' => $resultat["REGROUP_ID"],
// 			    'REGROUP_NAME' => $resultat["REGROUP_NAME"],
// 			    'TOT_KWH' => $resultat["TOT_KWH"],
// 			    'METER_NUMBER' => $resultat["METER_NUMBER"],
// 			    'PREMISE_REF' => $resultat["PREMISE_REF"],
// 			    'STATUS' => $resultat["STATUS"],
// 			    'RECORD_FLAG' => $resultat["RECORD_FLAG"],
// 			    'BILLING_ANOM' => $resultat["BILLING_ANOM"],
// 			    'BILLING_ANOM_STATUS' => $resultat["BILLING_ANOM_STATUS"],
// 			    'PHONE_NUMBERS' => $resultat["PHONE_NUMBERS"],
// 			    'NATURE_CLIENT' => $resultat["NATURE_CLIENT"],
// 			    'MMS_NUMBER' => $resultat["MMS_NUMBER"],
// 			    'CREATED_AT' => $resultat["CREATED_AT"]
// 			);
// 		}
// 		print(json_encode($result, JSON_PRETTY_PRINT));
// 		$req->closeCursor();
// 	}
	
// 	if (isset($_GET["getAllFactures"])) {
// 		$result = array(); $myObj = null;
// 		$req=$bdd->query('SELECT REGION,DIVISION,AGENCE,SERVICE_NO,SERVICESTATUS,CUST_NAME,METER_NUMBER,BILL_NO,DISPATCH_DATE,DUE_DATE,PREV_ACTUAL_READ,HHT_CURRENT_INDEX,CONSUMPTION,FK_CUST_CAT_ID,AMOUNT_WITHOUT_TAX,AMOUNT_WITH_TAX,DUE_AMT,VOLT_TP_ID,BILL_STATUS,PK_CUST_ID FROM bill');
// 		while($resultat=$req->fetch()){
// 			$result[] = array(
// 			    'REGION' => $resultat["REGION"],
// 			    'DIVISION' => $resultat["DIVISION"],
// 			    'AGENCE' => $resultat["AGENCE"],
// 			    'SERVICE_NO' => $resultat["SERVICE_NO"],
// 			    'SERVICESTATUS' => $resultat["SERVICESTATUS"],
// 			    'CUST_NAME' => $resultat["CUST_NAME"],
// 			    'METER_NUMBER' => $resultat["METER_NUMBER"],
// 			    'BILL_NO' => $resultat["BILL_NO"],
// 			    'DISPATCH_DATE' => $resultat["DISPATCH_DATE"],
// 			    'DUE_DATE' => $resultat["DUE_DATE"],
// 			    'PREV_ACTUAL_READ' => $resultat["PREV_ACTUAL_READ"],
// 			    'HHT_CURRENT_INDEX' => $resultat["HHT_CURRENT_INDEX"],
// 			    'CONSUMPTION' => $resultat["CONSUMPTION"],
// 			    'FK_CUST_CAT_ID' => $resultat["FK_CUST_CAT_ID"],
// 			    'AMOUNT_WITHOUT_TAX' => $resultat["AMOUNT_WITHOUT_TAX"],
// 			    'AMOUNT_WITH_TAX' => $resultat["AMOUNT_WITH_TAX"],
// 			    'DUE_AMT' => $resultat["DUE_AMT"],
// 			    'VOLT_TP_ID' => $resultat["VOLT_TP_ID"],
// 			    'BILL_STATUS' => $resultat["BILL_STATUS"],
// 			    'PK_CUST_ID' => $resultat["PK_CUST_ID"]
// 			);
// 		}
// 		print(json_encode($result, JSON_PRETTY_PRINT));
// 		$req->closeCursor();
// 	}
