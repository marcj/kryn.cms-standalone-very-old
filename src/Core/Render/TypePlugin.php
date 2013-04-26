<?php

namespace Core\Render;

use Core\Models\Content;
use Core\Kryn;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class TypePlugin implements TypeInterface
{

    public function render(Content $content, $parameter)
    {
        if ($response = Kryn::getResponse()->getPluginResponse($content)) {
            return $response->getContent();
        } elseif ($content->getContent()) {
            $plugin = json_decode($content->getContent(), 1);

            $bundleName = $plugin['bundle'] ? : $plugin['module'];

            $config = Kryn::getConfig($bundleName);

            if (!$config) {
                return tf(
                    'Bundle `%s` does not exist. You probably have to install this bundle.',
                    $bundleName
                );
            }

            if ($pluginDef = $config->getPlugin($plugin['plugin'])) {
                $clazz  = $pluginDef->getClass();
                $method = $pluginDef->getMethod();

                if (class_exists($clazz)) {
                    if (method_exists($clazz, $method)) {
                        //create a sub request
                        $request = new Request();
                        $request->attributes->add(
                            array(
                                 '_controller' => $clazz . '::' . $method,
                                 'options' => $plugin['options']
                            )
                        );

                        ob_start();
                        $response = Kryn::getHttpKernel()->handle($request, HttpKernelInterface::SUB_REQUEST);
                        $ob       = ob_get_clean();

                        if ($response instanceof Response) {
                            Kryn::sendResponse($response);
                        } else {
                            return $ob . $response->getContent();
                        }
                    } else {
                        return '';
                    }
                } else {
                    return tf('Class `%s` does not exist. You should create this class.', $clazz);
                }
            } else {
                return tf(
                    'Plugin `%s` in bundle `%s` does not exist. You probably have to install the bundle first.',
                    $plugin['plugin'],
                    $bundleName
                );
            }
        }
    }

}