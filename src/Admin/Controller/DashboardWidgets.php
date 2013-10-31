<?php

namespace Admin\Controller;

class DashboardWidgets
{
    public static function load(&$response)
    {
        $load = function_exists('sys_getloadavg') ? \sys_getloadavg() : '';

        $response['admin/load'] = array(
            'load' => $load,
            'os' => PHP_OS,
            'ram' => array(
                'used' => self::getRamUsed(),
                'size' => self::getRamSize()
            ),
            'cpu' => self::getCpuUsage()
        );
    }

    public static function analytics(&$response)
    {
        //todo
    }

    public static function space(&$response)
    {
        $response['admin/space'] = self::getSpace();
    }

    public static function uptime(&$response)
    {
        $uptime = `uptime`;
        $matches = array();
        preg_match('/up ([^,]*),/', $uptime, $matches);
        $response['admin/uptime'] = $matches[1];
    }

    public static function getSpace()
    {

        $matches = array();
        if ('darwin' == strtolower(PHP_OS)) {
            $sysctl = `df -kl`;
            preg_match_all(
                '/([a-zA-Z0-9\/]+)\s+([0-9]+)\s+([0-9]+)\s+([0-9]+)\s+([0-9%]+)\s+([0-9]+)\s+([0-9]+)\s+([0-9%]+)\s+(.*)/',
                $sysctl,
                $matches,
                PREG_SET_ORDER
            );

            $availIdx = 4;
            $usedIdx = 3;
            $nameIdx = 9;
        } else if ('linux' === strtolower(PHP_OS)) {
            $sysctl = `df -l --block-size=1K`;
            preg_match_all(
                '/([a-zA-Z0-9\/]+)\s+([0-9]+)\s+([0-9]+)\s+([0-9]+)\s+([0-9%]+)\s+(.*)/',
                $sysctl,
                $matches,
                PREG_SET_ORDER
            );

            $availIdx = 4;
            $usedIdx = 3;
            $nameIdx = 6;
        }

        $result = array();
        $blacklist = array('/boot', '/dev', '/run', '/run/lock', '/run/shm');
        foreach ($matches as $match) {

            if (count($result) > 2) {
                break;
            }

            $avail = $match[$availIdx] + 0;
            $user = $match[$usedIdx] + 0;
            $name = $match[$nameIdx];
            if (in_array($name, $blacklist)) {
                continue;
            }

            //anything under 1gb
            if (1000 * 1024 > $avail) {
                continue;
            }

            $result[$name] = array(
                'name' => '/' === $name ? '/' : basename($name),
                'used' => $user,
                'available' => $avail,
                'size' => $user + $avail
            );
        }
        return array_values($result) ? : array();
    }

    /**
     * @return integer kB
     */
    public static function getRamUsed()
    {
        $cpuUsage = str_replace(' ', '', `ps -A -o rss`);
        $processes = explode("\n", $cpuUsage);
        $ramSize = array_sum($processes);
        return $ramSize;
    }

    public static function latency(&$response)
    {
        $lastLatency = \Core\Kryn::getFastCache('core/latency');

        $result = array(
            'frontend' => 0,
            'backend' => 0,
            'database' => 0,
            'session' => 0,
            'cache' => 0
        );
        foreach ($result as $key => &$value) {
            if ($lastLatency[$key]) {
                $value = round((array_sum($lastLatency[$key]) / count($lastLatency[$key])) * 1000);
            }
        }
        $response['admin/latency'] = $result;
    }

    public static function latencies(&$response)
    {
        $lastLatency = \Core\Kryn::getFastCache('core/latency');
        $result = array(
            'frontend' => 0,
            'backend' => 0,
            'database' => 0,
            'session' => 0,
            'cache' => 0
        );
        foreach ($result as $key => &$value) {
            $value = $lastLatency[$key] ? : array();
        }
        $response['admin/latencies'] = $result;
    }

    /**
     * @return int kB
     */
    public static function getRamSize()
    {
        if ('darwin' == strtolower(PHP_OS)) {
            $sysctl = `sysctl hw.memsize`;
            $matches = array();
            preg_match('/hw.memsize: ([0-9\.]*)/', $sysctl, $matches);
            return ($matches[1] + 0) / 1024;
        } else if ('linux' === strtolower(PHP_OS)) {
            $sysctl = `free`;
            $matches = array();
            preg_match('/Mem:\s+([0-9\.]*)/', $sysctl, $matches);
            return ($matches[1] + 0);
        }

        return 1;
    }

    public static function getCpuCoreCount()
    {
        if ('darwin' === strtolower(PHP_OS)) {
            $sysctl = `sysctl hw.ncpu`;
            $matches = array();
            preg_match('/hw.ncpu: ([0-9\.]*)/', $sysctl, $matches);
            return $matches[1] + 0;
        } else if ('linux' === strtolower(PHP_OS)) {
            return (`nproc`) + 0;
        }

        return 1;
    }


    public static function getCpuUsage()
    {
        $cpuUsage = str_replace(' ', '', `ps -A -o %cpu`);
        $processes = explode("\n", $cpuUsage);
        $cpuUsage = array_sum($processes);
        return $cpuUsage / self::getCpuCoreCount();
    }

}