
<?php
switch ($this->requestUrl[1]) {
    case 'select':    
    //$tablesjson->{"address"}->{"pkey"}
    $sql="SELECT * FROM `product` order by description ";
    break;
    case 'price':    
      date_default_timezone_set('America/Jamaica');
      $cur_date= date('Ymd') ;
      $sql="select * from product as p left join (SELECT r.product_id,r.selling_price, max(r.effective_date) as effective_date FROM product_rate as r where  r.effective_date<=$cur_date group by r.product_id) as r on p.id=r.product_id  where p.id =".$this->requestUrl[2];
      break;
    case 'list':    
      date_default_timezone_set('America/Jamaica');
      $cur_date= date('Ymd') ;
      $sql="select * from product as p left join (SELECT r.product_id,r.selling_price, max(r.effective_date) as effective_date FROM product_rate as r where r.effective_date<=$cur_date group by r.product_id) as r on p.id=r.product_id  ";
      break;
    case 'DELETE':
      $sql = "delete from ddd`$table` where $where"; 
      break;
  }

?>
