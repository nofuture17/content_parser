<?php
/**
 * Created by PhpStorm.
 * User: nofuture17
 * Date: 07.04.17
 * Time: 15:52
 */

namespace nofuture17\parsers\tasks;


use Spatie\Crawler\Url;

class TaskCallback extends TaskBase
{
    /**
     * @var callable
     */
    protected $callback;

    public function run(Url $url, $response, Url $foundOnUrl = null, $data = null)
    {
        if (is_callable($this->callback)) {
            $result = call_user_func_array(
                $this->callback,
                [
                    'url' => $url,
                    'response' => $response,
                    'foundOnUrl' => $foundOnUrl,
                    'data' => $data,
                ]
            );

            if (!empty($result)) {
                $this->addToResult($url->path(), $result);
            }
        }
    }
}