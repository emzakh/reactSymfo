<?php

namespace App\Events;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JwtCreatedSubscriber {

    public function updateJwtData(JWTCreatedEvent $event){
        // récup l'utilisateur (pour avoir le firstName et la lastName )
        $user = $event->getUser();

        $data = $event->getData(); // récup un tableau qui contient les données de base sur l'utilisateur dans le token

        $data['firstName'] = $user->getFirstName();
        $data['lastName'] = $user->getLastName();

        $event->setData($data); // on repasse le tableau data modifié au EventData

    }



}