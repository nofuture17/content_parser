<?php
/**
 * Created by PhpStorm.
 * User: nofuture17
 * Date: 07.04.17
 * Time: 8:43
 */

namespace nofuture17\parsers\observers;

use nofuture17\parsers\contracts\TaskContract;
use nofuture17\parsers\traits\ConfigureTrait;
use Spatie\Crawler\Url;
use Spatie\Crawler\CrawlObserver;

class CrawlObserverBase implements CrawlObserver
{
    use ConfigureTrait;

    /**
     * @var TaskContract[]|array
     */
    protected $tasks;

    /**
     * @param $config
     * @return TaskContract
     */
    protected function initTask($config)
    {
        if (empty($config['options'])) {
            $config['options'] = [];
        }
        $task = new $config['class']($config['options']);
        return $task;
    }

    protected function initTasks($config)
    {
        foreach ($config as $taskName => $taskConfig) {
            $this->tasks[$taskName] = $this->initTask($taskConfig);
        }
    }

    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * @param $taskName string|null
     * @return array
     */
    public function getTasksResult($taskName = null)
    {
        $result = [];

        if ($taskName && !empty($this->tasks[$taskName])) {
            $result = $this->tasks[$taskName]->getResult();
        } else {
            foreach ($this->tasks as $name => $task) {
                $result[$name] = $task->getResult();
            }
        }

        return $result;
    }

    /**
     * CrawlObserverBase constructor.
     * Параметры в конфиге
     * tasksData - словарь
     *  (имя задачи => массив данных для настройки !class Имя класса задачи)
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->configure($config);
        if (!empty($config['tasksData'])) {
            $this->initTasks($config['tasksData']);
        }
    }

    public function willCrawl(Url $url)
    {

    }

    public function hasBeenCrawled(Url $url, $response, Url $foundOn = null)
    {
        if (!empty($this->tasks)) {
            foreach ($this->tasks as $task) {
                $task->run($url, $response, $foundOn);
            }
        }
    }

    public function finishedCrawling()
    {
        echo 'FINISH!' . PHP_EOL;
    }
}