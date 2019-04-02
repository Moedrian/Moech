# Moech

A PHP server-side project providing monitoring services for IoT devices

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

A step by step series of examples that tell you how to get a development env running

Say what the step will be

```
Give the example
```

And repeat

```
until finished
```

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

```
Give an example
```

### And coding style tests

Explain what these tests test and why

```
Give an example
```

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

