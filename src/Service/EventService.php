<?php

namespace App\Service;

use App\Repository\EventRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class PanierService
{
    private $repo;
    private $rs;


    public function __construct(EventRepository $repo, RequestStack $rs){
    $this->repo = $repo;
    $this->rs = $rs;

}


public function add($id){
    $session = $this->rs->getSession();

    $panier = $session->get('panier', []);
    if(!empty($panier[$id]))
        $panier[$id]++;
    else
    $panier[$id] = 1;
    
    $session->set('panier', $panier);
}


}