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
class VisitorVisualizationTest extends WorkflowTestCase
{
    /**
     * @var \Opensoft\Workflow\Visitors\Visualization
     */
    protected $visitor;


    protected function setUp()
    {
        parent::setUp();

        $this->visitor = new \Opensoft\Workflow\Visitors\Visualization();
    }

    /**
     * @dataProvider workflowNameProvider
     */
    public function testVisualizeWorkflow($workflowName)
    {
        $setupMethod = 'setUp' . $workflowName;

        $this->$setupMethod();
        $this->workflow->accept( $this->visitor );

        $this->assertEquals(
          $this->readExpected( $workflowName ),
          (string)$this->visitor
        );
    }

    public function testBug13467()
    {
        $this->workflow = $this->xmlStorage->loadByName( 'bug13467' );
        $this->workflow->accept( $this->visitor );

        $this->assertEquals(
          $this->readExpected( 'bug13467' ),
          (string)$this->visitor
        );
    }

    public function testHighlightedStartNode()
    {
        $this->visitor->setHighlightedNodes(array(1));

        $this->setUpStartEnd();
        $this->workflow->accept( $this->visitor );

        $this->assertEquals(
          $this->readExpected( 'StartEnd2' ),
          (string)$this->visitor
        );
    }

    protected function readExpected( $name )
    {
        return file_get_contents(
          dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR. $name . '.dot'
        );
    }
}