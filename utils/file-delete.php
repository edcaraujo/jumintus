<?php
header('Content-Type: application/json');
$r = delete(); echo json_encode($r);
exit();
 
function delete() {
    if (!unlink($_POST['key'])) {
        return ['error' => 'Ops! Ocorreu algum problema ao remover o arquivo \''.basename($_POST['key']).'\'. Tente novamente ou entre em contato com o setor de infomática.'];
    }

    return [];
}
?>