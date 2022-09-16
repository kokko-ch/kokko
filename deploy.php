<?php

namespace Deployer;

require 'recipe/laravel.php';

// Config

set('repository', 'https://github.com/kokko-ch/kokko.git');
set('bin/php', 'php81');

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

task('npm', [
    'npm:install',
    'npm:build',
]);

// task('deploy:writable')->disable();

// Hooks

after('deploy:update_code', 'npm');
after('deploy:failed', 'deploy:unlock');
