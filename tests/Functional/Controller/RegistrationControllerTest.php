<?php

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Browser\Test\HasBrowser;

class RegistrationControllerTest extends WebTestCase
{
    use HasBrowser;

    private EntityManagerInterface $entityManager;
    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        // Nettoyer tous les utilisateurs de test restants
        $testUsers = $this->entityManager->getRepository(User::class)
            ->createQueryBuilder('u')
            ->where('u.email LIKE :email')
            ->setParameter('email', '%@testmode.com')
            ->getQuery()
            ->getResult();

        foreach ($testUsers as $user) {
            $this->entityManager->remove($user);
        }

        $this->entityManager->flush();
    }

    /**
     * Tests access to the registration page.
     */
    public function testRegisterPageRendersSuccessfullyAndFormIsPresent(): void
    {
        $this->client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Inscrivez-vous');

        // Vérifie la présence de tous les champs du formulaire
        $this->assertSelectorExists('input[name="registration_form[pseudo]"]');
        $this->assertSelectorExists('input[name="registration_form[firstName]"]');
        $this->assertSelectorExists('input[name="registration_form[lastName]"]');
        $this->assertSelectorExists('input[name="registration_form[phone]"]');
        $this->assertSelectorExists('input[name="registration_form[email]"]');
        $this->assertSelectorExists('input[name="registration_form[plainPassword]"]');
        $this->assertSelectorExists('input[name="registration_form[agreeTerms]"]');
        $this->assertSelectorExists('button[type="submit"]');

        // Vérifie la présence du lien de connexion
        $this->assertSelectorExists('a[href="/login"]');

        // Vérifie qu'aucun message d'erreur de vérification d'email n'est présent initialement
        $this->assertSelectorNotExists('.alert.alert-danger');
    }

    /**
     * Tests the registration form submission with valid data.
     */
    public function testRegisterUserWithValidData(): void
    {
        // Compter les utilisateurs avant l'inscription (nécessite un UserRepository)
        $userRepository = static::getContainer()->get(UserRepository::class);
        $initialUserCount = count($userRepository->findAll());

        $crawler = $this->client->request('GET', '/register');
        // Vérifiez que la page d'inscription s'affiche bien
        $this->assertResponseIsSuccessful();
        $div = $crawler->filter('[data-controller="live"]');
        $dehydratedProps = json_decode($div->attr('data-live-props-value'), true);

        $testEmail = 'live.test' . uniqid() . '@testmode.com';
        $testPseudo = 'liveuser' . uniqid();
        // mimic user typing
        $updatedProps = [
            'registration_form.pseudo' => $testPseudo,
            'registration_form.firstName' => 'Test',
            'registration_form.lastName' => 'User',
            'registration_form.phone' => '0601020304',
            'registration_form.email' => $testEmail,
            'registration_form.plainPassword' => 'StrongPassword123!',
            'registration_form.agreeTerms' => true,
            'validatedFields' => [
                'registration_form.pseudo',
                'registration_form.firstName',
                'registration_form.lastName',
                'registration_form.phone',
                'registration_form.email',
                'registration_form.plainPassword',
                'registration_form.agreeTerms'
            ],
        ];
        $data = [
            'data' => json_encode([
                'props' => $dehydratedProps,
                'updated' => $updatedProps,
            ]),
        ];

        $this->client->request(
            'POST',
            '/_components/registration_form/save',
            $data
        );

        // Après une tentative de connexion échouée, Symfony redirige généralement vers la page de connexion elle-même.
        $this->assertResponseRedirects('/login');

        // Vérifications après un succès (si l'exception n'est pas levée)
        $finalUserCount = count($userRepository->findAll());

        $this->assertGreaterThan(
            $initialUserCount,
            $finalUserCount,
            "L'utilisateur n'a pas été persisté en base de données."
        );

        // Suit la redirection pour atterrir sur la page de connexion avec l'erreur
        $crawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-success');
        $this->assertSelectorTextContains(
            '.alert.alert-success',
            'Votre compte a bien été créé ! Un mail vous a été envoyé'
        );

        // Vérifiez l'existence de l'utilisateur par l'email ou pseudo
        $createdUser = $userRepository->findOneBy(['email' => $testEmail]);
        $this->assertNotNull(
            $createdUser,
            'L\'utilisateur avec cet email devrait exister.'
        );
        $this->assertSame(
            $testPseudo,
            $createdUser->getPseudo(),
            'Le pseudo de l\'utilisateur créé doit correspondre.'
        );
    }

    /**
     * Teste la soumission du formulaire avec des données valides.
     *
     * IMPORTANT : Pour que ce test passe, vous devez avoir un UserProvider configuré
     * et potentiellement un service de mailer qui ne lève pas d'erreur (ou qui est mocké).
     * Nous allons ici simuler une inscription et vérifier la redirection.
     * La vérification de la création en BDD et de l'envoi de mail nécessiterait
     * des étapes supplémentaires (fixtures, mail catcher, etc.).
     */
    public function testRegisterUserWithInvalidData(): void
    {
        // Compter les utilisateurs avant l'inscription (nécessite un UserRepository)
        $userRepository = static::getContainer()->get(UserRepository::class);
        $initialUserCount = count($userRepository->findAll());

        $crawler = $this->client->request('GET', '/register');
        // Vérifiez que la page d'inscription s'affiche bien
        $this->assertResponseIsSuccessful();
        $div = $crawler->filter('[data-controller="live"]');
        $dehydratedProps = json_decode($div->attr('data-live-props-value'), true);

        $testEmail = 'live.test' . uniqid() . '@example.com';
        $testPseudo = 'liveuser' . uniqid();
        // mimic user typing
        $updatedProps = [
            'registration_form.pseudo' => $testPseudo,
            'registration_form.firstName' => 'T', // Champ trop court
            'registration_form.lastName' => 'User',
            'registration_form.phone' => '0601020304',
            'registration_form.email' => $testEmail,
            'registration_form.plainPassword' => 'StrongPassword123!',
            'registration_form.agreeTerms' => true,
            'validatedFields' => [
                'registration_form.pseudo',
                'registration_form.firstName',
                'registration_form.lastName',
                'registration_form.phone',
                'registration_form.email',
                'registration_form.plainPassword',
                'registration_form.agreeTerms'
            ],
        ];
        $data = [
            'data' => json_encode([
                'props' => $dehydratedProps,
                'updated' => $updatedProps,
            ]),
        ];

        $browser = $this->browser();
        $crawler = $browser
            // post to action, which will add a new embedded comment
            ->post(
                '/_components/registration_form',
                [
                    'body' => $data,
                ]
            )
            ->assertStatus(422)
            ->assertContains('invalid-feedback')
            ->assertContains('Le prénom doit faire au moins 2 caractères')
            ->assertNotContains('The title field should not be blank')
            ->crawler();

        $div = $crawler->filter('[data-controller="live"]');
        $dehydratedProps = json_decode($div->attr('data-live-props-value'), true);

        // 1. Récupérer l'erreur du firstName
        $pseudoErrorNode = $crawler->filter('input[name="registration_form[firstName]"] + label + .invalid-feedback');
        $this->assertCount(1, $pseudoErrorNode, 'Le champ firstName devrait avoir un message d\'erreur.');
        $pseudoErrorMessage = $pseudoErrorNode->text();
        // 2. Vérifier le message d'erreur spécifique du pseudo
        $this->assertStringContainsString(
            'Le prénom doit faire au moins 2 caractères',
            $pseudoErrorMessage,
            'Le message d\'erreur pour le pseudo n\'est pas celui attendu.'
        );
        $this->assertSelectorTextContains(
            '.form-floating.mb-3 .invalid-feedback',
            'Le prénom doit faire au moins 2 caractères'
        );
        // 3. Vérifier que firstName n'a PAS d'erreurs (car il est maintenant valide)
        $firstNameErrorNode = $crawler->filter(
            'input[name="registration_form[pseudo]"] + label + .invalid-feedback'
        );
        $this->assertCount(
            0,
            $firstNameErrorNode,
            'Le champ pseudo ne devrait PAS avoir de message d\'erreur.'
        );

        // Vérifications après un échec (si l'exception est levée)
        $finalUserCount = count($userRepository->findAll());
        $this->assertEquals(
            $initialUserCount,
            $finalUserCount,
            "L'utilisateur n'aurait pas dû être persisté en base de données."
        );
    }
}
