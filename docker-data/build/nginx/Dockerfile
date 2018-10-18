FROM debian:stretch
RUN apt-get update && apt-get install -y nginx vim

COPY nginx.conf /etc/nginx/nginx.conf
ADD sites-enabled /etc/nginx/sites-enabled
RUN rm /etc/nginx/sites-enabled/default

WORKDIR /var/www/livestream

EXPOSE 80
EXPOSE 443

CMD ["nginx"]
