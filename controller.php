<?php
/**
 * List Your Bookmarks Installer controller
 * @author pc-shooter <info@pc-shooter.ch>
 * @package List Your Bookmarks
 * @version 0.1
 * @filesource
 */
defined('C5_EXECUTE') or die(_("Access Denied."));

/**
 * List Your Bookmarks
 * @author pc-shooter <info@pc-shooter.ch>
 *
 * <br>This addon creates a list of indiviual blocks, with your bookmarks in it.
 * <li>Export the Bookmarks from your browser(s)</li>
 * <li>Import your bookmarks into a list.</li>
 * <li>Add a small text and an image to each of the bookmarks.</li>
 * <li>Edit each bookmark like any normal block.</li>
 * <li>Each whole block is a link to another website.</li>
 * @version Development version 0.1
 * @category C5 package
 * @copyright pc-shooter - Development
 */
class PcShooterChListFavoritesPackage extends Package {

    /**
     * @var string Package handle
     */
    protected $pkgHandle = 'pc_shooter_ch_list_favorites';
    /**
     * @var string C5 version required for this package
     */
    protected $appVersionRequired = '5.3.0';
    /**
     * @var string Package version
     */
    protected $pkgVersion = '0.1';

    /**
     * Get package desc.
     * @return string The package information in the "Add functionality"-window
     */
    public function getPackageDescription() {
        return t("This addon creates a list of indiviual blocks,<br>with your bookmarks in it.
        <ul><li>Export the Bookmarks from your browser(s)</li>
        <li>Import your bookmarks into a list.</li>
        <li>Add a small text and an image to each of the bookmarks.</li>
        <li>Edit each bookmark like any normal block.</li>
        <li>Each whole block is a link to another website.");
    }

    /**
     * Get package name
     * @return string Package name
     */
    public function getPackageName() {
        return t("List Your Bookmarks");
    }

    /**
     * Package installer
     * @return Package|void
     */
    public function install() {
        $pkg = parent::install();
        BlockType::installBlockTypeFromPackage($this->pkgHandle, $pkg);
    }

    /**
     * Package uninstaller
     *
     */
    public function uninstall() {
        parent::uninstall();
        $db = Loader::db();
        $schema = Database::getADOSchema();
        $schema->RemoveSchema('db.xml');
        $schema->RemoveSchema('db.xml');
        //$db->Execute('alter table btPcShooterChListFavoritesBookMarks drop foreign key blockID');
        $db->Execute('drop table if exists btPcShooterChListFavorites');
        $db->Execute('drop table if exists btPcShooterChListFavoritesBookMarks');
    }


}