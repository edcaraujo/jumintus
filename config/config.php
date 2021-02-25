<?php
  $INCLUDES = [
    'jumintus/jumintus.config.php',

    'jumintus/components/auth.config.php',

    'jumintus/data/categories.config.php',
    'jumintus/data/sources.config.php',
    'jumintus/data/units.config.php',
    'jumintus/data/users.config.php',
    'jumintus/data/schedules.config.php',
    'jumintus/data/profiles.config.php',

    'misc/mailer.config.php',
    'misc/asana.config.php',
  ];

  foreach($INCLUDES as $item) {
    $r = getcwd().'/'.'config'; 
    $d = dirname($item);
    $f = basename($item);

    $name = basename($item,'.config.php');

    if (file_exists($r.'/'.$d.'/'.'local.'.$f)) {
      $CONFIG[$name] = include($r.'/'.$d.'/'.'local.'.$f);   
    } else if (file_exists($r.'/'.$d.'/'.$f)) {
      $CONFIG[$name] = include($r.'/'.$d.'/'.$f);   
    }
  }

  return $CONFIG;
?>