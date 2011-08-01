<?php
/**
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
 * @subpackage Tests
 * @version //autogentag//
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace Opensoft\Tests\Workflow;

/**
 * @package Workflow
 * @subpackage Tests
 */
abstract class WorkflowTestCase extends \PHPUnit_Framework_TestCase
{
    protected $xmlStorage;
    protected $tempDir;

    /**
     * @var \Opensoft\Workflow\Workflow
     */
    protected $workflow;
    protected $startNode;
    protected $endNode;
    protected $cancelNode;
    protected $branchNode;

    protected function setUp()
    {
        $this->xmlStorage = new \Opensoft\Workflow\DefinitionStorage\Xml(
          dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR
        );

        if ( !class_exists( 'ServiceObject', false ) )
        {
            $this->getMock( 'Opensoft\\Workflow\\ServiceObjectInterface', array(), array(), 'ServiceObject' );
        }
    }

    protected function setUpEmptyWorkflow( $name = 'Empty' )
    {
        $this->workflow = new \Opensoft\Workflow\Workflow( $name );
    }

    protected function setUpStartEnd()
    {
        $this->workflow = new \Opensoft\Workflow\Workflow( 'StartEnd' );
        $this->workflow->getStartNode()->addOutNode( $this->workflow->getEndNode() );
    }

    protected function setUpStartEndVariableHandler()
    {
        $this->workflow = new \Opensoft\Workflow\Workflow( 'StartEndVariableHandler' );
        $this->workflow->getStartNode()->addOutNode( $this->workflow->getEndNode() );
        $this->workflow->addVariableHandler( 'foo', 'Opensoft\Tests\Workflow\Mocks\WorkflowTestVariableHandler' );
    }

    protected function setUpStartInputEnd()
    {
        $this->workflow = new \Opensoft\Workflow\Workflow( 'StartInputEnd' );
        $inputNode = new \Opensoft\Workflow\Nodes\Variables\Input( array( 'variable' => new \Opensoft\Workflow\Conditions\IsString() ) );

        $this->workflow->getStartNode()->addOutNode( $inputNode );
        $this->workflow->getEndNode()->addInNode( $inputNode );

        $this->workflow->addVariableHandler( 'foo', 'Opensoft\Tests\Workflow\Mocks\WorkflowTestVariableHandler' );
    }

    protected function setUpStartInputEnd2()
    {
        $this->workflow = new \Opensoft\Workflow\Workflow( 'StartInputEnd2' );
        $inputNode = new \Opensoft\Workflow\Nodes\Variables\Input( array( 'variable' => new \Opensoft\Workflow\Conditions\InArray( array( '1', 2, 3 ) ) ) );

        $this->workflow->getStartNode()->addOutNode( $inputNode );
        $this->workflow->getEndNode()->addInNode( $inputNode );
    }

    protected function setUpStartSetEnd()
    {
        $this->workflow = new \Opensoft\Workflow\Workflow( 'StartSetEnd' );

        $set = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array(
            'null' => null,
            'true' => true,
            'false' => false,
            'array' => array( 22, 4, 1978 ),
            'object' => new \stdClass(),
            'string' => 'string',
            'integer' => 2241978,
            'float' => 22.04
          )
        );

        $this->workflow->getStartNode()->addOutNode( $set );
        $set->addOutNode( $this->workflow->getEndNode() );
    }

    protected function setUpStartSetUnsetEnd()
    {
        $this->workflow = new \Opensoft\Workflow\Workflow( 'StartSetUnsetEnd' );

        $set = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array( 'x' => 1 )
        );

        $unset = new \Opensoft\Workflow\Nodes\Variables\UnsetVar( 'x' );

        $this->workflow->getStartNode()->addOutNode( $set );
        $set->addOutNode( $unset );
        $unset->addOutNode( $this->workflow->getEndNode() );
    }

    protected function setUpDecrementingLoop()
    {
        $this->setUpLoop( 'decrement' );
    }

    protected function setUpIncrementingLoop()
    {
        $this->setUpLoop( 'increment' );
    }

    protected function setUpLoop( $direction )
    {
        if ( $direction == 'increment' )
        {
            $this->workflow = new \Opensoft\Workflow\Workflow( 'IncrementingLoop' );

            $start = 1;
            $step = new \Opensoft\Workflow\Nodes\Variables\Increment( 'i' );
            $break = new \Opensoft\Workflow\Conditions\Variable( 'i', new \Opensoft\Workflow\Conditions\IsEqual( 10 ) );
            $continue = new \Opensoft\Workflow\Conditions\Variable( 'i', new \Opensoft\Workflow\Conditions\IsLessThan( 10 ) );
        }
        else
        {
            $this->workflow = new \Opensoft\Workflow\Workflow( 'DecrementingLoop' );

            $start = 10;
            $step = new \Opensoft\Workflow\Nodes\Variables\Decrement( 'i' );
            $break = new \Opensoft\Workflow\Conditions\Variable( 'i', new \Opensoft\Workflow\Conditions\IsEqual( 1 ) );
            $continue = new \Opensoft\Workflow\Conditions\Variable( 'i', new \Opensoft\Workflow\Conditions\IsGreaterThan( 1 ) );
        }

        $set = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array( 'i' => $start )
        );

        $this->workflow->getStartNode()->addOutNode( $set );

        $loop = new \Opensoft\Workflow\Nodes\ControlFlow\Loop();
        $loop->addInNode( $set )
             ->addInNode( $step )
             ->addConditionalOutNode( $continue, $step )
             ->addConditionalOutNode( $break, $this->workflow->getEndNode() );
    }

    protected function setUpSetAddSubMulDiv()
    {
        $this->workflow = new \Opensoft\Workflow\Workflow( 'SetAddSubMulDiv' );

        $set = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array( 'x' => 1 )
        );

        $add = new \Opensoft\Workflow\Nodes\Variables\Add(
          array( 'name' => 'x', 'operand' => 1 )
        );

        $sub = new \Opensoft\Workflow\Nodes\Variables\Sub(
          array( 'name' => 'x', 'operand' => 1 )
        );

        $mul = new \Opensoft\Workflow\Nodes\Variables\Mul(
          array( 'name' => 'x', 'operand' => 2 )
        );

        $div = new \Opensoft\Workflow\Nodes\Variables\Div(
          array( 'name' => 'x', 'operand' => 2 )
        );

        $this->workflow->getStartNode()->addOutNode( $set );
        $set->addOutNode( $add );
        $add->addOutNode( $sub );
        $sub->addOutNode( $mul );
        $mul->addOutNode( $div );
        $this->workflow->getEndNode()->addInNode( $div );
    }

    protected function setUpAddVariables()
    {
        $this->workflow = new \Opensoft\Workflow\Workflow( 'AddVariables' );

        $set = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array( 'a' => 1, 'b' => 1 )
        );

        $add = new \Opensoft\Workflow\Nodes\Variables\Add(
          array( 'name' => 'b', 'operand' => 'a' )
        );

        $this->workflow->getStartNode()->addOutNode( $set );
        $set->addOutNode( $add );
        $this->workflow->getEndNode()->addInNode( $add );
    }

    protected function setUpAddVariables2()
    {
        $this->workflow = new \Opensoft\Workflow\Workflow( 'AddVariables2' );

        $set = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array( 'a' => 'a', 'b' => 1 )
        );

        $add = new \Opensoft\Workflow\Nodes\Variables\Add(
          array( 'name' => 'b', 'operand' => 'a' )
        );

        $this->workflow->getStartNode()->addOutNode( $set );
        $set->addOutNode( $add );
        $this->workflow->getEndNode()->addInNode( $add );
    }

    protected function setUpAddVariables3()
    {
        $this->workflow = new \Opensoft\Workflow\Workflow( 'AddVariables3' );

        $set = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array( 'a' => 1, 'b' => 'b' )
        );

        $add = new \Opensoft\Workflow\Nodes\Variables\Add(
          array( 'name' => 'b', 'operand' => 'a' )
        );

        $this->workflow->getStartNode()->addOutNode( $set );
        $set->addOutNode( $add );
        $this->workflow->getEndNode()->addInNode( $add );
    }

    protected function setUpVariableEqualsVariable()
    {
        $this->workflow = new \Opensoft\Workflow\Workflow( 'VariableEqualsVariable' );

        $set = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array( 'a' => 1, 'b' => 1 )
        );

        $set2 = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array( 'c' => 1 )
        );

        $set3 = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array( 'c' => 0 )
        );

        $this->branchNode = new \Opensoft\Workflow\Nodes\ControlFlow\ExclusiveChoice();
        $this->branchNode->addInNode( $set );

        $this->branchNode->addConditionalOutNode(
          new \Opensoft\Workflow\Conditions\Variables(
            'a', 'b', new \Opensoft\Workflow\Conditions\IsEqual()
          ),
          $set2
        );

        $this->branchNode->addConditionalOutNode(
          new \Opensoft\Workflow\Conditions\Variables(
            'a', 'b', new \Opensoft\Workflow\Conditions\IsNotEqual()
          ),
          $set3
        );

        $simpleMerge = new \Opensoft\Workflow\Nodes\ControlFlow\SimpleMerge();

        $simpleMerge->addInNode( $set2 )
                    ->addInNode( $set3 );

        $this->workflow->getStartNode()->addOutNode( $set );
        $this->workflow->getEndNode()->addInNode( $simpleMerge );
    }

    protected function setUpParallelSplitSynchronization()
    {
        $this->workflow   = new \Opensoft\Workflow\Workflow( 'ParallelSplitSynchronization' );
        $this->branchNode = new \Opensoft\Workflow\Nodes\ControlFlow\ParallelSplit();

        $actionNodeA = new \Opensoft\Workflow\Nodes\Action( 'ServiceObject' );
        $actionNodeB = new \Opensoft\Workflow\Nodes\Action( 'ServiceObject' );
        $actionNodeC = new \Opensoft\Workflow\Nodes\Action( 'ServiceObject' );

        $this->branchNode->addOutNode( $actionNodeA );
        $this->branchNode->addOutNode( $actionNodeB );
        $this->branchNode->addOutNode( $actionNodeC );

        $synchronization = new \Opensoft\Workflow\Nodes\ControlFlow\Synchronization();

        $synchronization->addInNode( $actionNodeA );
        $synchronization->addInNode( $actionNodeB );
        $synchronization->addInNode( $actionNodeC );

        $this->workflow->getStartNode()->addOutNode( $this->branchNode );
        $this->workflow->getEndNode()->addInNode( $synchronization );
    }

    protected function setUpParallelSplitSynchronization2()
    {
        $this->workflow   = new \Opensoft\Workflow\Workflow( 'ParallelSplitSynchronization2' );
        $this->branchNode = new \Opensoft\Workflow\Nodes\ControlFlow\ParallelSplit();

        $foo = new \Opensoft\Workflow\Nodes\Variables\Input( array( 'foo' => new \Opensoft\Workflow\Conditions\IsString() ) );
        $bar = new \Opensoft\Workflow\Nodes\Variables\Input( array( 'bar' => new \Opensoft\Workflow\Conditions\IsString() ) );

        $this->branchNode->addOutNode( $foo );
        $this->branchNode->addOutNode( $bar );

        $synchronization = new \Opensoft\Workflow\Nodes\ControlFlow\Synchronization();

        $synchronization->addInNode( $foo );
        $synchronization->addInNode( $bar );

        $this->workflow->getStartNode()->addOutNode( $this->branchNode );
        $this->workflow->getEndNode()->addInNode( $synchronization );
    }

    protected function setUpParallelSplitInvalidSynchronization()
    {
        $this->workflow = new \Opensoft\Workflow\Workflow( 'ParallelSplitInvalidSynchronization' );

        $branchA = new \Opensoft\Workflow\Nodes\ControlFlow\ParallelSplit();
        $branchB = new \Opensoft\Workflow\Nodes\ControlFlow\ParallelSplit();
        $branchC = new \Opensoft\Workflow\Nodes\ControlFlow\ParallelSplit();

        $branchA->addOutNode( $branchB )
                ->addOutNode( $branchC );

        $synchronization = new \Opensoft\Workflow\Nodes\ControlFlow\Synchronization();

        $branchB->addOutNode( new \Opensoft\Workflow\Nodes\End() )
                ->addOutNode( $synchronization );

        $branchC->addOutNode( $synchronization )
                ->addOutNode( new \Opensoft\Workflow\Nodes\End() );

        $this->workflow->getStartNode()->addOutNode( $branchA );
        $this->workflow->getEndNode()->addInNode( $synchronization );
    }

    protected function setUpExclusiveChoiceSimpleMerge( $a = 'Opensoft\\Workflow\\Conditions\\IsTrue', $b = 'Opensoft\\Workflow\\Conditions\\IsFalse' )
    {
        $this->workflow   = new \Opensoft\Workflow\Workflow( 'ExclusiveChoiceSimpleMerge' );
        $this->branchNode = new \Opensoft\Workflow\Nodes\ControlFlow\ExclusiveChoice();

        $actionNodeA = new \Opensoft\Workflow\Nodes\Action( 'ServiceObject' );
        $actionNodeB = new \Opensoft\Workflow\Nodes\Action( 'ServiceObject' );

        $this->branchNode->addConditionalOutNode(
          new \Opensoft\Workflow\Conditions\Variable(
            'condition',
            new $a
          ),
          $actionNodeA
        );

        $this->branchNode->addConditionalOutNode(
          new \Opensoft\Workflow\Conditions\Variable(
            'condition',
            new $b
          ),
          $actionNodeB
        );

        $simpleMerge = new \Opensoft\Workflow\Nodes\ControlFlow\SimpleMerge();

        $simpleMerge->addInNode( $actionNodeA );
        $simpleMerge->addInNode( $actionNodeB );

        $this->workflow->getStartNode()->addOutNode( $this->branchNode );
        $this->workflow->getEndNode()->addInNode( $simpleMerge );
    }

    protected function setUpExclusiveChoiceWithElseSimpleMerge()
    {
        $this->workflow   = new \Opensoft\Workflow\Workflow( 'ExclusiveChoiceWithElseSimpleMerge' );
        $this->branchNode = new \Opensoft\Workflow\Nodes\ControlFlow\ExclusiveChoice();

        $setX = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array( 'x' => true )
        );

        $setY = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array( 'y' => true )
        );

        $this->branchNode->addConditionalOutNode(
          new \Opensoft\Workflow\Conditions\Variable(
            'condition',
            new \Opensoft\Workflow\Conditions\IsTrue()
          ),
          $setX,
          $setY
        );

        $simpleMerge = new \Opensoft\Workflow\Nodes\ControlFlow\SimpleMerge();

        $simpleMerge->addInNode( $setX );
        $simpleMerge->addInNode( $setY );

        $this->workflow->getStartNode()->addOutNode( $this->branchNode );
        $this->workflow->getEndNode()->addInNode( $simpleMerge );
    }

    protected function setUpExclusiveChoiceWithUnconditionalOutNodeSimpleMerge()
    {
        $this->workflow = new \Opensoft\Workflow\Workflow( 'ExclusiveChoiceWithUnconditionalOutNodeSimpleMerge' );

        $setX = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array( 'x' => true )
        );

        $setY = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array( 'y' => true )
        );

        $setZ = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array( 'z' => true )
        );

        $this->branchNode = new \Opensoft\Workflow\Nodes\ControlFlow\ExclusiveChoice();

        $this->branchNode->addConditionalOutNode(
          new \Opensoft\Workflow\Conditions\Variable(
            'condition',
            new \Opensoft\Workflow\Conditions\IsTrue()
          ),
          $setX
        );

        $this->branchNode->addConditionalOutNode(
          new \Opensoft\Workflow\Conditions\Variable(
            'condition',
            new \Opensoft\Workflow\Conditions\IsFalse()
          ),
          $setY
        );

        $this->branchNode->addOutNode( $setZ );

        $simpleMerge = new \Opensoft\Workflow\Nodes\ControlFlow\SimpleMerge();

        $simpleMerge->addInNode( $setX )
                    ->addInNode( $setY )
                    ->addInNode( $setZ );

        $this->workflow->getStartNode()->addOutNode( $this->branchNode );
        $this->workflow->getEndNode()->addInNode( $simpleMerge );
    }

    protected function setUpNestedExclusiveChoiceSimpleMerge($x = true, $y = true)
    {
        $this->workflow = new \Opensoft\Workflow\Workflow( 'NestedExclusiveChoiceSimpleMerge' );

        $setX = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array( 'x' => $x )
        );

        $setY = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array( 'y' => $y )
        );

        $setZ1 = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array( 'z' => true )
        );

        $setZ2 = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array( 'z' => false )
        );

        $setZ3 = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array( 'z' => false )
        );

        $this->workflow->getStartNode()->addOutNode( $setX );

        $branch1 = new \Opensoft\Workflow\Nodes\ControlFlow\ExclusiveChoice();
        $branch1->addInNode( $setX );

        $branch1->addConditionalOutNode(
          new \Opensoft\Workflow\Conditions\Variable(
            'x',
            new \Opensoft\Workflow\Conditions\IsTrue()
          ),
          $setY
        );

        $branch1->addConditionalOutNode(
          new \Opensoft\Workflow\Conditions\Variable(
            'x',
            new \Opensoft\Workflow\Conditions\IsFalse()
          ),
          $setZ3
        );

        $branch2 = new \Opensoft\Workflow\Nodes\ControlFlow\ExclusiveChoice();
        $branch2->addInNode( $setY );

        $branch2->addConditionalOutNode(
          new \Opensoft\Workflow\Conditions\Variable(
            'y',
            new \Opensoft\Workflow\Conditions\IsTrue()
          ),
          $setZ1
        );

        $branch2->addConditionalOutNode(
          new \Opensoft\Workflow\Conditions\Variable(
            'y',
            new \Opensoft\Workflow\Conditions\IsFalse()
          ),
          $setZ2
        );

        $nestedMerge = new \Opensoft\Workflow\Nodes\ControlFlow\SimpleMerge();
        $nestedMerge->addInNode( $setZ1 )
                    ->addInNode( $setZ2 );

        $merge = new \Opensoft\Workflow\Nodes\ControlFlow\SimpleMerge();
        $merge->addInNode( $nestedMerge )
              ->addInNode( $setZ3 )
              ->addOutNode( $this->workflow->getEndNode() );
    }

    protected function setUpMultiChoiceSynchronizingMerge()
    {
        $this->setUpMultiChoice( 'SynchronizingMerge' );
    }

    protected function setUpMultiChoiceDiscriminator()
    {
        $this->setUpMultiChoice( 'Discriminator' );
    }

    protected function setUpMultiChoice( $mergeType )
    {
        $this->workflow = new \Opensoft\Workflow\Workflow( 'MultiChoice' . $mergeType );

        $set = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array(
            'x' => 1, 'y' => 2
          )
        );

        $multiChoice  = new \Opensoft\Workflow\Nodes\ControlFlow\MultiChoice();
        $actionNodeA  = new \Opensoft\Workflow\Nodes\Action( 'ServiceObject' );
        $actionNodeB  = new \Opensoft\Workflow\Nodes\Action( 'ServiceObject' );
        $actionNodeC  = new \Opensoft\Workflow\Nodes\Action( 'ServiceObject' );

        $multiChoice->addConditionalOutNode(
          new \Opensoft\Workflow\Conditions\BooleanAnd(
            array(
              new \Opensoft\Workflow\Conditions\Variable(
                'x',
                new \Opensoft\Workflow\Conditions\IsEqual( 1 )
              ),
              new \Opensoft\Workflow\Conditions\Not(
                new \Opensoft\Workflow\Conditions\Variable(
                  'y',
                  new \Opensoft\Workflow\Conditions\IsEqual( 3 )
                )
              )
            )
          ),
          $actionNodeA
        );

        $multiChoice->addConditionalOutNode(
          new \Opensoft\Workflow\Conditions\BooleanOr(
            array(
              new \Opensoft\Workflow\Conditions\Variable(
                'x',
                new \Opensoft\Workflow\Conditions\IsEqual( 1 )
              ),
              new \Opensoft\Workflow\Conditions\Variable(
                'y',
                new \Opensoft\Workflow\Conditions\IsEqual( 2 )
              )
            )
          ),
          $actionNodeB
        );

        $multiChoice->addConditionalOutNode(
          new \Opensoft\Workflow\Conditions\BooleanXor(
            array(
              new \Opensoft\Workflow\Conditions\Variable(
                'x',
                new \Opensoft\Workflow\Conditions\IsEqual( 1 )
              ),
              new \Opensoft\Workflow\Conditions\Variable(
                'y',
                new \Opensoft\Workflow\Conditions\IsEqual( 1 )
              )
            )
          ),
          $actionNodeC
        );

        if ( $mergeType == 'SynchronizingMerge' )
        {
            $merge = new \Opensoft\Workflow\Nodes\ControlFlow\SynchronizingMerge();
        }
        else
        {
            $merge = new \Opensoft\Workflow\Nodes\ControlFlow\Discriminator();
        }

        $merge->addInNode( $actionNodeA );
        $merge->addInNode( $actionNodeB );
        $merge->addInNode( $actionNodeC );

        $this->workflow->getStartNode()->addOutNode( $set );
        $set->addOutNode( $multiChoice );
        $this->workflow->getEndNode()->addInNode( $merge );
    }

    protected function setUpWorkflowWithSubWorkflowStartEnd()
    {
        $this->setUpWorkflowWithSubWorkflow( 'StartEnd' );
    }

    protected function setUpWorkflowWithSubWorkflowParallelSplitActionActionCancelCaseSynchronization()
    {
        $this->setUpWorkflowWithSubWorkflow( 'ParallelSplitActionActionCancelCaseSynchronization' );
    }

    protected function setUpWorkflowWithSubWorkflow( $subWorkflow )
    {
        $this->workflow = new \Opensoft\Workflow\Workflow( 'WorkflowWithSubWorkflow' . $subWorkflow );
        $subWorkflow    = new \Opensoft\Workflow\Nodes\SubWorkflow( $subWorkflow );

        $this->workflow->getStartNode()->addOutNode( $subWorkflow );
        $this->workflow->getEndNode()->addInNode( $subWorkflow );
    }

    protected function setUpWorkflowWithSubWorkflowAndVariablePassing()
    {
        $this->workflow = new \Opensoft\Workflow\Workflow( 'WorkflowWithSubWorkflowAndVariablePassing' );
        $set            = new \Opensoft\Workflow\Nodes\Variables\SetVar( array( 'x' => 1 ) );

        $subWorkflow = new \Opensoft\Workflow\Nodes\SubWorkflow(
          array(
            'workflow'  => 'IncrementVariable',
            'variables' => array(
              'in' => array(
                'x' => 'y'
              ),
              'out' => array(
                'y' => 'z'
              )
            )
          )
        );

        $subWorkflow->addInNode( $set );

        $this->workflow->getStartNode()->addOutNode( $set );
        $this->workflow->getEndNode()->addInNode( $subWorkflow );
    }

    protected function setUpNestedLoops()
    {
        $this->workflow = new \Opensoft\Workflow\Workflow( 'NestedLoops' );

        $innerSet      = new \Opensoft\Workflow\Nodes\Variables\SetVar( array( 'j' => 1 ) );
        $innerStep     = new \Opensoft\Workflow\Nodes\Variables\Increment( 'j' );
        $innerBreak    = new \Opensoft\Workflow\Conditions\Variable( 'j', new \Opensoft\Workflow\Conditions\IsEqual( 2 ) );
        $innerContinue = new \Opensoft\Workflow\Conditions\Variable( 'j', new \Opensoft\Workflow\Conditions\IsLessThan( 2 ) );

        $innerLoop = new \Opensoft\Workflow\Nodes\ControlFlow\Loop();
        $innerLoop->addInNode( $innerSet )
                  ->addInNode( $innerStep );

        $outerSet      = new \Opensoft\Workflow\Nodes\Variables\SetVar( array( 'i' => 1 ) );
        $outerStep     = new \Opensoft\Workflow\Nodes\Variables\Increment( 'i' );
        $outerBreak    = new \Opensoft\Workflow\Conditions\Variable( 'i', new \Opensoft\Workflow\Conditions\IsEqual( 2 ) );
        $outerContinue = new \Opensoft\Workflow\Conditions\Variable( 'i', new \Opensoft\Workflow\Conditions\IsLessThan( 2 ) );

        $this->workflow->getStartNode()->addOutNode( $outerSet );

        $outerLoop = new \Opensoft\Workflow\Nodes\ControlFlow\Loop();
        $outerLoop->addInNode( $outerSet )
                  ->addInNode( $outerStep );

        $innerLoop->addConditionalOutNode( $innerContinue, $innerStep )
                  ->addConditionalOutNode( $innerBreak, $outerStep );

        $outerLoop->addConditionalOutNode( $outerContinue, $innerSet )
                  ->addConditionalOutNode( $outerBreak, $this->workflow->getEndNode() );
    }

    protected function setUpParallelSplitCancelCaseActionActionSynchronization()
    {
        $this->setUpCancelCase( 'first' );
    }

    protected function setUpParallelSplitActionActionCancelCaseSynchronization()
    {
        $this->setUpCancelCase( 'last' );
    }

    protected function setUpCancelCase( $order )
    {
        if ( $order == 'first' )
        {
            $workflowName = 'ParallelSplitCancelCaseActionActionSynchronization';
        }
        else
        {
            $workflowName = 'ParallelSplitActionActionCancelCaseSynchronization';
        }

        $this->workflow = new \Opensoft\Workflow\Workflow( $workflowName );

        $this->branchNode = new \Opensoft\Workflow\Nodes\ControlFlow\ParallelSplit();
        $cancelNode       = new \Opensoft\Workflow\Nodes\Cancel();
        $actionNodeA      = new \Opensoft\Workflow\Nodes\Action( 'ServiceObject' );
        $actionNodeB      = new \Opensoft\Workflow\Nodes\Action( 'ServiceObject' );
        $actionNodeC      = new \Opensoft\Workflow\Nodes\Action( 'ServiceObject' );
        $synchronization  = new \Opensoft\Workflow\Nodes\ControlFlow\Synchronization();

        if ( $order == 'first' )
        {
            $this->branchNode->addOutNode( $cancelNode );
            $this->branchNode->addOutNode( $actionNodeB );
            $this->branchNode->addOutNode( $actionNodeC );

            $synchronization->addInNode( $cancelNode );
            $synchronization->addInNode( $actionNodeB );
            $synchronization->addInNode( $actionNodeC );
        }
        else
        {
            $this->branchNode->addOutNode( $actionNodeB );
            $this->branchNode->addOutNode( $actionNodeC );
            $this->branchNode->addOutNode( $cancelNode );

            $synchronization->addInNode( $actionNodeB );
            $synchronization->addInNode( $actionNodeC );
            $synchronization->addInNode( $cancelNode );
        }

        $this->workflow->getStartNode()->addOutNode( $actionNodeA );
        $actionNodeA->addOutNode( $this->branchNode );
        $this->workflow->getEndNode()->addInNode( $synchronization );
    }

    protected function setUpWorkflowWithFinalActivitiesAfterCancellation()
    {
        $this->workflow = new \Opensoft\Workflow\Workflow( 'WorkflowWithFinalActivitiesAfterCancellation' );
        $cancelNode     = new \Opensoft\Workflow\Nodes\Cancel();

        $this->workflow->getStartNode()->addOutNode( $cancelNode );
        $this->workflow->getEndNode()->addInNode( $cancelNode );

        $set = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array( 'finalActivityExecuted' => true )
        );

        $set->addOutNode( new \Opensoft\Workflow\Nodes\End() );

        $this->workflow->getFinallyNode()->addOutNode( $set );
    }

    protected function setUpServiceObjectWithArguments()
    {
        $this->setUpEmptyWorkflow( 'ServiceObjectWithArguments' );

        $action = new \Opensoft\Workflow\Nodes\Action(
          array(
            'class' => 'Opensoft\Tests\Workflow\Mocks\ServiceObjectWithConstructor',
            'arguments' => array(
              array( 'Sebastian' ), 22, 'April', 19.78, null, new \stdClass()
            )
          )
        );

        $this->workflow->getStartNode()->addOutNode( $action );
        $this->workflow->getEndNode()->addInNode( $action );
    }

    protected function setUpApprovalProcess()
    {
        $this->workflow = new \Opensoft\Workflow\Workflow( 'ApprovalProcess' );

        $init = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array( 'approved_by_a' => false, 'approved_by_b' => false )
        );

        $approveA = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array( 'approved_by_a' => true )
        );

        $approvedByA = new \Opensoft\Workflow\Conditions\Variable(
          'approved_by_a', new \Opensoft\Workflow\Conditions\IsTrue()
        );

        $notApprovedByA = new \Opensoft\Workflow\Conditions\Variable(
          'approved_by_a', new \Opensoft\Workflow\Conditions\IsFalse()
        );

        $approveB = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array( 'approved_by_b' => true )
        );

        $approvedByB = new \Opensoft\Workflow\Conditions\Variable(
          'approved_by_b', new \Opensoft\Workflow\Conditions\IsTrue()
        );

        $notApprovedByB = new \Opensoft\Workflow\Conditions\Variable(
          'approved_by_b', new \Opensoft\Workflow\Conditions\IsFalse()
        );

        $loop = new \Opensoft\Workflow\Nodes\ControlFlow\Loop();
        $loop->addInNode( $init )
             ->addInNode( $approveA )
             ->addInNode( $approveB )
             ->addConditionalOutNode( $notApprovedByA, $approveA )
             ->addConditionalOutNode( $notApprovedByB, $approveB )
             ->addConditionalOutNode( new \Opensoft\Workflow\Conditions\BooleanAnd( array( $approvedByA, $approvedByB ) ), $this->workflow->getEndNode() );

        $this->workflow->getStartNode()->addOutNode( $init );
    }

    public static function workflowNameProvider()
    {
        return array(
          array( 'AddVariables', 4 ),
          array( 'ApprovalProcess', 6 ),
          array( 'DecrementingLoop', 5 ),
          array( 'ExclusiveChoiceSimpleMerge', 6 ),
          array( 'ExclusiveChoiceWithElseSimpleMerge', 6 ),
          array( 'ExclusiveChoiceWithUnconditionalOutNodeSimpleMerge', 7 ),
          array( 'IncrementingLoop', 5 ),
          array( 'MultiChoiceDiscriminator', 8 ),
          array( 'MultiChoiceSynchronizingMerge', 8 ),
          array( 'NestedExclusiveChoiceSimpleMerge', 11 ),
          array( 'NestedLoops', 8 ),
          array( 'ParallelSplitSynchronization', 7 ),
          array( 'ParallelSplitSynchronization2', 6 ),
          array( 'ParallelSplitActionActionCancelCaseSynchronization', 8 ),
          array( 'ParallelSplitCancelCaseActionActionSynchronization', 8 ),
          array( 'ServiceObjectWithArguments', 3 ),
          array( 'SetAddSubMulDiv', 7 ),
          array( 'StartEnd', 2 ),
          array( 'StartInputEnd', 3 ),
          array( 'StartInputEnd2', 3 ),
          array( 'StartEndVariableHandler', 2 ),
          array( 'StartSetEnd', 3 ),
          array( 'StartSetUnsetEnd', 4 ),
          array( 'VariableEqualsVariable', 7 ),
          array( 'WorkflowWithFinalActivitiesAfterCancellation', 3 ),
          array( 'WorkflowWithSubWorkflowStartEnd', 3 ),
          array( 'WorkflowWithSubWorkflowAndVariablePassing', 4 ),
          array( 'WorkflowWithSubWorkflowParallelSplitActionActionCancelCaseSynchronization', 3 )
        );
    }



    /**
     * Creates and returns the temporary directory.
     *
     * @param string $prefix  Set the prefix of the temporary directory.
     *
     * @param string $path    Set the location of the temporary directory. If
     *                        set to false, the temporary directory will
     *                        probably placed in the /tmp directory.
     */
    protected function createTempDir( $prefix, $path = 'run-tests-tmp' )
    {
        if ( !is_dir( $path ) )
        {
            mkdir( $path );
        }
        if ( $tempname = tempnam( $path, $prefix ))
        {
            unlink( $tempname );
            if ( mkdir( $tempname ) )
            {
                $this->tempDir = $tempname;
                return $tempname;
            }
        }

        return false;
    }

    /**
     * Get the name of the temporary directory.
     */
    public function getTempDir()
    {
        return $this->tempDir;
    }

    /**
     * Remove the temp directory.
     */
    public function removeTempDir()
    {
        if ( file_exists( $this->tempDir ) )
        {
            $this->removeRecursively( $this->tempDir );
        }
    }

    public function cleanTempDir()
    {
        if ( is_dir( $this->tempDir ) )
        {
            if ( $dh = opendir( $this->tempDir ) )
            {
                while ( ( $file = readdir( $dh ) ) !== false )
                {
                    if ( $file[0] != "." )
                    {
                        $this->removeRecursively( $this->tempDir . DIRECTORY_SEPARATOR . $file );
                    }
                }
            }
        }
    }


    private function removeRecursively( $entry )
    {
        if ( is_file( $entry ) || is_link( $entry ) )
        {
            // Some extra security that you're not erasing your harddisk :-).
            if ( strncmp( $this->tempDir, $entry, strlen( $this->tempDir ) ) == 0 )
            {
                return unlink( $entry );
            }
        }

        if ( is_dir( $entry ) )
        {
            if ( $dh = opendir( $entry ) )
            {
                while ( ( $file = readdir( $dh ) ) !== false )
                {
                    if ( $file != "." && $file != '..' )
                    {
                        $this->removeRecursively( $entry . DIRECTORY_SEPARATOR . $file );
                    }
                }

                closedir( $dh );
                rmdir( $entry );
            }
        }
    }
}