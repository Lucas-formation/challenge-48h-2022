<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Demande;
use App\Form\EventType;
use App\Form\RechercheType;
use App\Form\SubmitType as sub;
use App\Repository\EventRepository;
use App\Repository\DemandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{

    #[Route('/', name: 'app_main')]
    public function home(EventRepository $repo,Request $request): Response
    {
        $form = $this->createForm(RechercheType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $data = $form->get('recherche')->getData();
            $events = $repo->getEventsByName($data);
        }
        else{
            $events = $repo->findAll();
        }
        
        return $this->render('main/index.html.twig',[
            'events' => $events,
            'formRecherche' => $form->createView(),
        ]);

    }
    
    #[Route('challenge/edit/{id}', name: 'edit_event')]
    #[Route('challenge/new', name: 'create_event')]
    public function create(Event $event = null,Request $request,EntityManagerInterface $manager): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_main');
        }
        if(!$event){
            $event = new Event;
            $event->setDatedAt(new \DateTime('now'));
            $event->setState(false);
            $event->setUpdatedAt(new \DateTime('now'));
            $event->setUser($this->getUser());

        }
        
            $form = $this-> createForm(EventType::class, $event);
            $form->handleRequest($request);
            dump($event);
            
            
            
            if ($form->isSubmitted() && $form->isValid())
            {
                $manager->persist($event);
                $manager->flush();
                return $this->redirectToRoute('show_event',[
                    'id' => $event->getId(),
    
                ]);
            }
    
            return $this->render('main/create.html.twig', [
               'editMode' => $event->getId() !== null,
               'formEvent' => $form->createView(),
               'event' => $event,
            ]);
            
}
#[Route('/challenge/show/{id}', name: 'show_event')]
    public function show(Event $event, Request $request,EntityManagerInterface $manager, DemandeRepository $repo): Response
    {
        $demandes = $repo->findBy(['user' => $this->getUser()]);
        $form = $this-> createForm(sub::class);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){
            $dm = new Demande();
            $dm->setUser($this->getUser());
            $dm->setEvent($event);
            $manager->persist($dm);
            $manager->flush();

            return$this->redirectToRoute('app_main');

        }

            return $this->render('main/show.html.twig',[
                'event' => $event,
                'form' => $form->createView(),
                'demandes' => $demandes,
            ]);
}

#[Route('/challenge/profil', name: 'profil')]
public function profil() : Response
{
    return $this->render('main/profil.html.twig', [
    ]);
}
}
