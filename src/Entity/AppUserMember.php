<?php

namespace App\Entity;

use App\Repository\AppUserMemberRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AppUserMemberRepository::class)]
#[ORM\Table(name: 'app_user_member')]
#[ORM\UniqueConstraint(name: "unique_selection", columns: ["app_user_id", "splitter_id"])]
class AppUserMember
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: AppUser::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?AppUser $appUser = null;

    #[ORM\ManyToOne(targetEntity: Splitter::class)]
    #[ORM\JoinColumn(referencedColumnName: 'id', nullable: false)]
    private ?Splitter $splitter = null;

    #[ORM\ManyToOne(targetEntity: Member::class, inversedBy: 'appUserMembers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Member $member = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAppUser(): ?AppUser
    {
        return $this->appUser;
    }

    public function setAppUser(?AppUser $appUser): static
    {
        $this->appUser = $appUser;
        return $this;
    }

    public function getSplitter(): ?Splitter
    {
        return $this->splitter;
    }

    public function setSplitter(?Splitter $splitter): static
    {
        $this->splitter = $splitter;
        return $this;
    }

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function setMember(?Member $member): static
    {
        $this->member = $member;
        return $this;
    }
}
