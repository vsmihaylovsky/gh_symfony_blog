<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 2/7/16
 * Time: 11:54 PM
 */

namespace AppBundle\Controller\Administration;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\User;

/**
 * @Route("/administration/user")
 */
class UserController extends Controller
{
    /**
     * @return array
     * @Route("/", name="show_all_users")
     * @Method("GET")
     * @Template("AppBundle:User:showAll.html.twig")
     */
    public function showAllAction()
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:User');

        $users = $repository->findAll();

        return ['users' => $users];
    }

    /**
     * @param $slug
     * @return array
     * @Route("/set_user_role/{slug}", name="set_user_role")
     * @Method("GET")
     */
    public function setUserRoleAction($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneBy(['slug' => $slug]);
        if (!$user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $user->setRoles(['ROLE_USER']);
        $em->flush();

        return $this->redirect($this->generateUrl('show_all_users'));
    }

    /**
     * @param $slug
     * @return array
     * @Route("/set_moderator_role/{slug}", name="set_moderator_role")
     * @Method("GET")
     */
    public function setModeratorRoleAction($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneBy(['slug' => $slug]);
        if (!$user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $user->setRoles(['ROLE_MODERATOR']);

        $em->flush();

        return $this->redirect($this->generateUrl('show_all_users'));
    }

    /**
     * @param $slug
     * @return array
     * @Route("/switch_active/{slug}", name="switch_active")
     * @Method("GET")
     */
    public function switchActiveAction($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneBy(['slug' => $slug]);
        if (!$user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $user->setIsActive(!$user->getIsActive());

        $em->flush();

        return $this->redirect($this->generateUrl('show_all_users'));
    }
}