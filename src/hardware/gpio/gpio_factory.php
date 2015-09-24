<?php

require_once('src/hardware/gpio/gpio.php');

class GpioFactory
{
    /**
     * @param int $pinNumber
     * @return Gpio
     */
    public function createGpioForPinNumber($pinNumber)
    {
        $gpio = new Gpio();
        $gpio->setGpioPinNumber($pinNumber);

        $this->exportGpioIfNeeded($gpio);

        return $gpio;
    }

    /**
     * @param Gpio $gpio
     * @throws Exception
     */
    private function exportGpioIfNeeded(Gpio $gpio)
    {
        if (!file_exists($gpio->getBaseGpioPath())) {
            throw new Exception("GPIO port " . $gpio->getGpioPinNumber() . " is not exported");
        }
    }
}
