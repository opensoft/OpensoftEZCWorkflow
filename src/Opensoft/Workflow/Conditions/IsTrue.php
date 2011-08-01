<?php
/**
 * File containing the IsTrue class.
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
 * Workflow condition that evaluates to true if the provided input is true.
 *
 * Typically used together with Variable to use the
 * condition on a workflow variable.
 *
 * <code>
 * <?php
 * $condition = new Variable(
 *   'variable name',
 *   new IsTrue
 * );
 * ?>
 * </code>
 *
 * @package Workflow
 * @version //autogen//
 */
class IsTrue implements ConditionInterface
{
    /**
     * Evaluates this condition with $value and returns true if it is true and false if it is not.
     *
     * @param  mixed $value
     * @return boolean true when the condition holds, false otherwise.
     * @ignore
     */
    public function evaluate( $value )
    {
        return $value === true;
    }

    /**
     * Returns a textual representation of this condition.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        return 'is true';
    }
}

