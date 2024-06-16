FROM alpine:latest

# Install required packages
RUN apk add --no-cache php nginx supervisor php-fpm && \
    apk add --no-cache php-sqlite3 php-pdo_sqlite php-session php-mbstring php-tokenizer php-dom php-xml php-openssl


RUN adduser -h /var/www/html -D -H taskify
WORKDIR /var/www/html

# Copy files and configure services
COPY --chown=taskify:taskify ./ ./
RUN mv docker/www.nginx /etc/nginx/nginx.conf && \
    mv docker/supervisord.conf /etc/supervisord.conf && \
    mv docker/php-fpm.conf /etc/php83/php-fpm.d/www.conf && \
    mv docker/entrypoint.sh /entrypoint.sh && \
    rm -rf docker

EXPOSE 80

ENTRYPOINT ["sh", "/entrypoint.sh"]
