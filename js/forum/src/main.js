import app from 'flarum/app';
import { extend, override } from 'flarum/extend';
import LogInModal from 'flarum/components/LogInModal';
import Button from 'flarum/components/Button';
import SessionDropdown from 'flarum/components/SessionDropdown';
import HeaderSecondary from 'flarum/components/HeaderSecondary';

app.initializers.add('flarum-sso', () => {

    //Override secondary headers.
    extend(HeaderSecondary.prototype, 'items', function(items) {
        //Remove sign up link in case its not disable in administration.
        if (items.has('signUp')) {
            items.remove('signUp');
        }

        //Change behavior of the global login link
        if (items.has('logIn')) {
            const url = app.forum.attribute('ssoLoginUrl');
            items.replace('logIn', Button.component({
                children: app.translator.trans('flarum-ext-sso.forum.sso_log_in'),
                className: 'Button Button--link',
                onclick: () => window.location.href = url
            }), 0);
        }
    });

    //Override Session dropdown.
    extend(SessionDropdown.prototype, 'items', function (items) {
        //Change logout behavior if sso logout uri is provided.
        const url = app.forum.attribute('ssoLogoutUrl');
        console.log(url);
        if (url && url.length > 0) {
            if (items.has('logOut')) {
                items.replace('logOut', Button.component({
                        icon: 'sign-out',
                        children: app.translator.trans('flarum-ext-sso.forum.sso_log_out'),
                        onclick: () => window.location = app.forum.attribute('baseUrl')
                            + '/auth/sso/logout?token=' + app.session.csrfToken
                    }),
                    -100);
            }
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
