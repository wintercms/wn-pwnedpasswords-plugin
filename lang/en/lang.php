<?php return [
    'plugin' => [
        'name'        => 'PwnedPasswords',
        'description' => 'Adds the notpwned validation rule to check passwords against the HIBP Pwned Passwords service',
    ],

    'validation' => [
        'notpwned' => 'The :attribute is insufficiently secure as it has been found at least :min times in known password breaches, please choose a different one.',
    ],
];