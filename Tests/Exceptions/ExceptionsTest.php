<?php
/**
 * This file is part of DoctrineRestDriver.
 *
 * DoctrineRestDriver is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * DoctrineRestDriver is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with DoctrineRestDriver.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace DoctrineRestDriver\Tests\Exceptions;

use DoctrineRestDriver\Exceptions\Exceptions;
use DoctrineRestDriver\Types\Request;

/**
 * Tests the exceptions trait
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 *
 * @coversDefaultClass DoctrineRestDriver\Exceptions\Exceptions
 *
 * @SuppressWarnings("PHPMD.StaticAccess")
 */
class ExceptionsTest extends \PHPUnit_Framework_TestCase {

    /**
     * @test
     * @group  unit
     * @covers ::invalidTypeException
     * @expectedException \DoctrineRestDriver\Validation\Exceptions\InvalidTypeException
     */
    public function invalidTypeExceptionTest() {
        Exceptions::invalidTypeException('expected', 'key', 'value');
    }

    /**
     * @test
     * @group  unit
     * @covers ::notNilException
     * @expectedException \DoctrineRestDriver\Validation\Exceptions\NotNilException
     */
    public function notNilExceptionTest() {
        Exceptions::notNilException('test');
    }

    /**
     * @test
     * @group  unit
     * @covers ::unsupportedFetchModeException
     * @covers DoctrineRestDriver\Exceptions\UnsupportedFetchModeException::__construct
     * @expectedException \DoctrineRestDriver\Exceptions\UnsupportedFetchModeException
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function unsupportedFetchModeExceptionTest() {
        Exceptions::unsupportedFetchModeException(\PDO::FETCH_CLASS);
    }

    /**
     * @test
     * @group  unit
     * @covers ::methodNotImplementedException
     * @covers DoctrineRestDriver\Exceptions\MethodNotImplementedException::__construct
     * @expectedException \DoctrineRestDriver\Exceptions\MethodNotImplementedException
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function methodNotImplementedExceptionTest() {
        Exceptions::methodNotImplementedException('class', 'method');
    }

    /**
     * @test
     * @group  unit
     * @covers ::requestFailedException
     * @covers DoctrineRestDriver\Exceptions\RequestFailedException::__construct
     * @expectedException \DoctrineRestDriver\Exceptions\RequestFailedException
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function requestFailedExceptionTest() {
        Exceptions::requestFailedException(new Request('method', 'url', []), 1, 'errorMessage');
    }

    /**
     * @test
     * @group  unit
     * @covers ::invalidAuthStrategyException
     * @covers DoctrineRestDriver\Exceptions\InvalidAuthStrategyException::__construct
     * @expectedException \DoctrineRestDriver\Exceptions\InvalidAuthStrategyException
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function invalidAuthStrategyExceptionTest() {
        Exceptions::invalidAuthStrategyException('class');
    }

    /**
     * @test
     * @group  unit
     * @covers ::invalidSqlOperationException
     * @covers DoctrineRestDriver\Exceptions\InvalidSqlOperationException::__construct
     * @expectedException \DoctrineRestDriver\Exceptions\InvalidSqlOperationException
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function invalidSqlOperationExceptionTest() {
        Exceptions::invalidSqlOperationException('operation');
    }
}