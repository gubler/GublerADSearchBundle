# GublerADSearchBundle

This is a Symfony 4 bundle to make searching Active Directory (or other LDAP directories) easier.

This will also work with Symfony 3.4 if you are running PHP >= 7.1.3.

## Example

```php
/** @var ADSearchService */
protected $adSearch;

public function __construct(ADSearchService $adSearch)
{
    $this->adSearch = $adSearch;
}

public function search () {
    // find all that match a search term
    $arrayOfUsers = $this->adSearch->search('User');
    
    // find one by GUID
    $guid = Uuid::fromString('192D7590-6036-4358-9239-BEA350285CA2');
    $singleUser = $this->adSearch->find($guid);
    
    // find one by search term
    $singleUser = $this->adSearch->findBy('samaccountname', 'User');
}
```

## Installation

Installation with composer:

```bash
composer require gubler/ad-search-bundle
```

Then register the bundle in your `config/bundles.php`:

```php
return [
    // ...
    Gubler\AdSearchBundle\GublerAdSearch::class => ['all' => true],
]
```

### Configuration

The ADSearchBundle supports either connecting to an LDAP server or using an array of test users (useful
in development when you may not have access to an LDAP server).

#### LDAP Server Configuration 

Create the file `config/packages/gubler_ad_search.yaml` and then add the following configuration:

```yaml
gubler_ad_search:
    connection_type: server
    server_address: test_server
    server_port: 3268
    server_bind_user: testUser
    server_bind_password: password
```

**Note:** Be careful with the `server_address` and `server_port` settings. `server_host` should be a domain controller
that is configured as a _Global Catalog_ so that you can find users across the entire AD forest. To search the Global
Catalog, you have to connect to the Domain Controller on port 3268 (instead of the normal LDAP port of 389).

#### Array of Test Users Configuration

First you need to create a `test_users.json` file. ADSearchBundle can do this for you by running
the command to create the file in `config/packages/dev`:

```bash
bin/console ad-search:create-user-json
```

Create the file `config/packages/gubler_ad_search.yaml` and then add the following configuration:

```yaml
gubler_ad_search:
    connection_type: array
    array_test_users: '%kernel.project_dir%/config/packages/dev/test_users.json'
```

## Roadmap

### Features

- Symfony Recipe to ease installation
- Move config to environment variables

### Documentation

- Custom Search Adapters (implementing `ADSearchAdapterInterface`)
- Custom LDAP Factories (implementing `LdapFactoryInterface`)
