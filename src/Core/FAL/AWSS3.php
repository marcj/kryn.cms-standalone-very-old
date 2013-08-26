<?php

namespace Core\FAL;

class AWSS3 extends AbstractFAL
{
    private $config = array();

    private $aws;

    public function __construct($mountPoint, $params = null)
    {
        require_once 'inc/lib/amazonSdk/sdk.class.php';
        $this->config = $params;

        $credentials = array();
        $credentials[$this->config['bucket']] = array(
            'key' => $this->config['key'],
            'secret' => $this->config['secret_key'],
            'default_cache_config' => 'cache/object/',
            'certificate_authority' => false
        );

        CFCredentials::set($credentials);

        $this->aws = new AmazonS3(array(
            'credentials' => $this->config['bucket']
        ));
    }

    public function fileExists($path)
    {
        return $this->aws->if_object_exists($this->config['bucket'], substr($path, 1)) ||
            $this->aws->if_object_exists($this->config['bucket'], substr($path, 1) . '/');
    }

    public function createFolder($path)
    {
        $response = $this->aws->create_object(
            $this->config['bucket'],
            substr($path, 1) . '/',
            array(
                 'acl' => AmazonS3::ACL_PUBLIC
            )
        );

        return $response->isOk();
    }

    public function createFile($path, $content = null)
    {
        $response = $this->aws->create_object(
            $this->config['bucket'],
            substr($path, 1),
            array(
                 'acl' => AmazonS3::ACL_PUBLIC,
                 'body' => $content,
                 'contentType' => mime_content_type_for_name($path)
            )
        );

        return $response->isOk();
    }

    public function setContent($path, $content)
    {
        $response = $this->aws->create_object(
            $this->config['bucket'],
            substr($path, 1),
            array(
                 'acl' => AmazonS3::ACL_PUBLIC,
                 'body' => $content,
                 'contentType' => mime_content_type_for_name($path)
            )
        );

        return $response->isOk();
    }

    public function deleteFile($path)
    {
        //delete subfiles
        $response2 = $this->aws->delete_all_objects(
            $this->config['bucket'],
            '/^' . preg_quote(substr($path, 1), '/') . '\/.*/'
        );

        //delete file
        $response = $this->aws->delete_all_objects(
            $this->config['bucket'],
            '/^' . preg_quote(substr($path, 1), '/') . '/'
        );

        return $response || $response2;
    }

    public function move($pathSource, $pathTarget)
    {
        //do we have subfiles ?
        $response = $this->aws->get_object_list(
            $this->config['bucket'],
            array(
                 'pcre' => '/^' . preg_quote(substr($pathSource, 1), '/') . '\/.*/'
            )
        );

        if (is_array($response) && count($response) > 0) {
            foreach ($response as $file) {

                $newFile = substr($pathTarget . '/' . substr($file, strlen($pathSource)), 1);
                $this->aws->copy_object(
                    array(
                         'bucket' => $this->config['bucket'],
                         'filename' => $file
                    ),
                    array(
                         'bucket' => $this->config['bucket'],
                         'filename' => $newFile
                    )
                );
            }

            $files2Delete = array();
            foreach ($response as $file) {
                $files2Delete[] = array('key' => $file);
            }

            $this->aws->delete_objects($this->config['bucket'], array('objects' => $files2Delete));
        } else {

            $this->aws->copy_object(
                array(
                     'bucket' => $this->config['bucket'],
                     'filename' => substr($pathSource, 1)
                ),
                array(
                     'bucket' => $this->config['bucket'],
                     'filename' => substr($pathTarget, 1)
                )
            );
            $this->aws->delete_object($this->config['bucket'], substr($pathSource, 1));
        }

        return true;
    }

    public function copy($pathSource, $pathTarget)
    {
        //do we have subfiles ?
        $response = $this->aws->get_object_list(
            $this->config['bucket'],
            array(
                 'pcre' => '/^' . preg_quote(substr($pathSource, 1), '/') . '\/.*/'
            )
        );

        if (is_array($response) && count($response) > 0) {
            foreach ($response as $file) {

                $newFile = substr($pathTarget . '/' . substr($file, strlen($pathSource)), 1);
                $this->aws->copy_object(
                    array(
                         'bucket' => $this->config['bucket'],
                         'filename' => $file
                    ),
                    array(
                         'bucket' => $this->config['bucket'],
                         'filename' => $newFile
                    )
                );
            }
        } else {

            $this->aws->copy_object(
                array(
                     'bucket' => $this->config['bucket'],
                     'filename' => substr($pathSource, 1)
                ),
                array(
                     'bucket' => $this->config['bucket'],
                     'filename' => substr($pathTarget, 1)
                )
            );
        }

        return true;
    }

    public function getPublicUrl($path)
    {
        //todo, handle amazon's cloudfront
        //todo, handle https (need an option)

        //http://<bucket>.s3.amazonaws.com/<path>
        return 'http://' . $this->config['bucket'] . '.s3.amazonaws.com/' . substr($path, 1);

        //this takes a bit long.
        return $this->aws->get_object_url($this->config['bucket'], substr($path, 1));
    }

    public function getContent($path)
    {
        $response = $this->aws->get_object($this->config['bucket'], substr($path, 1));

        return $response->body;
    }

    public function getFile($path)
    {
        $response = $this->aws->get_object_metadata($this->config['bucket'], substr($path, 1));

        if ($response) {
            return array(
                'name' => basename($response['Key']),
                'type' => 'file',
                'path' => '/' . $response['Key'],
                'size' => $response['Size'],
                'mtime' => strtotime($response['LastModified'])
            );
        }

        $response = $this->aws->get_object_metadata($this->config['bucket'], substr($path, 1) . '/');
        if ($response) {
            return array(
                'name' => basename($response['Key']),
                'type' => 'dir',
                'path' => '/' . substr($response['Key'], 0, -1),
                'size' => $response['Size'],
                'mtime' => strtotime($response['LastModified'])
            );
        }

        return false;
    }

    public function getFiles($path)
    {
        $items = array();
        $opts = array(
            'delimiter' => '/'
        );

        if ($path != '/') {
            $opts['prefix'] = substr($path, 1) . '/';
        }

        $response = $this->aws->list_objects($this->config['bucket'], $opts);

        foreach ($response->body->Contents as $file) {
            $name = (string)$file->Key;

            if ($name == $opts['prefix']) {
                continue;
            }

            if ($opts['prefix']) {
                $name = substr((string)$file->Key, strlen($opts['prefix']));
            }

            $items[] = array(
                'name' => $name,
                'type' => 'file',
                'path' => $path . ($path == '/' ? '' : '/') . $name,
                'size' => (string)$file->Size,
                'mtime' => strtotime((string)$file->LastModified)
            );
        }

        if ($response->body->CommonPrefixes) {
            //we maybe got subfolder
            foreach ($response->body->CommonPrefixes as $file) {
                $name = substr((string)$file->Prefix, strlen($opts['prefix']), -1);

                $items[] = array(
                    'name' => $name,
                    'type' => 'dir',
                    'path' => $path . ($path == '/' ? '' : '/') . $name,
                    'size' => 0,
                    'mtime' => 0
                );
            }
        }

        return $items;
    }

    public function getPublicAccess($path)
    {
        $response = $this->aws->get_object_metadata($this->config['bucket'], substr($path, 1));
        if (!is_array($response['ACL'])) {
            return -1;
        }

        foreach ($response['ACL'] as $item) {
            if ($item['id'] == AMAZONS3::USERS_ALL) {
                if ($item['permission'] == 'READ') {
                    return true;
                }
            }
        }

        return false;
    }

    public function setPublicAccess($path, $access = false)
    {
        $response = $this->aws->set_object_acl(
            $this->config['bucket'],
            substr($path, 1),
            $access ? AmazonS3::ACL_PUBLIC : AmazonS3::ACL_PRIVATE
        );

        return $response->isOK();
    }

    /**
     * Returns the file count inside $folderPath
     *
     * @static
     *
     * @param  string $folderPath
     *
     * @return mixed
     */
    public function getCount($folderPath)
    {
        // TODO: Implement getCount() method.
    }

    /**
     * Disk usage
     *
     * @param  string $path
     *
     * @return array|bool [size(bytes), fileCount, folderCount]
     */
    public function getSize($path)
    {
        // TODO: Implement getSize() method.
    }

    /**
     * Searchs files in a path by a regex pattern.
     *
     * @param  string $path
     * @param  string $pattern      Preg regex
     * @param  integer $depth        Maximum depth. -1 for unlimited.
     * @param  integer $currentDepth Internal
     *
     * @return array   Files array
     */
    public function search($path, $pattern, $depth = -1, $currentDepth = 1)
    {
        // TODO: Implement search() method.
    }

    /**
     * Removes a file or folder (recursive).
     *
     * @param  string $path
     *
     * @return bool|int
     */
    public function remove($path)
    {
        // TODO: Implement remove() method.
    }


}
