<?php

/**
 * This file is part of the menu-bundle.
 */

namespace Kematjaya\MenuBundle\Controller;

use Kematjaya\UserBundle\Entity\KmjUserInterface;
use Kematjaya\URLBundle\Type\AccessControlType;
use Kematjaya\URLBundle\Source\RoutingSourceInterface;
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
    public function index(RoleHierarchyInterface $roleHierarchy): Response
    {
        return $this->render('@Menu/access_control/index.html.twig', [
            'roles' => $this->getRoles($roleHierarchy)
        ]);
    }
    
    public function show(Request $request, string $role, RoutingSourceInterface $routingSource)
    {
        $form = $this->createForm(AccessControlType::class, null, [
            'role' => $role
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            try {
                $routingSource->dump($form->getData());
                $this->addFlash('info', 'update berhasil.');
                
                return $this->redirectToRoute('kmj_menu_access_control_show', ['role' => $role]);
            } catch (\Exception $ex) {
                $this->addFlash('error', $ex->getMessage());
            }
        }
        
        return $this->render('@Menu/access_control/show.html.twig', [
            'role' => $role,
            'form' => $form->createView()
        ]);
    }
    
    protected function getRoles(RoleHierarchyInterface $roleHierarchy):array
    {
        $roles = $roleHierarchy->getReachableRoleNames($this->getUser()->getRoles());
        $roles = array_map(function ($row) {
            
            return KmjUserInterface::ROLE_USER === $row ? null : $row;
        }, $roles);
        
        return array_filter($roles, function ($row) {
            
            return null !== $row;
        });
    }
}
