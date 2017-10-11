<?php

namespace Blogger\BlogBundle\Controller;

use Blogger\BlogBundle\BlogBundle;
use Blogger\BlogBundle\Entity\Comment;
use Blogger\BlogBundle\Entity\Post;
use Blogger\BlogBundle\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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

        $post = new Post();
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
    public function postAction($id){

        $post = $this->getDoctrine()
            ->getRepository(Post::class)
            ->find($id);

        return $this->render('BlogBundle:Blog:show.html.twig', array('result' => $post));
    }

    /**
     * @Route("/posts/{id}/comment/create", name="comment_store")
     * @Method({"POST"})
     */
    public function comment_storeAction($id, Request $request){


        $em = $this->getDoctrine()
            ->getManager();

        $post = $em->find('BlogBundle:Post', 'id');

        $comment = new Comment();
        $comment->setBody($request->get('body'));
        $comment->setCreated(\date('Y:m:d в H:i:s'));
        $commentId = $comment->getId();

        //$addComment = $em->f;

        //$post->comment($addComment);

        $em->persist($comment);
        $em->flush();

        return $this->$commentId;
    }
}
