<?php

require_once('config.php');

$code = $_GET['code'];

if (!$code) {
  header("Location: $LOGIN_URI");
} else {
  $basic_auth_header = 'Authorization: Basic ' .
                       base64_encode("$CLIENT_ID:$CLIENT_SECRET");

  $data = array(
    'code' => $code,
    'grant_type' => 'authorization_code',
    'redirect_uri' => $REDIRECT_URI,
  );

  $params = array(
    'http' => array(
      'method' => 'POST',
      'header' =>
        "$basic_auth_header\r\n" .
        "Content-type: application/json\r\n",
      'content' => json_encode($data),
    ),
  );

  $response = file_get_contents(
    'https://clever.com/oauth/tokens',
    false,
    stream_context_create($params)
  );

  if ($response === false) {
    header("Location: $LOGIN_URI");
  } else {
    $response = json_decode($response, true);
    $access_token = $response['access_token'];
    header('Location: ' . 
           "$SCHEDULE_URI?access_token=$access_token"
    );
  }
}

