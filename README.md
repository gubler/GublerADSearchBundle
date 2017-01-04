## GublerADSearchBundle

This is a Symfony 3 bundle to make searching Active Directory (or other LDAP directories) easier.

### Example

~~~php
$adSearch = $this->get('gubler_ad_search.ad_search');

$users = $adSearch->search('user'); // get an array of ADUser objects for search term

$user = $adSearch->getUser('user'); // get a single ADUser with the supplied `samaccountname`
~~~

### Installation

TODO: Composer
TODO: AppKernel

### Configuration

TODO: config.yml

### Using the ArraySearch

#### Using your own test data

### Implementing a ServerSearch class
