<?php
namespace Deployer;
require 'recipe/laravel.php';
require 'contrib/rsync.php';
require 'recipe/common.php';
set('log_files', 'storage/logs/*.log');

set('application', 'Return');
set('ssh_multiplexing', true);
set('rsync_src', function () {
    return __DIR__;
});
// Shared files between deploys
add('shared_files', ['.env']);
// Shared dirs between deploys
add('shared_dirs', ['node_modules', 'storage']);
set('writable_dirs', ['storage', 'bootstrap/cache']);
add('rsync', [
    'exclude' => [
        '.git',
        '/.env',
        // '/storage/',
        '/vendor/',
        '/node_modules/',
        '.github',
        'deploy.php',
    ],
]);

task('deploy:secrets', function () {
    file_put_contents(__DIR__ . '/.env', getenv('DOT_ENV'));
    upload('.env', get('deploy_path') . '/shared' );
});

// host('tempStaging')
// ->setHostname('159.89.98.163') 
// ->set('branch', 'tempStaging')
// ->set('remote_user', 'root')
// ->set('deploy_path', '/var/www/staging/temp-boutiques-users');

host('staging')
->setHostname('159.89.98.163') 
->set('branch', 'staging')
->set('remote_user', 'root')
->set('deploy_path', '/var/www/staging/return-feed');

// host('production')
// ->setHostname('104.248.141.194') 
// ->set('branch', 'deploy')
// ->set('remote_user', 'root')
// ->set('deploy_path', '/var/www/nadeera-boutiques-users');

//test
desc('Execute artisan key:generate');
task('artisan:key:generate', artisan('key:generate'));
desc('Execute artisan cache:clear');
task('artisan:cache:clear', artisan('cache:clear'));
desc('Execute artisan config:clear');
task('artisan:config:clear', artisan('config:clear'));
desc('Execute artisan config:cache');
task('artisan:config:cache', artisan('config:cache'));
desc('Deploy the application');
// task('deploy', [
//     'deploy:unlock',
// ]);
desc('Deploy the application');
task('deploy', [
    'deploy:info',
    //'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'rsync',
    'deploy:secrets',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'artisan:storage:link',
    'artisan:key:generate',
    'artisan:config:clear',
    'artisan:cache:clear',
    'artisan:config:cache',
    'deploy:symlink',
    'deploy:unlock',
    'deploy:cleanup',
]);
desc('End of Deploy the application');
after('deploy:failed', 'deploy:unlock');
