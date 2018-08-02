<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

class CORE{
    private $registry;
    private $action;
    private $db;
    private $uuid;
    private $usersFilePath;
    private $users = array();

    function __construct($registry)
    {
        $this->registry = $registry;
        $this->db = $this->registry['DB']->db;
        $this->usersFilePath = ROOT_DIR.'users';

        if(isset($_GET['action'])){
            $this->action = strip_tags($_GET['action']);
        }
        if(isset($_GET['uuid'])){
            $this->uuid = strip_tags($_GET['uuid']);
        }

        switch ($this->action) {
            case 'load_from_db':
                $this->loadFromDB();
                break;
            case 'generate_db':
                $this->generateDB();
                break;
            case 'remove_from_db':
                $this->removeFromDB();
                break;
            case 'load_from_file':
                $this->loadFromFile();
                break;
            case 'generate_file':
                $this->generateFile();
                break;
            case 'remove_from_file':
                $this->removeFromFile();
                break;
            default:
                $this->loadFromFile();
                $this->action = 'load_from_file';
                break;
        }
        include TPL_DIR.'template.php';
        exit();
    }
    function loadFromFile(){
        if(file_exists($this->usersFilePath)){
            $file = fopen($this->usersFilePath, 'r');
            flock($file, LOCK_SH);
            $this->users = unserialize(fread($file, filesize($this->usersFilePath)));
            flock($file, LOCK_UN);
            fclose($file);
        } else {
            $this->users = [];
        }
        $this->registry['Logger']->log('Load from file');
    }
    function loadFromDB(){
        $this->users = Cache::get('db');

        if ($this->users === null) {
            foreach ($this->db->query('SELECT * FROM users')->fetchAll() as $row) {
                $this->users[$row['uuid']] = [
                    'uuid' => $row['uuid'],
                    'first_name' =>  $row['first_name'],
                    'last_name' =>  $row['last_name'],
                    'location' => json_decode($row['address'], true),
                    'email' => $row['email'],
                    'phone' => $row['phone'],
                    'registered_at' => $row['registered_at'],
                ];
            }
            Cache::set('db', $this->users);
            $this->registry['Logger']->log('Load from db');
        } else {
            $this->registry['Logger']->log('Load from cache');
        }
    }
    function removeFromDB(){
        $this->db->exec('DELETE FROM users WHERE uuid = "' . $this->uuid . '"');

        Cache::reset('db');
        $this->registry['Logger']->log('Remove from db ' . $this->uuid);

        header("Location: /?action=load_from_db");
    }
    function removeFromFile(){
        $file = fopen($this->usersFilePath, 'a+');
        flock($file, LOCK_EX);
        $this->users = unserialize(fread($file, filesize($this->usersFilePath)));
        flock($file, LOCK_UN);

        unset($this->users[$this->uuid]);

        flock($file, LOCK_EX);
        ftruncate($file, 0);
        fwrite($file, serialize($this->users));
        fflush($file);
        flock($file, LOCK_UN);
        fclose($file);

        $this->registry['Logger']->log('Remove from file ' . $this->uuid);

        header("Location: /?action=load_from_file");
    }
    function generateDB(){
        foreach (json_decode(file_get_contents('https://randomuser.me/api/?results=5&nat=gb'), true)['results'] as $data) {
            $uuid = uniqid();
            $this->users[$uuid] = [
                'first_name' =>  $data['name']['first'],
                'last_name' =>  $data['name']['last'],
                'location' => $data['location'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'registered_at' => $data['registered']['date'],
            ];
        }

        $this->db->exec('DELETE FROM users');

        foreach ($this->users as $uuid => $user) {
            $serializedLocation = json_encode($user['location']);
            $this->db->exec("INSERT INTO users (uuid, first_name, last_name, email, phone, address, registered_at) VALUES 
                            ('$uuid', 
                            '{$user['first_name']}', 
                            '{$user['last_name']}', 
                            '{$user['email']}', 
                            '{$user['phone']}', 
                            '{$serializedLocation}', 
                            '{$user['registered_at']}')");
        }

        Cache::reset('db');
        $this->registry['Logger']->log('Fill db');

        header("Location: /?action=load_from_db");
    }
    function generateFile(){
        foreach (json_decode(file_get_contents('https://randomuser.me/api/?results=5&nat=gb'), true)['results'] as $data) {
            $uuid = uniqid();
            $this->users[$uuid] = [
                'uuid' => $uuid,
                'first_name' =>  $data['name']['first'],
                'last_name' =>  $data['name']['last'],
                'location' => $data['location'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'registered_at' => $data['registered']['date'],
            ];
        }

        $file = fopen($this->usersFilePath, 'a+');
        flock($file, LOCK_EX);
        ftruncate($file, 0);
        fwrite($file, serialize($this->users));
        fflush($file);
        flock($file, LOCK_UN);
        fclose($file);

        $this->registry['Logger']->log('Fill file');

        header("Location: /?action=load_from_file");
    }

    function parseLocationArray ($arr){
        $str = "";
        foreach($arr as $key => $val){
            if(is_array($arr[$key])){
                $str .= implode(", ", $val);
            } elseif (is_string($arr[$key])){
                $str .= $val.", ";
            }
        }
        return $str;
    }
    function parseDate($date){
        $date = new DateTime($date);
        $monthes = [
            '01' => 'января',
            '02' => 'февраля',
            '03' => 'марта',
            '04' => 'апреля',
            '05' => 'мая',
            '06' => 'июня',
            '07' => 'июля',
            '08' => 'августа',
            '09' => 'сентября',
            '10' => 'октября',
            '11' => 'ноября',
            '12' => 'декабря',
        ];
        $month = $monthes[$date->format('m')];
        return $date->format('d ' . $month . ' Y H:i:s');
    }
    public function showGenerateButton(){
        $btn = '';
        if($this->action == 'load_from_db'){
            $btn = '<a class="btn btn-success float-right mb-2" href="/?action=generate_db" role="button">Сгенерировать в базе</a>';
        } elseif($this->action == 'load_from_file'){
            $btn = '<a class="btn btn-success float-right mb-2" href="/?action=generate_file" role="button">Сгенерировать в файле</a>';
        }
        print $btn;
    }
    public function showUsersTable(){
        if($this->users != null){
            $count = 1;
            foreach($this->users as $user){
                print '<tr>';
                print '<th scope="row">'.$count++.'</th>';
                print '<td>'.$user['first_name'].'</td>';
                print '<td>'.$user['last_name'].'</td>';
                print '<td>'.$user['phone'].'</td>';
                print '<td>'.$user['email'].'</td>';
                print '<td>'.$this->parseLocationArray($user['location']).'</td>';
                print '<td>'.$this->parseDate($user['registered_at']).'</td>';
                print '<td>'.$this->showDelButton($user['uuid']).'</td>';
                print '</tr>';
            }
        }
    }
    public function showDelButton($uuid){
        $btn = '';
        if($this->action == 'load_from_db'){
            $btn = '<a href="/?action=remove_from_db&uuid='.$uuid.'" class="btn btn-danger">Удалить</a>';
        } elseif($this->action == 'load_from_file') {
            $btn = '<a href="/?action=remove_from_file&uuid='.$uuid.'" class="btn btn-danger">Удалить</a>';
        }
        return $btn;
    }
}