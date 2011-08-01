<?php
/**
 * File containing the NonInteractive class.
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

namespace Opensoft\Workflow\Execution;

use Opensoft\Workflow\Workflow;
use Opensoft\Workflow\Exception\ExecutionException;

/**
 * Workflow execution engine for non-interactive workflows.
 *
 * This workflow execution engine can only execute workflows that do not have
 * any Input and/or SubWorkflow nodes.
 *
 * @package Workflow
 * @version //autogen//
 */
class NonInteractive extends Execution
{

    public function setWorkflow(Workflow $workflow)
    {
        if ($workflow->isInteractive() || $workflow->hasSubWorkflows()) {
            throw new ExecutionException(
              'This executer can only execute workflows that have no Input and SubWorkflow nodes.'
            );
        }
        parent::setWorkflow($workflow);
    }

    /**
     * Start workflow execution.
     *
     * @param  integer $parentId
     */
    protected function doStart( $parentId )
    {
    }

    /**
     * Suspend workflow execution.
     */
    protected function doSuspend()
    {
    }

    /**
     * Resume workflow execution.
     */
    protected function doResume()
    {
    }

    /**
     * End workflow execution.
     */
    protected function doEnd()
    {
    }

    /**
     * Returns a new execution object for a sub workflow.
     *
     * @param  int $id
     * @return AbstractExecution
     */
    protected function doGetSubExecution( $id = null )
    {
    }
}

