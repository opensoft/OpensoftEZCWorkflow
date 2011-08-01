<?php
/**
 * File containing the Unset class.
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
use Opensoft\Workflow\Execution\Execution;
use Opensoft\Workflow\Exception\Exception as WorkflowException;

/**
 * An object of the Unset class unset the specified workflow variable.
 *
 * <code>
 * <?php
 * $unset = new Unset( 'variable name' );
 * ?>
 * </code>
 *
 * Incoming nodes: 1
 * Outgoing nodes: 1
 *
 * @package Workflow
 * @version //autogen//
 */
class UnsetVar extends Node
{
    /**
     * Constructs a new unset node.
     *
     * Configuration format:
     * String:
     *    The name of the workflow variable to unset.
     *
     * Array:
     *    An array of names of the workflow variables to unset.
     *
     * @param mixed $configuration
     * @throws WorkflowException
     */
    public function __construct( $configuration = '' )
    {
        if ( is_string( $configuration ) )
        {
            $configuration = array( $configuration );
        }

        if ( !is_array( $configuration ) )
        {
            throw WorkflowException::propertyBaseValue('configuration', $configuration, 'array');
        }

        parent::__construct( $configuration );
    }

    /**
     * Executes this node.
     *
     * @param AbstractExecution $execution
     * @return boolean true when the node finished execution,
     *                 and false otherwise
     * @ignore
     */
    public function execute( Execution $execution )
    {
        foreach ( $this->configuration as $variable )
        {
            $execution->unsetVariable( $variable );
        }

        $this->activateNode( $execution, $this->outNodes[0] );

        return parent::execute( $execution );
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
            $configuration[] = $variable->getAttribute( 'name' );
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
        foreach ( $this->configuration as $variable )
        {
            $variableXml = $element->appendChild(
              $element->ownerDocument->createElement( 'variable' )
            );

            $variableXml->setAttribute( 'name', $variable );
        }
    }

    /**
     * Returns a textual representation of this node.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        return 'unset(' . implode( ', ', $this->configuration ) . ')';
    }
}

