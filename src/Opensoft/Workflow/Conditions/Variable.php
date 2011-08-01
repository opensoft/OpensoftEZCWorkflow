<?php
/**
 * File containing the Variable class.
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
 * Wrapper that applies a condition to a workflow variable.
 *
 * @package Workflow
 * @version //autogen//
 */
class Variable implements ConditionInterface
{
    /**
     * The name of the variable the condition is applied to.
     *
     * @var string
     */
    protected $variableName;

    /**
     * The condition that is applied to the variable.
     *
     * @var ConditionInterface
     */
    protected $condition;

    /**
     * Constructor.
     *
     * @param  string $variableName
     * @param  ConditionInterface $condition
     */
    public function __construct( $variableName, ConditionInterface $condition )
    {
        $this->variableName = $variableName;
        $this->condition    = $condition;
    }

    /**
     * Evaluates this condition.
     *
     * @param  mixed $value
     * @return boolean true when the condition holds, false otherwise.
     * @ignore
     */
    public function evaluate( $value )
    {
        if ( is_array( $value ) && isset( $value[$this->variableName] ) )
        {
            return $this->condition->evaluate( $value[$this->variableName] );
        }
        else
        {
            return false;
        }
    }

    /**
     * Returns a textual representation of this condition.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        return $this->variableName . ' ' . $this->condition;
    }

    /**
     * Returns the name of the variable the condition is evaluated for.
     *
     * @return string
     * @ignore
     */
    public function getVariableName()
    {
        return $this->variableName;
    }

    /**
     * Returns the condition.
     *
     * @return ConditionInterface
     * @ignore
     */
    public function getCondition()
    {
        return $this->condition;
    }
}

