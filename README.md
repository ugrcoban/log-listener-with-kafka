# Big Data - Log Listener with KAFKA - Dockerfile

Big data - log listener PHP & MYSQL& KAFKA.
Upload logs to mysql && real-time display on charts.

## Requirements

On your own machine you should have:

- docker
- docker-compose

## Run the demo

```
docker-compose up -d
```

Visiting `http://localhost` or `http://{local_ip}`.


## Listen kafka to start.

```
docker-compose exec php php kafka.consumer.php
```

## Test, please delete to logs.
kafka.producer.php create logs automatically for test.

## Documentation

- Docker images for [kafka](https://hub.docker.com/r/wurstmeister/kafka/) and [zookeeper](https://hub.docker.com/r/wurstmeister/zookeeper/)
- [librdkafka](https://github.com/edenhill/librdkafka), a C implementation of the kafka protocol
- [php-rdkafka](https://github.com/arnaud-lb/php-rdkafka), a kafka client for php
