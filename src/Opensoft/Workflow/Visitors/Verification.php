<?php
/**
 * File containing the Verification class.
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
use Opensoft\Workflow\Workflow;
use Opensoft\Workflow\Nodes\Start;
use Opensoft\Workflow\Nodes\Finally;
use Opensoft\Workflow\Nodes\Node;
use Opensoft\Workflow\Exception\InvalidWorkflowException;

/**
 * An implementation of the Visitor interface that
 * verifies a workflow specification.
 *
 * This visitor should not be used directly but will be used by the
 * verify() method on the workflow.
 *
 * <code>
 * <?php
 * $workflow->verify();
 * ?>
 * </code>
 *
 * The verifier checks that:
 * - there is only one start node
 * - there is only one finally node
 * - each node satisfies the constraints of the respective node type
 *
 * @package Workflow
 * @version //autogen//
 */
class Verification extends Visitor
{
    /**
     * Holds the number of start nodes encountered during visiting.
     *
     * @var integer
     */
    protected $numStartNodes = 0;

    /**
     * Holds the number of finally nodes encountered during visiting.
     *
     * @var integer
     */
    protected $numFinallyNodes = 0;

    /**
     * Perform the visit.
     *
     * @param VisitableInterface $visitable
     */
    protected function doVisit(VisitableInterface $visitable)
    {
        if ($visitable instanceof Workflow) {
            foreach ($visitable->getNodes() as $node) {
                if ($node instanceof Start && !$node instanceof Finally) {
                    $this->numStartNodes++;

                    if ($this->numStartNodes > 1) {
                        throw new InvalidWorkflowException('A workflow may have only one start node.');
                    }
                }

                if ( $node instanceof Finally )  {
                    $this->numFinallyNodes++;

                    if ( $this->numFinallyNodes > 1 ) {
                        throw new InvalidWorkflowException('A workflow may have only one finally node.');
                    }
                }
            }
        }

        if ($visitable instanceof Node)
        {
            $visitable->verify();
        }
    }
}

