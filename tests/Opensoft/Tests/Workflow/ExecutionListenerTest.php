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
class ExecutionListenerTest extends WorkflowTestCase
{
    /**
     * @var \Opensoft\Tests\Workflow\Mocks\WorkflowTestExecution
     */
    protected $execution;

    /**
     * @var \Opensoft\Workflow\Execution\ExecutionListenerInterface
     */
    protected $listener;


    protected function setUp()
    {
        parent::setUp();

        $this->execution = new \Opensoft\Tests\Workflow\Mocks\WorkflowTestExecution();
        $this->listener  = $this->getMock( 'Opensoft\\Workflow\\Execution\\ExecutionListenerInterface' );
        $this->execution->addListener( $this->listener );
    }

    protected function tearDown()
    {
        $this->execution = NULL;
        $this->listener  = NULL;
    }

    public function testEventsForStartEnd()
    {
        $this->setUpExpectations( 'StartEnd' );
        $this->setUpStartEnd();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();
    }

    public function testEventsForStartEndVariableHandler()
    {
        $this->setUpExpectations( 'StartEndVariableHandler' );
        $this->setUpStartEndVariableHandler();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();
    }

    public function testEventsForStartInputEnd()
    {
        $this->setUpExpectations( 'StartInputEnd' );
        $this->setUpStartInputEnd();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->setInputVariable( 'variable', 'value' );
        $this->execution->start();
    }

    public function testEventsForStartSetUnsetEnd()
    {
        $this->setUpExpectations( 'StartSetUnsetEnd' );
        $this->setUpStartSetUnsetEnd();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();
    }

    public function testEventsForIncrementingLoop()
    {
        $this->setUpExpectations( 'IncrementingLoop' );
        $this->setUpLoop( 'increment' );
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();
    }

    public function testEventsForDecrementingLoop()
    {
        $this->setUpExpectations( 'DecrementingLoop' );
        $this->setUpLoop( 'decrement' );
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();
    }

    public function testEventsForSetAddSubMulDiv()
    {
        $this->setUpExpectations( 'SetAddSubMulDiv' );
        $this->setUpSetAddSubMulDiv();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();
    }

    public function testEventsForAddVariables()
    {
        $this->setUpExpectations( 'AddVariables' );
        $this->setUpAddVariables();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();
    }

    public function testEventsForParallelSplitSynchronization()
    {
        $this->setUpExpectations( 'ParallelSplitSynchronization' );
        $this->setUpParallelSplitSynchronization();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();
    }

    public function testEventsForExclusiveChoiceSimpleMerge()
    {
        $this->setUpExpectations( 'ExclusiveChoiceSimpleMerge' );
        $this->setUpExclusiveChoiceSimpleMerge();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->setVariables( array( 'condition' => true ) );
        $this->execution->start();
    }

    public function testEventsForExclusiveChoiceWithUnconditionalOutNodeSimpleMerge()
    {
        $this->setUpExpectations( 'ExclusiveChoiceWithUnconditionalOutNodeSimpleMerge' );
        $this->setUpExclusiveChoiceWithUnconditionalOutNodeSimpleMerge();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->setVariables( array( 'condition' => false ) );
        $this->execution->start();
    }

    public function testEventsForNestedExclusiveChoiceSimpleMerge()
    {
        $this->setUpExpectations( 'NestedExclusiveChoiceSimpleMerge' );
        $this->setUpNestedExclusiveChoiceSimpleMerge();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();
    }

    public function testEventsForMultiChoiceSynchronizingMerge()
    {
        $this->setUpExpectations( 'MultiChoiceSynchronizingMerge' );
        $this->setUpMultiChoice( 'SynchronizingMerge' );
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();
    }

    public function testEventsForMultiChoiceDiscriminator()
    {
        $this->setUpExpectations( 'MultiChoiceDiscriminator' );
        $this->setUpMultiChoice( 'Discriminator' );
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();
    }

    public function testEventsForNestedLoops()
    {
        $this->setUpExpectations( 'NestedLoops' );
        $this->setUpNestedLoops();
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();
    }

    public function testEventsForParallelSplitCancelCaseActionActionSynchronization()
    {
        $this->setUpExpectations( 'ParallelSplitCancelCaseActionActionSynchronization' );
        $this->setUpCancelCase( 'first' );
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();
    }

    public function testEventsForParallelSplitActionActionCancelCaseSynchronization()
    {
        $this->setUpExpectations( 'ParallelSplitActionActionCancelCaseSynchronization' );
        $this->setUpCancelCase( 'last' );
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();
    }

    protected function setUpExpectations( $log )
    {
        $lines = file(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . $log . '.log');

        $index = 0;

        foreach ( array_map('rtrim', $lines ) as $line )
        {
            $line = explode( ' ', $line );
            unset( $line[0], $line[1], $line[2], $line[3], $line[4], $line[5] );
            $line = join( ' ', $line );
            $line = preg_replace('/execution #(\d)+/', 'execution #0', $line);
            $line = preg_replace('/instance #(\d)+/', 'instance #0', $line);
            $line = preg_replace('/node #(\d)+/', 'node #0', $line);

            $this->listener
                 ->expects( $this->at( $index ) )
                 ->method( 'notify' )
                 ->with( $this->equalTo( $line ) );

            $index++;
        }
    }
}
