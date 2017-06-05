<?php
/**
 * Created by PhpStorm.
 * User: nofuture17
 * Date: 07.04.17
 * Time: 10:21
 */

namespace nofuture17\parsers\tasks;


use nofuture17\parsers\contracts\TaskContract;
use nofuture17\parsers\traits\ConfigureTrait;
use Spatie\Crawler\Url;

class TaskBase implements TaskContract
{
    use ConfigureTrait;

    /**
     * @var array
     */
    protected $result = [];

    public function __construct($config)
    {
        $this->configure($config);
        $this->init();
    }

    /**
     * @inheritdoc
     */
    public function run(Url $url, $response, Url $foundOnUrl = null, $data = null)
    {
        echo $url . PHP_EOL;
    }

    protected function init()
    {

    }

    /**
     * @inheritdoc
     */
    public function hasResult()
    {
        return !empty($this->result);
    }

    /**
     * @inheritdoc
     */
    public function getResult()
    {
        return $this->result;
    }

    protected function addToResult($urlPath, $data)
    {
        if (empty($urlPath)) {
            $urlPath = '/';
        }
        $this->result[$urlPath] = $data;
    }

    /**
     * @inheritdoc
     */
    public function clearResult()
    {
        $this->result = [];
    }
}