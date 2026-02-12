<?php

namespace Kematjaya\MenuBundle\Controller;

use Kematjaya\MenuBundle\MenuTreeGenerator;
use Kematjaya\URLBundle\Type\AccessControlType;
use Kematjaya\URLBundle\Repository\URLRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @package Kematjaya\MenuBundle\Controller
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class AccessControlController extends AbstractController
{

    public function __construct(private Security $security)
    {
    }
    public function index(RoleHierarchyInterface $roleHierarchy): Response
    {
        return $this->render('@Menu/access_control/index.html.twig', [
            'roles' => $this->getRoles($roleHierarchy)
        ]);
    }

    public function show(Request $request, string $role, URLRepositoryInterface $URLRepository, MenuTreeGenerator $generator)
    {
        $form = $this->createForm(AccessControlType::class, null, [
            'role' => $role
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            try {

                $URLRepository->save($form->getData());

                $this->addFlash('info', 'update berhasil.');

                return $this->redirectToRoute('kmj_menu_access_control_show', ['role' => $role]);
            } catch (\Exception $ex) {
                $this->addFlash('error', $ex->getMessage());
            }
        }

        return $this->render('@Menu/access_control/show.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
            "groups" => $generator->getGroupTree()
        ]);
    }

    protected function getRoles(RoleHierarchyInterface $roleHierarchy):array
    {
        $roles = array_map(function ($row) {

            return "ROLE_USER" === $row ? null : $row;
        }, $roleHierarchy->getReachableRoleNames($this->security->getUser()->getRoles()));

        return array_filter($roles, function ($row) {
            if (null === $row) {
                return false;
            }
            return !in_array($row, $this->security->getUser()->getRoles());
        });
    }
}
