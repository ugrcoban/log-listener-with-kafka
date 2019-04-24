# Big Data - Log Listener on Docker

Big data - Website log listener demo with PHP & MYSQL & KAFKA. Run on docker.
* Task 1 - Real-time display logs on charts 
* Task 2 - Upload exploding logs to sql by kafka. And searc it easy and fast.

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

### To destroy the setup

```
docker-compose down
```

## Documentation

* [Docker - Network](https://docs.docker.com/network/)
* [Docker - Environment Variables](https://docs.docker.com/compose/environment-variables/)

- Docker images for [kafka](https://hub.docker.com/r/wurstmeister/kafka/) and [zookeeper](https://hub.docker.com/r/wurstmeister/zookeeper/)
- [librdkafka](https://github.com/edenhill/librdkafka), a C implementation of the kafka protocol
- [php-rdkafka](https://github.com/arnaud-lb/php-rdkafka), a kafka client for php
