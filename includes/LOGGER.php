<?php

namespace Toolkit;

class LOGGER
{

    public function log($data)
    {
        if (is_array($data) || is_object($data)) {
            error_log(print_r($data, true));
        } else {
            error_log($data);
        }

    }

}