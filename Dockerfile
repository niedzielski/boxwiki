FROM mediawiki:1.31.1

RUN a2enmod rewrite

COPY wait-for-it.sh /wait-for-it.sh
COPY entrypoint.bash /entrypoint.bash

ENTRYPOINT ["/bin/bash"]
CMD ["/entrypoint.bash"]
