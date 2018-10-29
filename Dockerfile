FROM mediawiki:1.31.1

RUN a2enmod rewrite

COPY wait-for-it.sh /wait-for-it.sh
COPY entrypoint.bash /entrypoint.bash

# This causes mounted files to take forever to be updated.
RUN rm /usr/local/etc/php/conf.d/opcache-recommended.ini

ENTRYPOINT ["/bin/bash"]
CMD ["/entrypoint.bash"]
