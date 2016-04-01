<?php
/*
 * This file is part of Flarum.
 *
 * (c) Toby Zerner <toby.zerner@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flarum\Auth\Sso\Listener;

use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Auth\Sso\FlarumSingleSignOn;
use Flarum\Event\ConfigureForumRoutes;
use Flarum\Event\PrepareApiAttributes;
use Flarum\Foundation\Application;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Symfony\Component\HttpFoundation\Session\Session;

class AddSsoAuthRoute
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @param SettingsRepositoryInterface $settings
     */
    public function __construct(SettingsRepositoryInterface $settings, Session $session, Application $app)
    {
        $this->settings = $settings;
        $this->app = $app;
        $this->session = $session;
    }

    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(PrepareApiAttributes::class, [$this, 'addAttributes']);
        $events->listen(ConfigureForumRoutes::class, [$this, 'configureForumRoutes']);
    }

    /**
     * @param PrepareApiAttributes $event
     */
    public function addAttributes(PrepareApiAttributes $event)
    {
        if ($event->isSerializer(ForumSerializer::class)) {
            $currentUrl = $this->app->url() . $_SERVER['REQUEST_URI'];
            $nonce = md5(uniqid(rand(), true));
            $this->session->set('sso_nonce', $nonce);
            $event->attributes['ssoLoginUrl'] = FlarumSingleSignOn::toUrl(
                $this->settings->get('flarum-ext-sso.url'),
                $this->settings->get('flarum-ext-sso.secret'),
                [
                    'clientId' => $this->settings->get('flarum-ext-sso.id'),
                    'callbackUrl' => $currentUrl,
                    'nonce' => $nonce
                ]);
        }
    }

    /**
     * @param ConfigureForumRoutes $event
     */
    public function configureForumRoutes(ConfigureForumRoutes $event)
    {
        $event->get('/auth/sso/login',  'auth.sso.login', 'Flarum\Auth\Sso\SSoLoginAuthController');
        $event->get('/auth/sso/logout', 'auth.sso.logout', 'Flarum\Auth\Sso\SSoLogoutAuthController');
    }
}
