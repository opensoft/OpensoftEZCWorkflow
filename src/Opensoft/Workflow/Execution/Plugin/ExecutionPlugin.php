<?php
/**
 * File containing the AbstractExecutionPlugin class.
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

namespace Opensoft\Workflow\Execution\Plugin;

use Opensoft\Workflow\Execution\Execution;
use Opensoft\Workflow\Nodes\Node;

/**
 * Abstract base class for workflow execution engine plugins.
 *
 * @package Workflow
 * @version //autogen//
 */
abstract class ExecutionPlugin
{
    /**
     * Called after an execution has been started.
     *
     * @param AbstractExecution $execution
     */
    public function afterExecutionStarted( Execution $execution )
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called after an execution has been suspended.
     *
     * @param AbstractExecution $execution
     */
    public function afterExecutionSuspended( Execution $execution )
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called after an execution has been resumed.
     *
     * @param AbstractExecution $execution
     */
    public function afterExecutionResumed( Execution $execution )
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called after an execution has been cancelled.
     *
     * @param AbstractExecution $execution
     */
    public function afterExecutionCancelled( Execution $execution )
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called after an execution has successfully ended.
     *
     * @param AbstractExecution $execution
     */
    public function afterExecutionEnded( Execution $execution )
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called before a node is activated.
     *
     * @param AbstractExecution $execution
     * @param AbstractNode      $node
     * @return bool true, when the node should be activated, false otherwise
     */
    public function beforeNodeActivated( Execution $execution, Node $node )
    {
    // @codeCoverageIgnoreStart
        return true;
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called after a node has been activated.
     *
     * @param AbstractExecution $execution
     * @param AbstractNode      $node
     */
    public function afterNodeActivated( Execution $execution, Node $node )
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called after a node has been executed.
     *
     * @param AbstractExecution $execution
     * @param AbstractNode      $node
     */
    public function afterNodeExecuted( Execution $execution, Node $node )
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called after a new thread has been started.
     *
     * @param AbstractExecution $execution
     * @param int                  $threadId
     * @param int                  $parentId
     * @param int                  $numSiblings
     */
    public function afterThreadStarted( Execution $execution, $threadId, $parentId, $numSiblings )
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called after a thread has ended.
     *
     * @param AbstractExecution $execution
     * @param int                  $threadId
     */
    public function afterThreadEnded( Execution $execution, $threadId )
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called before a variable is set.
     *
     * @param  AbstractExecution $execution
     * @param  string               $variableName
     * @param  mixed                $value
     * @return mixed the value the variable should be set to
     */
    public function beforeVariableSet( Execution $execution, $variableName, $value )
    {
    // @codeCoverageIgnoreStart
        return $value;
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called after a variable has been set.
     *
     * @param AbstractExecution $execution
     * @param string               $variableName
     * @param mixed                $value
     */
    public function afterVariableSet( Execution $execution, $variableName, $value )
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called before a variable is unset.
     *
     * @param  AbstractExecution $execution
     * @param  string               $variableName
     * @return bool true, when the variable should be unset, false otherwise
     */
    public function beforeVariableUnset( Execution $execution, $variableName )
    {
    // @codeCoverageIgnoreStart
        return true;
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called after a variable has been unset.
     *
     * @param AbstractExecution $execution
     * @param string               $variableName
     */
    public function afterVariableUnset( Execution $execution, $variableName )
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd
}

