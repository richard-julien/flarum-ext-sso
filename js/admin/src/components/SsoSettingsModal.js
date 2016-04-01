import SettingsModal from 'flarum/components/SettingsModal';

export default class SsoSettingsModal extends SettingsModal {
  className() {
    return 'SsoSettingsModal Modal--small';
  }

  title() {
    return app.translator.trans('flarum-ext-sso.admin.sso_settings.title');
  }

  form() {
    return [

      <div className="Form-group">
        <label>{app.translator.trans('flarum-ext-sso.admin.sso_settings.id')}</label>
        <input className="FormControl" required bidi={this.setting('flarum-ext-sso.id')}/>
      </div>,

      <div className="Form-group">
        <label>{app.translator.trans('flarum-ext-sso.admin.sso_settings.loginUrl')}</label>
        <input className="FormControl" required bidi={this.setting('flarum-ext-sso.url')}/>
      </div>,

      <div className="Form-group">
        <label>{app.translator.trans('flarum-ext-sso.admin.sso_settings.logoutUrl')}</label>
        <input className="FormControl" bidi={this.setting('flarum-ext-sso.logoutUrl')}/>
      </div>,

      <div className="Form-group">
        <label>{app.translator.trans('flarum-ext-sso.admin.sso_settings.secret')}</label>
        <input className="FormControl" required bidi={this.setting('flarum-ext-sso.secret')}/>
      </div>
    ];
  }
}
