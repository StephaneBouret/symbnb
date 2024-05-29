<?php

namespace App\Security\Voter;

use App\Entity\Ad;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AdminUserVoter extends Voter
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['ADMIN_USER_EDIT'])
            && $subject instanceof Ad;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if (!$subject instanceof Ad) {
            throw new \LogicException('Subject is not an instance of Ad?');
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'ADMIN_USER_EDIT':
                return $user === $subject->getAuthor() || $this->security->isGranted('ROLE_ADMIN');
        }

        return false;
    }
}
