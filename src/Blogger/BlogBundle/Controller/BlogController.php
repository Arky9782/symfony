<?php

namespace Blogger\BlogBundle\Controller;

use Blogger\BlogBundle\BlogBundle;
use Blogger\BlogBundle\Entity\Comment;
use Blogger\BlogBundle\Entity\Post;
use Blogger\BlogBundle\Entity\User;
use Blogger\BlogBundle\Repository\PostRepository;
use Blogger\BlogBundle\Repository\UserRepository;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

class BlogController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction(){
        $post = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findAll();

        return $this->render('BlogBundle:Blog:index.html.twig', array('result' => $post));
    }

    /**
     * @Route("/posts/create", name="create")
     */
    public function createAction(){
        return $this->render('BlogBundle:Blog:create.html.twig');
    }

    /**
     * @Route("/posts/", name="store")
     * @Method({"POST"})
     */
    public function storeAction(Request $request){



        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        $post = new Post();
        $post->setUser($user);
        $post->setBody($request->get('body'));
        $post->setTitle($request->get('title'));
        $post->setCreated($time = date('H:i:s в d/m/Y'));

        $em->persist($post);

        $em->flush();

        return $this->redirect('/');


    }


    /**
     * @Route("/posts/{id}", name="post")
     */
    public function postAction(Comment $comment, Request $request, $id){

        $post = $this->getDoctrine()
            ->getRepository(Post::class)
            ->find($id);

        $user = $this->getUser();


        $comments = $post->getComments();
        $postAuthor = $post->getUser();



        $comment = new Comment();
        $comment->setUser($user);
        $comment->setBody('Write a new comment');
        $comment->setCreated();


        $form = $this->createFormBuilder($comment)
            ->add( 'body',TextareaType::class)
            ->add('save',SubmitType::class, array('label' => 'Отправить'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() ){

            $task = $form->getData();
            $em = $this->getDoctrine()->getManager();

            $post->comment($task);

            $em->persist($task);
            $em->flush();

            return $this->redirectToRoute('index');

        }

        return $this->render('BlogBundle:Blog:show.html.twig', array('post' => $post, 'postAuthor' => $postAuthor, 'comments' => $comments, 'form' => $form->createView()));
    }


}
