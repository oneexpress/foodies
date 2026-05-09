<?php
class ModelDesignTranslation extends Model {
	public function getTranslations($route) {

        // Journal Theme Modification
        // Avoid unneeded database queries
        static $results;

		if ($results === null) {
			if (!$this->config->get('config_language_id')) {
				return [];
			}

			$results = [];

			$language_code = !empty($this->session->data['language']) ? $this->session->data['language'] : $this->config->get('config_language');

			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "translation WHERE store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

			foreach ($query->rows as $row) {
				$results[$route][] = $row;
			}
		}

		return $results[$route] ?? [];
        // End Journal Theme Modification
            
		$language_code = !empty($this->session->data['language']) ? $this->session->data['language'] : $this->config->get('config_language');
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "translation WHERE store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "' AND (route = '" . $this->db->escape($route) . "' OR route = '" . $this->db->escape($language_code) . "')");

		return $query->rows;
	}
}
