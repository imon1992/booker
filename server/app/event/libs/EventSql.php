<?php
//include ('../../config.php');


class EventSql
{
    private $dbConnect;

    public function __construct()
    {
        $this->dbConnect=DbConnection::getInstance();
    }

    public function getEventInfo($bedrommId,$userId,$eventId,$starttime)
    {
        if($this->dbConnect !== 'connect error')
         {
             $stmt =$this->dbConnect->prepare('
                 SELECT e.description,e.timeOfCreate,et.startTime,et.endTime,bu.name from events as e
                 INNER JOIN eventsTime as et on et.event_id = e.id
                 INNER JOIN bookerUsers as bu on bu.id = user_id
                 WHERE bu.id = :userId and et.event_id = :eventId and e.boardroom_id = :boardroomId and et.startTime = :startTime
                 ');
             $stmt->bindParam(':userId',$userId);
$stmt->bindParam(':eventId',$eventId);
$stmt->bindParam(':boardroomId',$bedrommId);
$stmt->bindParam(':startTime',$starttime);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
         }else
        {
        $result = false;
        }

        return $result;
    }

    public function getEventsByMonth($firstDate,$lastDate,$id)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('
            SELECT e.id,e.user_id,e.boardroom_id,e.date,et.startTime,et.endTime FROM events as e
            INNER JOIN eventsTime as et on et.event_id = e.id
            where e.date BETWEEN :firstDate AND :lastDate AND boardroom_id = :id
            ');
            $stmt->bindParam(':firstDate',$firstDate);
            $stmt->bindParam(':lastDate',$lastDate);
            $stmt->bindParam(':id',$id);

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

    public function addNewEvent($userId,$boardRoom,$description,$dates,$timeOfCreate,$recursive)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('
                 INSERT INTO events(user_id,boardroom_id,description,date,timeOfCreate,recursive)
                 VALUES(:userId,:roomId,:desc,:date,:timeOfCreate,:recursive)
                 ');

            foreach($dates as &$date)
            {
                $stmt->bindParam(':userId',$userId);
                $stmt->bindParam(':roomId',$boardRoom);
                $stmt->bindParam(':desc',$description);
                $stmt->bindParam(':date',$date);
                $stmt->bindParam(':timeOfCreate',$timeOfCreate);
                $stmt->bindParam(':recursive',$recursive);
                if($stmt->execute())
                {
                    $result[] = $this->dbConnect->lastInsertId();
                } else
                {
                    $result = false;
                }
            }
        }else
        {
            $result = false;
        }

        return $result;
    }

    public function addEventTime($ids,$startTime,$endTime)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('
                 INSERT INTO eventsTime(event_id,startTime,endTime)
                 VALUES(:eventId,:startTime,:endTime)
                 ');

            foreach($ids as &$id)
            {
                $stmt->bindParam(':eventId',$id);
                $stmt->bindParam(':startTime',$startTime);
                $stmt->bindParam(':endTime',$endTime);
                $result = $stmt->execute();

            }
        }else
        {
            $result = false;
        }

        return $result;
    }

    public function checkEventDateTime($dates,$timeStart,$timeEnd)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('
                 SELECT e.date
                 from events as e
                 INNER JOIN eventsTime as et on et.event_id = e.id
                 WHERE e.date = :date AND ((:timeStart BETWEEN et.startTime AND et.endTime) OR (:timeEnd BETWEEN et.startTime AND et.endTime))
                 ');
            foreach($dates as &$date)
            {
                //var_dump($date);
                $stmt->bindParam(':date',$date);
                $stmt->bindParam(':timeStart',$timeStart);
                $stmt->bindParam(':timeEnd',$timeEnd);
                $stmt->execute();
                while($assocRow = $stmt->fetch(PDO::FETCH_ASSOC))
                {
                    if(!empty($assocRow))
                    {
                        $result[]=$assocRow;
                    }
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


//$c = new EventSql();
//$c->getEventsByMonth('2017-10-01','2017-10-31');
