FROM mysql/mysql-server:8.0

COPY docker/entrypoint-mysql.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

COPY docker/my.cnf /etc/mysql/my.cnf

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
