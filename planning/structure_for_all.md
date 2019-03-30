# STRUCTURE OF ALL

## FRONTEND OPTIONS

Plan to write the demo using Vue.js

### 1. DATA

* Data persistence
  * YES
    * Data integrity
      * Raw data
      * Simplified data, for example, average
    * Expire Data
  * NO

* Type
  * Interval
  * Magnitude

* Encryption

### 2. ALARM SERVICE

* Reference Value Required
* Mode
  * Simplest alarm (if abnormal then alarm)
    * Quantity
    * Interval
  * Duration
  * Customization
* Alarm events' persistence

### 3. VISUALIZATION

* Reference Value Required
* Graph
  * Line chart
  * Histogram
  * Customization

### 4. DIAGNOSIS SERVICE

* EX!

## BACKEND FUNCTIONS

### 1. LINUX ENVIRONMENT

### 2. DATA TRANSFERING

```txt
Sensor -data-> Server -data-> Clients
```

* Reception - Glue...
* Storage - MySQL & Redis Collaboration
* Distribution - Workerman

### 3. User System

* Privilege
* User information
* Group