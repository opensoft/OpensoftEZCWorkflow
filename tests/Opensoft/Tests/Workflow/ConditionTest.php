<?php
/**
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
 * @subpackage Tests
 * @version //autogentag//
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace Opensoft\Tests\Workflow;

/**
 * @package Workflow
 * @subpackage Tests
 */
class ConditionTest extends WorkflowTestCase
{
    public function testInArray()
    {
        $condition = new \Opensoft\Workflow\Conditions\InArray( array( '1', 2, 3 ) );
        $this->assertTrue( $condition->evaluate( 1 ) );
        $this->assertFalse( $condition->evaluate( 4 ) );
        $this->assertEquals( "in array('1', 2, 3)", (string)$condition );
    }

    public function testIsAnything()
    {
        $condition = new \Opensoft\Workflow\Conditions\IsAnything();

        $this->assertTrue( $condition->evaluate( null ) );
        $this->assertEquals( 'is anything', (string)$condition );
    }

    public function testIsArray()
    {
        $condition = new \Opensoft\Workflow\Conditions\IsArray();

        $this->assertTrue( $condition->evaluate( array() ) );
        $this->assertFalse( $condition->evaluate( null ) );
        $this->assertEquals( 'is array', (string)$condition );
    }

    public function testIsBool()
    {
        $condition = new \Opensoft\Workflow\Conditions\IsBool();

        $this->assertTrue( $condition->evaluate( true ) );
        $this->assertTrue( $condition->evaluate( false ) );
        $this->assertFalse( $condition->evaluate( null ) );
        $this->assertEquals( 'is bool', (string)$condition );
    }

    public function testIsTrue()
    {
        $condition = new \Opensoft\Workflow\Conditions\IsTrue();

        $this->assertTrue( $condition->evaluate( true ) );
        $this->assertFalse( $condition->evaluate( false ) );
        $this->assertEquals( 'is true', (string)$condition );
    }

    public function testIsFalse()
    {
        $condition = new \Opensoft\Workflow\Conditions\IsFalse();

        $this->assertFalse( $condition->evaluate( true ) );
        $this->assertTrue( $condition->evaluate( false ) );
        $this->assertEquals( 'is false', (string)$condition );
    }

    public function testIsFloat()
    {
        $condition = new \Opensoft\Workflow\Conditions\IsFloat();

        $this->assertTrue( $condition->evaluate( 0.0 ) );
        $this->assertFalse( $condition->evaluate( null ) );
        $this->assertEquals( 'is float', (string)$condition );
    }

    public function testIsInteger()
    {
        $condition = new \Opensoft\Workflow\Conditions\IsInteger();

        $this->assertTrue( $condition->evaluate( 0 ) );
        $this->assertFalse( $condition->evaluate( null ) );
        $this->assertEquals( 'is integer', (string)$condition );
    }

    public function testIsObject()
    {
        $condition = new \Opensoft\Workflow\Conditions\IsObject();

        $this->assertTrue( $condition->evaluate( new \stdClass() ) );
        $this->assertFalse( $condition->evaluate( null ) );
        $this->assertEquals( 'is object', (string)$condition );
    }

    public function testIsString()
    {
        $condition = new \Opensoft\Workflow\Conditions\IsString();

        $this->assertTrue( $condition->evaluate( '' ) );
        $this->assertFalse( $condition->evaluate( null ) );
        $this->assertEquals( 'is string', (string)$condition );
    }

    public function testIsEqual()
    {
        $condition = new \Opensoft\Workflow\Conditions\IsEqual( 2204 );

        $this->assertTrue( $condition->evaluate( 2204 ) );
        $this->assertFalse( $condition->evaluate( 1978 ) );
        $this->assertEquals( '== 2204', (string)$condition );
    }

    public function testIsNotEqual()
    {
        $condition = new \Opensoft\Workflow\Conditions\IsNotEqual( 2204 );

        $this->assertTrue( $condition->evaluate( 1978 ) );
        $this->assertFalse( $condition->evaluate( 2204 ) );
        $this->assertEquals( '!= 2204', (string)$condition );
    }

    public function testIsLessThan()
    {
        $condition = new \Opensoft\Workflow\Conditions\IsLessThan( 2204 );

        $this->assertTrue( $condition->evaluate( 1978 ) );
        $this->assertFalse( $condition->evaluate( 2204 ) );
        $this->assertEquals( '< 2204', (string)$condition );
    }

    public function testIsNotLessThan()
    {
        $condition = new \Opensoft\Workflow\Conditions\Not(
          new \Opensoft\Workflow\Conditions\IsLessThan( 2204 )
        );

        $this->assertTrue( $condition->evaluate( 2204 ) );
        $this->assertFalse( $condition->evaluate( 1978 ) );
        $this->assertEquals( '! < 2204', (string)$condition );
        $this->assertInstanceOf( 'Opensoft\Workflow\Conditions\IsLessThan', $condition->getCondition() );
    }

    public function testIsGreaterThan()
    {
        $condition = new \Opensoft\Workflow\Conditions\IsGreaterThan( 1978 );

        $this->assertTrue( $condition->evaluate( 2204 ) );
        $this->assertFalse( $condition->evaluate( 1978 ) );
        $this->assertEquals( '> 1978', (string)$condition );
    }

    public function testIsNotGreaterThan()
    {
        $condition = new \Opensoft\Workflow\Conditions\Not(
          new \Opensoft\Workflow\Conditions\IsGreaterThan( 1978 )
        );

        $this->assertTrue( $condition->evaluate( 1978 ) );
        $this->assertFalse( $condition->evaluate( 2204 ) );
        $this->assertEquals( '! > 1978', (string)$condition );
        $this->assertInstanceOf( 'Opensoft\Workflow\Conditions\IsGreaterThan', $condition->getCondition() );
    }

    public function testIsEqualOrGreaterThan()
    {
        $condition = new \Opensoft\Workflow\Conditions\IsEqualOrGreaterThan( 1 );

        $this->assertTrue( $condition->evaluate( 1 ) );
        $this->assertTrue( $condition->evaluate( 2 ) );
        $this->assertFalse( $condition->evaluate( 0 ) );
        $this->assertEquals( '>= 1', (string)$condition );
    }

    public function testIsEqualOrLessThan()
    {
        $condition = new \Opensoft\Workflow\Conditions\IsEqualOrLessThan( 1 );

        $this->assertTrue( $condition->evaluate( 1 ) );
        $this->assertTrue( $condition->evaluate( 0 ) );
        $this->assertFalse( $condition->evaluate( 2 ) );
        $this->assertEquals( '<= 1', (string)$condition );
    }

    public function testVariable()
    {
        $condition = new \Opensoft\Workflow\Conditions\Variable(
          'foo',
          new \Opensoft\Workflow\Conditions\IsAnything()
        );

        $this->assertTrue( $condition->evaluate( array( 'foo' => 'bar' ) ) );
        $this->assertFalse( $condition->evaluate( array( 'bar' => 'foo' ) ) );
    }

    public function testVariables()
    {
        $condition = new \Opensoft\Workflow\Conditions\Variables(
          'foo',
          'bar',
          new \Opensoft\Workflow\Conditions\IsEqual()
        );

        $this->assertTrue( $condition->evaluate( array( 'foo' => 'baz', 'bar' => 'baz' ) ) );
        $this->assertFalse( $condition->evaluate( array( 'foo' => 'bar', 'bar' => 'foo' ) ) );
    }

    public function testVariables2()
    {
        try
        {
            $condition = new \Opensoft\Workflow\Conditions\Variables(
              'foo',
              'bar',
              new \Opensoft\Workflow\Conditions\IsAnything()
            );
        }
        catch (\Exception $e)
        {
            $this->assertEquals( "The value 'O:39:\"Opensoft\\Workflow\\Conditions\\IsAnything\":0:{}' that you were trying to assign to setting 'condition' is invalid. Allowed values are: Comparison.", $e->getMessage() );
            return;
        }

        $this->fail( 'Expected an InvalidArgumentException to be thrown.' );
    }

    public function testVariables3()
    {
        $condition = new \Opensoft\Workflow\Conditions\Variables(
          'foo',
          'bar',
          new \Opensoft\Workflow\Conditions\IsEqual()
        );

        $this->assertFalse( $condition->evaluate( array() ) );
    }

    public function testAnd()
    {
        $true = new \Opensoft\Workflow\Conditions\IsTrue();

        $condition = new \Opensoft\Workflow\Conditions\BooleanAnd( array( $true, $true ) );
        $this->assertTrue( $condition->evaluate( true ) );
        $this->assertEquals( '( is true && is true )', (string)$condition );

        $condition = new \Opensoft\Workflow\Conditions\BooleanAnd( array( $true, $true ) );
        $this->assertFalse( $condition->evaluate( false ) );
    }

    public function testAnd2()
    {
        try
        {
            $condition = new \Opensoft\Workflow\Conditions\BooleanAnd( array( new \stdClass() ) );
        }
        catch ( \InvalidArgumentException $e )
        {
            $this->assertEquals( 'Array does not contain (only) ConditionInterface objects.', $e->getMessage() );
            return;
        }

        $this->fail( 'Expected a InvalidArgumentException to be thrown.' );
    }

    public function testOr()
    {
        $true  = new \Opensoft\Workflow\Conditions\IsTrue();
        $false = new \Opensoft\Workflow\Conditions\IsFalse();

        $condition = new \Opensoft\Workflow\Conditions\BooleanOr( array( $true, $true ) );
        $this->assertTrue( $condition->evaluate( true ) );
        $this->assertFalse( $condition->evaluate( false ) );
        $this->assertEquals( '( is true || is true )', (string)$condition );

        $condition = new \Opensoft\Workflow\Conditions\BooleanOr( array( $true, $false ) );
        $this->assertTrue( $condition->evaluate( true ) );
        $this->assertTrue( $condition->evaluate( false ) );
    }

    public function testXor()
    {
        $true  = new \Opensoft\Workflow\Conditions\IsTrue();
        $false = new \Opensoft\Workflow\Conditions\IsFalse();

        $condition = new \Opensoft\Workflow\Conditions\BooleanXor( array( $true, $false ) );
        $this->assertTrue( $condition->evaluate( true ) );
        $this->assertTrue( $condition->evaluate( false ) );
        $this->assertEquals( '( is true XOR is false )', (string)$condition );

        $condition = new \Opensoft\Workflow\Conditions\BooleanXor( array( $true, $true ) );
        $this->assertFalse( $condition->evaluate( true ) );
        $this->assertFalse( $condition->evaluate( false ) );
    }

}