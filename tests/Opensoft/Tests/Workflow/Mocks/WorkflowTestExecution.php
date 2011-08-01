<?php
/**
 * File containing the ezcWorkflowTestExecution class.
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

namespace Opensoft\Tests\Workflow\Mocks;

use Opensoft\Workflow\Execution\NonInteractive;
use Opensoft\Workflow\Workflow;

/**
 * Workflow execution engine for testing workflows.
 *
 * @package Workflow
 * @subpackage Tests
 * @version //autogen//
 */
class WorkflowTestExecution extends NonInteractive
{
    /**
     * Execution ID.
     *
     * @var integer
     */
    protected $id = 0;

    /**
     * @var array
     */
    protected $inputVariables = array();

    /**
     * @var array
     */
    protected $inputVariablesForSubWorkflow = array();

    /**
     * Sets an input variable.
     *
     * @param  string $name
     * @param  mixed  $value
     */
    public function setInputVariable( $name, $value )
    {
        $this->inputVariables[$name] = $value;
    }

    /**
     * Sets an input variable for a sub workflow.
     *
     * @param  string $name
     * @param  mixed  $value
     */
    public function setInputVariableForSubWorkflow( $name, $value )
    {
        $this->inputVariablesForSubWorkflow[$name] = $value;
    }

    /**
     * @param \Opensoft\Workflow\Workflow $workflow
     * @return void
     */
    public function setWorkflow(Workflow $workflow)
    {
        $this->workflow = $workflow;
    }

    /**
     * Suspend workflow execution.
     */
    public function suspend()
    {
        parent::suspend();

        \PHPUnit_Framework_Assert::assertFalse( $this->hasEnded() );
        \PHPUnit_Framework_Assert::assertFalse( $this->isResumed() );
        \PHPUnit_Framework_Assert::assertTrue( $this->isSuspended() );

        $inputData  = array();
        $waitingFor = $this->getWaitingFor();

        foreach ( $this->inputVariables as $name => $value )
        {
            if ( isset( $waitingFor[$name] ) )
            {
                $inputData[$name] = $value;
            }
        }

        if ( empty( $inputData ) )
        {
            throw new \Opensoft\Workflow\Exception\ExecutionException(
              'Workflow is waiting for input data that has not been mocked.'
            );
        }

        $this->resume( $inputData );
    }

    /**
     * Returns a new execution object for a sub workflow.
     *
     * @param  int $id
     * @return ezcWorkflowExecution
     */
    protected function doGetSubExecution( $id = NULL )
    {
        parent::doGetSubExecution( $id );

        $execution = new WorkflowTestExecution( $id );

        foreach ( $this->inputVariablesForSubWorkflow as $name => $value )
        {
            $execution->setInputVariable( $name, $value );
        }

        if ( $id !== NULL )
        {
            $execution->resume();
        }

        return $execution;
    }
}