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

];