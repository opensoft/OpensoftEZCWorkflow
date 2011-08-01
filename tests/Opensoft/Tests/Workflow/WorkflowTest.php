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
class WorkflowTest extends WorkflowTestCase
{
    /**
     * @var \Opensoft\Workflow\Workflow
     */
    protected $workflow;
    protected $startNode;
    protected $endNode;

    public function testWorkflowNameCanBeRetrieved()
    {
        $this->setUpStartEnd();
        $this->assertEquals( 'StartEnd', $this->workflow->getName() );
    }

    public function testWorkflowNameCanBeRetrievedAndSet()
    {
        $workflow = new \Opensoft\Workflow\Workflow( 'Test' );
        $this->assertEquals( 'Test', $workflow->getName() );

        $workflow->setName('Test2');
        $this->assertEquals( 'Test2', $workflow->getName() );
    }

    public function testWorkflowIdCanBeRetrievedAndSet()
    {
        $this->setUpStartEnd();
        $this->assertNull( $this->workflow->getId() );

        $this->workflow->setId(1);

        $this->assertEquals( 1, $this->workflow->getId() );
    }

    public function testWorkflowDefinitionCanBeRetrievedAndSet()
    {
        $this->setUpStartEnd();
        $this->assertNull( $this->workflow->getDefinitionStorage() );

        $this->workflow->setDefinitionStorage($this->xmlStorage);
        $this->assertNotNull( $this->workflow->getDefinitionStorage() );
    }

    public function testWorkflowNodesCanBeRetrieved()
    {
        $this->setUpStartEnd();
        $nodes = $this->workflow->getNodes();

        $this->assertSame( $this->workflow->getStartNode(), $nodes[1] );
        $this->assertSame( $this->workflow->getEndNode(), $nodes[2] );
    }

    public function testWhetherOrNotAWorkflowHasSubworkflowsCanBeDetermined()
    {
        $this->setUpStartEnd();
        $this->assertFalse( $this->workflow->hasSubWorkflows() );

        $this->setUpWorkflowWithSubWorkflow( 'StartEnd' );
        $this->assertTrue( $this->workflow->hasSubWorkflows() );
    }

    public function testWhetherOrNotAWorkflowIsInteractiveCanBeDetermined()
    {
        $this->setUpStartEnd();
        $this->assertFalse( $this->workflow->isInteractive() );
    }

    public function testValidityOfAWorkflowCanBeVerified()
    {
        $this->setUpStartEnd();
        $this->workflow->verify();
    }

    public function testWorkflowWithoutAStartNodeIsInvalid()
    {
        $workflow = new \Opensoft\Workflow\Workflow( 'Test' );

        try {
            $workflow->verify();
        } catch ( \Opensoft\Workflow\Exception\InvalidWorkflowException $e ) {
            $this->assertEquals( 'Node of type "Opensoft\\Workflow\\Nodes\\Start" has less outgoing nodes than required.', $e->getMessage() );
            return;
        }

        $this->fail( 'Expected an Opensoft\Workflow\Exception\InvalidWorkflowException to be thrown.' );
    }

    public function testWorkflowWithMoreThanOneStartNodeIsInvalid()
    {
        $workflow = new \Opensoft\Workflow\Workflow( 'Test' );
        $workflow->getStartNode()->addOutNode( new \Opensoft\Workflow\Nodes\Start() );

        try {
            $workflow->verify();
        } catch ( \Opensoft\Workflow\Exception\InvalidWorkflowException $e ) {
            $this->assertEquals( 'A workflow may have only one start node.', $e->getMessage() );
            return;
        }

        $this->fail( 'Expected an Opensoft\Workflow\Exception\InvalidWorkflowException to be thrown.' );
    }

    public function testWorkflowWithMoreThanOneFinallyNodeIsInvalid()
    {
        $workflow = new \Opensoft\Workflow\Workflow( 'Test' );
        $workflow->getFinallyNode()->addOutNode( new \Opensoft\Workflow\Nodes\Finally() );

        try
        {
            $workflow->verify();
        }
        catch ( \Opensoft\Workflow\Exception\InvalidWorkflowException $e )
        {
            $this->assertEquals( 'A workflow may have only one finally node.', $e->getMessage() );
            return;
        }

        $this->fail( 'Expected an Opensoft\Workflow\Exception\InvalidWorkflowException to be thrown.' );
    }

    public function testVariableHandler()
    {
        $this->setUpStartEnd();

        $this->assertFalse( $this->workflow->removeVariableHandler( 'foo' ) );

        $this->workflow->setVariableHandlers(
          array( 'foo' => 'Opensoft\\Tests\\Workflow\\Mocks\\VariableHandlerMock' )
        );

        $this->assertTrue( $this->workflow->removeVariableHandler( 'foo' ) );
    }

    public function testVariableHandler2()
    {
        $this->setUpStartEnd();

        try
        {
            $this->workflow->addVariableHandler( 'foo', '\stdClass' );
        }
        catch ( \Opensoft\Workflow\Exception\InvalidWorkflowException $e )
        {
            $this->assertEquals( 'Class "\stdClass" does not implement the VariableHandlerInterface interface.', $e->getMessage() );
            return;
        }

        $this->fail( 'Expected an Opensoft\Workflow\Exception\InvalidWorkflowException to be thrown.' );
    }

    public function testVariableHandler3()
    {
        $this->setUpStartEnd();

        try
        {
            $this->workflow->addVariableHandler( 'foo', 'NotExisting' );
        }
        catch ( \Opensoft\Workflow\Exception\InvalidWorkflowException $e )
        {
            $this->assertEquals( 'Class "NotExisting" not found.', $e->getMessage() );
            return;
        }

        $this->fail( 'Expected an Opensoft\Workflow\Exception\InvalidWorkflowException to be thrown.' );
    }



    public function testForIssue14451()
    {
        $this->workflow = new \Opensoft\Workflow\Workflow( 'Test' );

        $this->assertEquals( 1, count( $this->workflow ) );
        $this->assertEquals( 1, count( $this->workflow->getNodes() ) );

        $this->workflow->getStartNode()->addOutNode( $this->workflow->getEndNode() );

        $this->assertEquals( 2, count( $this->workflow ) );
        $this->assertEquals( 2, count( $this->workflow->getNodes() ) );

        $this->workflow->getStartNode()->removeOutNode( $this->workflow->getEndNode() );

        $this->assertEquals( 1, count( $this->workflow ) );
        $this->assertEquals( 1, count( $this->workflow->getNodes() ) );

        $input = new \Opensoft\Workflow\Nodes\Variables\Input( array( 'value' => new \Opensoft\Workflow\Conditions\IsInteger() ) );
        $this->workflow->getStartNode()->addOutNode( $input );

        $this->assertEquals( 2, count( $this->workflow ) );
        $this->assertEquals( 2, count( $this->workflow->getNodes() ) );

        $choice = new \Opensoft\Workflow\Nodes\ControlFlow\ExclusiveChoice();
        $input->addOutNode( $choice );

        $this->assertEquals( 3, count( $this->workflow ) );
        $this->assertEquals( 3, count( $this->workflow->getNodes() ) );

        $branch1 = new \Opensoft\Workflow\Nodes\Variables\Input( array( 'value' => new \Opensoft\Workflow\Conditions\IsAnything() ) );
        $branch2 = new \Opensoft\Workflow\Nodes\Variables\Input( array( 'value' => new \Opensoft\Workflow\Conditions\IsAnything() ) );

        $choice->addConditionalOutNode( new \Opensoft\Workflow\Conditions\IsAnything() , $branch1 );

        $this->assertEquals( 4, count( $this->workflow ) );
        $this->assertEquals( 4, count( $this->workflow->getNodes() ) );

        $choice->addConditionalOutNode( new \Opensoft\Workflow\Conditions\IsAnything() , $branch2 );

        $this->assertEquals( 5, count( $this->workflow ) );
        $this->assertEquals( 5, count( $this->workflow->getNodes() ) );

        $merge = new \Opensoft\Workflow\Nodes\ControlFlow\SimpleMerge();
        $merge->addInNode( $branch1 );
        $merge->addInNode( $branch2 );
        $merge->addOutNode( $this->workflow->getEndNode() );

        $this->assertEquals( 7, count( $this->workflow ) );
        $this->assertEquals( 7, count( $this->workflow->getNodes() ) );
    }
}

