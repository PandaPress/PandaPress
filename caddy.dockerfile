FROM caddy:2.9.1-alpine

RUN mkdir -p /var/www/html

RUN mkdir -p /var/log/caddy

# Set proper ownership for Caddy user (instead of uid 1000)
RUN chown -R 1000:1000 /var/log/caddy
RUN chmod 755 /var/log/caddy