<?php
/**
 * File containing the Listener class.
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
 * @access private
 */

namespace Opensoft\Workflow\Execution\Plugin;

use Opensoft\Workflow\Execution\ExecutionListenerInterface;
use Opensoft\Workflow\Util;
use Opensoft\Workflow\Nodes\Node;
use Opensoft\Workflow\Execution\Execution;

/**
 * Execution plugin that notifies ExecutionListenerInterface objects.
 *
 * @package Workflow
 * @version //autogen//
 * @access private
 */
class Listener extends ExecutionPlugin
{
    /**
     * Listeners.
     *
     * @var array
     */
    protected $listeners = array();

    /**
     * Adds a listener.
     *
     * @param ExecutionListenerInterface $listener
     * @return bool true when the listener was added, false otherwise.
     */
    public function addListener( ExecutionListenerInterface $listener )
    {
        if ( Util::findObject( $this->listeners, $listener ) !== false )
        {
            return false;
        }

        $this->listeners[] = $listener;

        return true;
    }

    /**
     * Removes a listener.
     *
     * @param ExecutionListenerInterface $listener
     * @return bool true when the listener was removed, false otherwise.
     */
    public function removeListener( ExecutionListenerInterface $listener )
    {
        $index = Util::findObject( $this->listeners, $listener );

        if ( $index === false )
        {
            return false;
        }

        unset( $this->listeners[$index] );

        return true;
    }

    /**
     * Notify listeners.
     *
     * @param string $message
     * @param int    $type
     */
    protected function notifyListeners( $message, $type = ExecutionListenerInterface::INFO )
    {
        foreach ( $this->listeners as $listener )
        {
            $listener->notify( $message, $type );
        }
    }

    /**
     * Called after an execution has been started.
     *
     * @param AbstractExecution $execution
     */
    public function afterExecutionStarted( Execution $execution )
    {
        $this->notifyListeners(
          sprintf(
            'Started execution #%d of workflow "%s" (version %d).',

            $execution->getId(),
            $execution->getWorkflow()->getName(),
            $execution->getWorkflow()->getVersion()
          )
        );
    }

    /**
     * Called after an execution has been suspended.
     *
     * @param AbstractExecution $execution
     */
    public function afterExecutionSuspended( Execution $execution )
    {
        $this->notifyListeners(
          sprintf(
            'Suspended execution #%d of workflow "%s" (version %d).',

            $execution->getId(),
            $execution->getWorkflow()->getName(),
            $execution->getWorkflow()->getVersion()
          )
        );
    }

    /**
     * Called after an execution has been resumed.
     *
     * @param AbstractExecution $execution
     */
    public function afterExecutionResumed( Execution $execution )
    {
        $this->notifyListeners(
          sprintf(
            'Resumed execution #%d of workflow "%s" (version %d).',

            $execution->getId(),
            $execution->getWorkflow()->getName(),
            $execution->getWorkflow()->getVersion()
          )
        );
    }

    /**
     * Called after an execution has been cancelled.
     *
     * @param AbstractExecution $execution
     */
    public function afterExecutionCancelled( Execution $execution )
    {
        $this->notifyListeners(
          sprintf(
            'Cancelled execution #%d of workflow "%s" (version %d).',

            $execution->getId(),
            $execution->getWorkflow()->getName(),
            $execution->getWorkflow()->getVersion()
          )
        );
    }

    /**
     * Called after an execution has successfully ended.
     *
     * @param AbstractExecution $execution
     */
    public function afterExecutionEnded( Execution $execution )
    {
        $this->notifyListeners(
          sprintf(
            'Ended execution #%d of workflow "%s" (version %d).',

            $execution->getId(),
            $execution->getWorkflow()->getName(),
            $execution->getWorkflow()->getVersion()
          )
        );
    }

    /**
     * Called after a node has been activated.
     *
     * @param AbstractExecution $execution
     * @param AbstractNode      $node
     */
    public function afterNodeActivated( Execution $execution, Node $node )
    {
        $this->notifyListeners(
          sprintf(
            'Activated node #%d(%s) for instance #%d of workflow "%s" (version %d).',

            $node->getId(),
            get_class( $node ),
            $execution->getId(),
            $execution->getWorkflow()->getName(),
            $execution->getWorkflow()->getVersion()
          ),
          ExecutionListenerInterface::DEBUG
        );
    }

    /**
     * Called after a node has been executed.
     *
     * @param AbstractExecution $execution
     * @param AbstractNode      $node
     */
    public function afterNodeExecuted( Execution $execution, Node $node )
    {
        $this->notifyListeners(
          sprintf(
            'Executed node #%d(%s) for instance #%d of workflow "%s" (version %d).',

            $node->getId(),
            get_class( $node ),
            $execution->getId(),
            $execution->getWorkflow()->getName(),
            $execution->getWorkflow()->getVersion()
          ),
          ExecutionListenerInterface::DEBUG
        );
    }

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
        $this->notifyListeners(
          sprintf(
            'Started thread #%d (%s%d sibling(s)) for execution #%d of workflow "%s" (version %d).',

            $threadId,
            $parentId != null ? 'parent: ' . $parentId . ', ' : '',
            $numSiblings,
            $execution->getId(),
            $execution->getWorkflow()->getName(),
            $execution->getWorkflow()->getVersion()
          ),
          ExecutionListenerInterface::DEBUG
        );
    }

    /**
     * Called after a thread has ended.
     *
     * @param AbstractExecution $execution
     * @param int                  $threadId
     */
    public function afterThreadEnded( Execution $execution, $threadId )
    {
        $this->notifyListeners(
          sprintf(
            'Ended thread #%d for execution #%d of workflow "%s" (version %d).',

            $threadId,
            $execution->getId(),
            $execution->getWorkflow()->getName(),
            $execution->getWorkflow()->getVersion()
          ),
          ExecutionListenerInterface::DEBUG
        );
    }

    /**
     * Called after a variable has been set.
     *
     * @param AbstractExecution $execution
     * @param string               $variableName
     * @param mixed                $value
     */
    public function afterVariableSet( Execution $execution, $variableName, $value )
    {
        $this->notifyListeners(
          sprintf(
            'Set variable "%s" to "%s" for execution #%d of workflow "%s" (version %d).',

            $variableName,
            Util::variableToString( $value ),
            $execution->getId(),
            $execution->getWorkflow()->getName(),
            $execution->getWorkflow()->getVersion()
          ),
          ExecutionListenerInterface::DEBUG
        );
    }

    /**
     * Called after a variable has been unset.
     *
     * @param AbstractExecution $execution
     * @param string               $variableName
     */
    public function afterVariableUnset( Execution $execution, $variableName )
    {
        $this->notifyListeners(
          sprintf(
            'Unset variable "%s" for execution #%d of workflow "%s" (version %d).',

            $variableName,
            $execution->getId(),
            $execution->getWorkflow()->getName(),
            $execution->getWorkflow()->getVersion()
          ),
          ExecutionListenerInterface::DEBUG
        );
    }
}

