FROM webdevops/php-nginx:8.0-alpine
WORKDIR /app/

ENV WEB_DOCUMENT_ROOT="/app/public"

COPY . .

RUN composer install \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --no-dev \
    --prefer-dist
