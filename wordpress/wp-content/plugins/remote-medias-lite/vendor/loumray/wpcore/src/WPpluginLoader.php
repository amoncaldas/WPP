<?php
/*
 * This file is part of WPCore project.
 *
 * (c) Louis-Michel Raynauld <louismichel@pweb.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WPRemoteMediaExt\WPCore;

use Composer\Autoload\ClassLoader;

/**
 * WP plugin loader
 *
 * @author Louis-Michel Raynauld <louismichel@pweb.ca>
 */

class WPpluginLoader extends ClassLoader
{

    // PSR-4
    private $prefixLengthsPsr4 = array();
    private $prefixDirsPsr4 = array();
    private $fallbackDirsPsr4 = array();

    // PSR-0
    private $prefixesPsr0 = array();
    private $fallbackDirsPsr0 = array();

    private $useIncludePath = false;
    private $classMap = array();

    protected static function getDeps($installedPackages, $vendorDir)
    {
        if (!class_exists(__NAMESPACE__.'\\Composer\\InstalledFile')) {
            require __DIR__.'/Composer/InstalledFile.php';
        }
        $installedFile = new Composer\InstalledFile($vendorDir.'/composer/installed.json');

        $deppackages = array();
        $newRootNamespaces = array();
        foreach ($installedPackages as $installedPackage => $newRootNamespace) {
            $deppackages = array_merge($deppackages, $installedFile->getNamespaces($installedPackage));
                
            foreach ($deppackages as $deppackage => $src) {
                if (isset($newRootNamespaces[$deppackage])) {
                    continue;
                }
                $newRootNamespaces[$deppackage] = $newRootNamespace;
            }
        }
        $supplierstochange = array();
        foreach ($deppackages as $deppackage => $autoloads) {
            $newRootNamespace = $newRootNamespaces[$deppackage];
            // print($deppackage.' - '.$newRootNamespace.PHP_EOL);
            // continue;
            foreach ($autoloads as $namespace => $relDir) {
                //Path of files to wrap
                // $supplier = stristr($namespace, '\\', true);

                $supplier = trim($namespace, '\\');
                if ($supplier == $newRootNamespace) {
                    continue;
                }
                $new = $newRootNamespace.'\\'.$supplier;

                $supplierstochange[$supplier] = $new;
            }
        }
        // print_r($supplierstochange);
        //Prevent duplicate wrapping of same roots
        foreach ($supplierstochange as $supplier => $new) {
            $rootsupplier = stristr($supplier, '\\', true);
            if (isset($supplierstochange[$rootsupplier])) {
                unset($supplierstochange[$supplier]);
            }
        }

        return array(
            'deps' => $deppackages,
            'wrap' => $supplierstochange,
        );
    }

    public static function wrapPackage($installedPackages, $vendorDir = '')
    {
        if (empty($vendorDir)) {
            $vendorDir = __DIR__.'/../../../';
        }

        echo "----- wrapping packages -------". PHP_EOL;
        //Make sure Composer Classes are loaded
        
        // $requires = $installedFile->getRequires($installedPackage);
        // print_r($requires);
        $supplierstochange = self::getDeps($installedPackages, $vendorDir);
        $deppackages       = $supplierstochange['deps'];
        $supplierstochange = $supplierstochange['wrap'];

        //For all dependant packages replace
        foreach ($deppackages as $deppackage => $autoloads) {
            foreach ($autoloads as $namespace => $relDir) {
                //Path of files to wrap
                $wrappingPath = realpath($vendorDir.DIRECTORY_SEPARATOR.$deppackage.DIRECTORY_SEPARATOR.$relDir);

                echo "----- wrapping package $deppackage ------- ".$deppackage.DIRECTORY_SEPARATOR.$relDir.PHP_EOL;
                
                // print($supplier.' to '.$new.PHP_EOL);
                // print($wrappingPath.PHP_EOL);
                if (empty($wrappingPath)) {
                    print "Empty Directory: $wrappingPath - skipping".PHP_EOL;
                    continue;
                }
                $fileList = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator(
                        $wrappingPath
                    ),
                    \RecursiveIteratorIterator::SELF_FIRST
                );
                foreach ($fileList as $item) {
                    if ($item->isFile() && stripos($item->getExtension(), 'php') !== false) {
                        if (is_writable($item->getPathName()) === false) {
                            echo "WPpluginLoader: unable to read/write file ".$item->getPathName(). PHP_EOL;
                            continue;
                        }
        
                        //TODO read and replace on the fly
                        $itemFile = new \SplFileObject($item->getPathName());
                        $file_contents = "";
                        while (!$itemFile->eof()) {
                            $file_contents.= $itemFile->fgets();
                        }

                        //Make sure it was not wrapped already
                        foreach ($supplierstochange as $supplier => $new) {
                            if (empty($new)) {
                                throw new \Exception('Trying to wrap to an empty namespace');
                            }

                            //Make sure it was not wrapped already
                            if ($pos = strpos($file_contents, $new) !== false) {
                                throw new \Exception(
                                    'Double wrapping? file already contains namespace '.
                                    $new.' @'.$pos.' in '.$item->getPathName()
                                );
                            }
                        }

                        foreach ($supplierstochange as $supplier => $new) {
                            // print $supplier .' -> '.$new. PHP_EOL;
                            //Added to handle sub namesapce want to access to root namespace
                            $file_contents = str_replace(" \\$supplier\\", " \\$new\\", $file_contents);
        
                            $file_contents = str_replace(" $supplier\\", " $new\\", $file_contents);
                            $file_contents = str_replace("\"$supplier\\", "\"$new\\", $file_contents);
                            $file_contents = str_replace("\"\\$supplier\\", "\"\\$new\\", $file_contents);
                            $file_contents = str_replace("'$supplier\\", "'$new\\", $file_contents);
                            $file_contents = str_replace("'\\$supplier\\", "'\\$new\\", $file_contents);
                            $file_contents = str_replace(" $supplier;", " $new;", $file_contents);
                            $file_contents = str_replace("use $supplier", "use $new", $file_contents);

                            //remove unwanted replacements
                            $file_contents = str_replace("as $new", "as $supplier", $file_contents);

                            //Replace if in text form (Ex. parent\\namespace), if supplier is multiple namespaced
                            if (strpos($supplier, '\\') !== false) {
                                $textsupplier = addslashes($supplier);
                                $textnew      = addslashes($new);
                                $file_contents = str_replace("$textsupplier", "$textnew", $file_contents);
                            }
                        }
                        // print_r($file_contents);
                        $itemFile = new \SplFileObject($item->getPathName(), "w");
                        $itemFile->fwrite($file_contents);
                    }
                }
            }
        }
    }

    public static function unwrapPackage($packages, $vendorDir = '')
    {
        echo "----- unwrapping packages -------". PHP_EOL;
        if (empty($vendorDir)) {
            $vendorDir = __DIR__.'/../../../';
        }

        $namespacesToUnwrap = self::getDeps($packages, $vendorDir);

        $deppackages        = $namespacesToUnwrap['deps'];
        $namespacesToUnwrap = array_flip($namespacesToUnwrap['wrap']);

        $rootsToremove = array();
        foreach ($namespacesToUnwrap as $wrapped => $namespace) {
            $root = stristr($wrapped, '\\', true);
            if (!in_array($root, $rootsToremove)) {
                $rootsToremove[] = $root;
            }
        }

        //Navigate through all packages dependancy
        foreach ($deppackages as $deppackage => $autoloads) {
            foreach ($autoloads as $namespace => $relDir) {
                //Path of files to wrap
                $wrappingPath = realpath($vendorDir.DIRECTORY_SEPARATOR.$deppackage.DIRECTORY_SEPARATOR.$relDir);
                echo "----- unwrapping $deppackage -------".$deppackage.DIRECTORY_SEPARATOR.$relDir.PHP_EOL;

                if (empty($wrappingPath)) {
                    print "Empty Directory: $wrappingPath - skipping".PHP_EOL;
                    continue;
                }
                $fileList = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator(
                        $wrappingPath
                    ),
                    \RecursiveIteratorIterator::SELF_FIRST
                );
                foreach ($fileList as $item) {
                    if ($item->isFile() && stripos($item->getExtension(), 'php') !== false) {
                        if (is_writable($item->getPathName()) === false) {
                            echo "WPpluginLoader: unable to read/write file ".$item->getPathName(). PHP_EOL;
                            continue;
                        }

                        //TODO read and replace on the fly
                        $itemFile = new \SplFileObject($item->getPathName());
                        $file_contents = "";
                        while (!$itemFile->eof()) {
                            $file_contents.= $itemFile->fgets();
                        }

                        //First replace full namespace
                        foreach ($namespacesToUnwrap as $unwrapNamespace => $oldNamespace) {
                            if (empty($unwrapNamespace)) {
                                throw new \Exception('Trying to unwrap from empty namespace');
                            }
                            $file_contents = str_replace("$unwrapNamespace", "$oldNamespace", $file_contents);
                        }

                        //then remove root for to unwrap "broken" appearances of namespace
                        foreach ($rootsToremove as $unwrapNamespace) {
                            //Replace if in text form (Ex. parent\\namespace)
                            // $textsupplier = addslashes($supplier);
                            // $textnew      = addslashes($new);
                            $file_contents = str_replace("$unwrapNamespace\\\\", "", $file_contents);

                            $file_contents = str_replace(" \\$unwrapNamespace\\", " \\", $file_contents);
                            $file_contents = str_replace(" $unwrapNamespace\\", " ", $file_contents);
                            $file_contents = str_replace("\"$unwrapNamespace\\", "\"", $file_contents);
                            $file_contents = str_replace("\"\\$unwrapNamespace\\", "\"\\", $file_contents);
                            $file_contents = str_replace("'$unwrapNamespace\\", "'", $file_contents);
                            $file_contents = str_replace("'\\$unwrapNamespace\\", "'\\", $file_contents);
                        }

                        $itemFile = new \SplFileObject($item->getPathName(), "w");
                        $itemFile->fwrite($file_contents);
                    }
                }
            }
        }
    }
    /**
     * Registers a set of PSR-4 directories for a given namespace,
     * replacing any others previously set for this namespace.
     *
     * @param string       $prefix  The prefix/namespace, with trailing '\\'
     * @param array|string $paths   The PSR-4 base directories
     */
    public function setPsr4($prefix, $paths)
    {
        if (!$prefix) {
            $this->fallbackDirsPsr4 = (array) $paths;
        } else {
            $length = strlen($prefix);
            if ('\\' !== $prefix[$length - 1]) {
                throw new \InvalidArgumentException("A non-empty PSR-4 prefix must end with a namespace separator.");
            }
            $this->prefixLengthsPsr4[$prefix[0]][$prefix] = $length;
            $this->prefixDirsPsr4[$prefix] = (array) $paths;
        }
    }

    /**
     * Registers a set of PSR-4 directories for a given namespace, either
     * appending or prepending to the ones previously set for this namespace.
     *
     * @param string       $prefix  The prefix/namespace, with trailing '\\'
     * @param array|string $paths   The PSR-0 base directories
     * @param bool         $prepend Whether to prepend the directories
     */
    public function addPsr4($prefix, $paths, $prepend = false)
    {
        if (!$prefix) {
            // Register directories for the root namespace.
            if ($prepend) {
                $this->fallbackDirsPsr4 = array_merge(
                    (array) $paths,
                    $this->fallbackDirsPsr4
                );
            } else {
                $this->fallbackDirsPsr4 = array_merge(
                    $this->fallbackDirsPsr4,
                    (array) $paths
                );
            }
        } elseif (!isset($this->prefixDirsPsr4[$prefix])) {
            // Register directories for a new namespace.
            $length = strlen($prefix);
            if ('\\' !== $prefix[$length - 1]) {
                throw new \InvalidArgumentException("A non-empty PSR-4 prefix must end with a namespace separator.");
            }
            $this->prefixLengthsPsr4[$prefix[0]][$prefix] = $length;
            $this->prefixDirsPsr4[$prefix] = (array) $paths;
        } elseif ($prepend) {
            // Prepend directories for an already registered namespace.
            $this->prefixDirsPsr4[$prefix] = array_merge(
                (array) $paths,
                $this->prefixDirsPsr4[$prefix]
            );
        } else {
            // Append directories for an already registered namespace.
            $this->prefixDirsPsr4[$prefix] = array_merge(
                $this->prefixDirsPsr4[$prefix],
                (array) $paths
            );
        }
    }


    /**
     * Registers a set of PSR-0 directories for a given prefix, either
     * appending or prepending to the ones previously set for this prefix.
     *
     * @param string       $prefix  The prefix
     * @param array|string $paths   The PSR-0 root directories
     * @param bool         $prepend Whether to prepend the directories
     */
    public function add($prefix, $paths, $prepend = false)
    {
        if (!$prefix) {
            if ($prepend) {
                $this->fallbackDirsPsr0 = array_merge(
                    (array) $paths,
                    $this->fallbackDirsPsr0
                );
            } else {
                $this->fallbackDirsPsr0 = array_merge(
                    $this->fallbackDirsPsr0,
                    (array) $paths
                );
            }

            return;
        }

        $first = $prefix[0];
        if (!isset($this->prefixesPsr0[$first][$prefix])) {
            $this->prefixesPsr0[$first][$prefix] = (array) $paths;

            return;
        }
        if ($prepend) {
            $this->prefixesPsr0[$first][$prefix] = array_merge(
                (array) $paths,
                $this->prefixesPsr0[$first][$prefix]
            );
        } else {
            $this->prefixesPsr0[$first][$prefix] = array_merge(
                $this->prefixesPsr0[$first][$prefix],
                (array) $paths
            );
        }
    }

    /**
     * Registers a set of PSR-0 directories for a given prefix,
     * replacing any others previously set for this prefix.
     *
     * @param string       $prefix The prefix
     * @param array|string $paths  The PSR-0 base directories
     */
    public function set($prefix, $paths)
    {
        if (!$prefix) {
            $this->fallbackDirsPsr0 = (array) $paths;
        } else {
            $this->prefixesPsr0[$prefix[0]][$prefix] = (array) $paths;
        }
    }
    /**
     * Finds the path to the file where the class is defined.
     *
     * @param string $class The name of the class
     *
     * @return string|false The path if found, false otherwise
     */
    public function findFile($class)
    {
        //PSR-4
        // $this->prefixLengthsPsr4 = ;
        // $this->prefixDirsPsr4    = $this->getPrefixesPsr4();
        // $this->fallbackDirsPsr4  = $this->getFallbackDirsPsr4();
        //PSR-0
        // $this->prefixesPsr0       = $this->getPrefixes();
        // $this->fallbackDirsPsr0   = $this->getFallbackDirs();
        $this->useIncludePath = $this->getUseIncludePath();
        $this->classMap       = $this->getClassMap();

        // work around for PHP 5.3.0 - 5.3.2 https://bugs.php.net/50731
        if ('\\' == $class[0]) {
            $class = substr($class, 1);
        }

        // class map lookup
        if (isset($this->classMap[$class])) {
            return $this->classMap[$class];
        }

        // PSR-4 lookup
        $logicalPathPsr4 = strtr($class, '\\', DIRECTORY_SEPARATOR) . '.php';
        // WP PSR-4 lookup
        $logicalPathPsr4WP = substr(stristr($logicalPathPsr4, DIRECTORY_SEPARATOR), 1);

        $first = $class[0];
        if (isset($this->prefixLengthsPsr4[$first])) {
            foreach ($this->prefixLengthsPsr4[$first] as $prefix => $length) {
                if (0 === strpos($class, $prefix)) {
                    foreach ($this->prefixDirsPsr4[$prefix] as $dir) {
                        if (file_exists($file = $dir . DIRECTORY_SEPARATOR . substr($logicalPathPsr4, $length))) {
                            return $file;
                        }
                        //WP add on
                        if (file_exists($file = $dir . DIRECTORY_SEPARATOR . $logicalPathPsr4WP)) {
                            return $file;
                        }
                    }
                }
            }
        }

        // PSR-4 fallback dirs
        foreach ($this->fallbackDirsPsr4 as $dir) {
            if (file_exists($file = $dir . DIRECTORY_SEPARATOR . $logicalPathPsr4)) {
                return $file;
            }
        }

        $logicalPathPsr0 = '';
        // PSR-0 lookup
        if (false !== $pos = strrpos($class, '\\')) {
            // namespaced class name
            $logicalPathPsr0 = substr($logicalPathPsr4, 0, $pos + 1)
                . strtr(substr($logicalPathPsr4, $pos + 1), '_', DIRECTORY_SEPARATOR);
        } else {
            // PEAR-like class name
            $logicalPathPsr0 = strtr($class, '_', DIRECTORY_SEPARATOR) . '.php';
        }
        // WP PSR-0 lookup
        $logicalPathPsr0WP = substr(stristr($logicalPathPsr0, DIRECTORY_SEPARATOR), 1);

        if (isset($this->prefixesPsr0[$first])) {
            foreach ($this->prefixesPsr0[$first] as $prefix => $dirs) {
                if (0 === strpos($class, $prefix)) {
                    foreach ($dirs as $dir) {
                        if (file_exists($file = $dir . DIRECTORY_SEPARATOR . $logicalPathPsr0)) {
                            return $file;
                        }
                        //WP add on
                        if (file_exists($file = $dir . DIRECTORY_SEPARATOR . $logicalPathPsr0WP)) {
                            return $file;
                        }
                    }
                }
            }
        }

        // PSR-0 fallback dirs
        foreach ($this->fallbackDirsPsr0 as $dir) {
            if (file_exists($file = $dir . DIRECTORY_SEPARATOR . $logicalPathPsr0)) {
                return $file;
            }
        }

        // PSR-0 include paths.
        if ($this->useIncludePath && $file = stream_resolve_include_path($logicalPathPsr0)) {
            return $file;
        }

        // Remember that this class does not exist.
        return $this->classMap[$class] = false;
    }

    public function composerRequire($fileIdentifier, $file)
    {
        if (empty($GLOBALS['__composer_autoload_files'][$fileIdentifier])) {
            require $file;

            $GLOBALS['__composer_autoload_files'][$fileIdentifier] = true;
        }
    }
}
