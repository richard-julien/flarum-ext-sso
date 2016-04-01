import app from 'flarum/app';

import SsoSettingsModal from 'flarum/auth/sso/components/SsoSettingsModal';

app.initializers.add('flarum-sso', () => {
  app.extensionSettings['flarum-sso'] = () => app.modal.show(new SsoSettingsModal());
});
