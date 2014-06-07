<?php
namespace GbiliFilesAsStorageModule\Service;

/**
 * There is a storage dir (passed to constructor)
 * THen there are subdirectories that add some taxonomy
 * to each data
 * Ex : /storage_dir/something
 * The something directory contains each data item
 * which is represented by a file with the item identifier
 * Ex : /storage_dir/some_taxonomy/i_am_that_thing.php
 * i_am_that_thing is used as identifier.
 *
 * Each file should return the data that should
 * be mapped to that identifier.
 * Ex: 
 * `<?php
 * return array('this' => 'is some data');
 * `
 * 
 * What this class does is to try to get all items
 * in some taxonomy dir (ex:some_taxonomy). And it
 * caches all the items in $cache. Each item
 * has its identifier as key, and each collection
 * of items is grouped under the taxonomy key.
 * Ex: 
 * `$this->cache = array(
 *     'some_taxonomy' => array(
 *         'i_am_that_thing' => array('this' => 'is some data')
 *         ...
 *     ),
 *     'some_other_taxonomy' => array(
 *         'i_am_another_thing' => array('that' => 'some data')
 *         'i_am' => array('this' => 'is some other data', ...)
 *         ...
 *     ),
 *     ...
 * );`
 *
 * Usage: 
 *     $someTaxonomy = getSomeTaxonomy('Some_item')
 *
 */
class FilesAsStorage
{
    /**
     * strlen('.php')
     */
    const DOT_PHP_LEN = 4; 

    protected $storageDir;

    protected $cache;

    /**
     * Query the taxonomies and files with a different inflection
     * than what they are saved with.
     * Example:
     *      Filename: magic_place/cueva_del_majanicho.php
     *      Query: $service->getMagicPlace('cueva_del_majanicho');
     *      Here you should pass an inflector UnderToCamelCase
     *      Alternatively you could query: 
     *          $service->getmagic_place('cueva_del_majanicho')
     *          $service->magic_place('cueva_del_majanicho')
     */
    protected $inflector;

    /**
     * @param $inflector set to false to avoid inflection
     */
    public function __construct($storageDir, $inflector=null)
    {
        $this->storageDir = $storageDir;
        if (null == $inflector) {
            // CamelCase to under 
            $inflector = function ($string) {
                $parts = preg_split('/(?=[A-Z])', $string);
                return strtolower(implode('_', $parts));
            };
        }
        $this->setInflector($inflector);
    }

    /**
     * Load and return the specified taxonomy (optionally item)
     * getPlace('San_Franciso');
     * taxonomy: Place
     * item: San_Francisco
     */
    public function __call($method, $params)
    {
        $taxonomy = $this->extractTaxonomyName($method);
        if (!isset($this->cache[$taxonomy])) {
            $this->loadTaxonomyIntoCache($taxonomy);
        }
        $ret = $this->cache[$taxonomy];

        if (!empty($params)) {
            $itemIdentifier = current($params);
            if (!isset($ret[$itemIdentifier])) {
                throw new \Exception('Item does not exist ' . $itemIdentifier);
            }
            $ret = $ret[$itemIdentifier];
        }
        return $ret;
    }

    /**
     * If thaxonomy has not items (files) then it returns
     * empty array
     * @throws Exception if taxonomy (dir) does not exist
     */
    public function loadTaxonomyIntoCache($what)
    {
        $taxonomyDir = $this->storageDir . '/' . $what;
        if (!is_dir($taxonomyDir)) {
            throw new \Exception('Not a directory');
        }

        $dirIterator        = new \DirectoryIterator($taxonomyDir);
        $this->cache[$what] = array();
        foreach ($dirIterator as $item) {
            if (!$item->isFile()) continue;
            $fileBasename = $item->getBasename();
            $identifier   = substr($fileBasename, 0, -self::DOT_PHP_LEN);
            $this->cache[$what][$identifier] = include $item->getPath() . '/' . $fileBasename;
        }
    }

    public function extractTaxonomyName($methodName)
    {
        $getPrefixed = ('get' === substr($methodName, 0, 3));
        $what = ((!$getPrefixed)? $methodName : substr($methodName, 3));

        if ($this->hasInflector()) {
            if (is_callable($this->inflector)) {
                $what = $this->inflector($what);
            } else {
                $what = call_user_func(array($this->inflector, ''), $what);
            }
        }
        return $what;
    }

    public function hasInflector()
    {
        return null !== $this->inflector;
    }

    public function getInflector()
    {
        return $this->inflector;
    }

    public function setInflector($inflector)
    {
        if (null !== $inflector) {
            $this->inflector = $inflector;
        }
        return $this;
    }
}
