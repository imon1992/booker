<?php

class EventSql
{
    private $dbConnect;

    public function __construct()
    {
        $this->dbConnect = DbConnection::getInstance();
    }

    /**
     * @param integer $boardRoomId event room
     * @param integer $userId user id
     * @param integer $eventId event id about need information
     * @param string $startTime time when event start
     * @return boolean Return false on error or failure.
     * @return array Return array with information about event
     */
    public function getEventInfo($boardRoomId, $userId, $eventId, $startTime)
    {
        if ($this->dbConnect !== 'connect error')
        {
            $stmt = $this->dbConnect->prepare('
                 SELECT bu.id,e.description,e.timeOfCreate,et.startTime,et.endTime,
                 bu.name,bu.isActive,bu.name
                 from events as e
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
    /**
     * @param string $firstDate start date
     * @param integer $lastDate end date
     * @param integer $id event room id
     * @return boolean Return false on error or failure.
     * @return array Return array with information about events by period
     */
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

    /**
     * @param integer $userId user id
     * @param integer $boardRoom event room id
     * @param integer $description event description
     * @param integer $dates event dates for add
     * @param integer $timeOfCreate time of create event
     * @param integer $recursive recursive event or not
     * @return boolean Return false on error or failure.
     * @return array Return array with added ids
     */
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

    /**
     * @param array $ids events isf for add time
     * @param string $startTime time when event start
     * @param string $endTime time when event end
     * @return boolean Return true is update is successful, false on error or failure.
     * add time for event
     */
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

    /**
     * @param array $dates date(s) for check
     * @param string $timeStart time when event start
     * @param string $timeEnd time when event end
     * @param integer $roomId event room id
     * @return boolean Return false on error or failure.
     * @return array Return array of busy dates
     * Check if the time is busy on this date for post
     */
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
                 OR (et.startTime >= :timeStart AND et.endTime <= :timeEnd AND boardroom_id=:roomId)
                 OR (et.startTime < :timeEnd AND et.endTime > :timeEnd AND boardroom_id=:roomId))
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

    /**
     * @param array $date date for delete
     * @param integer $eventId event id for delete
     * @return boolean Return true is update is successful, false on error or failure.
     * delete one event
     */
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

    /**
     * @param array $dates dates for check
     * @param string $timeStart time when event start
     * @param string $timeEnd time when event end
     * @param integer $eventId event id
     * @param integer $roomId room id
     * @return boolean Return false on error or failure.
     * @return array Return array of busy dates
     * Check if the time is busy on this date for no recursive update
     */
    public function checkEventTime($dates, $timeStart, $timeEnd, $eventId,$roomId)
    {
        if ($this->dbConnect !== 'connect error')
        {
            $stmt = $this->dbConnect->prepare('
                 SELECT e.date
                 from events as e
                 INNER JOIN eventsTime as et on et.event_id = e.id
                 WHERE e.date = :date AND e.boardroom_id = :roomId
                 AND ((et.startTime <= :timeStart AND et.endTime > :timeStart AND et.event_id <> :eventId)
                 OR (et.startTime >= :timeEnd AND et.endTime < :timeEnd AND et.event_id <> :eventId)
                 OR (et.startTime >= :timeStart AND et.endTime < :timeEnd AND et.event_id <> :eventId)
                 OR (et.startTime < :timeEnd AND et.endTime > :timeEnd AND et.event_id <> :eventId))
                 ');

            foreach ($dates as &$date)
            {
                $stmt->bindParam(':date', $date);
                $stmt->bindParam(':timeStart', $timeStart);
                $stmt->bindParam(':timeEnd', $timeEnd);
                $stmt->bindParam(':eventId', $eventId);
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

    /**
     * @param array $datesId  array or dates and ids for check
     * @param string $timeStart time when event start
     * @param string $timeEnd time when event end
     * @param integer $roomId room id
     * @return boolean Return false on error or failure.
     * @return array Return array of busy dates
     * Check if the time is busy on this date for recursive update
     */
    public function checkRecurrenceEventTime($datesId, $timeStart, $timeEnd,$roomId)
    {
        if ($this->dbConnect !== 'connect error')
        {
            $stmt = $this->dbConnect->prepare('
            SELECT e.date,et.event_id
                 from events as e
                 INNER JOIN eventsTime as et on et.event_id = e.id
                 WHERE e.date = :date AND e.boardroom_id = :roomId
                 AND ((et.startTime <= :timeStart AND et.endTime > :timeStart AND et.event_id <> :eventId)
                 OR (et.startTime >= :timeEnd AND et.endTime < :timeEnd AND et.event_id <> :eventId)
                 OR (et.startTime >= :timeStart AND et.endTime < :timeEnd AND et.event_id <> :eventId)
                 OR (et.startTime < :timeEnd AND et.endTime > :timeEnd AND et.event_id <> :eventId))
                 ');
            foreach ($datesId as &$dateId)
            {
                $stmt->bindParam(':date', $dateId['date']);
                $stmt->bindParam(':timeStart', $timeStart);
                $stmt->bindParam(':timeEnd', $timeEnd);
                $stmt->bindParam(':eventId', $dateId['id']);
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

    /**
     * @param integer $eventId event id/recursive id
     * @return boolean Return false on error or failure.
     * @return array Return array date for update
     */
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

    /**
     * @param integer $eventId recursive id
     * @param integer $date event date
     * @return boolean Return false on error or failure.
     * @return array Return array dates for update
     */
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

    /**
     * @param integer $eventId event id
     * @return boolean Return false on error or failure.
     * @return integer Return recursive id
     */
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

    /**
     * @param integer $eventId event id
     * @return boolean Return false on error or failure.
     * @return integer Return which user owns the event
     */
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

    /**
     * @param integer $recursiveId recursive id
     * @param string $date the date after which to delete
     * @return boolean Return true is delete is successful, false on error or failure.
     */
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

    /**
     * @param integer $userId id whose user event to delete
     * @param string $date the date after which to delete
     * @return boolean Return true is delete is successful, false on error or failure.
     */
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

    /**
     * @param integer $userId id whose user event to delete
     * @param string $date the date for update
     * @param string $desc new description
     * @param string $startTime new start time
     * @param string $endTime new end time
     * @param string $eventId
     * @param string $recursiveId
     * @return boolean Return true is update is successful, false on error or failure.
     */
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

    /**
     * @param integer $userId
     * @param string $dates dates for update
     * @param string $desc new event desc
     * @param string $startTime new event start time
     * @param string $endTime new event end time
     * @return boolean Return true is update is successful, false on error or failure.
     */
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

    /**
     * @param integer $userId
     * @param string $dates dates for update
     * @param string $desc new event desc
     * @param string $startTime new event start time
     * @param string $endTime new event end time
     * @param integer $recursiveId new recursive id
     * @return boolean Return true is update is successful, false on error or failure.
     */
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

    /**
     * @param string $date dates for update
     * @param integer $eventId
     * @return boolean Return false on error or failure.
     * @return integer Return recursive id of event
     */
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
