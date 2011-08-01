<?php
/**
 * File containing the Not class.
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
 * Boolean NOT.
 *
 * An object of the Not decorates an ConditionInterface object
 * and negates its expression.
 *
 * <code>
 * <?php
 * $notNondition = new Not( $condition ) ;
 * ?>
 * </code>
 *
 * @package Workflow
 * @version //autogen//
 */
class Not implements ConditionInterface
{
    /**
     * Holds the expression to negate.
     *
     * @var ConditionInterface
     */
    protected $condition;

    /**
     * Constructs a new not condition on $condition.
     *
     * @param  ConditionInterface $condition
     */
    public function __construct( ConditionInterface $condition )
    {
        $this->condition = $condition;
    }

    /**
     * Evaluates this condition with the value $value and returns true if the condition holds.
     *
     * If the condition does not hold false is returned.
     *
     * @param  mixed $value
     * @return boolean true when the condition holds, false otherwise.
     * @ignore
     */
    public function evaluate( $value )
    {
        return !$this->condition->evaluate( $value );
    }

    /**
     * Returns the condition that is negated.
     *
     * @return ConditionInterface
     * @ignore
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Returns a textual representation of this condition.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        return '! ' . $this->condition;
    }
}

