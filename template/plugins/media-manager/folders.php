<?php
include 'server_config.php';
$server = new Server();

if(isset($_GET['loadfolder']) && $_GET['loadfolder']=='alfa'){
    $result = [];
    $folderToLoad = isset($_GET['fname']) ? $_GET['fname'] : '';
    $folderPath = ROOTDIR . "/{$folderToLoad}";
    $folderUrl  = ROOTURL . "/{$folderToLoad}";

    if(file_exists($folderPath)){
        $files = scandir($folderPath);
        $mtype = finfo_open(FILEINFO_MIME_TYPE);
        $i = 0;
        foreach($files as $file){
            if(!in_array($file, ['.','..'])){
                $filepath = $folderPath . "/{$file}";
                $mimeType = finfo_file($mtype, $filepath);

                $imgUrl   = $folderUrl . "/{$file}";
                $result[$i] = ['name'=>$file, 'type'=>$mimeType, 'path'=>$filepath, 'url'=>$imgUrl];
                if(in_array($mimeType, ['image/png', 'image/jpeg', 'image/gif']) ){
                    $result[$i]['type'] = 'image';
                }

                if($mimeType == 'directory'){
                    $result[$i]['icon_url'] = MMURL . '/icons/folder.png';
                }
                else if($mimeType == 'application/pdf'){
                    $result[$i]['type'] = 'pdf';
                    $result[$i]['icon_url'] = MMURL . '/icons/pdf.png';
                }

                $i++;
            }
        }
        echo json_encode(['status'=>true, 'files' => $result], true);
    }else{
        echo json_encode(['status'=>false, 'message'=>'Folder not exists!']);
    }
}
if(isset($_GET['filename']) && isset($_GET['detail'])){
    $filename = addslashes($_GET['filename']);

    $fileInfo = $server->readFileInfo($filename);
    if(!empty($fileInfo) && is_array($fileInfo)){
        echo json_encode(['status'=>true, 'info'=>$fileInfo]);
    }else{
        echo json_encode(['status'=>false]);
    }
}

if(isset($_POST['mode']) && $_POST['mode']=='updatefile'){
    $id= addslashes($_POST['id']);
    $filename= addslashes($_POST['filename']);
    $title= addslashes($_POST['title']);
    $alt= addslashes($_POST['alt']);
    $description= addslashes($_POST['description']);
    $type = addslashes($_POST['type']);
    $update = $server->updateFile($id, $type, $filename, $title, $alt, $description);

    if($update){
        echo json_encode(['status'=>true]);
    }else{
        echo json_encode(['status'=>false]);
    }

}

if(isset($_POST['mode']) && $_POST['mode']=='delete'){
    $id = addslashes($_POST['id']);
    $path = $_POST['path'];
    $delete = $server->deleteFile($id, $path);
    if($delete){
        echo json_encode(['status'=>true]);
    }else{
        echo json_encode(['status'=>false]);
    }
}
