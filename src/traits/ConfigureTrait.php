<?php
/**
 * Created by PhpStorm.
 * User: nofuture17
 * Date: 07.04.17
 * Time: 9:02
 */

namespace nofuture17\parsers\traits;


trait ConfigureTrait
{
    protected function configure($params = [])
    {
        if (!empty($params)) {
            foreach ($params as $param => $value) {
                if (property_exists($this, $param)) {
                    $this->$param = $value;
                }
            }
        }
    }
}