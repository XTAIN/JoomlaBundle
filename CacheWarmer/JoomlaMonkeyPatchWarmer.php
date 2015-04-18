<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\CacheWarmer;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmer;

/**
 * Class JoomlaMonkeyPatchWarmer
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\CacheWarmer
 */
class JoomlaMonkeyPatchWarmer extends CacheWarmer
{
    /**
     * @var array
     */
    protected $overrides = [];

    /**
     * @var string
     */
    protected $baseDir;

    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * @var string
     */
    protected $overrideDir;

    /**
     * @param string $baseDir
     * @param string $cacheDir
     * @param string $overrideDir
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function __construct($baseDir, $cacheDir, $overrideDir)
    {
        $this->baseDir = $baseDir;
        $this->cacheDir = $cacheDir;
        $this->overrideDir = $overrideDir;
    }

    /**
     * @param array $override
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function addOverride(array $override)
    {
        $this->overrides[] = $override;
    }

    /**
     * Checks whether this warmer is optional or not.
     *
     * Optional warmers can be ignored on certain conditions.
     *
     * A warmer should return true if the cache can be
     * generated incrementally and on-demand.
     *
     * @return bool    true if the warmer is optional, false otherwise
     */
    public function isOptional()
    {
        return false;
    }

    public function warmUp($cacheDir)
    {
        $baseDir = $this->baseDir;
        $classMap = <<<EOF
<?php
/**
 * This file is generated. Dont touch.
 */

EOF;

        foreach ($this->overrides as $override) {
            $file = $override['file'];
            $name = $override['class'];
            $overrideClass = $override['override'];
            if (substr($overrideClass, 0, 1) != '\\') {
                $overrideClass = '\\' . $overrideClass;
            }

            $nameSafe = var_export($name, true);
            $overrideSafe = var_export($overrideClass, true);

            if (!empty($file)) {
                $path = dirname($file);
                $static = empty($override['static']) ? false : $override['static'];
                $proxyName = 'JProxy_' . $override['class'];
                $code = $this->loadAs($baseDir . DIRECTORY_SEPARATOR . $file, $name, $proxyName, $static);

                $totalCode = <<<EOF
<?php
/**
 * This class is copied and modified from file $file
 */
$code

//we dont need this anymore because we now use \XTAIN\Bundle\JoomlaBundle\Library::registerAlias
//class $name extends \\$overrideClass {}

EOF;

                $cachePath = $this->overrideDir . DIRECTORY_SEPARATOR . $path;
                $cacheFile = $this->overrideDir . DIRECTORY_SEPARATOR . $file;
                if (!is_dir($cachePath)) {
                    mkdir($cachePath, 0777, true);
                }
                file_put_contents($cacheFile, $totalCode);
                $cacheFileSafe = var_export($cacheFile, true);
                $classMap .= <<<EOF
// adding register class
\XTAIN\Bundle\JoomlaBundle\Library\Loader::register($nameSafe, $cacheFileSafe);

EOF;
            }

            $classMap .= <<<EOF
// adding override class
\XTAIN\Bundle\JoomlaBundle\Library\Loader::registerAlias($nameSafe, $overrideSafe);

EOF;
        }

        file_put_contents($this->overrideDir . DIRECTORY_SEPARATOR . 'classmap.php', $classMap);

        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    /**
     * @param string $file
     * @param string $oldName
     * @param string $newName
     * @param bool   $static
     *
     * @return mixed|string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function loadAs($file, $oldName, $newName, $static = false)
    {
        $code = file_get_contents($file);
        $code = str_replace('<?php', '', $code);
        $code = str_replace('?>', '', $code);
        $code = preg_replace('/(final[\s]+)?class[\s]+' . preg_quote($oldName) . '/i', 'class ' . $newName, $code);

        if ($static !== false) {
            // TODO this is a ugly hack!
            $code = str_replace('static::', $static . '::', $code);
            $code = str_replace('self::', $static . '::', $code);
        }

        return $code;
    }
}
