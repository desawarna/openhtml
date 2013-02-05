<?php

function validate($code, $type){

  if($type == "html") {

    $url="http://validator.w3.org/check";
  
    $handle = curl_init();
    curl_setopt_array(
      $handle,
      array(
        CURLOPT_URL => $url,
        CURLOPT_POSTFIELDS => "fragment=".urlencode($code),
        // CURLOPT_POSTFIELDS => "fragment=".$code."&output=soap12",
        CURLOPT_RETURNTRANSFER => true
      )
    );

  }

  elseif ($type == "css") 
  {

    $url = "http://jigsaw.w3.org/css-validator/validator?text=".urlencode($code)."&warning=1&profile=css3";
    $handle = curl_init();
    curl_setopt_array(
      $handle,
      array(
        CURLOPT_URL => $url,
        //CURLOPT_POSTFIELDS => "uri=http%3A%2F%2Fwww.w3.org%2F&warning=0&profile=css2",
        // CURLOPT_POSTFIELDS => "fragment=".$code."&output=soap12",
        CURLOPT_RETURNTRANSFER => true
      )
    );

  }

  $curl_response = curl_exec($handle);
  curl_close($handle);
  echo $curl_response;
  return $curl_response;  
}


validate($_POST["code"], $_POST["type"]);


?>