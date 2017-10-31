<?php

class OrderSql
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

   public function addOrder($paymentId,$statusId,$dateCreate,$totalPrice,$userDisc,$userId)
   {
       if($this->dbConnect !== 'connect error')
       {
           $stmt =$this->dbConnect->prepare('
                INSERT INTO userOrder(payment_id,status_id,createDate,totalPrice,userDiscount,user_id)
                VALUES(:payment,:status,:createDate,:totalPrice,:userDiscount,:userId)
                ');

               $stmt->bindParam(':payment',$paymentId);
               $stmt->bindParam(':status',$statusId);
               $stmt->bindParam(':createDate',$dateCreate);
               $stmt->bindParam(':totalPrice',$totalPrice);
               $stmt->bindParam(':userDiscount',$userDisc);
               $stmt->bindParam(':userId',$userId);
               $stmt->execute();
               $result = $this->dbConnect->lastInsertId();
       }else
       {
           $result = 'error';
       }

       return $result;
   }

    public function addOrderPart($userId,$params,$orderId)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('
                INSERT INTO orderPart(book_id,user_id,count,bookPrice,bookDiscount,order_id)
                VALUES(:bookId,:userId,:count,:bookPrice,:bookDiscount,:orderId)
                ');
            foreach($params as &$orderParam)
            {
                $stmt->bindParam(':bookId',$orderParam['id']);
                $stmt->bindParam(':userId',$userId);
                $stmt->bindParam(':count',$orderParam['count']);
                $stmt->bindParam(':bookPrice',$orderParam['price']);
                $stmt->bindParam(':bookDiscount',$orderParam['bookDiscount']);
                $stmt->bindParam(':orderId',$orderId);
                $result = $stmt->execute();
            }
        }else
        {
            $result = 'error';
        }

        return $result;
    }

    public function getOrdersInfoForUser($userId)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('
                SELECT uo.id,uo.createDate,uo.totalPrice,s.name,s.id as statusId
                FROM userOrder as uo, statusOrder as s
                WHERE uo.user_id = :userId and s.id = uo.status_id');
            $stmt->bindParam(':userId',$userId);
            $stmt->execute();

            while($assocRow = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $result[] = $assocRow;
            }
        }else
        {
            $result = 'error';
        }

        return $result;
    }

    public function getAdditionalOrdersInfoForUser($status,$orderId)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('
            SELECT op.count,op.bookPrice,op.bookDiscount,b.name
            FROM orderPart as op
            INNER JOIN book as b on b.id = op.book_id
            INNER JOIN userOrder as uo on uo.id = op.order_id
            WHERE uo.status_id = :createDate AND op.order_id = :orderId
            ');
            $stmt->bindParam(':createDate',$status);
            $stmt->bindParam(':orderId',$orderId);
            $stmt->execute();

            while($assocRow = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $result[] = $assocRow;
            }
        }else
        {
            $result = 'error';
        }

        return $result;
    }

    public function getAllOrders()
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('
                SELECT uo.id,uo.createDate,uo.totalPrice,s.name, s.id as statusId
                FROM userOrder as uo, statusOrder as s
                WHERE s.id = uo.status_id
                ');
            $stmt->execute();

            while($assocRow = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $result[] = $assocRow;
            }
        }else
        {
            $result = 'error';
        }

        return $result;
    }

    public function updateOrderStatus($orderId,$statusId)
    {
        if($this->dbConnect !== 'connect error')
        {
            $stmt =$this->dbConnect->prepare('UPDATE userOrder
                                            SET status_id = :statusId
                                            WHERE id = :id');
            $stmt->bindParam(':statusId',$statusId);
            $stmt->bindParam(':id',$orderId);
            $result = $stmt->execute();
        }else
        {
            $result = 'error';
        }

        return $result;
    }
}
