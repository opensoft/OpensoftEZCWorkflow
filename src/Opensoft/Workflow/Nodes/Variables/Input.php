<?php
/**
 * File containing the Input class.
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

namespace Opensoft\Workflow\Nodes\Variables;

use Opensoft\Workflow\Nodes\Node;
use Opensoft\Workflow\Conditions\ConditionInterface;
use Opensoft\Workflow\Execution\Execution;
use Opensoft\Workflow\Util;
use Opensoft\Workflow\DefinitionStorage\Xml;
use Opensoft\Workflow\Exception\Exception as WorkflowException;

/**
 * An object of the Input class represents an input (from the application) node.
 *
 * When the node is reached, the workflow engine will suspend the workflow execution if the
 * specified input data is not available (first activation). While the workflow is suspended,
 * the application that embeds the workflow engine may supply the input data and resume the workflow
 * execution (second activation of the input node). Input data is stored in a workflow variable.
 *
 * Incoming nodes: 1
 * Outgoing nodes: 1
 *
 * This example creates a simple workflow that expectes two input variables,
 * once which can be any value and another that can only be an integer between
 * one and ten.
 *
 * <code>
 * <?php
 * $workflow = new Workflow( 'Test' );
 *
 * $input = new Input(
 *   'mixedVar' => new IsAnything,
 *   'intVar'   => new And(
 *     array(
 *       new IsInteger,
 *       new ezcWorkflowConditionIsGreatherThan( 0 )
 *       new IsLessThan( 11 )
 *     )
 *   )
 * );
 *
 * $input->addOutNode( $workflow->endNode );
 * $workflow->startNode->addOutNode( $input );
 * ?>
 * </code>
 *
 * @package Workflow
 * @version //autogen//
 */
class Input extends Node
{
    /**
     * Constructs a new input node.
     *
     * An input node accepts an array of workflow variables to accept
     * and/or together with a condition on the variable if required.
     *
     * Each element in the configuration array must be either
     * <b>String:</b> The name of the workflow variable to require. No conditions.
     *
     * or
     * <ul>
     *   <li><i>Key:</i> The name of the workflow variable to require.</li>
     *   <li><i>Value:</i> An object of type ConditionInterface</li>
     *
     * </ul>
     *
     * @param mixed $configuration
     * @throws WorkflowException
     */
    public function __construct( $configuration = '' )
    {
        if ( !is_array( $configuration ) )
        {
            throw WorkflowException::propertyBaseValue('configuration', $configuration, 'array');
        }

        $tmp = array();

        foreach ( $configuration as $key => $value )
        {
            if ( is_int( $key ) )
            {
                if ( !is_string( $value ) )
                {
                    throw WorkflowException::propertyBaseValue('workflow variable name', $value, 'string');
                }

                $variable  = $value;
                $condition = new \Opensoft\Workflow\Conditions\IsAnything();
            }
            else
            {
                if ( !is_object( $value ) || !$value instanceof ConditionInterface )
                {
                    throw WorkflowException::propertyBaseValue('workflow variable condition', $value, 'ConditionInterface');
                }

                $variable  = $key;
                $condition = $value;
            }

            $tmp[$variable] = $condition;
        }

        parent::__construct( $tmp );
    }

    /**
     * Executes this node.
     *
     * @param AbstractExecution $execution
     * @return boolean true when the node finished execution,
     *                 and false otherwise
     * @ignore
     */
    public function execute(Execution $execution)
    {
        $variables  = $execution->getVariables();
        $canExecute = true;
        $errors     = array();

        foreach ( $this->configuration as $variable => $condition )
        {
            if ( !isset( $variables[$variable] ) )
            {
                $execution->addWaitingFor( $this, $variable, $condition );

                $canExecute = false;
            }

            else if ( !$condition->evaluate( $variables[$variable] ) )
            {
                $errors[$variable] = (string)$condition;
            }
        }

        if ( !empty( $errors ) )
        {
            throw new \Opensoft\Workflow\Exception\InvalidInputException( $errors );
        }

        if ( $canExecute )
        {
            $this->activateNode( $execution, $this->outNodes[0] );

            return parent::execute( $execution );
        }
        else
        {
            return false;
        }
    }

    /**
     * Generate node configuration from XML representation.
     *
     * @param DOMElement $element
     * @return array
     * @ignore
     */
    public static function configurationFromXML( \DOMElement $element )
    {
        $configuration = array();

        foreach ( $element->getElementsByTagName( 'variable' ) as $variable )
        {
            $configuration[$variable->getAttribute( 'name' )] = Xml::xmlToCondition(
              Util::getChildNode( $variable )
            );
        }

        return $configuration;
    }

    /**
     * Generate XML representation of this node's configuration.
     *
     * @param DOMElement $element
     * @ignore
     */
    public function configurationToXML( \DOMElement $element )
    {
        foreach ( $this->configuration as $variable => $condition )
        {
            $xmlVariable = $element->appendChild(
              $element->ownerDocument->createElement( 'variable' )
            );

            $xmlVariable->setAttribute( 'name', $variable );

            $xmlVariable->appendChild(
              Xml::conditionToXml(
                $condition, $element->ownerDocument
              )
            );
        }
    }
}

