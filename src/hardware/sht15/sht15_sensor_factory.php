<?php

require_once('src/hardware/sht15/sht15_sensor.php');
require_once('src/hardware/gpio/gpio_factory.php');

class SHT15SensorFactory
{
    /**
     * @param int $dataGpioPinNumber
     * @param int $sckGpioPinNumber
     * @return SHT15Sensor
     */
    public function createWithDataGpioPinNumberAndSckGpioPinNumber($dataGpioPinNumber, $sckGpioPinNumber)
    {
        $gpioFactory = new GpioFactory();

        $dataGpio = $gpioFactory->createGpioForPinNumber($dataGpioPinNumber);
        $sckGpio = $gpioFactory->createGpioForPinNumber($sckGpioPinNumber);

        $sht15Sensor = new SHT15Sensor();
        $sht15Sensor->setDataGpio($dataGpio);
        $sht15Sensor->setSckGpio($sckGpio);

        return $sht15Sensor;
    }
}
