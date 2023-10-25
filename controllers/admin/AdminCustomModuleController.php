<?php

class AdminCustomModuleController extends ModuleAdminController
{
    public function initContent()
    {
        parent::initContent();

        // Uložená cachovaná data
        $cachedData = \Cache::getInstance()->retrieve('custommodule_data');

        // Pokud cachovaná data existují a nejsou starší než 24 hodin
        if ($cachedData && ($cachedData['timestamp'] + 86400) > time()) {
            $data = $cachedData['data'];
        } else {
            // URL XML-feedu
            $url = 'Enter here your feed address';

            // Nastavení timeoutu na 1 sekundu
            ini_set('default_socket_timeout', 1);

            // Získání obsahu XML
            $xmlStr = file_get_contents($url);

            $data = array();
            if ($xmlStr === false) {
                $this->context->smarty->assign('error', 'Nedaří se načíst feed');
            } else {
                // Nastroje CDATA ve XML
                $dom = new DOMDocument;
                $dom->loadXML($xmlStr, LIBXML_NOCDATA);

                foreach ($dom->getElementsByTagName('item') as $item) {
                    $description = $item->getElementsByTagName('description')->item(0)->textContent;

                    // Regulární výraz pro nalezení odkazu v popisu(Treba menit podle kontextu)
                    preg_match('/<a\s[^>]*href=([\"\']??)([^\" >]*?)\\1[^>]*>(.*)<\/a>/siU', $description, $matches);
                    if (isset($matches[2])) {
                        $link = $matches[2];
                        $description = $matches[3];
                        $data[] = array(
                            'link' => $link,
                            'description' => $description
                        );
                    }
                }

                // Cache data na 24 hodin
                \Cache::getInstance()->store('custommodule_data', array('timestamp' => time(), 'data' => $data), 86400);
            }
        }

        $this->context->smarty->assign('data', $data);
        $this->setTemplate('admin.tpl');
    }
}
