<?php

function validate($code, $type){

  if($type == "html") $url="http://validator.w3.org/check";
  else if ($type == "css") $url = "http://jigsaw.w3.org/css-validator/validator";
  $handle = curl_init();
  curl_setopt_array(
    $handle,
    array(
      CURLOPT_URL => $url,
      CURLOPT_POSTFIELDS => "fragment=".$code,
      // CURLOPT_POSTFIELDS => "fragment=".$code."&output=soap12",
      CURLOPT_RETURNTRANSFER => true
    )
  );

  $curl_response = curl_exec($handle);
  curl_close($handle);

  echo $curl_response;
  return $curl_response;  
}

validate($_POST["html_code"], "html");


?>