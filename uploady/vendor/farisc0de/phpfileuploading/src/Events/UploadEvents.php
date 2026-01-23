<?php

namespace Farisc0de\PhpFileUploading\Events;

/**
 * Constants for upload event names
 *
 * @package PhpFileUploading
 */
final class UploadEvents
{
    /**
     * Fired before validation starts
     */
    public const BEFORE_VALIDATION = 'upload.before_validation';

    /**
     * Fired after validation completes
     */
    public const AFTER_VALIDATION = 'upload.after_validation';

    /**
     * Fired when validation fails
     */
    public const VALIDATION_FAILED = 'upload.validation_failed';

    /**
     * Fired before virus scan
     */
    public const BEFORE_SCAN = 'upload.before_scan';

    /**
     * Fired after virus scan
     */
    public const AFTER_SCAN = 'upload.after_scan';

    /**
     * Fired when virus is detected
     */
    public const VIRUS_DETECTED = 'upload.virus_detected';

    /**
     * Fired before file is moved to storage
     */
    public const BEFORE_UPLOAD = 'upload.before_upload';

    /**
     * Fired after file is successfully uploaded
     */
    public const AFTER_UPLOAD = 'upload.after_upload';

    /**
     * Fired when upload fails
     */
    public const UPLOAD_FAILED = 'upload.upload_failed';

    /**
     * Fired before file is deleted
     */
    public const BEFORE_DELETE = 'upload.before_delete';

    /**
     * Fired after file is deleted
     */
    public const AFTER_DELETE = 'upload.after_delete';

    /**
     * Fired before image processing
     */
    public const BEFORE_IMAGE_PROCESS = 'upload.before_image_process';

    /**
     * Fired after image processing
     */
    public const AFTER_IMAGE_PROCESS = 'upload.after_image_process';

    /**
     * Fired when rate limit is exceeded
     */
    public const RATE_LIMIT_EXCEEDED = 'upload.rate_limit_exceeded';
}
