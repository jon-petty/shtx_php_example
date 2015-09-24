<?php

class SHT15Sensor
{
    /** @var Gpio */
    private $dataGpio;

    /** @var Gpio */
    private $sckGpio;

    /**
     * @return float
     * @throws Exception
     */
    public function measureTemperatureF()
    {
        $this->resetInterface();
        $this->initiateTransmission();

        // Send measure temperature request (00000011)
        $this->writeBitString('00000011');

        // Read ACK - A value of 0 indicates command was received
        $commandACK = $this->readBitStringOfLength(1);
        if ($commandACK != 0) {
            throw new Exception("Read temperature command not acknowledged by sensor");
        }

        // DATA goes high until reading is complete
        $sensorMeasuringPullup = $this->dataGpio->readValue();
        if ($sensorMeasuringPullup != 1) {
            throw new Exception("Sensor did not start reading temperature");
        }

        // Wait until data goes LOW
        $startTime = microtime(true);
        while ($this->dataGpio->readValue() == 1 && microtime(true) - $startTime < 3) {
            sleep(0.001);
        }

        $sensorMeasuringCompletePulldown = $this->dataGpio->readValue();
        if ($sensorMeasuringCompletePulldown != 0) {
            throw new Exception("Sensor temperature reading timed out");
        }

        // Read first byte
        $firstByte = $this->readBitStringOfLength(8);

        // Receive ACK request from chip - Data HIGH
        $firstByteACK = $this->dataGpio->readValue();
        if ($firstByteACK != 1) {
            throw new Exception("Sensor did not send first byte ACK request");
        }

        // Send ACK response - Data LOW
        $this->writeBitString('0');

        // Read second byte
        $secondByte = $this->readBitStringOfLength(8);

        // ACK while high to terminate without crc
        $secondByteACK = $this->dataGpio->readValue();
        if ($secondByteACK != 1) {
            throw new Exception("Sensor did not send second byte ACK request");
        }

        // Send ACK response to decline CRC bits - Data HIGH
        $this->writeBitString('1');

        $sht15Response = bindec($firstByte . $secondByte);

        $temperatureF = -39.4 + 0.018*$sht15Response;

        return $temperatureF;
    }

    /**
     * @return float
     * @throws Exception
     */
    public function measureHumidity()
    {
        $this->resetInterface();
        $this->initiateTransmission();

        // Send measure humidity request (00000101)
        $this->writeBitString('00000101');

        // Read ACK - A value of 0 indicates command was received
        $commandACK = $this->readBitStringOfLength(1);
        if ($commandACK != 0) {
            throw new Exception("Read temperature command not acknowledged by sensor");
        }

        // DATA goes high until reading is complete
        $sensorMeasuringPullup = $this->dataGpio->readValue();
        if ($sensorMeasuringPullup != 1) {
            throw new Exception("Sensor did not start reading temperature");
        }

        // Wait until data goes LOW
        $startTime = microtime(true);
        while ($this->dataGpio->readValue() == 1 && microtime(true) - $startTime < 3) {
            sleep(0.001);
        }

        $sensorMeasuringCompletePulldown = $this->dataGpio->readValue();
        if ($sensorMeasuringCompletePulldown != 0) {
            throw new Exception("Sensor temperature reading timed out");
        }

        // Read first byte
        $firstByte = $this->readBitStringOfLength(8);

        // Receive ACK request from chip - Data HIGH
        $firstByteACK = $this->dataGpio->readValue();
        if ($firstByteACK != 1) {
            throw new Exception("Sensor did not send first byte ACK request");
        }

        // Send ACK response - Data LOW
        $this->writeBitString('0');

        // Read second byte
        $secondByte = $this->readBitStringOfLength(8);

        // ACK while high to terminate without crc
        $secondByteACK = $this->dataGpio->readValue();
        if ($secondByteACK != 1) {
            throw new Exception("Sensor did not send second byte ACK request");
        }

        // Send ACK response to decline CRC bits - Data HIGH
        $this->writeBitString('1');

        $sht15Response = bindec($firstByte . $secondByte);

        $humidity = -2.0468 + 0.0367*$sht15Response + -.0000015955*$sht15Response*$sht15Response;

        return $humidity;
    }

    /**
     *
     */
    public function initiateTransmission()
    {
        $this->dataGpio->changeDirectionToOut();
        $this->sckGpio->changeDirectionToOut();

        $this->sckGpio->writeValue(0);
        $this->dataGpio->writeValue(0);

        // Set data HIGH, then set sck HIGH
        $this->dataGpio->writeValue(1);
        $this->sckGpio->writeValue(1);

        // Lower data while sck is HIGH
        $this->dataGpio->writeValue(0);

        // Lower sck, raise sck, then raise data while sck high
        $this->sckGpio->writeValue(0);
        $this->sckGpio->writeValue(1);
        $this->dataGpio->writeValue(1);
        $this->sckGpio->writeValue(0);
    }

    /**
     *
     */
    public function resetInterface()
    {
        $this->writeBitString('111111111');
    }

    /**
     * @param string $bitString
     */
    private function writeBitString($bitString)
    {
        $bitArray = str_split($bitString);

        $this->sckGpio->changeDirectionToOut();
        $this->dataGpio->changeDirectionToOut();

        $this->sckGpio->writeValue(0);

        foreach ($bitArray as $bit) {
            $this->dataGpio->writeValue($bit);

            $this->sckGpio->writeValue(1);
            $this->sckGpio->writeValue(0);
        }
    }

    /**
     * @param string $bitStringLength
     * @return string
     */
    private function readBitStringOfLength($bitStringLength)
    {
        $bitString = '';

        $this->sckGpio->changeDirectionToOut();
        $this->dataGpio->changeDirectionToIn();

        for ($i=0; $i<$bitStringLength; $i++) {
            $this->sckGpio->writeValue(1);
            $bitString .= $this->dataGpio->readValue();
            $this->sckGpio->writeValue(0);
        }

        return $bitString;
    }

    /**
     * @param Gpio $dataGpio
     */
    public function setDataGpio(Gpio $dataGpio)
    {
        $this->dataGpio = $dataGpio;
    }

    /**
     * @param Gpio $sckGpio
     */
    public function setSckGpio(Gpio $sckGpio)
    {
        $this->sckGpio = $sckGpio;
    }
}
