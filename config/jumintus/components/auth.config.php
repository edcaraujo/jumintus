<?php
  return [
    'jumintus-config' => function($user, $pass) {
        $USERS = include('config/jumintus/data/users.config.php'); 
        
        $user = trim($user);
        $pass = md5(trim($pass));

        if (array_key_exists($user,$USERS)) {
          $data = $USERS[$user];

          if ($pass == $data['password']) {
            return [
              'user' => $user,
              'code' => $data['code'],
              'name' => $data['name'],
              'profile' => $data['profile'],
              'role' => $data['role'],
              'phone' => $data['phone'],
              'email' => $data['email'],
              'alternative' => $data['alternative'],

              'asana' => $data['asana'],

              'authorization' => $data['authorization'],
            ];
          }
        }

        return false; 
      },

      'jumintus-data' => function($user, $pass) {
        $USERS = include('users.config.php');  

        $user = trim($user);
        $pass = md5(trim($pass));

        $f = fopen('data/users.data.csv', 'r');

        if ($f) {
          while (($line = fgets($f)) !== false) {
            $data = explode(',',utf8_encode($line));

            if ($user == trim($data[0]) && $pass == trim($data[10])) { 
              fclose($f);
              
              return [
                'user' => $data[0],
                'code' => $data[1],
                'name' => $data[2],
                'profile' => $data[3],
                'role' => $data[4],
                'phone' => $data[5],
                'email' => $data[6],
                'alternative' => $data[7],
                
                'asana' => $data[8],

                'authorization' => $data[9],
              ];
            }
          }

          fclose($f);
        }

        return false; 
      },
  ];  
?>


