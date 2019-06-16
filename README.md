# Moech

![GitHub](https://img.shields.io/github/license/marogatari/Moech.svg) ![GitHub last commit](https://img.shields.io/github/last-commit/marogatari/Moech.svg) ![GitHub release](https://img.shields.io/github/release-pre/marogatari/Moech.svg)

A PHP server-side project providing monitoring services for IoT devices

## Getting Started

### Prerequisites

* Composer
* Git

### Installing

Get the newest repository by

```cmd
git clone git@github.com:marogatari/Moech.git
```

or find releases [here](https://github.com/marogatari/Moech/releases).

After the git clone or extraction, run `composer install` to get the dependencies.

## Running the tests

Find required json [here](https://github.com/marogatari/Moech/tree/master/test/example.json.d)

## Deployment

Find instances required file at

A new instance requires

* Environment
  * Apache
  * MariaDB
  * PHP
  * Redis 

And the ports for workerman services shall be open for communication.