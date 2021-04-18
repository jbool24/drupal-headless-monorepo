FROM drupal:9-apache

## Change the user to the host user for development
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data && chown -R www-data:www-data web/modules web/themes web/sites
## Install Drush CLI, GraphQL and dependencies
RUN	composer require drush/drush drupal/typed_data:^1.0-alpha5 drupal/graphql:^4.0.0

# COPY apache-drupal.conf /etc/apache2/sites-enabled/000-default.conf

WORKDIR /var/www/html