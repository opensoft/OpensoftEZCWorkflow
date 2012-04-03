<?php
/**
 * File containing the IsBetween class.
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

namespace Opensoft\Workflow\Conditions;

/**
 * Condition that checks if a value is between given range.
 *
 * Typically used together with Variable to use the
 * condition on a workflow variable.
 *
 * <code>
 * <?php
 * $min = 1;
 * $max = 10;
 *
 * $condition = new Variable(
 *   'variable name',
 *   new IsBetween(array($min, $max))
 * );
 * ?>
 * </code>
 *
 * @package Workflow
 * @version //autogen//
 */
class IsBetween extends Comparison
{
    /**
     * Textual representation of the comparison operator.
     *
     * @var mixed
     */
    protected $operator = 'BETWEEN';

    /**
     * Evaluates this condition with $value and returns true if it is between given boundaries.
     *
     * @param  array $value
     * @return boolean true when the condition holds, false otherwise.
     * @ignore
     */
    public function evaluate($value)
    {
        list($min,$max) = $this->value;
        return $value >= $min && $value <= $max;
    }

    /**
     * Returns a textual representation of this condition.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        return $this->operator.' '.implode(' AND ', $this->value);
    }
}

