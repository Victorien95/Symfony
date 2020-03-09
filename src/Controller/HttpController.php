<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HttpController
 * @package App\Controller
 *
 * @Route("/http")
 */
class HttpController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index()
    {
        return $this->render('http/index.html.twig', [
            'controller_name' => 'HttpController',
        ]);
    }

    /**
     * @Route("/requete")
     */
    public function request(Request $request)
    {
        //http://127.0.0.1:8000/http/requete?nom=Marx&prenom=Groucho
        dump($_GET); // ["nom" => "Marx", "prenom" => "Groucho"]

        // $request->query est une surcouche en objet à $_GET
        dump($request->query->all());

        // $_GET['prenom']
        dump($request->query->get('prenom')); // Groucho

        // isset($_GET['prenom'])
        dump($request->query->has('prenom')); //true

        //notice: undefined index
        //dump($_GET['surnom']);

        dump($request->query->get('surnom')); //null

        // optionnel :  valeur par defaut si null
        dump($request->query->get('surnom', 'aucun')); //aucun

        dump($request->getMethod()); // GET ou POST


        if($request->isMethod('POST')){
            //$request->request est une surcouche en objet à $_POST,
            // fonctionne exactement comme $request->query
            dump($request->request->all());
        }

        dump($request->headers->all());
        return $this->render('http/request.html.twig');
    }


//    public function session(Request $request)
//    {   // Pour accéder à la session depuis l'objet Request
//        $session = $request->getSession();
//    }

    /**
     * @Route("/session")
     */
    public function session(SessionInterface $session, Request $request)
    {
        $session->set('nom', 'Marx');
        $session->set('prenom', 'Groucho');

        // Les éléments stockés par l'objet session le sont dans $_SESSION['_sf2_attributes']
        dump($_SESSION);

        // pour accéder à un élément de la session
        dump($session->get('nom'));

        // tout ce qui a été mis dans al session
        // par l'objet Session
        dump($session->all());

        // pour supprimer un élément de la session
        $session->remove('nom');

        dump($session->all());

        // vide tout ce qui a été mis dans la session
        $session->clear();

        dump($session->all());

        return $this->render('http/session.html.twig');
    }

    /**
     * @Route("/reponse")
     */
    public function response(Request $request)
    {
        // toutes les méthodes des contrôleurs doivent retourner un objet instance de la classe Response
        $response = new Response('Contenue de la page');

        // http://127.0.0.1:8000/http/reponse?type=twig
        if ($request->query->get('type') == 'twig'){

            // $this->>render() retourne un objet Response dont le contenu est le HTML construit par le template
            return $this->render('http/response.html.twig');
            // 127.0.0.1:8000/http/response?type=json

        }elseif ($request->query->get('type') == 'json'){
            $tableau = [
                'nom' => 'Crocket',
                'prenom' => 'Davy'
            ];
            // return new Response(json_encode($tableau)); // OU ->
            // encode le tableau en json et le retourne dans une réponse avec l'entête HTTP Content-Type: application/json

            return new JsonResponse($tableau);
        }elseif ($request->query->get('found') == 'no')
        {
            // pour retourner une 404, on jette cette exception :
            throw  new NotFoundHttpException();
            http://127.0.0.1:8000/http/reponse?redirect=home
        }elseif ($request->query->get('redirect') == 'home'){
            return $this->redirectToRoute('app_index_index');
        }
        //http://127.0.0.1:8000/http/reponse?redirect=salut
        elseif ($request->query->get('redirect') == 'salut'){
            return $this->redirectToRoute('app_index_salut',
                [
                    'qui' => 'à toi'
                ]
            );
        }

        return $response;
    }


    /**
     * @Route("/formulaire")
     */
    public function formulaire(Request $request, SessionInterface $session)
    {
        $erreur = '';
        // Si la page a été appelé en POST
        if ($request->isMethod('POST')){
            $email = $request->request->get('email');
            $message = $request->request->get('message');

            if (!empty($email) && !empty($message)){
                $session->set('email', $email);
                $session->set('message', $message);
                return $this->redirectToRoute('app_http_resultat');

            }else{
                $erreur = 'Tous les champs sont obligatoires';
            }
        }
        return $this->render('http/formulaire.html.twig',
            [
                'erreur' => $erreur
            ]
        );
    }

    /**
     * @Route("/resultat")
     */
    public function resultat(SessionInterface $session)
    {
        if (empty($session->all())){
        //if(!$session->has('email')&& !$session->has('massage'))
            return $this->redirectToRoute('app_http_formulaire');
        }
        $email = $session->get('email');
        $message = $session->get('message');

        $session->clear();
       // $session->remove('email');
       // $session->remove('message');
        return $this->render('http/resultat.html.twig', [
            'email' => $email,
            'message' => $message
        ]);
    }



}
