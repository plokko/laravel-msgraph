# Laravel Microsoft Graph API


## Installation
Install with composer

`composer require plokko/laravel-msgraph`

Laravel >=5.5 should auto discover and register the required services.

If you have laravel <5.5 you need to manually register the provider in your /config/app.php
```php
<?php
//...
   'providers' => [
         //...
         plokko\MsGraph\MsGraphServiceProvider::class,
         //...
   ],
//...
```

## Configuration


```
MSGRAPH_TENANT=<YOUR-TENANT-ID>
MSGRAPH_CLIENT_ID=<YOUR-CLIENT-ID>
MSGRAPH_CLIENT_SECRET=<YOUR-CLIENT-SECRET>
MSGRAPH_REDIRECT_URI=<YOUR-URL-CALLBACK>
```

## User

## Get user
You can get information about an user by id
```php
$user = MsGraph::User()->find("USER-ID");
/**@var \Microsoft\Graph\Model\User $user**/
```
or by principal name
```php
$user = MsGraph::User()->findByPrincipalName("user@principal.name");
```

### List users

```php
$userQuery = MsGraph::User()->list();
$userQuery
    ->take(5)//limit to 5
    ->search('displayName:Test')//Search
    ->orderBy('displayName')//Order
    ->select(['id','displayName'])//Select fields
    ;

$users = $userList->get(); // Execute the query
/**@var \Microsoft\Graph\Model\User[] $users**/
```
### Count users
You can count the users like the list users
```php
$userQuery = MsGraph::User()->list();
$userQuery
    ->search('displayName:Test')
    ;

$count = $userList->count(); // Execute the query
/**@var int $count**/
```

### Create user

```php
// Prepare your user data as a Microsoft\Graph\Model\User object or as an Array of key-values
$userData = new \Microsoft\Graph\Model\User([
    "mailNickname" => 'UserName',
    "displayName" => "User Name",
    "givenName" => "User",
    "surname" => "Name",

    "password" => "TheUserPassword123",

    "jobTitle" => null,
    "mail" => "test@user.sample",
    "userPrincipalName" => "test@user.sample",
    
    "mobilePhone" => null,
    "businessPhones" => ['0000 123456'],

    "officeLocation" => null,
    "preferredLanguage" => null,
]);
// Or
$userData = [
    "mailNickname" => 'UserName',
    "displayName" => "User Name",
    "givenName" => "User",
    "surname" => "Name",

    "password" => "TheUserPassword123",

    "jobTitle" => null,
    "mail" => "test@user.sample",
    "userPrincipalName" => "test@user.sample",
    
    "mobilePhone" => null,
    "businessPhones" => ['0000 123456'],

    "officeLocation" => null,
    "preferredLanguage" => null,
];
//...
$password = null; //If set will overwrite user password in data
$accountEnabled = null; // If the account is enabled, if set will overwrite user data, if null and not set in user data it will default to true
$forcePasswordChange = false; // Will force password change on next login (see passwordProfile)
MsGraph::User()->create($userData,$password,$accountEnabled,$forcePasswordChange);
```

### Update user

```php
// Prepare your user data as a Microsoft\Graph\Model\User object or as an Array of key-values
// id or userPrincipalName are required to identify the user
// You can fill only the information you want to change
$userData = new \Microsoft\Graph\Model\User([
    'id' => 'your-user-id',
    "mailNickname" => 'UserName',
]);
// Or
$userData = [
    "userPrincipalName" => "test@user.sample",
    "mailNickname" => 'UserName',
];
//...
MsGraph::User()->update($userData);
```


### Delete user
User can be deleted by id
```php
MsGraph::User()->delete("USER-ID");
```
or by principal name
```php
MsGraph::User()->deleteByPrincipalName("user@principal.name");
```
