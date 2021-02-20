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
  ];

  foreach($INCLUDES as $item) 
  {
    $d = getcwd().'/'.'config'; $name = basename($item,'.config.php');

    if (file_exists($d.'/'.$item))
      $CONFIG[$name] = include($d.'/'.$item);   
  }

  return $CONFIG;
?>