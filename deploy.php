<?php

namespace Deployer;

require 'recipe/laravel.php';

// Config

set('repository', 'https://github.com/kokko-ch/kokko.git');
set('bin/php', 'php81');
set('keep_releases', 3);

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts

host('app.kokko.ch')
    ->set('deploy_path', '~/public_html/shared/app.kokko.ch');

// Tasks

task('npm:install', function () {
    cd('{{release_or_current_path}}');
    run('nvm use --delete-prefix --lts && npm ci');
});

task('npm:build', function () {
    cd('{{release_or_current_path}}');
    run('nvm use --delete-prefix --lts && npm run build');
});

task('npm:prune', function () {
    cd('{{release_or_current_path}}');
    run('nvm use --delete-prefix --lts && npm prune --production');
});

task('npm', [
    'npm:install',
    'npm:build',
    'npm:prune',
]);

task('deploy:writable')->disable();

// Hooks

after('deploy:vendors', 'npm');
after('deploy:failed', 'deploy:unlock');
