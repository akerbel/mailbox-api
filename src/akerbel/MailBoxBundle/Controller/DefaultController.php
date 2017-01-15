<?php

namespace akerbel\MailBoxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use akerbel\MailBoxBundle\Entity\Message;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('akerbelMailBoxBundle:Default:index.html.twig');
    }

    public function addAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $addressee = $em->getRepository('akerbelMailBoxBundle:User')->findOneBy(["username" => $request->get('addressee')]);

        if (!$addressee) {
            throw new \Exception("The addressee '{$request->get('addressee')}' cannot be found", 404);
        }
        
        $message = new Message();
        $message->setUser($addressee);
        $message->setSender($request->get('sender'));
        $message->setSubject($request->get('subject'));
        $message->setMessage($request->get('message'));

        $validator = $this->get('validator');
        $errors = $validator->validate($message);
        if (count($errors)) {
            throw new \Exception((string) $errors, Response::HTTP_BAD_REQUEST);
        }

        $em->persist($message);
        $em->flush();

        return new JsonResponse(
            [
                'result' => 'success',
            ],
            Response::HTTP_OK
        );

    }

    public function listAction(Request $request)
    {

        $user = $this->getUser();

        $result = [];

        foreach ($user->getMessages($request->get('offset'), $request->get('length')) as $message) {
            $result[] = $message->toArray();
        }

        return new JsonResponse(
            [
                'result' => 'success',
                'messages' => $result,
            ],
            Response::HTTP_OK
        );

    }
    
    public function listArchivedAction(Request $request)
    {
        $user = $this->getUser();

        $result = [];

        foreach ($user->getArchivedMessages($request->get('offset'), $request->get('length')) as $message) {
            $result[] = $message->toArray();
        }

        return new JsonResponse(
            [
                'result' => 'success',
                'messages' => $result,
            ],
            Response::HTTP_OK
        );
    }

    public function showAction($id)
    {
        $user = $this->getUser();

        return new JsonResponse(
            $user->getMessage($id)->toArray(),
            Response::HTTP_OK
        );
    }

    public function archiveAction($id)
    {
        $user = $this->getUser();

        $user->getMessage($id)->setArchived(1);

        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse(
            ['result' => 'success'],
            Response::HTTP_OK
        );
    }

    public function unarchiveAction($id)
    {
        $user = $this->getUser();

        $user->getMessage($id)->setArchived(0);

        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse(
            ['result' => 'success'],
            Response::HTTP_OK
        );
    }

    public function readAction($id)
    {
        $user = $this->getUser();

        $user->getMessage($id)->setReaded(1);

        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse(
            ['result' => 'success'],
            Response::HTTP_OK
        );
    }

    public function unreadAction($id)
    {
        $user = $this->getUser();

        $user->getMessage($id)->setReaded(0);

        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse(
            ['result' => 'success'],
            Response::HTTP_OK
        );
    }

    public function deleteAction($id)
    {
        $user = $this->getUser();

        $message = $user->getMessage($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($message);
        $em->flush();

        return new JsonResponse(
            ['result' => 'success'],
            Response::HTTP_OK
        );
    }
}
