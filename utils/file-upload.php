<?php
header('Content-type: application/json');
$r = upload(); echo json_encode($r);
exit();
 
function upload() {
    $CONFIG = include('../config/jumintus/jumintus.config.php');  

    $input = 'task-attachment';
    if (empty($_FILES[$input])) {
        return [];
    }

    $upload = 'data'.'/'.'tmp'.'/'.$_POST['UUID'].'/attachments';

    if (!is_dir('../'.$upload)) {
        mkdir('../'.$upload,0777, true);
    }

    $n = count($_FILES[$input]['name']);
    
    for ($i = 0; $i < $n; $i++) {
        $filepath = $_FILES[$input]['tmp_name'][$i];

        $filename = $_FILES[$input]['name'][$i];
        $filesize = $_FILES[$input]['size'][$i]; 

        if ($filepath != ""){
        
            $id = md5_file($filepath);
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
           
            $type = 'object';

            if (in_array(strtolower($ext),['pdf']))
                $type = 'pdf';

            if (in_array(strtolower($ext),['jpg','jpeg','png','gif','svg']))
                $type = 'image';

            if (in_array(strtolower($ext),['txt','md']))
                $type = 'text';

            $newfilename = $id.'.'.$ext;
            $newfilepath = '../'.$upload.'/'.$newfilename;

            if(move_uploaded_file($filepath, $newfilepath)) {
                return [
                    'initialPreview' => [
                       $upload.'/'.$newfilename
                    ],

                    'initialPreviewConfig' => [[
                        'key' => $newfilepath,
                        'caption' => $filename,
                        'size' => $filesize,
                        'type' => $type,
                        'showZoom' => false,
                        'downloadUrl' => false, 
                        'url' => $CONFIG['url'].'/'.'utils/file-delete.php',
                    ]],

                    'initialPreviewAsData' => true
                ];  
            } else {
                return [
                    'error' => [
                        'Ops! Ocorreu um problema ao realizar o upload do arquivo \''.$filename.'\'. Tente novamente mais tarde.'
                    ]
                ];
            }
        } else {
            return [
                'error' => [
                    'Ops! Ocorreu um problema ao realizar o upload do arquivo \''.$filename.'\'. Tente novamente mais tarde.'
                ]
            ];
        }
    }

    return [];
}
?>