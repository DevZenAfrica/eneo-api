	<?php
		include("connect_bd.php");
		header('Content-type:application/json;charset=utf-8');

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
					  
					  
												  for($i=1;$i<=12;$i++)
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
												   
												  
													  $query="SELECT DISTINCT  b.`SERVICE_NO` FROM `final_referentiele` b WHERE YEAR(b.SAVE_DATE)=2023";
														 $queryLV="SELECT DISTINCT  b.`SERVICE_NO` FROM `final_referentiele` b WHERE b.VOLTAGE = 'LV' and YEAR(b.SAVE_DATE)=2023"; 
														 $queryMV="SELECT DISTINCT  b.`SERVICE_NO` FROM `final_referentiele` b WHERE b.VOLTAGE = 'MV' and YEAR(b.SAVE_DATE)=2023;";
													
														
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
														 $query="SELECT DISTINCT  b.`SERVICE_NO` FROM `final_referentiele` b WHERE YEAR(b.SAVE_DATE)=2023 and MONTH(b.SAVE_DATE)=$i";
														 $queryLV="SELECT DISTINCT  b.`SERVICE_NO` FROM `final_referentiele` b WHERE b.VOLTAGE = 'LV' and YEAR(b.SAVE_DATE)=2023 and MONTH(b.SAVE_DATE)=$i"; 
														 $queryMV="SELECT DISTINCT  b.`SERVICE_NO` FROM `final_referentiele` b WHERE b.VOLTAGE = 'MV' and YEAR(b.SAVE_DATE)=2023 and MONTH(b.SAVE_DATE)=$i;";
													
														
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
					
															  		
															  if( intval($mois_en_cours)>=$i)
															  array_push($res,array(
																	'SOMMES' => $All->total_conso??0,
																	'SOMMESLV' =>$LV->total_conso??0,
																	'SOMMESMV' =>$MV->total_conso??0,
																	'NOMBRE' => intval( $All->nbr_contrat_facture??0),
																	'NOMBRELV' =>intval($LV->nbr_contrat_facture ??0),
																	'NOMBREML' =>intval($MV->nbr_contrat_facture ??0),
																	'PRICE' =>intval( $All->total_facture??0),
																	'PRICEMV' => intval($MV->total_facture??0),
																	'ACTIF' =>intval($All->contrats_actif??0),
																	'MONTH' =>$i,
																	'PRICELV' =>intval( $LV->total_facture??0),
																));
														} 
							
															print(json_encode($res, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
													
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
												  $query="SELECT DISTINCT  b.`SERVICE_NO` FROM `final_referentiele` b WHERE YEAR(b.SAVE_DATE)=2023";
												  $queryLV="SELECT DISTINCT  b.`SERVICE_NO` FROM `final_referentiele` b WHERE b.VOLTAGE = 'LV' and YEAR(b.SAVE_DATE)=2023"; 
												  $queryMV="SELECT DISTINCT  b.`SERVICE_NO` FROM `final_referentiele` b WHERE b.VOLTAGE = 'MV' and YEAR(b.SAVE_DATE)=2023;";
												  
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
													 $query="SELECT DISTINCT  b.`SERVICE_NO` FROM `final_referentiele` b WHERE YEAR(b.SAVE_DATE)=2023 and MONTH(b.SAVE_DATE)=$i";
													 $queryLV="SELECT DISTINCT  b.`SERVICE_NO` FROM `final_referentiele` b WHERE b.VOLTAGE = 'LV' and YEAR(b.SAVE_DATE)=2023 and MONTH(b.SAVE_DATE)=$i"; 
													 $queryMV="SELECT DISTINCT  b.`SERVICE_NO` FROM `final_referentiele` b WHERE b.VOLTAGE = 'MV' and YEAR(b.SAVE_DATE)=2023 and MONTH(b.SAVE_DATE)=$i;";
													 
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
											   
										  }
						  }

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
			 