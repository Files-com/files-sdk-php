<?php

namespace Files\Exception;

/**
 * An action wait failed, timed out or could not confirm completion.
 *
 * Retains the original action and latest migration observation for inspection.
 */
class FileActionException extends FilesException
{
    /** @var \Files\Model\FileAction */
    private $fileAction;
    /** @var \Files\Model\FileMigration|null */
    private $fileMigration;

    /**
     * Retain the action and most recent migration that led to this failure.
     *
     * @param string $message Failure description.
     * @param \Files\Model\FileAction $fileAction Original action.
     * @param \Files\Model\FileMigration|null $fileMigration Latest observation, when one is available.
     */
    public function __construct($message, $fileAction, $fileMigration = null)
    {
        parent::__construct($message);
        $this->fileAction = $fileAction;
        $this->fileMigration = $fileMigration;
    }

    /**
     * Get the action whose wait failed.
     *
     * @return \Files\Model\FileAction
     */
    public function getFileAction()
    {
        return $this->fileAction;
    }

    /**
     * Get the latest migration observed before the failure.
     *
     * @return \Files\Model\FileMigration|null Null when no migration was observed.
     */
    public function getFileMigration()
    {
        return $this->fileMigration;
    }
}
