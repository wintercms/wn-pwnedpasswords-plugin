<?php namespace LukeTowers\PwnedPasswords;

use Lang;
use Event;
use Illuminate\Support\Facades\Validator;
use System\Classes\PluginBase;
use LukeTowers\PwnedPasswords\ValidationRules\NotPwned;

/**
 * PwnedPasswords Plugin Information File
 */
class Plugin extends PluginBase
{
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
    }
}