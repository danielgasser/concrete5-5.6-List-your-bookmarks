<?php 
	defined('C5_EXECUTE') or die("Access Denied.");

/**
 * Class PcShooterChListFavoritesBlockController
 */
class PcShooterChListFavoritesBlockController extends Concrete5_Controller_Block_File {
    protected $btName = "List Your Bookmarks";
    protected $btTable = 'btPcShooterChListFavorites';
    protected $btInterfaceWidth = 960;
    protected $btInterfaceHeight = 600;
    protected $bookmarkTable = 'btPcShooterChListFavoritesBookMarks';
    protected $bookmarkTableForeignKeyField = 'blockID';

    public $blankImage = 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D';
    public $bookMarkData = array();

    public function getBlockTypeName() {
        return t("Bookmark Block(s)");
    }

    public function getBlockTypeDescription() {
        return t("Import your bookmarks into a list.");
    }

    public function getPkgHandle() {
        return BlockType::getByHandle($this->btHandle)->getPackageHandle();
    }

    public function getBookmarkTableColumnsNames() {
        $db = Loader::db();
        $query = "SELECT ORDINAL_POSITION, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='" . $this->bookmarkTable . "' AND COLUMN_NAME LIKE '" . $this->bookmarkTable . "%'";
        return $db->GetAssoc($query);
    }

    public function getJavaScriptStrings() {
        return array(
            'no-html' => t('You must select a HTML or HTM file.'),
            'no-image' => t('You must select an image.'),
            'test-link' => t('Test bookmark.'),
            'bookmark-error' => t('This bookmark is not a valid URL.'),
            'see-errors' => t('See header details.'),
            'url-error-dialog-title' => t('Header details for URL'),
            // Der angegebene Host ist unbekannt
            'no-errors-to-show' => t('The specified host is unknown.'),
            'bookmark-valid' => t('This bookmark is valid.'),
            'no-js-links' => t('No JavaScript-links allowed!'),
            'num-records' => t('Entries'),
            'close' => t('Close'),
            'print' => t('Print'),
            'copy' => t('Copy'),
            'meta-icon' => t('Icon'),
            'meta-title' => t('Title'),
            'meta-date' => t('Date'),
            'meta-url' => t('WWW'),
            'meta-check' => t('Test bookmark.'),
            'meta-errors' => t('See header details.'),
            'meta-delete' => t('Delete')
        );
    }
    public function setBookmarkData($args) {
        $db = Loader::db();
        $db->Execute('DELETE FROM ' . $this->bookmarkTable . ' WHERE ISNULL(' . $this->bookmarkTableForeignKeyField . ')');
        $db->Execute($this->createInsertBookmarksQuery($args));
    }

    public function createInsertBookmarksQuery($args)
    {
        $queryStr = 'INSERT INTO ' . $this->bookmarkTable . ' (' . $this->bookmarkTableForeignKeyField . ', ';
        $queryStr .= implode(', ', array_keys($args[0]));
        $queryStr .= ') VALUES ';

        foreach ($args as $arg) {
            $queryStr .= '(' . PHP_INT_MAX . ', ';
            foreach ($arg as $value) {
                $queryStr .= '\'' . $value . '\', ';
            }
            $queryStr = substr($queryStr, 0, -2);
            $queryStr .= '), ';
        }
        $queryStr = substr($queryStr, 0, -2);

        return $queryStr;
    }

    public function getBookmarkData(){

        return $this->bookMarkData;
    }
    public function view() {
        $this->clearHtmlExtension($this->getPkgHandle());
        $this->set('bookMarkData', $this->bookMarkData);
    }

    public function clearHtmlExtension($pkg) {
        $co = new Config();
        $co->setPackageObject($pkg);
        $co->clear('UPLOAD_FILE_EXTENSIONS_CONFIGURABLE');
        $co->clear('UPLOAD_FILE_EXTENSIONS_ALLOWED');
    }

    private function setHtmlExtension() {
        $co = new Config();
        $co->setPackageObject($this->getPkgHandle());
        $co->save('UPLOAD_FILE_EXTENSIONS_CONFIGURABLE', true);
        $co->save('UPLOAD_FILE_EXTENSIONS_ALLOWED', '*.htm;*.html;');
    }

    public function save($args) {
        $db = Loader::db();
        parent::save($args);
        $this->clearHtmlExtension($this->getPkgHandle());
        Database::setDebug(false);
        Log::addEntry('UPDATE ' . $this->bookmarkTable . ' SET ' . $this->bookmarkTableForeignKeyField . ' = ' . $this->bID . ' WHERE ' . $this->bookmarkTableForeignKeyField . ' = ' . PHP_INT_MAX);
        $db->Execute('UPDATE ' . $this->bookmarkTable . ' SET ' . $this->bookmarkTableForeignKeyField . ' = ' . $this->bID . ' WHERE ' . $this->bookmarkTableForeignKeyField . ' = ' . PHP_INT_MAX);
    }

    public function delete() {
        $db = Loader::db();
        parent::delete();
        $db->Execute('DELETE FROM ' . $this->bookmarkTable . ' WHERE ' . $this->bookmarkTableForeignKeyField . ' = ' . $this->bID);
    }

    public function validate($args) {
        $e = Loader::helper('validation/error');
        if ($args['fID'] < 1) {
            $e->add(t('You must select a file.'));
        }
        if (trim($args['fileLinkText']) == '') {
            $e->add(t('You must give your file a link.'));
        }

        return $e;
    }

    public function add() {
        $this->set_package_tool('upload_html');
        $this->setHtmlExtension();
        $this->set('blankImage', $this->blankImage);
        $this->set('bookMarkData', $this->bookMarkData);
    }

    public function edit() {
        $this->set_package_tool('upload_html');
        $this->setHtmlExtension();
        $this->set('blankImage', $this->blankImage);
    }

    public function getDateFormat() {

        return t('Y-m-d');
    }

    function getFileObject() {

        return File::getByID($this->fID);
    }

    private function set_block_tool($tool_name, $params) {
        $i = 0;
        $parStr = '';
        if (is_array($params)) {
            foreach($params as $key => $value) {
                $prefix = ($i == 0) ? '?' : '&';
                $parStr .= $prefix . $key . '=' . $value;
                $i++;
            }
        }
        $tool_helper = Loader::helper('concrete/urls');
        $bt = BlockType::getByHandle($this->btHandle);
        $this->set($tool_name, $tool_helper->getBlockTypeToolsURL($bt) . '/' . $tool_name);
    }

    private function set_package_tool($tool_name, $params = '') {
        $i = 0;
        $parStr = '';
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                $prefix = ($i == 0) ? '?' : '&';
                $parStr .= $prefix . $key . '=' . $value;
                $i++;
            }
        }

        $tool_helper = Loader::helper('concrete/urls');
        $this->set($tool_name, $tool_helper->getToolsURL($tool_name . $params, $this->getPkgHandle()));
    }

}