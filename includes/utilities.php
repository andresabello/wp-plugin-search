<?php

/**
 * pi_find_asset
 *
 * @param  mixed $path
 * @return string
 */
function pi_find_asset($path): string
{
    $defaut = '/wp-content/plugins/pi-search/assets/dist/' . $path;
    if (!file_exists(PI_PLUGIN_PATH . 'assets/dist/manifest.json')) {
        return $defaut;
    }

    $manifest = file_get_contents(PI_PLUGIN_PATH . 'assets/dist/manifest.json');
    $manifest = json_decode($manifest, true);
    return $manifest[$path] ?? $defaut;
}