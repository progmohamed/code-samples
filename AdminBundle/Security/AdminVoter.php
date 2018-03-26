<?php

namespace AdminBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use AdminBundle\Entity\User;
use Doctrine\ORM\EntityManager;

class AdminVoter extends Voter
{
    
    protected $em;
    
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    protected function supports($attribute, $subject) 
    {
        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) 
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        if($user->hasRole('ROLE_SUPER_ADMIN')) {
            return true;
        }
    }

}
