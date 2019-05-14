START TRANSACTION;

CREATE DATABASE IF NOT EXISTS vendor DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE vendor;

CREATE TABLE customer_info (
    cust_id int NOT NULL AUTO_INCREMENT,
    cust_name char(50) NOT NULL,
    cust_contact char(50) NOT NULL,
    cust_tel char(15) NOT NULL,
    cust_mail char(50) NULL,
    PRIMARY KEY (cust_id)
) ENGINE = InnoDB;

INSERT INTO customer_info (cust_id, cust_name, cust_contact, cust_tel, cust_mail) VALUES
(10000, 'Nano Inc.', 'Nano', '12345678900', 'nichijou@mail.hakase.com');

CREATE TABLE customer_reg (
    username char(30) NOT NULL,
    user_mail char(50) NOT NULL,
    password varchar(60) NOT NULL,
    cust_name char(50) NOT NULL,
    PRIMARY KEY (username)
) ENGINE = InnoDB;

CREATE TABLE devices (
    dev_id char(20) NOT NULL,
    cust_id int NOT NULL,
    cust_name char(50) NOT NULL,
    province char(30) NOT NULL,
    city char(30) NOT NULL,
    PRIMARY KEY (dev_id)
) ENGINE = InnoDB;

CREATE TABLE params_ref (
    seq_id int NOT NULL AUTO_INCREMENT,
    dev_id char(20) NOT NULL,
    param char(30) NOT NULL,
    freq int NOT NULL,
    min float(6,2),
    max float(6,2),
    abnormal_duration float,
    extra text,
    PRIMARY KEY (seq_id)
) ENGINE = InnoDB;

CREATE TABLE orders (
    order_num int NOT NULL AUTO_INCREMENT,
    order_date date NOT NULL,
    cust_id int NOT NULL,
    PRIMARY KEY(order_num)
) ENGINE=InnoDB;

INSERT INTO orders (order_num, order_date, cust_id) VALUES
(20000, '2019-04-01', 10000);

CREATE TABLE order_items (
    seq_id int NOT NULL,
    dev_id char(20) NOT NULL,
    order_num int NOT NULL,
    category char(30) NOT NULL,
    item char(30) NOT NULL,
    param char(30) NOT NULL,
    quantity int NOT NULL,
    price float(6,2) NOT NULL,
    table_status tinyint(10) NOT NULL DEFAULT 0,
    PRIMARY KEY (seq_id)
) ENGINE = InnoDB;

CREATE TABLE product_param (
    item char(20) NOT NULL,
    freq_min int,
    freq_max int,
    charging char(30) NOT NULL,
    price float(6,2) NOT NULL,
    description text,
    PRIMARY KEY (item)
) ENGINE = InnoDB;

INSERT INTO product_param (item, freq_min, freq_max, charging, price, description) VALUES
("param_small", 0, 100, "subscription/month", 200, "for params with a low frequency"),
("param_middle" , 101, 500, "subscription/month", 400, "for params with a relatively high frequency"),
("param_high" , 501, 800, "subscription/month", 700, "for params with a high frequency"),
("param_extra" , 801, null, "subscription/month", 1000, "for params with an extremely high frequency");

CREATE TABLE product_addition (
    item char(20) NOT NULL,
    charging char(30) NOT NULL,
    price float(6,2) NOT NULL,
    description text,
    PRIMARY KEY (item)
) ENGINE = InnoDB;

INSERT INTO product_addition (item, charging, price, description) VALUES
("alarm", "subscription/calculation", 2, "the price is multiplied by this price and the amount of params per second"),
("graph", "subscription/calculation", 5, "the price is multiplied by this price and the amount of params per second"),
("dignosis", "subscription/calculation", 10, "the price is multiplied by this price and the amount of params per second");


COMMIT;