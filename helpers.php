<?php

if (!function_exists('filter_description')) {

    /**
     * @param array $data
     * @return string A highly opinioneted filter dexcription for dates
     */
    function filter_description(array $data = null) {
        $text = "Showing records ";
        if (empty($data['from']) && empty($data['to'])) {
            $text = "Showing all records available";
        }
        if (!empty($data['from']))
            $text.=" from " . $data['from'];
        if (!empty($data['to']))
            $text.=" up to " . $data['to'];
        if (!empty($data['mode']))
            $text.=". Payment mode " . ucfirst($data['mode']);
        if (!empty($data['department']))
            $text.=". Department: " . ucfirst($data['department']);
        return $text;
    }

}
