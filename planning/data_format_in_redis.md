# DATA FORMAT FOR STORAGE IN REDIS

## RAW DATA FORMAT

Suppose we could easily get serialized data in JSON format, like this:

```JSON

{
    "sensor_1": {
        "time_1": "data_1",
        "time_2": "data_2"
    },
    "sensor_2": {
        "time_1": "data_1",
        "time_2": "data_2"
    }
}

```

Now we have two choices:

1. Just set the big JSON as a value
2. Or use a hash value

Here is a question, how to query the data via php in the future?
