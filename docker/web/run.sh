#!/bin/bash

SCRIPT_PATH=$(readlink -f "$0")
DIRECTORY_PATH=$(dirname "${SCRIPT_PATH}")

function init_server()
{
    APP_IP="$(/sbin/ifconfig eth0| grep "inet addr:" | awk {"print $2"} | cut -d ":" -f 2)"
    echo "ServerName localhost" >> /etc/apache2/apache2.conf

    service apache2 start
}

function init_configuration()
{
    a2enmod ssl expires headers rewrite
    php5enmod mcrypt

    # PHP INI
    echo "Config - PHP INI"
    CONFIG_FILE="${DIRECTORY_PATH}/extra/conf/php/php.ini"
    if [[ -e "${CONFIG_FILE}" ]]; then
        cp -f "${CONFIG_FILE}" /usr/local/etc/php/php.ini
        echo "File \"${CONFIG_FILE}\" successfully imported."
    fi
}

function init_vhosts()
{
    echo "Vhosts INIT - START"

    echo "Delete old vhosts"
    rm /etc/apache2/sites-enabled/*

    echo "Init vhosts"
    VHOSTS_PATH="/etc/apache2/sites-available/"
    if [[ -d "${VHOSTS_PATH}" ]]; then
        VHOST_FILES="$(find "${VHOSTS_PATH}" -maxdepth 1 -type f -name *.conf)"
        if [[ ! -z "${VHOST_FILES}" ]]; then
            for FILE in ${VHOST_FILES}; do
                FILENAME="$(basename "${FILE}")"

                VHOST_NAME="$(echo "${FILENAME}" | cut -d : -f 1)"

                a2ensite "${VHOST_NAME}"
            done
        fi
    fi

    echo "Vhosts INIT - END"
}

function init_blackfire()
{
    echo "Blackfire INIT - START"

    read -r -d '' BLACKFIRE_INI <<HEREDOC
extension=blackfire.so
blackfire.agent_socket=tcp://blackfire:${BLACKFIRE_PORT}
blackfire.agent_timeout=5
blackfire.log_file=/var/log/blackfire.log
blackfire.log_level=${BLACKFIRE_LOG_LEVEL}
blackfire.server_id=${BLACKFIRE_SERVER_ID}
blackfire.server_token=${BLACKFIRE_SERVER_TOKEN}
HEREDOC

    echo "${BLACKFIRE_INI}" >> /usr/local/etc/php/conf.d/blackfire.ini

    echo "Blackfire INIT - END"
}

function init_xdebug()
{
    echo "Xdebug INIT - START"

    read -r -d '' XDEBUG_INI <<HEREDOC
[xdebug]
xdebug.max_nesting_level=500
xdebug.profiler_enable_trigger=1
xdebug.profiler_output_dir=/var/www/html/xdebug
xdebug.profiler_output_name=cachegrind.out.%p.%u
xdebug.var_display_max_children=-1
xdebug.var_display_max_depth=-1
xdebug.var_display_max_data=-1
xdebug.remote_autostart=0
xdebug.remote_enable=1
xdebug.remote_port=9000
xdebug.remote_connect_back=1
xdebug.remote_handler=dbgp
HEREDOC

    echo "${XDEBUG_INI}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

     read -r -d '' XDEBUG_PROFILE <<HEREDOC
# Xdebug
export PHP_IDE_CONFIG="serverName=${XDEBUG_SERVER_NAME}"
export XDEBUG_CONFIG="remote_host=$(/sbin/ip route|awk '/default/ { print $3 }') idekey=${XDEBUG_IDE_KEY}"
HEREDOC

    echo "${XDEBUG_PROFILE}" >> ~/.bashrc

    echo "Xdebug INIT - END"
}

LOCK_FILE="/var/docker.lock"
if [[ ! -e "${LOCK_FILE}" ]]; then

    init_server
    init_xdebug
    init_blackfire

    touch "${LOCK_FILE}"
fi

init_configuration
init_vhosts

service apache2 start

tail -f /dev/null
