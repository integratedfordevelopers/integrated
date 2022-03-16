<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\DataFixtures\Faker\Provider;

use Integrated\Bundle\StorageBundle\DataFixtures\Faker\Util\CreateUtil;
use Integrated\Bundle\StorageBundle\Storage\Manager;

class VideoProvider
{
    /**
     * @var array
     */
    protected static $mimeTypes = [
        'application/ogg' => 'ogg',
        'video/3gpp' => '3gp',
        'video/3gpp2' => '3g2',
        'video/h261' => 'h261',
        'video/h263' => 'h263',
        'video/h264' => 'h264',
        'video/jpeg' => 'jpgv',
        'video/jpm' => ['jpm', 'jpgm'],
        'video/mj2' => 'mj2',
        'video/mp4' => 'mp4',
        'video/mpeg' => ['mpeg', 'mpg', 'mpe', 'm1v', 'm2v'],
        'video/ogg' => 'ogv',
        'video/quicktime' => ['mov', 'qt'],
        'video/vnd.dece.hd' => ['uvh', 'uvvh'],
        'video/vnd.dece.mobile' => ['uvm', 'uvvm'],
        'video/vnd.dece.pd' => ['uvp', 'uvvp'],
        'video/vnd.dece.sd' => ['uvs', 'uvvs'],
        'video/vnd.dece.video' => ['uvv', 'uvvv'],
        'video/vnd.dvb.file' => 'dvb',
        'video/vnd.fvt' => 'fvt',
        'video/vnd.mpegurl' => ['mxu', 'm4u'],
        'video/vnd.ms-playready.media.pyv' => 'pyv',
        'video/vnd.uvvu.mp4' => ['uvu', 'uvvu'],
        'video/vnd.vivo' => 'viv',
        'video/webm' => 'webm',
        'video/x-f4v' => 'f4v',
        'video/x-fli' => 'fli',
        'video/x-flv' => 'flv',
        'video/x-m4v' => 'm4v',
        'video/x-matroska' => ['mkv', 'mk3d', 'mks'],
        'video/x-mng' => 'mng',
        'video/x-ms-asf' => ['asf', 'asx'],
        'video/x-ms-vob' => 'vob',
        'video/x-ms-wm' => 'wm',
        'video/x-ms-wmv' => 'wmv',
        'video/x-ms-wmx' => 'wmx',
        'video/x-ms-wvx' => 'wvx',
        'video/x-msvideo' => 'avi',
        'video/x-sgi-movie' => 'movie',
    ];

    /**
     * @var Manager
     */
    private $sm;

    /**
     * @param Manager $sm
     */
    public function __construct(Manager $sm)
    {
        $this->sm = $sm;
    }

    /**
     * @param string|null $type
     *
     * @return \Integrated\Common\Content\Document\Storage\Embedded\StorageInterface
     *
     * @throws \Exception
     */
    public function createVideo($type = null)
    {
        return CreateUtil::path(
            $this->sm,
            $this->getVideo($type)
        );
    }

    /**
     * @param string|null $type
     *
     * @return bool|string
     *
     * @throws \Exception
     */
    protected function getVideo($type = null)
    {
        $destination = sys_get_temp_dir().\DIRECTORY_SEPARATOR.uniqid('video-');
        $url = sprintf('https://wosvideo.e-activesites.nl/%s/', $type ?: 'random');

        // create new local file
        $fp = fopen($destination, 'w');

        // init curl
        $ch = curl_init($url);

        // external file should write to local filehandle
        curl_setopt($ch, \CURLOPT_FILE, $fp);
        $success = curl_exec($ch);

        // fetch mimetype of external file
        $mimeType = (curl_getinfo($ch, \CURLINFO_CONTENT_TYPE));

        // close it all
        curl_close($ch);
        fclose($fp);

        if (!$success) {
            // could not contact the distant URL or HTTP error - fail silently.
            return false;
        }

        // check extension
        if (!isset(self::$mimeTypes[$mimeType])) {
            throw new \Exception(sprintf('No video extension found for mimetype "%s"', $mimeType));
        }

        $extension = \is_array(self::$mimeTypes[$mimeType]) ? self::$mimeTypes[$mimeType][0] : self::$mimeTypes[$mimeType];

        // new filename should include extension (we cannot guess extension beforehand)
        $newDestination = $destination.'.'.$extension;

        // add extension
        rename($destination, $newDestination);

        return $newDestination;
    }
}
