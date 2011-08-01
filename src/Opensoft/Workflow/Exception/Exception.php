<?php
/**
 * File containing the WorkflowException class.
 *
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 *
 * @package Workflow
 * @version //autogen//
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace Opensoft\Workflow\Exception;

/**
 * General exception for the Workflow component.
 *
 * @package Workflow
 * @version //autogen//
 */
class Exception extends \RuntimeException
{
    public static function propertyReadOnly($property)
    {
        throw new self(sprintf("The property '%s' is read-only.", $property));
    }

    public static function propertyNotFound($property)
    {
        throw new self(sprintf("No such property name '%s'.", $property));
    }

    public static function propertyBaseValue( $settingName, $value, $expectedValue = null )
    {
        $type = gettype( $value );
        if ( in_array( $type, array( 'array', 'object', 'resource' ) ) )
        {
            $value = serialize( $value );
        }
        $msg = "The value '{$value}' that you were trying to assign to setting '{$settingName}' is invalid.";
        if ( $expectedValue )
        {
            $msg .= " Allowed values are: " . $expectedValue . ".";
        }
        throw new self($msg);
    }

    public static function directoryFileNotFound($file)
    {
        throw new self(sprintf("The directory file '$file' could not be found."));
    }

    public static function classNotFound($className)
    {
        throw new self(sprintf('Class "%s" not found.', $className));
    }
}

