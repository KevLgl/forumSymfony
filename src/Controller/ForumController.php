<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Subject;
use App\Form\SubjectType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;





/**
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */




class ForumController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @Route("/subjects", name="subjects")
     */
    public function index(): Response
    {
        $subjetctRepository = $this->getDoctrine()->getRepository(Subject::class);
        $subjects = $subjetctRepository->findAll();

        return $this->render('forum/index.html.twig', [
            'subjects' => $subjects,
        ]);
    }

        /**
     * @Route("/rules", name="rules")
     */
    public function rules(): Response
    {
        return $this->render('forum/rules.html.twig', [
            'controller_name' => 'ForumController',
        ]);
    }

            /**
     * @Route("/subject/{id}", name="subject", requirements={"id"="\d+"})
     */
    public function subject(int $id): Response
    {
        $subjetctRepository = $this->getDoctrine()->getRepository(Subject::class);
        $subject = $subjetctRepository->find($id);
        return $this->render('forum/subject.html.twig', [
            'subject' => $subject,
        ]);
    }
       /**
     * @Route("/subject/new", name="new_subject")
     */
    public function newSubject(Request $request,ValidatorInterface $validator): Response
    {
        $errors = null;
        $subject = new Subject();
        $form = $this->createForm(SubjectType::class, $subject);
        //On traite les donnée de la requête dans l'object form
        $form->handleRequest($request);
        //Si on a soumis un formulaire et que tout est ok
        if ($form->isSubmitted() && $form->isValid()) {

            $errors = $validator->validate($subject);
            //si il n'y a pas d'erreur
            if(count($errors) === 0){
            // on enregistre le nouveau sujet 
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($subject);
            $entityManager->flush();

            //Creer un message flash
            $this->addFlash('success','Votre question à bien été enregistrée'); 
            $this->addFlash('success',"N'hesitez pas à visiter le forum"); 
            //redicection sur accueil
            return $this->redirectToRoute('index');
            }
        }
        
        return $this->render('forum/new_subject.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors,
        ]);
    }

}
