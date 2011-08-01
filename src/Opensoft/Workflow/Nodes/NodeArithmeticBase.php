<?php
/**
 * File containing the AbstractNodeArithmeticBase class.
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

namespace Opensoft\Workflow\Nodes;

use Opensoft\Workflow\Execution\Execution;
use Opensoft\Workflow\Exception\ExecutionException;

/**
 * Base class for nodes that implement simple integer arithmetic.
 *
 * This class takes care of the configuration and setting and getting of
 * data. The data to manipulate is put into the $variable member. The manipulating
 * parameter is put into the member $value.
 *
 * Implementors must implement the method doExecute() and put the result of the
 * computation in $value member variable.
 *
 * @package Workflow
 * @version //autogen//
 */
abstract class NodeArithmeticBase extends Node
{
    /**
     * Contains the data to manipulate.
     *
     * @var mixed
     */
    protected $variable;

    /**
     * Contains the operand (if any).
     *
     * @var mixed
     */
    protected $operand = null;

    /**
     * Constructs a new action node with the configuration $configuration.
     *
     * Configuration format
     * <ul>
     * <li><b>String:</b> The name of the workflow variable to operate on.</li>
     *
     * <li><b>Array:</b>
     *   <ul>
     *     <li><i>name:</i>  The name of the workflow variable to operate on.</li>
     *     <li><i>operand:</i> Name of workflow variable or a numerical value.
     *           Not used by implementations without an operand.</li>
     *    </ul>
     *  </li>
     *  </ul>
     *
     * @param mixed $configuration
     * @throws DefinitionStorageException
     */
    public function __construct( $configuration )
    {
        parent::__construct( $configuration );
    }

    /**
     * Executes this node and returns true.
     *
     * Expects the configuration parameters 'name' the name of the workflow
     * variable to work on and the parameter 'value' the value to operate with
     * or the name of the workflow variable containing the value.
     *
     * @param AbstractExecution $execution
     * @return boolean
     * @ignore
     */
    public function execute(Execution $execution)
    {
        if ( is_array( $this->configuration ) )
        {
            $variableName = $this->configuration['name'];
        }
        else
        {
            $variableName = $this->configuration;
        }

        $this->variable = $execution->getVariable( $variableName );

        if ( !is_numeric( $this->variable ) )
        {
            throw new ExecutionException(
                sprintf(
                'Variable "%s" is not a number.',
                $variableName
                )
            );
        }

        if ( is_numeric( $this->configuration['operand'] ) )
        {
            $this->operand = $this->configuration['operand'];
        }

        else if ( is_string( $this->configuration['operand'] ) )
        {
            try
            {
                $operand = $execution->getVariable( $this->configuration['operand'] );

                if ( is_numeric( $operand ) )
                {
                    $this->operand = $operand;
                }
            }
            catch ( ExecutionException $e )
            {
            }
        }

        if ( $this->operand === null )
        {
            throw new ExecutionException( 'Illegal operand.' );
        }

        $this->doExecute();

        $execution->setVariable( $variableName, $this->variable );
        $this->activateNode( $execution, $this->outNodes[0] );

        return parent::execute( $execution );
    }

    /**
     * Implementors should perform the variable computation in this method.
     *
     * doExecute() is called automatically by execute().
     */
    abstract protected function doExecute();
}

