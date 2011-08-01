<?php
/**
 * File containing the NodeCollector class.
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

use Opensoft\Workflow\Workflow;
use Opensoft\Workflow\Visitors\VisitableInterface;
use Opensoft\Workflow\Nodes\Node;

/**
 * Collects all the nodes in a workflow in an array.
 *
 * @package Workflow
 * @version //autogen//
 * @ignore
 */
class NodeCollector extends Visitor
{
    /**
     * Holds the start node object.
     *
     * @var Start
     */
    protected $startNode;

    /**
     * Holds the default end node object.
     *
     * @var End
     */
    protected $endNode;

    /**
     * Holds the finally node object.
     *
     * @var Finally
     */
    protected $finallyNode;

    /**
     * Flag that indicates whether the finally node has been visited.
     *
     * @var boolean
     */
    protected $finallyNodeVisited = false;

    /**
     * Holds the visited nodes.
     *
     * @var array(integer=>AbstractNode)
     */
    protected $nodes = array();

    /**
     * Holds the sequence of node ids.
     *
     * @var integer
     */
    protected $nextId = 0;

    /**
     * Flag that indicates whether the node list has been sorted.
     *
     * @var boolean
     */
    protected $sorted = false;

    /**
     * Constructor.
     *
     * @param Workflow $workflow
     */
    public function __construct( Workflow $workflow )
    {
        parent::__construct();
        $workflow->accept( $this );
    }

    /**
     * Perform the visit.
     *
     * @param VisitableInterface $visitable
     */
    protected function doVisit( VisitableInterface $visitable )
    {
        if ( $visitable instanceof Workflow ) {
            $visitable->getStartNode()->setId( ++$this->nextId );
            $this->startNode = $visitable->getStartNode();

            $visitable->getEndNode()->setId( ++$this->nextId );
            $this->endNode = $visitable->getEndNode();

            if ( count( $visitable->getFinallyNode()->getOutNodes() ) > 0 ) {
                $this->finallyNode = $visitable->getFinallyNode();
                $visitable->getFinallyNode()->setId( ++$this->nextId );
            }
        } else if ( $visitable instanceof Node ) {
            if ($visitable !== $this->startNode && $visitable !== $this->endNode && $visitable !== $this->finallyNode) {
                $id = ++$this->nextId;
                $visitable->setId( $id );
            } else {
                $id = $visitable->getId();
            }

            $this->nodes[$id] = $visitable;
        }
    }

    /**
     * Returns the collected nodes.
     *
     * @return array
     */
    public function getNodes()
    {
        if ($this->finallyNode !== null && !$this->finallyNodeVisited) {
            $this->finallyNode->accept($this);
            $this->finallyNode = true;
        }

        if (!$this->sorted) {
            ksort($this->nodes);
            $this->sorted = true;
        }

        return $this->nodes;
    }
}

