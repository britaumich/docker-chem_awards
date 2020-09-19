ARG APACHE_VERSION=""
FROM httpd:${APACHE_VERSION:+${APACHE_VERSION}-}alpine

RUN apk update; \
    apk upgrade;
#ENV APACHE_DOCUMENT_ROOT=${DOCUMENT_ROOT}
ENV APACHE_DOCUMENT_ROOT=/usr/local/chem-awards/website/docs

# Copy apache vhost file to proxy php requests to php-fpm container
COPY demo.apache.conf /usr/local/apache2/conf/demo.apache.conf

RUN echo "Include /usr/local/apache2/conf/demo.apache.conf" \
    >> /usr/local/apache2/conf/httpd.conf

RUN sed -ri -e 's!/html!${APACHE_DOCUMENT_ROOT}!g' /usr/local/apache2/conf/*.conf
