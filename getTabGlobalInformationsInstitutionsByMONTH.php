<?php 
define('BASE_ENEO_API', 'https://qsi.consonaute.biz/TestEC/myel/xwsmel/EasyServiceProd/service_Express.php');
define('BASE_DISPATCH_DATE',2023);
$mois_en_cours = date("m");
if(isset($_GET['getTabGlobalInformationsInstitutionsByMONTH']))
{
	$res=array();
	if(isset($_GET["DISPATCH_DATE"]))
	{
		if($_GET["DISPATCH_DATE"]>=BASE_DISPATCH_DATE)
		{
			$dispatchDate=$_GET["DISPATCH_DATE"];
					
			//if(isset($_GET["MONTH"]) && !empty($_GET["MONTH"]))
					   
				if(isset($_GET["ID_TAGS"]) && !empty($_GET["ID_TAGS"]) && $_GET["ID_TAGS"]!="GLOBAL") 
				{
					$SemiSQL=" AND  b.SERVICE_NO IN( SELECT SERVICE_NO FROM contrat_possede_tags WHERE  ID_TAGS = '$_GET[ID_TAGS]') ";
				}else
				{
					$SemiSQL="";
				}
				$queryLV="SELECT DISTINCT  b.`SERVICE_NO` FROM `final_referentiele` b WHERE 1 $SemiSQL AND b.VOLTAGE = 'LV' and YEAR(b.SAVE_DATE)=".$dispatchDate; 
				$queryMV="SELECT DISTINCT  b.`SERVICE_NO` FROM `final_referentiele` b WHERE 1 $SemiSQL AND b.VOLTAGE = 'MV' and YEAR(b.SAVE_DATE)=".$dispatchDate;
			
				
				//   var_dump($queryLV);
				$dataLV=getData($queryLV,[]);
				$dataMV=getData($queryMV,[]);
				//$AllContrat= implode_key(',',getData($query,[]),'SERVICE_NO');
			
			
				$ContratLV= implode_key(',',$dataLV,'SERVICE_NO');
				$ContratMV= implode_key(',',$dataMV,'SERVICE_NO');
		
				//   echo $AllContrat; 
				$mois= isset($_GET["MONTH"])?format_month($_GET["MONTH"]):'';
			
				$LV  =json_decode(postApiInfos(BASE_ENEO_API , array('contrat_bills_group' =>$ContratLV ,'annees' => $dispatchDate,'mois' =>$mois ) ));
				$MV  =json_decode(postApiInfos(BASE_ENEO_API , array('contrat_bills_group' =>$ContratMV ,'annees' => $dispatchDate,'mois' =>$mois ) ));

	
				array_push($res,array(
					'SOMMES' => ($LV->total_conso+$MV->total_conso)??0,
					'SOMMESLV' =>$LV->total_conso??0,
					'SOMMESMV' =>$MV->total_conso??0,
					'NOMBRE' => (int) count($dataLV) + count($dataMV)?? $LV->nbr_contrat_facture+$MV->nbr_contrat_facture??0,
					'NOMBRELV' => (int) count($dataLV) ?? $LV->nbr_contrat_facture??0,
					'NOMBREML' =>(int) count($dataMV) ??$MV->nbr_contrat_facture??0,
					'PRICE' => $LV->total_facture+ $MV->total_facture??0,
					'PRICEMV' => $MV->total_facture??0,
					'PRICELV' => $LV->total_facture??0,
					'ACTIF' => $LV->contrats_actif+$MV->contrats_actif??0,
					'MONTH' => 0,
				));
				
				$tabContratMont=array();
				$query="SELECT DISTINCT  b.`SERVICE_NO`, b.VOLTAGE, DATE_FORMAT(b.SAVE_DATE, '%Y-%m') SAVE_DATE FROM `final_referentiele` b 
						WHERE 1 $SemiSQL AND YEAR(b.SAVE_DATE) >= $dispatchDate";
				$contractVoltageMonths=getData($query,[]);
			
				
				foreach($contractVoltageMonths as $val)
				{
					$mois = $val['SAVE_DATE'];
					$voltage= $val['VOLTAGE'];
					$tabContratMont[$mois][$voltage][]=$val['SERVICE_NO'];
					$tabContactVoltage[$voltage][]=$val['SERVICE_NO'];
				}
				
				$LV = json_decode(postApiInfos(BASE_ENEO_API,array('contrat_bills_group' =>implode(',',$tabContactVoltage['LV']) ,'annees' => $dispatchDate)));
				$MV = json_decode(postApiInfos(BASE_ENEO_API,array('contrat_bills_group' =>implode(',',$tabContactVoltage['MV']) ,'annees' => $dispatchDate)));
			 
				 
		
				$moisDataLV=(array)$LV->bills_mois;
				$moisDataMV=(array)$LV->bills_mois;
				foreach($tabContratMont AS $mois=>$valcontrat)
				{
					$LVmois=isset($moisDataLV[$mois]) ? (array)$moisDataLV[$mois] : array('total_conso'=>0,'nbr_contrat_facture'=>0,'total_facture'=>0, 'contrats_actif'=>0);
					$MVmois=isset($moisDataMV[$mois])?(array) $moisDataMV[$mois] : array('total_conso'=>0,'nbr_contrat_facture'=>0,'total_facture'=>0, 'contrats_actif'=>0);

					 
					array_push($res,array(
						'SOMMES' => (int)($MVmois['total_conso']+ $LVmois['total_conso']),
						'SOMMESLV' =>$LVmois['total_conso'],
						'SOMMESMV' =>$MVmois['total_conso'],
						'NOMBRE' =>(int)  ($MVmois['nbr_contracts']+ $LVmois['nbr_contracts']),
						'NOMBRELV' =>(int) $LVmois['nbr_contracts'],
						'NOMBREML' =>(int) $MVmois['nbr_contracts'],
						'PRICE' => (int)($MVmois['total_facture']+ $LVmois['total_facture']),
						'PRICELV' =>(int) $LVmois['total_facture'],
						'PRICEMV' =>(int) $MVmois['total_facture'],
						'ACTIF' =>(int) $LVmois['nbr_contracts']+(int) $MVmois['nbr_contracts'],
						//'ACTIF' => (int)($MVmois['contrats_actif']+ $LVmois['contrats_actif']),
						'MONTH' =>$mois,
					));
				}

				print(json_encode($res, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
				
			
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
							for($i=0;$i<$end;$i++)
							{
							array_push($res,array(
								'MONTH' =>($i+1),
								'SOMMES' => $resultaT[$i]["SOMME"]??0,
								'SOMMESLV' =>  $resultaLV[$i]["SOMMELV"]??0,
								'SOMMESMV' =>  $resultaMV[$i]["SOMMEMV"]??0,
								'NOMBRE' =>$resultaT[$i]["NOMBRE"]??0,
								'NOMBRELV' =>  $resultaLV[$i]["NOMBRELV"]??0,
								'NOMBREML' => $resultaMV[$i]["NOMBREMV"]??0,
								'PRICE' => $resultaT[$i]["PRICE"]??0,
								'PRICEMV' => $resultaMV[$i]["PRICEMV"]??0,
								'ACTIF' =>$resultaACTIF[$i]["ACTIF"]??0,
								
								'PRICELV' => $resultaLV[$i]["PRICELV"]??0
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