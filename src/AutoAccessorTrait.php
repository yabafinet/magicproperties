<?php

namespace MagicProperties;

use MagicProperties\Exceptions\InvalidPropertyCallException;
use Prophecy\Exception\Doubler\MethodNotFoundException;

/**
 * Allow access to a getter implicitly
 */
trait AutoAccessorTrait
{
    /**
     * The properties that gonna be resolved
     * via the __get magic method
     *
     * @var array
     */
    protected $gettables = [];

    /**
     * Get the value of a gettable property
     *
     * @param string $prop
     * @return mixed
     * @throws InvalidPropertyCallException
     */
    final public function __get($prop)
    {
        if (!property_exists(__CLASS__, $prop)) {
            throw new InvalidPropertyCallException(
                "You're trying to access to undefined property {$prop}.",
                InvalidPropertyCallException::UNDEFINED_PROPERTY
            );
        }

        if (in_array($prop, $this->gettables) || $this->isImplementedMethodGettable($prop)) {
            return $this->callGetter($prop);
        }

        throw new InvalidPropertyCallException(
            "Property {$prop} is not accessible out of the class.",
            InvalidPropertyCallException::NOT_ACCESSABLE_PROPERTY
        );
    }

    /**
     * Call the defined getter for a gettable
     * property if there's not defined a getter,
     * get the value directly
     *
     * @param  string $prop
     * @return mixed
     */
    private function callGetter($prop)
    {
        if (method_exists(__CLASS__, toCamelCase($prop, 'get'))) {
            return call_user_func([__CLASS__, toCamelCase($prop, 'get')]);
        } elseif (method_exists(__CLASS__, toSnakeCase($prop, 'get'))) {
            return call_user_func([__CLASS__, toSnakeCase($prop, 'get')]);
        } else {
            throw new MethodNotFoundException("",__CLASS__,toCamelCase($prop, 'get'));
        }
        
        return $this->$prop;
    }

    /**
     * @param $prop
     * @return mixed
     */
    private function isImplementedMethodGettable($prop)
    {
        if (method_exists(__CLASS__, toCamelCase($prop, 'get')))
            return true;
        else
            return false;
    }



}
