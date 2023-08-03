<?php

	


if (isset($_GET["getNewContractInMonth"]))
		{
			
			$result = array();
			if(isset($_GET["DISPATCH_DATE"]) && !empty($_GET["DISPATCH_DATE"]) )
			{
				$date=$_GET["DISPATCH_DATE"];
			
				if(isset($_GET["ID_TAGS"]) and !empty($_GET["ID_TAGS"]) and $_GET["ID_TAGS"]!=="GLOBAL")
				{
	              if (isset($_GET["MONTH"]) && !empty($_GET["MONTH"]) and $_GET["MONTH"]!==1)
				    {
	
						
							$MONTHS=explode(",", $_GET["MONTH"]);
							foreach($MONTHS  as $key => $month)
							{
								if ($key==0)
								{
									if ($month==1)
									{
										if($date==2021)
										{
											$query="SELECT TUTELLE,SERVICE_NO,NAMES,AGENCE,SAVE_DATE FROM `referentiel_etat` WHERE YEAR(`SAVE_DATE`)=".$date." and MONTH(`SAVE_DATE`)=".$month;
										}
										else
										{
											$query="SELECT ref.*
											FROM
											(SELECT TUTELLE,SERVICE_NO,NAMES,AGENCE,SAVE_DATE FROM `referentiel_etat` WHERE YEAR(`SAVE_DATE`)=".$date." and MONTH(`SAVE_DATE`)=".$month." AND `SERVICE_NO` NOT IN (SELECT `SERVICE_NO` FROM `referentiel_etat` WHERE YEAR(`SAVE_DATE`)=".($date-1)." and MONTH(`SAVE_DATE`)=12)
											
											) as ref WHERE ref.`SERVICE_NO` IN (SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS='".$_GET["ID_TAGS"]."');";					
										}
									}
								
									else
									{
										$query="SELECT ref.*
										FROM
										(SELECT TUTELLE,SERVICE_NO,NAMES,AGENCE,SAVE_DATE FROM `referentiel_etat` WHERE YEAR(`SAVE_DATE`)=".$date." and MONTH(`SAVE_DATE`)=".$month." AND `SERVICE_NO` NOT IN (SELECT `SERVICE_NO` FROM `referentiel_etat` WHERE YEAR(`SAVE_DATE`)=".$date." and MONTH(`SAVE_DATE`)=".($month-1).")
										
										) as ref WHERE ref.`SERVICE_NO` IN (SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS='".$_GET["ID_TAGS"]."');";
									}
								}
								else
								{
									if ($month==1)
									{
										
										if($date==2021)
										{
											$query.=" UNION SELECT TUTELLE,SERVICE_NO,NAMES,AGENCE,SAVE_DATE FROM `referentiel_etat` WHERE YEAR(`SAVE_DATE`)=".$date." and MONTH(`SAVE_DATE`)=".$month;
										}
										else
										{
											$query.=" UNION SELECT ref.*
											FROM
											( SELECT TUTELLE,SERVICE_NO,NAMES,AGENCE,SAVE_DATE FROM `referentiel_etat` WHERE YEAR(`SAVE_DATE`)=".$date." and MONTH(`SAVE_DATE`)=".$month." AND `SERVICE_NO` NOT IN (SELECT `SERVICE_NO` FROM `referentiel_etat` WHERE YEAR(`SAVE_DATE`)=".($date-1)." and MONTH(`SAVE_DATE`)=12)
											
											) as ref WHERE ref.`SERVICE_NO` IN (SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS='".$_GET["ID_TAGS"]."');";					
										}
									}
									else
									{
										$query.=" UNION SELECT ref.*
										FROM
										(SELECT TUTELLE,SERVICE_NO,NAMES,AGENCE,SAVE_DATE FROM `referentiel_etat` WHERE YEAR(`SAVE_DATE`)=".$date." and MONTH(`SAVE_DATE`)=".$month." AND `SERVICE_NO` NOT IN (SELECT `SERVICE_NO` FROM `referentiel_etat` WHERE YEAR(`SAVE_DATE`)=".$date." and MONTH(`SAVE_DATE`)=".($month-1).")
										
										) as ref WHERE ref.`SERVICE_NO` IN (SELECT SERVICE_NO FROM contrat_possede_tags WHERE ID_TAGS='".$_GET["ID_TAGS"]."');";
									}
								}
							}
							//echo $query;
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
					if (isset($_GET["MONTH"]) && !empty($_GET["MONTH"]) and $_GET["MONTH"]!==1)
				    {
	
						
							$MONTHS=explode(",", $_GET["MONTH"]);
							foreach($MONTHS  as $key => $month)
							{
								if ($key==0)
								{
									if ($month==1)
									{
										if($date==2021)
										{
											$query="SELECT TUTELLE,SERVICE_NO,NAMES,AGENCE,SAVE_DATE FROM `referentiel_etat` WHERE YEAR(`SAVE_DATE`)=".$date." and MONTH(`SAVE_DATE`)=".$month;
										}
										else
										{
											$query="SELECT TUTELLE,SERVICE_NO,NAMES,AGENCE,SAVE_DATE FROM `referentiel_etat` WHERE YEAR(`SAVE_DATE`)=".$date." and MONTH(`SAVE_DATE`)=".$month." AND `SERVICE_NO` NOT IN (SELECT `SERVICE_NO` FROM `referentiel_etat` WHERE YEAR(`SAVE_DATE`)=".($date-1)." and MONTH(`SAVE_DATE`)=12)";					
										}
									}
								
									else
									{
										$query="SELECT TUTELLE,SERVICE_NO,NAMES,AGENCE,SAVE_DATE FROM `referentiel_etat` WHERE YEAR(`SAVE_DATE`)=".$date." and MONTH(`SAVE_DATE`)=".$month." AND `SERVICE_NO` NOT IN (SELECT `SERVICE_NO` FROM `referentiel_etat` WHERE YEAR(`SAVE_DATE`)=".$date." and MONTH(`SAVE_DATE`)=".($month-1).")";
									}
								}
								else
								{
									if ($month==1)
									{
										
										if($date==2021)
										{
											$query.=" UNION SELECT TUTELLE,SERVICE_NO,NAMES,AGENCE,SAVE_DATE FROM `referentiel_etat` WHERE YEAR(`SAVE_DATE`)=".$date." and MONTH(`SAVE_DATE`)=".$month;
										}
										else
										{
											$query.=" UNION SELECT TUTELLE,SERVICE_NO,NAMES,AGENCE,SAVE_DATE FROM `referentiel_etat` WHERE YEAR(`SAVE_DATE`)=".$date." and MONTH(`SAVE_DATE`)=".$month." AND `SERVICE_NO` NOT IN (SELECT `SERVICE_NO` FROM `referentiel_etat` WHERE YEAR(`SAVE_DATE`)=".($date-1)." and MONTH(`SAVE_DATE`)=12)";					
										}
									}
									else
									{
										$query.=" UNION SELECT TUTELLE,SERVICE_NO,NAMES,AGENCE,SAVE_DATE FROM `referentiel_etat` WHERE YEAR(`SAVE_DATE`)=".$date." and MONTH(`SAVE_DATE`)=".$month." AND `SERVICE_NO` NOT IN (SELECT `SERVICE_NO` FROM `referentiel_etat` WHERE YEAR(`SAVE_DATE`)=".$date." and MONTH(`SAVE_DATE`)=".($month-1).")";
									}
								}
							}
							
                            //echo $query;
								$req=$bdd->query($query, PDO::FETCH_ASSOC);
								$resultats=$req->fetchAll();
								
							
								foreach($resultats as $resultat )
								{
				
									array_push($result,(object)$resultat);
								}
							//var_dump($result);
						print(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
								$req->closeCursor();
			
			        }		
				
				}
				
				
				
			
			
			}
		}