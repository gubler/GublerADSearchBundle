# Configuration

The ADSearchBundle supports either connecting to an LDAP server or
using an array of test users (useful in development when you may not
have access to an LDAP server).

Create the file `config/packages/gubler_ad_search.yaml` and then add
the configuration depending on which type of connection you need.

## Connecting to a server

Minimal configuration for connection to a server is:

```yaml
# config/packages/gubler_ad_search.yaml
gubler_ad_search:
    connection_type: server
    config:
        address: 'server.address.fdqn'
        bind_user: 'username for binding to server'
        bind_password: 'password for binding to server'
```

### Server Address and Port

The server you connect to for searching should be one configured as a
_Global Catalog_ so that you can find users across the entire AD forest.

To search the Global Catalog, you have to connect to the Domain Controller
on port 3268 (instead of the normal LDAP port of 389). By default, this bundle
uses port 3268. If you need to connect to a different port, you can set that
in the configuration.

```yaml
# config/packages/gubler_ad_search.yaml
gubler_ad_search:
    connection_type: server
    config:
        address: 'server.address.fdqn'
        port: 389
```

### Bind Username and password

It is recommended that you set your `bind_user` and `bind_password`
in either environment variables or in your secrets.

```dotenv
# .env.local or other file not commited to repository
AD_SEARCH_BIND_USER="username"
AD_SEARCH_BIND_PASSWORD="password"
```

and then reference that in your config:

```yaml
# config/packages/gubler_ad_search.yaml
gubler_ad_search:
    connection_type: server
    config:
        address: 'server.address.fdqn'
        bind_user: '%env(AD_SEARCH_BIND_USER)%'
        bind_password: '%env(AD_SEARCH_BIND_PASSWORD)%'
```

### Secure Connections

You can also configure this bundle to use a secure connection (via
the [Symfony LDAP component](https://symfony.com/doc/current/components/ldap.html)).


By default, this bundle **does not** use a secure connection. To enable it,
add the following configuration: 

```yaml
# config/packages/gubler_ad_search.yaml
gubler_ad_search:
    connection_type: server
    config:
        address: 'server.address.fdqn'
        secure:
            enabled: true
```

When enabling the secure connection, you may have to also set the port to
connect to.

The secure connection will use whatever certificates PHP is configured to use.
You can also set a path to a certificate to use for the secure connection. When
setting this option, you must provide the full path to the certificate. You can
use default Symfony parameters to make this easier. In this example, the certificate
is stored in the application's `var` directory. The certificate's format can be any
that PHP can understand.

```yaml
# config/packages/gubler_ad_search.yaml
gubler_ad_search:
    connection_type: server
    config:
        address: 'server.address.fdqn'
        secure:
            enabled: true
            cert_path: '%kernel.project_dir%/var/certificate.pem'
```

## Using an array

Sometimes you do not have access to an LDAP/AD server (for example, during
development or testing). In those cases, you can use an array of LDAP data
to search against.

### Generating the array

This bundle provides a console command for generating a set of users with
(fairly) comprehensive AD information (including binary `ObjectGUID`s).

```console
php bin/console ad-search:create-user-json
```

This will generate a `test_users.json` file in your `var` directory that you
can then configure the bundle to use:

```yaml
gubler_ad_search:
    connection_type: array
    config:
        test_users: '%kernel.project_dir%/var/test_users.json'
```

## Different Environments

The common use is to use `server` configuration in PROD and `arra` configuration in DEV:

```yaml
gubler_ad_search:
    connection_type: array
    config:
        test_users: "%kernel.project_dir%/var/test_users.json"

when@prod:
    gubler_ad_search:
        connection_type: server
        config:
            address: '%env(AD_SEARCH_SERVER_ADDRESS)%'
            port: 3268
            bind_user: '%env(AD_SEARCH_BIND_USER)%'
            bind_password: '%env(AD_SEARCH_BIND_PASSWORD)%'
            secure:
                enable: true
                cert_path: '%env(AD_SEARCH_CERT_PATH)'
            test_users: ~
```
