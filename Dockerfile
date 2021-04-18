FROM drupal:9-apache

ARG USER_ID=1000
ARG GROUP_ID=1000

ENV USER_ID=$USER_ID
ENV GROUP_ID=$GROUP_ID
## Change the user to the host user for development
RUN usermod -u ${USER_ID} www-data && groupmod -g ${GROUP_ID} www-data && chown -R www-data:www-data web/modules web/themes web/sites
## Install Drush CLI, GraphQL and dependencies
RUN composer require drush/drush drupal/typed_data:^1.0-alpha5 drupal/graphql:^4.0.0

# COPY apache-drupal.conf /etc/apache2/sites-enabled/000-default.conf

WORKDIR /var/www/html