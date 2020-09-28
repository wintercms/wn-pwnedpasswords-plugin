# About

Adds the `notpwned:min` validation rule to check values against the [HaveIBeenPwned Pwned Passwords](https://haveibeenpwned.com/Passwords) service using the ranged search (k-anonymity) feature for privacy & security. If a value has been pwned more than `:min` (1 by default) times, then the provided value will fail validation.

# Installation

To install from the [Marketplace](https://octobercms.com/plugin/luketowers-pwnedpasswords), click on the "Add to Project" button and then select the project you wish to add it to before updating the project to pull in the plugin.

To install from the backend, go to **Settings -> Updates & Plugins -> Install Plugins** and then search for `LukeTowers.PwnedPasswords`.

To install from [the repository](https://github.com/luketowers/oc-pwnedpasswords-plugin), clone it into **plugins/luketowers/pwnedpasswords** and then run `composer update` from your project root in order to pull in the dependencies.

To install it with Composer, run `composer require luketowers/oc-pwnedpasswords-plugin` from your project root.

# Documentation

## Limiting by the number of times the password was pwned

You can limit rejected passwords to those that have been pwned a minimum number of times.
For example, `password` has been pwned 3,303,003 times, however `P@ssword!` has only been pwned 118 times.
If we wanted to block `password` but not `P@ssword!`, we can specify the minimum number as 150 like this:

```php
'password' => 'required|string|min:6|notpwned:150|confirmed',
```

## Enforce this rule on Backend authentication

To enforce this rule on the Backend authentication system, create a file at `config/luketowers/pwnedpasswords/config.php` and put the following in it:

```php
<?php return [

    /*
    |--------------------------------------------------------------------------
    | Enforce "notpwned" rule on backend users
    |--------------------------------------------------------------------------
    |
    | When this is enabled, the 'notpwned' validation rule is enforced on all
    | backend users by default. This means that they will not be able to update
    | their password to any password detected in a password breach, and if a
    | backend user tries to login with a "pwned" password, they will be rejected
    | and a password reset email will be sent to their email address for them to
    | change their password.
    */

    'enforceOnBackendUsers' => true,

];
```

## Overriding the validation message

To override the validation message, duplicate the plugin's `lang/en/lang.php` file to `project/lang/$locale/luketowers/pwnedpasswords/lang.php`.