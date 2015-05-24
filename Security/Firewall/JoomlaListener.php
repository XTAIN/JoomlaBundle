<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Security\Firewall;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use XTAIN\Bundle\JoomlaBundle\Joomla\JoomlaInterface;
use XTAIN\Bundle\JoomlaBundle\Security\Authentication\Token\JoomlaToken;

/**
 * Class JoomlaListener
 *
 * @author Maximilian Ruta <mr@xtain.net>
 */
class JoomlaListener implements ListenerInterface
{
    /**
     * @var string
     */
    protected $providerKey;

    /**
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    protected $securityContext;

    /**
     * @var \Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface
     */
    protected $authenticationManager;

    /**
     * @var JoomlaInterface
     */
    protected $joomla;

    /**
     * @param SecurityContextInterface       $securityContext
     * @param AuthenticationManagerInterface $authenticationManager
     * @param string                         $providerKey
     * @param JoomlaInterface                $joomla
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function __construct(SecurityContextInterface $securityContext,
                                AuthenticationManagerInterface $authenticationManager,
                                $providerKey,
                                JoomlaInterface $joomla)
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->providerKey = $providerKey;
        $this->joomla = $joomla;
    }

    /**
     * This interface must be implemented by firewall listeners.
     *
     * @param GetResponseEvent $event
     */
    public function handle(GetResponseEvent $event)
    {
        $this->joomla->getApplication();
        $user = \JFactory::getUser();

        if ($user instanceof UserInterface && $user->getUsername() !== null) {

            $token = new JoomlaToken(
                $user,
                $user->getPassword(),
                $this->providerKey,
                $user->getRoles()
            );

            try {
                $authToken = $this->authenticationManager->authenticate($token);
                $this->securityContext->setToken($authToken);

                return;
            } catch (AuthenticationException $failed) {
                // TODO logging
            }
        } else {
            $this->securityContext->setToken(null);
        }
    }
}
