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

## Using with the Backend User model

To use with the Backend user model, add the following to the boot() method of your plugin:

```php
\Backend\Models\User::extend(function($model) {
    $model->rules = array_merge($model->rules, ['password' => $model->rules['password'] . '|notpwned']);
});
```

## Force existing users to reset their passwords

To force existing users to reset their passwords on their next login, add the following to the boot() method of your plugin (ensure that `$elevated = true` is also set):

```php
// Force users to reset their passwords if they login with a pwned password
\Backend\Controllers\Auth::extend(function ($controller) {
    $controller->bindEvent('page.beforeDisplay', function ($action, $params) use ($controller) {
        switch ($action) {
            case 'signin':
            case 'reset':
                if (post('postback')) {
                    $validation = \Validator::make(post(), ['password' => 'notpwned']);
                    if ($validation->fails()) {
                        // Force users to reset their password
                        if ($action === 'signin') {
                            // Only generate the reset code after the user has been successfully authenticated
                            // otherwise anyone attempting to login with a weak password would automatically get
                            // to use the password reset functionality to reset the user's password.
                            \Event::listen('backend.user.login', function ($user) {
                                // Flash doesn't work since we're returning a crude redirect here // Flash::error("You must reset your password.");
                                header('Location: ' . \Backend::url('backend/auth/reset/'.$user->id.'/'.$user->getResetPasswordCode()));
                                die();
                            });
                        }
                        
                        // Ensure that they don't reset it to another terrible password
                        if ($action === 'reset') {
                            try {
                                throw new \ValidationException($validation);
                            } catch (\ValidationException $ex) {
                                // Make this as clean of a request as possible so we don't cause an infinite loop
                                request()->replace([]);
                                request()->request->replace([]);
                                $_POST = [];

                                // Flash the error message
                                \Flash::error($ex->getMessage());

                                // Return the reset action ready for the user to try again
                                return $controller->run('reset', $params);
                            }
                        }
                    }
                }
                break;
        }
    });
});
```

## Overriding the validation message

To override the validation message, duplicate the plugin's `lang/en/lang.php` file to `project/lang/$locale/luketowers/pwnedpasswords/lang.php`.