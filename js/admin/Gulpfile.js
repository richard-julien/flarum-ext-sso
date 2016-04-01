var gulp = require('flarum-gulp');

gulp({
  modules: {
    'flarum/auth/sso': 'src/**/*.js'
  }
});
