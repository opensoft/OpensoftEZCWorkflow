<?php
/**
 * File containing the Visualizer class.
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
use Opensoft\Workflow\Visitors\Visualization;
use Opensoft\Workflow\Exception\Exception as WorkflowException;

/**
 * Execution plugin that visualizes the execution.
 *
 * <code>
 * <?php
 * $db         = ezcDbFactory::create( 'mysql://test@localhost/test' );
 * $definition = new WorkflowDatabaseDefinitionStorage( $db );
 * $workflow   = $definition->loadByName( 'Test' );
 * $execution  = new WorkflowDatabaseExecution( $db );
 *
 * $execution->workflow = $workflow;
 * $execution->addPlugin( new Visualizer( '/tmp' ) );
 * $execution->start();
 * ?>
 * </code>
 *
 * @package Workflow
 * @version //autogen//
 */
class Visualizer extends ExecutionPlugin
{
    /**
     * Filename counter.
     *
     * @var integer
     */
    protected $fileCounter = 0;

//    /**
//     * Properties.
//     *
//     * @var array(string=>mixed)
//     */
//    protected $properties = array();

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var bool
     */
    protected $includeVariables = true;

    /**
     * Constructor.
     *
     * @param string $directory        The directory to which the DOT files are written.
     * @param bool   $includeVariables Includes variables in the visualization
     */
    public function __construct($directory, $includeVariables = true)
    {
        $this->directory = $directory;
        $this->includeVariables = $includeVariables;
    }

    /**
     * Called after a node has been activated.
     *
     * @param AbstractExecution $execution
     * @param AbstractNode      $node
     */
    public function afterNodeActivated( Execution $execution, Node $node )
    {
        $this->visualize( $execution );
    }



    /**
     * Called after a node has been executed.
     *
     * @param AbstractExecution $execution
     * @param AbstractNode      $node
     */
    public function afterNodeExecuted( Execution $execution, Node $node )
    {
        $this->visualize( $execution );
    }

    /**
     * Visualizes the current state of the workflow execution.
     *
     * @param AbstractExecution $execution
     */
    protected function visualize( Execution $execution )
    {
        $activatedNodes = array();

        foreach ( $execution->getActivatedNodes() as $node ) {
            $activatedNodes[] = $node->getId();
        }

        if ( $this->includeVariables) {
            $variables = $execution->getVariables();
        } else {
            $variables = array();
        }

        $visitor = new Visualization();
        $visitor->setHighlightedNodes($activatedNodes);
        $visitor->setWorkflowVariables($variables);

        $execution->getWorkflow()->accept( $visitor );

        file_put_contents(
          sprintf(
            '%s%s%s_%03d_%03d.dot',

            $this->directory,
            DIRECTORY_SEPARATOR,
            $execution->getWorkflow()->getName(),
            $execution->getId(),
            ++$this->fileCounter
          ),
          $visitor
        );
    }

    /**
     * @param boolean $includeVariables
     */
    public function setIncludeVariables($includeVariables)
    {
        $this->includeVariables = $includeVariables;
    }

    /**
     * @return boolean
     */
    public function getIncludeVariables()
    {
        return $this->includeVariables;
    }

    /**
     * @param string $directory
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }
}

