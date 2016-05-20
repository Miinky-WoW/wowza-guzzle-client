<?php


namespace Mi\Bundle\WowzaGuzzleClientBundle\Helper\Tests;

use GuzzleHttp\Psr7\Response;
use Mi\Bundle\WowzaGuzzleClientBundle\Helper\WowzaRecordingHelper;
use Mi\Bundle\WowzaGuzzleClientBundle\Model\Recording\WowzaRecording;
use Mi\Bundle\WowzaGuzzleClientBundle\Model\WowzaConfig;

/**
 * @author Jan Arnold <jan.arnold@movingimage.com>
 *
 */
class RecordingHelperTest extends \PHPUnit_Framework_TestCase
{
    /**@var WowzaRecordingHelper $obj */
    private $obj;
    /**@var WowzaConfig $wowzaConfig*/
    private $wowzaConfig;

    public function setUp()
    {
        $this->obj = new WowzaRecordingHelper();
        $data     = [
            'wowza_protocol' => 'http',
            'wowza_hostname' => 'host',
            'wowza_dvr_port'  => '123',
            'wowza_app'      => 'app',
            'wowza_admin'         => 'foo',
            'wowza_admin_password' => 'bar'

        ];
        $this->wowzaConfig = new WowzaConfig($data);
    }

    /**
     * @test
     */
    public function buildUrlTest()
    {
        $recording = new WowzaRecording();
        $recording->setAction('startRecording');
        $recording->setStreamname('stream');
        $result   = $this->obj->buildUrl('foo', $this->wowzaConfig, $recording);
        $expected = 'http://host:123/foo?app=app&streamname=stream&action=startRecording';

        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function parseResponseTest()
    {
        $recording = new WowzaRecording();
        $recording->setAction('startRecording');
        $response = new Response(200, ['foo' => 'bar']);
        $result   = $this->obj->parseResponse($response, $recording);
        $this->assertEquals(
            [
                'code'    => 200,
                'message' => 'startRecording'
            ],
            $result
        );

        $response = new Response('404', ['foo' => 'bar']);
        $result   = $this->obj->parseResponse($response, $recording);
        $this->assertEquals(
            [
                'code'    => 404,
                'message' => 'Something went wrong'
            ],
            $result
        );

        $response = new Response(400, ['foo' => 'bar'], 'baz');
        $result   = $this->obj->parseResponse($response, $recording);
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