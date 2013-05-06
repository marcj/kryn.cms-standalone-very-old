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
                'used' => self::getRamFree(),
                'size' => self::getRamSize() / 1024
            ),
            'cpu' => self::getCpuUsage()
        );
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

    public static function getSpace ()
    {

        if ('darwin' == strtolower(PHP_OS)) {
            $sysctl  = `df -kl`;
            $matches = array();
            preg_match_all('/([a-zA-Z0-9\/]+)\s+([0-9]+)\s+([0-9]+)\s+([0-9]+)\s+([0-9%]+)\s+([0-9]+)\s+([0-9]+)\s+([0-9%]+)\s+(.*)/', $sysctl, $matches, PREG_SET_ORDER);

            $result = array();
            foreach ($matches as $match) {
                $avail = $match[4] + 0;
                $user  = $match[3] + 0;
                if (1000 * 1024 > $avail) continue; //anything under 1gb
                $result[] = array(
                    'name' => '/' === $match[9] ? '/' : basename($match[9]),
                    'used' => $user,
                    'available' => $avail,
                    'size' => $user + $avail
                );
            }
            return $result;
        } else if ('linux' === strtolower(PHP_OS)) {
            //todo
        }
    }

    public static function getRamFree()
    {
        $cpuUsage  = str_replace(' ', '', `ps -A -o rss`);
        $processes = explode("\n", $cpuUsage);
        $ramSize  = array_sum($processes);
        return $ramSize;
    }
    public static function getRamSize()
    {
        if ('darwin' == strtolower(PHP_OS)) {
            $sysctl  = `sysctl hw.memsize`;
            $matches = array();
            preg_match('/hw.memsize: ([0-9\.]*)/', $sysctl, $matches);
            return $matches[1] + 0;
        } else if ('linux' === strtolower(PHP_OS)) {
            return (`free`) + 0;
        }

        return 1;
    }

    public static function getCpuCoreCount()
    {
        if ('darwin' === strtolower(PHP_OS)) {
            $sysctl  = `sysctl hw.ncpu`;
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
        $cpuUsage  = str_replace(' ', '', `ps -A -o %cpu`);
        $processes = explode("\n", $cpuUsage);
        $cpuUsage  = array_sum($processes);
        return $cpuUsage / self::getCpuCoreCount();
    }

}