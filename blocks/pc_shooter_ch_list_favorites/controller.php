<?php
/**
 * List Your Bookmarks Block controller
 * @author pc-shooter <info@pc-shooter.ch>
 * @package List Your Bookmarks
 * @version 0.1
 * @filesource
 */

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * List Your Bookmarks block controller
 * @author pc-shooter <info@pc-shooter.ch>
 *
 * @version Development version 0.1
 * @category C5 package
 * @copyright pc-shooter - Development
 */
class PcShooterChListFavoritesBlockController extends Concrete5_Controller_Block_File {
    /**
     * @var string Blocks name
     */
    protected $btName = "List Your Bookmarks";
    /**
     * @var string DB block table name
     */
    protected $btTable = 'btPcShooterChListFavorites';
    /**
     * @var int Add/edit window width
     */
    protected $btInterfaceWidth = 1250;
    /**
     * @var int Add/edit window height
     */
    protected $btInterfaceHeight = 600;
    /**
     * @var string DB bookmark table name
     */
    protected $bookmarkTable = 'btPcShooterChListFavoritesBookMarks';
    /**
     * @var string DB bookmark table foreign key name
     */
    protected $bookmarkTableForeignKeyField = 'blockID';

    /**
     * @var string C5 Styling
     */
    protected $btWrapperClass = 'ccm-ui';

    /**
     * @var string Blank image for icon
     */
    public $blankImage = 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D';
    /**
     * @var array Bookmarks container
     */
    public $bookMarkData = array();

    /**
     * @var string Package handle
     */
    public $pkgHandle = 'pc_shooter_ch_list_favorites';

    /**
     * Get block type name
     * @return string Block type name
     */
    public function getBlockTypeName() {
        return t("Bookmark Block(s)");
    }

    /**
     * Get block type description
     * @return string Block type description
     */
    public function getBlockTypeDescription() {
        return t("Import your bookmarks into a list.");
    }

    /**
     * Get package name
     * @return mixed Package Handle
     */
    public function getPkgHandle() {
        return $this->pkgHandle;
    }

    /**
     * Get columns names from DB-bookmark table
     * @return array Columns names from DB-bookmark table
     */
    public function getBookmarkTableColumnsNames() {
        $db = Loader::db();
        $query = "SELECT ORDINAL_POSITION, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='" . $this->bookmarkTable . "' AND COLUMN_NAME LIKE '" . $this->bookmarkTable . "%'";
        return $db->GetAssoc($query);
    }

    /**
     * Language getter from Javascript
     * @return array
     */
    public function getJavaScriptStrings() {
        return array(
            'no-html' => t('You must select a HTML or HTM file.'),
            'no-image' => t('You must select an image.'),
            'test-link' => t('Test bookmark.'),
            'bookmark-error' => t('This bookmark is not a valid URL.'),
            'see-errors' => t('See header details.'),
            'url-error-dialog-title' => t('Header details for URL'),
            'parsing-failed' => t('The bookmark file wasn\'t parsed'),
            // Der angegebene Host ist unbekannt
            'num-records' => t('Entries'),
            'meta-title' => ('Title'),
            'meta-date' => ('Date added'),
            'meta-url' => ('www'),
            'meta-check' => ('Test bookmark'),
            'meta-errors' => ('See header details'),
            'meta-delete' => ('Delete'),
            'no-errors-to-show' => t('The specified host is unknown.'),
            'bookmark-valid' => t('This bookmark is valid.'),
            'no-js-links' => t('No JavaScript-links allowed!'),
            'close' => t('Close'),
            'print' => t('Print'),
            'copy' => t('Copy')
        );
    }

    /**
     * Save bookmarks into DB
     * and sets 'bookMarkData'
     * @param $args Parsed Html-file assoc. array
     */
    public function setBookmarkData($args) {
        $this->bookMarkData = $args;
        $t = '';
        $db = Loader::db();
        $this->deleteBookmarkRecords(PHP_INT_MAX);
        $db->Execute($this->createInsertBookmarksQuery($args));
        $this->set('bookMarkData', $this->bookMarkData);
    }

    /**
     * Create INSERT query from parsed Html-file assoc. array
     * @param $args Parsed Html-file assoc. array
     * @return string The MySQL query
     */
    public function createInsertBookmarksQuery($args) {
        $queryStr = 'INSERT INTO ' . $this->bookmarkTable . ' (' . $this->bookmarkTableForeignKeyField . ', ';
        $queryStr .= implode(', ', array_keys($args[0]));
        $queryStr .= ', Zsort) VALUES ';
        Log::addEntry($queryStr);
        $i = 0;
        foreach ($args as $arg) {
            $queryStr .= '(' . PHP_INT_MAX . ', ';
            foreach ($arg as $value) {
                $queryStr .= '\'' . $value . '\', ';
            }
            $queryStr .= $i . ', ';
            $queryStr = substr($queryStr, 0, -2);
            $queryStr .= '), ';
            $i++;
        }
        $queryStr = substr($queryStr, 0, -2);
        Log::addEntry($queryStr);

        return $queryStr;
    }

    /**
     * Get 'bookMarkData'
     * @return array
     */
    public function getBookmarkData() {

        return $this->bookMarkData;
    }

    /**
     * Get 'bookMarkDataAjax'
     * @return array
     */
    public function action_get_bookmark_data_json() {
        echo json_encode($this->getBookmarkDataRecords($this->bID));
       // exit;
    }

    /**
     * Get bookmarks from DB
     * @param $blockID bID
     * @return mixed db records array
     */
    public function getBookmarkDataRecords($blockID) {
        $db = Loader::db();

        return $db->GetAll('SELECT bookmarkID, btPcShooterChListFavoritesBookMarksIcon, btPcShooterChListFavoritesBookMarksDate, btPcShooterChListFavoritesBookMarksText, btPcShooterChListFavoritesBookMarksUrl, Zsort FROM ' . $this->bookmarkTable . ' WHERE ' . $this->bookmarkTableForeignKeyField . ' = ' . $blockID . ' ORDER BY Zsort ASC');
    }

    public function updateBookmarksByID($bookmarkID, $args) {
        $db = Loader::db();
        Log::addEntry('UPDATE ' . $this->bookmarkTable . ' SET
                        btPcShooterChListFavoritesBookMarksIcon = "' . mysql_real_escape_string($args[0]) . '",
                        btPcShooterChListFavoritesBookMarksText = "' . mysql_real_escape_string($args[1]) . '",
                        btPcShooterChListFavoritesBookMarksDate = "' . mysql_real_escape_string($args[2]) . '",
                        btPcShooterChListFavoritesBookMarksUrl = "' . mysql_real_escape_string($args[3]) . '",
                        Zsort = ' . mysql_real_escape_string($args[4]) . '
                         WHERE bookmarkID = ' . $bookmarkID);


        $db->Execute('UPDATE ' . $this->bookmarkTable . ' SET
                        btPcShooterChListFavoritesBookMarksIcon = "' . mysql_real_escape_string($args[0]) . '",
                        btPcShooterChListFavoritesBookMarksText = "' . mysql_real_escape_string($args[1]) . '",
                        btPcShooterChListFavoritesBookMarksDate = "' . mysql_real_escape_string($args[2]) . '",
                        btPcShooterChListFavoritesBookMarksUrl = "' . mysql_real_escape_string($args[3]) . '",
                        Zsort = ' . mysql_real_escape_string($args[4]) . '
                         WHERE bookmarkID = ' . $bookmarkID);
    }
    /**
     * Delete bookmarks from db
     * @param $blockID bID
     */
    public function deleteBookmarkRecords($blockID) {
        $db = Loader::db();
        $db->Execute('DELETE FROM ' . $this->bookmarkTable . ' WHERE ' . $this->bookmarkTableForeignKeyField . ' = ' . $blockID);
    }

    /**
     * Updates the blockid of bookmarks
     */
    public function updateBookmarkRecordsBlockID() {
        $db = Loader::db();
        $db->Execute('UPDATE ' . $this->bookmarkTable . ' SET ' . $this->bookmarkTableForeignKeyField . ' = ' . $this->bID . ' WHERE ' . $this->bookmarkTableForeignKeyField . ' = ' . PHP_INT_MAX);
    }

    /**
     * Updates the blockid of bookmarks
     */
    public function updateAllBookmarkRecordsSort() {
        $db = Loader::db();
        $i = $db->Getall('SELECT bookmarkID, Zsort FROM ' . $this->bookmarkTable . ' WHERE ' . $this->bookmarkTableForeignKeyField . ' = ' . $this->bID);
        foreach ($i as $val) {
            foreach ($val as $key => $value) {
                Log::addEntry($key . ': '. $value);
            }

        }

       // foreach ($c as $key => $val) {
       //     Log::addEntry('UPDATE ' . $this->bookmarkTable . ' SET Zsort = ' . $i . ' WHERE bookmarkID = ' . $key);
       //     $db->Execute('UPDATE ' . $this->bookmarkTable . ' SET Zsort = ' . $i . ' WHERE bookmarkID = ' . $key);
       // }
    }

    /**
     * Clears permission to upload html files into file manager
     * @param $pkg Package handle
     */
    public function clearHtmlExtension($pkg) {
        $co = new Config();
        $co->setPackageObject($pkg);
        $co->clear('UPLOAD_FILE_EXTENSIONS_CONFIGURABLE');
        $co->clear('UPLOAD_FILE_EXTENSIONS_ALLOWED');
    }

    /**
     * Sets permission to upload html files into file manager
     */
    private function setHtmlExtension() {
        $co = new Config();
        $co->setPackageObject($this->getPkgHandle());
        $co->save('UPLOAD_FILE_EXTENSIONS_CONFIGURABLE', true);
        $co->save('UPLOAD_FILE_EXTENSIONS_ALLOWED', '*.htm;*.html;');
    }

    /**
     * Saves the block
     * @param array $args Posted values
     * {@source 3 1}
     */
    public function save($args) {
        $db = Loader::db();
        parent::save($args);
        $this->clearHtmlExtension($this->getPkgHandle());
        $this->updateBookmarkRecordsBlockID();
        $this->updateAllBookmarkRecordsSort();
        $this->deleteBookmarkRecords(PHP_INT_MAX);
    }

    /**
     * Adds a block
     */
    public function add() {
        $this->set_block_tool('check_url');
        $this->set_block_tool('save_bookmarks');
        $this->set_block_tool('update_sort');
        $this->set_package_tool('parse_html');
        $this->set_package_tool('delete_unused_bookmarks_from_db');
        $this->setHtmlExtension();
        $this->set('blankImage', $this->blankImage);
        $this->set('bookMarkData', $this->getBookmarkData());
        //FormBuilder::createForm($this->bokkMarkData);
    }

    /**
     * Block edit
     */
    public function edit() {
        $this->set_block_tool('check_url');
        $this->set_block_tool('get_bookmarks');
        $this->set_block_tool('save_bookmarks');
        $this->set_block_tool('update_sort');
        $this->set_package_tool('parse_html');
        $this->set_package_tool('delete_unused_bookmarks_from_db');
        $this->setHtmlExtension();
        $this->set('blankImage', $this->blankImage);
        $this->set('bookMarkData', $this->getBookmarkDataRecords($this->bID));
    }

    /**
     * Set things for viewing the block...
     */
    public function view() {
        $this->clearHtmlExtension($this->getPkgHandle());
        Database::setDebug(true);
        $this->set('bookMarkData', $this->getBookmarkDataRecords($this->bID));
    }

    /**
     * Deletes a block
     * @see parent::delete()
     */
    public function delete() {
        $db = Loader::db();
        $this->deleteBookmarkRecords($this->bID);
        $this->deleteBookmarkRecords(PHP_INT_MAX);
        parent::delete();
    }

    /**
     * Validates form
     * @param $args Posted values
     * @return mixed Errors
     */
    public function validate($args) {
        //$e = Loader::helper('validation/error');
        //if ($args['fID'] < 1) {
        //    $e->add(t('You must select a file.'));
        //}
        //if (trim($args['fileLinkText']) == '') {
        //    $e->add(t('You must give your file a link.'));
        //}
//
        //return $e;
        return;
    }

    /**
     * Whenever the block is called
     */
    public function on_start() {
        $this->pkgHandle = BlockType::getByHandle($this->btHandle)->getPackageHandle();
    }

    /**
     * Page view
     */
    public function on_page_view() {
        $html = Loader::helper('html');
        $this->addHeaderItem($html->javascript('jquery.ui.js'));
        $this->deleteBookmarkRecords(PHP_INT_MAX);
    }

    /**
     * Date Format defined in .mo file used for making a date from timestamp
     * @return string The date format see PHP-Manual
     */
    public function getDateFormat() {

        return t('Y-m-d');
    }

    /**
     * Gets the file object
     * @return File Object
     */
    public function getFileObject() {

        return File::getByID($this->fID);
    }

    /**
     * Helper method to set block tool URLs
     * @param $tool_name Tool file name
     * @param $params $_GET values
     */
    private function set_block_tool($tool_name, $params = '') {
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

    /**
     * Helper method to set package tool URLs
     * @param $tool_name Tool file name
     * @param string $params $_GET values
     */
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