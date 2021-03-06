<?php
/**
 * File containing the SubWorkflow class.
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

namespace Opensoft\Workflow\Nodes;

use \Opensoft\Workflow\Execution\Execution;
use \Opensoft\Workflow\Exception\ExecutionException;

/**
 * An object of the SubWorkflow class represents a sub-workflow.
 *
 * When the node is reached during execution of the workflow, the specified sub-workflow
 * is started. The original workflow is suspended until the sub-workflow has finished executing.
 *
 * Incoming nodes: 1
 * Outgoing nodes: 1
 *
 * The example below creates a sub-workflow node that passes the parent
 * execution's variable 'x' to the variable 'y' in the child execution when the
 * sub-workflow is started. When it ends, the child execution's 'y' variable is
 * passed to the parent execution as 'z'.
 *
 * <code>
 * <?php
 * $subWorkflow = new SubWorkflow(
 *   array(
 *     'workflow'  => 'IncrementVariable',
 *     'variables' => array(
 *       'in' => array(
 *         'x' => 'y'
 *       ),
 *       'out' => array(
 *         'y' => 'z'
 *       )
 *     )
 *   )
 * );
 * ?>
 * </code>
 *
 * @package Workflow
 * @version //autogen//
 */
class SubWorkflow extends Node
{
    /**
     * Execution ID of the sub workflow,
     * 0 if it has not been started yet.
     *
     * @var integer
     */
    protected $state = 0;

    /**
     * Constructs a new sub workflow with the configuration $configuration.
     *
     * Configuration format
     * <ul>
     * <li>
     *   <b>String:</b>
     *   The name of the workflow to execute. The workflow is loaded using the
     *   loadByName method on the execution engine.
     * </li>
     *
     * <li>
     *   <b>Array:</b>
     *   <ul>
     *     <li><i>workflow:</i> The name of the workflow to execute. The workflow
     *     is loaded using the loadByName method on the execution engine.</li>
     *     <li><i>variables:</i> An array with the information for mapping
     *     workflow variables between parent and child workflow execution.</li>
     *   </ul>
     * <li>
     * </ul>
     *
     * @param mixed $configuration
     */
    public function __construct( $configuration )
    {
        if ( is_string( $configuration ) )
        {
            $configuration = array( 'workflow' => $configuration );
        }

        if ( !isset( $configuration['variables'] ) )
        {
            $configuration['variables'] = array(
              'in' => array(), 'out' => array()
            );
        }

        parent::__construct( $configuration );
    }

    /**
     * Executes this node.
     *
     * @param AbstractExecution $execution
     * @return boolean true when the node finished execution,
     *                 and false otherwise
     * @ignore
     */
    public function execute(Execution $execution)
    {
        if ( $execution->getDefinitionStorage() === null )
        {
            throw new ExecutionException(
              'No DefinitionStorageInterface implementation available.'
            );
        }

        $workflow = $execution->getDefinitionStorage()->loadByName( $this->configuration['workflow'] );

        // Sub Workflow is not interactive.
        if ( !$workflow->isInteractive() && !$workflow->hasSubWorkflows() )
        {
            $subExecution = $execution->getSubExecution( null, false );
            $subExecution->setWorkflow($workflow);

            $this->passVariables(
              $execution, $subExecution, $this->configuration['variables']['in']
            );

            $subExecution->start();
        }
        // Sub Workflow is interactive.
        else
        {
            // Sub Workflow is to be started.
            if ( $this->state == 0 )
            {
                $subExecution = $execution->getSubExecution();
                $subExecution->setWorkflow($workflow);

                $this->passVariables(
                  $execution, $subExecution, $this->configuration['variables']['in']
                );

                $subExecution->start( $execution->getId() );

                $this->state = $subExecution->getId();
            }
            // Sub Workflow is to be resumed.
            else
            {
                $subExecution = $execution->getSubExecution( $this->state );
                $subExecution->setWorkflow($workflow);
                $subExecution->resume( $execution->getVariables() );
            }
        }

        // Execution of Sub Workflow was cancelled.
        if ( $subExecution->isCancelled() )
        {
            $execution->cancel( $this );
        }

        // Execution of Sub Workflow has ended.
        if ( $subExecution->hasEnded() )
        {
            $this->passVariables(
              $subExecution, $execution, $this->configuration['variables']['out']
            );

            $this->activateNode( $execution, $this->outNodes[0] );

            $this->state = 0;

            return parent::execute( $execution );
        }

        // Execution of Sub Workflow has been suspended.
        foreach ( $subExecution->getWaitingFor() as $variableName => $data )
        {
            $execution->addWaitingFor( $this, $variableName, $data['condition'] );
        }

        return false;
    }

    /**
     * Generate node configuration from XML representation.
     *
     * @param DOMElement $element
     * @return array
     * @ignore
     */
    public static function configurationFromXML( \DOMElement $element )
    {
        $configuration = array(
          'workflow'  => $element->getAttribute( 'subWorkflowName' ),
          'variables' => array(
            'in' => array(), 'out' => array()
          )
        );

        $xpath = new \DOMXPath( $element->ownerDocument );
        $in    = $xpath->query( 'in/variable', $element );
        $out   = $xpath->query( 'out/variable', $element );

        foreach ( $in as $variable )
        {
            $configuration['variables']['in'][$variable->getAttribute( 'name' )] = $variable->getAttribute( 'as' );
        }

        foreach ( $out as $variable )
        {
            $configuration['variables']['out'][$variable->getAttribute( 'name' )] = $variable->getAttribute( 'as' );
        }

        return $configuration;
    }

    /**
     * Generate XML representation of this node's configuration.
     *
     * @param DOMElement $element
     * @ignore
     */
    public function configurationToXML( \DOMElement $element )
    {
        $element->setAttribute( 'subWorkflowName', $this->configuration['workflow'] );

        if ( !empty( $this->configuration['variables']['in'] ) )
        {
            $in = $element->appendChild(
              $element->ownerDocument->createElement( 'in' )
            );

            foreach ( $this->configuration['variables']['in'] as $fromName => $toName )
            {
                $variable = $in->appendChild(
                  $in->ownerDocument->createElement( 'variable' )
                );

                $variable->setAttribute( 'name', $fromName );
                $variable->setAttribute( 'as', $toName );
            }
        }

        if ( !empty( $this->configuration['variables']['out'] ) )
        {
            $out = $element->appendChild(
              $element->ownerDocument->createElement( 'out' )
            );

            foreach ( $this->configuration['variables']['out'] as $fromName => $toName )
            {
                $variable = $out->appendChild(
                  $out->ownerDocument->createElement( 'variable' )
                );

                $variable->setAttribute( 'name', $fromName );
                $variable->setAttribute( 'as', $toName );
            }
        }
    }

    /**
     * Returns a textual representation of this node.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        return 'Sub Workflow: ' . $this->configuration['workflow'];
    }

    /**
     * Passes variables from one execution context to another.
     *
     * @param  AbstractExecution $from The execution context the variables are passed from.
     * @param  AbstractExecution $to The execution context the variables are passed to.
     * @param  array                $variables The names of the variables.
     * @throws ExecutionException if a variable that is to be passed does not exist.
     * @ignore
     */
    protected function passVariables(Execution $from, Execution $to, array $variables )
    {
        foreach ( $variables as $fromName => $toName )
        {
            $to->setVariable( $toName, $from->getVariable( $fromName ) );
        }
    }
}

