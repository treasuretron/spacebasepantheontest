<?php

/**
 * Load services definition file.
 */
$settings['container_yamls'][] = __DIR__ . '/services.yml';

/**
 * Include the Pantheon-specific settings file.
 *
 * n.b. The settings.pantheon.php file makes some changes
 *      that affect all environments that this site
 *      exists in.  Always include this file, even in
 *      a local development environment, to ensure that
 *      the site settings remain consistent.
 */
include __DIR__ . "/settings.pantheon.php";

/**
 * If there is a local settings file, then include it
 * Normally, don't git commit this settings.local.php file
 */
$local_settings = __DIR__ . "/settings.local.php";
if (file_exists($local_settings)) {
  include $local_settings;
}

/**
 * If you want  to use lando, include the lando settings file here, 
 *  otherwise comment this out, and/or delete the settings.lando.php file.
 * On a team, this choice might be per-user.
 * Unlike a simple settings.local.php, this basically turns itself off
 * when not on a lando website — so it can be part of git, can be
 * pushed to Pantheon
 */
$lando_settings = __DIR__ . "/settings.lando.php";
if (file_exists($lando_settings)) {
  include $lando_settings;
}

/**
 * If you want to use platform instead of Pantheon, we've left the out-of-date
 * and untested settings.platformsh.php file that SpaceBase originally used.
 */

/**
 * Always install the 'standard' profile to stop the installer from
 * modifying settings.php.
 */
$settings['install_profile'] = 'standard';


/**
 * Place the config directory outside of the Drupal root.
 * This is part of our git commit, so we need to override 
 * Pantheon, lando, etc if they suggest putting it elsewhere
 */
$config_directories[CONFIG_SYNC_DIRECTORY] = '../config/sync';



