
<?php
$mysqli=$this->db->conn;
switch ($this->requestUrl[1]) {
    case 'select':    
    //$tablesjson->{"address"}->{"pkey"}
    $sql="SELECT * FROM  preclearance ";
    break;
    case 'check':    
      date_default_timezone_set('America/Jamaica');
      $cur_date= date('Ymd') ;    
        // EXchange Rate
      $sql="SELECT * from currency_rate where currency_id=2 and effective_date <=".$cur_date." order by effective_date desc limit 1 " ;
      $result = mysqli_query($mysqli,$sql);
      $us_rate= mysqli_fetch_object($result);	
      $sql="select ".$us_rate->exchange_rate." as exchange_rate, cur.currency_code,b.id,b.charge_item_id,b.amount,d.amount as preclearance_amount,b.currency_id,c.currency_id as item_currency,c.description from (((bill_of_lading_other_charge as b left join charge_item as c on b.charge_item_id=c.id) left join (select d.* from preclearance as p left join preclearance_detail as d on p.id=d.preclearance_id  where billoflading_id=".$this->requestUrl[2]." and  (cancelled is null or cancelled=0)) as d on b.charge_item_id=d.charge_item_id) left join currency as cur on c.currency_id=cur.id)  where  b.billoflading_id=".$this->requestUrl[2]."  and b.prepaid_flag='c'";
      break;      
    case 'date_range':    
        //$tablesjson->{"address"}->{"pkey"}
        $sql="select * from preclearance where preclearance_date between ".$this->requestUrl[2].' and '.$this->requestUrl[3];        
        break;
    case 'outstanding':    
          $range='';
          if(isset($this->requestUrl[2])){
            $range=' and p.preclearance_date between '.$this->requestUrl[2].' and '.$this->requestUrl[3];        
          }
          $sql="select r.amount as receipt_amount, b.bill_of_lading_number,p.preclearance_date,p.payee, d.*,r.currency_amount,c.description from ((((preclearance as p left join preclearance_detail as d on p.id=d.preclearance_id) left join (select d.* from receipt_detail as d left join receipt as r on d.receipt_id=r.id where r.cancelled is null or r.cancelled=0) as r on d.bol_id=r.bol_id and d.charge_item_id=r.charge_item_id) left join charge_item as c on d.charge_item_id=c.id) left join bill_of_lading as b on p.billoflading_id=b.id) where (p.cancelled is null or p.cancelled=0)".$range;
          break;         
    case 'recovered':    
            $range='';
            if(isset($this->requestUrl[2])){
              $range=' and r.receipt_date between '.$this->requestUrl[2].' and '.$this->requestUrl[3];        
            }
            $sql="select r.amount as receipt_amount,r.receipt_date, b.bill_of_lading_number,p.preclearance_date,p.payee, d.*,r.currency_amount,c.description from ((((preclearance as p left join preclearance_detail as d on p.id=d.preclearance_id) left join (select d.*,r.receipt_date from receipt_detail as d left join receipt as r on d.receipt_id=r.id where r.cancelled is null or r.cancelled=0) as r on d.bol_id=r.bol_id and d.charge_item_id=r.charge_item_id) left join charge_item as c on d.charge_item_id=c.id) left join bill_of_lading as b on p.billoflading_id=b.id) where r.receipt_date is not null and (p.cancelled is null or p.cancelled=0)".$range;
            break;      
    case 'DELETE':
      $sql = "delete from ddd`$table` where $where"; 
      break;
  }

?>
