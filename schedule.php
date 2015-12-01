<?php

require_once('config.php');

$access_token = $_GET['access_token'];

if (!$access_token) {
  header("Location: $LOGIN_URI");
} else {
  $context = stream_context_create(
    array(
      'http' => array(
        'method' => 'GET',
        'header' => "Authorization: Bearer $access_token",
      ),
    )
  );
  $me = file_get_contents('https://api.clever.com/me', false, $context);

  if ($me === false) {
    header("Location: $LOGIN_URI");
  } else {
    $me = json_decode($me, true);
    $id = $me['data']['id'];
    $name = $me['data']['name']['first'];
    $type = $me['type'];

    echo '<!DOCTYPE html>';
    echo '<html>';
    echo '<head>';
    echo   '<link rel="stylesheet" type="text/css" href="schedule_style.css">';
    echo '</head>';
    echo '<body>';
    if ($type != 'student') {
      echo "<b>Sorry <span>$name</span>, only students have schedules.</b>";
    } else {
      $context = stream_context_create(
        array(
          'http' => array(
            'method' => 'GET',
            'header' => "Authorization: Bearer $DISTRICT_TOKEN",
          ),
        )
      );
      $sections = file_get_contents(
          "https://api.clever.com/v1.1/students/$id/sections",
          false,
          $context
      );

      if ($sections === false) {
        echo "<b>Sorry <span>$name</span>, we couldn't fetch your schedule.</b>";
      } else {
        $sections = json_decode($sections, true);
        $sorted_sections = array();
        foreach ($sections['data'] as $section) {
          $sorted_sections[$section['data']['name']] = $section['data']['period'];
        }
        asort($sorted_sections);

        echo "<b>Hi <span>$name</span>, here's your schedule:</b><br>";
        echo '<br>';
        echo '<table>';
        echo '<tr>';
        echo '<td style="text-align: center;"><b>Class</b></td>';
        echo '<td style="text-align: center;"><b>Period</b></td>';
        echo '</tr>';
        foreach ($sorted_sections as $class_name => $period) {
          echo '<tr>';
          echo '<td>' . $class_name . '</td>';
          echo '<td style="text-align: center;">' . $period . '</td>';
          echo '</tr>';
        }
        echo '</table>';
      }
      echo '</body>';
      echo '</html>';
    }
  }
}

