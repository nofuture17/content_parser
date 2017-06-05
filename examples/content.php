<?php
/**
 * Created by PhpStorm.
 * User: nofuture17
 * Date: 07.04.17
 * Time: 8:39
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../autoload.php';

use \nofuture17\parsers\crawlers\CrawlerBase as Crawler;
use \nofuture17\parsers\observers\CrawlObserverBase as Observer;
use \nofuture17\parsers\parsers\ParserBase as Parser;
use \nofuture17\parsers\profiles\CrawlerProfileBase as Profile;
use GuzzleHttp\RequestOptions;
use nofuture17\parsers\helpers\HelperDomParser;

//$startUrl = 'http://www.reeftime.ru';
$findData = [
    '!name' => 'h1',
//    '!name' => '[itemprop="name"]',
    'price' => '.price',
    'vendor' => '.prod_dop_option',
    'text_full' => '[itemprop="description"]',
];

$domParserHelper = new HelperDomParser([
    'findData' => $findData,
    'contentEncoding' => 'cp1251'
]);
$resultFilePath = __DIR__ . '/../result/content.txt';
$startUrl = 'http://makulatura99.ru';
$config = [
    'startUrl' => $startUrl,
    'crawlerOptions' => [
        'class' => Crawler::class,
        'options' => [
            RequestOptions::COOKIES => true,
            RequestOptions::CONNECT_TIMEOUT => 0,
            RequestOptions::TIMEOUT => 0,
            RequestOptions::ALLOW_REDIRECTS => true,
        ]
    ],
    'observerOptions' => [
        'class' => Observer::class,
        'options' => [
            'tasksData' => [
                'echo' => [
                    'class' => \nofuture17\parsers\tasks\TaskBase::class
                ],
                'callback' => [
                    'class' => \nofuture17\parsers\tasks\TaskCallback::class,
                    'options' => [
                        'callback' => function (
                            $url,
                            $response,
                            $foundOnUrl = null,
                            $data = null
                        ) use ($domParserHelper, $resultFilePath) {
                            $content = HelperDomParser::getHtmlContentFromResponse($response);
                            $data = $domParserHelper->run($content);
                            if (!empty($data)) {
                                $row = implode(' | ', $data) . ' | ' . $url . PHP_EOL;
                                file_put_contents($resultFilePath, $row, FILE_APPEND);
                            }
                        }
                    ]
                ],
//                'content' => [
//                    'class' => \nofuture17\parsers\tasks\TaskContentParser::class,
//                    'options' => [
//                        'findData' => $findData,
//                        'helperDomParser' => $domParserHelper
//                    ]
//                ]
            ]
        ]
    ],
    'profileOptions' => [
        'class' => Profile::class,
        'options' => [
            'urlRules' => [
                'basic' => [
                    'class' => \nofuture17\parsers\urlRules\UrlRuleBase::class,
                    'options' => [
                        'rules' => [
//                            '/' . preg_quote($startUrl, '/') . '\/katalog\/\S+/',
//                            '/' . preg_quote($startUrl, '/') . '\/?$/'
                            '/' . preg_quote($startUrl, '/') . '\/?/'
                        ]
                    ]
                ]
            ]
        ]
    ]
];


$parser = new Parser($config);
$parser->run();
//$result = $parser->getObserver()->getTasksResult('content');
//$fileContent = json_encode($result);
//file_put_contents(__DIR__ . '/../result/content.txt', $fileContent);