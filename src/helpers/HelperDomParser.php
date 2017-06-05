<?php
/**
 * Created by PhpStorm.
 * User: nofuture17
 * Date: 08.04.17
 * Time: 9:32
 */

namespace nofuture17\parsers\helpers;


use GuzzleHttp\Psr7\Response;
use nofuture17\parsers\traits\ConfigureTrait;
use nofuture17\parsers\components\PhpSimple\HtmlDomParser;
use simplehtmldom_1_5\simple_html_dom;

class HelperDomParser
{
    use ConfigureTrait;

    /**
     * @var \simplehtmldom_1_5\simple_html_dom
     */
    protected $domParser;

    /**
     * @var array
     */
    protected $findData;

    protected $encoding = 'UTF-8';

    /**
     * Предполагаемая колировка html
     * @var string
     */
    protected $contentEncoding;

    public function __construct($config)
    {
        $this->configure($config);
    }

    /**
     * @param array $findData
     */
    public function setFindData(array $findData)
    {
        $this->findData = $findData;
    }

    /**
     * @param $content string
     * @return array|null
     */
    public function run(string $content)
    {
        $content = $this->prepareHtmlContent($content);
        if (!empty($content) && !empty($this->findData)) {
            $this->getDomParser()->load($content);
            $data = $this->fillData();

            if (!empty($data)) {
                return $data;
            }
        }

        return null;
    }

    protected function prepareHtmlContent($content)
    {
        if (!empty($this->encoding) && !empty($this->contentEncoding)) {
            $content = $content = mb_convert_encoding(
                $content,
                $this->encoding,
                $this->contentEncoding
            );
        }

        return $content;
    }

    /**
     * @return \simplehtmldom_1_5\simple_html_dom
     */
    protected function getDomParser()
    {
        if ($this->domParser === null) {
            $this->domParser = HtmlDomParser::create();
        }
        return $this->domParser;
    }

    /**
     * @param $selector
     * @return bool|mixed|string
     */
    protected function getAttributeValue($selector)
    {
        $result = '';
        $element = $this->getDomParser()->find($selector, 0);
        if ($element) {
            $result = $element->innertext;
        }
        return $result;
    }

    /**
     * @param $response Response
     * @return string
     */
    public static function getHtmlContentFromResponse($response)
    {
        $content = '';
        if ($response) {
            try {
                $content = $response->getBody()->getContents();
            } catch (\Exception $exception) {
                $content = '';
            }
        }

        return $content;
    }

    /**
     * @return array
     */
    protected function fillData()
    {
        $data = [];

        foreach ($this->findData as $attribute => $selector) {
            $isRequired = false;
            if ($attribute[0] == '!') {
                $isRequired = true;
                $attribute = substr($attribute, 1);
            }
            $value = $this->getAttributeValue($selector);
            if (empty($value) && $isRequired) {
                break;
            }

            $data[$attribute] = $value;
        }

        return $data;
    }
}