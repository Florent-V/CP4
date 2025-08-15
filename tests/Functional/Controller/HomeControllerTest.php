<?php

namespace App\Tests\Functional\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testRedirectIfNotLoggedIn(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        // Symfony redirige automatiquement vers la page de login
        $this->assertResponseRedirects('/login');
    }

    public function testIndexPageForLoggedUser(): void
    {
        $client = static::createClient();

        // Récupération du service user & authentification
        $user = $client
            ->getContainer()
            ->get(UserRepository::class)
            ->findOneBy(
                ['email' => 'user1@mail.fr'],
            );
        $client->loginUser($user);

        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[name=search_bar_form]');
        $this->assertSelectorTextContains('h1', 'Kopeck !'); // à adapter au contenu réel
    }
}
