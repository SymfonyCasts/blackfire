<?php

namespace App\Tests\Controller;

use Blackfire\Bridge\PhpUnit\TestCaseTrait;
use Blackfire\Profile\Configuration;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainControllerTest extends WebTestCase
{
    use TestCaseTrait;

    public function testGetGitHubOrganization()
    {
        $client = static::createClient();

        $blackfireConfig = (new Configuration())
            ->assert('metrics.http.requests.count == 1');

        $this->assertBlackfire($blackfireConfig, function() use ($client) {
            $client->request('GET', '/api/github-organization');

            $this->assertResponseIsSuccessful();
            $data = json_decode($client->getResponse()->getContent(), true);
            $this->assertArrayHasKey('organization', $data);
        });
    }
}
