<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use XTAIN\Bundle\JoomlaBundle\Security\Authentication\Token\JoomlaToken;

/**
 * Class JoomlaAuthenticationProvider
 *
 * @author Maximilian Ruta <mr@xtain.net>
 */
class JoomlaAuthenticationProvider implements AuthenticationProviderInterface
{
    /**
     * @var string
     */
    protected $providerKey;

    /**
     * @var UserCheckerInterface
     */
    protected $userChecker;

    /**
     * @var UserProviderInterface
     */
    protected $userProvider;

    /**
     * Constructor.
     *
     * @param UserCheckerInterface $userChecker An UserCheckerInterface interface
     * @param string               $providerKey
     */
    public function __construct(UserCheckerInterface $userChecker, $providerKey)
    {
        $this->userChecker = $userChecker;
        $this->providerKey = $providerKey;
    }

    /**
     * @param UserProviderInterface $userProvider
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setUserProvider(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    /**
     * {@inheritdoc}
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return null;
        }

        $user = $token->getUser();

        if (!($user instanceof UserInterface)) {
            return null;
        }

        $user = $this->userProvider->refreshUser($user);
        $this->userChecker->checkPostAuth($user);

        $authenticatedToken = new JoomlaToken(
            $user,
            $user->getPassword(),
            $this->providerKey,
            $user->getRoles()
        );
        $authenticatedToken->setAttributes($token->getAttributes());

        return $authenticatedToken;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof JoomlaToken;
    }
}
