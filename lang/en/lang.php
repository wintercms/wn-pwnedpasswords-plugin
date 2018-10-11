<?php return [
    'plugin' => [
        'name'        => 'PwnedPasswords',
        'description' => 'Validation rule for checking values against the HIBP PwnedPasswords service',
    ],

    'validation' => [
        'notpwned' => 'The :attribute is insufficiently secure as it has been found at least :min times in known password breaches, please choose a different one.',
    ],
];