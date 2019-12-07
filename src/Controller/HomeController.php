<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\Book;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class HomeController extends AbstractController
{


     /**
     * @Route("/", name="book_liste")
     * @Method ({"GET"})
     */
    public function home(){
    
        $entityManager = $this->getDoctrine()->getManager();
        $books = $this->getDoctrine()->getRepository(Book::class)->findAll();
        return $this->render('home.html.twig', array('books' => $books));
    }

    /**
     * @Route("/new", name="new")
     * @Method({"GET","POST"})
     */
    public function createBook(Request $request)
    {
       $entityManager = $this->getDoctrine()->getManager();
       $book = new Book();
       $form = $this->createFormBuilder($book)
            
            ->add('titre', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('description', TextareaType::class, array('required' => FALSE, 'attr' => array('class' => 'form-control')))
            ->add('save', SubmitType::class, array('label' => 'Create', 'attr' => array('class' => 'btn btn-primary mt-2')))
            ->getForm();
        
            $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $book = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($book);
            $entityManager->flush();
            return $this->redirectToRoute('book_liste');
        }
        return $this->render('new.html.twig', array('form' => $form->createView()));
    }
    


    /**
 * @Route("/book/{id}", name="book_show")
 */
    public function show($id) {
    $book = $this->getDoctrine()
        ->getRepository(Book::class)
        ->find($id);

    if (!$book) {
        throw $this->createNotFoundException(
            'No book found for id '.$id
        );
    }

    return new Response('Check out this great product: '.$book->getTitre());
}

/**
 * @Route("/book/edit/{id}")
 */
public function update(Request $request, $id)
{
    $book = $this->getDoctrine()->getRepository(Book::class)->find($id);
    $form = $this->createFormBuilder($book)
        ->add('titre', TextType::class, array('attr' => array('class' => 'form-control')))
        ->add('description', TextareaType::class, array('required' => FALSE, 'attr' => array('class' => 'form-control')))
        ->add('save', SubmitType::class, array('label' => 'Update', 'attr' => array('class' => 'btn btn-primary mt-3')))
        ->getForm();
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        $books = $this->getDoctrine()->getRepository(Book::class)->findAll();
    
        return $this->render('home.html.twig', array('books' => $books));
    }
    return $this->render('edit.html.twig', array('form' => $form->createView()));
}

/**
 * @Route("/book/delete/{id}")
 */
public function delete($id)
{
    $entityManager = $this->getDoctrine()->getManager();
    $book = $entityManager->getRepository(Book::class)->find($id);

    if (!$book) {
        throw $this->createNotFoundException(
            'No book found for id '.$id
        );
    }

    $entityManager->remove($book);
    $entityManager->flush();
  
        
    $books = $this->getDoctrine()->getRepository(Book::class)->findAll();
    
    return $this->render('home.html.twig', array('books' => $books));
}


}