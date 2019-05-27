START TRANSACTION;

CREATE DATABASE IF NOT EXISTS management_info DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE management_info;


CREATE TABLE devices (
    dev_id char(20) NOT NULL,
    username char(50) NOT NULL,
    province char(30) NOT NULL,
    city char(30) NOT NULL,
    PRIMARY KEY (dev_id)
) ENGINE = InnoDB;

CREATE TABLE users (
    username char(50) DEFAULT NULL,
    user_mail char(50) NOT NULL,
    user_tel char(15) NOT NULL,
    password varchar(60) NOT NULL,
    PRIMARY KEY (user_mail)
) ENGINE = InnoDB;

CREATE TABLE params_ref (
    seq_id int NOT NULL AUTO_INCREMENT,
    param char(30) NOT NULL,
    order_num int NOT NULL,
    dev_id char(20) NOT NULL,
    freq int NOT NULL,
    min float(6,2) DEFAULT NULL,
    max float(6,2) DEFAULT NULL,
    abnormal_duration float DEFAULT NULL,
    PRIMARY KEY (seq_id)
)  ENGINE = InnoDB;

CREATE TABLE alert_events (
    seq_id int NOT NULL AUTO_INCREMENT,
    event_time datetime(3) NOT NULL,
    dev_id char(20) NOT NULL,
    param char(30) NOT NULL,
    abnormal_value float(6,2) NOT NULL,
    PRIMARY KEY (seq_id)
) ENGINE = InnoDB;

COMMIT;