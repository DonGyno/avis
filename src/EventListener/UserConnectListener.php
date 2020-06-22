<?php


namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class UserConnectListener
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager){
        $this->entityManager = $entityManager;
    }

    public function onAuthenticationSuccessAfter(InteractiveLoginEvent $event)
    {
        $em = $this->entityManager;
        $current_username = $event->getAuthenticationToken()->getUser()->getUsername();
        $user = $em->getRepository(User::class)->findOneBy(['email'=>$current_username]);
        if(!empty($user))
        {
            $date = new \DateTime();
            $user->setDerniereConnexion($date);
            $em->persist($user);
            $em->flush();
        }
    }
}