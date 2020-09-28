<?php namespace LukeTowers\PwnedPasswords\ValidationRules;

use Lang;
use Cache;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Validation\Rule;

class NotPwned implements Rule
{
    /**
     * @var int The minimum number of times the password has to have been pwned before it is invalid. Default: 1
     */
    private $minimum = 1;

    /**
     * Constructor
     *
     * @param int $minimum Minimum number of times the password was pwned before it is blocked
     */
    public function __construct($minimum = 1)
    {
        $this->minimum = $minimum;
    }

    /**
     * Validate the provided value
     *
     * @param string $attribute The attribute being tested
     * @param string $value The value being tested
     * @param array $params The parameters passed to the rule
     * @return bool
     */
    public function validate($attribute, $value, $params)
    {
        $this->minimum = array_shift($params) ?? 1;

        return $this->passes($attribute, $value);
    }

    /**
     * Checks to see if the provided value passes the rule
     *
     * @param string $attribute The attribute being tested
     * @param string $value The value being tested
     * @return bool $passes
     */
    public function passes($attribute, $value)
    {
        list($prefix, $suffix) = $this->hashAndSplit($value);
        $results = $this->query($prefix);
        $count = $results[$suffix] ?? 0;

        return $count < $this->minimum;
    }

    /**
     * Returns the validation failed message
     *
     * @return string
     */
    public function message()
    {
        return Lang::get('luketowers.pwnedpasswords::lang.validation.notpwned');
    }

    /**
     * Hashes the provided value and splits for use by the validation check
     *
     * @param string $value The provided value
     * @return array [$prefix, $suffix]
     */
    private function hashAndSplit($value)
    {
        $hash = strtoupper(sha1($value));
        $prefix = substr($hash, 0, 5);
        $suffix = substr($hash, 5);

        return [$prefix, $suffix];
    }

    /**
     * Query the HIBP Pwned Passwords API using k-anonymity
     *
     * @param string $prefix The password hash five-character prefix
     * @return array [$suffix => $count]
     */
    private function query($prefix)
    {
        // Cache results for a week, to avoid constant API calls for identical prefixes
        return Cache::remember('pwned:'.$prefix, now()->addWeeks(1), function () use ($prefix) {
            $curl = curl_init('https://api.pwnedpasswords.com/range/'.$prefix);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $results = curl_exec($curl);
            curl_close($curl);

            return (new Collection(explode("\n", $results)))
                ->mapWithKeys(function ($value) {
                    list($suffix, $count) = explode(':', trim($value));
                    return [$suffix => $count];
                });
        });
    }
}