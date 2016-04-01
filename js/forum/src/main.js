import app from 'flarum/app';
import { extend, override } from 'flarum/extend';
import LogInModal from 'flarum/components/LogInModal';
import Button from 'flarum/components/Button';
import SessionDropdown from 'flarum/components/SessionDropdown';

app.initializers.add('flarum-sso', () => {

    //Override logout button behavior.
    extend(SessionDropdown.prototype, 'items', function (items) {
        const url = app.forum.attribute('ssoLogoutUrl');
        if (!url || !url.length) {
            if (items.has('logOut')) {
                items.replace('logOut', Button.component({
                        icon: 'sign-out',
                        children: app.translator.trans('flarum-ext-sso.forum.sso_log_out'),
                        onclick: () => window.location = app.forum.attribute('baseUrl')
                            + '/auth/sso/logout?token=' + app.session.csrfToken
                    }),
                    -100);
            }
        } else {
            items.remove('logOut');
        }
    });

    //Override Login modal to provide only SSO
    override(LogInModal.prototype, 'content', function () {
        const url = app.forum.attribute('ssoLoginUrl');
        return [
            <div className="Modal-body">
                <div className="Form Form--centered">
                    <div className="Form-group">
                        {Button.component({
                            className: 'Button Button--primary Button--block',
                            onclick: () => window.location.href = url,
                            children: app.translator.trans('flarum-ext-sso.forum.sso_log_in')
                        })}
                    </div>
                </div>
            </div>
        ];
    });
});
