<?php

namespace App\Tests\LiveComponent;

use App\Entity\User;
use App\Entity\AppUser;
use App\Twig\Form\RegistrationForm;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\UX\LiveComponent\Test\InteractsWithLiveComponents;

class RegistrationFormLiveComponentTest extends KernelTestCase
{
    use InteractsWithLiveComponents;

    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();
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
     * Test the initial render of the component.
     */
    public function testInitialRender(): void
    {
        $liveComponent = $this->createLiveComponent(RegistrationForm::class);

        $component = $liveComponent->component();
        $this->assertFalse($component->isSuccessful);
        $this->assertFalse($component->isSubmitted);
        $this->assertNull($component->user);
        $this->assertFalse($component->hasValidationErrors());
    }

    /**
     * Test the rendering of the registration form.
     */
    public function testFormRendering(): void
    {
        // 1. Créez une nouvelle instance de User pour le composant
        $user = new User();
        // 2. Passez cette instance au composant lors de sa création
        $liveComponent = $this->createLiveComponent(RegistrationForm::class, [
            'user' => $user, // Initialisez la LiveProp 'user' avec un objet User vide
        ]);
        $rendered = $liveComponent->render();
        // Vérifier que le formulaire contient les champs attendus
        $this->assertStringContainsString('name="registration_form[pseudo]"', $rendered);
        $this->assertStringContainsString('name="registration_form[firstName]"', $rendered);
        $this->assertStringContainsString('name="registration_form[lastName]"', $rendered);
        $this->assertStringContainsString('name="registration_form[phone]"', $rendered);
        $this->assertStringContainsString('name="registration_form[email]"', $rendered);
        $this->assertStringContainsString('name="registration_form[plainPassword]"', $rendered);
        $this->assertStringContainsString('name="registration_form[agreeTerms]"', $rendered);
        $this->assertStringContainsString('enregistrer', $rendered);

        $component = $liveComponent->component();
        $this->assertFalse($component->isSuccessful);
        $this->assertFalse($component->isSubmitted);
        $this->assertInstanceOf(User::class, $component->user);
        $this->assertFalse($component->hasValidationErrors());
    }

    /**
     * Test validation errors in the registration form.
     */
    public function testValidationErrors(): void
    {
        // 1. Créez une nouvelle instance de User pour le composant
        $user = new User();
        // 2. Passez cette instance au composant lors de sa création
        $testComponent = $this->createLiveComponent(RegistrationForm::class, [
            'user' => $user, // Initialisez la LiveProp 'user' avec un objet User vide
        ]);

        // Pour tester les erreurs de validation, nous devons intercepter l'exception
        $this->expectException(UnprocessableEntityHttpException::class);
        $this->expectExceptionMessage('Form validation failed in component');

        $testComponent
            ->submitForm([
                'registration_form.pseudo' => '', // Champ vide
                'registration_form.firstName' => 'J', // Trop court
                'registration_form.lastName' => '', // Champ vide
                'registration_form.email' => 'invalid-email', // Email invalide
                'registration_form.phone' => '123', // Trop court
                'registration_form.plainPassword' => '12', // Trop court
                'registration_form.agreeTerms' => false // Non accepté
            ])
            ->call('save');
    }

    /**
     * Test successful registration.
     */
    public function testSuccessfulRegistration(): void
    {
        // 1. Créez une nouvelle instance de User pour le composant
        $user = new User();
        // 2. Passez cette instance au composant lors de sa création
        $testComponent = $this->createLiveComponent(RegistrationForm::class, [
            'user' => $user, // Initialisez la LiveProp 'user' avec un objet User vide
        ]);

        $testEmail = 'live.test' . uniqid() . '@testmode.com';
        $testPseudo = 'liveuser' . uniqid();
        $testComponent = $testComponent
            ->submitForm([
                'registration_form.pseudo' => $testPseudo,
                'registration_form.firstName' => 'John',
                'registration_form.lastName' => 'Doe',
                'registration_form.email' => $testEmail,
                'registration_form.phone' => '0123456789',
                'registration_form.plainPassword' => 'Password123!',
                'registration_form.agreeTerms' => true
            ])
            ->call('save');

        $response = $testComponent->response();

        // Vérifier la redirection
        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\RedirectResponse::class, $response);
        $this->assertEquals('/login', $response->getTargetUrl());

        // Vérifier que l'utilisateur a été créé en base
        $this->entityManager->clear(); // Clear pour forcer le rechargement depuis la BDD
        $createdUser = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => $testEmail]);

        $this->assertNotNull($createdUser);
        $this->assertEquals($testPseudo, $createdUser->getPseudo());
        $this->assertEquals('John', $createdUser->getFirstName());
        $this->assertEquals('Doe', $createdUser->getLastName());
        $this->assertEquals($testEmail, $createdUser->getEmail());
        $this->assertEquals('0123456789', $createdUser->getPhone());
        $this->assertContains('ROLE_USER', $createdUser->getRoles());
        $this->assertNotNull($createdUser->getAppUser());

        // Vérifier que le mot de passe a été hashé
        $passwordHasher = static::getContainer()->get(UserPasswordHasherInterface::class);
        $this->assertTrue($passwordHasher->isPasswordValid($createdUser, 'Password123!'));
    }

    /**
     * Test duplicate email validation.
     */
    public function testDuplicateEmailValidation(): void
    {
        // Créer un utilisateur existant
        $existingUser = new User();
        $existingUser->setEmail('existinguser@testmode.com');
        $existingUser->setPseudo('existing');
        $existingUser->setFirstName('Existing');
        $existingUser->setLastName('User');
        $existingUser->setPhone('0987654321');
        $existingUser->setPassword('hashedpassword');
        $existingUser->setRoles(['ROLE_USER']);
        $appUser = new AppUser();
        $existingUser->setAppUser($appUser);
        $this->entityManager->persist($existingUser);
        $this->entityManager->persist($appUser);
        $this->entityManager->flush();

        // 1. Créez une nouvelle instance de User pour le composant
        $user = new User();
        // 2. Passez cette instance au composant lors de sa création
        $testComponent = $this->createLiveComponent(RegistrationForm::class, [
            'user' => $user, // Initialisez la LiveProp 'user' avec un objet User vide
        ]);

        // Pour tester les erreurs de validation, nous devons intercepter l'exception
        $this->expectException(UnprocessableEntityHttpException::class);
        $this->expectExceptionMessage('Form validation failed in component');
        $testComponent
            ->submitForm([
                'registration_form.pseudo' => 'newuser',
                'registration_form.firstName' => 'New',
                'registration_form.lastName' => 'User',
                'registration_form.email' => 'existinguser@testmode.com',
                'registration_form.phone' => '0123456789',
                'registration_form.plainPassword' => 'Password123!',
                'registration_form.agreeTerms' => true
            ])
            ->call('save');
    }
}
