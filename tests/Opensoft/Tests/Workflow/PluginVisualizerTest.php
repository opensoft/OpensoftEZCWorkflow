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
class PluginVisualizerTest extends \Opensoft\Tests\Workflow\WorkflowTestCase
{
    /**
     * @var \Opensoft\Tests\Workflow\Mocks\WorkflowTestExecution
     */
    protected $execution;

    /**
     * @var string
     */
    protected $tempDir;

    /**
     * @var \Opensoft\Workflow\Execution\Plugin\Visualizer
     */
    protected $visualizer;

    protected function setUp()
    {
        parent::setUp();

        $this->tempDir    = $this->createTempDir( 'ezcWorkflow_' );
        $this->visualizer = new \Opensoft\Workflow\Execution\Plugin\Visualizer( $this->tempDir );

        $this->execution = new \Opensoft\Tests\Workflow\Mocks\WorkflowTestExecution();
        $this->execution->addPlugin( $this->visualizer );
    }

    protected function tearDown()
    {
        $this->removeTempDir();
    }

    public function testVisualizeStartEnd()
    {
        $this->setUpStartEnd();
        $this->execution->setWorkflow($this->workflow);

        $this->visualizer->setIncludeVariables(false);

        $this->execution->start();

        $common   = DIRECTORY_SEPARATOR . 'StartEnd_000_%03d.dot';
        $expected = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . $common;
        $actual   = $this->tempDir      . $common;

        for ( $i = 1; $i <= 4; $i++ )
        {
            $this->assertFileEquals(
              sprintf( $expected, $i ), sprintf( $actual, $i )
            );
        }
    }

    public function testVisualizeIncrementingLoop()
    {
        $this->setUpLoop( 'increment' );
        $this->execution->setWorkflow($this->workflow);
        $this->execution->start();

        $common   = DIRECTORY_SEPARATOR . 'IncrementingLoop_000_%03d.dot';
        $expected = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . $common;
        $actual   = $this->tempDir      . $common;

        for ( $i = 1; $i <= 44; $i++ )
        {
            $this->assertFileEquals(
              sprintf( $expected, $i ), sprintf( $actual, $i )
            );
        }
    }
}
