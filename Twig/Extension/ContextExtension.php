<?php

namespace Bigfoot\Bundle\ContextBundle\Twig\Extension;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig_Extension;
use Twig_Function_Method;

use Bigfoot\Bundle\ContentBundle\Entity\Page;
use Bigfoot\Bundle\ContentBundle\Entity\Sidebar;
use Bigfoot\Bundle\ContentBundle\Entity\Page\Block as PageBlock;
use Bigfoot\Bundle\ContentBundle\Entity\Page\Sidebar as PageSidebar;
use Bigfoot\Bundle\ContentBundle\Entity\Block;
use Bigfoot\Bundle\ContextBundle\Service\ContextService;

/**
 * ContextExtension
 */
class ContextExtension extends Twig_Extension
{
    private $contextService;

    /**
     * Construct ContentExtension
     */
    public function __construct(ContextService $contextService)
    {
        $this->contextService = $contextService;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'get_context' => new Twig_Function_Method($this, 'getContext'),
        );
    }

    public function getContext($name, $value = null)
    {
        return $this->contextService->get($name, $value);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bigfoot_context';
    }
}