# Moech

A PHP server-side project providing monitoring services for IoT devices

## Overview

### MySQL databases

#### infomation database tables

##### 1. customers

This table records basic information of customers. Those who don't have orders can still sign up.

column | type | null | default | extras | comments
:--- | :--- | :--- | :--- | :--- | :---
cust_id | int(11) | no | none | auto_increment | primary key
cust_name | char(50) | no | none | |
cust_province | char(20) | yes | null | |
cust_city | char(10) | yes | null | |
cust_address | char(50) | yes | null | |
cust_contact | char(50) | yes | null | |
cust_tel | char(15) | yes | null | |
cust_mail | char(50) | yes | null | |

##### 2. orders

column | type | null | default | extras | comments
:--- | :--- | :--- | :--- | :--- | :---
order_num | int(11) | no | none | | primary key
order_date | datetime | no | none | |
cust_id | int(11) | no | none | | foreign key

##### 3. orderitems

This table lists items in each orders

column | type | null | default | extras | comments
:--- | :--- | :--- | :--- | :--- | :---
ordernum | int(11) | no | none | | primary key
item | char(30) | no | none | |
charging method | char(20) | no | none | |

##### 4. products

This table lists the price of each service.

* The total cost is accumulated according to the magnititude of those parameters requested by the customers.

column | type | null | default | extras | comments
:--- | :--- | :--- | :--- | :--- | :---
item | char(20) | no | none | | primary key
charging method | char(20) | no | none | |
price | int(10) | no | none | |

#### customer database tables

##### 1. data_item

##### 2. alert_event

##### 3. default_values

## Getting Started

A little PHP and MySQL knowledge along with some NoSQL, for example, Redis.

### Prerequisites

* Environment
  * Apache
  * MariaDB (Main storage)
  * PHP
  * Redis (Cache)
  * Vue.js (for demonstration only)

### Installing

End with an example of getting some data out of the system or using it for a little demo

## Running the tests

* An ideal input for signing up should be like this:

```JSON
{
    "customer": {
        "name": "Pipimi",
        "mail": "anime@kuso.com",
        "tel": "114-514-893",
        "company": "Pop Team Epic"
    }
}
```

* An ideal input for devices' registration should be lile this:

```JSON
{
    "id_01": {
        "data": {
            "data_persistence": {
                "toggle": "on",
                "data_integrity": {
                    "raw": "yes",
                    "simplification": "no"
                }
            },
             "interval": "60",
             "Ecryption": "yes"
        },
        "owner": "Pipimi",
        "parameter_01": {
            "alarm": {
                "toggle": "on",
                "customization": "no",
                "type": "abnormal",
                "range": ""
            },
            "visualization": {
                "toggle": "on",
                "type": "line-chart"
            },
            "diagnosis": {
                "toggle": "off"
            }
        }
    }
}
```

### Break down into end to end tests

Explain what these tests test and why

### And coding style tests

Explain what these tests test and why

## Deployment

Add additional notes about how to deploy this on a live system

## Built With

* [predis](https://github.com/nrk/predis) - A flexible and feature-complete Redis client for PHP and HHVM
* [Workerman](https://github.com/walkor/Workerman) - An asynchronous event driven PHP framework for easily building fast, scalable network applications.
* [Vue.js](https://github.com/vuejs/vue) - A progressive, incrementally-adoptable JavaScript framework for building UI on the web.

## Contributing

## Versioning

## Authors

## License

## Acknowledgments

* Hat tip to anyone whose code was used
* Inspiration
* etc