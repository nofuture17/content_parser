<?php

/**
 * Created by PhpStorm.
 * User: nofuture17
 * Date: 07.04.17
 * Time: 11:13
 */

namespace nofuture17\parsers\profiles;

use nofuture17\parsers\traits\ConfigureTrait;
use Spatie\Crawler\Url;
use Spatie\Crawler\CrawlProfile;

class CrawlerProfileBase implements CrawlProfile
{
    use ConfigureTrait;

    protected $urlRules = [];

    public function __construct($config)
    {
        $this->configure($config);

        if (!empty($config['urlRules'])) {
            $this->initUrlRules($config['urlRules']);
        }
    }

    public function shouldCrawl(Url $url): bool
    {

        $result = true;

        if (!empty($this->urlRules)) {
            foreach ($this->urlRules as $urlRule) {
                if (!$urlRule->check($url)) {
                    $result = false;
                    break;
                }
            }
        }

        return boolval($result);
    }

    protected function initUrlRule($config)
    {
        $rule = new $config['class']($config['options']);
        return $rule;
    }

    protected function initUrlRules($config)
    {
        foreach ($config as $ruleName => $ruleConfig) {
            $this->urlRules[$ruleName] = $this->initUrlRule($ruleConfig);
        }
    }
}