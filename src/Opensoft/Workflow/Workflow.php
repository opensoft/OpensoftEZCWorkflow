<?php
/**
 * File containing the Workflow class.
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

namespace Opensoft\Workflow;

use Opensoft\Workflow\Visitors\VisitableInterface;
use Opensoft\Workflow\Visitors\Visitor;
use Opensoft\Workflow\Visitors\Verification;
use Opensoft\Workflow\Nodes\Variables\Input;
use Opensoft\Workflow\Nodes\SubWorkflow;
use Opensoft\Workflow\Visitors\Reset;
use Opensoft\Workflow\Nodes\Start;
use Opensoft\Workflow\Nodes\End;
use Opensoft\Workflow\Nodes\Finally;
use Opensoft\Workflow\Visitors\NodeCollector;
use Opensoft\Workflow\DefinitionStorage\DefinitionStorageInterface;
use Opensoft\Workflow\Exception\InvalidWorkflowException;
use Opensoft\Workflow\Exception\Exception as WorkflowException;

/**
 * Class representing a workflow.
 *
 * @package Workflow
 * @version //autogen//
 * @mainclass
 */
class Workflow implements \Countable, VisitableInterface
{
    /**
     * Unique ID set automatically by the definition handler when the workflow is stored.
     *
     * @var integer
     */
    protected $id;

    /**
     * A unique name (across the system) for this workflow.
     *
     * @var string
     */
    protected $name = '';

    /**
     * The version of the workflow. This must be incremented manually whenever you want a new version.
     *
     * @var integer
     */
    protected $version = 1;

    /**
     * The unique start node of the workflow.
     *
     * @var \Opensoft\Workflow\Nodes\Start
     */
    protected $startNode;

    /**
     * The default end node of the workflow.
     *
     * @var \Opensoft\Workflow\Nodes\End
     */
    protected $endNode;

    /**
     * The start of a node sequence that is executed when a
     * workflow execution is cancelled.
     * 
     * @var \Opensoft\Workflow\Nodes\Finally
     */
    protected $finallyNode;

    /**
     * The definition handler used to fetch sub workflows on demand.
     * This property is set automatically if you load a workflow using
     * a workflow definition storage.
     * 
     * @var \Opensoft\Workflow\DefinitionStorage\DefinitionStorageInterface
     */
    protected $definitionStorage;

    /**
     * The variable handlers of this workflow.
     *
     * @var array
     */
    protected $variableHandlers = array();

    /**
     * Constructs a new workflow object with the name $name.
     *
     * Use $startNode and $endNode parameters if you don't want to use the
     * default start and end nodes.
     *
     * $name must uniquely identify the workflow within the system.
     *
     * @param string                 $name        The name of the workflow.
     * @param Start   $startNode   The start node of the workflow.
     * @param End     $endNode     The default end node of the workflow.
     * @param Finally $finallyNode The start of a node sequence
     *                                            that is executed when a workflow
     *                                            execution is cancelled.
     */
    public function __construct($name, Start $startNode = null, End $endNode = null, Finally $finallyNode = null)
    {
        $this->name = $name;

        // Create default nodes if they give us null
        $this->startNode =   ($startNode === null)   ? new Start()   : $startNode;
        $this->endNode =     ($endNode === null)     ? new End()     : $endNode;
        $this->finallyNode = ($finallyNode === null) ? new Finally() : $finallyNode;
    }

    /**
     * Returns the number of nodes of this workflow.
     *
     * @return integer
     */
    public function count()
    {
        $visitor = new Visitor();
        $this->accept( $visitor );

        return count( $visitor );
    }

    /**
     * Returns true when the workflow requires user interaction
     * (ie. when it contains Input nodes)
     * and false otherwise.
     *
     * @return boolean true when the workflow is interactive, false otherwise.
     */
    public function isInteractive()
    {
        foreach ($this->getNodes() as $node) {
            if ($node instanceof Input) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns true when the workflow has sub workflows
     * (ie. when it contains SubWorkflow nodes)
     * and false otherwise.
     *
     * @return boolean true when the workflow has sub workflows, false otherwise.
     */
    public function hasSubWorkflows() {
        foreach ($this->getNodes() as $node) {
            if ($node instanceof SubWorkflow) {
                return true;
            }
        }

        return false;
    }

    /**
     * Resets the nodes of this workflow.
     *
     * See the documentation of Reset for
     * details.
     */
    public function reset()
    {
        $this->accept( new Reset() );
    }

    /**
     * Verifies the specification of this workflow.
     *
     * See the documentation of Verification for
     * details.
     *
     * @throws InvalidWorkflowException if the specification of this workflow is not correct.
     */
    public function verify()
    {
        $this->accept(new Verification());
    }

    /**
     * Overridden implementation of accept() calls
     * accept on the start node.
     *
     * @param Visitor $visitor
     */
    public function accept(Visitor $visitor)
    {
        $visitor->visit($this);
        $this->startNode->accept($visitor);
    }

    /**
     * Sets the class $className to handle the variable named $variableName.
     *
     * $className must be the name of a class implementing the
     * VariableHandlerInterface interface.
     *
     * @param string $variableName
     * @param string $className
     * @throws InvalidWorkflowException if $className does not contain the name of a valid class implementing VariableHandlerInterface
     */
    public function addVariableHandler( $variableName, $className )
    {
        if ( class_exists( $className ) )  {
            $class = new \ReflectionClass( $className );

            if ( $class->implementsInterface( 'Opensoft\Workflow\VariableHandlerInterface' ) ) {
                $this->variableHandlers[$variableName] = $className;
            } else {
                throw new InvalidWorkflowException(
                  sprintf( 'Class "%s" does not implement the VariableHandlerInterface interface.', $className )
                );
            }
        } else {
            throw new InvalidWorkflowException(
              sprintf( 'Class "%s" not found.', $className )
            );
        }
    }

    /**
     * Removes the handler for $variableName and returns true
     * on success.
     *
     * Returns false if no handler was set for $variableName.
     *
     * @param string $variableName
     * @return boolean
     */
    public function removeVariableHandler( $variableName )
    {
        if ( isset( $this->variableHandlers[$variableName] ) ) {
            unset( $this->variableHandlers[$variableName] );
            return true;
        }

        return false;
    }

    /**
     * Sets handlers for multiple variables.
     *
     * The format of $variableHandlers is
     * array( 'variableName' => VariableHandlerInterface )
     *
     * @throws InvalidWorkflowException if $className does not contain the name of a valid class implementing VariableHandlerInterface
     * @param array $variableHandlers
     */
    public function setVariableHandlers( array $variableHandlers )
    {
        $this->variableHandlers = array();

        foreach ( $variableHandlers as $variableName => $className )
        {
            $this->addVariableHandler( $variableName, $className );
        }
    }

    /**
     * Returns the variable handlers.
     *
     * The format of the returned array is
     * array( 'variableName' => VariableHandlerInterface )
     *
     * @return array
     */
    public function getVariableHandlers()
    {
        return $this->variableHandlers;
    }

    /**
     * @param DefinitionStorage\DefinitionStorageInterface $definitionStorage
     * @return void
     */
    public function setDefinitionStorage(DefinitionStorageInterface $definitionStorage)
    {
        $this->definitionStorage = $definitionStorage;
    }

    /**
     * @return DefinitionStorage\DefinitionStorageInterface
     */
    public function getDefinitionStorage()
    {
        return $this->definitionStorage;
    }


    /**
     * @return \Opensoft\Workflow\Nodes\End
     */
    public function getEndNode()
    {
        return $this->endNode;
    }

    public function setEndNode(End $end)
    {
        $this->endNode = $end;
    }

    /**
     * @return \Opensoft\Workflow\Nodes\Finally
     */
    public function getFinallyNode()
    {
        return $this->finallyNode;
    }

    public function setFinallyNode(Finally $finallyNode)
    {
        $this->finallyNode = $finallyNode;
    }

    /**
     * @param int $id
     * @return void
     */
    public function setId($id)
    {
        $this->id = (int) $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = (string) $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array(AbstractNode)
     */
    public function getNodes()
    {
        $visitor = new NodeCollector( $this ); // todo do we want to cache this?
        return $visitor->getNodes();
    }

    /**
     * @return \Opensoft\Workflow\Nodes\Start
     */
    public function getStartNode()
    {
        return $this->startNode;
    }

    public function setStartNode(Start $startNode)
    {
        $this->startNode = $startNode;
    }

    /**
     * @param int $version
     * @return void
     */
    public function setVersion($version)
    {
        $this->version = (integer) $version;
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }
}

