<?php
/**
 * Created by JetBrains PhpStorm.
 * User: temp
 * Date: 19.09.13
 * Time: 20:16
 * To change this template use File | Settings | File Templates.
 */
defined('C5_EXECUTE') or die(_("Access Denied."));

class PcShooterChListFavoritesPackage extends Package {

    protected $pkgHandle = 'pc_shooter_ch_list_favorites';
    protected $appVersionRequired = '5.3.0';
    protected $pkgVersion = '0.1';

    public function getPackageDescription() {
        return t("This addon creates a list of indiviual blocks,<br>with your bookmarks in it.
        <ul><li>Export the Bookmarks from your browser(s)</li>
        <li>Import your bookmarks into a list.</li>
        <li>Add a small text and an image to each of the bookmarks.</li>
        <li>Edit each bookmark like any normal block.</li>
        <li>Each whole block is a link to another website.");
    }

    public function getPackageName() {
        return t("List Your Bookmarks");
    }

    public function install() {
        $pkg = parent::install();
        $db = Loader::db();
        $queryFKey = 'ALTER TABLE btPcShooterChListFavoritesBookMarks
                        ADD CONSTRAINT blockID
                        FOREIGN KEY fbID (blockID)
                        REFERENCES btPcShooterChListFavorites (bID)
                        ON DELETE CASCADE
                        ON UPDATE CASCADE';

        BlockType::installBlockTypeFromPackage($this->pkgHandle, $pkg);
        //$db->Execute($queryFKey);
    }
    public function uninstall() {
        $db = Loader::db();
        $schema = Database::getADOSchema();
        $sql = $schema->RemoveSchema('db.xml');
        $schema->RemoveSchema('db.xml');
        $db->Execute('alter table btPcShooterChListFavoritesBookMarks drop foreign key blockID');
        $db->Execute('drop table if exists btPcShooterChListFavorites');
        $db->Execute('drop table if exists btPcShooterChListFavoritesBookMarks');
        parent::uninstall();
    }


}