<?php
/**
 * Created by PhpStorm.
 * User: nofuture17
 * Date: 07.04.17
 * Time: 8:57
 */

namespace nofuture17\parsers\parsers;

use nofuture17\parsers\crawlers\CrawlerBase;
use nofuture17\parsers\observers\CrawlObserverBase;
use nofuture17\parsers\profiles\CrawlerProfileBase;
use \nofuture17\parsers\traits\ConfigureTrait;
use Spatie\Crawler\Url;

/**
 * Class ParserBase
 * @package nofuture17\parsers\parsers
 * ContentParser constructor.
 * configurable params:
 * startUrl - начальный url
 * crawlerOptions - Настройки паука (!class - имя класса паука)
 *  options: ...
 * observerOptions - Настройки драйвера паука (!class - имя класса)
 *  options: tasksData - [(!class - имя класса), $options - массив с настройками]
 * profileOptions - Настройки профиля паука (!class - имя класса)
 *  options: urlRules - [(!class - имя класса), $options - массив с настройками]
 * @param array $config
 * @throws \ErrorException
 */
class ParserBase
{
    use ConfigureTrait;

    /**
     * Начальный url для опаучивания
     * @var Url
     */
    protected $startUrl;

    /**
     * @var string
     */
    protected $observerClass;

    /**
     * @var array|null
     */
    protected $observerOptions;

    /**
     * @var CrawlObserverBase
     */
    protected $observer;

    /**
     * @var array|null
     */
    protected $profileOptions;

    /**
     * @var CrawlerProfileBase
     */
    protected $profile;

    /**
     * @var array|null
     */
    protected $crawlerOptions;

    /**
     * @var CrawlerBase
     */
    protected $crawler;

    public function __construct(array $config)
    {
        if (empty($config)) {
            throw new \ErrorException('Необходимо задать данные конфигурации');
        }

        $this->configure($config);
        $this->init();
    }

    protected function init()
    {
        if (empty($this->startUrl)) {
            throw new \ErrorException('Необходимо задать начальный url');
        }
        $this->startUrl = new Url($this->startUrl);

        $this->crawler = $this->getCrawler();
    }

    /**
     * @return CrawlerBase|static
     */
    protected function initCrawler(): CrawlerBase
    {
        if (empty($this->crawlerOptions['class'])) {
            throw new \ErrorException('Необходимо указать имя класса паука');
        }

        $crawler = call_user_func_array(
            [$this->crawlerOptions['class'], 'create'],
            [
                'options' => $this->crawlerOptions['options']
            ]
        );

        if ($observer = $this->getObserver()) {
            $crawler->setCrawlObserver($observer);
        }

        if ($profile = $this->getProfile()) {
            $crawler->setCrawlProfile($profile);
        }

        return $crawler;
    }

    protected function runCrawler(Url $url)
    {
        $this->getCrawler()->startCrawling($url);
    }

    /**
     * @return CrawlObserverBase|null
     */
    protected function initObserver()
    {
        if (!empty($this->observerOptions['class'])) {
            $observer = new $this->observerOptions['class'](
                $this->observerOptions['options']
            );
            return $observer;
        }

        return null;
    }

    protected function initProfile()
    {
        if (!empty($this->profileOptions['class'])) {
            $profile = new $this->profileOptions['class'](
                $this->profileOptions['options']
            );
            return $profile;
        }

        return null;
    }

    /**
     * @return mixed|CrawlerProfileBase
     */
    public function getProfile()
    {
        if ($this->profile === null) {
            $this->profile = $this->initProfile();
        }

        return $this->profile;
    }

    /**
     * @return CrawlerBase|static
     */
    public function getCrawler()
    {
        if ($this->crawler === null) {
            $this->crawler = $this->initCrawler();
        }

        return $this->crawler;
    }

    /**
     * @return mixed|CrawlObserverBase
     */
    public function getObserver()
    {
        if ($this->observer === null) {
            $this->observer = $this->initObserver();
        }

        return $this->observer;
    }

    /**
     * @return mixed
     */
    public function getStartUrl()
    {
        return $this->startUrl;
    }

    public function run()
    {
        $this->runCrawler($this->startUrl);
    }
}