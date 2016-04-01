<?php
/*
 * This file is part of Flarum.
 *
 * (c) Toby Zerner <toby.zerner@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flarum\Auth\Sso;

use Flarum\Core\User;
use Flarum\Forum\AuthenticationResponseFactory;
use Flarum\Foundation\Application;
use Flarum\Http\Controller\ControllerInterface;
use Flarum\Http\Exception\TokenMismatchException;
use Flarum\Http\Rememberer;
use Flarum\Http\SessionAuthenticator;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\RedirectResponse;

class SsoLogoutAuthController implements ControllerInterface
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @var AuthenticationResponseFactory
     */
    protected $authResponse;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Dispatcher
     */
    protected $events;

    /**
     * @var SessionAuthenticator
     */
    protected $authenticator;

    /**
     * @var Rememberer
     */
    protected $rememberer;

    public function __construct(
        SettingsRepositoryInterface $settings,
        Application $app,
        Dispatcher $events,
        SessionAuthenticator $authenticator,
        Rememberer $rememberer
    ) {
        $this->settings = $settings;
        $this->app = $app;
        $this->events = $events;
        $this->authenticator = $authenticator;
        $this->rememberer = $rememberer;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return \Psr\Http\Message\ResponseInterface|RedirectResponse
     * @throws TokenMismatchException
     */
    public function handle(ServerRequestInterface $request)
    {
        $session = $request->getAttribute('session');
        if ($user = User::find($session->get('user_id'))) {
            //Clean session of Flarum
            if (array_get($request->getQueryParams(), 'token') !== $session->get('csrf_token')) {
                throw new TokenMismatchException;
            }
            $this->authenticator->logOut($session);
            $user->accessTokens()->delete();
            //Logout from SSO
            $logoutUrl = $this->settings->get('flarum-ext-sso.logoutUrl');
            return new RedirectResponse($logoutUrl);
        } else {
            return new RedirectResponse($this->app->url());
        }
    }
}