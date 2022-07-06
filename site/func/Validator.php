<?php
/**
 * This class represents data validation layer.
 * 
 * @author Anatoly Khaytovich
 * 
 * Sample:
 * $arrFields = array(
 *      'field_name_1' => array(
 *           'value' => $intA,
 *           'validators' => array(
 *               'not_null',
 *               'is_numeric',
 *               'min_range' => $intMinValue,
 *               'max_range' => $intMaxValue,
 *           ),
 *       ),
 *       
 *       'field_name_2' => array(
 *           'value' => $strSomeString,
 *           'validators' => array(
 *               'not_null',
 *               'min_string_length'  => $intMinimalAmountOfCharacters,
 *               'max_string_length' => $intMaximalAmountOfCharacters,
 *           ),
 *       ),
 *       
 *       'field_name_3' => array(
 *           'value' => $strEmail,
 *           'validators' => array(
 *               'not_null',
 *               'min_string_lengh'  => $intMinimalAmountOfCharacters,
 *               'max_string_length' => $intMaximalAmountOfCharacters,
 *               'is_email',
 *           ),
 *       ),
 *       
 *       'field_name_4' => array(
 *           'value' => $strUrl,
 *           'validators' => array(
 *               'max_string_length' => $intMaximalAmountOfCharacters,
 *               'is_url',
 *           ),
 *       ),
 *       
 *       'field_name_5' => array(
 *           'value' => $someValue,
 *           'validators' => array(
 *               'not_null',
 *               'is_ip',
 *           ),
 *       ),
 *   );
 */
class Validator
{
    /**
     * Array, consists from fields(values) and their validation constrains.
     * 
     * @var array
     */
    private $arrFields;
    
    /**
     * Array, consists from error messages, or empty, if no constrains were violated.
     * 
     * @var array
     */
    private $arrErrors;
    
    /**
     * Constructor.
     * 
     * @param array $arrFields
     */
    public function __construct(array $arrFields)
    {
        $this->arrFields = is_array($arrFields) ? $arrFields : array();
        $this->arrErrors = array();
    }
    
    /**
     * Checks if a given value is null.
     * 
     * @param  mixed $value
     * @return bool
     */
    private function notNull($value)
    {
        return isset($value) && !is_null($value);
    }
    
    /**
     * Checks if given value is greater or equals to minimal range value.
     * 
     * @param  string $value
     * @param  mixed  $minRange
     * @return bool
     */
    private function minRange($value, $minRange)
    {
        if (is_float($minRange)) {
            return (float) $value >= $minRange;
        }
        return (int) $value >= $minRange;
    }
    
    /**
     * Checks if given value is smaller or equals to maximal range value.
     * 
     * @param  string $value
     * @param  mixed  $maxRange
     * @return bool
     */
    private function maxRange($value, $maxRange)
    {
        if (is_float($maxRange)) {
            return (float) $value <= $maxRange;
        }
        return (int) $value <= $maxRange;
    }
    
    /**
     * Checks if a length of the given string is greater or equals to minimal length.
     * 
     * @param  string $value
     * @param  int    $intMinLength
     * @return bool
     */
    private function minStringLength($value, $intMinLength)
    {
        if ($this->notNull($value)) {
            return strlen($value) >= $intMinLength;
        }
        return false;
    }
    
    /**
     * Checks if a length of the given string is smaller or equals to maximal length.
     * 
     * @param  string $value
     * @param  int    $intMaxLength
     * @return bool
     */
    private function maxStringLength($value, $intMaxLength)
    {
        if ($this->notNull($value)) {
            return strlen($value) <= $intMaxLength;
        }
        return false;
    }
    
    /**
     * Checks if given value is a valid email address.
     * 
     * @param  mixed $value
     * @return bool
     */
    private function isEmail($value)
    {
        if ($this->notNull($value)) {
            return false !== filter_var($value, FILTER_VALIDATE_EMAIL);
        }
        return false;
    }
    
    /**
     * Checks if given value is a valid URL.
     * 
     * @param  mixed $value
     * @return bool
     */
    private function isUrl($value)
    {
        if ($this->notNull($value)) {
            return false !== filter_var($value, FILTER_VALIDATE_URL);
        }
        return false;
    }
    
    /**
     * Checks if given value is a valid IP address.
     * 
     * @param  mixed $value
     * @return bool
     */
    private function isIp($value)
    {
        if ($this->notNull($value)) {
            return false !== filter_var($value, FILTER_VALIDATE_IP);
        }
        return false;
    }
    
    /**
     * Determines if given field is mandatory or not, 
     * and adds an appropriate error message to '$arrErrors'.
     * 
     * @param  string $strFieldName
     * @param  array  $arrValidationSet
     * @param  string $strErrorMessage
     * @return void
     */
    private function addErrorMessage($strFieldName, array $arrValidationSet, $strErrorMessage)
    {
        if (in_array('not_null', $arrValidationSet['validators'])) {
            // In case when given field is mandatory.
            if (!$this->notNull($arrValidationSet['value'])) {
                if (!in_array($strFieldName . ' value is missing', $this->arrErrors)) {
                    $this->arrErrors[$strFieldName] = $strFieldName . ' value is missing';
                }
                
            } elseif (!in_array($strFieldName . ' ' . $strErrorMessage, $this->arrErrors)) {
                $this->arrErrors[$strFieldName] = $strFieldName . ' ' . $strErrorMessage;
            }
            
        } elseif ($this->notNull($arrValidationSet['value'])) {
            // In case when given field is NOT mandatory.
            if (!in_array($strFieldName . ' ' . $strErrorMessage, $this->arrErrors)) {
                $this->arrErrors[$strFieldName] = $strFieldName . ' ' . $strErrorMessage;
            }
        }
    }
    
    /**
     * Returns an array of error messages.
     * This array will be empty, if no errors occurred.
     * 
     * @param  void
     * @return array
     */
    public function getErrorMessages()
    {
        return $this->arrErrors;
    }
    
    /**
     * Performs validation according to given constrains. 
     * 
     * @param  void
     * @return bool
     */
    public function isValid()
    {
        if (empty($this->arrFields)) {
            // Return true, if no values passed for validation.
            return true;
        }
        
        foreach ($this->arrFields as $strFieldName => $arrValidationSet) {
            if (!is_string($strFieldName)) {
                $this->arrErrors[$strFieldName] = $strFieldName . ' is invalid attribute name';
            }
            
            if (is_string($strFieldName) && is_numeric($strFieldName[0])) {
                $this->arrErrors[$strFieldName] = $strFieldName . ' is invalid attribute name';
            }
            
            foreach ($arrValidationSet['validators'] as $k => $v) {
                // Cut spaces before validation.
                $arrValidationSet['value'] = trim($arrValidationSet['value']);
                
                // Determines a type of current validator, and acts accordingly. 
                if (is_numeric($v)) {
                    switch ($k) {
                        case 'min_range':
                            if (!is_numeric($arrValidationSet['value'])) {
                                $this->addErrorMessage(
                                        $strFieldName, 
                                        $arrValidationSet, 
                                        'value is not a number'
                                        );
                                
                            } elseif (!$this->minRange($arrValidationSet['value'], $v)) {
                                $this->addErrorMessage(
                                        $strFieldName, 
                                        $arrValidationSet, 
                                        'value is smaller then ' . $v
                                        );
                            }
                            break;
                        
                        case 'max_range':
                            if (!is_numeric($arrValidationSet['value'])) {
                                $this->addErrorMessage(
                                        $strFieldName, 
                                        $arrValidationSet, 
                                        'value is not a number'
                                        );
                                
                            } elseif (!$this->maxRange($arrValidationSet['value'], $v)) {
                                $this->addErrorMessage(
                                        $strFieldName, 
                                        $arrValidationSet, 
                                        'value is greater then ' . $v
                                        );
                            }
                            break;
                            
                        case 'min_string_length':
                            if (!$this->minStringLength($arrValidationSet['value'], $v)) {
                                $this->addErrorMessage(
                                        $strFieldName, 
                                        $arrValidationSet, 
                                        'value is shorter then ' . $v
                                        );
                            }
                            break;
                            
                        case 'max_string_length':
                            if (!$this->maxStringLength($arrValidationSet['value'], $v)) {
                                $this->addErrorMessage(
                                        $strFieldName, 
                                        $arrValidationSet, 
                                        'value is longer then ' . $v
                                        );
                            }
                            break;
                    }
                    
                } else {
                    switch ($v) {
                        case 'not_null':
                            if (!$this->notNull($arrValidationSet['value'])) {
                                $this->arrErrors[$strFieldName] = $strFieldName . ' value is missing';
                            }
                            break;
                            
                        case 'is_ip':
                            if (!$this->isIp($arrValidationSet['value'])) {
                                $this->addErrorMessage(
                                        $strFieldName, 
                                        $arrValidationSet, 
                                        'value is not a valid IP'
                                        );
                            }
                            break;
                            
                        case 'is_email':
                            if (!$this->isEmail($arrValidationSet['value'])) {
                                $this->addErrorMessage(
                                        $strFieldName, 
                                        $arrValidationSet, 
                                        'value is not a valid email'
                                        );
                            }
                            break;
                            
                        case 'is_url':
                            if (!$this->isUrl($arrValidationSet['value'])) {
                                $this->addErrorMessage(
                                        $strFieldName, 
                                        $arrValidationSet, 
                                        'value is not a valid URL'
                                        );
                            }
                            break;
                            
                        case 'is_numeric':
                            if (!is_numeric($arrValidationSet['value'])) {
                                $this->addErrorMessage(
                                        $strFieldName, 
                                        $arrValidationSet, 
                                        'value is not a number'
                                        );
                            }
                            break;
                    }
                }
                unset($k, $v);
            }
            unset($strFieldName, $arrValidationSet);
        }
        
        return empty($this->arrErrors);
    }
}



