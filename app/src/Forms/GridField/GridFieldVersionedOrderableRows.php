<?php

namespace App\Forms\GridField;

use Exception;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\ORM\ManyManyList;
use SilverStripe\ORM\ManyManyThroughList;
use SilverStripe\ORM\SS_List;
use SilverStripe\Versioned\Versioned;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

class GridFieldVersionedOrderableRows extends GridFieldOrderableRows
{
    /**
     * @var bool
     */
    protected $updateLiveTableImmediately = true;

    /**
     * @param $update
     * @return $this
     */
    public function setUpdateLiveTableImmediately($update)
    {
        $this->updateLiveTableImmediately = $update;
        return $this;
    }

    /**
     * @return bool
     */
    public function getUpdateLiveTableImmediately()
    {
        return $this->updateLiveTableImmediately;
    }

    /**
     * Patched to allow copying sort order directly to live table
     *
     * @param SS_List $list
     * @param array $values
     * @param array $sortedIDs
     * @throws Exception
     */
    protected function reorderItems($list, array $values, array $sortedIDs)
    {
        // setup
        $sortField = $this->getSortField();
        $class = $list->dataClass();
        // The problem is that $sortedIDs is a list of the _related_ item IDs, which causes trouble
        // with ManyManyThrough, where we need the ID of the _join_ item in order to set the value.
        $itemToSortReference = ($list instanceof ManyManyThroughList) ? 'getJoin' : 'Me';
        $currentSortList = $list->map('ID', $itemToSortReference)->toArray();

        // sanity check.
        $this->validateSortField($list);

        $isVersioned = false;
        // check if sort column is present on the model provided by dataClass() and if it's versioned
        // cases:
        // Model has sort column and is versioned - handle as versioned
        // Model has sort column and is NOT versioned - handle as NOT versioned
        // Model doesn't have sort column because sort column is on ManyManyList - handle as NOT versioned
        // Model doesn't have sort column because sort column is on ManyManyThroughList - inspect through object
        if ($list instanceof ManyManyThroughList) {
            // We'll be updating the join class, not the relation class.
            $class = $this->getManyManyInspector($list)->getJoinClass();
            $isVersioned = $class::create()->hasExtension(Versioned::class);
        } elseif (!$this->isManyMany($list)) {
            $isVersioned = $class::create()->hasExtension(Versioned::class);
        }

        // Loop through each item, and update the sort values which do not
        // match to order the objects.
        if (!$isVersioned) {
            $sortTable = $this->getSortTable($list);
            $now = DBDatetime::now()->Rfc2822();
            $additionalSQL = '';
            $baseTable = DataObject::getSchema()->baseDataTable($class);

            $isBaseTable = ($baseTable == $sortTable);
            if (!$list instanceof ManyManyList && $isBaseTable) {
                $additionalSQL = ", \"LastEdited\" = '$now'";
            }

            foreach ($sortedIDs as $newSortValue => $targetRecordID) {
                if ($currentSortList[$targetRecordID]->$sortField != $newSortValue) {
                    DB::query(sprintf(
                        'UPDATE "%s" SET "%s" = %d%s WHERE %s',
                        $sortTable,
                        $sortField,
                        $newSortValue,
                        $additionalSQL,
                        $this->getSortTableClauseForIds($list, $targetRecordID)
                    ));

                    if (!$isBaseTable && !$list instanceof ManyManyList) {
                        DB::query(sprintf(
                            'UPDATE "%s" SET "LastEdited" = \'%s\' WHERE %s',
                            $baseTable,
                            $now,
                            $this->getSortTableClauseForIds($list, $targetRecordID)
                        ));
                    }
                }
            }
        } else {
            // For versioned objects, modify them with the ORM so that the
            // *_Versions table is updated. This ensures re-ordering works
            // similar to the SiteTree where you change the position, and then
            // you go into the record and publish it.
            foreach ($sortedIDs as $newSortValue => $targetRecordID) {
                // either the list data class (has_many, (belongs_)many_many)
                // or the intermediary join class (many_many through)
                $record = $currentSortList[$targetRecordID];
                if ($record->$sortField != $newSortValue) {
                    $record->$sortField = $newSortValue;

                    if (!$this->updateLiveTableImmediately) {
                        $record->write();
                    } else {
                        // If this sort should be immediately published to live, skip creating version records...
                        $record->setNextWriteWithoutVersion(true);
                        $record->write();

                        // Manual query to skip publish logic, as that creates a ChangeSet which we really don't need
                        $table = DataObject::getSchema()->tableForField($record, $sortField);
                        $liveTable = $record->stageTable($table, Versioned::LIVE);
                        DB::prepared_query(
                            "UPDATE {$liveTable} SET {$sortField} = ? WHERE ID = ?",
                            [
                                (int)$newSortValue,
                                (int)$record->ID
                            ]
                        );
                    }
                }
            }
        }

        $this->extend('onAfterReorderItems', $list, $values, $sortedIDs);
    }
}
