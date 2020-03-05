<?php

namespace Esc\Tests;

use Esc\RequestParams;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RequestParamsTest extends TestCase
{
    public function testRequestParamsFiltersReturnException(): void
    {
        $request = $this->getMockBuilder(Request::class)
            ->getMock();
        $request->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('filters'), $this->equalTo(''))
            ->willReturn(null);

        $this->expectException(\Exception::class);
        RequestParams::fromRequest($request);
    }

    public function testRequestParamsFiltersResultBasedOnRequestFilters(): void
    {
        $expectedFilters = [
            'foo' => 'bar'
        ];
        $request = $this->getMockBuilder(Request::class)
            ->getMock();
        $request->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('filters'), $this->equalTo(''))
            ->willReturn('{"foo": "bar"}');

        $actualRequestParams = RequestParams::fromRequest($request);
        $actualFilters = $actualRequestParams->get('filters');
        $this->assertEquals($expectedFilters, $actualFilters);
    }

    public function testIfRequestParamsSetEmptyArrayWhenSortByIsNull(): void
    {
        $expectedValue = [];
        $request = $this->getMockBuilder(Request::class)
            ->getMock();

        $request->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('filters'), $this->equalTo(''))
            ->willReturn('{"foo": "bar"}');

        $request->expects($this->at(1))
            ->method('get')
            ->with($this->equalTo('sortBy'))
            ->willReturn(null);

        $requestParams = RequestParams::fromRequest($request);
        $requestParamsValue = $requestParams->get('sortBy');
        $this->assertEquals($expectedValue, $requestParamsValue);
    }

    public function testIfDescendingIsTrue(): void
    {
        $expectedValue =[
            'foo' => 'DESC',
        ];

        $request = $this->getMockBuilder(Request::class)
            ->getMock();

        $request->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('filters'), $this->equalTo(''))
            ->willReturn('{"foo": "bar"}');

        $request->expects($this->at(1))
            ->method('get')
            ->with($this->equalTo('sortBy'))
            ->willReturn('foo');

        $request->expects($this->at(2))
            ->method('get')
            ->with($this->equalTo('descending'))
            ->willReturn(true);

        $requestParams = RequestParams::fromRequest($request);
        $requestParamsValue = $requestParams->get('sortBy');
        $this->assertEquals($expectedValue, $requestParamsValue);
    }

    public function testIfDescendingIsFalse(): void
    {
        $expectedValue =[
            'foo' => 'ASC',
        ];

        $request = $this->getMockBuilder(Request::class)
            ->getMock();

        $request->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('filters'), $this->equalTo(''))
            ->willReturn('{"foo": "bar"}');

        $request->expects($this->at(1))
            ->method('get')
            ->with($this->equalTo('sortBy'))
            ->willReturn('foo');

        $request->expects($this->at(2))
            ->method('get')
            ->with($this->equalTo('descending'))
            ->willReturn(false);

        $requestParams = RequestParams::fromRequest($request);
        $requestParamsValue = $requestParams->get('sortBy');
        $this->assertEquals($expectedValue, $requestParamsValue);
    }

    public function testRowsPerPageAreEqualToLimitWhenRowsPerPageAreMoreThan0(): void
    {
        $request = $this->getMockBuilder(Request::class)
            ->getMock();

        $request->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('filters'), $this->equalTo(''))
            ->willReturn('{"foo": "bar"}');

        $request->expects($this->at(1))
            ->method('get')
            ->with($this->equalTo('sortBy'))
            ->willReturn('foo');

        $request->expects($this->at(2))
            ->method('get')
            ->with($this->equalTo('descending'))
            ->willReturn(false);

        $request->expects($this->at(3))
            ->method('get')
            ->with($this->equalTo('rowsPerPage'))
            ->willReturn(3);

        $requestParams = RequestParams::fromRequest($request);
        $requestParamsValue = $requestParams->get('limit');
        $this->assertEquals(3, $requestParamsValue);
    }

    public function testLimitIsNullWhenRowsPerPageAre0(): void
    {
        $request = $this->getMockBuilder(Request::class)
            ->getMock();

        $request->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('filters'), $this->equalTo(''))
            ->willReturn('{"foo": "bar"}');

        $request->expects($this->at(1))
            ->method('get')
            ->with($this->equalTo('sortBy'))
            ->willReturn('foo');

        $request->expects($this->at(2))
            ->method('get')
            ->with($this->equalTo('descending'))
            ->willReturn(false);

        $request->expects($this->at(3))
            ->method('get')
            ->with($this->equalTo('rowsPerPage'))
            ->willReturn(0);

        $requestParams = RequestParams::fromRequest($request);
        $requestParamsValue = $requestParams->get('limit');
        $this->assertEquals(null, $requestParamsValue);
    }

    public function testIfOffsetIsNot0WhenPageAreMoreThan0(): void
    {
        $request = $this->getMockBuilder(Request::class)
            ->getMock();

        $request->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('filters'), $this->equalTo(''))
            ->willReturn('{"foo": "bar"}');

        $request->expects($this->at(1))
            ->method('get')
            ->with($this->equalTo('sortBy'))
            ->willReturn('foo');

        $request->expects($this->at(2))
            ->method('get')
            ->with($this->equalTo('descending'))
            ->willReturn(false);

        $request->expects($this->at(3))
            ->method('get')
            ->with($this->equalTo('rowsPerPage'))
            ->willReturn(10);

        $request->expects($this->at(4))
            ->method('get')
            ->with($this->equalTo('page'))
            ->willReturn(2);

        $requestParams = RequestParams::fromRequest($request);
        $requestParamsValue = $requestParams->get('offset');
        $this->assertEquals(10, $requestParamsValue);
    }

    public function testIfOffsetIs0(): void
    {
        $request = $this->getMockBuilder(Request::class)
            ->getMock();

        $request->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('filters'), $this->equalTo(''))
            ->willReturn('{"foo": "bar"}');

        $request->expects($this->at(1))
            ->method('get')
            ->with($this->equalTo('sortBy'))
            ->willReturn('foo');

        $request->expects($this->at(2))
            ->method('get')
            ->with($this->equalTo('descending'))
            ->willReturn(false);

        $request->expects($this->at(3))
            ->method('get')
            ->with($this->equalTo('rowsPerPage'))
            ->willReturn(10);

        $request->expects($this->at(4))
            ->method('get')
            ->with($this->equalTo('page'))
            ->willReturn(1);

        $requestParams = RequestParams::fromRequest($request);
        $requestParamsValue = $requestParams->get('offset');
        $this->assertEquals(0, $requestParamsValue);
    }
}
