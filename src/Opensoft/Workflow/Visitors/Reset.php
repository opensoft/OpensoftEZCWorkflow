<?php
/**
 * File containing the Reset class.
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

namespace Opensoft\Workflow\Visitors;

use Opensoft\Workflow\Visitors\VisitableInterface;
use Opensoft\Workflow\Nodes\Node;

/**
 * An implementation of the Visitor interface that
 * resets all the nodes of a workflow.
 *
 * This visitor should not be used directly but will be used by the
 * reset() method on the workflow.
 *
 * <code>
 * <?php
 * $workflow->reset();
 * ?>
 * </code>
 *
 * @package Workflow
 * @version //autogen//
 */
class Reset extends Visitor
{
    /**
     * Perform the visit.
     *
     * @param VisitableInterface $visitable
     */
    protected function doVisit( VisitableInterface $visitable )
    {
        if ( $visitable instanceof Node )
        {
            $visitable->initState();
        }
    }
}

