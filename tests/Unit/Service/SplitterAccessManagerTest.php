<?php

namespace App\Tests\Unit\Service;

use App\Entity\AppUser;
use App\Entity\Splitter;
use App\Entity\User;
use App\Service\SplitterAccessManager;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SplitterAccessManagerTest extends TestCase
{
    private AuthorizationCheckerInterface $authCheckerMock;
    private SplitterAccessManager $accessManager;
    private Splitter $splitterMock;
    private User $userMock;
    private AppUser $appUserMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authCheckerMock = $this->createMock(AuthorizationCheckerInterface::class);
        $this->accessManager = new SplitterAccessManager($this->authCheckerMock);

        $this->splitterMock = $this->createMock(Splitter::class);
        $this->userMock = $this->createMock(User::class);
        $this->appUserMock = $this->createMock(AppUser::class);

        $this->userMock->method('getAppUser')->willReturn($this->appUserMock);
    }

    // Tests for checkEditAccess

    public function testCheckEditAccessAllowedForOwner(): void
    {
        $this->splitterMock->method('getOwner')->willReturn($this->appUserMock);
        $this->authCheckerMock->method('isGranted')->with('ROLE_ADMIN')->willReturn(false);

        $this->accessManager->checkEditAccess($this->splitterMock, $this->userMock);
        $this->assertTrue(true); // No exception means success
    }

    public function testCheckEditAccessAllowedForAdmin(): void
    {
        $this->splitterMock->method('getOwner')->willReturn($this->createMock(AppUser::class)); // Not the owner
        $this->authCheckerMock->method('isGranted')->with('ROLE_ADMIN')->willReturn(true);

        $this->accessManager->checkEditAccess($this->splitterMock, $this->userMock);
        $this->assertTrue(true); // No exception means success
    }

    public function testCheckEditAccessDeniedForNonOwnerNonAdmin(): void
    {
        $this->splitterMock->method('getOwner')->willReturn($this->createMock(AppUser::class)); // Not the owner
        $this->authCheckerMock->method('isGranted')->with('ROLE_ADMIN')->willReturn(false);

        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionMessage('Accès non autorisé à cette ressource. !');
        $this->accessManager->checkEditAccess($this->splitterMock, $this->userMock);
    }

    public function testCheckEditAccessDeniedForNullUser(): void
    {
        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionMessage('Utilisateur non connecté.');
        $this->accessManager->checkEditAccess($this->splitterMock, null);
    }

    // Tests for checkReadAccess

    public function testCheckReadAccessAllowedForFavorite(): void
    {
        $favorites = new ArrayCollection([$this->splitterMock]);
        $this->appUserMock->method('getFavoriteSplitters')->willReturn($favorites);
        $this->authCheckerMock->method('isGranted')->with('ROLE_ADMIN')->willReturn(false);

        $this->accessManager->checkReadAccess($this->splitterMock, $this->userMock);
        $this->assertTrue(true); // No exception means success
    }

    public function testCheckReadAccessAllowedForAdmin(): void
    {
        $favorites = new ArrayCollection(); // Not in favorites
        $this->appUserMock->method('getFavoriteSplitters')->willReturn($favorites);
        $this->authCheckerMock->method('isGranted')->with('ROLE_ADMIN')->willReturn(true);

        $this->accessManager->checkReadAccess($this->splitterMock, $this->userMock);
        $this->assertTrue(true); // No exception means success
    }

    public function testCheckReadAccessDeniedNonFavoriteNonAdmin(): void
    {
        $favorites = new ArrayCollection(); // Not in favorites
        $this->appUserMock->method('getFavoriteSplitters')->willReturn($favorites);
        $this->authCheckerMock->method('isGranted')->with('ROLE_ADMIN')->willReturn(false);

        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionMessage('Accès non autorisé à cette ressource.');
        $this->accessManager->checkReadAccess($this->splitterMock, $this->userMock);
    }

    public function testCheckReadAccessDeniedForNullUser(): void
    {
        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionMessage('Utilisateur non connecté.');
        $this->accessManager->checkReadAccess($this->splitterMock, null);
    }
}
