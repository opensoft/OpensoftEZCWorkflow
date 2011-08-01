<?php
/**
 * File containing the ServiceObjectWithConstructor class.
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

namespace Opensoft\Tests\Workflow\Mocks;

use \Opensoft\Workflow\ServiceObjectInterface;

/**
 * A service object that has a constructor.
 *
 * @package Workflow
 * @subpackage Tests
 * @version //autogen//
 */
class ServiceObjectWithConstructor implements ServiceObjectInterface
{
    /**
     * Constructor.
     */
    public function __construct()
    {
    }

    /**
     * Executes the business logic of this service object.
     *
     * @param ezcWorkflowExecution $execution
     * @return boolean $executionFinished
     */
    public function execute( \Opensoft\Workflow\Execution\Execution $execution )
    {
        return true;
    }

    /**
     * Returns a textual representation of this service object.
     *
     * @return string
     */
    public function __toString()
    {
        return '';
    }
}
