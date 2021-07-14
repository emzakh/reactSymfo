<?php

namespace App\Security;

use App\Entity\Commentaires;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;


class CommentVoter extends Voter
{

    const EDIT='EDIT_COMMENT';

    protected function supports(string $attribute, $subject)
    {
        return
            $attribute === self::EDIT &
            $subject instanceof Commentaires;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if($user instanceof User || $subject instanceof Commentaires){
            return $subject->getAuthor()->getId() === $user->getId();
        }
        return false;

    }
}
