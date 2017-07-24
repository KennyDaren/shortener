#!/usr/bin/env bash

cd /tmp
wget https://github.com/phpredis/phpredis/archive/php7.zip -O phpredis.zip
unzip -o /tmp/phpredis.zip && mv /tmp/phpredis-* /tmp/phpredis && cd /tmp/phpredis && phpize && ./configure && make && sudo make install
sudo touch /etc/php/mods-available/redis.ini && echo extension=redis.so > /etc/php/mods-available/redis.ini
sudo ln -s /etc/php/mods-available/redis.ini /etc/php/7.0/apache2/conf.d/redis.ini
sudo ln -s /etc/php/mods-available/redis.ini /etc/php/7.0/fpm/conf.d/redis.ini
sudo ln -s /etc/php/mods-available/redis.ini /etc/php/7.0/cli/conf.d/redis.ini

sudo service php7.0-fpm restart
sudo service nginx restart
