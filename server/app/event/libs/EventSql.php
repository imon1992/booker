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
                 SELECT bu.id,e.description,e.timeOfCreate,et.startTime,et.endTime,bu.name from events as e
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

            $keys = array_keys($dates);
            foreach($dates as $key=>&$date)
            {
                $stmt->bindParam(':userId',$userId);
                $stmt->bindParam(':roomId',$boardRoom);
                $stmt->bindParam(':desc',$description);
                $stmt->bindParam(':date',$date);
                $stmt->bindParam(':timeOfCreate',$timeOfCreate);
                $stmt->bindParam(':recursive', $recursive);

                if($stmt->execute())
                {
                    if($key == $keys[0])
                    {
                        $recursive = $this->dbConnect->lastInsertId();
//                        var_dump($recursiveId);
                    }
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

    public function checkEventDateTimeInterval($dates,$timeStart,$timeEnd)
    {
        if($this->dbConnect !== 'connect error')
        {
//				 SELECT e.date
//                 from events as e
//                 INNER JOIN eventsTime as et on et.event_id = e.id
//                 WHERE e.date = :date AND ((:timeStart =et.endTime OR :timeStart BETWEEN et.startTime AND et.endTime)
//                 OR (:timeEnd  BETWEEN et.startTime AND et.endTime))
//                 SELECT e.date,et.startTime,et.endTime
//                 from events as e
//                 INNER JOIN eventsTime as et on et.event_id = e.id
//                 WHERE e.date = :date
//                 SELECT e.date
//                 from events as e
//                 INNER JOIN eventsTime as et on et.event_id = e.id
//                 WHERE e.date = :date AND ((et.startTime <= :timeStart AND et.endTime <= :timeStart)
//                 OR (et.startTime >= :timeEnd AND et.endTime >= :timeEnd))
            $stmt =$this->dbConnect->prepare('
                                  SELECT e.date
                 from events as e
                 INNER JOIN eventsTime as et on et.event_id = e.id
                 WHERE e.date = :date AND ((et.startTime <= :timeStart AND et.endTime > :timeStart)
                 OR (et.startTime >= :timeEnd AND et.endTime < :timeEnd))
                    OR (et.startTime >= :timeStart AND et.endTime <= :timeEnd)
                 ');
            foreach($dates as &$date)
            {
//                var_dump($date);
                $stmt->bindParam(':date',$date);
                $stmt->bindParam(':timeStart',$timeStart);
                $stmt->bindParam(':timeEnd',$timeEnd);
                $stmt->execute();
//                var_dump($stmt->fetch(PDO::FETCH_ASSOC));
//                $assocRow = $stmt->fetch(PDO::FETCH_ASSOC);
//                                    if(!empty($assocRow))
//                    {
////                        var_dump($assocRow);
//                        $result[]=$assocRow;
//                    }
                while($assocRow = $stmt->fetch(PDO::FETCH_ASSOC))
                {
//                    var_dump($assocRow);
                    if(!empty($assocRow))
                    {
//                        var_dump($assocRow);
                        $result[]=$assocRow['date'];
                    }
                }
            }
        }else
        {
            $result = false;
        }

        return $result;
    }

    public function deleteEvent($date,$eventId)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('
                DELETE FROM events
                WHERE date >= :date AND id = :eventId 
                ');
                $stmt->bindParam(':date',$date);
                $stmt->bindParam(':eventId',$eventId);

                $result = $stmt->execute();

        } else 
        {
            $result = false;
        }
        return $result;
    }

    public function checkEventDateTimeForUpdate($dates,$timeStart,$timeEnd,$eventId)
    {
//                  SELECT e.date
//                 from events as e
//                 INNER JOIN eventsTime as et on et.event_id = e.id
//                 WHERE e.date = :date AND et.event_id <> :eventId AND ((et.startTime <= :timeStart AND et.endTime > :timeStart)
//                 OR (et.startTime >= :timeEnd AND et.endTime < :timeEnd))
//                    OR (et.startTime >= :timeStart AND et.endTime <= :timeEnd)
//                 AND e.id <> :eventId AND ((et.startTime <= :timeStart AND et.endTime > :timeStart)
//                 OR (:timeEnd >= et.startTime AND :timeEnd < et.endTime ))
//                    OR (:timeStart >= et.startTime   AND  :timeEnd <= et.endTime)
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('
                                  SELECT e.date
                 from events as e
                 INNER JOIN eventsTime as et on et.event_id = e.id
                 WHERE e.date = :date AND ((et.startTime <= :timeStart AND et.endTime > :timeStart AND et.event_id <> :eventId)
                 OR (et.startTime >= :timeEnd AND et.endTime < :timeEnd AND et.event_id <> :eventId)
                    OR (et.startTime >= :timeStart AND et.endTime <= :timeEnd AND et.event_id <> :eventId))
                 ');

            foreach($dates as &$date)
            {
               // var_dump($date);
                //var_dump($eventId);
                $stmt->bindParam(':date',$date);
                $stmt->bindParam(':timeStart',$timeStart);
                $stmt->bindParam(':timeEnd',$timeEnd);
                $stmt->bindParam(':eventId',$eventId);
                $stmt->execute();
                while($assocRow = $stmt->fetch(PDO::FETCH_ASSOC))
                {
//                    var_dump($assocRow);
                    if(!empty($assocRow))
                    {
                        $result[]=$assocRow['date'];
                    }
                }
            }
        }else
        {
            $result = false;
        }

        return $result;
    }

    public function recurrenceDeleteEvent($date,$recursiveId)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('
                DELETE FROM events
                WHERE date >= :date AND recursive = :recursiveId
                ');
                $stmt->bindParam(':date',$date);
                $stmt->bindParam(':recursiveId',$recursiveId);

                $result = $stmt->execute();

        } else 
        {
            $result = false;
        }
        return $result;
    }

    public function updateEvent($userId,$desc,$startTime,$endTime,$eventId,$date)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('
                UPDATE events as e, eventsTime as et SET
                e.user_id = :userId,
                e.description = :desc,
                e.recursive = :eventId,
                et.startTime = :startTime,
                et.endTime = :endTime
                WHERE et.event_id=e.id AND e.id= :eventId AND e.date = :date
                ');
            $stmt->bindParam(':userId',$userId);
            $stmt->bindParam(':desc',$desc);
            $stmt->bindParam(':startTime',$startTime);
            $stmt->bindParam(':endTime',$endTime);
            $stmt->bindParam(':eventId',$eventId);
            $stmt->bindParam(':date',$date);

            $result = $stmt->execute();

        } else
        {
            $result = false;
        }
        return $result;
    }

    public function checkRecurrence($date,$eventId)
    {
        //var_dump($date,$eventId);
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('
                SELECT recursive FROM events
                WHERE date = :date AND id = :eventId
                ');
                $stmt->bindParam(':date',$date);
                $stmt->bindParam(':eventId',$eventId);

                $stmt->execute();
                //var_dump($stmt->fetchAll(PDO::FETCH_ASSOC));
                while($assocRow = $stmt->fetch(PDO::FETCH_ASSOC))
                {
//                    var_dump($assocRow);
                    if(!empty($assocRow))
                    {
//                        var_dump($assocRow);
                        $result=$assocRow['recursive'];
                        //var_dump($assocRow);
                    }
                }

        } else 
        {
            $result = false;
        }
        return $result;
    }

//    private function generateEventUpdateSql($params)
//    {
//        $arrLength = count($params);
//        $i = 1;
//        $sql = 'UPDATE events as e, eventsTime as et SET ';
//
//        foreach($params as $key=>$val)
//        {
//            if($val !='')
//            {
//                if ($arrLength != $i)
//                {
//                    $sql .= $key . '=:' . $key . ',';
//                } else
//                {
//                    $sql .= $key . '=:' . $key ;
//                }
//            }
//            $i++;
//        }
//        $sql .= ' WHERE id=:id';
//        return $sql;
//    }
}


//$c = new EventSql();
//$c->getEventsByMonth('2017-10-01','2017-10-31');
