<?php
  /*
   * Loading configs...
   * 
   */ 

  $CONFIG = include('config/config.php'); 

  /*/
   * Setting task...
   * 
   */ 

  $UUID = dechex(time()-rand(0,10000));

  $TASK = array(
    'auth-user' => '',

    'user-profile' => '',
    'user-contact' => '',

    'local-label' => '',
    'local-unity' => '',
    'local-departament' => '',

    'task-responsable' => '',
    'task-category' => '',
    'task-source' => '',
    'task-description' => '',
  );
  

  if (isset($_GET['auth-user']))
    $TASK['auth-user'] = urldecode($_GET['auth-user']);

  if (isset($_GET['user-profile']))
    $TASK['user-profile'] = urldecode($_GET['user-profile']);

  if (isset($_GET['user-contact']))
    $TASK['user-contact'] = urldecode($_GET['user-contact']);

  if (isset($_GET['local-label']))
    $TASK['local-label'] = urldecode($_GET['local-label']);

  if (isset($_GET['local-unity']))
    $TASK['local-unity'] = urldecode($_GET['local-unity']);

  if (isset($_GET['local-departament']))
    $TASK['local-departament'] = urldecode($_GET['local-departament']);

  if (isset($_GET['task-responsable']))
    $TASK['task-responsable'] = urldecode($_GET['task-responsable']);

  if (isset($_GET['task-category']))
    $TASK['task-category'] = urldecode($_GET['task-category']);

  if (isset($_GET['task-source']))
    $TASK['task-source'] = urldecode($_GET['task-source']);

  if (isset($_GET['task-description']))
    $TASK['task-description'] = urldecode($_GET['task-description']);

  /*
   * Setting session...
   * 
   */ 

  session_start();

  if (isset($_POST['auth-user']) && isset($_POST['auth-pass'])) {
    foreach($CONFIG['jumintus']['auth'] as $item) {
      $data = $CONFIG['auth'][$item](explode('@',$_POST['auth-user'])[0],$_POST['auth-pass']);

      if ($data) {
        $_SESSION['auth-user'] = $data['user'];
        $_SESSION['auth-code'] = $data['code'];
        $_SESSION['auth-name'] = $data['name'];
        $_SESSION['auth-profile'] = $data['profile'];
        $_SESSION['auth-role'] = $data['role'];
        $_SESSION['auth-phone'] = $data['phone'];
        $_SESSION['auth-email'] = $data['email'];
        $_SESSION['auth-alternative'] = $data['alternative'];

        $_SESSION['auth-asana'] = $data['asana'];
        
        $_SESSION['auth-authorization'] = $data['authorization'];

        break;
      }
    }
  }

  if ($_GET['do'] == 'logout') {
    session_unset();
    session_destroy();
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
          <?php if (!isset($_SESSION['auth-user'])): ?>
            <li class="nav-item">
              <a class="btn btn-outline-light" href="index.php" data-toggle="modal" data-target="#moauthuser"><i class="fas fa-sign-in-alt"></i> Login</a>  
            </li>
          <?php else: ?> 
            <li class="nav-item">
              <span class="nav-link active" href="index.php?do=logout"> <?= $_SESSION['auth-name'] ?> (@<?= $_SESSION['auth-user'] ?>)</span>
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
        <div class="text-center">
          <div class="alert alert-warning">
            <h2 class="alert-heading"><b>Atenção</b>: esta é uma versão de teste. Chamados enviados por esta versão <b>não serão atendidos</b>.</h2>
          </div>
        </div>
      </div>

      <?php endif; ?>

      <?php if (isset($_POST['auth-user']) && !isset($_SESSION['auth-user'])): ?>

      <div>
        <div class="text-center">
          <div class="alert alert-danger">
            <h2 class="alert-heading">:(</h2>
            <h3>Ops!</h3>
            <p><b>Usuário ou senha inválidos!</b> Somente usuários avançados podem realizar login no sistema. Por favor, caso tenha certeza de suas credenciais tente realizar o <a class="btn btn-dark" href="index.php" data-toggle="modal" data-target="#moauthuser"><i class="fas fa-sign-in-alt"></i> Login</a>  novamente ou entre em contato com setor de informática.</p>
          </div>
        </div>
      </div>

      <?php endif; ?>

      <form id="task" method="post" action="haiosilver.php">
        
        <?php if (!isset($_SESSION['auth-user'])): ?>

          <div class="py-3">
            <div>
              <h2><i class="fas fa-unlock-alt"></i> Autenticação</h2>
            </div>

            <div class="pt-3">
              <div class="form-group">
                <label for="inauthuser"><i class="fas fa-id-card"></i> Usuário</label>
                <input type="text" class="form-control form-control-lg" id="inauthuser" name="auth-user" aria-describedby="inauthuserdetails" placeholder="Digite seu usuário ou e-mail. Por exemplo: 'joao' ou 'joao@exemplo.com'." maxlength="45" value="<?= $TASK['auth-user'] ?>">
                <div class="invalid-feedback">Por favor, digite seu usuário válido.</div>

                <small id="inauthuserdetails" class="form-text text-muted">O usuário será utilizado para identificação do colaborador na abertura do chamado.</small>
              </div>
          
              <div class="form-group">
                <label for="inauthpass"><i class="fas fa-key"></i> Senha</label>
                <input type="password" class="form-control form-control-lg" id="inauthpass" name="auth-pass" aria-describedby="inauthpassdetails" placeholder="Digite sua senha..." maxlength="15">
                <div class="invalid-feedback">Por favor, digite uma senha válida.</div>
                
                <small id="inauthpassdetails" class="form-text text-muted">A senha é utilizada para autenticar a solicitação.</small>
              </div>
            </div>
          </div>
        
        <?php endif; ?> 
        
        <?php if (!isset($_SESSION['auth-user'])): ?>

        <div class="py-3">
          <div>
            <h2><i class="fas fa-user"></i> Dados pessoais</h2>
          </div>

          <div class="pt-3">
            <div class="form-group">
              <label for="inuserprofile"><i class="fas fa-user-tie"></i> Perfil profissional</label>
              <select id="inuserprofile" name="user-profile" class="form-control form-control-lg">
                <option value="">Selecione o perfil...</option>
                <option value="">----------</option>
                <?php foreach ($CONFIG['profiles'] as $k => $v): ?> 
                  <option value="<?= $k ?>" <?= ($TASK['user-profile'] == $k ? 'selected' : '') ?>><?= $v['name'] ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Por favor, selecione o perfil com maior afinidade ao cargo que exerce na instituição.</div>

              <small id="inuserprofiledetails" class="form-text text-muted">O perfil será utilizada para levantarmos dados e estatísticas com base no tipo de atividade exercida pelos colaboradores. Com isso, poderemos adaptar nossas ações para com base nos problemas notificados por cada perfil de profissional.</small>
            </div>
        
            <div class="form-group">
              <label for="inusercontact"><i class="fas fa-phone-alt"></i> Telefone de contato</label>
              <input type="text" class="form-control form-control-lg" id="inusercontact" name="user-contact" aria-describedby="inusercontactdetails" placeholder="Digite seu telefone..." maxlength="15" value="<?= $TASK['user-contact'] ?>">
              <div class="invalid-feedback">Por favor, digite um telefone válido.</div>
              
              <small id="inusercontactdetails" class="form-text text-muted">O telefone será utilizado pela equipe de suporte para contactar o colaborador. Caso a equipe não consiga entrar em contato, a tarefa poderá ser fechada, portanto essa informação é de extrema importância.</small>
            </div>
          </div>
        </div>

        <?php endif; ?> 

        <div class="py-3">
          <div>
            <h2><i class="fas fa-location-arrow"></i> Localização</h2>
          </div>

          <div class="pt-3">
            <div class="form-group">
              <label for="inlocallabel"><i class="fas fa-tag"></i> Etiqueta (Opcional)</label>
              <input type="text" class="form-control form-control-lg" id="inlocallabel" name="local-label" aria-describedby="inlocallabeldetails" placeholder="Digite a etiqueta do equipamento..." maxlength="9" value="<?= $TASK['local-label'] ?>">
              <div class="invalid-feedback">Por favor, digite uma etiqueta válida.</div>

              <small id="inlocallabeldetails" class="form-text text-muted">A etiqueta será utilizada para idenficação precisa do equipamento, além de possibilitar análise remota em minutos. Por favor, digite a etiqueta do equipamento com problema ou, caso não seja um defeito no equipamento, a etiqueta do equipamento mais próximo do problema.</small>
            </div>
        
            <div class="form-group">
              <label for="inlocalunity"><i class="fas fa-building"></i> Unidade</label>
              <select id="inlocalunity" name="local-unity" class="form-control form-control-lg" >
                <option value="">Selecione a unidade...</option>
                <option value="">----------</option>
                <?php foreach ($CONFIG['units'] as $k => $v): ?>
                <option value="<?= $k ?>" <?= ($TASK['local-unity'] == $k ? 'selected' : '') ?>><?= $v['name'] ?> (<?= $k ?>)</option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Por favor, selecione uma unidade válida.</div>
              
              <small id="inlocalunitydetails" class="form-text text-muted">A unidade é utilizada para direcionarmos o atendimento de forma adequada. Atente-se que as unidades possuem setores semelhantes, de modo que a escolha da unidade correta é importantíssima.</small>
            </div>

            <div class="form-group">
              <label for="inlocaldepartament"><i class="fas fa-door-open"></i> Setor</label>
              <input type="text" class="form-control form-control-lg" id="inlocaldepartament" name="local-departament" aria-describedby="inlocaldepartamentdetails" placeholder="Digite o setor..." value="<?= $TASK['local-departament'] ?>">
              <div class="invalid-feedback">Por favor, digite um setor válido.</div>

              <small id="inlocaldepartamentdetails" class="form-text text-muted">O setor é utilizado para localizarmos rapidamente a origem do problema. Por favor, digite a informação e caso não esteja presente no local notifique os outros colaboradores para que possam compartilhar o problema ocorrido com o nosso suporte.</small>
            </div>
          </div>
        </div>         
       
        <div>
          <h2><i class="fas fa-edit"></i> Descrição</h2>
        </div>

        <div class="py-3">
          <div class="form-group">
            <label for="intaskcategory"><i class="fas fa-tags"></i> Categoria</label>
            <select id="intaskcategory" name="task-category" class="form-control form-control-lg">
              <option value="">Selecione a categoria...</option>
              <option value="">----------</option>
              <?php foreach ($CONFIG['categories'] as $k => $v): ?>
                <?php if (isset($_SESSION['auth-user']) && $_SESSION['auth-authorization'] >= $v['authorization'] || $v['authorization'] == 0): ?>
                  <option value="<?= $k ?>" <?= ($TASK['task-category'] == $k ? 'selected' : '') ?>><?= $v['name'] ?></option>
                <?php endif; ?> 
              <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Por favor, selecione uma categoria válida.</div>

            <small id="intaskcategorydetails" class="form-text text-muted">A categoria é utilizada para priorizarmos os atendimentos e levantarmos dados e estatísticas dos principais problemas encontrados nas unidades. Com isso, poderemos antecipar determinados problemas, atuando de forma preventiva economizando tempo e recursos dos colaboradores.</small>
          </div>

          <?php if (isset($_SESSION['auth-user']) && $_SESSION['auth-authorization'] >= 1000): ?>

          <div class="form-group">  
            <label for="intasksource"><i class="fas fa-tty"></i> Origem (Opcional)</label>
            <select id="intasksource" name="task-source" class="form-control form-control-lg">
              <option value="">Automático</option>
              <option value="">----------</option>
              <?php foreach ($CONFIG['sources'] as $k => $v): ?>
              <option value="<?= $k ?>" <?= ($TASK['task-source'] == $k ? 'selected' : '') ?>><?= $v['name'] ?></option>
              <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Por favor, selecione uma opção válida.</div>

            <small id="intasksourcedetails" class="form-text text-muted">A informação da origem é utilizada para levantarmos dados e estatísticas dos principais meios de contato e notificação dos problemas.</small>
          </div>

          <?php endif; ?> 

          <?php if (isset($_SESSION['auth-user']) && $_SESSION['auth-authorization'] >= 1000): ?>

          <div class="form-group">
            <label for="intaskresponsable"><i class="fas fa-users"></i> Responsável (Opcional)</label>
            <select id="intaskresponsable" name="task-responsable" class="form-control form-control-lg">
              <option value="">Automático</option>
              <option value="">----------</option>
              <?php foreach ($CONFIG['users'] as $k => $v): ?>
              <option value="<?= $k ?>" <?= ($TASK['task-responsable'] == $k ? 'selected' : '') ?>><?= $v['name'] ?> (@<?= strtolower($k) ?>)</option>
              <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Por favor, selecione um responsável válido.</div>

            <small id="intaskresponsabledetails" class="form-text text-muted">A definição do responsável é utilizado para atribuir automaticamente a tarefa a pessoa selecionada. Por padrão, a tarefa é 'automaticamente' atribuida a um responsável conforme unidade de origem ou escala de atendimento.</small>
          </div>

          <?php endif; ?> 

          <div class="form-group">
            <label for="intaskdescription"><i class="fas fa-edit"></i> Descrição</label>
            <textarea class="form-control" id="intaskdescription" name="task-description" rows="7"><?= $TASK['task-description'] ?></textarea>
            <div class="invalid-feedback">Por favor, digite uma descrição válida (mínimo de 25 caracteres).</div>
            
            <small id="intaskdescriptiondetails" class="form-text text-muted">A descrição é a informação mais relevante da solicitação de suporte. Não deixei de entrar em detalhes e inserir todo o conteúdo necessário para o atendimento do chamado. A forma como descrição está escrita influencia na priorização do atendimento, isso significa que caso as informações não estejam completas outras solicitações serão atendidas primeiro.</small>
          </div>

          <div class="form-group">
            <label for="intaskattachment"><i class="fas fa-upload"></i> Anexo (Opcional)</label>
            <input id="intaskattachment" name="task-attachment[]" type="file" class="file" multiple data-show-upload="true" data-show-caption="true">
            <div class="invalid-feedback">Por favor, selecione um arquivo válido.</div>
        
            <small id="intaskattachmentdetails" class="form-text text-muted">Se necessário, faça o upload do arquivo ou imagem para complementar a solicitação e descrição do chamado. Caso o arquivo seja muito grande, recomendamos enviar o link de compartilhamento na núvem (OneDrive, Drive, Dropbox etc.) na descrição do chamado ou até mesmo por e-mail.</small>
          </div> 
        </div>

        <div>
          <input type="hidden" name="task-uuid" value="<?= $UUID ?>">
        </div>

        <div class="pt-3 pb-5">
          <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-paper-plane"></i> Enviar</button>
        </div>
      </form>

      <div class="py-3">
        <div class="text-center">
          <p><small><i class="fas fa-code"></i> with <i class="fas fa-coffee"></i> | Powered by <a href="https://github.com/edcaraujo/jumintus/">Jumintus</a><br/><a href="https://github.com/edcaraujo/jumintus/"><i class="fas fa-code-branch"></i> <?= $CONFIG['jumintus']['version'] ?></a></small></p>
        </div>
      </div>
    </div>

    <div class="modal fade" id="moauthuser" tabindex="-1" role="dialog" aria-labelledby="moauthusertitle" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="moauthusertitle"><i class="fas fa-exclamation-triangle"></i> Identifique-se</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="py-3">
              <div>
                <div>
                  <div class="text-center">
                    <div class="alert alert-info">
                      <h2 class="alert-heading"><?= $CONFIG['jumintus']['icon'] ?></h2>
                      <h3>Atenção!</h3>
                      <p>Este acesso é exclusivo para usuários avançados.</p>
                    </div>
                  </div>
                </div>
                <form method="post" action="index.php" id="moauthuserform">
                  <div class="form-group">
                    <label for="inmoauthuser"><i class="fas fa-id-card"></i> Usuário</label>
                    <input type="text" class="form-control form-control-lg" id="inmoauthuser" name="auth-user" aria-describedby="inmoauthuserdetails" placeholder="Digite seu usuário ou e-mail." maxlength="45" value="<?= $TASK['auth-user'] ?>">
                    <div class="invalid-feedback">Por favor, digite seu usuário válido.</div>

                    <small id="inmoauthuserdetails" class="form-text text-muted">O usuário será utilizado para identificação do colaborador na abertura do chamado.</small>
                  </div>
              
                  <div class="form-group">
                    <label for="inmoauthpass"><i class="fas fa-key"></i> Senha</label>
                    <input type="password" class="form-control form-control-lg" id="inmoauthpasspass" name="auth-pass" aria-describedby="inmoauthpassdetails" placeholder="Digite sua senha" maxlength="15">
                    <div class="invalid-feedback">Por favor, digite uma senha válida.</div>
                    
                    <small id="inmoauthpassdetails" class="form-text text-muted">A senha é utilizada para autenticar a solicitação.</small>
                  </div>

                  <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-sign-in-alt"></i> Continuar</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="deps/jquery/js/jquery.min.js"></script>

    <script src="deps/bootstrap/js/bootstrap.min.js"></script>
    
    <script src="deps/fileinput/js/fileinput.min.js"></script>
    <script src="deps/fileinput/js/theme/theme.min.js"></script>
    <script src="deps/fileinput/js/locales/pt-BR.js"></script>
    <script src="deps/fileinput/js/plugins/piexif.min.js"></script>
    <script src="deps/fileinput/js/plugins/sortable.min.js"></script>

    <script type="text/javascript">
      $("#intaskattachment").fileinput({
          uploadUrl: "utils/file-upload.php",
          uploadAsync: true,
          uploadExtraData: {UUID: "<?= $UUID ?>"},

          deleteUrl: "utils/file-delete.php",
          
          maxFileCount: 5,
          maxFileSize: 25000,
          maxFilePreviewSize: 25000,

          showRemove: false,         

          theme: "fas",
          language: "pt-BR",

          initialPreviewAsData: true,
          initialPreviewFileType: 'text',

          previewFileExtSettings: {
            'doc': function(ext) {
                return ext.match(/(doc|docx)$/i);
            },
            'xls': function(ext) {
                return ext.match(/(xls|xlsx)$/i);
            },
            'ppt': function(ext) {
                return ext.match(/(ppt|pptx)$/i);
            },
            'zip': function(ext) {
                return ext.match(/(zip|rar|tar|gzip|gz|7z)$/i);
            },
            'htm': function(ext) {
                return ext.match(/(htm|html)$/i);
            },
            'txt': function(ext) {
                return ext.match(/(txt|ini|csv|java|php|js|css)$/i);
            },
            'mov': function(ext) {
                return ext.match(/(avi|mpg|mkv|mov|mp4|3gp|webm|wmv)$/i);
            },
            'mp3': function(ext) {
                return ext.match(/(mp3|wav)$/i);
            }
        },

        previewFileIconSettings: {
            'doc': '<i class="fas fa-file-word text-secondary"></i>',
            'xls': '<i class="fas fa-file-excel text-secondary"></i>',
            'ppt': '<i class="fas fa-file-powerpoint text-secondary"></i>',
            'pdf': '<i class="fas fa-file-pdf text-secondary"></i>',
            'zip': '<i class="fas fa-file-archive text-secondary"></i>',
            'htm': '<i class="fas fa-file-code text-secondary"></i>',
            'txt': '<i class="fas fa-file-alt text-secondary"></i>',
            'mov': '<i class="fas fa-file-video text-secondary"></i>',
            'mp3': '<i class="fas fa-file-audio text-secondary"></i>',
            'jpg': '<i class="fas fa-file-image text-secondary"></i>', 
            'gif': '<i class="fas fa-file-image text-secondary"></i>', 
            'png': '<i class="fas fa-file-image text-secondary"></i>'    
        },
      });

      $("#inusercontact").on("keyup", function(e)
      {
        $(this).val(
            $(this).val()
            .replace(/\D/g, '')
            .replace(/^(\d{2})(\d{4})?(\d{4})?/, "($1) $2-$3")
            .replace(/^\((\d{2})\) (\d{1})(\d{3})\-(\d{1})(\d{4})/, "($1) $2 $3$4-$5"));
      });

      $("#inlocallabel").on("keyup", function(e)
      {
        $(this).val(
            $(this).val()
            .toUpperCase());
      });

      $("#task").submit(function( event ) {
        var e;

        if ($("#inauthuser").length)
        {
          if (!$("#inauthuser").val().match(/^.{6,}/))
          {
            $("#inauthuser").addClass("is-invalid");

            if (!e)
              e = "#inauthuser";
          }
          else
          {
            $("#inauthuser").removeClass("is-invalid");
          }
        }

        if ($("#inauthpass").length)
        {
          if (!$("#inauthpass").val().match(/^.{8,}/))
          {
            $("#inauthpass").addClass("is-invalid");

            if (!e)
              e = "#inauthpass";
          }
          else
          {
            $("#inauthpass").removeClass("is-invalid");
          }
        }

        if ($("#inuserprofile option:selected").text() == "Selecione o perfil..." ||
          $("#inuserprofile option:selected").text() == "----------")
        {
          $("#inuserprofile").addClass("is-invalid");

          if (!e)
            e = "#inuserprofile";
        }
        else
        {
          $("#inuserprofile").removeClass("is-invalid");
        }

        if ($("#inusercontact").length)
        {
          if (!$("#inusercontact").val().match(/^\(\d{2}\) \d?\s?\d{4}\-\d{4}$/))
          {
            $("#inusercontact").addClass("is-invalid");

            if (!e)
              e = "#inusercontact";
          }
          else
          {
            $("#inusercontact").removeClass("is-invalid");
          }
        }     

        if ($("#inlocalunity option:selected").text() == "Selecione a unidade..." ||
            $("#inlocalunity option:selected").text() == "----------")
        {
          $("#inlocalunity").addClass("is-invalid");

          if (!e)
            e = "#inlocalunity";
        }
        else
        {
          $("#inlocalunity").removeClass("is-invalid");
        } 
        
        if ($("#inlocaldepartament").length)
        {
          if (!$("#inlocaldepartament").val().match(/^.{2,}/))
          {
            $("#inlocaldepartament").addClass("is-invalid");

            if (!e)
              e = "#inlocaldepartament";
          }
          else
          {
            $("#inlocaldepartament").removeClass("is-invalid");
          }
        }

        if ($("#intaskcategory option:selected").text() == "Selecione a categoria..." ||
          $("#intaskcategory option:selected").text() == "----------")
        {
          $("#intaskcategory").addClass("is-invalid");

          if (!e)
            e = "#intaskcategory";
        }
        else
        {
          $("#intaskcategory").removeClass("is-invalid");
        }   

        if ($("#intasksource option:selected").text() == "----------")
        {
          $("#intasksource").addClass("is-invalid");

          if (!e)
            e = "#intasksource";
        }
        else
        {
          $("#intasksource").removeClass("is-invalid");
        }

        if ($("#intaskresponsable option:selected").text() == "----------")
        {
          $("#intaskresponsable").addClass("is-invalid");

          if (!e)
            e = "#intaskresponsable";
        }
        else
        {
          $("#intaskresponsable").removeClass("is-invalid");
        }

        if ($("#intaskdescription").val().length < 25)
        {
          $("#intaskdescription").addClass("is-invalid");

          if (!e)
            e = "#intaskdescription";
        }
        else
        {
          $("#intaskdescription").removeClass("is-invalid");
        }

        if ($('#intaskattachment').fileinput('getFilesCount')) 
        {
          var msg = '<div class="kv-fileinput-error"><div class="text-center"><div class="alert alert-danger"><h2 class="alert-heading">:(</h2><h3>Ops!</h3><p><b>Parece que vocês esqueceu de fazer o <i class="fas fa-upload"></i> Upload de algum arquivo.</b> Por favor, finalize o upload de todos os aquivos antes de enviar o restante das informações.</p></div></div></div>';
          
          $('.file-drop-zone').append(msg);
          
          if (!e)
            e = "#intaskattachment";
        }
        else
        {
          $('.kv-fileinput-error').remove();
        }

        if (e)
        {
          $('html, body').animate({
              scrollTop: $(e).offset().top - 100
          }, 500);

          event.preventDefault();
        }
      });
    </script>
  </body>
</html>