# GublerADSearchBundle

This bundle provides easy connection to Active Directory servers from your Symfony application.

- [Installation](installation.md)
- [Configuration](configuration.md)

## Usage

Once you have the bundle installed and configured, you can use autowiring to provide the
`ADSearchService` into your code:

```php
public function __construct(private ADSearchService $adSearch)
{
}
```

Once you have the `ADSearchService`, you have multiple ways of searching Active Directory/LDAP:

```php
public function search () {
    // find all that match a search term
    $arrayOfUsers = $this->adSearch->search('name');
    
    // find one by GUID (using Symfony/Uuid)
    $guid = Uuid::fromString('192D7590-6036-4358-9239-BEA350285CA2');
    $singleUser = $this->adSearch->find($guid);
    
    // find one by search term
    $singleUser = $this->adSearch->findBy('samaccountname', 'User');
}
```

## Handling the Result

Each of the methods provided by the `ADSearchService` return either an `Symfony\Component\Ldap\Entry` or
an array of `Entry` objects.

Normally you want to query the `Entry` object for one or more fields. This bundle provides a helper class
to access values in the `Entry` object: `EntryAttributeHelper`.

```php

$email = EntryAttributeHelper::getAttribute(entry: $entry, attribute: 'mail');
$name = EntryAttributeHelper::getAttributeOrNull(entry: $entry, attribute: 'displayName');

```

You can also set if the attribute name is case-sensitive (default `false`) and if you want the first
value (the default option) or if you want all values for the attribute.
