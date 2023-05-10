# GublerADSearchBundle

This is a Symfony 6 bundle to make searching Active Directory (or other LDAP directories) easier.

| Bundle Version | Symfony Version | PHP Version |
|----------------|-----------------|-------------|
| 6.0            | 6.2             | >=8.1       |
| 5.0            | 6.1             | >=8.1       |
| 4.x            | 5.0 - 6.0       | >=7.2       |
| 1.2            | >= 4.4          | >=7.0       |

**DO NOT USE THE 1.3 Release, it is very broken**.

## Usage

For full usage documentation, see the [documentation](docs/index.md).

### Example

```php
public function __construct(private ADSearchService $adSearch)
{
}

public function search () {
    // find all that match a search term
    $arrayOfUsers = $this->adSearch->search('name');

    // find one by GUID
    $guid = Uuid::fromString('192D7590-6036-4358-9239-BEA350285CA2');
    $singleUser = $this->adSearch->find($guid);

    // find one by search term
    $singleUser = $this->adSearch->findBy('samaccountname', 'User');
}
```

## Installation

Full details are in the [installation documentation](docs/installation.md).

Installation with composer:

```console
composer require gubler/ad-search-bundle
```

### Configuration

Configuration details are in the [configuration documentation](docs/configuration.md).
