<?php

class EventSql
{
    private $dbConnect;

    public function __construct()
    {
        $this->dbConnect = DbConnection::getInstance();
    }

    public function getEventInfo($boardRoomId, $userId, $eventId, $startTime)
    {
        if ($this->dbConnect !== 'connect error')
        {
            $stmt = $this->dbConnect->prepare('
                 SELECT bu.id,e.description,e.timeOfCreate,et.startTime,et.endTime,bu.name from events as e
                 INNER JOIN eventsTime as et on et.event_id = e.id
                 INNER JOIN bookerUsers as bu on bu.id = user_id
                 WHERE bu.id = :userId and et.event_id = :eventId and e.boardroom_id = :boardroomId and et.startTime = :startTime
                 ');
            $stmt->bindParam(':userId', $userId);
            $stmt->bindParam(':eventId', $eventId);
            $stmt->bindParam(':boardroomId', $boardRoomId);
            $stmt->bindParam(':startTime', $startTime);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else
        {
            $result = false;
        }

        return $result;
    }

    public function getEventsByMonth($firstDate, $lastDate, $id)
    {
        if ($this->dbConnect !== 'connect error')
        {
            $stmt = $this->dbConnect->prepare('
            SELECT e.id,e.user_id,e.boardroom_id,e.date,et.startTime,et.endTime FROM events as e
            INNER JOIN eventsTime as et on et.event_id = e.id
            where e.date BETWEEN :firstDate AND :lastDate AND boardroom_id = :id
            ');
            $stmt->bindParam(':firstDate', $firstDate);
            $stmt->bindParam(':lastDate', $lastDate);
            $stmt->bindParam(':id', $id);

            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $result = [];
            foreach ($data as $val)
            {
                $events = [];
                $a = [];
                $d = [];

                if (!isset($result[$val['id']]))
                {
                    $a['startTime'] = substr($val['startTime'], 0, 5);
                    $a['endTime'] = substr($val['endTime'], 0, 5);
                    array_unshift($d, $a);
                    $events['id'] = $val['id'];
                    $events['userId'] = $val['user_id'];
                    $events['date'] = $val['date'];
                    $events['events'] = $a;
                    $result[$val['id']] = $events;
                }
                if ($result[$val['id']]['id'] == $val['id'])
                {
                    $a['startTime'] = substr($val['startTime'], 0, 5);
                    $a['endTime'] = substr($val['endTime'], 0, 5);
                    array_unshift($result[$val['id']]['events'], $a);
                    unset($result[$val['id']]['events']['startTime']);
                    unset($result[$val['id']]['events']['endTime']);
                }
            }
        } else
        {
            $result = false;
        }

        return $result;
    }

    public function addNewEvent($userId, $boardRoom, $description, $dates, $timeOfCreate, $recursive=0)
    {
        if ($this->dbConnect !== 'connect error')
        {
            $stmt = $this->dbConnect->prepare('
                 INSERT INTO events(user_id,boardroom_id,description,date,timeOfCreate,recursive)
                 VALUES(:userId,:roomId,:desc,:date,:timeOfCreate,:recursive)
                 ');

            $keys = array_keys($dates);
            foreach ($dates as $key => &$date)
            {
                $stmt->bindParam(':userId', $userId);
                $stmt->bindParam(':roomId', $boardRoom);
                $stmt->bindParam(':desc', $description);
                $stmt->bindParam(':date', $date);
                $stmt->bindParam(':timeOfCreate', $timeOfCreate);
                $stmt->bindParam(':recursive', $recursive);

                if ($stmt->execute())
                {
                    if ($key == $keys[0])
                    {
                        $recursive = $this->dbConnect->lastInsertId();
                    }
                    $result[] = $this->dbConnect->lastInsertId();
                } else
                {
                    $result = false;
                }
            }
        } else
        {
            $result = false;
        }

        return $result;
    }

    public function addEventTime($ids, $startTime, $endTime)
    {
        if ($this->dbConnect !== 'connect error')
        {
            $stmt = $this->dbConnect->prepare('
                 INSERT INTO eventsTime(event_id,startTime,endTime)
                 VALUES(:eventId,:startTime,:endTime)
                 ');

            foreach ($ids as &$id)
            {
                $stmt->bindParam(':eventId', $id);
                $stmt->bindParam(':startTime', $startTime);
                $stmt->bindParam(':endTime', $endTime);
                $result = $stmt->execute();

            }
        } else
        {
            $result = false;
        }

        return $result;
    }

    public function checkEventDateTimeInterval($dates, $timeStart, $timeEnd, $roomId)
    {
        if ($this->dbConnect !== 'connect error')
        {
            $stmt = $this->dbConnect->prepare('
                 SELECT e.date
                 from events as e
                 INNER JOIN eventsTime as et on et.event_id = e.id
                 WHERE e.date = :date 
                 AND ((et.startTime <= :timeStart AND et.endTime > :timeStart AND boardroom_id=:roomId)
                 OR (et.startTime >= :timeEnd AND et.endTime < :timeEnd AND boardroom_id=:roomId)
                 OR (et.startTime >= :timeStart AND et.endTime <= :timeEnd AND boardroom_id=:roomId))
                 ');
            foreach ($dates as &$date)
            {
                $stmt->bindParam(':date', $date);
                $stmt->bindParam(':timeStart', $timeStart);
                $stmt->bindParam(':timeEnd', $timeEnd);
                $stmt->bindParam(':roomId', $roomId);
                $stmt->execute();

                while ($assocRow = $stmt->fetch(PDO::FETCH_ASSOC))
                {
                    if (!empty($assocRow))
                    {
                        $result[] = $assocRow['date'];
                    }
                }
            }
        } else
        {
            $result = false;
        }

        return $result;
    }

    public function deleteEvent($date, $eventId)
    {
        if ($this->dbConnect !== 'connect error')
        {
            $stmt = $this->dbConnect->prepare('
                DELETE FROM events
                WHERE date >= :date AND id = :eventId 
                ');
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':eventId', $eventId);

            $result = $stmt->execute();

        } else
        {
            $result = false;
        }

        return $result;
    }

    public function checkEventTime($dates, $timeStart, $timeEnd, $eventId)
    {
        if ($this->dbConnect !== 'connect error')
        {
            $stmt = $this->dbConnect->prepare('
                 SELECT e.date
                 from events as e
                 INNER JOIN eventsTime as et on et.event_id = e.id
                 WHERE e.date = :date AND ((et.startTime <= :timeStart AND et.endTime > :timeStart AND et.event_id <> :eventId)
                 OR (et.startTime >= :timeEnd AND et.endTime < :timeEnd AND et.event_id <> :eventId)
                 OR (et.startTime >= :timeStart AND et.endTime <= :timeEnd AND et.event_id <> :eventId))
                 ');

            foreach ($dates as &$date)
            {
                $stmt->bindParam(':date', $date);
                $stmt->bindParam(':timeStart', $timeStart);
                $stmt->bindParam(':timeEnd', $timeEnd);
                $stmt->bindParam(':eventId', $eventId);
                $stmt->execute();
                while ($assocRow = $stmt->fetch(PDO::FETCH_ASSOC))
                {
                    if (!empty($assocRow))
                    {
                        $result[] = $assocRow['date'];
                    }
                }
            }
        } else
        {
            $result = false;
        }

        return $result;
    }

    public function checkRecurrenceEventTime($datesId, $timeStart, $timeEnd)
    {
        if ($this->dbConnect !== 'connect error')
        {
//            var_dump($datesId);
//                 SELECT e.date
//                 from events as e
//                 INNER JOIN eventsTime as et on et.event_id = e.id
//                 WHERE e.date = :date AND ((et.startTime <= :timeStart AND et.endTime >= :timeStart AND et.event_id <> :eventId)
//                 OR (et.startTime >= :timeEnd AND et.endTime < :timeEnd AND et.event_id <> :eventId)
//                 OR (et.startTime >= :timeStart AND et.endTime <= :timeEnd AND et.event_id <> :eventId))

//            SELECT e.date
//                 from events as e
//                 INNER JOIN eventsTime as et on et.event_id = e.id
//                 WHERE e.date = :date AND ((et.startTime <= :timeStart AND et.endTime > :timeStart)
//                 OR (et.startTime >= :timeEnd AND et.endTime < :timeEnd)
//                 OR (et.startTime >= :timeStart AND et.endTime <= :timeEnd))
            $stmt = $this->dbConnect->prepare('
            SELECT e.date
                 from events as e
                 INNER JOIN eventsTime as et on et.event_id = e.id
                 WHERE e.date = :date AND ((et.startTime <= :timeStart AND et.endTime > :timeStart AND et.event_id <> :eventId)
                 OR (et.startTime >= :timeEnd AND et.endTime < :timeEnd AND et.event_id <> :eventId)
                 OR (et.startTime >= :timeStart AND et.endTime < :timeEnd AND et.event_id <> :eventId))
                 ');

            foreach ($datesId as &$dateId)
            {
                $stmt->bindParam(':date', $dateId['date']);
                $stmt->bindParam(':timeStart', $timeStart);
                $stmt->bindParam(':timeEnd', $timeEnd);
                $stmt->bindParam(':eventId', $dateId['id']);
                $stmt->execute();
                while ($assocRow = $stmt->fetch(PDO::FETCH_ASSOC))
                {
                    if (!empty($assocRow))
                    {
                        $result[] = $assocRow['date'];
                    }
                }
            }
        } else
        {
            $result = false;
        }

        return $result;
    }

    public function selectEventDates($eventId)
    {
        if ($this->dbConnect !== 'connect error')
        {
            $stmt = $this->dbConnect->prepare('
                 SELECT date,id
                 FROM events
                 WHERE id = :eventId OR recursive = :eventId
                 ');

            $stmt->bindParam(':eventId', $eventId);
            $stmt->execute();
            $result = [];
            while ($assocRow = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $result[] = $assocRow;
            }
        } else
        {
            $result = false;
        }

        return $result;
    }


    public function selectEventDatesRecurrence($eventId,$date)
    {
        if ($this->dbConnect !== 'connect error')
        {
            $stmt = $this->dbConnect->prepare('
                 SELECT date,id
                 FROM events
                 WHERE recursive = :eventId AND date >= :date
                 ');

            $stmt->bindParam(':eventId', $eventId);
            $stmt->bindParam(':date', $date);
            $stmt->execute();
            $result = [];
            while ($assocRow = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $result[] = $assocRow;
            }
        } else
        {
            $result = false;
        }

        return $result;
    }

    public function selectRecurrenceId($eventId)
    {
        if ($this->dbConnect !== 'connect error')
        {
            $stmt = $this->dbConnect->prepare('
                 SELECT recursive
                 FROM events
                 WHERE id = :eventId
                 ');
            $stmt->bindParam(':eventId', $eventId);
            $stmt->execute();
            while ($assocRow = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $result = $assocRow['recursive'];
            }
        } else
        {
            $result = false;
        }

        return $result;
    }

    public function selectEventUser($eventId)
    {
        if ($this->dbConnect !== 'connect error')
        {
            $stmt = $this->dbConnect->prepare('
                 SELECT user_id
                 FROM events
                 WHERE id = :eventId
                 ');

            $stmt->bindParam(':eventId', $eventId);
            $stmt->execute();
            $result = [];
            while ($assocRow = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $result = $assocRow['user_id'];
            }
        } else
        {
            $result = false;
        }

        return $result;
    }

    public function recurrenceDeleteEvent($date, $recursiveId)
    {
        if ($this->dbConnect !== 'connect error')
        {
            $stmt = $this->dbConnect->prepare('
                DELETE FROM events
                WHERE date >= :date AND recursive = :recursiveId
                ');
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':recursiveId', $recursiveId);

            $result = $stmt->execute();

        } else
        {
            $result = false;
        }

        return $result;
    }

    public function deleteUserEvents($date,$userId)
    {
        if ($this->dbConnect !== 'connect error')
        {
            $stmt = $this->dbConnect->prepare('
                DELETE FROM events
                WHERE date > :date AND user_id = :userId
                ');
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':userId', $userId);

            $result = $stmt->execute();

        } else
        {
            $result = false;
        }

        return $result;
    }

    public function updateEvent($userId, $desc, $startTime, $endTime, $eventId, $date, $recursiveId)
    {
        if ($this->dbConnect !== 'connect error')
        {
            $stmt = $this->dbConnect->prepare('
                UPDATE events as e, eventsTime as et SET
                e.user_id = :userId,
                e.description = :desc,
                e.recursive = :recursiveId,
                et.startTime = :startTime,
                et.endTime = :endTime
                WHERE et.event_id=e.id AND e.id= :eventId AND e.date = :date
                ');
            $stmt->bindParam(':userId', $userId);
            $stmt->bindParam(':desc', $desc);
            $stmt->bindParam(':startTime', $startTime);
            $stmt->bindParam(':endTime', $endTime);
            $stmt->bindParam(':eventId', $eventId);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':recursiveId', $recursiveId);

            $result = $stmt->execute();

        } else
        {
            $result = false;
        }

        return $result;
    }


    public function recurrenceUpdateEventNoChangeRecurrence($userId, $desc, $startTime, $endTime, $dates)
    {
        if ($this->dbConnect !== 'connect error')
        {
            $stmt = $this->dbConnect->prepare('
                UPDATE events as e, eventsTime as et SET
                e.user_id = :userId,
                e.description = :desc,
                et.startTime = :startTime,
                et.endTime = :endTime
                WHERE et.event_id=e.id AND e.id= :eventId AND e.date = :date
                ');

            $stmt->bindParam(':userId', $userId);
            $stmt->bindParam(':desc', $desc);
            $stmt->bindParam(':startTime', $startTime);
            $stmt->bindParam(':endTime', $endTime);

            foreach ($dates as &$val)
            {
                $stmt->bindParam(':eventId', $val['id']);
                $stmt->bindParam(':date', $val['date']);
                $result = $stmt->execute();
            }


        } else
        {
            $result = false;
        }

        return $result;
    }

    public function recurrenceUpdateEventChangeRecurrence($userId, $desc, $startTime, $endTime, $dates, $recursiveId)
    {
        if ($this->dbConnect !== 'connect error')
        {
            $stmt = $this->dbConnect->prepare('
                UPDATE events as e, eventsTime as et SET
                e.user_id = :userId,
                e.description = :desc,
                e.recursive = :recursiveId,
                et.startTime = :startTime,
                et.endTime = :endTime
                WHERE et.event_id=e.id AND e.id= :eventId AND e.date = :date
                ');

            $stmt->bindParam(':userId', $userId);
            $stmt->bindParam(':desc', $desc);
            $stmt->bindParam(':startTime', $startTime);
            $stmt->bindParam(':endTime', $endTime);
            $stmt->bindParam(':recursiveId', $recursiveId);

            foreach ($dates as &$val)
            {
                $stmt->bindParam(':eventId', $val['id']);
                $stmt->bindParam(':date', $val['date']);
                $result = $stmt->execute();
            }


        } else
        {
            $result = false;
        }

        return $result;
    }

    public function checkRecurrence($date, $eventId)
    {
        if ($this->dbConnect !== 'connect error')
        {
            $stmt = $this->dbConnect->prepare('
                SELECT recursive FROM events
                WHERE date = :date AND id = :eventId
                ');
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':eventId', $eventId);

            $stmt->execute();
            while ($assocRow = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                if (!empty($assocRow))
                {
                    $result = $assocRow['recursive'];
                }
            }

        } else
        {
            $result = false;
        }

        return $result;
    }
}
