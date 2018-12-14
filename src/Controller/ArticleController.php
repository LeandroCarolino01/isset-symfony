<?php
    namespace App\Controller;

    use App\Entity\Article;

    use Symfony\Component\HttpFoundation\RedirectResponse;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Annotation\Route;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\Extension\Core\Type\TextareaType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;

    class ArticleController extends Controller{
        /**
         * @Route("/", name="article_list")
         * @Method({"GET"})
         */
        public function index(){

            $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();
            return $this->render('articles/index.html.twig',
                ['articles' => $articles]
            );
        }


        /**
         * @Route("/article/new", name="new_article")
         * Method({"GET", "POST"})
         * @param Request $request
         *
         * @return RedirectResponse|Response
         */

        public function new(Request $request){
            $article = new Article();

            $form = $this->createFormBuilder($article)
                ->add('title', TextType::class, array('attr' =>
                array('class' => 'form-control')))
                ->add('body', TextareaType::class, array(
                    'required' => false,
                    'attr' => array('class' => 'form-control')
                ))
                ->add('save', SubmitType::class, array(
                    'label' => 'Create',
                    'attr' => array('class' => 'btn btn-primary mt-3')
                ))
                ->getForm();

            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()) {
                $article = $form->getData();

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($article);
                $entityManager->flush();

                return $this->redirectToRoute('article_list');
            }
            
            return $this->render('articles/new.html.twig', array(
                'form' => $form->createView()
            ));
        }


        /**
         * @Route("/article/edit/{id}", name="edit_article")
         *
         * Method({"GET", "POST"})
         *
         * @param Request $request
         * @param Article $article
         *
         * @return RedirectResponse|Response
         */

        public function edit(Request $request, Article $article)
        {
            $form = $this->createFormBuilder($article)
                ->add('title', TextType::class, array('attr' =>
                    array('class' => 'form-control')))
                ->add('body', TextareaType::class, array(
                    'required' => false,
                    'attr' => array('class' => 'form-control')
                ))
                ->add('save', SubmitType::class, array(
                    'label' => 'Update',
                    'attr' => array('class' => 'btn btn-primary mt-3')
                ))
                ->getForm();

            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();

                return $this->redirectToRoute('article_list');
            }

            return $this->render('articles/edit.html.twig', array(
                'form' => $form->createView()
            ));
        }

        /**
         * @Route("/article/{id}", name="article_show")
         *
         * @param Article $article
         *
         * @return Response
         */

        public function show(Article $article)
        {
            return $this->render('articles/show.html.twig', ['article' => $article]);
        }

        /**
         * @Route("/article/delete/{id}")
         *
         * @Method({"DELETE"})
         *
         * @param Request $request
         * @param Article $article
         */

        public function delete(Request $request, Article $article){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();

            $response = new Response();
            $response->send();
        }


//        /**
//         * @Route("/article/save")
//         */
//
//        public function save(){
//            $entityManager = $this->getDoctrine()->getManager();
//
//            $article = new Article();
//
//            $article->setTitle('Article two');
//            $article->setBody('This is the body 2');
//
//            $entityManager->persist($article);
//
//            $entityManager->flush();
//
//            return new Response('saved an article with id'.$article->getId());
//        }
    }