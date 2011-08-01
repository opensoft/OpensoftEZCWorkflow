<?php
/**
 * File containing the SynchronizingMerge class.
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

namespace Opensoft\Workflow\Nodes\ControlFlow;

/**
 * This node implements the Synchronizing Merge workflow pattern.
 *
 * The Synchronizing Merge workflow pattern is to be used to synchronize multiple parallel
 * threads of execution that are activated by a preceding Multi-Choice.
 *
 * Incoming nodes: 2..*
 * Outgoing nodes: 1
 *
 * This example displays how you can use MultiChoice to activate one or more
 * branches depending on the input and how you can use a synchronizing merge to merge them
 * together again. Execution will not contiue until all activated branches have been completed.
 *
 * <code>
 * <?php
 * $workflow = new Workflow( 'Test' );
 *
 * // wait for input into the workflow variable value.
 * $input = new Input( array( 'value' => new ezcWorkflowConditionIsInt ) );
 * $workflow->startNode->addOutNode( $input );
 *
 * // create the exclusive choice branching node
 * $choice = new MultiChoice;
 * $input->addOutNode( $choice );
 *
 * $branch1 = ....; // create nodes for the first branch of execution here..
 * $branch2 = ....; // create nodes for the second branch of execution here..
 *
 * // add the outnodes and set the conditions on the exclusive choice
 * $choice->addConditionalOutNode( new Variable( 'value',
 *                                                                  new ezcWorkflowConditionGreaterThan( 1 ) ),
 *                                $branch1 );
 * $choice->addConditionalOutNode( new Variable( 'value',
 *                                                                  new ezcWorkflowConditionGreaterThan( 10 ) ),
 *                                $branch2 );
 *
 * // Merge the two branches together and continue execution.
 * $merge = new SynchronizingMerge();
 * $merge->addInNode( $branch1 );
 * $merge->addInNode( $branch2 );
 * $merge->addOutNode( $workflow->endNode );
 * ?>
 * </code>
 *
 * @package Workflow
 * @version //autogen//
 */
class SynchronizingMerge extends Synchronization
{
}

