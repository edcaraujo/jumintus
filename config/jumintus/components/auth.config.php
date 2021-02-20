<?php
  return [
    'jumintus-config' => function($user, $pass) 
      {
        $USERS = include('users.config.php'); 
        
        $user = trim($user);
        $pass = md5(trim($pass));

        foreach($USERS as $k => $v) {
          
          if ($user == $k &&
              $pass == $v['password']) {

            return [
              'user' => $k,
              'code' => $v['code'],
              'name' => $v['name'],
              'profile' => $v['profile'],
              'role' => $v['role'],
              'phone' => $v['phone'],
              'email' => $v['email'],
              'alternative' => $v['alternative'],

              'asana' => $v['asana'],

              'authorization' => $v['authorization'],
            ];
          }
        }

        return false; 
      },

      'jumintus-data' => function($user, $pass) 
      {
        $USERS = include('users.config.php');  

        $user = trim($user);
        $pass = md5(trim($pass));

        $f = fopen('data/users.data.csv', 'r');

        if ($f) 
        {
          while (($line = fgets($f)) !== false) 
          {
            $r = explode(',',utf8_encode($line));

            if ($user == trim($r[0]) &&
                $pass == trim($r[10]))
            { 
              fclose($f);
              
              return [
                'user' => $r[0],
                'code' => $r[1],
                'name' => $r[2],
                'profile' => $r[3],
                'role' => $r[4],
                'phone' => $r[5],
                'email' => $r[6],
                'alternative' => $r[7],
                
                'asana' => $r[8],

                'authorization' => $r[9],
              ];
            }
          }

          fclose($f);
        }

        return false; 
      },
  ];  
?>


