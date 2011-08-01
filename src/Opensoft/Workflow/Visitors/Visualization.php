<?php
/**
 * File containing the Visualization class.
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

use Opensoft\Workflow\Nodes\Node;
use Opensoft\Workflow\Visitors\VisitableInterface;
use Opensoft\Workflow\Workflow;
use Opensoft\Workflow\Nodes\NodeConditionalBranch;
use Opensoft\Workflow\Util;
use Opensoft\Workflow\Exception\Exception as WorkflowException;

/**
 * An implementation of the Visitor interface that
 * generates GraphViz/dot markup for a workflow definition.
 *
 * <code>
 * <?php
 * $visitor = new Visualization();
 * $workflow->accept( $visitor );
 * print $visitor;
 * ?>
 * </code>
 *
 * @package Workflow
 * @version //autogen//
 */
class Visualization extends Visitor
{
    /**
     * Holds the displayed strings for each of the nodes.
     *
     * @var array(string => string)
     */
    protected $nodes = array();

    /**
     * Holds all the edges of the graph.
     *
     * @var array( id => array( AbstractNode ) )
     */
    protected $edges = array();

    /**
     * Holds the name of the workflow.
     *
     * @var string
     */
    protected $workflowName = 'Workflow';

    /**
     * @var string
     */
    protected $colorHighlighted  = '#cc0000';

    /**
     * @var string
     */
    protected $colorNormal       = '#2e3436';

    /**
     * @var array
     */
    protected $highlightedNodes  = array();

    /**
     * @var array
     */
    protected $workflowVariables = array();

    /**
     * Perform the visit.
     *
     * @param VisitableInterface $visitable
     */
    protected function doVisit( VisitableInterface $visitable )
    {
        if ( $visitable instanceof Workflow ) {
            $this->workflowName = $visitable->getName();

            // The following line of code is not a no-op. It triggers the
            // Workflow::__get() method, thus initializing the respective
            // NodeCollector object.
            $visitable->getNodes();
        }

        if ( $visitable instanceof Node ) {
            $id = $visitable->getId();

            if ( in_array( $id, $this->highlightedNodes ) ) {
                $color = $this->colorHighlighted;
            } else {
                $color = $this->colorNormal;
            }

            if ( !isset( $this->nodes[$id] ) ) {
                $this->nodes[$id] = array(
                  'label' => (string)$visitable,
                  'color' => $color
                );
            }

            $outNodes = array();

            foreach ( $visitable->getOutNodes() as $outNode ) {
                $label = '';

                if ( $visitable instanceof NodeConditionalBranch ) {
                    $condition = $visitable->getCondition( $outNode );

                    if ( $condition !== false ) {
                        $label = ' [label="' . $condition . '"]';
                    }
                }

                $outNodes[] = array( $outNode->getId(), $label );
            }

            $this->edges[$id] = $outNodes;
        }
    }

    /**
     * Returns a the contents of a graphviz .dot file.
     *
     * @return boolean
     * @ignore
     */
    public function __toString()
    {
        $dot = 'digraph ' . $this->workflowName . " {\n";

        foreach ( $this->nodes as $key => $data ) {
            $dot .= sprintf(
              "node%s [label=\"%s\", color=\"%s\"]\n",
              $key,
              $data['label'],
              $data['color']
            );
        }

        $dot .= "\n";

        foreach ( $this->edges as $fromNode => $toNodes ) {
            foreach ( $toNodes as $toNode ) {
                $dot .= sprintf(
                  "node%s -> node%s%s\n",

                  $fromNode,
                  $toNode[0],
                  $toNode[1]
                );
            }
        }

        if (!empty($this->workflowVariables)) {
            $dot .= 'variables [shape=none, label=<<table>';

            foreach ($this->workflowVariables as $name => $value) {
                $dot .= sprintf(
                  '<tr><td>%s</td><td>%s</td></tr>',

                  $name,
                  htmlspecialchars( Util::variableToString( $value ) )
                );
            }

            $dot .= "</table>>]\n";
        }

        return $dot . "}\n";
    }

    public function setColorHighlighted($colorHighlighted)
    {
        $this->colorHighlighted = $colorHighlighted;
    }

    public function getColorHighlighted()
    {
        return $this->colorHighlighted;
    }

    public function setColorNormal($colorNormal)
    {
        $this->colorNormal = $colorNormal;
    }

    public function getColorNormal()
    {
        return $this->colorNormal;
    }

    public function setWorkflowVariables(array $workflowVariables)
    {
        foreach ($workflowVariables as $name => $value) {
            $this->addWorkflowVariable($name, $value);
        }
    }

    public function addWorkflowVariable($name, $value)
    {
        $this->workflowVariables[$name] = $value;
    }

    public function getWorkflowVariables()
    {
        return $this->workflowVariables;
    }

    /**
     * @param array $highlightedNodes
     */
    public function setHighlightedNodes($highlightedNodes)
    {
        $this->highlightedNodes = $highlightedNodes;
    }

    /**
     * @return array
     */
    public function getHighlightedNodes()
    {
        return $this->highlightedNodes;
    }

}

