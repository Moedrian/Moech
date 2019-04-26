# Moech

A PHP server-side project providing monitoring services for IoT devices

## Stack

Considering the demand of monitoring IoT sensors or system data that come with a large quantity, nowadays there's a new kind of storage program called time series database(TSDB) optimized specially for handling and storaging data with a timestamp. But each of them requires special runtime environment configuration or high level hardware, for example,

* [InfluxDB](https://github.com/influxdata/influxdb) requires SSD and more RAMs;
* [TimescaleDB](https://github.com/timescale/timescaledb) is a package built as a PostgreSQL extension;
* [OpenTSDB](https://github.com/OpenTSDB/opentsdb) need Apache HBase, etc.

Since the present database configuration is MySQL, the original cache layer architecture plan, which deploys Redis, a NoSQL database runs in RAM in order to achieve fast querying, seems easy and economic. But if there's a chance to give TSDB a try, it's fairly feasible to switch to PostgreSQL Timescale because PHP has PDO driver as well.

## Overview

The database schemas is [here](docs/schemas.md). Based on this setting, the procedures doc is [here](docs/procedure.md).

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

* An ideal input for an order should be like this

```json
{
    "cust_name":"Pop Team Epic",
    "orders":{
        "item_1":{
            "dev_id":"YJSP114",
            "item":"param-middle",
            "param":"rev",
            "quantity":"2"
        },
        "item_2":{
            "dev_id":"YJSP514",
            "item":"param-high",
            "param":"voltage_AB",
            "quantity":"3"
        }
    }
}
```

* An ideal input for signing up should be like this:

```JSON
{
    "customer": {
        "cust_name": "Pop Team Epic",
        "cust_contact": "Pipimi",
        "cust_tel": "114-514-893",
        "mail": "anime@kuso.com"
    }
}
```

* An ideal input for devices' registration should be lile this:

```JSON
{
    "cust_name": "Pop Team Epic",
    "dev":{
        "dev_1":{
            "dev_id": "YJSNPI114",
            "province": "Shandong",
            "city": "Utopia"
        },
        "dev_2":{
            "dev_id": "YJSNPI514",
            "province": "Jessie",
            "city": "Lucy"
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