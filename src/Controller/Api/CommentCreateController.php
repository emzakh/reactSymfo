<?php

namespace App\Controller\Api;

use App\Entity\Commentaires;
use Symfony\Component\Security\Core\Security;

class CommentCreateController
{

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function __invoke(Commentaires $data)
    {
        $data -> setAuthor($this->security->getUser());
        return $data;
    }


}