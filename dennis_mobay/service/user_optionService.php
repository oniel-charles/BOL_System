<?php
switch ($request[2]) {
  case 'select':         
    $sql="SELECT u.id,g.title as menu,m.title FROM ((user_option as u left join menu_item as m on u.menu_item_id=m.id) left join (SELECT id,title FROM `menu_item` where level=1) as g on m.menu_group_id=g.id) WHERE u.user_id=".preg_replace('/[^a-z0-9_]+/i','',$request[3])." order by g.title,m.title ";
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
