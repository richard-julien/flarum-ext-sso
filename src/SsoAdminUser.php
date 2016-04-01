<?php
/**
 * Created by PhpStorm.
 * User: julienrichard
 * Date: 01/04/2016
 * Time: 01:43
 */

namespace Flarum\Auth\Sso;

use Flarum\Core\User;

class SsoAdminUser extends User
{
    /**
     * Override the ID of this user, virtual root
     *
     * @var int
     */
    public $id = 0;

    /**
     * {@inheritdoc}
     */
    public function isAdmin()
    {
        return true;
    }
}
