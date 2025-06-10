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

    'enforceOnBackendUsers' => false,

    /*
    |--------------------------------------------------------------------------
    | Enable logging for rejected backend logins
    |--------------------------------------------------------------------------
    |
    | When enabled, any login attempt by a backend user with a compromised
    | (pwned) password will trigger a log entry via the system logger. This
    | is useful for auditing and monitoring potential security issues.
    */

    'enableRejectionLog' => false

];