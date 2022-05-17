<?php

namespace App\Service;

use App\Repository\EventRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class EventService
{
    private $repo;
    private $rs;


    public function __construct(EventRepository $repo, RequestStack $rs){
    $this->repo = $repo;
    $this->rs = $rs;

}


public function addToEvent($id){
    $session = $this->rs->getSession();

    $event = $session->get('event', []);
    if(!empty($event[$id]))
        $event[$id]++;
    else
    $event[$id] = 1;
    
    $session->set('event', $event);
}
}