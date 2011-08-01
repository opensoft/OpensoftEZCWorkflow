<?php
/**
 * File containing the DefinitionStorageInterface interface.
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

namespace Opensoft\Workflow\DefinitionStorage;

use Opensoft\Workflow\Workflow;

/**
 * Interface for workflow definition storage handlers.
 *
 * @package Workflow
 * @version //autogen//
 */
interface DefinitionStorageInterface
{
    /**
     * Load a workflow definition by name.
     *
     * @param  string  $workflowName
     * @param  int $workflowVersion
     * @return \Opensoft\Workflow\Workflow
     * @throws DefinitionStorageException
     */
    public function loadByName( $workflowName, $workflowVersion = 0 );

    /**
     * Save a workflow definition to the database.
     *
     * @param  Workflow $workflow
     * @throws DefinitionStorageException
     */
    public function save( Workflow $workflow );
}

