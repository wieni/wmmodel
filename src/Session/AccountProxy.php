<?php

namespace Drupal\wmmodel\Session;

use Drupal\Core\Session\AccountProxy as DrupalAccountProxy;
use Drupal\Core\Session\AccountInterface;

class AccountProxy extends DrupalAccountProxy
{

    /** @var Drupal\user\Entity\User */
    protected $fullUser;

    public function setAccount(AccountInterface $account)
    {
        if ($account instanceof DrupalAccountProxy) {
            $account = $this->getCachedAccount();
        }

        $this->account = $account;
        $this->id = $account->id();

        // Ugh wtf bruh
        date_default_timezone_set(drupal_get_user_timezone());
    }

    public function getAccount()
    {
        if (!isset($this->fullUser)) {
            $user = $this->loadUserEntity($this->id);
            $this->fullUser = $user;
            $this->setAccount($user);
            $this->account = $this->fullUser;
        }

        return $this->account;
    }

    protected function getCachedAccount()
    {
        return parent::getAccount();
    }

    public function id()
    {
        return $this->id;
    }

    public function getRoles($exclude_locked_roles = false)
    {
        return $this->getCachedAccount()->getRoles($exclude_locked_roles);
    }

    public function hasPermission($permission)
    {
        return $this->getCachedAccount()->hasPermission($permission);
    }

    public function isAuthenticated()
    {
        return $this->getCachedAccount()->isAuthenticated();
    }

    public function isAnonymous()
    {
        return $this->getCachedAccount()->isAnonymous();
    }

    public function getPreferredLangcode($fallback_to_default = true)
    {
        return $this->getCachedAccount()->getPreferredLangcode($fallback_to_default);
    }

    public function getPreferredAdminLangcode($fallback_to_default = true)
    {
        return $this->getCachedAccount()->getPreferredAdminLangcode($fallback_to_default);
    }

    public function getAccountName()
    {
        return $this->getCachedAccount()->getAccountName();
    }

    public function getDisplayName()
    {
        return $this->getCachedAccount()->getDisplayName();
    }

    public function getEmail()
    {
        return $this->getCachedAccount()->getEmail();
    }

    public function getTimeZone()
    {
        return $this->getCachedAccount()->getTimeZone();
    }

    public function getLastAccessedTime()
    {
        return $this->getCachedAccount()->getLastAccessedTime();
    }
}
