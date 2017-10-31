<?php
//include ('../../config.php');


class EventSql
{
    private $dbConnect;

    public function __construct()
    {
        $baseAndHostDbName = MY_SQL_DB . ':host=' . MY_SQL_HOST . '; dbname=' . MY_SQL_DB_NAME;
        try {
            $this->dbConnect = new PDO($baseAndHostDbName, MY_SQL_USER, MY_SQL_PASSWORD);
            $this->dbConnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $this->dbConect = 'connect error';
        }
    }

    public function getEventsByMonth($firstDate,$lastDate)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('
            SELECT e.id,e.user_id,e.badroom_id,e.date,et.startTime,et.endTime FROM events as e
            INNER JOIN eventsTime as et on et.event_id = e.id
            where e.date BETWEEN :firstDate AND :lastDate
            ');
            $stmt->bindParam(':firstDate',$firstDate);
            $stmt->bindParam(':lastDate',$lastDate);

            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
//            var_dump($data);
            $result = [];
            foreach ($data as $val) {
//                var_dump($val);
                $events = [];
                $a = [];
                $d = [];

                if (!isset($result[$val['id']])) {
                    $a['startTime'] = substr($val['startTime'], 0, 5);
                    $a['endTime'] = substr($val['endTime'], 0, 5);
                    array_unshift($d,$a);
                    $events['id'] = $val['id'];
//                    $events['badroom_id'] = $val['badroom_id'];
                    $events['userId'] = $val['user_id'];
                    $events['date'] = $val['date'];
                    $events['events'] = $a;
                    $result[$val['id']] = $events;
                }
                if($result[$val['id']]['id'] ==  $val['id'])
                {
                    $a['startTime'] = substr($val['startTime'], 0, 5);
                    $a['endTime'] = substr($val['endTime'], 0, 5);
                    array_unshift($result[$val['id']]['events'],$a);
                    unset($result[$val['id']]['events']['startTime']);
                    unset($result[$val['id']]['events']['endTime']);
                }
            }
        }else
        {
            $result = false;
        }
        return $result;
    }

//    public function createNewUser($name,$surname,$phone,$email,$login,$password,$discount,$isActive,$role)
//    {
//        if($this->dbConnect !== 'connect error')
//        {
//            $stmt =$this->dbConnect->prepare('INSERT INTO client(name,surname,phone,email,login,password,discount,isActive,role)
//                VALUES(:name,:surname,:phone,:email,:login,:password,:discount,:isActive,:role)');
//            $stmt->bindParam(':name',$name);
//            $stmt->bindParam(':surname',$surname);
//            $stmt->bindParam(':phone',$phone);
//            $stmt->bindParam(':email',$email);
//            $stmt->bindParam(':login',$login);
//            $stmt->bindParam(':password',$password);
//            $stmt->bindParam(':discount',$discount);
//            $stmt->bindParam(':isActive',$isActive);
//            $stmt->bindParam(':role',$role);
//
//            $result = $stmt->execute();
//            return $result;
//        }else
//        {
//            return 'error';
//        }
//    }
//
//    public function getIdPassRoleActiveByLogin($login)
//    {
//        if($this->dbConnect !== 'connect error')
//        {
//            $stmt =$this->dbConnect->prepare('SELECT id,password,role,isActive
//                FROM client
//                WHERE login=:login');
//
//            $stmt->bindParam(':login',$login);
//            $stmt->execute();
//            while($assocRow = $stmt->fetch(PDO::FETCH_ASSOC))
//            {
//                $result[]=$assocRow;
//            }
//            return $result;
//        }else
//            {
//                return 'error';
//            }
//    }
//
//    public function getIdHashByCookieId($id)
//    {
//        if($this->dbConnect !== 'connect error')
//        {
//            $stmt =$this->dbConnect->prepare('SELECT hash,id
//                FROM client
//                WHERE id=:id');
//
//            $stmt->bindParam(':id',$id);
//            $stmt->execute();
//            while($assocRow = $stmt->fetch(PDO::FETCH_ASSOC))
//            {
//                $result[]=$assocRow;
//            }
//            return $result;
//        }else
//            {
//                return 'error';
//            }
//    }
//
//    public function setNewHash($hash,$id)
//    {
//        if($this->dbConnect !== 'connect error')
//        {
//            $stmt =$this->dbConnect->prepare('UPDATE client
//                SET hash= :hash
//                WHERE id=:id');
//
//            $stmt->bindParam(':hash',$hash);
//            $stmt->bindParam(':id',$id);
//            $result = $stmt->execute();
//            return $result;
//        }else
//        {
//            return 'error';
//        }
//    }
//
//    public function checkUser($login,$password)
//    {
//        if($this->dbConnect !== 'connect error')
//        {
//            $stmt =$this->dbConnect->prepare('SELECT id
//                FROM client
//                WHERE login=:login AND password=:password');
//
//            $stmt->bindParam(':login',$login);
//            $stmt->bindParam(':password',$password);
//            $stmt->execute();
//            $result = $stmt->rowCount();
//            return $result;
//        }else
//        {
//            return 'error';
//        }
//    }
//
//    public function checkUserLogin($login)
//    {
//        if($this->dbConnect !== 'connect error')
//        {
//            $stmt =$this->dbConnect->prepare('SELECT COUNT(id)
//                FROM client
//                WHERE login=:login');
//
//            $stmt->bindParam(':login',$login);
//            $stmt->execute();
//            $result = $stmt->fetch(PDO::FETCH_ASSOC);
//            return $result['COUNT(id)'];
//        }else
//        {
//            return 'error';
//        }
//    }
//
//    public function checkAdminHash($hash)
//    {
//        if($this->dbConnect !== 'connect error')
//        {
//            $stmt =$this->dbConnect->prepare('SELECT COUNT(id)
//                FROM client
//                WHERE hash=:hash AND role=\'admin\'');
//
//            $stmt->bindParam(':hash',$hash);
//            $stmt->execute();
//            $result = $stmt->fetch(PDO::FETCH_ASSOC);
//            return $result['COUNT(id)'];
//        }else
//        {
//            return 'error';
//        }
//    }
//
//    public function checkUserHash($hash,$id)
//    {
//        if($this->dbConnect !== 'connect error')
//        {
//            $stmt =$this->dbConnect->prepare('SELECT COUNT(id)
//                FROM client
//                WHERE hash=:hash AND id=:id');
//
//            $stmt->bindParam(':hash',$hash);
//            $stmt->bindParam(':id',$id);
//            $stmt->execute();
//            $result = $stmt->fetch(PDO::FETCH_ASSOC);
//            return $result['COUNT(id)'];
//        }else
//        {
//            return 'error';
//        }
//    }
}


$c = new EventSql();
$c->getEventsByMonth('2017-10-01','2017-10-31');