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
    'password' => 'required|string|min:6|pwned:150|confirmed',
```

## Using with the Backend User model

To use with the Backend user model, add the following to the boot() method of your plugin:

```php
    \Backend\Models\User::extend(function($model) {
        $model->rules = array_merge($model->rules, ['password' => $model->rules['password'] . '|notpwned']);
    });
```

## Overriding the validation message

To override the validation message, duplicate the plugin's `lang/en/lang.php` file to `project/lang/$locale/luketowers/pwnedpasswords/lang.php`.