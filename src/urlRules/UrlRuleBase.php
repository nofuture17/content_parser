<?php
/**
 * Created by PhpStorm.
 * User: nofuture17
 * Date: 07.04.17
 * Time: 11:22
 */

namespace nofuture17\parsers\urlRules;


use nofuture17\parsers\contracts\UrlRuleContract;
use nofuture17\parsers\traits\ConfigureTrait;
use Spatie\Crawler\Url;

class UrlRuleBase implements UrlRuleContract
{
    use ConfigureTrait;

    /**
     * Регулярные выражения для черного списка
     * @var array
     */
    protected $blackList;

    /**
     * Регулярные выражения для проверки на соответствие
     * @var array
     */
    protected $rules;

    public function __construct($config)
    {
        $this->configure($config);
    }

    public function check(Url $url): bool
    {
        $result = true;

        $urlString = (string) $url;

        if (!empty($this->rules)) {
            $rulesStatus = false;
            foreach ($this->rules as $rule) {
                if ($this->applyRule($rule, $urlString, $url)) {
                    $rulesStatus = true;
                    break;
                }
            }
            $result = $rulesStatus;
        }

        if ($result && !empty($this->blackList)) {
            foreach ($this->blackList as $rule) {
                if ($this->applyRule($rule, $urlString, $url)) {
                    $result = false;
                    break;
                }
            }
        }

        return boolval($result);
    }

    public function applyRule($rule, $urlString, $url): bool
    {
        if (is_callable($rule)) {
            $result = call_user_func_array($rule, ['url' => $url]);
        } else {
            $result = preg_match($rule, $urlString);
        }
        return boolval($result);
    }
}