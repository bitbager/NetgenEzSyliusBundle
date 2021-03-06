<?php

namespace Netgen\Bundle\EzSyliusBundle\Entity;

use eZ\Publish\API\Repository\Values\User\User as APIUser;
use eZ\Publish\Core\Repository\Values\User\UserReference;
use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;
use LogicException;

trait User
{
    /**
     * @var \eZ\Publish\API\Repository\Values\User\UserReference
     */
    protected $reference;

    /**
     * @var \eZ\Publish\API\Repository\Values\User\User
     */
    protected $apiUser;

    /**
     * @return \eZ\Publish\API\Repository\Values\User\UserReference
     */
    public function getAPIUserReference()
    {
        if ($this->reference === null && class_exists(UserReference::class)) {
            $this->reference = new UserReference(
                $this->apiUser instanceof APIUser ?
                    $this->apiUser->id :
                    null
            );
        }

        return $this->reference;
    }

    /**
     * @throws \LogicException If api user has not been refreshed yet by UserProvider after being unserialized from session.
     *
     * @return \eZ\Publish\API\Repository\Values\User\User
     */
    public function getAPIUser()
    {
        if (!$this->apiUser instanceof APIUser) {
            throw new LogicException(
                'Attempt to get APIUser before it has been set by UserProvider, APIUser is not serialized to session'
            );
        }

        return $this->apiUser;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\User\User $apiUser
     */
    public function setAPIUser(APIUser $apiUser)
    {
        $this->apiUser = $apiUser;
    }

    /**
     * Compares the users.
     *
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     *
     * @return bool
     */
    public function isEqualTo(BaseUserInterface $user)
    {
        if ($user instanceof self && $this->apiUser instanceof APIUser) {
            return $user->getAPIUser()->id === $this->apiUser->id;
        }

        return false;
    }

    /**
     * Returns string representation of the user.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->apiUser->contentInfo->name;
    }
}
