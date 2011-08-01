<?php
/**
 * File containing the Xml class.
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

use Opensoft\Workflow\Exception\DefinitionStorageException;
use Opensoft\Workflow\Conditions\ConditionInterface;
use Opensoft\Workflow\Workflow;
use Opensoft\Workflow\Util;

/**
 * XML workflow definition storage handler.
 *
 * The definitions are stored inside the directory specified to the constructor with the name:
 * [workflowName]_[workflowVersion].xml where the name of the workflow has dots and spaces
 * replaced by '_'.
 *
 * @todo DTD for the XML file.
 * @package Workflow
 * @version //autogen//
 */
class Xml implements DefinitionStorageInterface
{
    /**
     * The directory that holds the XML files.
     *
     * @var string
     */
    protected $directory;

    /**
     * Constructs a new definition loader that loads definitions from $directory.
     *
     * $directory must contain the trailing '/'
     *
     * @param  string $directory The directory that holds the XML files.
     */
    public function __construct( $directory = '' )
    {
        $this->directory = $directory;
    }

    /**
     * Load a workflow definition from a file.
     *
     * When the $workflowVersion argument is omitted,
     * the most recent version is loaded.
     *
     * @param  string $workflowName
     * @param  int    $workflowVersion
     * @return Workflow
     * @throws DefinitionStorageException
     */
    public function loadByName( $workflowName, $workflowVersion = 0 )
    {
        if ( $workflowVersion == 0 )
        {
            // Load the latest version of the workflow definition by default.
            $workflowVersion = $this->getCurrentVersion( $workflowName );
        }

        $filename = $this->getFilename( $workflowName, $workflowVersion );

        // Load the document.
        $document = new \DOMDocument;

        if ( is_readable( $filename ) )
        {
            libxml_use_internal_errors( true );

            $loaded = @$document->load( $filename );

            if ( $loaded === false )
            {
                $message = '';

                foreach ( libxml_get_errors() as $error )
                {
                    $message .= $error->message;
                }

                throw new DefinitionStorageException(
                  sprintf(
                    'Could not load workflow "%s" (version %d) from "%s".%s',

                    $workflowName,
                    $workflowVersion,
                    $filename,
                    $message != '' ? "\n" . $message : ''
                  )
                );
            }
        }
        else
        {
            throw new DefinitionStorageException(
              sprintf(
                'Could not read file "%s".',
                $filename
              )
            );
        }

        return $this->loadFromDocument( $document );
    }

    /**
     * Load a workflow definition from a DOMDocument.
     *
     * @param  DOMDocument $document
     * @return Workflow
     */
    public function loadFromDocument( \DOMDocument $document )
    {
        $workflowName    = $document->documentElement->getAttribute( 'name' );
        $workflowVersion = (int) $document->documentElement->getAttribute( 'version' );

        // Create node objects.
        $nodes    = array();
        $xmlNodes = $document->getElementsByTagName( 'node' );

        foreach ( $xmlNodes as $xmlNode )
        {
            $id        = (int)$xmlNode->getAttribute( 'id' );
            $className = 'Opensoft\\Workflow\\Nodes\\' . $xmlNode->getAttribute( 'type' );

            if ( class_exists( $className ) )
            {
                $configuration = call_user_func_array(
                  array( $className, 'configurationFromXML' ), array( $xmlNode )
                );

                if ( is_null( $configuration ) )
                {
                    $configuration = Util::getDefaultConfiguration( $className );
                }
            }

            $node = new $className( $configuration );
            $node->setId( $id );

            if ( $node instanceof \Opensoft\Workflow\Nodes\Finally &&
                 !isset( $finallyNode ) )
            {
                $finallyNode = $node;
            }

            else if ( $node instanceof \Opensoft\Workflow\Nodes\End &&
                      !isset( $defaultEndNode ) )
            {
                $defaultEndNode = $node;
            }

            else if ( $node instanceof \Opensoft\Workflow\Nodes\Start )
            {
                $startNode = $node;
            }

            $nodes[$id] = $node;
        }

        if ( !isset( $startNode ) || !isset( $defaultEndNode ) )
        {
            throw new DefinitionStorageException(
              'Could not load workflow definition.'
            );
        }

        // Connect node objects.
        foreach ( $xmlNodes as $xmlNode )
        {
            $id        = (int)$xmlNode->getAttribute( 'id' );
            $className = 'Opensoft\\Workflow\\Nodes\\' . $xmlNode->getAttribute( 'type' );

            foreach ( $xmlNode->getElementsByTagName( 'outNode' ) as $outNode )
            {
                $nodes[$id]->addOutNode( $nodes[(int)$outNode->getAttribute( 'id' )] );
            }

            if ( is_subclass_of( $className, 'Opensoft\\Workflow\\Nodes\\NodeConditionalBranch' ) )
            {
                foreach ( Util::getChildNodes( $xmlNode ) as $childNode )
                {
                    if ( $childNode->tagName == 'condition' )
                    {
                        foreach ( $childNode->getElementsByTagName( 'else' ) as $elseNode )
                        {
                            foreach ( $elseNode->getElementsByTagName( 'outNode' ) as $outNode )
                            {
                                $elseId = (int)$outNode->getAttribute( 'id' );
                            }
                        }

                        $condition = self::xmlToCondition( $childNode );
                        $xpath     = new \DOMXPath( $childNode->ownerDocument );

                        foreach ( $xpath->query( 'outNode', $childNode ) as $outNode )
                        {
                            if ( !isset( $elseId ) )
                            {
                                $nodes[$id]->addConditionalOutNode(
                                  $condition,
                                  $nodes[(int)$outNode->getAttribute( 'id' )]
                                );
                            }
                            else
                            {
                                $nodes[$id]->addConditionalOutNode(
                                  $condition,
                                  $nodes[(int)$outNode->getAttribute( 'id' )],
                                  $nodes[$elseId]
                                );

                                unset( $elseId );
                            }
                        }
                    }
                }
            }
        }

        if ( !isset( $finallyNode ) ||
             count( $finallyNode->getInNodes() ) > 0 )
        {
            $finallyNode = null;
        }

        // Create workflow object and add the node objects to it.
        $workflow = new Workflow( $workflowName, $startNode, $defaultEndNode, $finallyNode );
        $workflow->setDefinitionStorage($this);
        $workflow->setVersion($workflowVersion);

        // Handle the variable handlers.
        foreach ( $document->getElementsByTagName( 'variableHandler' ) as $variableHandler )
        {
            $workflow->addVariableHandler(
              $variableHandler->getAttribute( 'variable' ),
              $variableHandler->getAttribute( 'class' )
            );
        }

        // Verify the loaded workflow.
        $workflow->verify();

        return $workflow;
    }

    /**
     * Save a workflow definition to a file.
     *
     * @param  Workflow $workflow
     * @throws DefinitionStorageException
     */
    public function save( Workflow $workflow )
    {
        $workflowVersion = $this->getCurrentVersion( $workflow->getName() ) + 1;
        $filename        = $this->getFilename( $workflow->getName(), $workflowVersion );
        $document        = $this->saveToDocument( $workflow, $workflowVersion );

        file_put_contents( $filename, $document->saveXML() );
    }

    /**
     * Save a workflow definition to a DOMDocument.
     *
     * @param  Workflow $workflow
     * @param  int         $workflowVersion
     * @return DOMDocument
     */
    public function saveToDocument( Workflow $workflow, $workflowVersion )
    {
        $document = new \DOMDocument( '1.0', 'UTF-8' );
        $document->formatOutput = true;

        $root = $document->createElement( 'workflow' );
        $document->appendChild( $root );

        $root->setAttribute( 'name', $workflow->getName() );
        $root->setAttribute( 'version', $workflowVersion );

        $nodes    = $workflow->getNodes();
        $numNodes = count( $nodes );

        // Workaround for foreach() bug in PHP 5.2.1.
        // http://bugs.php.net/bug.php?id=40608
        $keys = array_keys( $nodes );

        for ( $i = 0; $i < $numNodes; $i++ )
        {
            $id        = $keys[$i];
            $node      = $nodes[$id];
            $nodeClass = get_class( $node );

            $xmlNode = $document->createElement( 'node' );
            $xmlNode->setAttribute( 'id', $id );
            $xmlNode->setAttribute(
              'type',
              str_replace( 'Opensoft\\Workflow\\Nodes\\', '', get_class( $node ) )
            );

            $node->configurationToXML( $xmlNode );
            $root->appendChild( $xmlNode );

            $outNodes    = $node->getOutNodes();
            $_keys       = array_keys( $outNodes );
            $numOutNodes = count( $_keys );

            for ( $j = 0; $j < $numOutNodes; $j++ )
            {
                foreach ( $nodes as $outNodeId => $_node )
                {
                    if ( $_node === $outNodes[$_keys[$j]] )
                    {
                        break;
                    }
                }

                $xmlOutNode = $document->createElement( 'outNode' );
                $xmlOutNode->setAttribute( 'id', $outNodeId );

                if ( is_subclass_of( $nodeClass, 'Opensoft\\Workflow\\Nodes\\NodeConditionalBranch' ) &&
                      $condition = $node->getCondition( $outNodes[$_keys[$j]] ) )
                {
                    if ( !$node->isElse( $outNodes[$_keys[$j]] ) )
                    {
                        $xmlCondition = self::conditionToXml(
                          $condition,
                          $document
                        );

                        $xmlCondition->appendChild( $xmlOutNode );
                        $xmlNode->appendChild( $xmlCondition );
                    }
                    else
                    {
                        $xmlElse = $xmlCondition->appendChild( $document->createElement( 'else' ) );
                        $xmlElse->appendChild( $xmlOutNode );
                    }
                }
                else
                {
                    $xmlNode->appendChild( $xmlOutNode );
                }
            }
        }

        foreach ( $workflow->getVariableHandlers() as $variable => $class )
        {
            $variableHandler = $root->appendChild(
              $document->createElement( 'variableHandler' )
            );

            $variableHandler->setAttribute( 'variable', $variable );
            $variableHandler->setAttribute( 'class', $class );
        }

        return $document;
    }

    /**
     * "Convert" an ConditionInterface object into an DOMElement object.
     *
     * @param  ConditionInterface $condition
     * @param  DOMDocument $document
     * @return DOMElement
     */
    public static function conditionToXml( ConditionInterface $condition, \DOMDocument $document )
    {
        $xmlCondition = $document->createElement( 'condition' );

        $conditionClass = get_class( $condition );
        $conditionType  = str_replace( 'Opensoft\\Workflow\\Conditions\\', '', $conditionClass );

        $xmlCondition->setAttribute( 'type', $conditionType );

        switch ( $conditionClass )
        {
            case 'Opensoft\\Workflow\\Conditions\\Variable': {
                $xmlCondition->setAttribute( 'name', $condition->getVariableName() );

                $xmlCondition->appendChild(
                  self::conditionToXml( $condition->getCondition(), $document )
                );
            }
            break;

            case 'Opensoft\\Workflow\\Conditions\\Variables': {
                list( $variableNameA, $variableNameB ) = $condition->getVariableNames();

                $xmlCondition->setAttribute( 'a', $variableNameA );
                $xmlCondition->setAttribute( 'b', $variableNameB );

                $xmlCondition->appendChild(
                  self::conditionToXml( $condition->getCondition(), $document )
                );
            }
            break;

            case 'Opensoft\\Workflow\\Conditions\\BooleanAnd':
            case 'Opensoft\\Workflow\\Conditions\\BooleanOr':
            case 'Opensoft\\Workflow\\Conditions\\BooleanXor': {
                foreach ( $condition->getConditions() as $childCondition )
                {
                    $xmlCondition->appendChild(
                      self::conditionToXml( $childCondition, $document )
                    );
                }
            }
            break;

            case 'Opensoft\\Workflow\\Conditions\\Not': {
                $xmlCondition->appendChild(
                  self::conditionToXml( $condition->getCondition(), $document )
                );
            }
            break;

            case 'Opensoft\\Workflow\\Conditions\\IsEqual':
            case 'Opensoft\\Workflow\\Conditions\\IsEqualOrGreaterThan':
            case 'Opensoft\\Workflow\\Conditions\\IsEqualOrLessThan':
            case 'Opensoft\\Workflow\\Conditions\\IsGreaterThan':
            case 'Opensoft\\Workflow\\Conditions\\IsLessThan':
            case 'Opensoft\\Workflow\\Conditions\\IsNotEqual': {
                $xmlCondition->setAttribute( 'value', $condition->getValue() );
            }
            break;

            case 'Opensoft\\Workflow\\Conditions\\InArray': {
                $xmlCondition->appendChild(
                  self::variableToXml( $condition->getValue(), $document )
                );
            }
            break;
        }

        return $xmlCondition;
    }

    /**
     * "Convert" an DOMElement object into an ConditionInterface object.
     *
     * @param  DOMElement $element
     * @return ConditionInterface
     */
    public static function xmlToCondition( \DOMElement $element )
    {
        $class = 'Opensoft\\Workflow\\Conditions\\' . $element->getAttribute( 'type' );

        switch ( $class )
        {
            case 'Opensoft\\Workflow\\Conditions\\Variable': {
                return new $class(
                  $element->getAttribute( 'name' ),
                  self::xmlToCondition( Util::getChildNode( $element ) )
                );
            }
            break;

            case 'Opensoft\\Workflow\\Conditions\\Variables': {
                return new $class(
                  $element->getAttribute( 'a' ),
                  $element->getAttribute( 'b' ),
                  self::xmlToCondition( Util::getChildNode( $element ) )
                );
            }
            break;

            case 'Opensoft\\Workflow\\Conditions\\BooleanAnd':
            case 'Opensoft\\Workflow\\Conditions\\BooleanOr':
            case 'Opensoft\\Workflow\\Conditions\\BooleanXor': {
                $conditions = array();

                foreach ( Util::getChildNodes( $element ) as $childNode )
                {
                    if ( $childNode->tagName == 'condition' )
                    {
                        $conditions[] = self::xmlToCondition( $childNode );
                    }
                }

                return new $class( $conditions );
            }
            break;

            case 'Opensoft\\Workflow\\Conditions\\Not': {
                return new $class( self::xmlToCondition( Util::getChildNode( $element ) ) );
            }
            break;

            case 'Opensoft\\Workflow\\Conditions\\IsEqual':
            case 'Opensoft\\Workflow\\Conditions\\IsEqualOrGreaterThan':
            case 'Opensoft\\Workflow\\Conditions\\IsEqualOrLessThan':
            case 'Opensoft\\Workflow\\Conditions\\IsGreaterThan':
            case 'Opensoft\\Workflow\\Conditions\\IsLessThan':
            case 'Opensoft\\Workflow\\Conditions\\IsNotEqual': {
                return new $class( $element->getAttribute( 'value' ) );
            }
            break;

            case 'Opensoft\\Workflow\\Conditions\\InArray': {
                return new $class( self::xmlToVariable( Util::getChildNode( $element ) ) );
            }
            break;

            default: {
                return new $class;
            }
            break;
        }
    }

    /**
     * "Convert" a PHP variable into an DOMElement object.
     *
     * @param  mixed $variable
     * @param  DOMDocument $document
     * @return DOMElement
     */
    public static function variableToXml( $variable, \DOMDocument $document )
    {
        if ( is_array( $variable ) )
        {
            $xmlResult = $document->createElement( 'array' );

            foreach ( $variable as $key => $value )
            {
                $element = $document->createElement( 'element' );
                $element->setAttribute( 'key', $key );
                $element->appendChild( self::variableToXml( $value, $document ) );

                $xmlResult->appendChild( $element );
            }
        }

        if ( is_object( $variable ) )
        {
            $xmlResult = $document->createElement( 'object' );
            $xmlResult->setAttribute( 'class', '\\' . get_class( $variable ) );
        }

        if ( is_null( $variable ) )
        {
            $xmlResult = $document->createElement( 'null' );
        }

        if ( is_scalar( $variable ) )
        {
            $type = gettype( $variable );

            if ( is_bool( $variable ) )
            {
                $variable = $variable === true ? 'true' : 'false';
            }

            $xmlResult = $document->createElement( $type, $variable );
        }

        return $xmlResult;
    }

    /**
     * "Convert" an DOMElement object into a PHP variable.
     *
     * @param  DOMElement $element
     * @return mixed
     */
    public static function xmlToVariable( \DOMElement $element )
    {
        $variable = null;

        switch ( $element->tagName )
        {
            case 'array': {
                $variable = array();

                foreach ( $element->getElementsByTagName( 'element' ) as $element )
                {
                    $value = self::xmlToVariable( Util::getChildNode( $element ) );

                    if ( $element->hasAttribute( 'key' ) )
                    {
                        $variable[ (string)$element->getAttribute( 'key' ) ] = $value;
                    }
                    else
                    {
                        $variable[] = $value;
                    }
                }
            }
            break;

            case 'object': {
                $className = $element->getAttribute( 'class' );

                if ( $element->hasChildNodes() )
                {
                    $arguments = Util::getChildNodes(
                      Util::getChildNode( $element )
                    );

                    $constructorArgs = array();

                    foreach ( $arguments as $argument )
                    {
                        if ( $argument instanceof \DOMElement )
                        {
                            $constructorArgs[] = self::xmlToVariable( $argument );
                        }
                    }

                    $class    = new \ReflectionClass( $className );
                    $variable = $class->newInstanceArgs( $constructorArgs );
                }
                else
                {
                    $variable = new $className;
                }
            }
            break;

            case 'boolean': {
                $variable = $element->nodeValue == 'true' ? true : false;
            }
            break;

            case 'integer':
            case 'double':
            case 'string': {
                $variable = $element->nodeValue;

                settype( $variable, $element->tagName );
            }
        }

        return $variable;
    }

    /**
     * Returns the current version number for a given workflow name.
     *
     * @param  string $workflowName
     * @return integer
     */
    protected function getCurrentVersion( $workflowName )
    {
        $workflowName = $this->getFilesystemWorkflowName( $workflowName );
        $files = glob( $this->directory . $workflowName . '_*.xml' );

        if ( !empty( $files ) )
        {
            return (int)str_replace(
              array(
                $this->directory . $workflowName . '_',
                '.xml'
              ),
              '',
              $files[count( $files ) - 1]
            );
        }
        else
        {
            return 0;
        }
    }

    /**
     * Returns the filename with path for given workflow name and version.
     *
     * The name of the workflow file is of the format [workFlowName]_[workFlowVersion].xml
     *
     * @param  string  $workflowName
     * @param  int $workflowVersion
     * @return string
     */
    protected function getFilename( $workflowName, $workflowVersion )
    {
        return sprintf(
          '%s%s_%d.xml',

          $this->directory,
          $this->getFilesystemWorkflowName( $workflowName ),
          $workflowVersion
        );
    }

    /**
     * Returns a safe filesystem name for a given workflow.
     *
     * This method replaces whitespace and '.' with '_'.
     *
     * @param  string $workflowName
     * @return string
     */
    protected function getFilesystemWorkflowName( $workflowName )
    {
        return preg_replace( '#[^\w.]#', '_', $workflowName );
    }

}

