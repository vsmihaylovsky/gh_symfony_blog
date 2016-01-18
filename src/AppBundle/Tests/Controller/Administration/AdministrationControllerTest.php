<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 1/18/16
 * Time: 10:01 PM
 */

namespace AppBundle\Tests\Controller\Administration;

use AppBundle\Tests\TestBaseWeb;

class AdministrationControllerTest extends TestBaseWeb
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/administration/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
//        $this->assertContains('Welcome to Symfony', $crawler->filter('#container h1')->text());
    }
}