<?php
class ApiView extends View {
    protected $apiFormat;

    public function __construct($controller) {
        parent::__construct($controller);

        if ($this->request->is('json')) {
            $this->apiFormat = 'json';
        } else {
            throw new Exception('Unknown API request format');
        }
    }

    protected function _getViewFileName($name = null) {
        // try to find it with default
        try {
            return parent::_getViewFileName($name);
        } catch (MissingViewException $e) { }

        // Try default api views
        try {
            $this->viewPath = str_replace($this->apiFormat, 'api', $this->viewPath);
            return parent::_getViewFileName($name);
        } catch (MissingViewException $e) { }

        // Try default api action view
        $old_plugin = $this->plugin;
        $this->plugin = 'Api';
        try {
            $file = parent::_getViewFileName('/' . $this->apiFormat . '/' . $this->view);
        } catch (MissingViewException $e) { }

        if (!empty($file)) {
            // Reset plugin
            $this->plugin = $old_plugin;
            unset($old_plugin);

            return $file;
        }

        // Finally try default api view
        $file = parent::_getViewFileName('/' . $this->apiFormat . '/default');
        $this->plugin = $old_plugin;
        unset($old_plugin);
        return $file;
    }
}