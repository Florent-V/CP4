<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginControllerTest extends WebTestCase
{
    public function testLoginPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    /**
     * Teste l'accès à la page de connexion sans erreur.
     */
    public function testLoginPageRendersSuccessfullyBis(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        // Vérifie que la page est chargée avec succès (code 200 OK)
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Connectez-vous pour accéder à vos groupes !');
        $this->assertSelectorExists('input[name="_username"]');
        $this->assertSelectorExists('input[name="_password"]');
        $this->assertSelectorExists('button[type="submit"]');
        $this->assertSelectorExists('input[name="_csrf_token"]');
        // Vérifie que le champ last_username est vide initialement
        $this->assertInputValueSame('_username', '');
        // Vérifie qu'il n'y a pas de message d'erreur initialement
        $this->assertSelectorNotExists('.alert.alert-warning');
    }

    /**
     * Teste l'affichage d'un message d'erreur pour les mauvais identifiants.
     * On simule une erreur d'authentification.
     */
    public function testLoginPageDisplaysBadCredentialsError(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        // Simule la soumission du formulaire de connexion avec de mauvaises informations
        $form = $crawler->selectButton('Login')->form([
            '_username' => 'wrong@example.com',
            '_password' => 'wrongpassword',
        ]);

        $client->submit($form);

        // Après une tentative de connexion échouée, Symfony redirige généralement vers la page de connexion elle-même.
        $this->assertResponseRedirects('/login');
        // Suit la redirection pour atterrir sur la page de connexion avec l'erreur
        $crawler = $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-warning');
        // Vérifie le message d'erreur spécifique pour les mauvais identifiants
        $this->assertSelectorTextContains(
            '.alert.alert-warning',
            'Les identifiants sont incorrects. Veuillez les vérifier et réessayer.'
        );
        // Vérifie que le dernier nom d'utilisateur est pré-rempli
        $this->assertInputValueSame('_username', 'wrong@example.com');
    }

    /**
     * Teste l'affichage d'un message d'erreur pour les mauvais identifiants.
     * On simule une erreur d'authentification.
     */
    public function testLoginPageDisplaysNotVerifiedError(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        // Simule la soumission du formulaire de connexion avec de mauvaises informations
        $form = $crawler->selectButton('Login')->form([
            '_username' => 'notverified@mail.fr',
            '_password' => 'motdepasse',
        ]);

        $client->submit($form);

        // Après une tentative de connexion échouée, Symfony redirige généralement vers la page de connexion elle-même.
        $this->assertResponseRedirects('/login');
        // Suit la redirection pour atterrir sur la page de connexion avec l'erreur
        $crawler = $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-warning');
        // Vérifie le message d'erreur spécifique pour les mauvais identifiants
        $this->assertSelectorTextContains(
            '.alert.alert-warning',
            'Votre compte n\'est pas activé, un mail vient de vous être envoyé pour procéder à l\'activation'
        );
        // Vérifie que le dernier nom d'utilisateur est pré-rempli
        $this->assertInputValueSame('_username', 'notverified@mail.fr');
    }

    /**
     * Teste la présence du token CSRF.
     */
    public function testCsrfTokenIsPresent(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('input[name="_csrf_token"]');

        // On peut même tenter de récupérer sa valeur pour une vérification plus poussée si nécessaire
        $csrfToken = $crawler->filter('input[name="_csrf_token"]')->attr('value');
        $this->assertNotEmpty($csrfToken);
        $this->assertIsString($csrfToken);
    }
}
