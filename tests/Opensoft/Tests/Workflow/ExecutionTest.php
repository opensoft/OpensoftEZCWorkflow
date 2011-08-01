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
class ExecutionTest extends WorkflowTestCase
{
    /**
     * @var \Opensoft\Tests\Workflow\Mocks\WorkflowTestExecution
     */
    protected $execution;


    protected function setUp()
    {
        parent::setUp();
        $this->execution = new \Opensoft\Tests\Workflow\Mocks\WorkflowTestExecution();
    }

    protected function tearDown()
    {
        $this->execution = NULL;
    }

    public function testExecuteStartEnd()
    {
        $this->setUpStartEnd();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();

        $this->assertFalse( $this->execution->isCancelled() );
        $this->assertTrue( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );
    }

    public function testExecuteStartEndVariableHandler()
    {
        $this->setUpStartEndVariableHandler();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();

        $this->assertFalse( $this->execution->isCancelled() );
        $this->assertTrue( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );
        $this->assertEquals( 'bar', $this->execution->getVariable( 'foo' ) );
    }

    public function testExecuteStartInputEnd()
    {
        $this->setUpStartInputEnd();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->setInputVariable( 'variable', 'value' );
        $this->execution->start();

        $this->assertFalse( $this->execution->isCancelled() );
        $this->assertTrue( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );
    }

    public function testExecuteStartInputEnd2()
    {
        $this->setUpStartInputEnd();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->setInputVariable( 'variable', false );

        try
        {
            $this->execution->start();
        }
        catch ( \Opensoft\Workflow\Exception\InvalidInputException $e )
        {
            $this->assertTrue( isset( $e->errors ) );
            $this->assertFalse( isset( $e->foo ) );
            $this->assertArrayHasKey( 'variable', $e->errors );
            $this->assertContains( 'is string', $e->errors );

            $this->assertFalse( $this->execution->isCancelled() );
            $this->assertFalse( $this->execution->hasEnded() );
            $this->assertTrue( $this->execution->isResumed() );
            $this->assertFalse( $this->execution->isSuspended() );

            return;
        }

        $this->fail( 'Expected an Opensoft\Workflow\Exception\InvalidInputException to be thrown.' );
    }

    public function testExecuteStartInputEnd3()
    {
        $this->setUpStartInputEnd();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->setInputVariable( 'variable', false );

        try
        {
            $this->execution->start();
        }
        catch ( \Opensoft\Workflow\Exception\InvalidInputException $e )
        {
            try
            {
                $e->errors = array();
            }
            catch ( \Exception $e )
            {
                return;
            }

            $this->fail( 'Expected an Exception to be thrown.' );
        }

        $this->fail( 'Expected an Opensoft\Workflow\Exception\InvalidInputException to be thrown.' );
    }

    public function testExecuteStartInputEnd4()
    {
        $this->setUpStartInputEnd();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->setInputVariable( 'variable', false );

        try
        {
            $this->execution->start();
        }
        catch ( \Opensoft\Workflow\Exception\InvalidInputException $e )
        {
            try
            {
                $foo = $e->foo;
            }
            catch ( \Exception $e )
            {
                return;
            }

            $this->fail( 'Expected an Exception to be thrown.' );
        }

        $this->fail( 'Expected an Opensoft\Workflow\Exception\InvalidInputException to be thrown.' );
    }

    public function testExecuteStartInputEnd5()
    {
        $this->setUpStartInputEnd();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->setInputVariable( 'variable', false );

        try
        {
            $this->execution->start();
        }
        catch ( \Opensoft\Workflow\Exception\InvalidInputException $e )
        {
            try
            {
                $e->foo = 'bar';
            }
            catch ( \Exception $e )
            {
                return;
            }

            $this->fail( 'Expected an Exception to be thrown.' );
        }

        $this->fail( 'Expected an Opensoft\Workflow\Exception\InvalidInputException to be thrown.' );
    }

    public function testExecuteStartInputEnd6()
    {
        $this->setUpStartInputEnd();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->setVariable( 'variable', false );

        try
        {
            $this->execution->start();
        }
        catch ( \Opensoft\Workflow\Exception\InvalidInputException $e )
        {
            $this->assertTrue( isset( $e->errors ) );
            $this->assertFalse( isset( $e->foo ) );
            $this->assertArrayHasKey( 'variable', $e->errors );
            $this->assertContains( 'is string', $e->errors );

            $this->assertFalse( $this->execution->isCancelled() );
            $this->assertFalse( $this->execution->hasEnded() );
            $this->assertFalse( $this->execution->isResumed() );
            $this->assertFalse( $this->execution->isSuspended() );

            return;
        }

        $this->fail( 'Expected an \Opensoft\Workflow\Execution\InvalidInputException to be thrown.' );
    }

    public function testExecuteStartSetUnsetEnd()
    {
        $this->setUpStartSetUnsetEnd();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();

        $this->assertFalse( $this->execution->isCancelled() );
        $this->assertTrue( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );
        $this->assertFalse( $this->execution->hasVariable( 'x' ) );
    }

    public function testExecuteStartSetUnsetEnd2()
    {
        $plugin = $this->getMock( 'Opensoft\\Workflow\\Execution\\Plugin\\ExecutionPlugin', array( 'beforeVariableUnset' ) );
        $plugin->expects( $this->any() )
               ->method( 'beforeVariableUnset' )
               ->will( $this->returnValue( false ) );

        $this->setUpStartSetUnsetEnd();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->addPlugin( $plugin );
        $this->execution->start();

        $this->assertFalse( $this->execution->isCancelled() );
        $this->assertTrue( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );
        $this->assertTrue( $this->execution->hasVariable( 'x' ) );
    }

    public function testExecuteIncrementingLoop()
    {
        $this->setUpLoop( 'increment' );
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();

        $this->assertFalse( $this->execution->isCancelled() );
        $this->assertTrue( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );
    }

    public function testExecuteDecrementingLoop()
    {
        $this->setUpLoop( 'decrement' );
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();

        $this->assertFalse( $this->execution->isCancelled() );
        $this->assertTrue( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );
    }

    public function testExecuteSetAddSubMulDiv()
    {
        $this->setUpSetAddSubMulDiv();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();

        $this->assertFalse( $this->execution->isCancelled() );
        $this->assertTrue( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );
        $this->assertEquals( 1, $this->execution->getVariable( 'x' ) );
    }

    public function testExecuteAddVariables()
    {
        $this->setUpAddVariables();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();

        $this->assertFalse( $this->execution->isCancelled() );
        $this->assertTrue( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );
        $this->assertEquals( 2, $this->execution->getVariable( 'b' ) );
    }

    public function testExecuteAddVariables2()
    {
        $this->setUpAddVariables2();
        $this->execution->setWorkflow($this->workflow);

        try
        {
            $this->execution->start();
        }
        catch ( \Exception $e )
        {
            $this->assertEquals( 'Illegal operand.', $e->getMessage() );
            return;
        }

        $this->fail( 'Expected an Exception to be thrown.' );
    }

    public function testExecuteAddVariables3()
    {
        $this->setUpAddVariables3();
        $this->execution->setWorkflow($this->workflow);

        try
        {
            $this->execution->start();
        }
        catch ( \Exception $e )
        {
            $this->assertEquals( 'Variable "b" is not a number.', $e->getMessage() );
            return;
        }

        $this->fail( 'Expected an Exception to be thrown.' );
    }

    public function testExecuteVariableEqualsVariable()
    {
        $this->setUpVariableEqualsVariable();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();

        $this->assertFalse( $this->execution->isCancelled() );
        $this->assertTrue( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );
        $this->assertEquals( 1, $this->execution->getVariable( 'c' ) );
    }

    public function testExecuteParallelSplitSynchronization()
    {
        $this->setUpParallelSplitSynchronization();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();

        $this->assertFalse( $this->execution->isCancelled() );
        $this->assertTrue( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );
    }

    public function testExecuteParallelSplitSynchronization2()
    {
        $this->setUpParallelSplitSynchronization2();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->setVariables( array( 'foo' => 'bar', 'bar' => 'foo' ) );
        $this->execution->start();

        $this->assertFalse( $this->execution->isCancelled() );
        $this->assertTrue( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );
    }

    public function testExecuteParallelSplitInvalidSynchronization()
    {
        $this->setUpParallelSplitInvalidSynchronization();
        $this->execution->setWorkflow($this->workflow);

        try
        {
            $this->execution->start();
        }
        catch ( \Exception $e )
        {
            $this->assertEquals(
              'Cannot synchronize threads that were started by different branches.',
              $e->getMessage()
            );

            return;
        }

        $this->fail( 'Expected an Exception to be thrown.' );
    }

    public function testExecuteExclusiveChoiceSimpleMerge()
    {
        $this->setUpExclusiveChoiceSimpleMerge();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->setVariables( array( 'condition' => true ) );
        $this->execution->start();

        $this->assertFalse( $this->execution->isCancelled() );
        $this->assertTrue( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );
    }

    public function testExecuteExclusiveChoiceSimpleMerge2()
    {
        $this->setUpExclusiveChoiceSimpleMerge();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->setVariables( array( 'condition' => false ) );
        $this->execution->start();

        $this->assertFalse( $this->execution->isCancelled() );
        $this->assertTrue( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );
    }

    public function testExecuteExclusiveChoiceSimpleMerge3()
    {
        $this->setUpExclusiveChoiceSimpleMerge( 'Opensoft\\Workflow\\Conditions\\IsTrue', 'Opensoft\\Workflow\\Conditions\\IsTrue' );
        $this->execution->setWorkflow($this->workflow);
        $this->execution->setVariables( array( 'condition' => false ) );

        try
        {
            $this->execution->start();
        }
        catch ( \Exception $e )
        {
            $this->assertEquals(
              'Node activates less conditional outgoing nodes than required.',
              $e->getMessage()
            );

            return;
        }

        $this->fail( 'Expected an Exception to be thrown.' );
    }

    public function testExecuteExclusiveChoiceWithElseSimpleMerge()
    {
        $this->setUpExclusiveChoiceWithElseSimpleMerge();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->setVariables( array( 'condition' => true ) );
        $this->execution->start();

        $this->assertFalse( $this->execution->isCancelled() );
        $this->assertTrue( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );
        $this->assertEquals( true, $this->execution->getVariable( 'x' ) );
    }

    public function testExecuteExclusiveChoiceWithElseSimpleMerge2()
    {
        $this->setUpExclusiveChoiceWithElseSimpleMerge();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->setVariables( array( 'condition' => false ) );
        $this->execution->start();

        $this->assertFalse( $this->execution->isCancelled() );
        $this->assertTrue( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );
        $this->assertEquals( true, $this->execution->getVariable( 'y' ) );
    }

    public function testExecuteExclusiveChoiceWithUnconditionalOutNodeSimpleMerge()
    {
        $this->setUpExclusiveChoiceWithUnconditionalOutNodeSimpleMerge();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->setVariables( array( 'condition' => false ) );
        $this->execution->start();

        $this->assertFalse( $this->execution->isCancelled() );
        $this->assertTrue( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );

        $this->assertTrue( $this->execution->getVariable( 'y' ) );
        $this->assertTrue( $this->execution->getVariable( 'z' ) );
    }

    public function testExecuteExclusiveChoiceWithUnconditionalOutNodeSimpleMerge2()
    {
        $this->setUpExclusiveChoiceWithUnconditionalOutNodeSimpleMerge();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->setVariables( array( 'condition' => true ) );
        $this->execution->start();

        $this->assertFalse( $this->execution->isCancelled() );
        $this->assertTrue( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );

        $this->assertTrue( $this->execution->getVariable( 'x' ) );
        $this->assertTrue( $this->execution->getVariable( 'z' ) );
    }

    public function testExecuteNestedExclusiveChoiceSimpleMerge()
    {
        $this->setUpNestedExclusiveChoiceSimpleMerge();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();

        $this->assertFalse( $this->execution->isCancelled() );
        $this->assertTrue( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );

        $this->assertTrue( $this->execution->getVariable( 'x' ) );
        $this->assertTrue( $this->execution->getVariable( 'y' ) );
        $this->assertTrue( $this->execution->getVariable( 'z' ) );
    }

    public function testExecuteNestedExclusiveChoiceSimpleMerge2()
    {
        $this->setUpNestedExclusiveChoiceSimpleMerge( true, false );
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();

        $this->assertFalse( $this->execution->isCancelled() );
        $this->assertTrue( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );

        $this->assertTrue( $this->execution->getVariable( 'x' ) );
        $this->assertFalse( $this->execution->getVariable( 'y' ) );
        $this->assertFalse( $this->execution->getVariable( 'z' ) );
    }

    public function testExecuteNestedExclusiveChoiceSimpleMerge3()
    {
        $this->setUpNestedExclusiveChoiceSimpleMerge( false );
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();

        $this->assertFalse( $this->execution->isCancelled() );
        $this->assertTrue( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );

        $this->assertFalse( $this->execution->getVariable( 'x' ) );
        $this->assertFalse( $this->execution->getVariable( 'z' ) );
    }

    public function testExecuteMultiChoiceSynchronizingMerge()
    {
        $this->setUpMultiChoice( 'SynchronizingMerge' );
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();

        $this->assertFalse( $this->execution->isCancelled() );
        $this->assertTrue( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );
    }

    public function testExecuteMultiChoiceDiscriminator()
    {
        $this->setUpMultiChoice( 'Discriminator' );
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();

        $this->assertFalse( $this->execution->isCancelled() );
        $this->assertTrue( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );
    }

    public function testExecuteNonInteractiveSubWorkflow()
    {
        $this->setUpWorkflowWithSubWorkflow( 'StartEnd' );
        $this->execution->setDefinitionStorage($this->xmlStorage);
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();

        $this->assertFalse( $this->execution->isCancelled() );
        $this->assertTrue( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );
    }

    public function testExecuteNonInteractiveSubWorkflow2()
    {
        $this->setUpWorkflowWithSubWorkflow( 'StartEnd' );
        $this->execution->setWorkflow($this->workflow);

        try
        {
            $this->execution->start();
        }
        catch ( \Exception $e )
        {
            $this->assertEquals(
              'No DefinitionStorageInterface implementation available.',
              $e->getMessage()
            );

            return;
        }

        $this->fail( 'Expected an Exception to be thrown.' );
    }

    public function testExecuteInteractiveSubWorkflow()
    {
        $this->setUpWorkflowWithSubWorkflow( 'StartInputEnd' );
        $this->execution->setDefinitionStorage($this->xmlStorage);
        $this->execution->setWorkflow($this->workflow);
        $this->execution->setInputVariableForSubWorkflow( 'variable', 'value' );
        $this->execution->start();

        $this->assertFalse( $this->execution->isCancelled() );
        $this->assertTrue( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );
    }

    public function testExecuteWorkflowWithCancelCaseSubWorkflow()
    {
        $this->setUpWorkflowWithSubWorkflow( 'ParallelSplitActionActionCancelCaseSynchronization' );
        $this->execution->setDefinitionStorage($this->xmlStorage);
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();

        $this->assertTrue( $this->execution->isCancelled() );
        $this->assertFalse( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );
    }

    public function testExecuteServiceObjectWithConstructor()
    {
        $this->workflow = $this->xmlStorage->loadByName( 'ServiceObjectWithArguments' );
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();

        $this->assertFalse( $this->execution->isCancelled() );
        $this->assertTrue( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );
    }

    public function testExecuteServiceObjectThatDoesNotFinish()
    {
        $this->workflow = $this->xmlStorage->loadByName( 'ServiceObjectThatDoesNotFinish' );
        $this->execution->setWorkflow($this->workflow);

        try
        {
            $this->execution->start();
        }
        catch( \Opensoft\Workflow\Exception\ExecutionException $e )
        {
            $this->assertEquals(
              'Workflow is waiting for input data that has not been mocked.',
              $e->getMessage()
            );

            return;
        }

        $this->fail( 'Expected an Opensoft\Workflow\Exception\ExecutionException to be thrown.' );
    }

    public function testExecuteNestedLoops()
    {
        $this->setUpNestedLoops();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();

        $this->assertFalse( $this->execution->isCancelled() );
        $this->assertTrue( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );

        $this->assertEquals( 2, $this->execution->getVariable( 'i' ) );
        $this->assertEquals( 2, $this->execution->getVariable( 'j' ) );
    }

    public function testExecuteWorkflowWithSubWorkflowAndVariablePassing()
    {
        $this->setUpWorkflowWithSubWorkflowAndVariablePassing();
        $this->execution->setDefinitionStorage($this->xmlStorage);
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();

        $this->assertFalse( $this->execution->isCancelled() );
        $this->assertTrue( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );

        $this->assertEquals( 1, $this->execution->getVariable( 'x' ) );
        $this->assertEquals( 2, $this->execution->getVariable( 'z' ) );
    }

    public function testExecuteParallelSplitCancelCaseActionActionSynchronization()
    {
        $this->setUpCancelCase( 'first' );
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();

        $this->assertTrue( $this->execution->isCancelled() );
        $this->assertFalse( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );
    }

    public function testExecuteParallelSplitActionActionCancelCaseSynchronization()
    {
        $this->setUpCancelCase( 'last' );
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();

        $this->assertTrue( $this->execution->isCancelled() );
        $this->assertFalse( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );
    }

    public function testExecuteWorkflowWithFinalActivitiesAfterCancellation()
    {
        $this->setUpWorkflowWithFinalActivitiesAfterCancellation();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();

        $this->assertTrue( $this->execution->getVariable( 'finalActivityExecuted' ) );
        $this->assertTrue( $this->execution->isCancelled() );
        $this->assertFalse( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );
    }

    public function testExecuteApprovalProcess()
    {
        $this->setUpApprovalProcess();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();

        $this->assertFalse( $this->execution->isCancelled() );
        $this->assertTrue( $this->execution->hasEnded() );
        $this->assertFalse( $this->execution->isResumed() );
        $this->assertFalse( $this->execution->isSuspended() );
    }

    public function testListener()
    {
        $listener = $this->getMock( 'Opensoft\\Workflow\\Execution\\ExecutionListenerInterface' );

        $this->assertFalse( $this->execution->removeListener( $listener ) );

        $this->assertTrue( $this->execution->addListener( $listener ) );
        $this->assertFalse( $this->execution->addListener( $listener ) );

        $this->assertTrue( $this->execution->removeListener( $listener ) );
        $this->assertFalse( $this->execution->removeListener( $listener ) );
    }

    public function testPlugin()
    {
        $plugin = $this->getMock( 'Opensoft\\Workflow\\Execution\\Plugin\\ExecutionPlugin' );

        $this->assertTrue( $this->execution->addPlugin( $plugin ) );
        $this->assertFalse( $this->execution->addPlugin( $plugin ) );

        $this->assertTrue( $this->execution->removePlugin( $plugin ) );
        $this->assertFalse( $this->execution->removePlugin( $plugin ) );
    }

    public function testNoWorkflowStartRaisesException()
    {
        $execution = new \Opensoft\Workflow\Execution\NonInteractive();

        try
        {
            $execution->start();
        }
        catch ( \Opensoft\Workflow\Exception\ExecutionException $e )
        {
            $this->assertEquals( 'No workflow has been set up for execution.', $e->getMessage() );
            return;
        }

        $this->fail( 'Expected an Opensoft\Workflow\Exception\ExecutionException to be thrown.' );
    }

    public function testNoExecutionIdResumeRaisesException()
    {
        $execution = new \Opensoft\Workflow\Execution\NonInteractive();

        try
        {
            $execution->resume();
        }
        catch ( \Opensoft\Workflow\Exception\ExecutionException $e )
        {
            $this->assertEquals( 'No execution id given.', $e->getMessage() );
            return;
        }

        $this->fail( 'Expected an Opensoft\Workflow\Exception\ExecutionException to be thrown.' );
    }

    public function testInteractiveWorkflowRaisesException()
    {
        $this->setupEmptyWorkflow();

        $input = new \Opensoft\Workflow\Nodes\Variables\Input( array( 'choice' => new \Opensoft\Workflow\Conditions\IsBool() ) );

        $this->workflow->getStartNode()->addOutNode( $input );
        $this->workflow->getEndNode()->addInNode( $input );

        $execution = new \Opensoft\Workflow\Execution\NonInteractive();

        try
        {
            $execution->setWorkflow($this->workflow);
        }
        catch ( \Opensoft\Workflow\Exception\ExecutionException $e )
        {
            $this->assertEquals( 'This executer can only execute workflows that have no Input and SubWorkflow nodes.', $e->getMessage() );
            return;
        }

        $this->fail( 'Expected an Opensoft\Workflow\Exception\ExecutionException to be thrown.' );
    }

    public function testGetVariable()
    {
        $this->setUpStartEnd();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();

        $this->assertFalse( $this->execution->hasVariable( 'foo' ) );

        try
        {
            $this->execution->getVariable( 'foo' );
        }
        catch ( \Opensoft\Workflow\Exception\ExecutionException $e )
        {
            $this->assertEquals( 'Variable "foo" does not exist.', $e->getMessage() );
            return;
        }

        $this->fail( 'Expected an Opensoft\Workflow\Exception\ExecutionException to be thrown.' );
    }

    public function testEndNonExistingThread()
    {
        try
        {
            $this->execution->endThread( 0 );
        }
        catch ( \Opensoft\Workflow\Exception\ExecutionException $e )
        {
            $this->assertEquals( 'There is no thread with id #0.', $e->getMessage() );
            return;
        }

        $this->fail( 'Expected an Opensoft\Workflow\Exception\ExecutionException to be thrown.' );
    }

    public function testGetSiblingsForNonExistingThread()
    {
        $this->assertFalse( $this->execution->getNumSiblingThreads( 0 ) );
    }
}

