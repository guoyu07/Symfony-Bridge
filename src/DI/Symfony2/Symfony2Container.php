<?php

namespace DI\Symfony2;

use DI\Container;
use DI\NotFoundException;
use Symfony\Component\DependencyInjection\Container as SymfonyContainer;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * Replacement for the Symfony 2 container.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class Symfony2Container extends SymfonyContainer
{
    /**
     * @var Container
     */
    private $fallbackContainer;

    /**
     * @param Container $container
     */
    public function setPHPDIContainer(Container $container)
    {
        $this->fallbackContainer = $container;
    }

    public function has($id)
    {
        if (parent::has($id)) {
            return true;
        }

        if (! $this->fallbackContainer) {
            return false;
        }

        return $this->fallbackContainer->has($id);
    }

    public function get($id, $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE)
    {
        if (parent::has($id)) {
            return parent::get($id, $invalidBehavior);
        }

        if (! $this->fallbackContainer) {
            return false;
        }

        try {
            return $this->fallbackContainer->get($id);
        } catch (NotFoundException $e) {
            if ($invalidBehavior === self::EXCEPTION_ON_INVALID_REFERENCE) {
                throw new ServiceNotFoundException($id);
            }
        }

        return null;
    }
}
