<?php

// src/Twig/MemberExtension.php
namespace App\Twig;

use App\Entity\AppUser;
use App\Entity\Member;
use App\Entity\User;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Symfony\Bundle\SecurityBundle\Security;

class MemberExtension extends AbstractExtension
{
    public function __construct(
        private readonly Security $security
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_current_user_member', [$this, 'isCurrentUserMember']),
            new TwigFunction('get_member_for_user', [$this, 'getMemberForUser']),
        ];
    }

    public function isCurrentUserMember(Member $member): bool
    {
        /**
         * @var ?User $user
         */
        $user = $this->security->getUser();
        $appUser = $user?->getAppUser();
        if (!$appUser) {
            return false;
        }

        foreach ($member->getAppUserMembers() as $appUserMember) {
            if ($appUserMember->getAppUser()->getId() === $appUser->getId()) {
                return true;
            }
        }

        return false;
    }

    public function getMemberForUser(iterable $members): ?Member
    {
        /**
         * @var ?User $user
         */
        $user = $this->security->getUser();
        $appUser = $user?->getAppUser();

        if (!$appUser) {
            return null;
        }

        foreach ($members as $member) {
            foreach ($member->getAppUserMembers() as $aum) {
                if ($aum->getAppUser()->getId() === $appUser->getId()) {
                    return $member;
                }
            }
        }

        return null;
    }
}
