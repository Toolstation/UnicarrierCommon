<?php
/**
 * Helper class
 * Based on the Helper class from Omnipay/Common
 */

namespace UniCarrier\Common;

/**
 * Helper class
 *
 * This class defines various static utility functions that are in use
 * throughout the UniCarrier system.
 */
class Helper
{
    /**
     * Convert a string to camelCase. Strings already in camelCase will not be harmed.
     *
     * @param  string  $str The input string
     * @return string camelCased output string
     */
    public static function camelCase($str)
    {
        return preg_replace_callback(
            '/_([a-z])/',
            function ($match) {
                return strtoupper($match[1]);
            },
            $str
        );
    }

    /**
     * Initialize an object with a given array of parameters
     *
     * Parameters are automatically converted to camelCase. Any parameters which do
     * not match a setter on the target object are ignored.
     *
     * @param mixed $target     The object to set parameters on
     * @param array $parameters An array of parameters to set
     *
     * @return bool
     */
    public static function initialise($target, array $parameters)
    {
        if (is_array($parameters)) {
            foreach ($parameters as $key => $value) {
                $method = 'set' . ucfirst(static::camelCase($key));
                if (method_exists($target, $method)) {
                    $target->$method($value);
                }
            }
            return true;
        }
    } //@codeCoverageIgnore

    /**
     * Returns true if all elements in $requiredParameters have a getter and this returns a non null value.
     *
     * @param $target
     * @param array $requiredParameters
     * @return array
     */
    public static function checkRequiredParametersSet($target, array $requiredParameters) : array
    {
        $unsetParameters = [];

        foreach ($requiredParameters as $requiredParameter) {
            $method = 'get' . ucfirst(static::camelCase($requiredParameter));
            if (method_exists($target, $method)) {
                if ($target->$method() === null) {
                    $unsetParameters[] = $requiredParameter;
                }
            } else {
                $unsetParameters[] = $requiredParameter;
            }
        }

        return $unsetParameters;
    }

    /**
     * Resolve a carrier class to a short name.
     *
     * The short name can be used with CarrierFactory as an alias of the carrier class,
     * to create new instances of a carrier.
     *
     * @param string $className
     *
     * @return string
     */
    public static function getCarrierShortName($className)
    {
        if (0 === strpos($className, '\\')) {
            $className = substr($className, 1);
        }

        if (0 === strpos($className, 'UniCarrier\\')) {
            return trim(str_replace('\\', '_', substr($className, 11, -8)), '_');
        }

        return '\\'.$className;
    }

    /**
     * Resolve a short carrier name to a full namespaced carrier class.
     *
     * Class names beginning with a namespace marker (\) are left intact.
     * Non-namespaced classes are expected to be in the \UniCarrier namespace, e.g.:
     *
     *      \Custom\Carrier     => \Custom\Carrier
     *      \Custom_Carrier     => \Custom_Carrier
     *      Stripe              => \UniCarrier\Stripe\Carrier
     *
     * @param  string  $shortName The short carrier name
     * @return string  The fully namespaced carrier class name
     */
    public static function getCarrierClassName($shortName)
    {
        if (0 === strpos($shortName, '\\')) {
            return $shortName;
        }

        // replace underscores with namespace marker, PSR-0 style
        $shortName = str_replace('_', '\\', $shortName);
        if (false === strpos($shortName, '\\')) {
            $shortName .= '\\';
        }

        return '\\UniCarrier\\'.$shortName.'Carrier';
    }

    /**
     * get the namespace of the supplied class.
     *
     * @param $class
     *
     * @return string
     */
    public static function getNamespace($class)
    {
        $className = explode('\\', get_class($class));
        array_pop($className);
        $nameSpace = implode('\\', $className);
        return $nameSpace;
    }
}
