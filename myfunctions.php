<?php

function validateString($input) {
    // Vérifier si la chaîne est un email
    if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
        return 1; // Email valide
    }

    // Vérifier si la chaîne est un numéro de téléphone
    // Vous pouvez personnaliser cette vérification en fonction du format attendu
    if (preg_match("/^[0-9]{9}$/", $input)) {
        return 2; // Numéro de téléphone valide
    }

    // La chaîne n'est ni un email ni un numéro de téléphone
    return 0;
}


function sendApiSMS($url,$datas)
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

// $datasAll = array("fullname"=>strtok($nom," "),"telephone"=>$telephone,"groupcode"=>$code_tuteur,"groupname"=>$prenoms,"chiefname"=>strtok($donnee['nom'], " "));
// 				if(postApiInfos('https://ju.clientxp.com/yamohz/api/index.php',$datasAll))

    function global_sendsms($destination_phone, $content, $sender = 'MTN-ZiK')
    {
        $url = 'http://wsp.zen-sms.com/smssend/?';
        $timeout = 10;
        $token = "843e5c4sqcsdg2f8ssa5ef4abc9e9667sq256gsdoyzb78";
        $destination = $destination_phone;
        $source = $sender;
        $message = $content;
        $request = $url . "token=" . urlencode($token);
        $request .= "&sender=" . urlencode($source) . "&phone=" . urlencode($destination) . "&msg=" . urlencode($message);

        $url = $request;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }


    function generateNewPassword() {
        $length = 10; // Longueur du mot de passe
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'; // Caractères autorisés
        $password = '';
    
        $characterCount = strlen($characters);
        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, $characterCount - 1);
            $password .= $characters[$index];
        }
    
        return $password;
    }
?>