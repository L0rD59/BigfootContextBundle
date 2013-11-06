<?php

namespace Bigfoot\Bundle\ContextBundle\Controller;

use Bigfoot\Bundle\ContextBundle\Entity\ContextualizableEntities;
use Bigfoot\Bundle\ContextBundle\Entity\ContextualizableEntity;
use Bigfoot\Bundle\CoreBundle\Controller\AdminControllerInterface;

use Bigfoot\Bundle\CoreBundle\Crud\CrudController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Context controller.
 *
 * @Cache(maxage="0", smaxage="0", public="false")
 * @Route("/admin/context")
 */
class ContextController extends CrudController
{
    public function getEntity()
    {
        return 'BigfootContextBundle:ContextualizableEntities';
    }

    public function getName()
    {
        return 'admin_context';
    }

    public function getFields()
    {
        return array(
            'id'    => 'Slug',
            'label' => 'Label',
        );
    }

    protected function getEntityLabelPlural()
    {
        return 'Contextualizable Entities';
    }

    /**
     * @return string Route to be used as the homepage for this controller
     */
    public function getControllerIndex()
    {
        return '';
    }

    /**
     * @return string Title to be used in the BackOffice for routes implemented by this controller
     */
    public function getControllerTitle()
    {
        return 'Context admin';
    }

    /**
     * @Route("/", name="admin_context")
     * @Method("GET")
     * @Template("BigfootCoreBundle:crud:index.html.twig")
     */
    public function indexAction()
    {
        $contextsConfig = $this->container->getParameter('bigfoot_contexts');

        foreach ($contextsConfig as $key => $context) {
            $context['id'] = $key;
            $contextsConfig[$key] = $context;
        }

        return array(
            'list_items'        => $contextsConfig,
            'list_edit_route'   => $this->getRouteNameForAction('edit'),
            'list_title'        => $this->getEntityLabelPlural(),
            'list_fields'       => $this->getFields(),
            'breadcrumbs'       => array(
                array(
                    'url'   => $this->generateUrl($this->getRouteNameForAction('index')),
                    'label' => $this->getEntityLabelPlural()
                ),
            ),
        );
    }

    /**
     * Displays a form to create a new ContextualizableEntities entity.
     *
     * @Route("/new/{context}", name="admin_context_new")
     * @Method("GET")
     * @Template("BigfootCoreBundle:crud:new.html.twig")
     */
    public function newAction($context)
    {
        $entity = new ContextualizableEntities();
        $entity->setContext($context);
        $form = $this->createForm('bigfoot_contextualizable_entities', $entity);

        return array(
            'form'          => $form->createView(),
            'form_title'    => sprintf('%s creation', $this->getEntityLabel()),
            'form_action'   => $this->generateUrl($this->getRouteNameForAction('create')),
            'form_submit'   => 'Create',
            'cancel_route'  => $this->getRouteNameForAction('index'),
            'isAjax'        => $this->get('request')->isXmlHttpRequest(),
            'breadcrumbs'       => array(
                array(
                    'url'   => $this->generateUrl($this->getRouteNameForAction('index')),
                    'label' => $this->getEntityLabelPlural()
                ),
                array(
                    'url'   => $this->generateUrl($this->getRouteNameForAction('new'), array('context' => $context)),
                    'label' => sprintf('%s creation', $this->getEntityLabel())
                ),
            ),
        );
    }

    /**
     * Creates a new ContextualizableEntities entity.
     *
     * @Route("/", name="admin_context_create")
     * @Method("POST")
     * @Template("BigfootCoreBundle:crud:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new ContextualizableEntities();
        $form = $this->createForm('bigfoot_contextualizable_entities', $entity);

        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->renderView('BigfootCoreBundle:includes:flash.html.twig', array(
                    'icon' => 'ok',
                    'heading' => 'Success!',
                    'message' => sprintf('The %s has been created.', $this->getEntityName()),
                    'actions' => array(
                        array(
                            'route' => $this->generateUrl($this->getRouteNameForAction('index')),
                            'label' => 'Back to the listing',
                            'type'  => 'success',
                        ),
                    )
                ))
            );

            return $this->redirect($this->generateUrl('admin_context'));
        }

        return array(
            'form'          => $form->createView(),
            'form_title'    => sprintf('%s creation', $this->getEntityLabel()),
            'form_action'   => $this->generateUrl($this->getRouteNameForAction('create')),
            'form_submit'   => 'Create',
            'cancel_route'  => $this->getRouteNameForAction('index'),
            'isAjax'        => $this->get('request')->isXmlHttpRequest(),
            'breadcrumbs'       => array(
                array(
                    'url'   => $this->generateUrl($this->getRouteNameForAction('index')),
                    'label' => $this->getEntityLabelPlural()
                ),
                array(
                    'url'   => $this->generateUrl($this->getRouteNameForAction('new')),
                    'label' => sprintf('%s creation', $this->getEntityLabel())
                ),
            ),
        );
    }

    /**
     * @Route("/{id}", name="admin_context_edit")
     * @Method("GET")
     * @Template("BigfootCoreBundle:crud:edit.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BigfootContextBundle:ContextualizableEntities')->findOneBy(array('context' => $id));

        if (!$entity) {
            return $this->redirect($this->generateUrl('admin_context_new', array('context' => $id)));
        }

        $editForm = $this->createForm('bigfoot_contextualizable_entities', $entity);

        return array(
            'form'              => $editForm->createView(),
            'form_method'       => 'PUT',
            'form_action'       => $this->generateUrl($this->getRouteNameForAction('update'), array('id' => $entity->getId())),
            'form_cancel_route' => $this->getRouteNameForAction('index'),
            'form_title'        => sprintf('%s edit', $this->getEntityLabel()),
            'isAjax'            => $this->get('request')->isXmlHttpRequest(),
            'breadcrumbs'       => array(
                array(
                    'url'   => $this->generateUrl($this->getRouteNameForAction('index')),
                    'label' => $this->getEntityLabelPlural()
                ),
                array(
                    'url'   => $this->generateUrl($this->getRouteNameForAction('edit'), array('id' => $entity->getId())),
                    'label' => sprintf('%s edit', $this->getEntityLabel())
                ),
            ),
        );
    }

    /**
     * Edits an existing ContextualizableEntities entity.
     *
     * @Route("/{id}", name="admin_context_update")
     * @Method("PUT")
     * @Template("BigfootCoreBundle:crud:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BigfootContextBundle:ContextualizableEntities')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException(sprintf('Unable to find %s entity.', 'ContextualizableEntities'));
        }

        $editForm = $this->createForm('bigfoot_contextualizable_entities', $entity);
        $editForm->submit($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->renderView('BigfootCoreBundle:includes:flash.html.twig', array(
                    'icon' => 'ok',
                    'heading' => 'Success!',
                    'message' => sprintf('The %s has been updated.', $this->getEntityName()),
                    'actions' => array(
                        array(
                            'route' => $this->generateUrl($this->getRouteNameForAction('index')),
                            'label' => 'Back to the listing',
                            'type'  => 'success',
                        ),
                    )
                ))
            );

            return $this->redirect($this->generateUrl('admin_context_edit', array('id' => $entity->getContext())));
        }

        return array(
            'form'              => $editForm->createView(),
            'form_method'       => 'PUT',
            'form_action'       => $this->generateUrl($this->getRouteNameForAction('update'), array('id' => $entity->getId())),
            'form_cancel_route' => $this->getRouteNameForAction('index'),
            'form_title'        => sprintf('%s edit', $this->getEntityLabel()),
            'isAjax'            => $this->get('request')->isXmlHttpRequest(),
            'breadcrumbs'       => array(
                array(
                    'url'   => $this->generateUrl($this->getRouteNameForAction('index')),
                    'label' => $this->getEntityLabelPlural()
                ),
                array(
                    'url'   => $this->generateUrl($this->getRouteNameForAction('edit'), array('id' => $entity->getId())),
                    'label' => sprintf('%s edit', $this->getEntityLabel())
                ),
            ),
        );
    }
}
