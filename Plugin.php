<?php namespace LukeTowers\PwnedPasswords;

use Lang;
use Event;
use Flash;
use Config;
use Validator;
use BackendAuth;
use ValidationException;
use Backend\Models\User;
use Backend\Controllers\Auth;
use System\Classes\PluginBase;
use LukeTowers\PwnedPasswords\ValidationRules\NotPwned;

/**
 * PwnedPasswords Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Ensure the plugin is available on all routes
     */
    public $elevated = true;

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'luketowers.pwnedpasswords::lang.plugin.name',
            'description' => 'luketowers.pwnedpasswords::lang.plugin.description',
            'author'      => 'Luke Towers',
            'icon'        => 'icon-check',
            'homepage'    => 'https://github.com/LukeTowers/oc-pwnedpasswords-plugin',
        ];
    }

    /**
     * Runs right before the request route
     */
    public function boot()
    {
        Event::listen('translator.beforeResolve', function ($key, $replaces, $locale) {
            if ($key === 'validation.notpwned') {
                return Lang::get('luketowers.pwnedpasswords::lang.validation.notpwned');
            }
        });

        // Register the `notpwned:min` rule
        Validator::extend('notpwned', NotPwned::class);
        Validator::replacer('notpwned', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':min', array_shift($parameters) ?? 1, $message);
        });

        // Enforce rule on backend users if desired
        if (Config::get('luketowers.pwnedpasswords::enforceOnBackendUsers', false)) {
            User::extend(function($model) {
                $model->rules = array_merge($model->rules, ['password' => $model->rules['password'] . '|notpwned']);
            });

            // Force users to reset their passwords if they login with a pwned password
            Auth::extend(function ($controller) {
                $controller->bindEvent('page.beforeDisplay', function ($action, $params) use ($controller) {
                    if (post('postback') &&
                        ($action === 'signin' || $action === 'reset')
                    ) {
                        $validation = Validator::make(post(), ['password' => 'notpwned']);
                        if ($validation->fails()) {
                            // Force users to reset their password
                            if ($action === 'signin') {
                                Event::listen('backend.user.login', function ($user) use ($controller) {
                                    // Make sure the user is not authenticated
                                    BackendAuth::logout();

                                    // Send out the password reset email
                                    $response = $controller->restore_onSubmit();

                                    // Notify the user
                                    Flash::error("Your password has been detected in known password breaches and must be changed. An email with instructions to reset your password has been sent to your email address on file.");

                                    // Return the response
                                    abort($response);
                                });
                            }

                            // Ensure that they don't reset it to another terrible password
                            if ($action === 'reset') {
                                try {
                                    throw new ValidationException($validation);
                                } catch (ValidationException $ex) {
                                    // Notify the user & reload the page
                                    Flash::error($ex->getMessage());
                                    return redirect()->refresh();
                                }
                            }
                        }
                    }
                });
            });
        }
    }
}