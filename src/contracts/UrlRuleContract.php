<?php
/**
 * Created by PhpStorm.
 * User: nofuture17
 * Date: 07.04.17
 * Time: 11:21
 */

namespace nofuture17\parsers\contracts;


use Spatie\Crawler\Url;

interface UrlRuleContract
{
    public function check(Url $url): bool;
}