<?php

define('ROOTDIR', 'D:\SERVER\YII_PROJECT\travelportal\backend\web');
define('ROOTURL', 'http://yii.localhost/travelportal/backend/web');
define('MMURL', 'http://yii.localhost/travelportal/backend/web/js/media-manager');

class Server {
    private $connection;

    public function __construct(){
        $this->getConnection();
    }

    private function getConnection(){
        $dsn = 'mysql:dbname=travelportal;host=localhost;port=3306';
        $username = 'root';
        $password = '';

        try {
            $this->connection = new PDO($dsn, $username, $password); // also allows an extra parameter of configuration
        } catch(PDOException $e) {
            die('Could not connect to the database:<br/>' . $e);
        }
    }


    public function readFileInfo($filename){
        $stmt = $this->connection->prepare("SELECT * FROM uploads WHERE filename=:filename; LIMIT 1");
        $stmt->bindValue(':filename', $filename);
        $stmt->execute();
        $data = $stmt->Fetch();
        return $data;
    }

    public function updateFile($id, $type, $filename, $title, $alt, $description){
        if(!empty($id)){
            $stmt = $this->connection->prepare("UPDATE uploads SET type=:type, title=:title, alt=:alt, description=:description WHERE filename=:filename AND id=:id ; ");
            return $stmt->execute([':title'=>$title, ':alt'=>$alt, ':description'=>$description, ':filename'=>$filename, ':id'=>$id, ':type'=>$type]);
        }else{
            $stmt = $this->connection->prepare("INSERT INTO uploads (filename, type, title, alt, description) VALUES (:filename, :type, :title, :alt, :description) ; ");
            return $stmt->execute([':title'=>$title, ':alt'=>$alt, ':description'=>$description, ':filename'=>$filename, ':type'=>$type]);
        }
    }

    public function deleteFile($id, $path){
        if(file_exists(ROOTDIR.'/'.$path)){
            unlink(ROOTDIR.'/'.$path);
            $stmt = $this->connection->prepare("DELETE FROM uploads WHERE id=:id");
            return $stmt->execute(['id'=>$id]);
        }else{
            return false;
        }
    }
}



// -----------------------------------------------------------------------------
