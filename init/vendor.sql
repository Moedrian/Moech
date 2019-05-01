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

CREATE TABLE product_param (
    category char(20) NOT NULL,
    freq_min int,
    freq_max int,
    charging char(30) NOT NULL,
    price float(6,2) NOT NULL,
    description text,
    PRIMARY KEY(category)
) ENGINE=InnoDB;

CREATE TABLE product_addition (
    category char(20) NOT NULL,
    charging char(30) NOT NULL,
    price float(6,2) NOT NULL,
    description text,
    PRIMARY KEY(category)
) ENGINE=InnoDB;

CREATE TABLE params_ref (
    seq_id int NOT NULL AUTO_INCREMENT,
    dev_id char(20) NOT NULL,
    param char(30) NOT NULL,
    freq int NOT NULL,
    min float(6,2),
    max float(6,2),
    duration float,
    extra text,
    PRIMARY KEY(seq_id)
) ENGINE=InnoDB;

INSERT INTO customers (cust_id, cust_name, cust_contact, cust_tel, cust_mail) VALUES
    (10000, 'Ariadust', 'Tori', '12345678900', 'musashi@mail.com');

INSERT INTO orders (order_num, order_date, cust_id) VALUES
    (20000, '2019-04-01', 10000);

COMMIT;