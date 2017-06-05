<?php
/**
 * Created by PhpStorm.
 * User: nofuture17
 * Date: 07.04.17
 * Time: 11:38
 */

namespace nofuture17\parsers\tasks;


use nofuture17\parsers\helpers\HelperDomParser;
use Spatie\Crawler\Url;

class TaskContentParser extends TaskBase
{
    /**
     * @var array
     */
    protected $findData;

    /**
     * @var HelperDomParser
     */
    protected $helperDomParser;

    protected function init()
    {
        parent::init();
        if (empty($this->helperDomParser)) {
            $this->helperDomParser = new HelperDomParser(['findData' => $this->findData]);
        }
    }

    /**
     * @inheritdoc
     */
    public function run(Url $url, $response, Url $foundOnUrl = null, $data = null)
    {
        $content = HelperDomParser::getHtmlContentFromResponse($response);

        $data = $this->helperDomParser->run($content);

        if (!empty($data)) {
            $this->addToResult($url->path(), $data);
        }
    }
}