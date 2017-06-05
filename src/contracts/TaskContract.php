<?php
/**
 * Created by PhpStorm.
 * User: nofuture17
 * Date: 07.04.17
 * Time: 10:15
 */

namespace nofuture17\parsers\contracts;

use GuzzleHttp\Psr7\Response;
use Spatie\Crawler\Url;

interface TaskContract
{
    /**
     * Запустить задачу
     * @var $url Url
     * @var Response
     * @var $foundOnUrl Url
     * @var $data mixed
     * @return void
     */
    public function run(Url $url, $response, Url $foundOnUrl = null, $data = null);

    /**
     * Есть ли данные результата
     * @return boolean
     */
    public function hasResult();

    /**
     * Получить данные результата
     * @return mixed
     */
    public function getResult();

    /**
     * Удалить данные результата
     * @return void
     */
    public function clearResult();
}