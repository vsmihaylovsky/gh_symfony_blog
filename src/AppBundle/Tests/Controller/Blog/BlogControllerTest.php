<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 1/18/16
 * Time: 10:10 PM
 */

namespace AppBundle\Tests\Controller\Blog;

use AppBundle\Tests\TestBaseWeb;

class BlogControllerTest extends TestBaseWeb
{
    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
//        $this->assertContains('Welcome to Symfony', $crawler->filter('#container h1')->text());
    }
}