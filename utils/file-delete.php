<?php
header('Content-Type: application/json');
$r = delete(); echo json_encode($r);
exit();
 
function delete() {
    $upload = '../uploads/';

    if (!unlink('../uploads/'.$_POST['key'])) {
        return ['error' => 'Ops! Ocorreu algum problema ao remover o arquivo \''.$FILENAME.'\'. Tente novamente ou entre em contato com o setor de infomática.'];
    }

    return [];
}
?>