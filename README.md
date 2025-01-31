OroHealthCheckBundle
====================

OroHealthCheckBundle implements a set of health checks for applications built on OroPlatform.
Based on the [Liip Monitor Bundle](https://github.com/liip/LiipMonitorBundle), it provides a way to perform the checks 
using the same configuration and environment that the application uses.

## Table of Contents

 - [Purpose](#purpose)
 - [Basic usage](#basic-usage)
 - [HealthCheck under Maintenance mode](#рealthcheck-under-maintenance-mode)
 - [Built in checks](#built-in-checks)
 - [Build your own check](#build-your-own-check)
 - [Links](#links)

## Purpose

With OroHealthCheckBundle, you can learn about the environment and configuration health check results via the web UI, API and CLI.
It helps you to ensure that the application environment is configured correctly, the external services integrated with 
the application are alive and accessible from the application.

It checks the following:

- Database server connection
- Elasticsearch server connection
- FileStorage state 
- Mail Transport connection
- RabbitMQ server connection
- Redis server connection
- WebSocket server connection
- Maintenance Mode state

## Basic usage

You can use the health check via:
- **CLI**. There are 2 available commands
    - the `bin/console monitor:list --env=prod` command provides a list of configured checks
    ```bash
    $ bin/console monitor:list --env=prod
    
    doctrine_dbal Check if Doctrine DBAL is available
    mail_transport Check if Mail Transport is available
    rabbitmq_server Check if RabbitMQ is available in case it is configured
    elasticsearch Check if Elasticsearch is available in case it is configured
    websocket Check if WebSocket server is available
    maintenance_mode Check if Maintenance Mode is running and not expired
    fs_cache_prod Check if "/var/www/var/cache/prod" is writable
    fs_logs Check if "/var/www/var/logs" is writable
    fs_var_data Check if "/var/www/var/data" is writable
    fs_web_media Check if "/var/www/public/media" is writable
    redis_cache Check if Redis cache is available
    redis_doctrine_cache Check if Redis doctrine cache is available
    redis_session_storage Check if Redis session storage is available
    ```

    - the `bin/console monitor:health --env=prod` command performs health checks
    ```bash
    $ bin/console monitor:health --env=prod

    OK Check if Doctrine DBAL is available
    OK Check if Mail Transport is available
    SKIP Check if RabbitMQ is available in case it is configured: RabbitMQ connection is not configured. Check Skipped.
    OK Check if Elasticsearch is available in case it is configured
    FAIL Check if WebSocket server is available: Not available
    FAIL Check if Maintenance Mode is running and not expired: Expired
    OK Check if "/var/www/var/cache/prod" is writable: The path is a writable directory.
    OK Check if "/var/www/var/logs" is writable: The path is a writable directory.
    OK Check if "/var/www/var/data" is writable: The path is a writable directory.
    OK Check if "/var/www/public/media" is writable: The path is a writable directory.
    OK Check if Redis cache is available
    OK Check if Redis doctrine cache is available
    OK Check if Redis session storage is available
    ```
If all health checks were successful, the `bin/console monitor:health --env=prod` command returns the 0 code. If at 
least one check has failed, the 1 code is returned.
- **Web Interface**. All configured checks and REST API documentation are available on the page with `/healthcheck` path
- **HTTP Status endpoint**. Pages that send only HTTP status in response
    - `/healthcheck/http_status_checks` can be used to get status after all available checks are executed
    - `/healthcheck/http_status_check/<some_check_id>` can be used to get status of a specific check
    (use `bin/console monitor:list --env=prod` to get check identifiers)
- **REST API**. Docs are available on the page with `/healthcheck` path

**Note:** For an OroCommerce application, make sure that the `%web_backend_prefix%` parameter is used before health check
urls. This parameter has `/admin` value by default. For example:
- `/admin/healthcheck`
- `/admin/healthcheck/http_status_check/<some_check_id>`
- `/admin/healthcheck/http_status_checks`

## HealthCheck under Maintenance mode

Keep in mind that you will NOT be able to use any http request to your web server if it has the configured _maintenance_ page.
In this case, you can use only **CLI** commands illustrated in the [Basic usage](#basic-usage) section.

When using healthcheck, you typically receive either the 200 or 502 http status codes. However, if you have any of the
configurations listed below, you receive the 503 http status code.

For the _Apache_ web server (in the `.htaccess` file)
```
<IfModule mod_rewrite.c>
    RewriteEngine On

    # Maintenance mode rewrites
    RewriteCond %{DOCUMENT_ROOT}/maintenance.html -f
    RewriteCond %{DOCUMENT_ROOT}/../var/maintenance/maintenance_lock -f
    RewriteCond %{SCRIPT_FILENAME} !maintenance.html
    RewriteRule ^.*$ /maintenance.html [R=503,L]
    ErrorDocument 503 /maintenance.html
</IfModule>
```

For the _Nginx_ web server (in the host configuration)
```
server {
    location / {
        if (-f /var/www/var/maintenance/maintenance_lock) {
            return 503;
        }
    }

    # Error pages
    error_page 503 /maintenance.html;
    location = /maintenance.html {
        root /var/www/;
    }
}
```

## Built-in checks

### Database server connection

Verifies the connection to the database via the application config.

### Elasticsearch server connection

Verifies if Elasticsearch server is accessible and may be connected via the application functionality.

### FileStorage state

Verifies if specific directories are accessible for writing:
- cache
- logs
- data
- media

### Mail Transport connection

Verifies if the mail transport is configured correctly and is accessible.

### RabbitMQ server connection

Verifies the connection to the RabbitMQ server via the application config.

### Redis server connection

Verifies the connection to the Redis server via the application config.

### WebSocket server connection

Verifies if the service is configured correctly and is running.

### Maintenance Mode state

With OroHealthCheckBundle, the maintenance mode undergoes the following changes:

#### Health checks in the maintenance mode

The bundle configuration keeps the `/healthcheck/http_status_checks` path whitelisted as the endpoint for the API calls.
If all health checks were successful, the `/healthcheck/http_status_checks` request returns the 200 response code. If at 
least one check has failed, the 502 response code is returned. The `/healthcheck/http_status_check/<some_check_id>` path
is used for an individual check, with the same response codes.

- The `Oro\Bundle\MaintenanceBundle\Drivers\FileDriver` class is used as maintenance driver.

The following example illustrates the configuration which can be used in _config.yml_ to change the behavior of the maintenance mode: 
```yaml
oro_health_check:
    maintenance_driver:
        options:
            file_path: %kernel.project_dir%/var/maintenance/maintenance_lock
```

## Build your own check

Each health check class must implement the `Laminas\Diagnostics\Check\CheckInterface` interface.
```php
<?php

namespace Oro\Bundle\HealthCheckBundle\Check;

use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;
use Laminas\Diagnostics\Result\Failure;

class CustomCheck implements CheckInterface
{
    #[\Override]
    public function check(): ResultInterface
    {
        $result = <result of some check>;
        
        return $result ? new Success() : new Failure();
    }

    #[\Override]
    public function getLabel(): string
    {
        return 'Custom check verifies ...';
    }
}
```
 
This class must be defined as a Symfony service and tagged as `liip_monitor.check` in order to be picked up
by the health check runner.
```yaml
oro_health_check.check.custom:
    class: Oro\Bundle\HealthCheckBundle\Check\CustomCheck
    tags:
        - { name: liip_monitor.check, alias: custom }
```

Alternatively, if you need to run various checks inside one service, implement the `Laminas\Diagnostics\Check\CheckCollectionInterface` interface.
The `getChecks()` method of this interface returns an array of checks that need to be executed.
```php
<?php

namespace Oro\Bundle\HealthCheckBundle\Check;

use Laminas\Diagnostics\Check\CheckCollectionInterface;

class CustomCheckCollection implements CheckCollectionInterface
{
    #[\Override]
    public function getChecks(): array
    {
        return [new CustomCheck()];
    }
}
```

Then, tag it as a `liip_monitor.check_collection`.
```yaml
oro_health_check.check.custom_collection:
    class: Oro\Bundle\HealthCheckBundle\Check\CustomCheckCollection
    tags:
        - { name: liip_monitor.check_collection, alias: custom_collection }
```

Resources
---------

  * [OroCommerce, OroCRM and OroPlatform Documentation](https://doc.oroinc.com)
  * [Contributing](https://doc.oroinc.com/community/contribute/)
  * [Liip Monitor Bundle][1]
  * [ZendDiagnostics][2]

[1]: https://github.com/liip/LiipMonitorBundle
[2]: https://github.com/zendframework/ZendDiagnostics
