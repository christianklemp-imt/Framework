<?php
/**
 * Webiny Framework (http://www.webiny.com/framework)
 *
 * @copyright Copyright Webiny LTD
 */

namespace Webiny\Component\Rest\Parser;

use Webiny\Component\Config\ConfigObject;

/**
 * Parameter parser parses the parameters on a api method.
 * Unlike MethodParser, which parses only a single method, the ParameterParser class parses all the parameters on a
 * single method.
 *
 * @package         Webiny\Component\Rest\Parser
 */
class ParameterParser
{
    /**
     * @var array List of parameters that should be parsed.
     */
    private $paramList;

    /**
     * @var ConfigObject List of phpDoc annotations describing parameters.
     */
    private $paramAnnotations;

    /**
     * @var array List of default parameter type.
     */
    protected static $paramTypes = [
        'integer' => [
            'integer',
            'int'
        ],
        'string'  => [
            'string',
            'str'
        ],
        'float'   => [
            'float',
            'decimal'
        ],
        'bool'    => [
            'boolean',
            'bool'
        ]
    ];


    /**
     * Base constructor.
     *
     * @param array        $paramList
     * @param ConfigObject $paramAnnotations
     */
    public function __construct($paramList, $paramAnnotations)
    {
        $this->paramList = $paramList;
        $this->paramAnnotations = (!is_object($paramAnnotations
        )) ? new ConfigObject([$paramAnnotations]) : $paramAnnotations;
    }

    /**
     * Parses all the parameters and return an array of ParsedParameter instances.
     *
     * @return array Array of ParsedParameter instances.
     */
    public function parse()
    {
        if (!is_array($this->paramList)) {
            return [];
        }

        $parsedParams = [];
        foreach ($this->paramList as $p) {
            $param = new ParsedParameter();
            $param->name = $p->name;
            $param->type = $this->getType($p->name, $this->paramAnnotations);
            try {
                $param->default = $p->getDefaultValue();
                if (is_bool($param->default)) {
                    $param->default = ($param->default===false) ? '0' : '1';
                }
                $param->required = false;
            } catch (\Exception $e) {
                $param->default = null;
                $param->required = true;
            }

            $parsedParams[] = $param;
        }

        return $parsedParams;
    }

    /**
     * Tries to detect the type of a parameter based on its phpDoc block.
     * If type is not detected, "string" is returned as the default type.
     *
     * @param string       $pName       Name of the parameter.
     * @param ConfigObject $annotations Parameter annotations.
     *
     * @return string Name of the parameter type, based on keys in static::$paramTypes.
     */
    private function getType($pName, $annotations)
    {
        $type = 'string';
        foreach ($annotations as $a) {
            if (strpos($a, '$' . $pName)) {
                foreach (self::$paramTypes as $typeKey => $typeList) {
                    foreach ($typeList as $t) {
                        if (strpos(trim($a), $t) === 0) {
                            $type = $typeKey;
                        }
                    }
                }
            }
        }

        return $type;
    }
}