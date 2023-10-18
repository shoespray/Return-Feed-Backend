<?php
namespace Deployer;
require 'recipe/laravel.php';
require 'contrib/rsync.php';
require 'recipe/common.php';
set('log_files', 'storage/logs/*.log');

set('laravel_version', function (){

    $result = run('{{bin/php}} {{release_path}}/artisan --version');
    preg_match_all('/(\d+\.?)+/', $result, $matches);

    return $matches[0][0] ?? 5.5;

});

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

host('ksaDeploy')
->setHostname('38.54.38.25')
->set('branch', 'ksa-deploy')
->set('remote_user', 'ubuntu')
->set('deploy_path', '/var/www/return-feed');

host('production')
->setHostname('104.248.141.194') 
->set('branch', 'deploy')
->set('remote_user', 'root')
->set('deploy_path', '/var/www/return-feed');

host('staging')
->setHostname('159.89.98.163') 
->set('branch', 'staging')
->set('remote_user', 'root')
->set('deploy_path', '/var/www/staging/return-feed');

host('production')
->setHostname('104.248.141.194') 
->set('branch', 'deploy')
->set('remote_user', 'root')
->set('deploy_path', '/var/www/return-feed');

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

desc('Make writable dirs');
task('deploy:writable', function () {
    $dirs = join(' ', get('writable_dirs'));
    $mode = get('writable_mode');
    $sudo = get('writable_use_sudo') ? 'sudo' : '';
    $httpUser = get('http_user');

    if (empty($dirs)) {
        return;
    }

    cd('{{release_path}}');

    // Create directories if they don't exist
    run("mkdir -p $dirs");

    $recursive = get('writable_recursive') ? '-R' : '';

    if ($mode === 'chown') {
        // Change owner.
        // -R   operate on files and directories recursively
        // -L   traverse every symbolic link to a directory encountered
        run("$sudo chown -L $recursive $httpUser $dirs");
    } elseif ($mode === 'chgrp') {
        // Change group ownership.
        // -R   operate on files and directories recursively
        // -L   if a command line argument is a symbolic link to a directory, traverse it
        $httpGroup = get('http_group', false);
        if ($httpGroup === false) {
            throw new \RuntimeException("Please setup `http_group` config parameter.");
        }
        run("$sudo chgrp -H $recursive $httpGroup $dirs");
    } elseif ($mode === 'chmod') {
        // in chmod mode, defined `writable_chmod_recursive` has priority over common `writable_recursive`
        if (is_bool(get('writable_chmod_recursive'))) {
            $recursive = get('writable_chmod_recursive') ? '-R' : '';
        }
        run("$sudo chmod $recursive {{writable_chmod_mode}} $dirs");
    } elseif ($mode === 'acl') {
        if (strpos(run("chmod 2>&1; true"), '+a') !== false) {
            // Try OS-X specific setting of access-rights

            run("$sudo chmod +a \"$httpUser allow delete,write,append,file_inherit,directory_inherit\" $dirs");
            run("$sudo chmod +a \"`whoami` allow delete,write,append,file_inherit,directory_inherit\" $dirs");
        } elseif (commandExist('setfacl')) {
            if (!empty($sudo)) {
                run("$sudo setfacl -L $recursive -m u:\"$httpUser\":rwX -m u:`whoami`:rwX $dirs");
                run("$sudo setfacl -dL $recursive -m u:\"$httpUser\":rwX -m u:`whoami`:rwX $dirs");
            } else {
                // When running without sudo, exception may be thrown
                // if executing setfacl on files created by http user (in directory that has been setfacl before).
                // These directories/files should be skipped.
                // Now, we will check each directory for ACL and only setfacl for which has not been set before.
                $writeableDirs = get('writable_dirs');
                foreach ($writeableDirs as $dir) {
                    // Check if ACL has been set or not
                    $hasfacl = run("getfacl -p $dir | grep \"^user:$httpUser:.*w\" | wc -l");
                    // Set ACL for directory if it has not been set before
                    if (!$hasfacl) {
                        run("setfacl -L $recursive -m u:\"$httpUser\":rwX -m u:`whoami`:rwX $dir");
                        run("setfacl -dL $recursive -m u:\"$httpUser\":rwX -m u:`whoami`:rwX $dir");
                    }
                }
            }
        } else {
            $alias = currentHost()->getAlias();
            throw new \RuntimeException("Can't set writable dirs with ACL.\nInstall ACL with next command:\ndep run $alias -- sudo apt-get install acl");
        }
    } else {
        throw new \RuntimeException("Unknown writable_mode `$mode`.");
    }
});

// task('deploy', [
//     'deploy:unlock',
// ]);
desc('Deploy the application');
task('deploy', [
    'deploy:info',
    //'deploy:prepare',
    //'deploy:lock',
    'deploy:release',
    'rsync',
    'deploy:secrets',
    'deploy:shared',
    'deploy:vendors',
    //'deploy:writable',
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
