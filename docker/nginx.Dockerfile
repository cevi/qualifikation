FROM nginx:alpine

# Configure nginx
COPY docker/default.conf /etc/nginx/conf.d
COPY . /var/www/html/
