<?php

/**
 * LMSPGEPowerOutagesPlugin
 * 
 * @author Łukasz Kopiszka <lukasz@alfa-system.pl>
 */
class LMSPGEPowerOutagesPlugin extends LMSPlugin
{
    const PLUGIN_NAME = 'LMS PGE Power Outages plugin';
    const PLUGIN_DESCRIPTION = 'Shows power outages in areas served by PGE.';
    const PLUGIN_AUTHOR = 'Łukasz Kopiszka &lt;lukasz@alfa-system.pl&gt;';
    const PLUGIN_DIRECTORY_NAME = 'LMSPGEPowerOutagesPlugin';

    public function registerHandlers()
    {
        $this->handlers = array(
            'smarty_initialized' => array(
                'class' => 'PGEPowerOutagesHandler',
                'method' => 'smartyPGEPowerOutages'
            ),
            'modules_dir_initialized' => array(
                'class' => 'PGEPowerOutagesHandler',
                'method' => 'modulesDirPGEPowerOutages'
            ),
            'welcome_before_module_display' => array(
                'class' => 'PGEPowerOutagesHandler',
                'method' => 'welcomePGEPowerOutages'
            ),
            'access_table_initialized' => array(
                'class' => 'PGEPowerOutagesHandler',
                'method' => 'accessTableInit'
            ),
        );
    }
}
