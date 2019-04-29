START TRANSACTION;

CREATE DATABASE IF NOT EXISTS vendor DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE vendor;

CREATE TABLE customers (
    cust_id int NOT NULL AUTO_INCREMENT,
    cust_name char(50) NOT NULL,
    cust_contact char(50) NOT NULL,
    cust_tel char(15) NOT NULL,
    cust_mail char(50) NULL,
    PRIMARY KEY(cust_id)
) ENGINE=InnoDB;

CREATE TABLE devices (
    dev_id char(20) NOT NULL,
    cust_id int NOT NULL,
    cust_name char(50) NOT NULL,
    province char(30) NOT NULL,
    city char(30) NOT NULL,
    PRIMARY KEY(dev_id)
) ENGINE=InnoDB;

CREATE TABLE orders (
    order_num int NOT NULL AUTO_INCREMENT,
    order_date date NOT NULL,
    cust_id int NOT NULL,
    PRIMARY KEY(order_num)
) ENGINE=InnoDB;

CREATE TABLE order_items (
    seq_id int NOT NULL,
    dev_id char(20) NOT NULL,
    order_num int NOT NULL,
    item char(30) NOT NULL,
    param char(30) NOT NULL,
    quantity int NOT NULL,
    price float(6,2) NOT NULL,
    PRIMARY KEY(seq_id)
) ENGINE=InnoDB;

CREATE TABLE products (
    item char(30) NOT NULL,
    charging_method char(20) NOT NULL,
    price float(6,2) NOT NULL,
    description text NOT NULL,
    PRIMARY KEY(item)
) ENGINE=InnoDB;

CREATE TABLE params_ref (
    seq_id int NOT NULL AUTO_INCREMENT,
    dev_id char(20) NOT NULL,
    param char(30) NOT NULL,
    min float(6,2) NOT NULL,
    max float(6,2) NOT NULL,
    duration float,
    extra text,
    PRIMARY KEY(seq_id)
) ENGINE=InnoDB;

INSERT INTO `products` (`item`, `charging_method`, `price`, `description`) VALUES
    ('diagnosis', 'variable', 500.00, 'enable diagnosis data feedback and graph'),
    ('graph-simple', 'subscription', 50.00, 'enable graph page but need extra charge'),
    ('graph-extra', 'variable', 150.00, 'a customized service of graph'),
    ('alarm-simple', 'subscription', 100.00, 'simple abnormal alarm for a param'),
    ('alarm-duration', 'subscription', 150.00, 'simple abnormal plus a duration time value'),
    ('alarm-extra', 'variable', 500.00, 'a customized service of alarm'),
    ('param-extra', 'subscription', 800.00, 'need technique evaluation'),
    ('param-high', 'subscription', 400.00, 'for volume from 151 to 400 per second'),
    ('param-middle', 'subscription', 200.00, 'for volume from 51 to 150 per second'),
    ('param-small', 'subscription', 100.00, 'for volume from 1 to 50 per second');

COMMIT;