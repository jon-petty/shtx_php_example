README

The scripts script_record_humidity.php and script_record_temperature.php are intended to be run
on an Odroid using GPIO pin #100 for DATA and GPIO pin #97 for SCK. These pins must be exported for the scripts
to use them. Please change to super-user with the following command.

sudo su -

Next, export the two pins so they can be used by the scripts.

cd /sys/class/gpio
echo 100 > export
echo 97 > export

The scripts can be executed with the following commands.

php script_record_humidity.php
php script_record_tempertature.php
