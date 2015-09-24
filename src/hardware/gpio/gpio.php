<?php

class Gpio
{
    /** @var int */
    private $gpioPinNumber;

    /**
     *
     */
    public function changeDirectionToIn()
    {
        $fileHandle = fopen($this->getGpioDirectionFilePath(), 'w');
        fwrite($fileHandle, 'in');
        fclose($fileHandle);
    }

    /**
     *
     */
    public function changeDirectionToOut()
    {
        $fileHandle = fopen($this->getGpioDirectionFilePath(), 'w');
        fwrite($fileHandle, 'out');
        fclose($fileHandle);
    }

    /**
     * @return string
     */
    private function getGpioDirectionFilePath()
    {
        return $this->getBaseGpioPath() . '/direction';
    }

    /**
     * @param int $value
     */
    public function writeValue($value)
    {
        $fileHandle = fopen($this->getGpioValueFilePath(), 'w');
        fwrite($fileHandle, $value);
        fclose($fileHandle);
    }

    /**
     * @return int
     */
    public function readValue()
    {
        $fileHandle = fopen($this->getGpioValueFilePath(), 'r');
        $value = fgets($fileHandle);
        fclose($fileHandle);

        $value = trim($value);

        return $value;
    }

    /**
     * @return string
     */
    private function getGpioValueFilePath()
    {
        return $this->getBaseGpioPath() . '/value';
    }

    /**
     * @return string
     */
    public function getBaseGpioPath()
    {
        return '/sys/class/gpio/gpio' . $this->gpioPinNumber;
    }

    /**
     * @return int
     */
    public function getGpioPinNumber()
    {
        return $this->gpioPinNumber;
    }

    /**
     * @param int $gpioPinNumber
     */
    public function setGpioPinNumber($gpioPinNumber)
    {
        $this->gpioPinNumber = $gpioPinNumber;
    }
}
