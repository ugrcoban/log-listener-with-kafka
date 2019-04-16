<?php

	header('Cache-Control: no-cache, must-revalidate');
	header('Content-type: application/json');
	
	require_once 'ugr.class.php';
	$UGR = new UGR();
	
	
	
	$producer = new \RdKafka\Producer();
	$producer->setLogLevel(LOG_DEBUG);

	if ($producer->addBrokers("kafka:9092") < 1) {
		echo "Failed adding brokers\n";
		exit;
	}

	$topic = $producer->newTopic("test");

	if (!$producer->getMetadata(false, $topic, 2000)) {
		echo "Failed to get metadata, is broker down?\n";
		exit;
	}

	$arr=$UGR->simpleLogCreator();
	if(is_array($arr)) {
		$topic->produce(RD_KAFKA_PARTITION_UA, 0, json_encode($arr));
		#echo "Message published\n";
	} else {
		#echo "No Message published\n";
	}

	## Google Chart LOG
	echo @file_get_contents('chart.json');