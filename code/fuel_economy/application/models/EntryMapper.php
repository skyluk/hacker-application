<?php

/**
 * This class provides a mapping of entries in the entries table to instances
 * of Application_Model_Entry.
 */
class Application_Model_EntryMapper {

    protected $_dbTable;

    /**
     * This function sets the DB table object for this object
     */
    public function setDbTable($dbTable) {

        if( is_string($dbTable) )
            $dbTable = new $dbTable();

        if( !$dbTable instanceof Zend_Db_Table_Abstract )
            throw new Exception('Invalid table object provided');

        $this->_dbTable = $dbTable;
        return $this;
    }

    /**
     * This function returns the DB table object associated with this object
     */
    public function getDbTable() {

        if( null === $this->_dbTable )
            $this->setDbTable('Application_Model_DbTable_Entry');

        return $this->_dbTable;
    }

    /**
     * This function saves an entry to the database.  If the ID is null,
     * an insert is performed, otherwise an update is performed.
     */
    public function save(Application_Model_Entry $entry) {

        $data = array(
            'desc' => $entry->getDescription(),
            'drive_time' => $entry->getDrivetime(),
            'miles' => $entry->getMiles(),
            'cost' => $entry->getCost(),
            'mpg' => $entry->getMpg(),
        );

        $id = $entry->getId();

        if( $id === null ) {
            unset($data['id']);
            $this->getDbTable()->insert($data);
        }
        else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }

    /**
     * This function will remove a line from the entries table with the
     * corresponding id.
     */
    public function remove($id) {
        //if( $id !== null )
        //    $this->getDbTable()->delete(array('id = ?' => $id));
    }

    /*
     * This function attempts to find an entry in the entries table with
     * the specified id.  If one is found, the details of the entry are
     * set in the entry parameter.
     */
    public function find($id, Application_Model_Entry $entry) {

        $rs = $this->getDbTable()->find($id);

        if( count($rs) == 0 )
            return;

        if( !$entry instanceof Application_Model_Entry )
            $entry = new Application_Model_Entry();

        $row = $rs->current();

        $entry->setId($row->id)
              ->setDescription($row->desc)
              ->setDrivetime($row->drive_time)
              ->setMiles($row->miles)
              ->setCost($row->cost)
              ->setMpg($row->mpg);
    }

    /**
     * This function returns each row from the entries table
     */
    public function fetchAll() {

        $rs = $this->getDbTable()->fetchAll();
        $entries = array();

        foreach( $rs as $row ) {
            $entry = new Application_Model_Entry();

            $entry->setId($row->id)
                  ->setDescription($row->desc)
                  ->setDrivetime($row->drive_time)
                  ->setMiles($row->miles)
                  ->setCost($row->cost)
                  ->setMpg($row->mpg);

            $entries[] = $entry;
        }

        return $entries;
    }
}

