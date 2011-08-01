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
class NodeTest extends WorkflowTestCase
{

    public function testActionClassNotFound()
    {
        $this->markTestSkipped('Skipping until Action::__toString() is resolved');

        $action = new \Opensoft\Workflow\Nodes\Action( 'NotExistingClass' );
        $this->assertEquals( 'Class "NotExistingClass" not found.', (string)$action );
    }

    public function testActionClassNotServiceObject()
    {
        $this->markTestSkipped('Skipping until Action::__toString() is resolved');
        
        $action = new \Opensoft\Workflow\Nodes\Action( '\stdClass' );
        $this->assertEquals( 'Class "StdClass" does not implement the ezcWorkflowServiceObject interface.', (string)$action );
    }

    public function testInputConstructor()
    {
        try
        {
            new \Opensoft\Workflow\Nodes\Variables\Input( null );
        }
        catch ( \Exception $e )
        {
            $this->assertEquals( 'The value \'\' that you were trying to assign to setting \'configuration\' is invalid. Allowed values are: array.', $e->getMessage() );
            return;
        }

        $this->fail( 'Expected an Exception to be thrown.' );
    }

    public function testInputConstructor2()
    {
        try
        {
            new \Opensoft\Workflow\Nodes\Variables\Input( array( 'foo' => new \stdClass() ) );
        }
        catch ( \Exception $e )
        {
            $this->assertEquals( 'The value \'O:8:"stdClass":0:{}\' that you were trying to assign to setting \'workflow variable condition\' is invalid. Allowed values are: ConditionInterface.', $e->getMessage() );
            return;
        }
        $this->fail( 'Expected an Exception to be thrown.' );
    }

    public function testInputConstructor3()
    {
        try
        {
            new \Opensoft\Workflow\Nodes\Variables\Input( array( new \stdClass() ) );
        }
        catch ( \Exception $e )
        {
            $this->assertEquals( 'The value \'O:8:"stdClass":0:{}\' that you were trying to assign to setting \'workflow variable name\' is invalid. Allowed values are: string.', $e->getMessage() );
            return;
        }

        $this->fail( 'Expected an Exception to be thrown.' );
    }

    public function testInputConstructor4()
    {
        $input         = new \Opensoft\Workflow\Nodes\Variables\Input( array( 'variable' ) );
        $configuration = $input->getConfiguration();

        $this->assertArrayHasKey( 'variable', $configuration );
        $this->assertInstanceOf( 'Opensoft\Workflow\Conditions\IsAnything', $configuration['variable'] );
    }

    public function testVariableSetConstructor()
    {
        try
        {
            new \Opensoft\Workflow\Nodes\Variables\SetVar( null );
        }
        catch ( \Exception $e )
        {
            $this->assertEquals( 'The value \'\' that you were trying to assign to setting \'configuration\' is invalid. Allowed values are: array.', $e->getMessage() );
            return;
        }

        $this->fail( 'Expected an Exception to be thrown.' );
    }

    public function testVariableUnsetConstructor()
    {
        try
        {
            new \Opensoft\Workflow\Nodes\Variables\UnsetVar( null );
        }
        catch ( \Exception $e )
        {
            $this->assertEquals( 'The value \'\' that you were trying to assign to setting \'configuration\' is invalid. Allowed values are: array.', $e->getMessage() );
            return;
        }

        $this->fail( 'Expected an Exception to be thrown.' );
    }

    public function testGetInNodes()
    {
        $this->setUpStartEnd();

        $inNodes = $this->workflow->getEndNode()->getInNodes();

        $this->assertSame( $this->workflow->getStartNode(), $inNodes[0] );
    }

    public function testGetOutNodes()
    {
        $this->setUpStartEnd();

        $outNodes = $this->workflow->getStartNode()->getOutNodes();

        $this->assertSame( $this->workflow->getEndNode(), $outNodes[0] );
    }

    public function testBranchGetCondition()
    {
        $this->setUpExclusiveChoiceSimpleMerge();

        $outNodes = $this->branchNode->getOutNodes();

        $this->assertEquals( 'condition is true', (string)$this->branchNode->getCondition( $outNodes[0] ) );
        $this->assertEquals( 'condition is false', (string)$this->branchNode->getCondition( $outNodes[1] ) );
    }

    public function testBranchGetCondition2()
    {
        $this->setUpExclusiveChoiceWithUnconditionalOutNodeSimpleMerge();

        $outNodes = $this->branchNode->getOutNodes();
        $this->assertFalse( $this->branchNode->getCondition( $outNodes[2] ) );
    }

    public function testBranchGetCondition3()
    {
        $this->setUpExclusiveChoiceWithUnconditionalOutNodeSimpleMerge();

        $this->assertFalse( $this->branchNode->getCondition( new \Opensoft\Workflow\Nodes\End() ) );
    }

    public function testRemoveInNode()
    {
        $this->setUpStartEnd();

        $this->assertTrue( $this->workflow->getEndNode()->removeInNode( $this->workflow->getStartNode() ) );
        $this->assertFalse( $this->workflow->getEndNode()->removeInNode( $this->workflow->getStartNode() ) );
    }

    public function testRemoveOutNode()
    {
        $this->setUpStartEnd();

        $this->assertTrue( $this->workflow->getStartNode()->removeOutNode( $this->workflow->getEndNode() ) );
        $this->assertFalse( $this->workflow->getStartNode()->removeOutNode( $this->workflow->getEndNode() ) );
    }

    public function testToString()
    {
        $this->setUpEmptyWorkflow();

        $this->assertEquals( 'Start', (string)$this->workflow->getStartNode() );
        $this->assertEquals( 'End', (string)$this->workflow->getEndNode() );
    }

    public function testStartVerifyFails()
    {
        try
        {
            $this->setUpEmptyWorkflow();
            $this->workflow->getStartNode()->verify();
        }
        catch ( \Opensoft\Workflow\Exception\InvalidWorkflowException $e )
        {
            $this->assertEquals( 'Node of type "Opensoft\\Workflow\\Nodes\\Start" has less outgoing nodes than required.', $e->getMessage() );
            return;
        }

        $this->fail( 'Expected an Opensoft\Workflow\Exception\InvalidWorkflowException to be thrown.' );
    }

    public function testEndVerifyFails()
    {
        try
        {
            $this->setUpEmptyWorkflow();
            $this->workflow->getEndNode()->verify();
        }
        catch ( \Opensoft\Workflow\Exception\InvalidWorkflowException $e )
        {
            $this->assertEquals( 'Node of type "Opensoft\\Workflow\\Nodes\\End" has less incoming nodes than required.', $e->getMessage() );
            return;
        }

        $this->fail( 'Expected an Opensoft\Workflow\Exception\InvalidWorkflowException to be thrown.' );
    }

    public function testVerifyTooManyIncomingNodes()
    {
        $a = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array( 'foo' => 'bar' )
        );

        $b = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array( 'foo' => 'bar' )
        );

        $c = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array( 'foo' => 'bar' )
        );

        $d = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array( 'foo' => 'bar' )
        );

        $c->addInNode( $a );
        $c->addInNode( $b );
        $c->addOutNode( $d );

        try
        {
            $c->verify();
        }
        catch ( \Opensoft\Workflow\Exception\InvalidWorkflowException $e )
        {
            $this->assertEquals( 'Node of type "Opensoft\\Workflow\\Nodes\\Variables\\SetVar" has more incoming nodes than allowed.', $e->getMessage() );
            return;
        }

        $this->fail( 'Expected an Opensoft\Workflow\Exception\InvalidWorkflowException to be thrown.' );
    }

    public function testVerifyTooManyOutgoingNodes()
    {
        $a = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array( 'foo' => 'bar' )
        );

        $b = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array( 'foo' => 'bar' )
        );

        $c = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array( 'foo' => 'bar' )
        );

        $d = new \Opensoft\Workflow\Nodes\Variables\SetVar(
          array( 'foo' => 'bar' )
        );

        $b->addOutNode( $c );
        $b->addOutNode( $d );
        $b->addInNode( $a );

        try
        {
            $b->verify();
        }
        catch ( \Opensoft\Workflow\Exception\InvalidWorkflowException $e )
        {
            $this->assertEquals( 'Node of type "Opensoft\\Workflow\\Nodes\\Variables\\SetVar" has more outgoing nodes than allowed.', $e->getMessage() );
            return;
        }

        $this->fail( 'Expected an Opensoft\Workflow\Exception\InvalidWorkflowException to be thrown.' );
    }

    public function testVerifyTooFewConditionalOutNodes()
    {
        $branch = new \Opensoft\Workflow\Nodes\ControlFlow\ExclusiveChoice();
        $branch->addInNode( new \Opensoft\Workflow\Nodes\Start() )
               ->addOutNode( new \Opensoft\Workflow\Nodes\End() )
               ->addOutNode( new \Opensoft\Workflow\Nodes\End() );

        try
        {
            $branch->verify();
        }
        catch ( \Opensoft\Workflow\Exception\InvalidWorkflowException $e )
        {
            $this->assertEquals( 'Node has less conditional outgoing nodes than required.', $e->getMessage() );
            return;
        }

        $this->fail( 'Expected an Opensoft\Workflow\Exception\InvalidWorkflowException to be thrown.' );
    }

    public function testActivatedFrom()
    {
        $node = new \Opensoft\Workflow\Nodes\Start();
        $this->assertEquals( array(), $node->getActivatedFrom() );
        $node->setActivatedFrom( array( TRUE ) );
        $this->assertEquals( array( TRUE ), $node->getActivatedFrom() );
    }

    public function testState()
    {
        $node = new \Opensoft\Workflow\Nodes\Start();
        $this->assertNull( $node->getState() );
        $node->setState( TRUE );
        $this->assertTrue( $node->getState() );
    }
}
