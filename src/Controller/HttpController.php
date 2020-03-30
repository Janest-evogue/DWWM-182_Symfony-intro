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
        // http://127.0.0.1:8000/http/requete?nom=Marx&prenom=Groucho
        dump($_GET); // ["nom" => "Marx", "prenom" => "Groucho"]

        // $request->query contient un objet qui est une surcouche à $_GET
        dump($request->query->all()); // ["nom" => "Marx", "prenom" => "Groucho"]

        // $_GET['prenom']
        dump($request->query->get('prenom')); // "Groucho"

        // pas de notice si la clé n'existe pas
        dump($request->query->get('surnom')); // null

        // valeur par défaut si la clé n'existe pas
        dump($request->query->get('surnom', 'John Doe'));

        // isset($_GET['surnom'])
        dump($request->query->has('surnom')); // false

        dump($request->getMethod()); // GET ou POST

        // si la page est appelée en POST
        if ($request->isMethod('POST')) {
            // $request->request contient un objet qui est une surcouche à $_POST
            // et contient les mêmes méthodes que $request->query
            dump($request->request->all());
        }

        // pour accéder à la session depuis l'objet request
        $session = $request->getSession();
        dump($session->all());

        return $this->render('http/request.html.twig');
    }

    /**
     * On utilise un paramètre typé sur SessionInterface
     * pour utiliser la session dans une méthode de contrôleur
     *
     * @Route("/session")
     */
    public function session(SessionInterface $session)
    {
        $nom = 'Julien';

        // pour ajouter des éléments à la session
        $session->set('prenom', $nom);
        $session->set('nom', 'Anest');

        // les éléments stockés par l'objet session le sont
        // dans $_SESSION['_sf2_attributes']
        dump($_SESSION);

        // pour accéder directement aux éléments stockés
        // par l'objet session
        dump($session->all());

        // pour accéder à un élément de la session
        dump($session->get('prenom'));

        // pour supprimer un élément de la session
        $session->remove('nom');

        dump($session->all());

        // pour vider la session
        $session->clear();

        dump($session->all());


        return $this->render('http/session.html.twig');
    }

    /**
     * Toutes les méthodes de controleurs doivent retourner
     * un objet instance de Response
     *
     * @Route("/reponse")
     */
    public function response(Request $request)
    {
        // http://127.0.0.1:8000/http/reponse?type=twig
        if ($request->query->get('type') == 'twig') {
            // $this->render retourne un objet Response dont le contenu
            // est le HTML construit par le template
            return $this->render('http/response.html.twig');
        // http://127.0.0.1:8000/http/reponse?type=json
        } elseif ($request->query->get('type') == 'json') {
            $response = [
                'nom' => 'Marx',
                'prenom' => 'Groucho'
            ];

            //return new Response(json_encode($response));

            // encode le tableau $response en json
            // et retourne une réponse avec l'entête HTTP Content-Type: application/json
            return new JsonResponse($response);
        // http://127.0.0.1:8000/http/reponse?found=no
        }elseif ($request->query->get('found') == 'no') {
            // pour retourner une 404, on jette cette exception
            throw new NotFoundHttpException();
        // http://127.0.0.1:8000/http/reponse?redirect=index
        } elseif ($request->query->get('redirect') == 'index') {
            // redirection vers une page en passant le nom de la route
            // app_http_index : la route de HttpController::index()
            return $this->redirectToRoute('app_http_index');
        // http://127.0.0.1:8000/http/reponse?redirect=bonjour
        } elseif ($request->query->get('redirect') == 'bonjour') {
            // redirection vers une route qui contient une partie variable
            return $this->redirectToRoute(
                'app_index_bonjour',
                [
                    'qui' => 'le monde'
                ]
            );
        }

        $response = new Response('Contenu en texte brut de la page');

        return $response;
    }

    /*
     * Faire une page avec un formulaire en post avec :
     * - email (text)
     * - message (textarea)
     *
     * Si le formulaire est envoyé, vérifier que les deux champs sont remplis
     * Si non, afficher un message d'erreur
     * Si oui, enregistrer les valeurs en session et rediriger vers
     * une nouvelle page qui les affiche et vide la session
     * Dans cette page, si la session est vide, on redirige vers le formulaire
     */

    /**
     * @Route("/formulaire")
     */
    public function formulaire(Request $request, SessionInterface $session)
    {
        $erreur = '';

        // si le form a été envoyé
        if ($request->isMethod('POST')) {
            // $_POST['email'] et $_POST['message']
            $email = trim($request->request->get('email'));
            $message = trim($request->request->get('message'));

            if (!empty($email) && !empty($message)) {
                // pour accéder à la session sans la passer en paramètre à la méthode
                // $session = $request->getSession();

                $session->set('email', $email);
                $session->set('message', $message);

                return $this->redirectToRoute('app_http_affichagecontenu');
            } else {
                $erreur = 'Tous les champs sont obligatoires';
            }
        }

        return $this->render(
            'http/formulaire.html.twig',
            [
                'erreur' => $erreur
            ]
        );
    }

    /**
     * @Route("/affichage")
     */
    public function affichageContenu(SessionInterface $session)
    {
        if (empty($session->all())) {
            return $this->redirectToRoute('app_http_formulaire');
        }

        // dump($session->all());
        $email = $session->get('email');
        $message = $session->get('message');

        $session->clear();

        return $this->render(
            'http/affichage.html.twig',
            [
                'email' => $email,
                'message' => $message
            ]
        );
    }
}
