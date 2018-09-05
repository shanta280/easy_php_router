<?php
namespace Shantanu\Test;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class EasyRouterTest extends TestCase {

    public function getRoutesToTest() {
        return [
            "GET /" => ["/", 200],
            "GET /" => ["/index.php", 200],
            "GET /about/{name}" => ["/index.php/about/shantanu", 200],
            "POST URL /contact/" => ["/index.php/contact", 405] 
        ];
    }
    
    /** 
    * @dataProvider getRoutesToTest
    */
    public function testGetRoutes($path, $expected) {
        $client = new Client();
        
        $response = $client->get("http://localhost:8989".$path);
        $this->assertTrue($response->getStatusCode() == $expected);
    }
}