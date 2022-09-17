<?php

namespace Deployer;

if ('cli' !== PHP_SAPI) {
    throw new \Exception('The deployer configuration must be used in cli.');
}

// Der Pfad ist ggf. anzupassen, falls der Projekt-Root nicht dem REDAXO-Root entspricht
require __DIR__.'/redaxo/src/addons/ydeploy/deploy.php';

set('repository', 'https://github.com/xong/ydeploy_test.git');

host('staging')
    ->hostname('maumha.de')
    ->set('deploy_path', '/kunden/536664_06108/webseiten/ydeploy-test.maumha.de')
;