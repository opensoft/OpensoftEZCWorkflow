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

namespace Opensoft\Tests\Workflow\Mocks;

use \Opensoft\Workflow\VariableHandlerInterface;
use \Opensoft\Workflow\Execution\Execution;

/**
 * @package Workflow
 * @subpackage Tests
 */
class WorkflowTestVariableHandler implements VariableHandlerInterface
{
    protected $storage = array( 'foo' => 'bar' );

    public function load(Execution $execution, $variableName )
    {
        return $this->storage[$variableName];
    }

    public function save(Execution $execution, $variableName, $value )
    {
        $this->storage[$variableName] = $value;
    }
}
