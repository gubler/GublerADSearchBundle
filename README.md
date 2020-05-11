# GublerADSearchBundle

This is a Symfony 4 bundle to make searching Active Directory (or other LDAP directories) easier.

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

#### Environment Variables

Until a recipe has been created, copy the following to your `.env.dist` file:

```dotenv
###> gubler/ad-search-bundle ###
AD_SEARCH_ARRAY_TEST_USERS='%kernel.project_dir%/config/packages/dev/test_users.json'
AD_SEARCH_SERVER_ADDRESS=
AD_SEARCH_SERVER_PORT=3268
AD_SEARCH_BIND_USER=
AD_SEARCH_BIND_PASSWORD=
###< gubler/ad-search-bundle ###
```

#### LDAP Server Configuration 

Create the file `config/packages/gubler_ad_search.yaml` and then add the following configuration:

```yaml
gubler_ad_search:
    connection_type: server
    config:
        address: '%env(AD_SEARCH_SERVER_ADDRESS)%'
        port: '%env(AD_SEARCH_SERVER_PORT)%'
        bind_user: '%env(AD_SEARCH_BIND_USER)%'
        bind_password: '%env(AD_SEARCH_BIND_PASSWORD)%'
        test_users: ~
```

**Note:** Be careful with the `address` and `port` settings. `address` should be a domain controller
that is configured as a _Global Catalog_ so that you can find users across the entire AD forest. To search the Global
Catalog, you have to connect to the Domain Controller on port 3268 (instead of the normal LDAP port of 389).

#### Array of Test Users Configuration

First you need to create a `test_users.json` file. ADSearchBundle can do this for you by running
the command to create the file in `config/packages/dev`:

**NOTE: THIS IS CURRENTLY NOT WORKING. COPY THE FILE FROM THE `Resources` DIRECTORY.

```bash
bin/console ad-search:create-user-json
```

Create the file `config/packages/gubler_ad_search.yaml` and then add the following configuration:

```yaml
gubler_ad_search:
    connection_type: array
    config:
        address: ~
        port: ~
        bind_user: ~
        bind_password: ~
        test_users: '%env(resolve:AD_SEARCH_ARRAY_TEST_USERS)%'
```

#### Different Environments

The common use is to add the _Server_ configuration to `config/packages/gubler_ad_search.yaml` and the _Array_
configuration to `config/packages/dev/gubler_ad_search.yaml`. This will let you use Active Directory in `prod`
and a test array in `dev`.

## Roadmap

### Features

- Fix console command
- Symfony Recipe to ease installation
- Move config to environment variables

### Documentation

- Custom Search Adapters (implementing `ADSearchAdapterInterface`)
- Custom LDAP Factories (implementing `LdapFactoryInterface`)
