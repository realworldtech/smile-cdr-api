FROM debian:stretch

RUN apt-get -y update && apt-get -y install php-cli php-curl php-xml php-soap
COPY ./run_cdr.php /src/run_cdr.php
COPY ./config_params.php /src/config_params.php

WORKDIR /src
CMD [ "/usr/bin/php", "/src/run_cdr.php" ]