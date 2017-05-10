# GublerADSearchBundle

This is a Symfony 3 bundle to make searching Active Directory (or other LDAP directories) easier.

## Example

``` php
$adSearch = $this->get('gubler_ad_search.ad_search');

$users = $adSearch->search('user'); // get an array of ADUser objects for search term

$user = $adSearch->getUser('user'); // get a single ADUser with the supplied `samaccountname`
```

## Installation

Installation with composer:

``` json
    ...
    "require": {
        ...
        "gubler/ad-search-bundle": "0.1.*",
        ...
    },
    ...
```

Next, be sure to enable the bundle in your `app/AppKernel.php` file:

``` php
public function registerBundles()
{
    return array(
        // ...
        new Gubler\ADSearchBundle\GublerADSearchBundle(),
        // ...
    );
}
```

## Configuration

To configure the bundle, you must set the AD Connection information and what search class to use.

``` yaml
gubler_ad_search:
    ad_username: ~
    ad_password: ~
    ad_host: ~
    ad_base_dn: ~
    ad_port: 3268
    ad_search_class: Gubler\ADSearchBundle\Domain\Search\ArraySearch
```

### Connection Information

All of the parameters except for `ad_search_class` define values for binding to and searching an
Active Directory server.
- `ad_username` and `ad_password` must be an account that can do the initial `ldap_bind()` to the server.
- `ad_host` is the Active Directory server to bind to and then search.
- `ad_base_dn` is where to the bundle will start searching within your AD forest.
- `ad_port` is the port to connect to the `ad_host` on.

**Note:** Be careful with the `ldap_host` and `ldap_port` settings. `ldap_host` should be a domain controller that is
configured as a _Global Catalog_ so that you can find users across the entire AD forest. To search the Global Catalog,
you have to connect to the Domain Controller on port 3268 (instead of the normal LDAP port of 389).

### Search Class

This bundle comes with two search classes:

- **ArraySearch** searches against an array of test users and is good for automated tests and local development when you
  do not have access to an actual AD server. You can extend this class and override the `testUsers` method if you want
  to define your own test users.
- **AbstractServerSearch** is an abstract class for searching against an actual AD server. You will need to create a
  concrete class in your application that defines two methods (`chooseNameForAccount` and `dnToDomain`) whose
  implementation will depend heavily on your actual AD layout and application needs. You can see an example
  implementation in the Example directory.

You can create any other types of search classes as long as they implement the
`Gubler\ADSearchBundle\Domain\Search\ActiveDirectorySearch` interface.

By default the Bundle uses the `ArraySearch` class. Once you have created a class for searching your Active Directory
(either through extending the `AbstractServerSearch` class or implementing the `ActiveDirectorySearch` interface) you
configure your application to use that class.

### The LDAP Adapter Class

This bundle abstracts LDAP interactions so that you can stub/mock the LDAP adapter when you need to test your
Search Class. This bundle ships with two LDAP adapter classes:

- **LdapArrayAdapter** (default) that is used with the default `ArraySearch` and never actually tries to
  connect to LDAP.
- **LdapAdapter** which is a more robust adapter that will try and connect to LDAP. This is designed to be used with
  the `AbstractServerSearch` (specifically, your implementations of `AbstractServerSearch`).
  
If you need something more custom, you can also create your own adapter by implementing the `LdapAdapterInterfact` and
set your LDAP adapter in your Symfony configuration (see below).

### Where to put the configuration

You _can_ put all of the configuration in `app/config/config.yml`, but that locks you into a single search class and
connection information. That would also cause you to put your connection information into your version control system
(which would be a bad thing).

The better option is to add the connection information at the bottom your `app/config/parameters.yml.dist` file and then
set the actual values per installation after the actual `parameters.yml` file is created. 

``` yaml
gubler_ad_search:
    ad_username: ~
    ad_password: ~
    ad_host: ~
    ad_base_dn: ~
    ad_port: 3268
```

Symfony's [per environment config files](http://symfony.com/doc/current/configuration/environments.html) allows you to
load different search classes depending on environment. In your `config.yml` file you can load the search class to use
in development (or do not define anything and use the default `ArraySearch` class that comes with the bundle).

Then, in your `app/config/config_prod.yml` file you can define the class to use in production that searches your actual
Active Directory instance:

``` yaml
gubler_ad_search:
    ad_search_class: Your\Search\Class
    ldap_adapter_class: Gubler\ADSearchBundle\Domain\LdapAdapter\LdapAdapter (or Your\Ldap\Adapter\Class)
```
