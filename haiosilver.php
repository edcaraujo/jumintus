<?php
    /*
     * Including deps...
     * 
     */ 

    require_once('deps/phpmailer/php/class.phpmailer.php');

    /*
     * Loading configs...
     * 
     */ 

    $CONFIG = include('config/config.php'); 

    /*
     * Setting session...
     * 
     */

    session_start();

    /*
     * Setting task...
     * 
     */

    function create() {
        global $CONFIG;

        $TASK = [
          'task-timestamp' => '',
          'task-uuid' => '', 

          'user-auth' => '',
          'user-pass' => '',   
    
          'task-category' => '',
          'task-description' => '',
    
          'task-source' => '',
          'task-responsable' => '',
    
          'task-schedule' => '',

          'task-priority' => '',

          'task-project' => '',

          'task-attachments' => [],
          'task-follows' => [],
    
          'task-infos' => [],
          'task-warnings' => [],
          'task-errors' => [],
    
          'user-name' => '',
          'user-code' => '',
          'user-profile' => '',
          'user-role' => '',
          'user-phone' => '',
          'user-email' => '',
          'user-alternative' => '',
          'user-authorization' => '',
    
          'local-label' => '',
          'local-unity' => '',
          'local-departament' => '',
        ];

        // Setting 'task-timestamp'...

        $TASK['task-timestamp'] = time();
        $TASK['task-date'] = date('d/m/Y',$TASK['task-timestamp']);
        $TASK['task-time'] = date('H:i:m',$TASK['task-timestamp']);

        // Setting 'task-uuid'...

        if (!empty($_POST['task-uuid'])) {
            $TASK['task-uuid'] = $_POST['task-uuid'];

            if (file_exists('tasks'.'/'.$TASK['task-uuid'].'/'.$TASK['task-uuid'].'.task.jumintus')) {
              $TASK['task-errors'][] = 'Parece que houve um conflito com o protocolo do chamado. Tente novamente.';
              return $TASK;
            }
        
        } else {
            $TASK['task-errors'][] = 'Impossível determinar o protocolo do chamado. Tente novamente.';
            return $TASK;
        }

        // Setting 'user-auth'...

        if (!isset($_SESSION['user-auth'])) {
            if (!empty($_POST['user-auth'])) {
                $TASK['user-auth'] = explode('@',$_POST['user-auth'])[0];
            } else {
                $TASK['task-errors'][] = 'Usuário não especificado. Tente novamente.'; 
                return $TASK;
            }
        } else {
            $TASK['user-auth'] = $_SESSION['user-auth'];
        }

        // Setting 'user-pass'...

        if (!isset($_SESSION['user-auth'])) {
            if (!empty($_POST['user-pass'])) {
                $TASK['user-pass'] = $_POST['user-pass'];
            } else {
                $TASK['task-errors'][] = 'Senha não especificada. Tente novamente.'; 
                return $TASK;
            }
        } else {
            $TASK['user-pass'] = $_SESSION['user-pass'];
        }

        // Setting 'user-*'...
        if (!isset($_SESSION['user-auth'])) {


            foreach($CONFIG['jumintus']['auth'] as $item) {
                $data = $CONFIG['auth'][$item]($TASK['user-auth'],$TASK['user-pass']);

                if ($data) {
                    $TASK['user-name'] = $data['name'];
                    $TASK['user-role'] = $data['role'];
                    $TASK['user-email'] = $data['email'];
                    $TASK['user-authorization'] = 0;
                    break;
                }
            }

            if (!empty($_POST['user-profile'])) {
              $TASK['user-profile'] = $_POST['user-profile'];
            } else {
              $TASK['task-errors'][] = 'Perfil não especificada. Tente novamente.'; 
              return $TASK;
            }

            if (!empty($_POST['user-phone'])) {
              $TASK['user-phone'] = $_POST['user-phone'];
            } else {
              $TASK['task-errors'][] = 'Telefone não especificado. Tente novamente.'; 
              return $TASK;
            }
        } else {
            $TASK['user-code'] = $_SESSION['user-code'];
            $TASK['user-name'] = $_SESSION['user-name'];
            $TASK['user-profile'] = $_SESSION['user-profile'];
            $TASK['user-role'] = $_SESSION['user-role'];
            $TASK['user-phone'] = $_SESSION['user-phone'];
            $TASK['user-email'] = $_SESSION['user-email'];
            $TASK['user-alternative'] = $_SESSION['user-alternative'];
            $TASK['user-authorization'] = $_SESSION['user-authorization'];
        }

        if (empty($TASK['user-name']) || empty($TASK['user-role']) || empty($TASK['user-email'])) {
            $TASK['task-errors'][] = 'Usuário e/ou senha inválido. Tente novamente.'; 
            return $TASK;
        }

        // Setting 'task-category'...

        if (!empty($_POST['task-category'])) {
            $TASK['task-category'] = $_POST['task-category'];
        } else {
            $TASK['task-errors'][] = 'Categoria inválida. Tente novamente.';
            return $TASK;
        }

        // Setting 'task-description'...

        if (!empty($_POST['task-description'])) {
            $TASK['task-description'] = $_POST['task-description'];
        } else {
            $TASK['task-errors'][] = 'Descrição insuficiente. Tente novamente.';
            return $TASK;
        }

        // Setting 'task-source'...

        $TASK['task-source'] = '*';   

        if (!empty($_POST['task-source'])) {
            $TASK['task-source'] = $_POST['task-source'];
        } 

        // Setting 'task-responsable'...

        $TASK['task-responsable'] = '*';   

        if (!empty($_POST['task-responsable'])) {
            $TASK['task-responsable'] = $_POST['task-responsable'];
        } 
        
        // Setup by rules.

        // Setting 'task-schedule'...

        $TASK['task-schedule'] = '*'; $DAYTIMESTAMP = 60*60*24;

        foreach ($CONFIG['schedules'] as $key => $schedule) {
            $today = strtolower(date('D', $TASK['task-timestamp']));

            if (isset($schedule['week'])) {
                if (array_key_exists($today,$schedule['week'])) {
                    $scheduletime = [
                        strtotime(date('Y-m-d',$TASK['task-timestamp']).' '.$schedule['week'][$today][0]),
                        strtotime(date('Y-m-d',$TASK['task-timestamp']).' '.$schedule['week'][$today][1]),
                    ];                    

                    if ($scheduletime[0] > $scheduletime[1])
                        $scheduletime[1] += $DAYTIMESTAMP; 

                    if ($TASK['task-timestamp'] < $scheduletime[0]) {
                        $yesterday = strtolower(date('D', $TASK['task-timestamp']-$DAYTIMESTAMP));

                        if (array_key_exists($yesterday,$schedule['week'])) {
                            $scheduletime = [
                                strtotime(date('Y-m-d',$TASK['task-timestamp']-$DAYTIMESTAMP).' '.$schedule['week'][$yesterday][0]),
                                strtotime(date('Y-m-d',$TASK['task-timestamp']-$DAYTIMESTAMP).' '.$schedule['week'][$yesterday][1]),
                            ];

                            if ($scheduletime[0] > $scheduletime[1]) 
                                $scheduletime[1] += $DAYTIMESTAMP;
                        }
                    }

                    if ($TASK['task-timestamp'] >= $scheduletime[0] && $TASK['task-timestamp'] < $scheduletime[1]) {
                        $TASK['task-schedule'] = $key;
                        break;
                    }
                }
            }
        }

        // Setting 'task-priority'...

        // Setup by rules.

        // Setting 'task-project'...

        // Setup by rules.

        // Setting 'task-attachments'...

        $upload = 'tasks'.'/'.$TASK['task-uuid'].'/'.'attachments'.'/';

        foreach(scandir($upload) as $attachment) {
          if (!in_array($attachment,['.','..'])) {
            $TASK['task-attachments'][] = $attachment;
          }
        }

        // Setting 'task-follows'...

        // Setup by rules.

        // Setting 'local-label'...

        if (!empty($_POST['local-label'])) {
            $TASK['local-label'] = $_POST['local-label'];
        }

        // Setting 'local-unity'...

        if (!empty($_POST['local-unity'])) {
            $TASK['local-unity'] = $_POST['local-unity'];
        } else {
            $TASK['task-errors'][] = 'Unidade não especificada. Tente novamente.';
            return $TASK;
        }

        // Setting 'local-departament'...

        if (!empty($_POST['local-departament'])) {
            $TASK['local-departament'] = $_POST['local-departament'];
        } else {
            $TASK['task-errors'][] = 'Setor não especificado. Tente novamente.';
            return $TASK;
        }

        // Analyzing rules...

        $lists = []; 

        foreach(array_keys($TASK) as $property) {
            if (is_array($TASK[$property])) {
                $lists[] = $property;
            }

            if ($TASK[$property] == '*') {
              $defaults = $property;
            }
        }

        $locks = [];

        if ($TASK['task-schedule'] != '*') {
          $locks[] = 'task-schedule';
        }

        if ($TASK['task-source'] != '*') {
          $locks[] = 'task-source';
        }

        if ($TASK['task-responsable'] != '*') {
          $locks[] = 'task-responsable';
        }

        foreach($CONFIG['jumintus']['rules'] as $rule) {
          if (array_key_exists($rule[15],$TASK)) {

            if (($rule[0] == '*' || $TASK['task-timestamp'] >= strtotime($rule[0])) && //
                ($rule[1] == '*' || $TASK['task-timestamp'] < strtotime($rule[1])) &&
                ($rule[2] == '*' || $TASK['user-auth'] == $rule[2]) &&
                ($rule[3] == '*' || $TASK['user-profile'] == $rule[3]) &&
                ($rule[4] == '*' || $TASK['user-role'] == $rule[4]) &&
                ($rule[5] == '*' || $TASK['task-authorization'] <= $rule[5]) &&
                ($rule[6] == '*' || $TASK['task-category'] == $rule[6]) &&
                ($rule[7] == '*' || $TASK['task-source'] == $rule[7]) &&
                ($rule[8] == '*' || $TASK['task-responsable'] == $rule[8]) &&
                ($rule[9] == '*' || $TASK['task-schedule'] == $rule[9]) &&
                ($rule[10] == '*' || $TASK['task-priority'] == $rule[10]) &&
                ($rule[11] == '*' || $TASK['task-project'] == $rule[11]) &&
                ($rule[12] == '*' || $TASK['local-label'] == $rule[12]) &&
                ($rule[13] == '*' || $TASK['local-unity'] == $rule[13]) &&
                ($rule[14] == '*' || $TASK['local-departament'] == $rule[14])) {

              if (!in_array($rule[15],$locks)) {
                if (in_array($rule[15],$lists)) {
                  $TASK[$rule[15]][] = $rule[16];
                } else {
                  $TASK[$rule[15]] = $rule[16];
                }
              }
            } 
          }           
        }

        return $TASK;
    }

    function delete($task) {
      $upload = 'tasks'.'/'.$task.'/'.'attachments'.'/';

      foreach(scandir($upload) as $attachment) {
        if (!in_array($attachment,['.','..'])) {
          unlink('tasks'.'/'.$task.'/'.'attachments'.'/'.$attachment);
        }
      }

      rmdir('tasks'.'/'.$task.'/'.'attachments');
      rmdir('tasks'.'/'.$task);
    }

    function process($template, $task) {
      global $CONFIG;

      $content = file_get_contents($template);

      $naming = [
        'task-category' => 'categories',
        'task-source' => 'sources',
        'task-schedule' => 'schedules',
        'local-unity' => 'units',
        'user-profile' => 'profiles',
      ];

      foreach ($task as $k => $v) {
        $content = str_replace('{{'.$k.'}}', (is_array($v) ? implode(',',$v) : $v), $content);
        $content = str_replace('{{'.$k.'@name}}', (array_key_exists($k,$naming) ? $CONFIG[$naming[$k]][$v]['name'] : ''), $content);
        $content = str_replace('[['.$k.']]', mb_strtoupper((is_array($v) ? implode(',',$v) : $v)), $content);
        $content = str_replace('[['.$k.'@name]]', mb_strtoupper((array_key_exists($k,$naming) ? $CONFIG[$naming[$k]][$v]['name'] : '')), $content);
      }

      $content = str_replace('{{jumintus-version}}', $CONFIG['jumintus']['version'], $content);
      $content = str_replace('[[jumintus-version]]', mb_strtoupper($CONFIG['jumintus']['version']), $content);

      return $content;
    }

    $TASK = create();

    if (empty($TASK['task-errors'])) {

      $mailer = new PHPMailer();

      $mailer->IsSMTP();  
      $mailer->SMTPDebug = 0;
      $mailer->SMTPAuth = true;
      $mailer->IsHTML(false);  
    
      if (!empty($CONFIG['mailer']['host'])) {
        $mailer->Host = $CONFIG['mailer']['host'];
      } else {
        $TASK['task-errors'][] = 'Parece que erramos em algo: \'host\' não está configurado no mailer. Tente novamente mais tarde';
      }

      if (!empty($CONFIG['mailer']['port'])) {
        $mailer->Port = $CONFIG['mailer']['port'];
      } else {
        $TASK['task-errors'][] = 'Parece que erramos em algo: \'port\' não está configurado no mailer. Tente novamente mais tarde';
      }

      if (!empty($CONFIG['mailer']['username'])) {
        $mailer->Username = $CONFIG['mailer']['username'];
        $mailer->SetFrom($CONFIG['mailer']['username'], utf8_decode('Jumintus'));
      } else {
        $TASK['task-errors'][] = 'Parece que erramos em algo: \'username\' não está configurado no mailer. Tente novamente mais tarde';
      }

      if (!empty($CONFIG['mailer']['password'])) {
        $mailer->Password = $CONFIG['mailer']['password'];
      } else {
        $TASK['task-errors'][] = 'Parece que erramos em algo: \'password\' não está configurado no mailer. Tente novamente mais tarde';
      }

      if (!empty($TASK['task-project']) && in_array($TASK['task-project'],$CONFIG['asana']['projects'])) {
        $mailer->AddAddress('x+'.$TASK['task-project'].'@mail.asana.com');
      } else {
        $TASK['task-errors'][] = 'Parece que erramos em algo: o projeto do asana não está configurado. Tente novamente mais tarde';
      }

      if (!empty($TASK['task-responsable'])) {
        if (!empty($CONFIG['users'][$TASK['task-responsable']]) && !empty($CONFIG['users'][$TASK['task-responsable']]['asana'])) {
          $mailer->AddAddress($CONFIG['users'][$TASK['task-responsable']]['asana']);
        }
      }
      
      foreach($CONFIG['users'] as $key => $user) {
        if ($user['authorization'] >= 1000) {
          $mailer->AddCC($user['asana']);
        }
      }
      
      if (empty($TASK['task-errors'])) {
        $subject = process('templates/subject.task.template.jumintus',$TASK);
        $message = process('templates/task.template.jumintus',$TASK);
    
        $mailer->Subject = utf8_decode(wordwrap($subject, 80, "\n"));
        $mailer->Body = utf8_decode(wordwrap($message, 80, "\n"));

        foreach ($TASK['task-attachments'] as $attachment) {
          $mailer->AddAttachment('tasks'.'/'.$TASK['task-uuid'].'/'.'attachments'.'/'.$attachment, $attachment);
        }

        if (!$mailer->Send()) {
          $TASK['task-errors'][] = 'Parece que tivemos um problema com o nosso servidor de e-mail (\''.$mailer->ErrorInfo.'\')';
        }
      }

      $mailer->clearAddresses(); unset($mailer);
    } 

    if (empty($TASK['task-errors'])) {

      $mailer = new PHPMailer();

      $mailer->IsSMTP();  
      $mailer->SMTPDebug = 0;
      $mailer->SMTPAuth = true;
      $mailer->IsHTML(false);  
    
      if (!empty($CONFIG['mailer']['host'])) {
        $mailer->Host = $CONFIG['mailer']['host'];
      } else {
        $TASK['task-errors'][] = 'Parece que erramos em algo: \'host\' não está configurado no mailer. Tente novamente mais tarde';
      }

      if (!empty($CONFIG['mailer']['port'])) {
        $mailer->Port = $CONFIG['mailer']['port'];
      } else {
        $TASK['task-errors'][] = 'Parece que erramos em algo: \'port\' não está configurado no mailer. Tente novamente mais tarde';
      }

      if (!empty($CONFIG['mailer']['username'])) {
        $mailer->Username = $CONFIG['mailer']['username'];
        $mailer->SetFrom($CONFIG['mailer']['username'], utf8_decode('Jumintus'));
      } else {
        $TASK['task-errors'][] = 'Parece que erramos em algo: \'username\' não está configurado no mailer. Tente novamente mais tarde';
      }

      if (!empty($CONFIG['mailer']['password'])) {
        $mailer->Password = $CONFIG['mailer']['password'];
      } else {
        $TASK['task-errors'][] = 'Parece que erramos em algo: \'password\' não está configurado no mailer. Tente novamente mais tarde';
      }

      $mailer->AddAddress($TASK['user-email']);

      foreach ($TASK['task-follows'] as $follow) {
        $mailer->AddCC($follow);
      }
 
      if (empty($TASK['task-errors'])) {
        $subject = process('templates/subject.create.template.jumintus',$TASK);
        $message = process('templates/create.template.jumintus',$TASK);
    
        $mailer->Subject = utf8_decode(wordwrap($subject, 80, "\n"));
        $mailer->Body = utf8_decode(wordwrap($message, 80, "\n"));

        foreach ($TASK['task-attachments'] as $attachment) {
          $mailer->AddAttachment('tasks'.'/'.$TASK['task-uuid'].'/'.'attachments'.'/'.$attachment, $attachment);
        }

        if (!$mailer->Send()) {
          $TASK['task-errors'][] = 'Parece que tivemos um problema com o nosso servidor de e-mail (\''.$mailer->ErrorInfo.'\')';
        }
      }

      $mailer->clearAddresses(); unset($mailer);
    } 

    if (!empty($TASK['task-errors'])) {
      delete($TASK['task-uuid']);
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title><?= $CONFIG['jumintus']['project'] ?> | <?= $CONFIG['jumintus']['company'] ?></title>

    <link rel="stylesheet" href="deps/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="deps/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="deps/fileinput/css/fileinput.min.css">
  </head>
  <body>
    <header>
    <nav  class="navbar navbar-expand navbar-dark bg-dark">
      <a class="navbar-brand" href="index.php"><?= $CONFIG['jumintus']['project'] ?></a>

      <div class="navbar-collapse collapse">
        <ul class="navbar-nav ml-auto">
          <?php if (!isset($_SESSION['user-auth'])): ?>
            <li class="nav-item">
              <a class="btn btn-outline-light" href="index.php" data-toggle="modal" data-target="#moauthuser"><i class="fas fa-sign-in-alt"></i> Login</a>  
            </li>
          <?php else: ?> 
            <li class="nav-item">
              <span class="nav-link active" href="index.php?do=logout"> <?= $_SESSION['user-name'] ?> (@<?= $_SESSION['user-auth'] ?>)</span>
            </li>
            <li class="nav-item">
              <a class="btn btn-light" href="index.php?do=logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </li>
          <?php endif; ?>
        </ul>
    </div>
   </nav>

    <div class="jumbotron jumbotron-fluid">
      <div class="container">
        <h1 class="display-4"><?= $CONFIG['jumintus']['icon'] ?> <?= $CONFIG['jumintus']['title'] ?></h1>
        <p class="lead"><?= $CONFIG['jumintus']['description'] ?></p>
      </div>
    </div>
    </header>

    <div class="container">

      <?php if ($CONFIG['jumintus']['test']): ?>
        <div class="py-3">
          <div class=" text-center">
            <div class="alert alert-warning">
              <h2 class="alert-heading"><b>Atenção</b>: esta é uma versão de teste. Chamados enviados por esta versão <b>não serão atendidos</b>.</h2>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <?php if (!empty($TASK['task-infos'])): ?>
        <?php foreach($TASK['task-infos'] as $info): ?>
          <div>
            <div class="text-center">
              <div class="alert alert-info">
                <h2 class="alert-heading">:)</h2>
                <h3>Olá!</h3>
                <p><?= $info ?></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

      <?php if (!empty($TASK['task-warnings'])): ?>
        <?php foreach($TASK['task-warnings'] as $warning): ?>
          <div>
            <div class="text-center">
              <div class="alert alert-warning">
                <h2 class="alert-heading">:|</h2>
                <h3>Atenção!</h3>
                <p><?= $warning ?></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

      <?php if (!empty($TASK['task-errors'])): ?>
        <?php foreach($TASK['task-errors'] as $error): ?>
          <div>
            <div class="text-center">
              <div class="alert alert-danger">
                <h2 class="alert-heading">:(</h2>
                <h3>Ops!</h3>
                <p><?= $error ?></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
          
          <div class="py-3">
            <div class="text-center">
              <a href="index.php" class="btn btn-primary" role="button"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
            </div>
          </div>
      <?php endif; ?>

      <?php if (empty($TASK['task-errors'])): ?>
        <div>
          <div class="text-center">
            <div class="alert alert-success">
              <h2 class="alert-heading">:)</h2>
              <h3>Agora é só aguardar!</h3>
              <p><b>Solicitação enviada com sucesso!</b> Em breve um dos colaboradores da TI entrarão em contato através do <b>telefone de contato '<?= $TASK['user-contact'] ?>' ou e-mail</b> do colaborador.</p>
            </div>
          </div>
        </div>

        <div class="accordion" id="accordion-<?= $TASK['task-uuid'] ?>">
          <div class="card">
            <div class="card-header" id="card-header-<?= $TASK['task-uuid'] ?>">
              <div class="row">
                <div class="col-md-8">
                  <div class="text-md-left text-center">
                    <button class="btn btn-sm btn-dark" type="button" data-toggle="collapse" data-target="#card-body-<?= $TASK['task-uuid'] ?>" aria-expanded="true" aria-controls="B<?= $TASK['task-uuid'] ?>">
                      <i class="fas fa-hashtag"></i> <?= $TASK['task-uuid'] ?>
                    </button>
                    <button class="btn btn-sm btn-secondary" type="button" data-toggle="collapse" data-target="#card-body-<?= $TASK['task-uuid'] ?>" aria-expanded="true" aria-controls="B<?= $TASK['task-uuid'] ?>">
                      <i class="far fa-clock"></i> <?= date('d/m/Y H:i:m',$TASK['task-timestamp']) ?>
                    </button>
                  </div>
                </div>
                <div class="col-md-4";>
                  <div class="text-right">
                    <button class="btn btn-sm btn-dark" type="button">
                      <?= $CONFIG['schedules'][$TASK['task-schedule']]['icon'] ?> <?= $CONFIG['schedules'][$TASK['task-schedule']]['name'] ?>
                    </button> 
                  </div>
                </div>
              </div>
            </div>

            <div id="card-body-<?= $TASK['task-uuid'] ?>" class="collapse show" aria-labelledby="card-header-<?= $TASK['task-uuid'] ?>" data-parent="#accordion-<?= $TASK['task-uuid'] ?>">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-8";>
                    <div class="text-left">
                      <div>
                        <h3 class="text-uppercase"><b><i class="fas fa-user"></i> <?= $TASK['user-name'] ?></b></h3>
                        <h4 class="text-uppercase"><i class="fas fa-briefcase"></i> <?= $TASK['user-role'] ?></h4>
                        <p><i class="fas fa-id-card-alt"></i> <?= $TASK['user-auth'] ?> • <i class="fas fa-unlock-alt"></i> <?= $TASK['user-authorization'] ?></p>
                      </div>
                      
                      <div>
                        <p>
                          <span class="badge badge-pill badge-dark"><i class="fas fa-hashtag"></i> <?= $TASK['task-uuid'] ?></span> 
                          <span class="badge badge-pill badge-secondary"><i class="far fa-clock"></i> <?= date('d/m/Y H:i:m',$TASK['task-timestamp']) ?></span> 
                          <span class="badge badge-pill btn-dark"><?= $CONFIG['schedules'][$TASK['task-schedule']]['icon'] ?> <?= $CONFIG['schedules'][$TASK['task-schedule']]['name'] ?></span>
                        </p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="text-center text-md-right">
                        <img src="https://chart.googleapis.com/chart?chs=120x120&cht=qr&chl=<?= urlencode($CONFIG['jumintus']['url'].'/'.'tasks'.'/'.$TASK['task-uuid'].'/') ?>&choe=UTF-8&chld=L|0" alt="<?php echo $TASK['task-uuid']; ?>" />
                        <p><i><small>Código do chamado</small></i></p>
                    </div>
                  </div>
                </div>
                
                <div class="row">
                  <div class="col-md-6">
                    <div>
                      <div class="py-3">
                        <h5><i class="fas fa-user"></i> <u>Identificação</u></h5>
                      </div>
                      <dl>
                        <dt><i class="fas fa-phone-alt"></i> Telefone:</dt>
                        <dd><?= $TASK['user-phone'] ?></dd>
                        <dt><i class="fas fa-phone-alt"></i> Perfil:</dt>
                        <dd><?= $CONFIG['profiles'][$TASK['user-profile']]['name'] ?></dd>
                      </dl>
                    </div>

                    <div>
                      <div class="py-3">
                        <h5><i class="fas fa-location-arrow"></i> <u>Localização</u></h5>
                      </div>
                      <dl>
                        <dt><i class="fas fa-tag"></i> Etiqueta:</dt>
                        <dd><?= (!$TASK['local-label'] ? "Não informada" : $TASK['local-label']) ?></dd>
                        <dt><i class="fas fa-building"></i> Unidade:</dt>
                        <dd><?= $CONFIG['units'][$TASK['local-unity']]['name'] ?> (<?= mb_strtoupper($TASK['local-unity']) ?>)</dd>
                        <dt><i class="fas fa-door-open"></i> Setor:</dt>
                        <dd><?= $TASK['local-departament'] ?></dd>
                      </dl>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="pb-3">
                      <div class="py-3">
                        <h5><i class="fas fa-edit"></i> <u>Descrição</u></h5>
                      </div>
                      <dl>
                        <dt><i class="fas fa-tags"></i> Categoria:</dt>
                        <dd><?= $CONFIG['categories'][$TASK['task-category']]['name'] ?></dd>
                        <dt><i class="fas fa-edit"></i> Descrição:</dt>
                        <dd>"<?= $TASK['task-description'] ?>"</dd>
                        <dt><i class="fas fa-upload"></i> Anexo:</dt>
                        <?php if (!empty($TASK['task-attachments'])): ?>
                          <dd>
                            <p class="h1">
                            <?php foreach($TASK['task-attachments'] as $attachment): ?>
                              <span class="py-5 pr-1"><a href="<?= $CONFIG['jumintus']['url'].'/'.'tasks'.'/'.$TASK['task-uuid'].'/'.'attachments'.'/'.$attachment ?>" target="_blank"><i class="fas fa-file-download"></i></a></span>
                            <?php endforeach; ?>    
                            </p>
                          </dd>
                        <?php else: ?>
                          <dd>Sem anexo.</dd>
                        <?php endif; ?>                     
                      </dl>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="py-3">
          <div class="text-center">
            <a href="index.php" class="btn btn-primary" role="button"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
          </div>
        </div>
      <?php endif; ?>

      <div class="py-3">
        <div class="text-center">
          <p><small><i class="fas fa-code"></i> with <i class="fas fa-coffee"></i> | Powered by <a href="https://github.com/edcaraujo/jumintus/">Jumintus</a><br/><a href="https://github.com/edcaraujo/jumintus/"><i class="fas fa-code-branch"></i> <?= $CONFIG['jumintus']['version'] ?></a></small></p>
        </div>
      </div>
    </div>

    <script src="deps/jquery/js/jquery.min.js"></script>
    <script src="deps/bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>
