FROM circleci/php:7.3-node-browsers

# Install php extension required for laravel
RUN sudo apt-get install -y libpng-dev libxml2-dev && \
    sudo docker-php-ext-install mbstring && \
    sudo docker-php-ext-install gd && \
    sudo docker-php-ext-install tokenizer && \
    sudo docker-php-ext-install pdo && \
    sudo docker-php-ext-install pdo_mysql && \
    sudo docker-php-ext-install json && \
    sudo docker-php-ext-install bcmath && \
    sudo docker-php-ext-install ctype && \
    sudo docker-php-ext-install xml

# Intall dockerize utils
ENV DOCKERIZE_VERSION v0.6.1
RUN sudo wget https://github.com/jwilder/dockerize/releases/download/$DOCKERIZE_VERSION/dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && sudo tar -C /usr/local/bin -xzvf dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && sudo rm dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz
