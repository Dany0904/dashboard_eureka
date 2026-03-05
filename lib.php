<?php
defined('MOODLE_INTERNAL') || die();

function local_dashboard_extend_settings_navigation(settings_navigation $settingsnav, context $context) {
    global $PAGE;

    // Solo mostrar si tiene permiso
    if (!has_capability('local/dashboard:access', context_system::instance())) {
        return;
    }

    // Buscar el nodo "Administración del sitio"
    $rootnode = $settingsnav->find('root', navigation_node::TYPE_SITE_ADMIN);

    if (!$rootnode) {
        return;
    }

    // Recorrer sus hijos para encontrar el nodo de "Reportes"
    foreach ($rootnode->children as $child) {
        if ($child->key === 'reports') {
            $url = new moodle_url('/local/dashboard/index.php');
            $child->add(
                get_string('pluginname', 'local_dashboard'),
                $url,
                navigation_node::TYPE_SETTING,
                null,
                'local_dashboard'
            );
            break;
        }
    }
}
