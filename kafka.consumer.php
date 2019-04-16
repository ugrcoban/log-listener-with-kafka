<?php

	require_once 'ugr.class.php';
	$UGR = new UGR();
	
	$consumer = new \RdKafka\Consumer();
	$consumer->setLogLevel(LOG_DEBUG);
	$consumer->addBrokers("kafka:9092");

	$topic = $consumer->newTopic("test");

	$topic->consumeStart(0, RD_KAFKA_OFFSET_BEGINNING);

	
	## Find Last Table for insert DB
	$last_table=$UGR->getLastTable();#name,date,order
	$insert_limit=50;#Insert all records for break - checker limit
				
	
	## Listen and catch kafka
	echo "Consumer started ( Listening to Kafka ) \n";
	while (true) {
		
		$msg = $topic->consume(0, 1000);
		
		#Waiting to array {timestamp, log_level, server_name, log_detail}   
		if ($msg->payload) {
			
			## Records logs for one/multi
			$payload=json_decode($msg->payload);
			if($records=is_array($payload[0]) ? $payload : array("0"=>$payload)) {
				UNSET($i,$sql_inserts);
				foreach($records as $record) {
					$sql_inserts[floor(++$i/$insert_limit)][]="('{$record[0]}','{$record[1]}','{$record[2]}','{$record[3]}')";
					$min_date=!$min_date || $min_date>substr($record[0],0,10) ? substr($record[0],0,10) : $min_date;
					
					## Display terminal screen
					echo "{$record[0]} {$record[1]} {$record[2]} {$record[3]}\n";
					
					##Chart data stats
					$stats[substr($record[0],11,3).(substr($record[0],14,1) >=3 ? '30' : '00')][$record[2]]++;
				}
				
				
				## INSERT DB for every limit
				if(COUNT($sql_inserts)) {
					foreach($sql_inserts as $sql_insert) {
						
						## Crete New Table : No last table or max limit MB last table
						if($last_table===NULL || $UGR->getTableSizes($last_table['name'])[$last_table['name']]['size']>$UGR->table['max_load']) {
							$last_table=$UGR->createNewTable($last_table,$min_date);
						}
						
						try {
							$UGR->db->exec("INSERT INTO {$last_table['name']} (timestamp,log_level,server_name,log_detail)
														VALUES ".implode(",",$sql_insert));
						} catch(PDOException $ex) {
							if($ex->getCode()!=23000)#double checker
								$UGR->PDOException($ex);
						}
					}
				}
				
				
				
				
				/**** OUTPUT - Display Google Charts ****/
				UNSET($chart_data);
				$thead=$UGR->logs['servers'];
				array_unshift($thead, "Time");
				$chart_data[]=$thead;
	
				## No stats yet
				if(!COUNT($stats)) {
				
					$chart_data[]=array(date("H:").(date('i')>=30 ? '30':'00'),0, 0, 0, 0, 0);
				
				## Stats update
				} else {
				
					foreach($stats as $time=>$s) {
						UNSET($temp);
						$temp[]=$time;
						foreach($UGR->logs['servers'] as $server_name) {
							$counter=intval($stats[$time][$server_name]);
							$temp[]=$counter;
						}
						$chart_data[]=$temp;
					}
				}
	
				## Display on google chart
				$fp = fopen('chart.json', 'w');
				fwrite($fp, json_encode($chart_data,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
				fclose($fp);
			}
			
		}#endRecord
	}
