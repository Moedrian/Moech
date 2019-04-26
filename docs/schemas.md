# Database Schemas

## Vendor Database Tables

![vendor_database](../assets/dbschemas/vendor_database.png)

### 1. customers

* This table records basic information of customers.

* Those who don't have orders can still sign up.

* The order number is an auth token that is visable to purchasers.

column | type | null | default | extras | comments
:--- | :--- | :--- | :--- | :--- | :---
cust_id | int | no | none | auto_increment | primary key
cust_name | char(50) | no | none | |
cust_contact | char(50) | no | none | |
cust_tel | char(15) | no | none | |
cust_mail | char(50) | yes | null | |

### 2. devices

column | type | null | default | extras | comments
:--- | :--- | :--- | :--- | :--- | :---
dev_id | char(20) | no | none | | primary key
cust_name | char(50) | no | none | |
cust_id | int | no | none | |
province | char(30) | no | none | |
city | char(30) | no | none | |

### 3. orders

column | type | null | default | extras | comments
:--- | :--- | :--- | :--- | :--- | :---
order_num | int | no | none | auto_increment | primary key
order_date | date | no | none | |
cust_id | int | no | none | |

### 4. orderitems

* This table lists items in each order.

column | type | null | default | extras | comments
:--- | :--- | :--- | :--- | :--- | :---
seq_id | int | no | none | auto_increment | primary key
dev_id | char(20) | no | none
order_num | int | no | none | | index
item | char(30) | no | none | |
param | char(30) | no | none | |
quantity | int | no | none | |
price | float(6,2) | no | none | |

### 5. products

* This table lists the price of each service.

* The total cost is accumulated according to the magnititude of those parameters requested by the customers.

column | type | null | default | extras | comments
:--- | :--- | :--- | :--- | :--- | :---
item | char(30) | no | none | | primary key
charging method | char(20) | no | none | |
price | float(6,2) | no | none | |
description | text | no | none | |

## Customer database tables

![customer_database](../assets/dbschemas/cust_databases.png)

### 1. information

#### 1. users

* It's optional whether the new customer could provide the system manager registration info when a purchase  is made.

* They can send this message later with an unique code associated with the order number, or simply, just the order num.
  
column | type | null | default | extras | comments
:--- | :--- | :--- | :--- | :--- | :---
username | char(30) | no | none | for logging in | primary key
alias | char(30) | yes | null | |
password | varchar(60) | no | none | encrypted |
user_tel | char(15) | no | none | |
user_mail | char(50) | yes | null | |
group | char(40) | no | none | | primary key
role | char(20) | no | none | |

#### 2. devices

column | type | null | default | extras | comments
:--- | :--- | :--- | :--- | :--- | :---
dev_id | char(20) | no | none | | primary key
group | char(40) | no | none | |

#### 3. alert_event

column | type | null | default | extras | comments
:--- | :--- | :--- | :--- | :--- | :---
dev_id | char(20) | no | none | | primary key
para | char(20) | no | none | |
value | float(8,2) | no | none | |
occur_time | datetime | no | none | | primary key

#### 4. default_values

column | type | null | default | extras | comments
:--- | :--- | :--- | :--- | :--- | :---
dev_id | char(20) | no | none | | primary key
para | char(20) | no | none | |
val | float(6,2) | no | none | |

### 2. values

#### value_item

* The name format of those tables is `dev_id_para`
* No primary keys set for those tables

column | type | null | default | extras | comments
:--- | :--- | :--- | :--- | :--- | :---
crt_time | datetime(3) | no | none | |
val | float(6,2) | no | none | |