DELETE FROM `bill_of_lading` WHERE voyage_id not in (select id from voyage);
DELETE FROM voyage WHERE vessel_id not in (select id from vessel);
DELETE FROM `receipt_detail` WHERE receipt_id not in (select id from receipt);
DELETE FROM `receipt` WHERE billoflading_id not in (select id from bill_of_lading);
DELETE FROM `receipt_detail` WHERE receipt_id not in (select id from receipt);
DELETE FROM `bill_of_lading_other_charge` WHERE `billoflading_id` not in (select id from bill_of_lading);
DELETE FROM `bill_of_lading_detail` WHERE `billoflading_id` not in (select id from bill_of_lading);
DELETE FROM `bill_of_lading_container` WHERE  `billoflading_id` not in (select id from bill_of_lading);
