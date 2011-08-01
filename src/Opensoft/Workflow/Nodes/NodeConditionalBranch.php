<?php
/**
 * File containing the AbstractNodeConditionalBranch class.
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

use Opensoft\Workflow\Conditions\ConditionInterface;
use Opensoft\Workflow\Exception\InvalidWorkflowException;
use Opensoft\Workflow\Exception\ExecutionException;
use Opensoft\Workflow\Util;
use Opensoft\Workflow\Conditions\Not;
use Opensoft\Workflow\Execution\Execution;

/**
 * Abstract base class for nodes that conditionally branch multiple threads of
 * execution.
 *
 * Most implementations only need to set the conditions for proper functioning.
 *
 * @package Workflow
 * @version //autogen//
 */
abstract class NodeConditionalBranch extends NodeBranch
{
    /**
     * Constraint: The minimum number of conditional outgoing nodes this node
     * has to have. Set to false to disable this constraint.
     *
     * @var integer
     */
    protected $minConditionalOutNodes = false;

    /**
     * Constraint: The minimum number of conditional outgoing nodes this node
     * has to activate. Set to false to disable this constraint.
     *
     * @var integer
     */
    protected $minActivatedConditionalOutNodes = false;

    /**
     * Constraint: The maximum number of conditional outgoing nodes this node
     * may activate. Set to false to disable this constraint.
     *
     * @var integer
     */
    protected $maxActivatedConditionalOutNodes = false;

    /**
     * Holds the conditions of the out nodes.
     *
     * The key is the position of the out node in the array of out nodes.
     *
     * @var array( 'condition' => array( 'int' => WorkflowCondtion ) )
     */
    protected $configuration = array(
      'condition' => array(),
      'else' => array()
    );

    /**
     * Adds the conditional outgoing node $outNode to this node with the
     * condition $condition. Optionally, an $else node can be specified that is
     * activated when the $condition evaluates to false.
     *
     * @param ConditionInterface $condition
     * @param AbstractNode      $outNode
     * @param AbstractNode      $else
     * @return AbstractNode
     */
    public function addConditionalOutNode(ConditionInterface $condition, Node $outNode, Node $else = null)
    {
        $this->addOutNode( $outNode );
        $this->configuration['condition'][Util::findObject( $this->outNodes, $outNode )] = $condition;

        if ( !is_null( $else ) )
        {
            $this->addOutNode( $else );

            $key = Util::findObject( $this->outNodes, $else );
            $this->configuration['condition'][$key] = new Not( $condition );
            $this->configuration['else'][$key] = true;
        }

        return $this;
    }

    /**
     * Returns the condition for a conditional outgoing node
     * and false if the passed not is not a (unconditional)
     * outgoing node of this node.
     *
     * @param  AbstractNode $node
     * @return ConditionInterface
     * @ignore
     */
    public function getCondition(Node $node)
    {
        $keys    = array_keys( $this->outNodes );
        $numKeys = count( $keys );

        for ( $i = 0; $i < $numKeys; $i++ )
        {
            if ( $this->outNodes[$keys[$i]] === $node )
            {
                if ( isset( $this->configuration['condition'][$keys[$i]] ) )
                {
                    return $this->configuration['condition'][$keys[$i]];
                }
                else
                {
                    return false;
                }
            }
        }

        return false;
    }

    /**
     * Returns true when the $node belongs to an ELSE condition.
     *
     * @param AbstractNode $node
     * @return bool
     * @ignore
     */
    public function isElse(Node $node)
    {
        return isset( $this->configuration['else'][Util::findObject( $this->outNodes, $node )] );
    }

    /**
     * Evaluates all the conditions, checks the constraints and activates any nodes that have
     * passed through both checks and condition evaluation.
     *
     * @param AbstractExecution $execution
     * @return boolean true when the node finished execution,
     *                 and false otherwise
     * @ignore
     */
    public function execute(Execution $execution)
    {
        $keys                            = array_keys( $this->outNodes );
        $numKeys                         = count( $keys );
        $nodesToStart                    = array();
        $numActivatedConditionalOutNodes = 0;

        if ( $this->maxActivatedConditionalOutNodes !== false )
        {
            $maxActivatedConditionalOutNodes = $this->maxActivatedConditionalOutNodes;
        }
        else
        {
            $maxActivatedConditionalOutNodes = $numKeys;
        }

        for ( $i = 0; $i < $numKeys && $numActivatedConditionalOutNodes <= $maxActivatedConditionalOutNodes; $i++ )
        {
            if ( isset( $this->configuration['condition'][$keys[$i]] ) )
            {
                // Conditional outgoing node.
                if ( $this->configuration['condition'][$keys[$i]]->evaluate( $execution->getVariables() ) )
                {
                    $nodesToStart[] = $this->outNodes[$keys[$i]];
                    $numActivatedConditionalOutNodes++;
                }
            }
            else
            {
                // Unconditional outgoing node.
                $nodesToStart[] = $this->outNodes[$keys[$i]];
            }
        }

        if ( $this->minActivatedConditionalOutNodes !== false && $numActivatedConditionalOutNodes < $this->minActivatedConditionalOutNodes )
        {
            throw new ExecutionException(
              'Node activates less conditional outgoing nodes than required.'
            );
        }

        return $this->activateOutgoingNodes($execution, $nodesToStart);
    }

    /**
     * Checks this node's constraints.
     *
     * @throws InvalidWorkflowException if the constraints of this node are not met.
     */
    public function verify()
    {
        parent::verify();

        $numConditionalOutNodes = count( $this->configuration['condition'] );

        if ( $this->minConditionalOutNodes !== false && $numConditionalOutNodes < $this->minConditionalOutNodes )
        {
            throw new InvalidWorkflowException(
              'Node has less conditional outgoing nodes than required.'
            );
        }
    }
}

