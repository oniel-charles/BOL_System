<?php
$sql='';
switch ($this->requestUrl[1]) {
  case 'select':         
    //$sql="SELECT g.id as gid,i.id,g.title as g_title,g.icon as g_icon,g.url as g_url,i.title,i.url,i.icon FROM `menu_item` as g left join (SELECT * FROM `menu_item` where level=2) as i  on g.id=i.menu_group_id where g.level=1 order by g.menu_order,i.menu_order ";
    $sql="SELECT g.id as gid,i.id,g.title as g_title,g.icon as g_icon,g.url as g_url,i.title,i.url,i.icon FROM `menu_item` as g left join (SELECT * FROM `menu_item` where level=2) as i on g.id=i.menu_group_id where g.level=1 and (i.id in (select menu_item_id from user_option where user_id=".$this->claims['id']." ) or (i.id is null and g.id in (select menu_item_id from user_option where user_id=".$this->claims['id']." )))order by g.menu_order,i.menu_order ";
    break;
    case 'select_all':         
    $sql="SELECT g.id as gid,i.id,g.title as g_title,g.icon as g_icon,g.url as g_url,i.title,i.url,i.icon FROM `menu_item` as g left join (SELECT * FROM `menu_item` where level=2) as i  on g.id=i.menu_group_id where g.level=1 order by g.menu_order,i.menu_order ";    
    break;
    case 'PUT':
      $sql = "update `$table` set $set where $where"; 
      break;
    case 'POST':
      $sql = "insert into `$table` set $set"; 
      break;
    case 'DELETE':
      $sql = "delete from `$table` where $where"; 
      break;
  }
?>
