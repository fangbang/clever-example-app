<?php

require_once('config.php');

$url = "https://clever.com/oauth/authorize?" .
         "response_type=code&" .
         "redirect_uri=$REDIRECT_URI&" .
         "client_id=$CLIENT_ID&" .
         "district_id=$DISTRICT_ID";

echo "
<!DOCTYPE html>
<html>
<body>

<b>Please log in to view your class schedule:</b><br>
<br>
<a href='$url'>
  <img src='https://s3.amazonaws.com/assets.clever.com/sign-in-with-clever/sign-in-with-clever-full.png'>
</a>

</body>
</html>
";

