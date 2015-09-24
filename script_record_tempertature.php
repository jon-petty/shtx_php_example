<?php

require_once('src/hardware/sht15/sht15_sensor_factory.php');

$sht15SensorFactory = new SHT15SensorFactory();
$sht15Sensor = $sht15SensorFactory->createWithDataGpioPinNumberAndSckGpioPinNumber(100, 97);

$humidity = $sht15Sensor->measureTemperatureF();

print "Temperature: " . $humidity . " F\n";
