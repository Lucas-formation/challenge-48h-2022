<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Demande;
use App\Form\EventType;
use App\Form\AcceptType;
use App\Form\RechercheType;
use App\Service\EventService;
use App\Form\SubmitType as sub;
use App\Repository\UserRepository;
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
    #[Route('/challenge/profil', name: 'profil')]
public function profil() : Response
{
    return $this->render('main/profil.html.twig', [
    ]);
}
    

#[Route('/challenge/show/{id}', name: 'show_event', requirements: ['id'=> "\d+"])]
    public function show(Event $event, Request $request,EntityManagerInterface $manager,Demande $dm = null, DemandeRepository $repo, Bool $ask = false): Response
    {
        $findevent = $repo->findBy(['event' => $event]);
        foreach ($findevent as $value){
            if($value->getUser() == $this->getUser()){
                $ask = true;
            }

        }
        
        if(!$dm){
            $dm = new Demande();
            $dm->setUser($this->getUser());
            $dm->setEvent($event);
            $dm->setIsAccepted(false);
            
        }
        
        $form = $this-> createForm(sub::class);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
           
            $manager->persist($dm);
            $manager->flush();
            return$this->redirectToRoute('show_event',['id' => $event->getId()]);

        }
        

        $formAccept = $this->createForm(AcceptType::class);
        $formAccept->handleRequest($request);

        $setisAccepted = $repo->findOneBy(['event' => $event]);
        
        if($formAccept->isSubmitted() && $formAccept->isValid()){
            $setisAccepted->setIsAccepted(true);
            dd($setisAccepted);
            
            $manager->persist($setisAccepted);
            $manager->flush();

            return$this->redirectToRoute('show_event',['id' => $event->getId()]);

        }
        

            return $this->render('main/show.html.twig',[
                'ask' => $ask,
                'event' => $event,
                'form' => $form->createView(),
                'formAccept' =>$formAccept->createView(),
                'demande' => $dm,
                'getUser' => $findevent,
            ]);
}
#[Route('/challenge/show/user/{id}', name: 'showuser_event')]
public function getUserIdForEvent(Event $event = null, $id, UserRepository $repo){
    $user = $repo->find($id);
    

    return $this->render('main/show.html.twig',['id' => $event->getId()]);
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

}

