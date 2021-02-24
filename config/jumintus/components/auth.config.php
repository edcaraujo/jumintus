<?php
  return [
    'jumintus-config' => function($auth, $pass) {
        $USERS = include('users.config.php'); 
        
        $auth = trim($auth);
        $pass = md5(trim($pass));

        foreach($USERS as $key => $user) {
          
          if ($auth == $key && $pass == $user['password']) {

            return [
              'user' => $key,
              'code' => $user['code'],
              'name' => $user['name'],
              'profile' => $user['profile'],
              'role' => $user['role'],
              'phone' => $user['phone'],
              'email' => $user['email'],
              'alternative' => $user['alternative'],

              'asana' => $user['asana'],

              'authorization' => $user['authorization'],
            ];
          }
        }

        return false; 
      },

      'jumintus-data' => function($auth, $pass) {
        $USERS = include('users.config.php');  

        $auth = trim($auth);
        $pass = md5(trim($pass));

        $f = fopen('data/users.data.csv', 'r');

        if ($f) {
          while (($line = fgets($f)) !== false) {
            $user = explode(',',utf8_encode($line));

            if ($auth == trim($user[0]) && $pass == trim($user[10])) { 
              fclose($f);
              
              return [
                'user' => $user[0],
                'code' => $user[1],
                'name' => $user[2],
                'profile' => $user[3],
                'role' => $user[4],
                'phone' => $user[5],
                'email' => $user[6],
                'alternative' => $user[7],
                
                'asana' => $user[8],

                'authorization' => $user[9],
              ];
            }
          }

          fclose($f);
        }

        return false; 
      },
  ];  
?>


