<?php

class PGEPowerOutagesHandler
{
    public function smartyPGEPowerOutages(Smarty $hook_data)
    {
        $template_dirs = $hook_data->getTemplateDir();
        $plugin_templates = PLUGINS_DIR . DIRECTORY_SEPARATOR . LMSPGEPowerOutagesPlugin::PLUGIN_DIRECTORY_NAME . DIRECTORY_SEPARATOR . 'templates';
        array_unshift($template_dirs, $plugin_templates);
        $hook_data->setTemplateDir($template_dirs);
        return $hook_data;
    }

    public function modulesDirPGEPowerOutages(array $hook_data = array())
    {
        $plugin_modules = PLUGINS_DIR . DIRECTORY_SEPARATOR . LMSPGEPowerOutagesPlugin::PLUGIN_DIRECTORY_NAME . DIRECTORY_SEPARATOR . 'modules';
        array_unshift($hook_data, $plugin_modules);
        return $hook_data;
    }
    public function welcomePGEPowerOutages(array $hook_data = [])
    {
        $SMARTY = LMSSmarty::getInstance();

        $filename = ConfigHelper::getConfig('pge.filename', 'plugins/LMSPGEPowerOutagesPlugin/pge.json');
        $url = ConfigHelper::getConfig('pge.outages_url', 'https://pgedystrybucja.pl/planowanewylaczenia/wylaczenia/');
        $time_in_cache = ConfigHelper::getConfig('pge.time_in_cache', 60);
        $area = ConfigHelper::getConfig('pge.area', 'Legionowo');

        $last_updated_cache = file_exists($filename) ? filemtime($filename) : 0;

        if ((time() - $last_updated_cache) > $time_in_cache) {
            $html = $this->fetchHtmlContent($url . $area);

            if ($html !== false) {
                $outages = $this->extractOutagesFromHtml($html);
                $this->updateCache($filename, $outages);
            } else {
                echo "Failed to fetch HTML content\n";
            }
        }

        $outages = json_decode(file_get_contents($filename), true);
        $outageItems = $outages ?? [];
        $outagesCount = is_array($outageItems) ? count($outageItems) : 0;

        $SMARTY->assign(
            'pge_power_outages',
            [
                'area' => $area,
                'outages' => $outageItems,
                'outages_count' => $outagesCount,
                'last_updated_cache' => date("Y-m-d H:i:s", filemtime($filename))
            ]
        );

        return $hook_data;
    }

    private function fetchHtmlContent($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');

        $html = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
            return false;
        }

        curl_close($ch);
        return $html;
    }

    private function extractOutagesFromHtml($html)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        @$dom->loadHTML('<?xml encoding="UTF-8">' . $html);

        $xpath = new DOMXPath($dom);
        $tables = $xpath->query('//table[contains(@class, "switch_off_list")]');

        $outages = [];

        if ($tables->length > 0) {
            $table = $tables->item(0);

            foreach ($table->getElementsByTagName('tr') as $rowIndex => $row) {
                if ($rowIndex === 0) {
                    continue;
                }

                $rowData = [];

                foreach ($row->getElementsByTagName('td') as $cellIndex => $cell) {
                    $rowData[] = trim($cell->textContent);
                }

                $rowData = array_pad($rowData, 5, null);
                $outages[] = $rowData;
            }
        } else {
            echo "Table not found\n";
        }

        return $outages;
    }

    private function updateCache($filename, $outages)
    {

        $jsonContent = json_encode($outages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo 'JSON encoding error: ' . json_last_error_msg() . PHP_EOL;
        } else {
            if (file_put_contents($filename, $jsonContent) === false) {
                echo 'Error writing to file: ' . error_get_last()['message'] . PHP_EOL;
            }
        }

    }

    public function accessTableInit()
    {
        $access = AccessRights::getInstance();
        $access->insertPermission(
            new Permission(
                'pgepoweroutages_full_access',
                trans('PGE power outages'),
                '^pgepoweroutages$'
            ),
            AccessRights::FIRST_FORBIDDEN_PERMISSION
        );
    }
}