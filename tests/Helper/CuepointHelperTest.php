<?php


namespace Mi\Bundle\WowzaGuzzleClientBundle\Helper\Tests;

use GuzzleHttp\Psr7\Response;
use Mi\Bundle\WowzaGuzzleClientBundle\Helper\CuepointHelper;

/**
 * @author Jan Arnold <jan.arnold@movingimage.com>
 *
 */
class CuepointHelperTest extends \PHPUnit_Framework_TestCase
{
    /**@var CuepointHelper $obj */
    private $obj;

    public function setUp()
    {
        $this->obj = new CuepointHelper();
    }

    /**
     * @test
     */
    public function buildUrlTest()
    {
        $data     = [
            'wowzaProtocol' => 'http',
            'wowzaHostname' => 'host',
            'wowzaDvrPort'  => '123',
            'wowzaApp'      => 'app',
            'streamname'    => 'stream',
            'recordingname' => 'recording',
            'text'          => 'cuepointfoo'
        ];
        $result   = $this->obj->buildUrl('foo', $data);
        $expected = 'http://host:123/foo?app=app&streamname=stream&text=cuepointfoo';

        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function parseResponseTest()
    {
        $data     = [
            'text' => 'cuepointfoo'
        ];
        $response = new Response(200, ['foo' => 'bar']);
        $result   = $this->obj->parseResponse($response, $data);
        $this->assertEquals(
            [
                'code'    => 200,
                'message' => 'cuepointfoo'
            ],
            $result
        );

        $response = new Response('404', ['foo' => 'bar']);
        $result   = $this->obj->parseResponse($response, $data);
        $this->assertEquals(
            [
                'code'    => 404,
                'message' => 'Something went wrong'
            ],
            $result
        );

        $response = new Response(400, ['foo' => 'bar'], 'baz');
        $result   = $this->obj->parseResponse($response, $data);
        $this->assertEquals(
            [
                'code'    => 400,
                'message' => 'Bad Request'
            ]
            ,
            $result
        );
    }
}