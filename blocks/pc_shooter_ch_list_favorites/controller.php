<?php 
	defined('C5_EXECUTE') or die("Access Denied.");
	
class PcShooterChListFavoritesBlockController extends Concrete5_Controller_Block_File {
    protected $btName = "List Your Bookmarks";
    protected $btTable = 'btPcShooterChListFavorites';
    protected $btInterfaceWidth = 960;
    protected $btInterfaceHeight = 600;
    protected $bookmarkTable = 'btPcShooterChListFavoritesBookMarks';
    protected $bookmarkTableForeignKeyField = 'blockID';


    public function getBlockTypeName() {
        return t("Bookmark Block(s)");
    }

    public function getBlockTypeDescription() {
        return t("Import your bookmarks into a list.");
    }

    public function getPkgHandle() {
        return BlockType::getByHandle($this->btHandle)->getPackageHandle();

    }

    public function getJavaScriptStrings() {
        return array(
            'no-html' => t('You must select a HTML or HTM file.'),
            'no-image' => t('You must select an image.'),
            'test-link' => t('Test bookmark.'),
            'bookmark-error' => t('This bookmark is not a valid URL.'),
            'see-errors' => t('See header details.'),
            'url-error-dialog-title' => t('Header details for URL'),
            'no-errors-to-show' => t('No errors to show.'),
            'bookmark-valid' => t('This bookmark is valid.'),
            'no-js-links' => t('No JavaScript-links allowed!'),
            'num-records' => t('Entries')
        );
    }

    public function add(){
        $this->set_package_tool('check_url');
    }

    public function edit(){
        $db = Loader::db();
        $this->set_package_tool('check_url');
        $this->set('soUndSo', $db->GetAll('SELECT * FROM ' . $this->bookmarkTable . ' WHERE ' . $this->bookmarkTableForeignKeyField . ' = ' . $this->bID));
    }

    public function validate($args) {
        return;
    }

    /**
     * Calls the parent save method.
     * Reconstructs the posted multiple-fields
     * into an db-query string
     * and saves it into $this->bookmarkTable
     *
     * @param array $args
     */
    public function save($args){
        Database::setDebug(false);
        $db = Loader::db();
        $keyArr = array();

        $keyArr[] = $this->bookmarkTableForeignKeyField;

        parent::save($args);
        $newArgs = self::transformPosts($args);
        $queryStr = $newArgs[1];
        $queryStr .= self::createQueryString($newArgs[0]);
        $db->Execute($queryStr);
       // print $queryStr;
    }

    public function view() {
        Database::setDebug(true);
        $db = Loader::db();
        $this->set('soUndSo', $db->GetAll('SELECT * FROM ' . $this->bookmarkTable . ' WHERE ' . $this->bookmarkTableForeignKeyField . ' = ' . $this->bID));
    }

    public function on_start() {
        $html = Loader::helper('html');
        $this->addHeaderItem($html->css(BASE_URL . DIR_REL . '/' . DIRNAME_PACKAGES . '/' . self::getPkgHandle() . '/css/add.css'));
        $this->addHeaderItem($html->javascript(BASE_URL . DIR_REL . '/' . DIRNAME_PACKAGES . '/' . self::getPkgHandle() . '/js/add.js'));
    }

    private function set_package_tool($tool_name){
        $tool_helper = Loader::helper('concrete/urls');
        $this->set ( $tool_name, $tool_helper->getToolsURL($tool_name, $this->getPkgHandle()));
    }

    /**
     * Creates 2-dimensional array out of 4 1-dimensional ones * number of records (4 form-fields per record)
     * Array [number of records][number of form-fields]
     *
     * @param $args: Posted values
     * @return string: The VALUES - part of the query-string
     */
    private function transformPosts($args){
        $queryStr = 'INSERT INTO ' . $this->bookmarkTable . ' (' . $this->bookmarkTableForeignKeyField . ', ';
        $counterFields = 0;
        $ret = array();
        foreach($args as $key => $val){
            if (strpos($key, $this->bookmarkTable) !== false ){
                $queryStr .= $key . ', ';
            }
            if (is_array($val)){
                $counterValues = 0;
                while($counterValues <  sizeof($val)){
                    $finalArr[$counterValues][$counterFields] = $val[$counterValues];
                    $counterValues++;
                }
                $counterFields++;
            }
        }
        $queryStr = substr($queryStr, 0, -2);
        $queryStr .= ') VALUES ';
        $ret[0] = $finalArr;
        $ret[1] = $queryStr;
        return $ret;
    }

    /**
     * Create query string
     * Reason: A 2-dimensional array is passed
     *      With a lot of records, the ADOB query() method
     *      sends too much queries.
     * A query string for 1 INSERT query with (val,val,...), (val,val,...), (val,val,...)
     * is created here.
     */
    private function createQueryString($args){
        $valuesStr = '';
        foreach($args as $val){
            $valuesStr .= '(' . $this->bID . ', ';
            foreach($val as $value){
                if (strpos($value, 'data:image') === false){
                    $fieldValue =  mysql_real_escape_string($value);
                    $prefix = '\'';
                }else {
                    $fieldValue =  $value;
                    $prefix = '\'';
                }
                $valuesStr .= $prefix . $fieldValue . $prefix . ', ';
            }
            $valuesStr = substr($valuesStr, 0,  -2);
            $valuesStr .= '), ';
        }
        $valuesStr = substr($valuesStr, 0,  -2);

        return $valuesStr;
    }
}