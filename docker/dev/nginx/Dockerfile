FROM nginx:stable-alpine

LABEL maintainer="Nazar Taran"

WORKDIR /var/www/html

RUN set -x ; \
    addgroup -g 82 -S www-data ; \
    adduser -u 82 -D -S -G www-data www-data ; exit 0
COPY nginx.conf /etc/nginx/

EXPOSE 80 443

CMD chown www-data:www-data -R /var/www/html/ && \
    nginx -g 'daemon off;'
