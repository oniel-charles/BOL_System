CREATE TABLE Voyage
(
  id integer NOT NULL AUTO_INCREMENT,
  vessel_id integer,
  voyage_number char(20),
  departure_date integer,
  arrival_date integer,
  stripped smallint,
  stripped_date integer,
  CONSTRAINT pk_shipment PRIMARY KEY (id)
);

CREATE TABLE bill_of_lading
(
  id integer NOT NULL AUTO_INCREMENT,
  parent_bol smallint,
  bol_total decimal(10,2),
  bill_of_lading_number char(25),
  port_of_origin integer,
  port_of_loading integer,
  port_of_discharge integer,
  port_of_delivery integer,
  currency_id integer,
  consignee_name char(40),
  consignee_address char(200),
  consignee_id integer,
  shipper_name char(40),
  shipper_address char(200),
  notify_name char(40),
  notify_address char(200),
  notify_date integer,
  master_bol_id integer,
  CONSTRAINT pk_bill_of_lading PRIMARY KEY (id)
);

CREATE TABLE bill_of_lading_container
(
  id integer NOT NULL AUTO_INCREMENT,
  container_number char(15),
  container_size_type_id integer,
  billoflading_id integer,
  CONSTRAINT pk_bill_of_lading_container PRIMARY KEY (id)
);

CREATE TABLE bill_of_lading_detail
(
  id integer NOT NULL AUTO_INCREMENT,
  billoflading_id integer,
  package_type_id integer,
  commodity_id integer,
  Description_of_goods varchar(500),
  number_of_items integer,
  weight decimal(10,2),
  measure decimal(10,2),
  weight_unit char(10),
  measure_unit char(10),
  width decimal(10,2),
  depth decimal(10,2),
  breath decimal(10,2),
  volume decimal(10,2),
  CONSTRAINT pk_bill_of_lading_detail PRIMARY KEY (id)
);

CREATE TABLE bill_of_lading_other_charge
(
  id integer NOT NULL AUTO_INCREMENT,
  charge_item_id integer,
  amount decimal(10,2),
  prepaid_flag char(10),
  attract_gct smallint,
  currency_id integer,
  billoflading_id integer,
  CONSTRAINT pk_bill_of_lading_other_charges PRIMARY KEY (id)
);

CREATE TABLE charge_item
(
  id integer NOT NULL AUTO_INCREMENT,
  item_code char(20),
  description char(50),
  basis char(20),
  currency_id integer,
  item_rate decimal(10,2),
  commodity_id integer,
  package_id integer,
  print_seperate smallint,
  CONSTRAINT pk_charge_item PRIMARY KEY (id)
);

CREATE TABLE client
(
  id integer NOT NULL AUTO_INCREMENT,
  client_code char(10),
  client_name char(40),
  client_address char(200),
  phone_number char(25),
  email_address char(80),
  CONSTRAINT pk_client PRIMARY KEY (id)
);

CREATE TABLE commodity
(
  id integer NOT NULL AUTO_INCREMENT,
  commodity_code char(10),
  description char(50),
  CONSTRAINT pk_commodity PRIMARY KEY (id)
);

CREATE TABLE container_size_type
(
  id integer NOT NULL AUTO_INCREMENT,
  size_type_code char(10),
  description char(50),
  CONSTRAINT pk_container_size_type PRIMARY KEY (id)
);

CREATE TABLE country
(
  id integer NOT NULL AUTO_INCREMENT,
  country_code char(10),
  country_name char(60),
  CONSTRAINT pk_country PRIMARY KEY (id)
);

CREATE TABLE currency
(
  id integer NOT NULL AUTO_INCREMENT,
  currency_code char(10),
  currency_name char(50),
  CONSTRAINT pk_currency PRIMARY KEY (id)
);

CREATE TABLE currency_rate
(
  currency_id integer NOT NULL,
  effective_date integer NOT NULL,
  exchange_rate decimal(7,2),
  CONSTRAINT pk_currency_rate PRIMARY KEY (currency_id, effective_date)
);

CREATE TABLE gct_rate
(
  id integer NOT NULL AUTO_INCREMENT,
  effective_date integer,
  rate decimal(5,2),
  CONSTRAINT pk_gct_rate PRIMARY KEY (id)
);

CREATE TABLE menu_group
(
  id bigint NOT NULL AUTO_INCREMENT,
  title char(100),
  menu_order integer,
  status smallint,
  level smallint,
  icon char(100),
  description char(100),
  CONSTRAINT pk_menu_group PRIMARY KEY (id)
);

CREATE TABLE menu_item
(
  id integer NOT NULL AUTO_INCREMENT,
  menu_group_id bigint,
  title varchar(100),
  url varchar(100),
  menu_order integer,
  status integer,
  level smallint,
  icon char(100),
  description char(150),
  CONSTRAINT pk_menu_item PRIMARY KEY (id)
);

CREATE TABLE package
(
  id integer NOT NULL AUTO_INCREMENT,
  package_code char(10),
  description char(50),
  CONSTRAINT pk_package PRIMARY KEY (id)
);

CREATE TABLE port
(
  id integer NOT NULL AUTO_INCREMENT,
  port_code char(10),
  port_name char(60),
  country_id integer,
  CONSTRAINT pk_port PRIMARY KEY (id)
);

CREATE TABLE receipt
(
  id integer NOT NULL AUTO_INCREMENT,
  receipt_date integer,
  receipt_time smallint,
  client_id integer,
  payee char(40),
  receipt_total decimal(12,2),
  currency_id integer,
  local_total decimal(12,2),
  exchange_rate decimal(8,2),
  printed smallint,
  created_by integer,
  billoflading_id integer,
  deleted_by integer,
  date_deleted integer,
  time_deleted smallint,
  cancelled smallint,
  CONSTRAINT pk_receipt PRIMARY KEY (id)
);

CREATE TABLE receipt_detail
(
  id integer NOT NULL AUTO_INCREMENT,
  receipt_id integer,
  bol_id integer,
  charge_item_id integer,
  amount decimal(10,2),
  discount decimal(10,2),
  comment char(30),
  CONSTRAINT pk_receipt_detail PRIMARY KEY (id)
);

CREATE TABLE shipment_order
(
  id integer NOT NULL AUTO_INCREMENT,
  voyage_id integer,
  billoflading_id integer,
  created_by integer,
  locked integer,
  printed smallint,
  cancelled smallint,
  order_date integer,
  cancelled_by integer,
  cancelled_date integer,
  CONSTRAINT pk_order PRIMARY KEY (id)
);

CREATE TABLE system_values
(
  id integer NOT NULL AUTO_INCREMENT,
  description char(30),
  data_type char(10),
  data_value char(30),
  CONSTRAINT pk_system_values PRIMARY KEY (id)
);

CREATE TABLE user_option
(
  menu_item_id integer NOT NULL,
  user_id integer NOT NULL,
  CONSTRAINT pk_user_option PRIMARY KEY (menu_item_id, user_id)
);

CREATE TABLE user_profile
(
  id integer NOT NULL AUTO_INCREMENT,
  user_name char(60),
  password char(50),
  full_name char(70),
  status char(10),
  CONSTRAINT pk_user_profile PRIMARY KEY (id)
);

CREATE TABLE vessel
(
  id integer NOT NULL AUTO_INCREMENT,
  vessel_name char(50),
  vessel_code char(10),
  lloyd_number char(20),
  country_id integer,
  CONSTRAINT pk_vessel PRIMARY KEY (id)
);

CREATE TABLE voyage_container
(
  id integer NOT NULL AUTO_INCREMENT,
  voyage_id integer,
  container_number char(15),
  port_origin integer,
  seal char(20),
  CONSTRAINT pk_voyage_container PRIMARY KEY (id)
);

ALTER TABLE bill_of_lading ADD CONSTRAINT fk_bol_port_delivery
  FOREIGN KEY (port_of_delivery) REFERENCES port (id);

ALTER TABLE bill_of_lading ADD CONSTRAINT fk_bol_port_discharge
  FOREIGN KEY (port_of_discharge) REFERENCES port (id);

ALTER TABLE bill_of_lading ADD CONSTRAINT fk_bol_port_loading
  FOREIGN KEY (port_of_loading) REFERENCES port (id);

ALTER TABLE bill_of_lading ADD CONSTRAINT fk_bol_port_origin
  FOREIGN KEY (port_of_origin) REFERENCES port (id);

ALTER TABLE bill_of_lading_container ADD CONSTRAINT fk_bill_of_lading_container_
  FOREIGN KEY (billoflading_id) REFERENCES bill_of_lading (id);

ALTER TABLE bill_of_lading_container ADD CONSTRAINT fk_bol_container_size_type
  FOREIGN KEY (container_size_type_id) REFERENCES container_size_type (id);

ALTER TABLE bill_of_lading_detail ADD CONSTRAINT fk_bol_detail_bill_of_lading
  FOREIGN KEY (billoflading_id) REFERENCES bill_of_lading (id);

ALTER TABLE bill_of_lading_other_charge ADD CONSTRAINT fk_bol_other_charge_bol
  FOREIGN KEY (billoflading_id) REFERENCES bill_of_lading (id);

ALTER TABLE bill_of_lading_other_charge ADD CONSTRAINT fk_bol_other_charge_commodity
  FOREIGN KEY (currency_id) REFERENCES commodity (id);

ALTER TABLE bill_of_lading_other_charge ADD CONSTRAINT fk_bol_other_charge_currency
  FOREIGN KEY (currency_id) REFERENCES currency (id);

ALTER TABLE bill_of_lading_other_charge ADD CONSTRAINT fk_bol_other_charge_item
  FOREIGN KEY (charge_item_id) REFERENCES charge_item (id);

ALTER TABLE charge_item ADD CONSTRAINT fk_charge_item_commodity
  FOREIGN KEY (commodity_id) REFERENCES commodity (id);

ALTER TABLE charge_item ADD CONSTRAINT fk_charge_item_package
  FOREIGN KEY (package_id) REFERENCES package (id);

ALTER TABLE currency_rate ADD CONSTRAINT fk_currency_rate_currency
  FOREIGN KEY (currency_id) REFERENCES currency (id);

ALTER TABLE menu_item ADD CONSTRAINT fk_menu_item_
  FOREIGN KEY (menu_group_id) REFERENCES menu_group (id);

ALTER TABLE port ADD CONSTRAINT fk_port_country
  FOREIGN KEY (country_id) REFERENCES country (id);

ALTER TABLE receipt ADD CONSTRAINT fk_receipt_bol
  FOREIGN KEY (billoflading_id) REFERENCES bill_of_lading (id);

ALTER TABLE receipt ADD CONSTRAINT fk_receipt_client
  FOREIGN KEY (client_id) REFERENCES client (id);

ALTER TABLE receipt ADD CONSTRAINT fk_receipt_created_by
  FOREIGN KEY (created_by) REFERENCES user_profile (id);

ALTER TABLE receipt ADD CONSTRAINT fk_receipt_currency
  FOREIGN KEY (currency_id) REFERENCES currency (id);

ALTER TABLE receipt ADD CONSTRAINT fk_receipt_deleted_by
  FOREIGN KEY (deleted_by) REFERENCES user_profile (id);

ALTER TABLE receipt_detail ADD CONSTRAINT fk_receipt_detail_charge_item
  FOREIGN KEY (charge_item_id) REFERENCES charge_item (id);

ALTER TABLE receipt_detail ADD CONSTRAINT fk_receipt_detail_receipt
  FOREIGN KEY (id) REFERENCES receipt (id);

ALTER TABLE shipment_order ADD CONSTRAINT fk_order_bol
  FOREIGN KEY (billoflading_id) REFERENCES bill_of_lading (id);

ALTER TABLE shipment_order ADD CONSTRAINT fk_order_created_by
  FOREIGN KEY (created_by) REFERENCES user_profile (id);

ALTER TABLE shipment_order ADD CONSTRAINT fk_order_voyage
  FOREIGN KEY (voyage_id) REFERENCES Voyage (id);

ALTER TABLE user_option ADD CONSTRAINT fk_user_option_menu_item
  FOREIGN KEY (menu_item_id) REFERENCES menu_item (id);

ALTER TABLE user_option ADD CONSTRAINT fk_user_option_user_profile
  FOREIGN KEY (user_id) REFERENCES user_profile (id);

ALTER TABLE vessel ADD CONSTRAINT fk_vessel_country
  FOREIGN KEY (country_id) REFERENCES country (id);

ALTER TABLE voyage_container ADD CONSTRAINT fk_voyage_container_voyage
  FOREIGN KEY (voyage_id) REFERENCES Voyage (id);

