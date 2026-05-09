<?php
class ModelDesignTheme extends Model {
	public function getTheme($route, $theme) {

        // Journal Theme Modification
        // Avoid unneeded database queries
        static $results;

        if ($results === null) {
            $results = [];

            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "theme WHERE store_id = '" . (int)$this->config->get('config_store_id') . "'");

            foreach ($query->rows as $row) {
                $results[$row['theme']][$row['route']] = $row;
            }
        }

        return $results[$theme][$route] ?? null;
        // End Journal Theme Modification
            
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "theme WHERE store_id = '" . (int)$this->config->get('config_store_id') . "' AND theme = '" . $this->db->escape($theme) . "' AND route = '" . $this->db->escape($route) . "'");

		return $query->row;
	}
}