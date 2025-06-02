<?php

namespace App\Tests\Functional\Controller\Splitter;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AffectControllerTest extends WebTestCase
{
    public function testAffect(): void
    {
        $client = static::createClient();
        // Replace with a valid or test-specific ID if necessary
        $testId = '12345678-1234-1234-1234-1234567890ab';
        $client->request('GET', '/splitter/' . $testId . '/affect');

        // This will likely fail if the ID doesn't exist or if authentication is required.
        // For a basic structure, we assert for a successful response,
        // but in a real scenario, you'd handle authentication and specific response codes.
        $this->assertResponseIsSuccessful();
    }
}
