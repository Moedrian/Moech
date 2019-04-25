START TRANSACTION;

CREATE DATABASE IF NOT EXISTS "what" DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE "what";

CREATE TABLE "customers" (
  "cust_id" int NOT NULL AUTO_INCREMENT,
  "cust_name" char(50) NOT NULL,
  "cust_contact" char(50) NOT NULL,
  "cust_tel" char(15) NOT NULL,
  "cust_mail" char(50) NULL,
  PRIMARY KEY("cust_id")
) ENGINE=InnoDB;

CREATE TABLE "devices" (
  "dev_id" char(20) NOT NULL,
  "cust_id" int NOT NULL,
  "cust_name" char(50) NOT NULL,
  "province" char(30) NOT NULL,
  "city" char(30) NOT NULL,
  PRIMARY KEY("dev_id")
) ENGINE=InnoDB;

CREATE TABLE "orders" (
  "order_num" int NOT NULL AUTO_INCREMENT,
  "order_date" date NOT NULL,
  "cust_id" int NOT NULL,
  PRIMARY KEY("order_num")
) ENGINE=InnoDB;

CREATE TABLE "order_items" (
  "seq_id" int NOT NULL,
  "dev_id" char(20) NOT NULL,
  "order_num" int NOT NULL,
  "item" char(30) NOT NULL,
  "param" char(30) NOT NULL,
  "quantity" int NOT NULL,
  "price" float(6,2) NOT NULL,
  PRIMARY KEY("seq_id")
) ENGINE=InnoDB;

CREATE TABLE "products" (
  "item" char(30) NOT NULL,
  "charging_method" char(20) NOT NULL,
  "price" float(6,2) NOT NULL,
  "description" text NOT NULL,
  PRIMARY KEY("item")
) ENGINE=InnoDB;

COMMIT;