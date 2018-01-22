# GublerADSearchBundle TODOs

## Needs

- AD to User Adapter
    - ADUser interface for user accounts
        - Set AD information
            - ADguid
            - ADemail
            - ADsamAccountName
            - ADrecord
        - getADGuid
        - getADEmail (nullable)
        - getADSamAccountName
        - getADDomain (nullable)
        - getADRecord
    
- Class for Searching AD
    - Abstract Class for determining Domain <- Interface?
        - Passed User's AD information
- Class for searching Array
    - Default test user JSON
    - Command for creating JSON for customization

## What is the Use Case for this bundle?

- I need to search for a user in AD to create a new User in my system
    - provide a search term
    - LDAP Connection
    - return an array of ADUser objects
- I need to search for user from an Array when testing
    - provide a search term
    - array filter
    - return an array of ADUser objects
- I need to update a user in my system with their information in AD
    - Need their GUID

## Reference

- [GUID vs SID](https://technet.microsoft.com/en-us/library/cc961625.aspx)
- [AD User Attributes](http://www.kouti.com/tables/userattributes.htm)

## Ideas

- Create an ADUser interface that include AD GUID, SamAccountName, etc.

## TODO

- DomainResolverInterface
- ServerSearchInterface    
- TestUserProviderInterface
    - TestUserProvider


### Config

- LDAP Connection Credentials
- Test User Provider Class
    - Has Default
- Server Search Class
- Domain Resolver Class
- User Class